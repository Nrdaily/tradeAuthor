<?php
// admin/test_encryption.php
require_once 'session.php';
require_once '../app/config/encryption.php';

echo "<h2>Encryption Test</h2>";

// Test encryption and decryption
$test_card_number = "4111111111111111";
$iv = openssl_random_pseudo_bytes(16);
$encrypted = openssl_encrypt($test_card_number, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
$encrypted_b64 = base64_encode($encrypted);
$iv_b64 = base64_encode($iv);

echo "Original: $test_card_number<br>";
echo "Encrypted: $encrypted_b64<br>";
echo "IV: $iv_b64<br>";

// Test decryption
$decrypted = openssl_decrypt(base64_decode($encrypted_b64), 'AES-256-CBC', ENCRYPTION_KEY, 0, base64_decode($iv_b64));
echo "Decrypted: $decrypted<br>";

echo "Match: " . ($test_card_number === $decrypted ? "YES" : "NO") . "<br>";
echo "<hr><h3>Note</h3>";
echo "Yes, if you want to be able to see (decrypt) the original number later, you must <b>encrypt</b> it (not hash it).<br>";
echo "Hashing is one-way and cannot be reversed. Encryption is two-way and allows you to recover the original value.<br>";
// Test with actual database data
echo "<h3>Database Test</h3>";
$query = "SELECT encrypted_card_number, card_number_iv, card_last_four FROM payment_cards LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if ($card) {
    echo "Last Four: " . $card['card_last_four'] . "<br>";
    echo "Has Encrypted Data: " . (!empty($card['encrypted_card_number']) ? "Yes" : "No") . "<br>";
    echo "Has IV: " . (!empty($card['card_number_iv']) ? "Yes" : "No") . "<br>";
    
    if (!empty($card['encrypted_card_number']) && !empty($card['card_number_iv'])) {
        $decrypted_card = openssl_decrypt(
            base64_decode($card['encrypted_card_number']), 
            'AES-256-CBC', 
            ENCRYPTION_KEY, 
            0, 
            base64_decode($card['card_number_iv'])
        );
        
        echo "Decrypted Card: " . ($decrypted_card ? $decrypted_card : "Failed to decrypt") . "<br>";
    }
}
?>