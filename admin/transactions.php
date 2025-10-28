<?php
// admin/transactions.php (enhanced version)
require_once 'session.php';

// Get all transactions with user information
$query = "
    SELECT t.*, u.first_name, u.last_name, u.email, c.symbol, c.name as crypto_name
    FROM transactions t 
    JOIN users u ON t.user_id = u.id 
    JOIN cryptocurrencies c ON t.crypto_id = c.id 
    ORDER BY t.created_at DESC
";
$stmt = $db->prepare($query);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add this to the existing POST handling in admin/transactions.php
if (isset($_POST['approve_transaction'])) {
    $transaction_id = $_POST['transaction_id'];
    
    try {
        $db->beginTransaction();
        
        // Get transaction details
        $stmt = $db->prepare("SELECT t.*, c.symbol FROM transactions t JOIN cryptocurrencies c ON t.crypto_id = c.id WHERE t.id = :id");
        $stmt->bindParam(':id', $transaction_id);
        $stmt->execute();
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaction) {
            $symbol = strtolower($transaction['symbol']);
            $reserved_column = $symbol . '_reserved';
            
            // Remove from reserved (funds were already deducted from main balance)
            $update_user = $db->prepare("UPDATE users SET $reserved_column = $reserved_column - :amount WHERE id = :user_id");
            $update_user->bindValue(':amount', $transaction['crypto_amount']);
            $update_user->bindValue(':user_id', $transaction['user_id']);
            $update_user->execute();
            
            // Update transaction status
            $update_tx = $db->prepare("UPDATE transactions SET status = 'completed' WHERE id = :id");
            $update_tx->bindParam(':id', $transaction_id);
            $update_tx->execute();
            
            $db->commit();
            $success = "Transaction approved successfully!";
        }
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error approving transaction: " . $e->getMessage();
    }
}

if (isset($_POST['reject_transaction'])) {
    $transaction_id = $_POST['transaction_id'];
    
    try {
        $db->beginTransaction();
        
        // Get transaction details
        $stmt = $db->prepare("SELECT t.*, c.symbol FROM transactions t JOIN cryptocurrencies c ON t.crypto_id = c.id WHERE t.id = :id");
        $stmt->bindParam(':id', $transaction_id);
        $stmt->execute();
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaction) {
            $symbol = strtolower($transaction['symbol']);
            $reserved_column = $symbol . '_reserved';
            $balance_column = $symbol . '_balance';
            
            // Return funds to main balance
            $update_user = $db->prepare("UPDATE users SET $balance_column = $balance_column + :amount, $reserved_column = $reserved_column - :amount WHERE id = :user_id");
            $update_user->bindValue(':amount', $transaction['crypto_amount']);
            $update_user->bindValue(':user_id', $transaction['user_id']);
            $update_user->execute();
            
            // Update transaction status
            $update_tx = $db->prepare("UPDATE transactions SET status = 'failed' WHERE id = :id");
            $update_tx->bindParam(':id', $transaction_id);
            $update_tx->execute();
            
            $db->commit();
            $success = "Transaction rejected successfully!";
        }
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error rejecting transaction: " . $e->getMessage();
    }
}

    if (isset($_POST['update_status'])) {
        $transaction_id = $_POST['transaction_id'];
        $new_status = $_POST['status'];

        // Get transaction details
        $stmt = $db->prepare("SELECT * FROM transactions WHERE id = :id");
        $stmt->bindParam(':id', $transaction_id);
        $stmt->execute();
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($transaction) {
            try {
                $db->beginTransaction();

                // Update transaction status
                $update_stmt = $db->prepare("UPDATE transactions SET status = :status WHERE id = :id");
                $update_stmt->bindParam(':status', $new_status);
                $update_stmt->bindParam(':id', $transaction_id);
                $update_stmt->execute();

                // If status is changed to completed for a withdrawal, finalize the balance update
                if ($transaction['type'] === 'withdraw' && $new_status === 'completed' && $transaction['status'] === 'pending') {
                    $symbol = strtolower($transaction['symbol']);
                    $reserve_column = $symbol . '_reserved';
                    $balance_column = $symbol . '_balance';

                    // Remove from reserved and don't add back to balance (since it was already deducted)
                    $user_update = $db->prepare("
                        UPDATE users 
                        SET $reserve_column = $reserve_column - :amount 
                        WHERE id = :user_id
                    ");
                    $user_update->bindValue(':amount', $transaction['crypto_amount']);
                    $user_update->bindValue(':user_id', $transaction['user_id']);
                    $user_update->execute();
                }

                // If status is changed to failed for a withdrawal, return the funds
                if ($transaction['type'] === 'withdraw' && $new_status === 'failed' && $transaction['status'] === 'pending') {
                    $symbol = strtolower($transaction['symbol']);
                    $reserve_column = $symbol . '_reserved';
                    $balance_column = $symbol . '_balance';

                    // Return funds to balance and remove from reserved
                    $user_update = $db->prepare("
                        UPDATE users 
                        SET $balance_column = $balance_column + :amount,
                            $reserve_column = $reserve_column - :amount 
                        WHERE id = :user_id
                    ");
                    $user_update->bindValue(':amount', $transaction['crypto_amount']);
                    $user_update->bindValue(':user_id', $transaction['user_id']);
                    $user_update->execute();
                }

                $db->commit();
                $success = "Transaction status updated successfully!";
            } catch (Exception $e) {
                $db->rollBack();
                $error = "Error updating transaction: " . $e->getMessage();
            }
        }
    }

    // Speed up transaction
    if (isset($_POST['speed_up'])) {
        $transaction_id = $_POST['transaction_id'];

        // In a real application, this would interact with the blockchain
        // For this demo, we'll just update a priority field
        $update_stmt = $db->prepare("UPDATE transactions SET priority = 'high' WHERE id = :id");
        $update_stmt->bindParam(':id', $transaction_id);

        if ($update_stmt->execute()) {
            $success = "Transaction prioritized for faster processing!";
        } else {
            $error = "Error speeding up transaction";
        }
    }
}

$pending_transactions = $db->query("SELECT t.*, u.email, c.symbol FROM transactions t JOIN users u ON t.user_id = u.id JOIN cryptocurrencies c ON t.crypto_id = c.id WHERE t.status = 'pending' AND t.type = 'send'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Transaction Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending {
            background-color: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .status-completed {
            background-color: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .status-failed {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <?php require_once "nav.php" ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="header-title">
                <h1>Transaction Management</h1>
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

        <div class="card">
            <div class="card-title">
                <h2>All Transactions</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Cryptocurrency</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td>#<?php echo $transaction['id']; ?></td>
                            <td><?php echo $transaction['first_name'] . ' ' . $transaction['last_name']; ?></td>
                            <td><?php echo ucfirst($transaction['type']); ?></td>
                            <td><?php echo $transaction['symbol']; ?></td>
                            <td><?php echo number_format($transaction['crypto_amount'], 8); ?> <?php echo $transaction['symbol']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if ($transaction['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                        <button type="submit" name="speed_up" class="btn btn-primary btn-sm">
                                            <i class="fas fa-bolt"></i> Speed Up
                                        </button>
                                    </form>

                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="form-control">
                                            <option value="pending" <?php echo $transaction['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="completed" <?php echo $transaction['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="failed" <?php echo $transaction['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>