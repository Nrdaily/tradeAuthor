<?php
// admin/get_user_cards.php
require_once 'session.php';
require_once '../app/config/encryption.php';

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit;
}

$user_id = $_GET['user_id'];
$encryptionKey = ENCRYPTION_KEY;

// Get user's payment cards
$query = "SELECT * FROM payment_cards WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to decrypt card number
function decryptCardNumber($encryptedCardNumber, $iv, $encryptionKey) {
    if (empty($encryptedCardNumber) || empty($iv)) {
        return false;
    }
    
    try {
        $encryptedCardNumber = base64_decode($encryptedCardNumber);
        $iv = base64_decode($iv);
        $decrypted = openssl_decrypt($encryptedCardNumber, 'AES-256-CBC', $encryptionKey, 0, $iv);
        return $decrypted;
    } catch (Exception $e) {
        error_log("Decryption error: " . $e->getMessage());
        return false;
    }
}

$cards_data = [];
foreach ($cards as $card) {
    $fullCardNumber = '';
    $canDecrypt = false;
    
    if (!empty($card['encrypted_card_number']) && !empty($card['card_number_iv'])) {
        $fullCardNumber = decryptCardNumber($card['encrypted_card_number'], $card['card_number_iv'], $encryptionKey);
        $canDecrypt = ($fullCardNumber !== false && !empty($fullCardNumber));
    }
    if ($canDecrypt) {
        // Format the card number with spaces
        $card_number_hash = preg_replace('/(.{4})/', '$1 ', $card['card_number_hash']);
        $card_number_hash = trim($card['card_number_hash']);
        return $card_number_hash;
    }
    
    $cards_data[] = [
        'id' => $card['id'],
        'card_brand' => $card['card_brand'],
        'card_number_hash' => $card['card_number_hash'],
        'full_card_number' => $canDecrypt ? $card_number_hash : $card['card_number_hash'],
        'expiry_month' => $card['expiry_month'],
        'expiry_year' => $card['expiry_year'],
        'cardholder_name' => $card['cardholder_name'],
        'is_default' => $card['is_default'],
        'created_at' => $card['created_at'],
        'can_decrypt' => $canDecrypt
    ];
}

header('Content-Type: application/json');
echo json_encode($cards_data);
?>