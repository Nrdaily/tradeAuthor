<?php
// approve_transaction.php 
require_once 'session.php';
// admin session 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
$transaction_id = $_POST['transaction_id'] ?? null;
if (!$transaction_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Transaction ID required']);
    exit;
}
// Get the transaction 
$stmt = $db->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'pending'");
$stmt->execute([$transaction_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$transaction) {
    http_response_code(404);
    echo json_encode(['error' => 'Transaction not found']);
    exit;
}
// Get the cryptocurrency symbol 
$crypto_stmt = $db->prepare("SELECT symbol FROM cryptocurrencies WHERE id = ?");
$crypto_stmt->execute([$transaction['crypto_id']]);
$crypto = $crypto_stmt->fetch(PDO::FETCH_ASSOC);
$reserved_column = strtolower($crypto['symbol']) . '_reserved';
// Start transaction 
$db->beginTransaction();
try {
    // Remove the reserved balance (it was already deducted from the main balance when the withdrawal was created) 
    $update_user = $db->prepare("UPDATE users SET $reserved_column = $reserved_column - ? WHERE id = ?");
    $update_user->execute([$transaction['crypto_amount'], $transaction['user_id']]);
    // Update the transaction status to completed 
    $update_transaction = $db->prepare("UPDATE transactions SET status = 'completed' WHERE id = ?");
    $update_transaction->execute([$transaction_id]);
    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Transaction approved successfully!']);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to approve transaction: ' . $e->getMessage()]);
}
