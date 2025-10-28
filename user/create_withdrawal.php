<?php
// create_withdrawal.php 
require_once '../app/config/session.php';
require_once '../app/config/database.php';
$db = (new Database())->getConnection();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
// Check if user is banned 
if ($_SESSION['banned']) {
    http_response_code(403);
    echo json_encode(['error' => 'You are banned from performing this action']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$crypto_id = $data['crypto_id'] ?? null;
$amount = $data['amount'] ?? null;
$address = $data['address'] ?? null;
// Validate input 
if (!$crypto_id || !$amount || !$address) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Check if user has sufficient balance 
// First, get the user's balance for the selected crypto 
$crypto = $db->prepare("SELECT symbol FROM cryptocurrencies WHERE id = ?");
$crypto->execute([$crypto_id]);
$crypto_data = $crypto->fetch(PDO::FETCH_ASSOC);
if (!$crypto_data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid cryptocurrency']);
    exit;
}
$balance_column = strtolower($crypto_data['symbol']) . '_balance';
$reserved_column = strtolower($crypto_data['symbol']) . '_reserved';

// Check if the reserved column exists? We assume it does after our alteration. 
$user_balance = $db->prepare("SELECT $balance_column FROM users WHERE id = ?");
$user_balance->execute([$_SESSION['user_id']]);
$balance = $user_balance->fetch(PDO::FETCH_ASSOC)[$balance_column];
if ($balance < $amount) {
    http_response_code(400);
    echo json_encode(['error' => 'Insufficient balance']);
    exit;
}

// Calculate network fee (for example, 0.1% with a minimum of 0.0001)
$network_fee = max($amount * 0.001, 0.0001); // Start transaction 
$db->beginTransaction();
try {
    // Deduct the amount from the main balance and add to reserved 
    $update_balance = $db->prepare(" UPDATE users SET $balance_column = $balance_column - :amount, $reserved_column = $reserved_column + :amount WHERE id = :user_id ");
    $update_balance->bindValue(':amount', $amount);
    $update_balance->bindValue(':user_id', $_SESSION['user_id']);
    $update_balance->execute();

    // Get the current price of the cryptocurrency to calculate the total in USD? 
    // Our transactions table requires price and total. We can get the current price. 
    $get_price = $db->prepare("SELECT current_price FROM cryptocurrencies WHERE id = ?");
    $get_price->execute([$crypto_id]);
    $price = $get_price->fetch(PDO::FETCH_ASSOC)['current_price'];
    $total = $amount * $price;

    // Create a withdrawal transaction 
    $insert = $db->prepare(" INSERT INTO transactions (user_id, crypto_id, type, amount, price, total, crypto_amount, fee, status, created_at) VALUES (?, ?, 'withdraw', ?, ?, ?, ?, ?, 'pending', NOW()) ");

    // Note: For withdrawal, the 'amount' and 'total' are in USD? We are storing the crypto_amount separately. 
    // Let's set: 
    // amount: the USD value of the withdrawal (amount * price) 
    // total: same as amount? Or we can have the total including fee? But the fee is in crypto. 
    // We'll set the amount and total to the USD value of the crypto amount (without fee) for consistency with buy transactions. 
    $insert->execute([
        $_SESSION['user_id'],
        $crypto_id,
        $amount * $price,
        // amount in USD
        $price,
        $total,
        // total in USD 
        $amount,
        // crypto amount 
        $network_fee,
        // fee in crypto 
    ]);
    $transaction_id = $db->lastInsertId();
    $db->commit();
    echo json_encode(['success' => true, 'transaction_id' => $transaction_id, 'message' => 'Withdrawal request submitted successfully!']);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create transaction: ' . $e->getMessage()]);
}
