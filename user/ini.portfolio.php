<?php
// portfolio.php (with enhanced withdrawal functionality)
require_once '../app/config/session.php';
require_once '../app/func/functions.php';

// Get live cryptocurrency prices
$livePrices = getLiveCryptoPrices();

// Get all cryptocurrencies
$crypto_query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE";
$crypto_stmt = $db->prepare($crypto_query);
$crypto_stmt->execute();
$all_cryptos = $crypto_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's portfolio data
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate total portfolio value with live prices
$totalPortfolioValue = 0;
$assets = [];

foreach ($all_cryptos as $crypto) {
    $balance_column = strtolower($crypto['symbol']) . '_balance';
    $balance = $user[$balance_column] ?? 0;

    // Use live price if available, otherwise use database price
    $current_price = $livePrices[$crypto['symbol']]['price'] ?? $crypto['current_price'];
    $value = $balance * $current_price;
    $totalPortfolioValue += $value;

    $assets[] = [
        'id' => $crypto['id'],
        'symbol' => $crypto['symbol'],
        'name' => $crypto['name'],
        'current_price' => $current_price,
        'balance' => $balance,
        'value' => $value,
        'change' => $livePrices[$crypto['symbol']]['change'] ?? 0,
        'receiving_address' => $crypto['receiving_address'] ?? '',
        'qr_code' => $crypto['qr_code'] ?? ''
    ];
}

// Debug: Check what's being fetched
error_log("Assets fetched: " . print_r($assets, true));

// Get recent transactions
$transaction_query = "
    SELECT t.*, c.symbol, c.name
    FROM transactions t
    JOIN cryptocurrencies c ON t.crypto_id = c.id
    WHERE t.user_id = :user_id
    ORDER BY t.created_at DESC
    LIMIT 10
";

$transaction_stmt = $db->prepare($transaction_query);
$transaction_stmt->bindParam(':user_id', $_SESSION['user_id']);
$transaction_stmt->execute();
$transactions = $transaction_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle send form submission
$send_errors = [];
$send_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_crypto'])) {
    $crypto_id = $_POST['crypto_id'];
    $amount = floatval($_POST['amount']);
    $wallet_address = trim($_POST['wallet_address']);
    $speed_up = isset($_POST['speed_up']) ? 1 : 0;

    // Validate inputs
    if (empty($crypto_id) || empty($amount) || empty($wallet_address)) {
        $send_errors[] = "All fields are required.";
    } elseif ($amount <= 0) {
        $send_errors[] = "Amount must be greater than zero.";
    } else {
        // Get cryptocurrency details
        $crypto_stmt = $db->prepare("SELECT * FROM cryptocurrencies WHERE id = :id");
        $crypto_stmt->bindParam(':id', $crypto_id);
        $crypto_stmt->execute();
        $crypto = $crypto_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$crypto) {
            $send_errors[] = "Invalid cryptocurrency selected.";
        } else {
            $symbol = $crypto['symbol'];
            $balance_column = strtolower($symbol) . '_balance';
            $user_balance = $user[$balance_column] ?? 0;

            // Check if user has sufficient balance
            if ($amount > $user_balance) {
                $send_errors[] = "Insufficient balance. You have " . number_format($user_balance, 8) . " $symbol.";
            } else {
                try {
                    // Start transaction
                    $db->beginTransaction();

                    // Calculate network fee (0.1% of amount with minimum of 0.0001)
                    $network_fee = max($amount * 0.001, 0.0001);
                    $net_amount = $amount - $network_fee;

                    // Create send transaction
                    $insert_query = "
                        INSERT INTO transactions (user_id, crypto_id, type, amount, price, total, crypto_amount, fee, status, metadata) 
                        VALUES (:user_id, :crypto_id, 'send', :amount, :price, :total, :crypto_amount, :fee, 'pending', :metadata)
                    ";

                    $metadata = json_encode([
                        'wallet_address' => $wallet_address,
                        'network_fee' => $network_fee,
                        'speed_up' => $speed_up,
                        'net_amount' => $net_amount
                    ]);

                    $insert_stmt = $db->prepare($insert_query);
                    $insert_stmt->bindValue(':user_id', $_SESSION['user_id']);
                    $insert_stmt->bindValue(':crypto_id', $crypto_id);
                    $insert_stmt->bindValue(':amount', $amount);
                    $insert_stmt->bindValue(':price', $crypto['current_price']);
                    $insert_stmt->bindValue(':total', $amount * $crypto['current_price']);
                    $insert_stmt->bindValue(':crypto_amount', $amount);
                    $insert_stmt->bindValue(':fee', $network_fee);
                    $insert_stmt->bindValue(':metadata', $metadata);

                    if ($insert_stmt->execute()) {
                        $transaction_id = $db->lastInsertId();

                        // Reserve the funds by updating user balance (will be finalized when admin approves)
                        $reserve_column = strtolower($symbol) . '_reserved';
                        $update_query = "
                            UPDATE users 
                            SET $balance_column = $balance_column - :amount,
                                $reserve_column = $reserve_column + :amount 
                            WHERE id = :user_id
                        ";

                        $update_stmt = $db->prepare($update_query);
                        $update_stmt->bindValue(':amount', $amount);
                        $update_stmt->bindValue(':user_id', $_SESSION['user_id']);

                        if ($update_stmt->execute()) {
                            // Commit transaction
                            $db->commit();

                            $send_success = "Send request submitted successfully! Transaction ID: #$transaction_id";

                            // Set session variables to show processing modal
                            $_SESSION['show_processing_modal'] = true;
                            $_SESSION['processing_transaction_id'] = $transaction_id;
                            $_SESSION['processing_crypto_symbol'] = $symbol;
                            $_SESSION['processing_amount'] = $amount;
                            $_SESSION['processing_network_fee'] = $network_fee;
                            $_SESSION['processing_wallet_address'] = $wallet_address;

                            // Refresh user data
                            $stmt->execute();
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);

                            // Refresh transactions
                            $transaction_stmt->execute();
                            $transactions = $transaction_stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Redirect to clear POST data and show modal
                            header('Location: portfolio');
                            exit;
                        } else {
                            throw new Exception("Failed to update user balance.");
                        }
                    } else {
                        throw new Exception("Failed to create transaction.");
                    }
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $db->rollBack();
                    $send_errors[] = "Error processing send request: " . $e->getMessage();
                }
            }
        }
    }
}

// Function to refresh cryptocurrency data (call this when needed)
function refreshCryptoData()
{
    global $db;

    $query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all cryptocurrencies (ensure we have the latest data)
$all_cryptos = refreshCryptoData();
?>