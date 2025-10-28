<?php
// admin/cryptocurrencies.php
require_once 'session.php';

// Get all cryptocurrencies
$query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE";
$stmt = $db->prepare($query);
$stmt->execute();
$cryptocurrencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_crypto'])) {
        $crypto_id = $_POST['crypto_id'];
        $receiving_address = $_POST['receiving_address'];

        // Handle QR code upload
        $qr_code = $cryptocurrencies[$crypto_id]['qr_code'] ?? ''; // Keep existing if no new upload

        if (isset($_FILES['qr_code_file']) && $_FILES['qr_code_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/qr_codes/';
            $web_path = '/assets/qr_codes/';

            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Generate unique filename
            $file_extension = pathinfo($_FILES['qr_code_file']['name'], PATHINFO_EXTENSION);
            $filename = 'qr_' . $crypto_id . '_' . time() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;

            // Check if file is an image
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['qr_code_file']['type'];

            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['qr_code_file']['tmp_name'], $target_path)) {
                    $qr_code = $web_path . $filename;

                    // Delete old QR code if it exists
                    if (!empty($cryptocurrencies[$crypto_id]['qr_code']) && $cryptocurrencies[$crypto_id]['qr_code'] != $qr_code) {
                        $old_file = $_SERVER['DOCUMENT_ROOT'] . $cryptocurrencies[$crypto_id]['qr_code'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                } else {
                    $error = "Failed to upload QR code image.";
                }
            } else {
                $error = "Only JPG, PNG, and GIF files are allowed.";
            }
        }

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
    } elseif (isset($_POST['update_price'])) {
        $crypto_id = $_POST['crypto_id'];
        $current_price = $_POST['current_price'];

        $query = "UPDATE cryptocurrencies SET current_price = :current_price WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':current_price', $current_price);
        $stmt->bindParam(':id', $crypto_id);

        if ($stmt->execute()) {
            $success = "Cryptocurrency price updated successfully!";
            // Refresh the data
            $stmt = $db->prepare("SELECT * FROM cryptocurrencies WHERE is_active = TRUE");
            $stmt->execute();
            $cryptocurrencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "Failed to update cryptocurrency price.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Cryptocurrency Management</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .crypto-card {
            background-color: #1b1b1cff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #553d33ff;
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
            background-color: #e9860eff;
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
            background: rgba(6, 6, 6, 0.5);
            border: 1px solid #553f33ff;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 16px;
        }

        .form-input:focus {
            outline: none;
            border-color: #e96d0eff;
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

        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #553e33ff;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }

        .tab.active {
            border-bottom: 2px solid #e9710eff;
            color: #e96d0eff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .qr-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #554233ff;
        }

        .file-input-container {
            position: relative;
            margin-bottom: 15px;
        }

        .file-input-label {
            display: inline-block;
            padding: 10px 15px;
            background-color: #e9740eff;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .file-input-label:hover {
            background-color: #a14d03ff;
        }

        .file-input {
            position: absolute;
            left: -9999px;
        }

        .file-name {
            margin-left: 10px;
            font-size: 14px;
            color: #94a3b8;
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
                <h1>Cryptocurrency Management</h1>
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
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="tabs">
                <div class="tab active" data-tab="addresses">Addresses & QR Codes</div>
                <div class="tab" data-tab="prices">Price Management</div>
            </div>

            <!-- Addresses & QR Codes Tab -->
            <div class="tab-content active" id="addresses-tab">
                <?php foreach ($cryptocurrencies as $crypto): ?>
                    <div class="crypto-card">
                        <div class="crypto-header">
                            <div class="crypto-icon"><?php echo $crypto['symbol'][0]; ?></div>
                            <div>
                                <h3><?php echo $crypto['name']; ?> (<?php echo $crypto['symbol']; ?>)</h3>
                                <p>Current Price: $<?php echo number_format($crypto['current_price'], 8); ?></p>
                            </div>
                        </div>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="crypto_id" value="<?php echo $crypto['id']; ?>">

                            <div class="form-group">
                                <label for="receiving_address_<?php echo $crypto['id']; ?>" class="form-label">Receiving Address</label>
                                <input type="text" id="receiving_address_<?php echo $crypto['id']; ?>" name="receiving_address" class="form-input" value="<?php echo htmlspecialchars($crypto['receiving_address'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">QR Code</label>
                                <div class="file-input-container">
                                    <label for="qr_code_file_<?php echo $crypto['id']; ?>" class="file-input-label">
                                        <i class="fas fa-upload"></i> Upload QR Code
                                    </label>
                                    <input type="file" id="qr_code_file_<?php echo $crypto['id']; ?>" name="qr_code_file" class="file-input" accept="image/jpeg,image/png,image/gif">
                                    <span id="file-name_<?php echo $crypto['id']; ?>" class="file-name">No file selected</span>
                                </div>

                                <?php if (!empty($crypto['qr_code'])): ?>
                                    <div>
                                        <img src="<?php echo $crypto['qr_code']; ?>?t=<?php echo time(); ?>" alt="QR Code" class="qr-preview">
                                        <div class="text-muted" style="margin-top: 5px; font-size: 12px;">
                                            Current QR Code
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" name="update_crypto" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Details
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Price Management Tab -->
            <div class="tab-content" id="prices-tab">
                <?php foreach ($cryptocurrencies as $crypto): ?>
                    <div class="crypto-card">
                        <div class="crypto-header">
                            <div class="crypto-icon"><?php echo $crypto['symbol'][0]; ?></div>
                            <div>
                                <h3><?php echo $crypto['name']; ?> (<?php echo $crypto['symbol']; ?>)</h3>
                                <p>Current Price: $<?php echo number_format($crypto['current_price'], 8); ?></p>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <input type="hidden" name="crypto_id" value="<?php echo $crypto['id']; ?>">

                            <div class="form-group">
                                <label for="current_price_<?php echo $crypto['id']; ?>" class="form-label">Current Price (USD)</label>
                                <input type="number" id="current_price_<?php echo $crypto['id']; ?>" name="current_price" class="form-input" step="0.00000001" value="<?php echo $crypto['current_price']; ?>" required>
                            </div>

                            <button type="submit" name="update_price" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Update Price
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));

                // Add active class to clicked tab
                tab.classList.add('active');

                // Show corresponding content
                const tabName = tab.getAttribute('data-tab');
                document.getElementById(`${tabName}-tab`).classList.add('active');
            });
        });

        // File input functionality
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files[0] ? this.files[0].name : 'No file selected';
                const id = this.id.replace('qr_code_file_', '');
                document.getElementById(`file-name_${id}`).textContent = fileName;
            });
        });
    </script>
</body>

</html>