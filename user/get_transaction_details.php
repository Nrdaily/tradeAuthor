<?php
// get_transaction_details.php
require_once '../app/config/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

$transaction_id = $_GET['id'];

// Get transaction details
$query = "
    SELECT t.*, c.symbol, c.name as crypto_name
    FROM transactions t
    JOIN cryptocurrencies c ON t.crypto_id = c.id
    WHERE t.id = :id AND t.user_id = :user_id
";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $transaction_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if ($transaction) {
    echo json_encode([
        'success' => true,
        'transaction' => $transaction
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Transaction not found'
    ]);
}
?>