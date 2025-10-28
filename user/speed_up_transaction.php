<?php
// speed_up_transaction.php
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
    // Get transaction details
    $stmt = $db->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'pending'");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception("Transaction not found or not pending");
    }

    // Update transaction priority and reduce estimated time
    $update_stmt = $db->prepare("UPDATE transactions SET priority = 'high' WHERE id = ?");
    $update_stmt->execute([$transaction_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Transaction prioritized! Estimated confirmation time reduced.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>