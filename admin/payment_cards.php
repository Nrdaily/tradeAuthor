<?php
// admin/payment_cards.php
require_once 'session.php';
require_once '../app/config/encryption.php';
$encryptionKey = ENCRYPTION_KEY;

// Get all payment cards with user information
$query = "
    SELECT pc.*, u.first_name, u.last_name, u.email 
    FROM payment_cards pc 
    JOIN users u ON pc.user_id = u.id 
    ORDER BY pc.created_at DESC
";
$stmt = $db->prepare($query);
$stmt->execute();
$payment_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to decrypt card number
function decryptCardNumber($encryptedCardNumber, $iv) {
    // Use a secure encryption key (store this in a config file in production)
    $encryptionKey = ENCRYPTION_KEY;
    
    if (empty($encryptedCardNumber) || empty($iv)) {
        return false;
    }
    
    try {
        $encryptedCardNumber = base64_decode($encryptedCardNumber);
        $iv = base64_decode($iv);
        return openssl_decrypt($encryptedCardNumber, 'AES-256-CBC', $encryptionKey, 0, $iv);
    } catch (Exception $e) {
        error_log("Decryption error: " . $e->getMessage());
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <title>Trade Author - Payment Cards</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card-details {
            background-color: #1a1a1a;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #553f33ff;
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e97c0eff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .user-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: rgba(233, 113, 14, 0.1);
            border-radius: 8px;
        }
        
        .card-number-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .card-number {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 1px;
            padding: 15px;
            background-color: #000000ff;
            border-radius: 8px;
            min-height: 50px;
            display: flex;
            align-items: center;
            flex-grow: 1;
            position: relative;
            overflow: hidden;
        }
        
        .card-number.hidden {
            filter: blur(8px);
            user-select: none;
            -webkit-user-select: none;
        }
        
        .toggle-btn {
            padding: 10px 15px;
            background-color: #e9660eff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 100px;
            justify-content: center;
        }
        
        .toggle-btn:hover {
            background-color: #a13503ff;
        }
        
        .toggle-btn.reveal {
            background-color: #10b981;
        }
        
        .toggle-btn.reveal:hover {
            background-color: #059669;
        }
        
        .toggle-btn.hide {
            background-color: #ef4444;
        }
        
        .toggle-btn.hide:hover {
            background-color: #dc2626;
        }
        
        .card-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-weight: 500;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .security-warning {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .copy-btn {
            padding: 8px 12px;
            background-color: #1e293b;
            color: #94a3b8;
            border: 1px solid #554033ff;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 10px;
        }
        
        .copy-btn:hover {
            background-color: #553f33ff;
            color: #e2e8f0;
        }
        
        .copy-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            display: none;
            z-index: 1000;
        }
        
        .decryption-error {
            color: #ef4444;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php require_once "nav.php" ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="header-title">
                <h1>Payment Cards</h1>
            </div>

            <div class="user-actions">
                <div class="user-profile">
                    <div class="avatar">
                        <img src="../assets/icons/user.jpg" alt="">
                    </div>
                    <span><?php echo $_SESSION['admin_username']; ?></span>
                </div>
            </div>
        </header>

        <div class="admin-container">
            <div class="security-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <div>
                    <strong>Security Notice:</strong> Full card numbers are displayed for administrative purposes only. 
                    Ensure you follow PCI DSS compliance guidelines when handling card data.
                    <br><small>Access to this information is logged and monitored.</small>
                </div>
            </div>
            
            <?php if (count($payment_cards) > 0): ?>
                <?php foreach ($payment_cards as $card): 
                    // Decrypt card number
                    $fullCardNumber = '';
                    $canDecrypt = false;
                    
                    if (!empty($card['encrypted_card_number']) && !empty($card['card_number_iv'])) {
                        $fullCardNumber = decryptCardNumber($card['encrypted_card_number'], $card['card_number_iv']);
                        $canDecrypt = ($fullCardNumber !== false);
                    }
                    
                    // Format card number for display
                    $maskedCardNumber =  $card['card_number_hash'];
                    $formattedCardNumber = $canDecrypt ? implode(' ', str_split($fullCardNumber, 4)) : $maskedCardNumber;
                ?>
                <div class="card-details">
                    <div class="card-header">
                        <div class="card-brand">
                            <div class="card-brand-icon">
                                <?php echo $card['card_brand'][0]; ?>
                            </div>
                            <div>
                                <h3><?php echo $card['card_brand']; ?></h3>
                                <p><?php echo $maskedCardNumber; ?></p>
                            </div>
                        </div>
                        <div class="card-date">
                            <?php echo $card['expiry_month']; ?>/<?php echo $card['expiry_year']; ?>
                        </div>
                    </div>
                    
                    <div class="user-info">
                        <strong><?php echo $card['first_name'] . ' ' . $card['last_name']; ?></strong> 
                        <span style="color: #94a3b8;">(<?php echo $card['email']; ?>)</span>
                    </div>
                    
                    <div class="card-number-container">
                        <div class="card-number hidden" id="card-number-<?php echo $card['id']; ?>">
                            <?php echo $formattedCardNumber; ?>
                        </div>
                        <button class="toggle-btn <?php echo $canDecrypt ? 'reveal' : ''; ?>" 
                                data-card-id="<?php echo $card['id']; ?>" 
                                <?php echo !$canDecrypt ? 'disabled' : ''; ?>>
                            <i class="fas fa-eye"></i> <?php echo $canDecrypt ? 'Show' : 'N/A'; ?>
                        </button>
                        <?php if ($canDecrypt): ?>
                        <button class="copy-btn" data-card-id="<?php echo $card['id']; ?>" data-card-number="<?php echo $fullCardNumber; ?>">
                            <i class="fas fa-copy"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$canDecrypt): ?>
                    <div class="decryption-error">
                        <i class="fas fa-exclamation-circle"></i> Unable to decrypt card number. 
                        This might be due to missing encryption data or an encryption key mismatch.
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Cardholder Name</span>
                            <span class="detail-value"><?php echo $card['cardholder_name']; ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Expiry Date</span>
                            <span class="detail-value"><?php echo $card['expiry_month']; ?>/<?php echo $card['expiry_year']; ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Card Brand</span>
                            <span class="detail-value"><?php echo $card['card_brand']; ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Added On</span>
                            <span class="detail-value"><?php echo date('M j, Y g:i A', strtotime($card['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Default Card</span>
                        <span class="detail-value"><?php echo $card['is_default'] ? 'Yes' : 'No'; ?></span>
                    </div>
                    
                    <?php if ($canDecrypt): ?>
                    <div class="detail-item">
                        <span class="detail-label">Card Number Hash</span>
                        <span class="detail-value"><?php echo $card['card_number_hash']; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <h3>No Payment Cards Found</h3>
                    <p>No payment cards have been added by users yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="copy-notification" id="copy-notification">
        <i class="fas fa-check-circle"></i> Card number copied to clipboard!
    </div>

    <script>
        // Toggle card number visibility
        document.querySelectorAll('.toggle-btn').forEach(button => {
            button.addEventListener('click', function() {
                const cardId = this.getAttribute('data-card-id');
                const cardNumber = document.getElementById(`card-number-${cardId}`);
                
                if (this.classList.contains('reveal')) {
                    // Reveal card number
                    cardNumber.classList.remove('hidden');
                    this.classList.remove('reveal');
                    this.classList.add('hide');
                    this.innerHTML = '<i class="fas fa-eye-slash"></i> Hide';
                } else {
                    // Hide card number
                    cardNumber.classList.add('hidden');
                    this.classList.remove('hide');
                    this.classList.add('reveal');
                    this.innerHTML = '<i class="fas fa-eye"></i> Show';
                }
            });
        });
        
        // Copy card number to clipboard
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const cardNumber = this.getAttribute('data-card-number');
                if (cardNumber) {
                    navigator.clipboard.writeText(cardNumber.replace(/\s/g, '')).then(() => {
                        const notification = document.getElementById('copy-notification');
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.display = 'none';
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                        alert('Failed to copy card number to clipboard.');
                    });
                }
            });
        });
        
        // Auto-hide card numbers after 30 seconds
        setInterval(() => {
            document.querySelectorAll('.card-number:not(.hidden)').forEach(cardNumber => {
                const cardId = cardNumber.id.replace('card-number-', '');
                const button = document.querySelector(`.toggle-btn[data-card-id="${cardId}"]`);
                
                if (button && button.classList.contains('hide')) {
                    cardNumber.classList.add('hidden');
                    button.classList.remove('hide');
                    button.classList.add('reveal');
                    button.innerHTML = '<i class="fas fa-eye"></i> Show';
                }
            });
        }, 30000);
    </script>
</body>
</html>