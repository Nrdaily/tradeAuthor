<?php
// admin/debug_crypto.php
require_once 'session.php';

// Get all cryptocurrencies with their addresses and QR codes
$query = "SELECT id, symbol, name, receiving_address, qr_code FROM cryptocurrencies WHERE is_active = TRUE";
$stmt = $db->prepare($query);
$stmt->execute();
$cryptos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Cryptocurrency Addresses and QR Codes</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Symbol</th><th>Name</th><th>Receiving Address</th><th>QR Code URL</th></tr>";

foreach ($cryptos as $crypto) {
    echo "<tr>";
    echo "<td>{$crypto['id']}</td>";
    echo "<td>{$crypto['symbol']}</td>";
    echo "<td>{$crypto['name']}</td>";
    echo "<td style='word-break: break-all;'>" . ($crypto['receiving_address'] ? $crypto['receiving_address'] : 'NULL') . "</td>";
    echo "<td style='word-break: break-all;'>" . ($crypto['qr_code'] ? $crypto['qr_code'] : 'NULL') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check if the data is being fetched correctly in portfolio.php
echo "<h2>Test Portfolio Data Fetch</h2>";
echo "<pre>";

$test_query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE";
$test_stmt = $db->prepare($test_query);
$test_stmt->execute();
$test_cryptos = $test_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($test_cryptos as $crypto) {
    echo "Symbol: {$crypto['symbol']}\n";
    echo "Address: " . ($crypto['receiving_address'] ? $crypto['receiving_address'] : 'EMPTY') . "\n";
    echo "QR Code: " . ($crypto['qr_code'] ? $crypto['qr_code'] : 'EMPTY') . "\n";
    echo "---\n";
}

echo "</pre>";
?>