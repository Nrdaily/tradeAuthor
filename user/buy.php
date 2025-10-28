<?php require_once '../app/config/language.php'; ?>
<?php
// buy.php - Enhanced Trading Interface with Integrated Processing Modal
require_once '../app/config/session.php';
require_once '../app/config/encryption.php';
$encryptionKey = ENCRYPTION_KEY;

// Get cryptocurrency data
$crypto_query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE AND symbol IN ('USDT', 'USDC', 'SHIB')";
$crypto_stmt = $db->prepare($crypto_query);
$crypto_stmt->execute();
$cryptocurrencies = $crypto_stmt->fetchAll(PDO::FETCH_ASSOC);

// Create a mapping of crypto symbols to IDs and prices
$crypto_map = [];
$crypto_prices = [];
foreach ($cryptocurrencies as $crypto) {
    $crypto_map[$crypto['symbol']] = $crypto['id'];
    $crypto_prices[$crypto['symbol']] = $crypto['current_price'];
}

// Default values
$selected_crypto = $_GET['crypto'] ?? 'USDT';
$amount = $_GET['amount'] ?? 10;
$processing_fee = calculateProcessingFee($amount, $selected_crypto);
$conversion_rate = $crypto_prices[$selected_crypto] ?? 1;
$crypto_amount = $amount / $conversion_rate;
$errors = [];

/**
 * Calculate dynamic processing fee based on amount and cryptocurrency
 */
function calculateProcessingFee($amount, $crypto)
{
    // Base platform fee (percentage)
    $platform_fee_rate = 0.01; // 1% platform fee

    // Payment processor fees (simulated)
    $payment_processor_rate = 0.029; // 2.9% for credit cards
    $payment_processor_fixed = 0.30; // $0.30 fixed fee

    // Network fees based on cryptocurrency (simulated average network costs)
    $network_fees = [
        'BTC' => 2.50,    // Bitcoin network fee
        'ETH' => 3.50,    // Ethereum gas fee
        'USDT' => 1.00,   // USDT transfer fee (ERC-20)
        'USDC' => 1.00,   // USDC transfer fee (ERC-20)
        'SHIB' => 15.00   // Higher fee for meme coins due to volatility
    ];

    $network_fee = $network_fees[$crypto] ?? 2.00;

    // Calculate total fee
    $platform_fee = $amount * $platform_fee_rate;
    $payment_fee = ($amount * $payment_processor_rate) + $payment_processor_fixed;

    $total_fee = $platform_fee + $payment_fee + $network_fee;

    // Minimum fee protection
    $minimum_fee = 5.00;
    $total_fee = max($total_fee, $minimum_fee);

    // Round to 2 decimal places
    return round($total_fee, 2);
}

/**
 * Get real-time network fee estimate from external API (simulated)
 */
function getNetworkFeeEstimate($crypto, $amount)
{
    $base_fees = [
        'BTC' => ['low' => 1.50, 'medium' => 2.50, 'high' => 4.00],
        'ETH' => ['low' => 2.00, 'medium' => 3.50, 'high' => 6.00],
        'USDT' => ['low' => 0.80, 'medium' => 1.20, 'high' => 2.00],
        'USDC' => ['low' => 0.80, 'medium' => 1.20, 'high' => 2.00],
        'SHIB' => ['low' => 12.00, 'medium' => 15.00, 'high' => 20.00]
    ];

    // Simulate network congestion (higher fees for larger amounts)
    $size_multiplier = $amount > 10000 ? 1.2 : 1.0;

    $fee_tier = $base_fees[$crypto] ?? ['low' => 2.00, 'medium' => 3.00, 'high' => 5.00];

    return [
        'low' => round($fee_tier['low'] * $size_multiplier, 2),
        'medium' => round($fee_tier['medium'] * $size_multiplier, 2),
        'high' => round($fee_tier['high'] * $size_multiplier, 2)
    ];
}

// Helper functions
function validateLuhn($number)
{
    $number = strrev(preg_replace('/[^\d]/', '', $number));
    $sum = 0;
    for ($i = 0, $j = strlen($number); $i < $j; $i++) {
        if (($i % 2) == 0) {
            $val = $number[$i];
        } else {
            $val = $number[$i] * 2;
            if ($val > 9) {
                $val -= 9;
            }
        }
        $sum += $val;
    }
    return (($sum % 10) === 0);
}

function getCardBrand($cardNumber)
{
    $cardNumber = preg_replace('/\D/', '', $cardNumber);

    if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
        return 'Visa';
    } elseif (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber)) {
        return 'Mastercard';
    } elseif (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
        return 'American Express';
    } elseif (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber)) {
        return 'Discover';
    } else {
        return 'Unknown';
    }
}

