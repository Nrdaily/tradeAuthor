<?php
// admin_crypto.php
require_once 'session.php';

// Get all cryptocurrencies
$query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE";
$stmt = $db->prepare($query);
$stmt->execute();
$cryptocurrencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_crypto'])) {
    $crypto_id = $_POST['crypto_id'];
    $receiving_address = $_POST['receiving_address'];
    $qr_code = $_POST['qr_code'];
    
    $query = "UPDATE cryptocurrencies SET receiving_address = :receiving_address, qr_code = :qr_code WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':receiving_address', $receiving_address);
    $stmt->bindParam(':qr_code', $qr_code);
    $stmt->bindParam(':id', $crypto_id);
    
    if ($stmt->execute()) {
        $success = "Cryptocurrency details updated successfully!";
        // Refresh the data
        $stmt = $db->prepare("SELECT * FROM cryptocurrencies WHERE is_active = TRUE");
        $stmt->execute();
        $cryptocurrencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = "Failed to update cryptocurrency details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Admin Panel</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .crypto-card {
            background-color: #1e293b;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #334155;
            margin-bottom: 20px;
        }
        
        .crypto-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .crypto-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #0ea5e9;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid #334155;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 16px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #0ea5e9;
        }
        
        .success-message {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #10b981;
        }
        
        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #ef4444;
        }
    </style>
</head>
<body>
    <?php require_once "nav.php" ?>



<div class="main-content">
    <div class="container">
        <header>
            <div class="header-title">
                <h1>Admin Panel - Cryptocurrency Management</h1>
            </div>
        </header>
        
        <div class="admin-container">
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php foreach ($cryptocurrencies as $crypto): ?>
            <div class="crypto-card">
                <div class="crypto-header">
                    <div class="crypto-icon"><?php echo $crypto['symbol'][0]; ?></div>
                    <div>
                        <h3><?php echo $crypto['name']; ?> (<?php echo $crypto['symbol']; ?>)</h3>
                        <p>Current Price: $<?php echo number_format($crypto['current_price'], 8); ?></p>
                    </div>
                </div>
                
                <form method="POST" action="admin_crypto.php">
                    <input type="hidden" name="crypto_id" value="<?php echo $crypto['id']; ?>">
                    
                    <div class="form-group">
                        <label for="receiving_address_<?php echo $crypto['id']; ?>" class="form-label">Receiving Address</label>
                        <input type="text" id="receiving_address_<?php echo $crypto['id']; ?>" name="receiving_address" class="form-input" value="<?php echo htmlspecialchars($crypto['receiving_address'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="qr_code_<?php echo $crypto['id']; ?>" class="form-label">QR Code URL</label>
                        <input type="url" id="qr_code_<?php echo $crypto['id']; ?>" name="qr_code" class="form-input" value="<?php echo htmlspecialchars($crypto['qr_code'] ?? ''); ?>">
                        <small>Leave empty to generate from address</small>
                    </div>
                    
                    <button type="submit" name="update_crypto" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Details
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- <script src="../assets/js/app.js"></script> -->
</body>
</html>