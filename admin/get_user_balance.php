<?php
// admin/get_user_balance.php
require_once 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $symbol = $_POST['symbol'] ?? '';
    
    if (!empty($user_id) && !empty($symbol)) {
        // Get user's balance for the specified cryptocurrency
        $balance_column = strtolower($symbol) . '_balance';
        $query = "SELECT $balance_column FROM users WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $balance = $result[$balance_column] ?? 0;
        
        echo json_encode([
            'success' => true,
            'balance' => number_format($balance, 8)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Missing parameters'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>