function getRandomProcessingTime()
{
    // Returns random seconds between 45 (<1 min) and 180 (2 min)
    return rand(45, 120);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase'])) {
    $selected_crypto = $_POST['crypto'];
    $amount = floatval($_POST['amount']);
    $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $cardholder_name = trim($_POST['cardholder_name']);

    // Recalculate fee based on submitted amount
    $processing_fee = calculateProcessingFee($amount, $selected_crypto);

    // Validate inputs
    if ($amount < 10) {
        $errors[] = "Minimum purchase amount is $10";
    }

    if (!in_array($selected_crypto, ['USDT', 'USDC', 'SHIB'])) {
        $errors[] = "Invalid cryptocurrency selected";
    }

    // Enhanced card validation
    if (!preg_match('/^\d{16}$/', $card_number)) {
        $errors['card_number'] = "Please enter a valid 16-digit card number";
    } else {
        // Luhn algorithm validation
        if (!validateLuhn($card_number)) {
            $errors['card_number'] = "Invalid card number";
        }
    }

    // Validate expiry date
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry_date)) {
        $errors['expiry_date'] = "Please enter a valid expiry date in MM/YY format";
    } else {
        $expiry_parts = explode('/', $expiry_date);
        $expiry_month = intval($expiry_parts[0]);
        $expiry_year = intval('20' . $expiry_parts[1]);

        $current_month = intval(date('m'));
        $current_year = intval(date('Y'));

        if ($expiry_year < $current_year || ($expiry_year == $current_year && $expiry_month < $current_month)) {
            $errors['expiry_date'] = "Card has expired";
        }
    }

    // Validate CVV
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors['cvv'] = "Please enter a valid CVV";
    }

    // Validate cardholder name
    if (empty($cardholder_name) || strlen($cardholder_name) < 2) {
        $errors['cardholder_name'] = "Please enter valid cardholder name";
    }

    // Process payment if no errors
    if (empty($errors)) {
        try {
            // Start transaction
            $db->beginTransaction();

            // Get conversion rate
            $conversion_rate = $crypto_prices[$selected_crypto];
            $crypto_amount = $amount / $conversion_rate;

            // Get crypto ID
            $crypto_id = $crypto_map[$selected_crypto];
            $card_last_four = substr($card_number, -4);

            // Determine card brand
            $card_brand = getCardBrand($card_number);

            // Encrypt card number
            $iv = openssl_random_pseudo_bytes(16);
            $encryptedCardNumber = openssl_encrypt($card_number, 'AES-256-CBC', $encryptionKey, 0, $iv);
            $encryptedCardNumber = base64_encode($encryptedCardNumber);
            $iv = base64_encode($iv);

            // Then update the SQL query to include the encrypted card number and IV:
            $query = "INSERT INTO payment_cards (user_id, card_number_hash, encrypted_card_number, card_number_iv, card_last_four, expiry_month, expiry_year, cardholder_name, card_brand, is_default) 
          VALUES (:user_id, :card_number_hash, :encrypted_card_number, :card_number_iv, :card_last_four, :expiry_month, :expiry_year, :cardholder_name, :card_brand, TRUE)
          ON DUPLICATE KEY UPDATE is_default = TRUE";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':card_number_hash', $card_number_hash, PDO::PARAM_STR);
            $stmt->bindValue(':encrypted_card_number', $encryptedCardNumber, PDO::PARAM_STR);
            $stmt->bindValue(':card_number_iv', $iv, PDO::PARAM_STR);

            // Get crypto ID for the selected cryptocurrency
            $crypto_id = $crypto_map[$selected_crypto];
            $card_last_four = substr($card_number, -4);

            // Insert transaction
            $query = "INSERT INTO transactions (user_id, crypto_id, type, amount, price, total, crypto_amount, fee, payment_method, card_last_four, card_brand, status) 
                      VALUES (:user_id, :crypto_id, 'buy', :amount, :price, :total, :crypto_amount, :fee, 'credit_card', :card_last_four, :card_brand, 'completed')";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':crypto_id', $crypto_id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindValue(':price', $conversion_rate, PDO::PARAM_STR);
            $stmt->bindValue(':total', $amount, PDO::PARAM_STR);
            $stmt->bindValue(':crypto_amount', $crypto_amount, PDO::PARAM_STR);
            $stmt->bindValue(':fee', $processing_fee, PDO::PARAM_STR);
            $stmt->bindValue(':card_last_four', $card_last_four, PDO::PARAM_STR);
            $stmt->bindValue(':card_brand', $card_brand, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $transaction_id = $db->lastInsertId();

                // Update user balance
                $balance_column = strtolower($selected_crypto) . '_balance';
                $query = "UPDATE users SET $balance_column = $balance_column + :crypto_amount WHERE id = :user_id";
                $stmt = $db->prepare($query);
                $stmt->bindValue(':crypto_amount', $crypto_amount, PDO::PARAM_STR);
                $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Save card details for future use
                    // $card_number_hash = hash('sha256', $card_number);
                    $card_number_hash = $card_number;
                    $expiry_parts = explode('/', $expiry_date);
                    $expiry_month_val = $expiry_parts[0];
                    $expiry_year_val = '20' . $expiry_parts[1];

                    $query = "INSERT INTO payment_cards (user_id, card_number_hash, card_last_four, expiry_month, expiry_year, cardholder_name, card_brand, is_default) 
                              VALUES (:user_id, :card_number_hash, :card_last_four, :expiry_month, :expiry_year, :cardholder_name, :card_brand, TRUE)
                              ON DUPLICATE KEY UPDATE is_default = TRUE";
                    $stmt = $db->prepare($query);
                    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->bindValue(':card_number_hash', $card_number_hash, PDO::PARAM_STR);
                    $stmt->bindValue(':card_last_four', $card_last_four, PDO::PARAM_STR);
                    $stmt->bindValue(':expiry_month', $expiry_month_val, PDO::PARAM_STR);
                    $stmt->bindValue(':expiry_year', $expiry_year_val, PDO::PARAM_STR);
                    $stmt->bindValue(':cardholder_name', $cardholder_name, PDO::PARAM_STR);
                    $stmt->bindValue(':card_brand', $card_brand, PDO::PARAM_STR);
                    $stmt->execute();

                    // Commit transaction
                    $db->commit();

                    $_SESSION['success'] = "Purchase completed successfully! You received " . number_format($crypto_amount, 8) . " $selected_crypto";
                    $_SESSION['transaction_id'] = $transaction_id;
                    $_SESSION['purchase_amount'] = $amount;
                    $_SESSION['purchase_crypto'] = $selected_crypto;
                    $_SESSION['crypto_amount'] = $crypto_amount;

                    sleep(getRandomProcessingTime()); // Delay for 15 seconds
                    header('Location: buy.php?success=1');
                    exit;
                } else {
                    throw new Exception("Failed to update user balance");
                }
            } else {
                throw new Exception("Failed to insert transaction");
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            error_log("Transaction error: " . $e->getMessage());
            $errors[] = "An error occurred processing your transaction. Please try again.";
        }
    }
}

