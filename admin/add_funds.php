<?php
// admin/add_funds.php
require_once 'session.php';

// Get all users for the dropdown
$users_query = "SELECT id, first_name, last_name, email FROM users ORDER BY first_name, last_name";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all cryptocurrencies
$crypto_query = "SELECT * FROM cryptocurrencies WHERE is_active = TRUE";
$crypto_stmt = $db->prepare($crypto_query);
$crypto_stmt->execute();
$cryptocurrencies = $crypto_stmt->fetchAll(PDO::FETCH_ASSOC);

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_funds'])) {
    $user_id = $_POST['user_id'];
    $crypto_id = $_POST['crypto_id'];
    $amount = floatval($_POST['amount']);

    // Validate inputs
    if (empty($user_id) || empty($crypto_id) || empty($amount) || $amount <= 0) {
        $error = "Please fill in all fields with valid values.";
    } else {
        try {
            // Start transaction
            $db->beginTransaction();

            // Get cryptocurrency symbol
            $crypto_stmt = $db->prepare("SELECT symbol FROM cryptocurrencies WHERE id = :id");
            $crypto_stmt->bindParam(':id', $crypto_id);
            $crypto_stmt->execute();
            $crypto = $crypto_stmt->fetch(PDO::FETCH_ASSOC);
            $symbol = strtolower($crypto['symbol']);

            // Update user balance
            $balance_column = $symbol . '_balance';
            $query = "UPDATE users SET $balance_column = $balance_column + :amount WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            // Get current price for the transaction record
            $price_stmt = $db->prepare("SELECT current_price FROM cryptocurrencies WHERE id = :id");
            $price_stmt->bindParam(':id', $crypto_id);
            $price_stmt->execute();
            $price_data = $price_stmt->fetch(PDO::FETCH_ASSOC);
            $current_price = $price_data['current_price'];

            // Add transaction record
            $transaction_query = "INSERT INTO transactions (user_id, crypto_id, type, amount, price, total, crypto_amount, status) 
                                  VALUES (:user_id, :crypto_id, 'admin_deposit', :amount, :price, :total, :crypto_amount, 'completed')";
            $transaction_stmt = $db->prepare($transaction_query);
            $transaction_stmt->bindValue(':user_id', $user_id);
            $transaction_stmt->bindValue(':crypto_id', $crypto_id);
            $transaction_stmt->bindValue(':amount', $amount);
            $transaction_stmt->bindValue(':price', $current_price);
            $transaction_stmt->bindValue(':total', $amount);
            $transaction_stmt->bindValue(':crypto_amount', $amount / $current_price);
            $transaction_stmt->execute();

            // Commit transaction
            $db->commit();

            $success = "Successfully added $" . number_format($amount, 2) . " worth of " . $crypto['symbol'] . " to user's account.";
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            $error = "Error adding funds: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Add Funds</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid #553e33ff;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 16px;
        }

        .form-input:focus,
        .form-select:focus {
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

        .user-balance {
            margin-top: 10px;
            padding: 10px;
            background-color: rgba(14, 165, 233, 0.1);
            border-radius: 8px;
            font-size: 14px;
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
                <h1>Add Funds to User Account</h1>
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
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="user_id" class="form-label">Select User</label>
                        <select id="user_id" name="user_id" class="form-select" required>
                            <option value="">Select a user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="crypto_id" class="form-label">Select Cryptocurrency</label>
                        <select id="crypto_id" name="crypto_id" class="form-select" required>
                            <option value="">Select a cryptocurrency</option>
                            <?php foreach ($cryptocurrencies as $crypto): ?>
                                <option value="<?php echo $crypto['id']; ?>" data-symbol="<?php echo strtolower($crypto['symbol']); ?>">
                                    <?php echo $crypto['name'] . ' (' . $crypto['symbol'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="form-label">Amount (USD)</label>
                        <input type="number" id="amount" name="amount" class="form-input" step="0.01" min="0.01" required>
                        <small>Enter the amount in USD to add to the user's account</small>
                    </div>

                    <div id="user-balance-info" class="user-balance" style="display: none;">
                        <i class="fas fa-wallet"></i> Current balance: <span id="balance-amount">0</span> <span id="balance-symbol"></span>
                    </div>

                    <button type="submit" name="add_funds" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Funds
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fetch user balance when user and cryptocurrency are selected
        document.getElementById('user_id').addEventListener('change', fetchUserBalance);
        document.getElementById('crypto_id').addEventListener('change', fetchUserBalance);

        function fetchUserBalance() {
            const userId = document.getElementById('user_id').value;
            const cryptoId = document.getElementById('crypto_id').value;
            const cryptoSelect = document.getElementById('crypto_id');
            const selectedOption = cryptoSelect.options[cryptoSelect.selectedIndex];
            const symbol = selectedOption ? selectedOption.getAttribute('data-symbol') : '';

            if (userId && cryptoId && symbol) {
                // AJAX request to get user balance
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'get_user_balance.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            document.getElementById('balance-amount').textContent = response.balance;
                            document.getElementById('balance-symbol').textContent = symbol.toUpperCase();
                            document.getElementById('user-balance-info').style.display = 'block';
                        } else {
                            document.getElementById('user-balance-info').style.display = 'none';
                        }
                    }
                };
                xhr.send('user_id=' + userId + '&symbol=' + symbol);
            } else {
                document.getElementById('user-balance-info').style.display = 'none';
            }
        }
    </script>
</body>

</html>