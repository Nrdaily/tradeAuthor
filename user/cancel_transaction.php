<?php
// cancel_transaction.php
require_once '../app/config/session.php';
require_once '../app/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$transaction_id = $data['transaction_id'] ?? null;

if (!$transaction_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Transaction ID required']);
    exit;
}

$db = (new Database())->getConnection();

try {
    $db->beginTransaction();

    // Get transaction details
    $stmt = $db->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'pending' AND user_id = ?");
    $stmt->execute([$transaction_id, $_SESSION['user_id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception("Transaction not found or not pending");
    }

    // Get cryptocurrency symbol
    $crypto_stmt = $db->prepare("SELECT symbol FROM cryptocurrencies WHERE id = ?");
    $crypto_stmt->execute([$transaction['crypto_id']]);
    $crypto = $crypto_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$crypto) {
        throw new Exception("Cryptocurrency not found");
    }

    $symbol = strtolower($crypto['symbol']);
    $reserved_column = $symbol . '_reserved';
    $balance_column = $symbol . '_balance';

    // Return reserved amount to available balance
    $update_user = $db->prepare("UPDATE users SET $balance_column = $balance_column + ?, $reserved_column = $reserved_column - ? WHERE id = ?");
    $update_user->execute([$transaction['crypto_amount'], $transaction['crypto_amount'], $_SESSION['user_id']]);

    // Update transaction status to cancelled
    $update_tx = $db->prepare("UPDATE transactions SET status = 'cancelled' WHERE id = ?");
    $update_tx->execute([$transaction_id]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transaction cancelled successfully'
    ]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>