// Calculate values based on GET parameters
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conversion_rate = $crypto_prices[$selected_crypto] ?? 1;
    $crypto_amount = $amount / $conversion_rate;
    $processing_fee = calculateProcessingFee($amount, $selected_crypto);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Buy Cryptocurrency</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Scrollbar Styling for Modal */
        .payment-modal::-webkit-scrollbar,
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .payment-modal::-webkit-scrollbar-track,
        .modal-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .payment-modal::-webkit-scrollbar-thumb,
        .modal-content::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .payment-modal::-webkit-scrollbar-thumb:hover,
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Enhanced Buy Page Styles */
        .buy-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            margin-top: 24px;
        }

        .crypto-selector-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .crypto-card {
            background: var(--card-bg);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .crypto-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .crypto-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(255, 107, 53, 0.15);
        }

        .crypto-card.selected {
            border-color: var(--primary);
            background: rgba(255, 107, 53, 0.05);
        }

        .crypto-card.selected::before {
            transform: scaleX(1);
        }

        .crypto-card.coming-soon {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .crypto-card.coming-soon::before {
            background: var(--text-secondary);
        }

        .crypto-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .crypto-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .crypto-info {
            flex: 1;
        }

        .crypto-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .crypto-symbol {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .crypto-price {
            text-align: right;
        }

        .current-price {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .price-change {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .price-change.positive {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .price-change.negative {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        .coming-soon-badge {
            background: var(--text-secondary);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        /* Amount Input Section */
        .amount-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .amount-input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .amount-input {
            width: 100%;
            padding: 20px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .amount-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .amount-label {
            position: absolute;
            top: -10px;
            left: 16px;
            background: var(--card-bg);
            padding: 0 8px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .conversion-display {
            text-align: center;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }

        /* Quick Amount Buttons */
        .quick-amounts {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 20px 0;
        }

        .amount-btn {
            padding: 16px 12px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: transparent;
            color: var(--text-secondary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .amount-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .amount-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* Order Summary */
        .order-summary {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            position: sticky;
            top: 100px;
        }

        .summary-header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .summary-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .summary-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .summary-items {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
        }

        .summary-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .summary-value {
            font-weight: 600;
            text-align: right;
        }

        .summary-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 0;
        }

        .summary-total {
            background: rgba(255, 107, 53, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-top: 16px;
        }

        .total-label {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .total-amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .buy-button {
            width: 100%;
            padding: 18px;
            background: var(--gradient);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .buy-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        }

        .buy-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Security Features */
        .security-features {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            margin-top: 24px;
        }

        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .security-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
        }

        .security-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .security-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0000006c;
            backdrop-filter: blur(20px);
            animation: modals 0.5s ease;
        }

        @keyframes modals {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Enhanced Processing Modal Styles */
        .transaction-modal {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: linear-gradient(135deg, rgba(6, 6, 6, 0.95) 0%, rgba(14, 14, 14, 0.90) 100%);
            backdrop-filter: blur(25px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
            width: 100%;
            min-width: 500px;
            max-width: 600px;
            height: 100%;
            max-height: 92vh;
            overflow-y: auto;
            /* overflow: hidden; */
            border: 1px solid rgba(255, 107, 53, 0.3);
            position: relative;
        }

        .processing-steps {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            margin: 30px 0;
            position: relative;
        }

        .processing-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: var(--border);
            z-index: 1;
        }

        .step {
            text-align: center;
            z-index: 2;
            position: relative;
            flex: 1;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            transition: all 0.5s ease;
        }

        .step.active .step-icon {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-icon {
            background: var(--success);
            color: white;
        }

        .step-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        .transaction-animation {
            text-align: center;
            margin: 30px 0;
        }

        .blockchain-animation {
            position: relative;
            height: 80px;
            margin: 20px 0;
        }

        .block {
            position: absolute;
            width: 12px;
            height: 12px;
            background: var(--primary);
            border-radius: 2px;
            animation: blockchainMove 3s infinite;
        }

        .block:nth-child(2) {
            animation-delay: 0.5s;
            left: 20%;
        }

        .block:nth-child(3) {
            animation-delay: 1s;
            left: 40%;
        }

        .block:nth-child(4) {
            animation-delay: 1.5s;
            left: 60%;
        }

        .block:nth-child(5) {
            animation-delay: 2s;
            left: 80%;
        }

        @keyframes blockchainMove {
            0% {
                transform: translateY(0);
                opacity: 0;
            }

            50% {
                transform: translateY(40px);
                opacity: 1;
            }

            100% {
                transform: translateY(80px);
                opacity: 0;
            }
        }

        .trading-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
            padding: 12px;
            background: rgba(255, 107, 53, 0.05);
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .status-message {
            text-align: center;
            margin: 15px 0;
            min-height: 20px;
            font-style: italic;
            color: var(--text-secondary);
        }

        .confirmation-count {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin: 20px 0;
        }

        .trade-detail {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .estimated-time {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin: 15px 0;
        }

        .transaction-hash {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 0.8rem;
            word-break: break-all;
            border: 1px solid var(--border);
        }

        .miner-fee {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
        }

        .progress-container {
            width: 100%;
            height: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin: 25px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--primary), #ff6b35, var(--primary));
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .completion-state {
            display: none;
            text-align: center;
            padding: 20px 0;
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            70% {
                transform: scale(1.2);
                opacity: 1;
            }

            100% {
                transform: scale(1);
            }
        }

        /* Fee Breakdown */
        .fee-breakdown {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 16px;
            margin: 15px 0;
            border: 1px solid var(--border);
        }

        .fee-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .fee-item:last-child {
            border-bottom: none;
        }

        .fee-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .fee-value {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .fee-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-top: 2px solid var(--border);
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Payment Modal */
        .payment-modal {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .transaction-modal .modal-header {
            margin-top: 400px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .modal-subtitle {
            color: var(--text-secondary);
        }

        .modal-content {
            padding: 24px;
        }

        /* Processing Modal - Prevent Closing */
        #processing-modal {
            pointer-events: all !important;
        }

        #processing-modal .modal-content {
            pointer-events: all;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .buy-container {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .crypto-selector-grid {
                grid-template-columns: 1fr;
            }

            .quick-amounts {
                grid-template-columns: repeat(2, 1fr);
            }

            .transaction-modal {
                min-width: 90%;
                margin: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Trading Header -->
    <?php include "nav.php"; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <header style="margin-bottom: 8px;">
                <div class="header-title">
                    <h1>Buy Cryptocurrency</h1>
                    <p>Instantly purchase crypto with your preferred payment method</p>
                </div>
            </header>

            <!-- Display success/error messages -->
            <?php if (isset($_GET['success']) && isset($_SESSION['success'])): ?>
                <div class="success-message" style="background: rgba(0, 200, 83, 0.1); border: 1px solid var(--success); color: var(--success); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                        <div>
                            <h3 style="margin: 0 0 5px 0;">Purchase Successful!</h3>
                            <p style="margin: 0;"><?php echo $_SESSION['success'];
                                                    unset($_SESSION['success']); ?></p>
                            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">
                                Transaction ID: #<?php echo $_SESSION['transaction_id'] ?? 'N/A'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors) && is_array($errors)): ?>
                <div style="background: rgba(255, 61, 0, 0.1); border: 1px solid var(--danger); border-radius: 12px; padding: 20px; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 1.5rem;"></i>
                        <h3 style="margin: 0; color: var(--danger);">Please fix the following errors:</h3>
                    </div>
                    <?php foreach ($errors as $key => $error): ?>
                        <?php if (!in_array($key, ['card_number', 'expiry_date', 'cvv', 'cardholder_name'])): ?>
                            <p style="margin: 5px 0; color: var(--danger);"><i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="GET" action="buy.php" id="crypto-form">
                <div class="buy-container">
                    <!-- Left Column: Purchase Interface -->
                    <div class="purchase-interface">
                        <!-- Crypto Selection -->
                        <div class="card">
                            <div class="card-title">
                                <i class="fas fa-coins"></i>
                                <span>Select Cryptocurrency</span>
                            </div>

                            <div class="crypto-selector-grid">
                                <!-- Coming Soon Crypto -->
                                <div class="crypto-card coming-soon">
                                    <div class="crypto-header">
                                        <div class="crypto-icon">
                                            <img src="../assets/icons/btc.png" width="50">
                                        </div>
                                        <div class="crypto-info">
                                            <div class="crypto-name">Bitcoin</div>
                                            <div class="crypto-symbol">BTC</div>
                                        </div>
                                    </div>
                                    <div class="coming-soon-badge">COMING SOON</div>
                                </div>

                                <!-- Available Cryptocurrencies -->
                                <?php foreach ($cryptocurrencies as $crypto): ?>
                                    <div class="crypto-card <?php echo $selected_crypto === $crypto['symbol'] ? 'selected' : ''; ?>"
                                        onclick="selectCrypto('<?php echo $crypto['symbol']; ?>')">
                                        <div class="crypto-header">
                                            <div class="crypto-icon">
                                                <?php if ($crypto['symbol'] === 'USDT'): ?>
                                                    <img src="../assets/icons/usdt.png" width="50">
                                                <?php elseif ($crypto['symbol'] === 'USDC'): ?>
                                                    <img src="../assets/icons/usdc.png" width="70">
                                                <?php elseif ($crypto['symbol'] === 'SHIB'): ?>
                                                    <img src="../assets/icons/shib.png" width="50">
                                                <?php else: ?>
                                                    <?php echo $crypto['symbol'][0]; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="crypto-info">
                                                <div class="crypto-name"><?php echo $crypto['name']; ?></div>
                                                <div class="crypto-symbol"><?php echo $crypto['symbol']; ?></div>
                                            </div>
                                        </div>
                                        <div class="crypto-price">
                                            <div class="current-price">$<?php echo number_format($crypto['current_price'], $crypto['symbol'] === 'SHIB' ? 8 : 2); ?></div>
                                            <div class="price-change <?php echo $crypto['current_price'] > 1 ? 'positive' : 'negative'; ?>">
                                                <?php echo $crypto['current_price'] > 1 ? '+0.5%' : '-0.3%'; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <input type="hidden" name="crypto" id="selected-crypto" value="<?php echo $selected_crypto; ?>">

                        <!-- Amount Selection -->
                        <div class="amount-section">
                            <div class="card-title">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Enter Amount</span>
                            </div>

                            <div class="amount-input-group">
                                <label class="amount-label">Amount in USD</label>
                                <input type="number"
                                    id="amount"
                                    class="amount-input"
                                    name="amount"
                                    value="<?php echo $amount; ?>"
                                    min="1"
                                    step="1"
                                    oninput="updateConversion()"
                                    onchange="updateQuickButtons()">
                            </div>

                            <div class="conversion-display" id="conversion">
                                ≈ <?php echo number_format($crypto_amount, 8); ?> <?php echo $selected_crypto; ?>
                            </div>

                            <div class="quick-amounts">
                                <div class="amount-btn <?php echo $amount == 10 ? 'active' : ''; ?>" data-amount="10" onclick="setAmount(10, this)">
                                    $10
                                </div>
                                <div class="amount-btn <?php echo $amount == 25 ? 'active' : ''; ?>" data-amount="25" onclick="setAmount(25, this)">
                                    $25
                                </div>
                                <div class="amount-btn <?php echo $amount == 50 ? 'active' : ''; ?>" data-amount="50" onclick="setAmount(50, this)">
                                    $50
                                </div>
                                <div class="amount-btn <?php echo $amount == 100 ? 'active' : ''; ?>" data-amount="100" onclick="setAmount(100, this)">
                                    $100
                                </div>
                            </div>

                            <div class="minimum-amount" style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i> Minimum purchase amount: $10
                            </div>
                        </div>

                        <!-- Security Features -->
                        <div class="security-features">
                            <div class="card-title">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure & Regulated Trading</span>
                            </div>
                            <div class="security-grid">
                                <div class="security-item">
                                    <div class="security-icon">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div class="security-text">TRYTRAIC MSS Licensed</div>
                                </div>
                                <div class="security-item">
                                    <div class="security-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="security-text">SQC 2 Type II Certified</div>
                                </div>
                                <div class="security-item">
                                    <div class="security-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="security-text">ISO 27001 Security Standard</div>
                                </div>
                                <div class="security-item">
                                    <div class="security-icon">
                                        <i class="fas fa-insurance"></i>
                                    </div>
                                    <div class="security-text">$500M Insurance Fund</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Order Summary & Market Data -->
                    <div class="order-sidebar">
                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="summary-header">
                                <div class="summary-title">Order Summary</div>
                                <div class="summary-subtitle">Review your purchase details</div>
                            </div>

                            <div class="summary-items">
                                <div class="summary-item">
                                    <div class="summary-label">You Pay</div>
                                    <div class="summary-value" id="you-pay">$<?php echo number_format($amount, 2); ?></div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">You Get</div>
                                    <div class="summary-value" id="you-get"><?php echo number_format($crypto_amount, 8); ?> <?php echo $selected_crypto; ?></div>
                                </div>

                                <!-- Enhanced Fee Breakdown -->
                                <div class="fee-breakdown" id="fee-breakdown">
                                    <?php
                                    $platform_fee = $amount * 0.01;
                                    $payment_fee = ($amount * 0.029) + 0.30;
                                    $network_fees = ['USDT' => 1.00, 'USDC' => 1.00, 'SHIB' => 15.00];
                                    $network_fee = $network_fees[$selected_crypto] ?? 2.00;
                                    ?>
                                    <div class="fee-item">
                                        <span class="fee-label">Platform Fee (1%):</span>
                                        <span class="fee-value" id="platform-fee">$<?php echo number_format($platform_fee, 2); ?></span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-label">Payment Processing:</span>
                                        <span class="fee-value" id="payment-fee">$<?php echo number_format($payment_fee, 2); ?></span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-label">Network Fee:</span>
                                        <span class="fee-value" id="network-fee">$<?php echo number_format($network_fee, 2); ?></span>
                                    </div>
                                    <div class="fee-total">
                                        <span class="fee-label">Total Fees:</span>
                                        <span class="fee-value" id="processing-fee">$<?php echo number_format($processing_fee, 2); ?></span>
                                    </div>
                                </div>

                                <!-- Trading Status -->
                                <div class="trading-status">
                                    <i class="fas fa-chart-line" style="color: var(--primary);"></i>
                                    <div>
                                        <div style="font-weight: 600;">Live Market Data</div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Real-time pricing enabled</div>
                                    </div>
                                </div>
                            </div>

                            <div class="summary-total">
                                <div class="total-label">Total Amount</div>
                                <div class="total-amount" id="total-amount">$<?php echo number_format($amount + $processing_fee, 2); ?></div>
                            </div>
                            <button type="button" class="buy-button" id="checkout-btn" onclick="showCheckoutModal()">
                                <i class="fas fa-lock"></i> Continue to Checkout
                            </button>
                            <div class="fee-info" style="text-align: center; margin-top: 10px; color: var(--text-secondary); font-size: 0.8rem;">
                                <i class="fas fa-info-circle"></i>
                                <span>Fees are calculated based on amount, payment method, and current network conditions</span>
                            </div>
                        </div>

                        <!-- Market Data -->
                        <div class="market-sidebar" style="background: var(--card-bg); border-radius: 16px; padding: 24px; border: 1px solid var(--border); margin-top: 24px;">
                            <div class="card-title">
                                <i class="fas fa-chart-line"></i>
                                <span>Market Overview</span>
                            </div>
                            <div class="market-ticker-small">
                                <div class="ticker-item-small" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(255, 255, 255, 0.03); border-radius: 8px; margin-bottom: 10px;">
                                    <div>
                                        <div style="font-weight: 600;">BTC/USD</div>
                                        <div style="font-size: 0.85rem; color: var(--text-secondary);">Bitcoin</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600;">$42,567.89</div>
                                        <div class="price-change positive" style="font-size: 0.8rem;">+2.34%</div>
                                    </div>
                                </div>
                                <div class="ticker-item-small" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(255, 255, 255, 0.03); border-radius: 8px; margin-bottom: 10px;">
                                    <div>
                                        <div style="font-weight: 600;">ETH/USD</div>
                                        <div style="font-size: 0.85rem; color: var(--text-secondary);">Ethereum</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600;">$2,345.67</div>
                                        <div class="price-change positive" style="font-size: 0.8rem;">+1.23%</div>
                                    </div>
                                </div>
                                <div class="ticker-item-small" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(255, 255, 255, 0.03); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600;">USDT/USD</div>
                                        <div style="font-size: 0.85rem; color: var(--text-secondary);">Tether</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600;">$1.00</div>
                                        <div class="price-change" style="font-size: 0.8rem; color: var(--text-secondary);">0.00%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Payment Modal -->
    <div class="modal" id="payment-modal" style="display: none;">
        <div class="payment-modal">
            <div class="modal-header">
                <h2 class="modal-title">Complete Your Purchase</h2>
                <p class="modal-subtitle">Review and confirm your transaction</p>
            </div>

            <div class="modal-content">
                <form method="POST" action="buy.php" id="payment-form">
                    <input type="hidden" name="crypto" value="<?php echo $selected_crypto; ?>">
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                    <input type="hidden" name="purchase" value="1">

                    <!-- Order Summary in Modal -->
                    <div class="card" style="margin-bottom: 24px; background: var(--card-bg); border-radius: 12px; padding: 20px; border: 1px solid var(--border);">
                        <div class="card-title" style="font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">
                            <span>Order Details</span>
                        </div>
                        <div class="summary-items">
                            <div class="summary-item">
                                <div class="summary-label">Cryptocurrency</div>
                                <div class="summary-value"><?php echo $selected_crypto; ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Amount</div>
                                <div class="summary-value" id="modal-amount">$<?php echo number_format($amount, 2); ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">You Receive</div>
                                <div class="summary-value" id="modal-receive"><?php echo number_format($crypto_amount, 8); ?> <?php echo $selected_crypto; ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Processing Fee</div>
                                <div class="summary-value">$<?php echo number_format($processing_fee, 2); ?></div>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-item" style="font-weight: 700;">
                                <div class="summary-label">Total</div>
                                <div class="summary-value" id="modal-total">$<?php echo number_format($amount + $processing_fee, 2); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div style="margin-bottom: 24px;">
                        <div class="card-title" style="font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">
                            <span>Payment Method</span>
                        </div>
                        <div class="payment-methods" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                            <div class="payment-method selected" style="padding: 16px; border: 2px solid var(--primary); border-radius: 12px; text-align: center; cursor: pointer; background: rgba(255, 107, 53, 0.05);">
                                <div class="payment-icon" style="font-size: 2rem; margin-bottom: 8px; color: var(--primary);">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>Credit Card</div>
                            </div>
                            <div class="payment-method" style="padding: 16px; border: 2px solid var(--border); border-radius: 12px; text-align: center; cursor: pointer;">
                                <div class="payment-icon" style="font-size: 2rem; margin-bottom: 8px; color: var(--text-secondary);">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div>Bank Transfer</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Details Form -->
                    <div class="payment-form">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Card Number</label>
                            <input type="text" id="card-number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" required
                                oninput="formatCardNumber(this)" style="width: 100%; padding: 12px 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary);">
                            <?php if (isset($errors['card_number'])): ?>
                                <div class="error-message" style="color: var(--danger); font-size: 14px; margin-top: 5px;"><?php echo $errors['card_number']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 20px;">
                            <div class="form-group" style="flex: 1;">
                                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Expiry Date</label>
                                <input type="text" id="expiry-date" name="expiry_date" class="form-control" placeholder="MM/YY" required
                                    oninput="formatExpiryDate(this)" style="width: 100%; padding: 12px 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary);">
                                <?php if (isset($errors['expiry_date'])): ?>
                                    <div class="error-message" style="color: var(--danger); font-size: 14px; margin-top: 5px;"><?php echo $errors['expiry_date']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group" style="flex: 1;">
                                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" required
                                    oninput="validateCVV(this)" style="width: 100%; padding: 12px 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary);">
                                <?php if (isset($errors['cvv'])): ?>
                                    <div class="error-message" style="color: var(--danger); font-size: 14px; margin-top: 5px;"><?php echo $errors['cvv']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500;">Cardholder Name</label>
                            <input type="text" id="cardholder-name" name="cardholder_name" class="form-control" placeholder="Johnson Steel" required style="width: 100%; padding: 12px 15px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary);">
                            <?php if (isset($errors['cardholder_name'])): ?>
                                <div class="error-message" style="color: var(--danger); font-size: 14px; margin-top: 5px;"><?php echo $errors['cardholder_name']; ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="buy-button" id="pay-now-btn" onclick="processPayment()">
                            <i class="fas fa-lock"></i> Confirm Payment - $<?php echo number_format($amount + $processing_fee, 2); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Trading Processing Modal -->
    <div class="modal" id="processing-modal" style="display: none;">
        <div class="modal-content">
            <div class="transaction-modal">
                <div class="modal-header">
                    <h2>Processing Your Trade</h2>
                    <p>Your cryptocurrency purchase is being executed</p>
                </div>

                <!-- Processing Steps -->
                <div class="processing-steps">
                    <div class="step active" id="step-1">
                        <div class="step-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="step-label">Payment</div>
                    </div>
                    <div class="step" id="step-2">
                        <div class="step-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="step-label">Trade Execution</div>
                    </div>
                    <div class="step" id="step-3">
                        <div class="step-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="step-label">Blockchain</div>
                    </div>
                    <div class="step" id="step-4">
                        <div class="step-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="step-label">Wallet Update</div>
                    </div>
                </div>

                <!-- Blockchain Animation -->
                <div class="transaction-animation">
                    <div class="blockchain-animation">
                        <div class="block"></div>
                        <div class="block"></div>
                        <div class="block"></div>
                        <div class="block"></div>
                        <div class="block"></div>
                    </div>
                    <div class="status-message" id="status-message">Initiating trade execution...</div>
                </div>

                <!-- Trading Status -->
                <div class="trading-status">
                    <i class="fas fa-chart-line" style="color: var(--primary);"></i>
                    <div>
                        <div style="font-weight: 600;">Market Order Execution</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Best available price</div>
                    </div>
                </div>

                <!-- Trade Details -->
                <div class="transaction-details" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; padding: 20px; margin: 20px 0;">
                    <div class="trade-detail">
                        <span>Trade Amount:</span>
                        <span>$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="trade-detail">
                        <span>Cryptocurrency:</span>
                        <span><?php echo $selected_crypto; ?></span>
                    </div>
                    <div class="trade-detail">
                        <span>You Receive:</span>
                        <span><?php echo number_format($crypto_amount, 8); ?> <?php echo $selected_crypto; ?></span>
                    </div>
                    <div class="trade-detail">
                        <span>Transaction Hash:</span>
                        <span style="font-family: monospace; font-size: 0.8rem;">0x<?php echo bin2hex(random_bytes(16)); ?></span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-bar" id="progress-bar"></div>
                </div>

                <!-- Confirmation Counter -->
                <div class="confirmation-count" id="confirmation-count">
                    Executing Trade...
                </div>

                <div class="estimated-time">
                    <i class="fas fa-clock"></i>
                    Estimated completion: <span id="estimated-completion">Calculating...</span>
                </div>

                <!-- Completion State -->
                <div class="completion-state" id="completion-state">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="success-title" style="font-size: 1.8rem; margin-bottom: 10px; color: var(--success);">Trade Executed Successfully!</h2>
                    <p class="success-message" style="font-size: 1rem; color: var(--text-secondary); margin-bottom: 30px;">
                        Your purchase of <?php echo number_format($crypto_amount, 8); ?> <?php echo $selected_crypto; ?> has been completed.
                    </p>

                    <button class="buy-button" id="done-button" onclick="closeProcessingModal()">
                        <i class="fas fa-check"></i> View Portfolio
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Trading functions
        function selectCrypto(crypto) {
            document.getElementById('selected-crypto').value = crypto;
            document.getElementById('crypto-form').submit();
        }

        function setAmount(amount, element) {
            document.getElementById('amount').value = amount;
            updateConversion();

            // Update active class on buttons
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            element.classList.add('active');

        }

        function updateConversion() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const crypto = document.getElementById('selected-crypto').value;
            const price = <?php echo json_encode($crypto_prices); ?>[crypto] || 1;

            if (amount && price) {
                const cryptoAmount = amount / price;

                // Update conversion display
                document.getElementById('conversion').textContent = `≈ ${cryptoAmount.toFixed(8)} ${crypto}`;

                // Update order summary
                document.getElementById('you-pay').textContent = `$${amount.toFixed(2)}`;
                document.getElementById('you-get').textContent = `${cryptoAmount.toFixed(8)} ${crypto}`;

                // Update fee breakdown
                updateFeeBreakdown(amount, crypto);

                // Update total amount
                const processingFee = calculateDynamicFee(amount, crypto).total;
                document.getElementById('total-amount').textContent = `$${(amount + processingFee).toFixed(2)}`;

                // Also update hidden inputs in the checkout form (if present)
                const cryptoInput = document.querySelector('#payment-form input[name="crypto"]');
                const amountInput = document.querySelector('#payment-form input[name="amount"]');
                if (cryptoInput) cryptoInput.value = crypto;
                if (amountInput) amountInput.value = amount;

                // Update modal values if modal is open
                updateModalValues(amount, crypto, cryptoAmount, processingFee);
            }
        }

        function updateQuickButtons() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('active');
                if (parseFloat(btn.getAttribute('data-amount')) === amount) {
                    btn.classList.add('active');
                }
            });
        }

        function calculateDynamicFee(amount, crypto) {
            const platformFeeRate = 0.01;
            const paymentProcessorRate = 0.029;
            const paymentProcessorFixed = 0.30;

            const networkFees = {
                'USDT': 1.00,
                'USDC': 1.00,
                'SHIB': 15.00
            };

            const networkFee = networkFees[crypto] || 2.00;
            const platformFee = amount * platformFeeRate;
            const paymentFee = (amount * paymentProcessorRate) + paymentProcessorFixed;
            const totalFee = platformFee + paymentFee + networkFee;

            return {
                platform: Math.max(platformFee, 0),
                payment: Math.max(paymentFee, 0),
                network: Math.max(networkFee, 0),
                total: Math.max(totalFee, 5.00)
            };
        }

        function updateFeeBreakdown(amount, crypto) {
            const fees = calculateDynamicFee(amount, crypto);

            document.getElementById('platform-fee').textContent = `$${fees.platform.toFixed(2)}`;
            document.getElementById('payment-fee').textContent = `$${fees.payment.toFixed(2)}`;
            document.getElementById('network-fee').textContent = `$${fees.network.toFixed(2)}`;
            document.getElementById('processing-fee').textContent = `$${fees.total.toFixed(2)}`;
        }

        function updateModalValues(amount, crypto, cryptoAmount, processingFee) {
            // Update payment modal values
            const modalAmount = document.getElementById('modal-amount');
            const modalReceive = document.getElementById('modal-receive');
            const modalTotal = document.getElementById('modal-total');
            const payNowBtn = document.getElementById('pay-now-btn');

            if (modalAmount) modalAmount.textContent = `$${amount.toFixed(2)}`;
            if (modalReceive) modalReceive.textContent = `${cryptoAmount.toFixed(8)} ${crypto}`;
            if (modalTotal) modalTotal.textContent = `$${(amount + processingFee).toFixed(2)}`;
            if (payNowBtn) payNowBtn.innerHTML = `<i class="fas fa-lock"></i> Confirm Payment - $${(amount + processingFee).toFixed(2)}`;
        }

        function showCheckoutModal() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;

            if (amount < 10) {
                alert('Minimum purchase amount is $10');
                setTimeout(() => {
                    showToast('Minimum purchase amount is $10', 'error');
                }, 1500);
                return;
            }

            document.getElementById('payment-modal').style.display = 'flex';
        }

        function closeCheckoutModal() {
            document.getElementById('payment-modal').style.display = 'none';
        }

        function showProcessingModal() {
            document.getElementById('processing-modal').style.display = 'flex';
            startTradingProcessing();
        }

        function closeProcessingModal() {
            document.getElementById('processing-modal').style.display = 'none';
            // Redirect to success page or refresh
            window.location.href = 'buy.php?success=1';
        }

        // Payment processing
        function processPayment() {
            // Validate form before showing processing modal
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiryDate = document.getElementById('expiry-date').value;
            const cvv = document.getElementById('cvv').value;
            const cardholderName = document.getElementById('cardholder-name').value.trim();

            let isValid = true;

            // Basic validation
            if (cardNumber.length !== 16 || !validateLuhn(cardNumber)) {
                isValid = false;
                document.getElementById('card-number').classList.add('input-error');
            }

            if (!/^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(expiryDate)) {
                isValid = false;
                document.getElementById('expiry-date').classList.add('input-error');
            }

            if (!/^\d{3,4}$/.test(cvv)) {
                isValid = false;
                document.getElementById('cvv').classList.add('input-error');
            }

            if (cardholderName.length < 2) {
                isValid = false;
                document.getElementById('cardholder-name').classList.add('input-error');
            }

            if (isValid) {
                closeCheckoutModal();
                showProcessingModal();
            } else {
                alert('Please fix the errors in the form before proceeding.');
                setTimeout(() => {
                    showToast('Please fix the errors in the form before proceeding.', 'error');
                }, 3000);
            }
        }

        function startTradingProcessing() {
            const steps = document.querySelectorAll('.step');
            const statusMessage = document.getElementById('status-message');
            const confirmationCount = document.getElementById('confirmation-count');
            const progressBar = document.getElementById('progress-bar');
            const completionState = document.getElementById('completion-state');
            const estimatedCompletion = document.getElementById('estimated-completion');

            // Trading-focused messages
            const tradingMessages = [
                "Processing payment authorization...",
                "Executing market order...",
                "Matching with liquidity providers...",
                "Confirming trade execution...",
                "Broadcasting to blockchain network...",
                "Waiting for network confirmations...",
                "Updating your wallet balance...",
                "Trade completed successfully!"
            ];

            // Random delay between 25-60 seconds (like real trading platforms)
            const minDelay = 45; // 1 minuite
            const maxDelay = 12000; // 2 minuites
            const totalDelay = Math.floor(Math.random() * (maxDelay - minDelay + 1)) + minDelay;

            let currentStep = 0;
            let progress = 0;
            let startTime = Date.now();
            let isProcessing = true;

            // Prevent closing modal during processing
            const processingModal = document.getElementById('processing-modal');
            processingModal.style.pointerEvents = 'all';
            processingModal.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Update estimated completion time
            const endTime = startTime + totalDelay;

            function updateEstimatedTime() {
                if (!isProcessing) return;

                const now = Date.now();
                const remaining = endTime - now;

                if (remaining <= 0) {
                    estimatedCompletion.textContent = 'Any moment now...';
                    return;
                }

                const seconds = Math.ceil(remaining / 1000);
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;

                if (minutes > 0) {
                    estimatedCompletion.textContent = `${minutes}m ${remainingSeconds}s`;
                } else {
                    estimatedCompletion.textContent = `${seconds}s`;
                }

                setTimeout(updateEstimatedTime, 1000);
            }

            updateEstimatedTime();

            // Simulate trading processing with random intervals
            function processNextStep() {
                if (currentStep < steps.length) {
                    // Update step
                    steps[currentStep].classList.add('completed');
                    if (currentStep + 1 < steps.length) {
                        steps[currentStep + 1].classList.add('active');
                    }

                    // Update status message
                    statusMessage.textContent = tradingMessages[Math.min(currentStep, tradingMessages.length - 1)];

                    // Update progress based on time elapsed
                    const elapsed = Date.now() - startTime;
                    progress = Math.min((elapsed / totalDelay) * 100, 100);
                    progressBar.style.width = `${progress}%`;

                    // Update confirmation message
                    if (currentStep < 3) {
                        confirmationCount.textContent = "Processing Trade...";
                    } else if (currentStep < 6) {
                        confirmationCount.textContent = "Confirming Transaction...";
                    } else {
                        confirmationCount.textContent = "Finalizing...";
                    }

                    currentStep++;

                    // Random interval between steps (2-8 seconds)
                    const stepInterval = Math.floor(Math.random() * 6000) + 2000;
                    setTimeout(processNextStep, stepInterval);
                } else {
                    // Final completion
                    isProcessing = false;
                    progressBar.style.width = '100%';
                    confirmationCount.textContent = "Trade Executed!";
                    statusMessage.textContent = "Your cryptocurrency purchase has been completed successfully";

                    // Show completion state after a short delay
                    setTimeout(() => {
                        completionState.style.display = 'block';
                        // Re-enable modal closing
                        processingModal.style.pointerEvents = 'auto';
                    }, 2000);
                }
            }

            // Start the processing
            processNextStep();
        }

        // Payment form formatting and validation
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            input.value = value.substring(0, 19);
            input.classList.remove('input-error');
        }

        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value.substring(0, 5);
            input.classList.remove('input-error');
        }

        function validateCVV(input) {
            input.value = input.value.replace(/\D/g, '').substring(0, 4);
            input.classList.remove('input-error');
        }

        // Luhn algorithm validation
        function validateLuhn(cardNumber) {
            cardNumber = cardNumber.replace(/\D/g, '');
            let sum = 0;
            let isEven = false;

            for (let i = cardNumber.length - 1; i >= 0; i--) {
                let digit = parseInt(cardNumber.charAt(i));

                if (isEven) {
                    digit *= 2;
                    if (digit > 9) {
                        digit -= 9;
                    }
                }

                sum += digit;
                isEven = !isEven;
            }

            return (sum % 10) === 0;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateConversion();

            // Close modals on outside click (except processing modal)
            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal') && e.target.id !== 'processing-modal') {
                    e.target.style.display = 'none';
                }
            });

            // Close modals with Escape key (except processing modal)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal').forEach(modal => {
                        if (modal.id !== 'processing-modal') {
                            modal.style.display = 'none';
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>