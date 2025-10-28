<?php
// admin/index.php
require_once 'session.php';

// Get stats for dashboard
$user_count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$transaction_count = $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$pending_transactions = $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'pending'")->fetchColumn();
$total_volume = $db->query("SELECT SUM(amount * price) FROM transactions WHERE status = 'completed'")->fetchColumn();

// Get recent transactions
$recent_transactions = $db->query("
    SELECT t.*, u.email, c.symbol 
    FROM transactions t 
    JOIN users u ON t.user_id = u.id 
    JOIN cryptocurrencies c ON t.crypto_id = c.id 
    ORDER BY t.created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get recent users
$recent_users = $db->query("
    SELECT id, first_name, last_name, email, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Admin Dashboard</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php require_once "nav.php" ?>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="header-title">
                <h1>Admin Dashboard</h1>
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

        <div style="margin: 10px 0; display:flex; gap:10px; align-items:center;">
            <button id="ajax-snapshot-btn" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Run Snapshots</button>
            <span id="ajax-snapshot-status" style="display:none;">Running...</span>
        </div>
        <div style="margin-bottom: 20px; color: #94a3b8;">
            <strong>Snapshots</strong> — Daily snapshots capture each user's total portfolio USD value and are stored in the <code>portfolio_snapshots</code> table. Use snapshots for accurate historical charts and faster dashboard rendering. You can also schedule the CLI script <code>scripts/daily_portfolio_snapshot.php</code> to run automatically (cron or Task Scheduler).
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $user_count; ?></div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-value"><?php echo $transaction_count; ?></div>
                <div class="stat-label">Total Transactions</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $pending_transactions; ?></div>
                <div class="stat-label">Pending Transactions</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">$<?php echo number_format($total_volume, 2); ?></div>
                <div class="stat-label">Total Volume</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-title">
                    <h2>Recent Transactions</h2>
                    <a href="transactions" class="view-all">View All</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_transactions as $transaction): ?>
                            <tr>
                                <td><?php echo $transaction['email']; ?></td>
                                <td><?php echo $transaction['type']; ?></td>
                                <td><?php echo number_format($transaction['amount'], 8); ?> <?php echo $transaction['symbol']; ?></td>
                                <td><span class="status status-<?php echo $transaction['status']; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                                <td><?php echo date('M j, Y', strtotime($transaction['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Users -->
            <div class="card">
                <div class="card-title">
                    <h2>Recent Users</h2>
                    <a href="users" class="view-all">View All</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_users as $user): ?>
                            <tr>
                                <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td><button class="btn btn-primary btn-sm">View</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Simple chart for dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('statsChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Transactions',
                            data: [12, 19, 3, 5, 2, 3],
                            borderColor: '#0ea5e9',
                            tension: 0.1,
                            fill: true,
                            backgroundColor: 'rgba(14, 165, 233, 0.1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('ajax-snapshot-btn');
            const status = document.getElementById('ajax-snapshot-status');

            if (btn) {
                btn.addEventListener('click', async function() {
                    btn.disabled = true;
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running...';
                    status.style.display = 'inline-block';

                    try {
                        const resp = await fetch('ajax_run_snapshot.php', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        const data = await resp.json();
                        if (data.success) {
                            alert(data.message);
                        } else {
                            alert('Snapshot failed: ' + (data.message || 'Unknown error'));
                        }
                    } catch (err) {
                        alert('Request failed: ' + err.message);
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                        status.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>

</html>