<?php
// admin/users.php
require_once 'session.php';

// Handle ban/unban actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ban_user'])) {
        $user_id = $_POST['user_id'];
        $ban_reason = $_POST['ban_reason'] ?? 'Violation of terms of service';
        
        $query = "UPDATE users SET banned = 1, banned_reason = :reason, banned_at = NOW() WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':reason', $ban_reason);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "User has been banned successfully.";
        } else {
            $_SESSION['error'] = "Failed to ban user.";
        }
    }
    
    if (isset($_POST['unban_user'])) {
        $user_id = $_POST['user_id'];
        
        $query = "UPDATE users SET banned = 0, banned_reason = NULL, banned_at = NULL WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "User has been unbanned successfully.";
        } else {
            $_SESSION['error'] = "Failed to unban user.";
        }
    }
    
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $usdt_balance = $_POST['usdt_balance'];
        $usdc_balance = $_POST['usdc_balance'];
        $btc_balance = $_POST['btc_balance'];
        $eth_balance = $_POST['eth_balance'];
        $shib_balance = $_POST['shib_balance'];
        
        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, 
                  usdt_balance = :usdt_balance, usdc_balance = :usdc_balance, btc_balance = :btc_balance, 
                  eth_balance = :eth_balance, shib_balance = :shib_balance, updated_at = NOW() 
                  WHERE id = :user_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':usdt_balance', $usdt_balance);
        $stmt->bindParam(':usdc_balance', $usdc_balance);
        $stmt->bindParam(':btc_balance', $btc_balance);
        $stmt->bindParam(':eth_balance', $eth_balance);
        $stmt->bindParam(':shib_balance', $shib_balance);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "User details updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update user details.";
        }
    }
    
    // Redirect to avoid form resubmission
    header('Location: users');
    exit;
}

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user payment cards
$user_cards = [];
foreach ($users as $user) {
    $query = "SELECT * FROM payment_cards WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $user_cards[$user['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get user transactions
$user_transactions = [];
foreach ($users as $user) {
    $query = "SELECT t.*, c.symbol, c.name 
              FROM transactions t 
              JOIN cryptocurrencies c ON t.crypto_id = c.id 
              WHERE t.user_id = :user_id 
              ORDER BY t.created_at DESC 
              LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    $user_transactions[$user['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Manage Users</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-details {
            background-color: #1e293b;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #334155;
            margin-bottom: 20px;
            position: relative;
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-active {
            background-color: #10b981;
            color: white;
        }

        .status-banned {
            background-color: #ef4444;
            color: white;
        }

        .user-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }

        .btn-ban {
            background-color: #ef4444;
            color: white;
        }

        .btn-unban {
            background-color: #10b981;
            color: white;
        }

        .btn-edit {
            background-color: #3b82f6;
            color: white;
        }

        .btn-cards {
            background-color: #8b5cf6;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: #1e293b;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 20px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(30, 30, 30, 0.8);
            border: 1px solid #333;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 16px;
        }

        .balance-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .balance-item {
            background-color: #1e293b;
            padding: 15px;
            border-radius: 8px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .card-item {
            background-color: #1e293b;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #334155;
        }

        .card-number {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 1px;
            margin: 10px 0;
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
    <!-- Sidebar -->
    <?php require_once "nav.php" ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="header-title">
                <h1>Manage Users</h1>
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

        <!-- Display messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-title">
                <h2>All Users (<?php echo count($users); ?>)</h2>
            </div>

            <?php foreach ($users as $user): ?>
                <div class="user-details">
                    <div class="user-header">
                        <div>
                            <h3><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
                            <p>User ID: #<?php echo $user['id']; ?></p>
                        </div>
                        <div class="user-status <?php echo $user['banned'] ? 'status-banned' : 'status-active'; ?>">
                            <?php echo $user['banned'] ? 'BANNED' : 'ACTIVE'; ?>
                        </div>
                    </div>

                    <div class="balance-grid">
                        <div>
                            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $user['phone'] ? $user['phone'] : 'N/A'; ?></p>
                            <p><strong>Joined:</strong> <?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></p>
                            <?php if ($user['banned'] && $user['banned_at']): ?>
                                <p><strong>Banned Since:</strong> <?php echo date('M j, Y g:i A', strtotime($user['banned_at'])); ?></p>
                                <p><strong>Reason:</strong> <?php echo $user['banned_reason'] ?: 'Not specified'; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <h4>Balances</h4>
                            <p>USDT: <?php echo number_format($user['usdt_balance'], 8); ?></p>
                            <p>USDC: <?php echo number_format($user['usdc_balance'], 8); ?></p>
                            <p>BTC: <?php echo number_format($user['btc_balance'], 8); ?></p>
                            <p>ETH: <?php echo number_format($user['eth_balance'], 8); ?></p>
                            <p>SHIB: <?php echo number_format($user['shib_balance'], 8); ?></p>
                        </div>
                    </div>

                    <h4>Recent Transactions (Last 5):</h4>
                    <?php if (isset($user_transactions[$user['id']]) && count($user_transactions[$user['id']]) > 0): ?>
                        <div style="background: #2a2a2a; padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <?php foreach ($user_transactions[$user['id']] as $transaction): ?>
                                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #333;">
                                    <div>
                                        <strong><?php echo ucfirst($transaction['type']); ?> <?php echo $transaction['symbol']; ?></strong>
                                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                            <?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?>
                                            <?php if ($transaction['card_last_four']): ?>
                                                • Card: **** **** **** <?php echo $transaction['card_last_four']; ?>
                                            <?php endif; ?>
                                        </p>
                                        <span class="status-<?php echo $transaction['status']; ?>" style="padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                                            <?php echo ucfirst($transaction['status']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <?php echo number_format($transaction['crypto_amount'], 8); ?> <?php echo $transaction['symbol']; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No transactions yet.</p>
                    <?php endif; ?>

                    <div class="user-actions">
                        <?php if ($user['banned']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="unban_user" class="btn btn-unban">
                                    <i class="fas fa-user-check"></i> Unban User
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-ban" onclick="openBanModal(<?php echo $user['id']; ?>, '<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>')">
                                <i class="fas fa-user-slash"></i> Ban User
                            </button>
                        <?php endif; ?>

                        <button class="btn btn-edit" onclick="openEditModal(<?php echo $user['id']; ?>, 
                            '<?php echo addslashes($user['first_name']); ?>', 
                            '<?php echo addslashes($user['last_name']); ?>', 
                            '<?php echo addslashes($user['email']); ?>', 
                            '<?php echo addslashes($user['phone']); ?>',
                            '<?php echo $user['usdt_balance']; ?>',
                            '<?php echo $user['usdc_balance']; ?>',
                            '<?php echo $user['btc_balance']; ?>',
                            '<?php echo $user['eth_balance']; ?>',
                            '<?php echo $user['shib_balance']; ?>')">
                            <i class="fas fa-edit"></i> Edit User
                        </button>

                        <button class="btn btn-cards" onclick="openCardsModal(<?php echo $user['id']; ?>, '<?php echo addslashes($user['first_name'] . ' ' . $user['last_name']); ?>')">
                            <i class="fas fa-credit-card"></i> View Cards
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Ban User Modal -->
    <div class="modal" id="ban-modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeBanModal()">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2>Ban User</h2>
            </div>

            <form method="POST" action="users.php">
                <input type="hidden" name="user_id" id="ban-user-id">
                
                <div class="form-group">
                    <label class="form-label">User:</label>
                    <p id="ban-user-name" style="font-weight: bold; margin: 5px 0;"></p>
                </div>

                <div class="form-group">
                    <label for="ban_reason" class="form-label">Ban Reason</label>
                    <textarea id="ban_reason" name="ban_reason" class="form-control" rows="4" placeholder="Enter reason for banning this user..." required></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn" onclick="closeBanModal()" style="background: #6b7280;">Cancel</button>
                    <button type="submit" name="ban_user" class="btn btn-ban">
                        <i class="fas fa-user-slash"></i> Confirm Ban
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal" id="edit-modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2>Edit User</h2>
            </div>

            <form method="POST" action="users.php">
                <input type="hidden" name="user_id" id="edit-user-id">
                <input type="hidden" name="update_user" value="1">

                <div class="form-group">
                    <label for="edit-first-name" class="form-label">First Name</label>
                    <input type="text" id="edit-first-name" name="first_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit-last-name" class="form-label">Last Name</label>
                    <input type="text" id="edit-last-name" name="last_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit-email" class="form-label">Email</label>
                    <input type="email" id="edit-email" name="email" class="form-control" disabled>
                    <small>Email cannot be changed</small>
                </div>

                <div class="form-group">
                    <label for="edit-phone" class="form-label">Phone</label>
                    <input type="text" id="edit-phone" name="phone" class="form-control">
                </div>

                <h3>Balance Management</h3>

                <div class="balance-grid">
                    <div class="form-group">
                        <label for="edit-usdt-balance" class="form-label">USDT Balance</label>
                        <input type="number" id="edit-usdt-balance" name="usdt_balance" step="0.00000001" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-usdc-balance" class="form-label">USDC Balance</label>
                        <input type="number" id="edit-usdc-balance" name="usdc_balance" step="0.00000001" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-btc-balance" class="form-label">BTC Balance</label>
                        <input type="number" id="edit-btc-balance" name="btc_balance" step="0.00000001" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-eth-balance" class="form-label">ETH Balance</label>
                        <input type="number" id="edit-eth-balance" name="eth_balance" step="0.00000001" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-shib-balance" class="form-label">SHIB Balance</label>
                        <input type="number" id="edit-shib-balance" name="shib_balance" step="0.00000001" class="form-control" required>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn" onclick="closeEditModal()" style="background: #6b7280;">Cancel</button>
                    <button type="submit" class="btn btn-edit">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Cards Modal -->
    <div class="modal" id="cards-modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeCardsModal()">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2 id="cards-modal-title">User's Payment Cards</h2>
            </div>

            <div id="cards-container">
                <!-- Cards will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // Ban Modal Functions
        function openBanModal(userId, userName) {
            document.getElementById('ban-user-id').value = userId;
            document.getElementById('ban-user-name').textContent = userName;
            document.getElementById('ban-modal').classList.add('active');
        }

        function closeBanModal() {
            document.getElementById('ban-modal').classList.remove('active');
        }

        // Edit Modal Functions
        function openEditModal(userId, firstName, lastName, email, phone, usdtBalance, usdcBalance, btcBalance, ethBalance, shibBalance) {
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-first-name').value = firstName;
            document.getElementById('edit-last-name').value = lastName;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-usdt-balance').value = usdtBalance;
            document.getElementById('edit-usdc-balance').value = usdcBalance;
            document.getElementById('edit-btc-balance').value = btcBalance;
            document.getElementById('edit-eth-balance').value = ethBalance;
            document.getElementById('edit-shib-balance').value = shibBalance;
            
            document.getElementById('edit-modal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.remove('active');
        }

        // Cards Modal Functions
        function openCardsModal(userId, userName) {
            document.getElementById('cards-modal-title').textContent = userName + "'s Payment Cards";
            
            // Load cards via AJAX
            fetch('get_user_cards.php?user_id=' + userId)
                .then(response => response.json())
                .then(cards => {
                    const container = document.getElementById('cards-container');
                    if (cards.length === 0) {
                        container.innerHTML = '<p>No payment cards found for this user.</p>';
                    } else {
                        container.innerHTML = '<div class="cards-grid"></div>';
                        const grid = container.querySelector('.cards-grid');
                        
                        cards.forEach(card => {
                            const cardElement = document.createElement('div');
                            cardElement.className = 'card-item';
                            cardElement.innerHTML = `
                                <h4>${card.card_brand}</h4>
                                <div class="card-number">${card.full_card_number || '**** **** **** ' + card.card_last_four}</div>
                                <p><strong>Cardholder:</strong> ${card.cardholder_name}</p>
                                <p><strong>Expires:</strong> ${card.expiry_month}/${card.expiry_year}</p>
                                <p><strong>Added:</strong> ${new Date(card.created_at).toLocaleDateString()}</p>
                                <p><strong>Default:</strong> ${card.is_default ? 'Yes' : 'No'}</p>
                            `;
                            grid.appendChild(cardElement);
                        });
                    }
                })
                .catch(error => {
                    document.getElementById('cards-container').innerHTML = '<p>Error loading payment cards.</p>';
                });
            
            document.getElementById('cards-modal').classList.add('active');
        }

        function closeCardsModal() {
            document.getElementById('cards-modal').classList.remove('active');
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        });
    </script>
</body>
</html>