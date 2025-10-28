<?php
// functions.php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// app/functions.php
function getLiveCryptoPrices() {
    // API endpoint for cryptocurrency prices (using CoinGecko API)
    $api_url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,tether,usd-coin,shiba-inu&vs_currencies=usd&include_24hr_change=true';
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CryptTradePro/1.0');
    
    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 && $response) {
        $data = json_decode($response, true);
        
        // Map API data to our cryptocurrency symbols
        $prices = [
            'BTC' => [
                'price' => $data['bitcoin']['usd'] ?? 0,
                'change' => $data['bitcoin']['usd_24h_change'] ?? 0
            ],
            'ETH' => [
                'price' => $data['ethereum']['usd'] ?? 0,
                'change' => $data['ethereum']['usd_24h_change'] ?? 0
            ],
            'USDT' => [
                'price' => $data['tether']['usd'] ?? 1,
                'change' => $data['tether']['usd_24h_change'] ?? 0
            ],
            'USDC' => [
                'price' => $data['usd-coin']['usd'] ?? 1,
                'change' => $data['usd-coin']['usd_24h_change'] ?? 0
            ],
            'SHIB' => [
                'price' => $data['shiba-inu']['usd'] ?? 0,
                'change' => $data['shiba-inu']['usd_24h_change'] ?? 0
            ]
        ];
        
        return $prices;
    }
    
    // Fallback to hardcoded prices if API fails
    return [
        'BTC' => ['price' => 36842.15, 'change' => 1.24],
        'ETH' => ['price' => 2045.67, 'change' => 0.87],
        'USDT' => ['price' => 1.00, 'change' => 0.01],
        'USDC' => ['price' => 1.00, 'change' => 0.01],
        'SHIB' => ['price' => 0.00003582, 'change' => 2.34]
    ];
}

function updateCryptoPricesInDatabase($db, $prices) {
    foreach ($prices as $symbol => $data) {
        $query = "UPDATE cryptocurrencies SET current_price = :price, price_change_24h = :change WHERE symbol = :symbol";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':change', $data['change']);
        $stmt->bindParam(':symbol', $symbol);
        $stmt->execute();
    }
}