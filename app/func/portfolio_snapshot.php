<?php
// app/func/portfolio_snapshot.php
// Shared snapshot logic for daily portfolio snapshots

require_once __DIR__ . '/functions.php'; // for getLiveCryptoPrices()

// Helper to send alert emails. Tries to use PHPMailer via Composer if available,
// otherwise falls back to mail().
function send_alert($to, $subject, $body, $from = null) {
    $from = $from ?: 'noreply@localhost';
    // Try Composer autoload + PHPMailer
    $autoload = __DIR__ . '/../../vendor/autoload.php';
    if (file_exists($autoload)) {
        try {
            require_once $autoload;
            if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                // Use mail() transport by default; if SMTP configured in env, user can update here.
                $mail->setFrom($from);
                foreach (explode(',', $to) as $addr) {
                    $addr = trim($addr);
                    if ($addr) $mail->addAddress($addr);
                }
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->isHTML(false);
                $mail->send();
                return true;
            }
        } catch (Exception $e) {
            // fall back to mail()
        }
    }

    // fallback
    $headers = "From: {$from}\r\n";
    return @mail($to, $subject, $body, $headers);
}

function run_portfolio_snapshots(PDO $db, array $options = []) {
    $logToFile = $options['logToFile'] ?? true;
    $logFile = $options['logFile'] ?? __DIR__ . '/../../logs/portfolio_snapshot.log';
    $alerts = [];
    $alertsConfigPath = __DIR__ . '/../config/alerts.php';
    if (file_exists($alertsConfigPath)) {
        $alerts = include $alertsConfigPath;
    }

    // Ensure table exists
    $createSql = "CREATE TABLE IF NOT EXISTS portfolio_snapshots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        snapshot_date DATE NOT NULL,
        total_value DECIMAL(20,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_date_unique (user_id, snapshot_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($createSql);

    // Get live prices
    $livePrices = [];
    if (function_exists('getLiveCryptoPrices')) {
        try {
            $livePrices = getLiveCryptoPrices();
        } catch (Exception $e) {
            // ignore and fallback to DB prices
            $livePrices = [];
        }
    }

    // Get users
    $usersStmt = $db->prepare("SELECT id FROM users");
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_COLUMN);

    $created = 0;
    try {
        foreach ($users as $userId) {
            $balancesStmt = $db->prepare(
                "SELECT usdt_balance, usdc_balance, btc_balance, eth_balance, shib_balance FROM users WHERE id = :user_id"
            );
            $balancesStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $balancesStmt->execute();
            $balances = $balancesStmt->fetch(PDO::FETCH_ASSOC);

            $total = 0.0;
            $mapping = [
                'USDT' => $balances['usdt_balance'] ?? 0,
                'USDC' => $balances['usdc_balance'] ?? 0,
                'BTC' => $balances['btc_balance'] ?? 0,
                'ETH' => $balances['eth_balance'] ?? 0,
                'SHIB' => $balances['shib_balance'] ?? 0,
            ];

            foreach ($mapping as $symbol => $amt) {
                $price = $livePrices[$symbol]['price'] ?? null;
                if ($price === null) {
                    $pstmt = $db->prepare("SELECT current_price FROM cryptocurrencies WHERE symbol = :sym LIMIT 1");
                    $pstmt->bindValue(':sym', $symbol, PDO::PARAM_STR);
                    $pstmt->execute();
                    $p = $pstmt->fetchColumn();
                    $price = $p ?: 0;
                }
                $total += floatval($amt) * floatval($price);
            }

            $insert = $db->prepare(
                "INSERT INTO portfolio_snapshots (user_id, snapshot_date, total_value) VALUES (:user_id, :snapshot_date, :total_value)
                 ON DUPLICATE KEY UPDATE total_value = :total_value_upd"
            );
            $insert->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $insert->bindValue(':snapshot_date', date('Y-m-d'), PDO::PARAM_STR);
            $insert->bindValue(':total_value', round($total, 2), PDO::PARAM_STR);
            $insert->bindValue(':total_value_upd', round($total, 2), PDO::PARAM_STR);
            $insert->execute();
            $created++;
        }
    } catch (Exception $ex) {
        $errMsg = date('Y-m-d H:i:s') . " - Error running snapshots: " . $ex->getMessage() . "\n";
        if ($logToFile) {
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) mkdir($logDir, 0755, true);
            file_put_contents($logFile, $errMsg, FILE_APPEND);
        }
        // Send alert email if configured
        if (!empty($alerts) && !empty($alerts['enabled'])) {
            $to = $alerts['recipients'] ?? '';
            $subject = 'Portfolio snapshot job failed';
            $body = "An error occurred while running portfolio snapshots:\n\n" . $ex->getMessage();
            $from = $alerts['from'] ?? 'noreply@localhost';
            @send_alert($to, $subject, $body, $from);
        }
        return ['count' => $created, 'message' => $errMsg];
    }

    $msg = date('Y-m-d H:i:s') . " - Snapshots updated for {$created} users\n";
    if ($logToFile) {
        // ensure logs dir exists
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);
        file_put_contents($logFile, $msg, FILE_APPEND);
    }

    // If snapshots created is 0, optionally notify
    if ($created === 0 && !empty($alerts) && !empty($alerts['enabled'])) {
        $to = $alerts['recipients'] ?? '';
        $subject = 'Portfolio snapshot job warning';
        $body = "The portfolio snapshot job completed but created 0 snapshots. Check system logs.";
        $from = $alerts['from'] ?? 'noreply@localhost';
        @send_alert($to, $subject, $body, $from);
    }

    return ['count' => $created, 'message' => $msg];
}

