<?php
// user/api_portfolio_history.php
require_once '../app/config/session.php';

header('Content-Type: application/json');

$allowed = ['7','30','90','365','all'];
$range = isset($_GET['range']) ? $_GET['range'] : '30';
if (!in_array($range, $allowed)) $range = '30';

try {
    // Try to use snapshots if available
    if ($range !== 'all') {
        $days = intval($range);
        $stmt = $db->prepare("SELECT snapshot_date, total_value FROM portfolio_snapshots WHERE user_id = :user_id AND snapshot_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY) ORDER BY snapshot_date ASC");
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // all snapshots for this user
        $stmt = $db->prepare("SELECT snapshot_date, total_value FROM portfolio_snapshots WHERE user_id = :user_id ORDER BY snapshot_date ASC");
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $labels = [];
    $values = [];

    if (!empty($rows)) {
        foreach ($rows as $r) {
            $labels[] = date('M j', strtotime($r['snapshot_date']));
            $values[] = floatval($r['total_value']);
        }
        echo json_encode(['labels' => $labels, 'values' => $values]);
        exit;
    }

    // Fallback: compute daily series from transactions
    // Determine date range
    if ($range === 'all') {
        // cap to last 730 days to avoid heavy queries
        $days = 730;
    } else {
        $days = intval($range);
    }

    $history_query = "SELECT t.*, c.symbol FROM transactions t JOIN cryptocurrencies c ON t.crypto_id = c.id WHERE t.user_id = :user_id ORDER BY t.created_at ASC";
    $history_stmt = $db->prepare($history_query);
    $history_stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $history_stmt->execute();
    $history_txns = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by date
    $txs_by_date = [];
    foreach ($history_txns as $tx) {
        $dateKey = date('Y-m-d', strtotime($tx['created_at']));
        if (!isset($txs_by_date[$dateKey])) $txs_by_date[$dateKey] = [];
        $txs_by_date[$dateKey][] = $tx;
    }

    $holdings = [];
    $labels = [];
    $values = [];

    for ($i = $days - 1; $i >= 0; $i--) {
        $day = new DateTime("-{$i} days");
        $dateKey = $day->format('Y-m-d');
        $labels[] = $day->format('M j');

        if (isset($txs_by_date[$dateKey])) {
            foreach ($txs_by_date[$dateKey] as $tx) {
                $sym = $tx['symbol'];
                if (!isset($holdings[$sym])) $holdings[$sym] = 0;
                $crypto_amount = floatval($tx['crypto_amount']);
                if ($tx['type'] === 'buy') {
                    $holdings[$sym] += $crypto_amount;
                } elseif ($tx['type'] === 'sell') {
                    $holdings[$sym] -= $crypto_amount;
                }
            }
        }

        // compute pv using live prices when available
        $pv = 0.0;
        foreach ($holdings as $hsym => $hamt) {
            // try live price table
            $price = null;
            // try from cryptocurrencies table if no live feed
            $pstmt = $db->prepare("SELECT current_price FROM cryptocurrencies WHERE symbol = :sym LIMIT 1");
            $pstmt->bindValue(':sym', $hsym, PDO::PARAM_STR);
            $pstmt->execute();
            $p = $pstmt->fetchColumn();
            $price = $p ?: 0;
            $pv += $hamt * $price;
        }
        $values[] = round($pv, 2);
    }

    echo json_encode(['labels' => $labels, 'values' => $values]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

?>