/**
 * Create or update a snapshot for a specific date for all users.
 * Uses transaction history up to the snapshot date to compute holdings and
 * uses the most-recent transaction price on-or-before the date as the valuation price
 * (falls back to cryptocurrencies.current_price when not available).
 *
 * @param PDO $db
 * @param string $snapshot_date YYYY-MM-DD
 * @param array $options
 * @return array ['count' => int, 'message' => string]
 */
function run_portfolio_snapshot_for_date(PDO $db, string $snapshot_date, array $options = []) {
    $logToFile = $options['logToFile'] ?? true;
    $logFile = $options['logFile'] ?? __DIR__ . '/../../logs/portfolio_snapshot.log';
    $alerts = [];
    $alertsConfigPath = __DIR__ . '/../config/alerts.php';
    if (file_exists($alertsConfigPath)) {
        $alerts = include $alertsConfigPath;
    }

    // ensure table exists
    $createSql = "CREATE TABLE IF NOT EXISTS portfolio_snapshots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        snapshot_date DATE NOT NULL,
        total_value DECIMAL(20,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_date_unique (user_id, snapshot_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($createSql);

    $cutoff = $snapshot_date . ' 23:59:59';

    // detect if a crypto_price_history table exists (for more accurate historical prices)
    $priceHistoryExists = false;
    try {
        $check = $db->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'crypto_price_history' LIMIT 1");
        $check->execute();
        $priceHistoryExists = (bool)$check->fetchColumn();
    } catch (Exception $e) {
        $priceHistoryExists = false;
    }

    // get users
    $usersStmt = $db->prepare("SELECT id FROM users");
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_COLUMN);

    $created = 0;
    try {
        foreach ($users as $userId) {
            // compute holdings as of cutoff date from transactions
            $holdStmt = $db->prepare(
                "SELECT c.symbol,
                    SUM(CASE WHEN t.type = 'buy' THEN t.crypto_amount WHEN t.type = 'sell' THEN -t.crypto_amount ELSE 0 END) as qty
                 FROM transactions t
                 JOIN cryptocurrencies c ON t.crypto_id = c.id
                 WHERE t.user_id = :user_id AND t.created_at <= :cutoff
                 GROUP BY c.symbol"
            );
            $holdStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $holdStmt->bindValue(':cutoff', $cutoff, PDO::PARAM_STR);
            $holdStmt->execute();
            $holdings = $holdStmt->fetchAll(PDO::FETCH_KEY_PAIR); // symbol => qty

            $total = 0.0;
            foreach ($holdings as $symbol => $qty) {
                $qty = floatval($qty);
                if ($qty == 0) continue;

                // Prefer historical price from crypto_price_history if available
                $price = null;
                if ($priceHistoryExists) {
                    $hp = $db->prepare("SELECT price FROM crypto_price_history WHERE symbol = :sym AND price_date <= :snapshot_date ORDER BY price_date DESC LIMIT 1");
                    $hp->bindValue(':sym', $symbol, PDO::PARAM_STR);
                    $hp->bindValue(':snapshot_date', $snapshot_date, PDO::PARAM_STR);
                    $hp->execute();
                    $price = $hp->fetchColumn();
                }

                // If no price history, try to use the last transaction price on-or-before the cutoff
                if ($price === false || $price === null) {
                    $pstmt = $db->prepare(
                        "SELECT t.price FROM transactions t JOIN cryptocurrencies c ON t.crypto_id = c.id
                         WHERE c.symbol = :sym AND t.created_at <= :cutoff AND t.price IS NOT NULL
                         ORDER BY t.created_at DESC LIMIT 1"
                    );
                    $pstmt->bindValue(':sym', $symbol, PDO::PARAM_STR);
                    $pstmt->bindValue(':cutoff', $cutoff, PDO::PARAM_STR);
                    $pstmt->execute();
                    $price = $pstmt->fetchColumn();
                }

                // fallback to cryptocurrencies.current_price
                if ($price === false || $price === null) {
                    $pp = $db->prepare("SELECT current_price FROM cryptocurrencies WHERE symbol = :sym LIMIT 1");
                    $pp->bindValue(':sym', $symbol, PDO::PARAM_STR);
                    $pp->execute();
                    $price = $pp->fetchColumn() ?: 0;
                }

                $total += $qty * floatval($price);
            }

            $insert = $db->prepare(
                "INSERT INTO portfolio_snapshots (user_id, snapshot_date, total_value) VALUES (:user_id, :snapshot_date, :total_value)
                 ON DUPLICATE KEY UPDATE total_value = :total_value_upd"
            );
            $insert->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $insert->bindValue(':snapshot_date', $snapshot_date, PDO::PARAM_STR);
            $insert->bindValue(':total_value', round($total, 2), PDO::PARAM_STR);
            $insert->bindValue(':total_value_upd', round($total, 2), PDO::PARAM_STR);
            $insert->execute();
            $created++;
        }
    } catch (Exception $ex) {
        $errMsg = date('Y-m-d H:i:s') . " - Error running snapshot for {$snapshot_date}: " . $ex->getMessage() . "\n";
        if ($logToFile) {
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) mkdir($logDir, 0755, true);
            file_put_contents($logFile, $errMsg, FILE_APPEND);
        }
        if (!empty($alerts) && !empty($alerts['enabled'])) {
            $to = $alerts['recipients'] ?? '';
            $subject = 'Portfolio snapshot backfill failed';
            $body = "An error occurred while running portfolio snapshot for {$snapshot_date}:\n\n" . $ex->getMessage();
            $from = $alerts['from'] ?? 'noreply@localhost';
            @send_alert($to, $subject, $body, $from);
        }
        return ['count' => $created, 'message' => $errMsg];
    }

    $msg = date('Y-m-d H:i:s') . " - Snapshot updated for {$created} users on {$snapshot_date}\n";
    if ($logToFile) {
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);
        file_put_contents($logFile, $msg, FILE_APPEND);
    }

    return ['count' => $created, 'message' => $msg];
}

?>