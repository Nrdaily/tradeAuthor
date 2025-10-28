<?php require_once '../app/config/language.php'; ?>
<?php
// transaction_history.php
require_once '../app/config/session.php';
require_once '../app/func/functions.php';

// Get all transactions for the user
$transaction_query = "
    SELECT t.*, c.symbol, c.name
    FROM transactions t
    JOIN cryptocurrencies c ON t.crypto_id = c.id
    WHERE t.user_id = :user_id
    ORDER BY t.created_at DESC
";

$transaction_stmt = $db->prepare($transaction_query);
$transaction_stmt->bindParam(':user_id', $_SESSION['user_id']);
$transaction_stmt->execute();
$transactions = $transaction_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get distinct transaction types and cryptocurrencies for filter
$types = array_unique(array_column($transactions, 'type'));
$cryptos = array_unique(array_column($transactions, 'symbol'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'favicon.php'; ?>
    <title>Trade Author - Transaction History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .transaction-history {
            margin-top: 20px;
        }

        .filters {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-label {
            font-size: 14px;
            color: #94a3b8;
        }

        .filter-select, .filter-input {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #334155;
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transaction-table th, .transaction-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #334155;
        }

        .transaction-table th {
            background-color: #1e293b;
            color: #e2e8f0;
            font-weight: 600;
        }

        .transaction-table tr:hover {
            background-color: #1e293b;
        }

        .transaction-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .type-buy {
            background-color: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .type-sell {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .type-send {
            background-color: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .type-receive {
            background-color: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .transaction-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
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

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <?php include "nav.php"; ?>

    <div class="main-content">
        <div class="container">
            <header>
                <div class="header-title">
                    <h1>Transaction History</h1>
                </div>
            </header>

            <div class="transaction-history">
                <div class="filters">
                    <div class="filter-group">
                        <label class="filter-label">Transaction Type</label>
                        <select class="filter-select" id="filter-type">
                            <option value="">All Types</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Cryptocurrency</label>
                        <select class="filter-select" id="filter-crypto">
                            <option value="">All Cryptocurrencies</option>
                            <?php foreach ($cryptos as $crypto): ?>
                                <option value="<?php echo $crypto; ?>"><?php echo $crypto; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Date From</label>
                        <input type="date" class="filter-input" id="filter-date-from">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Date To</label>
                        <input type="date" class="filter-input" id="filter-date-to">
                    </div>

                    <div class="filter-group" style="align-self: flex-end;">
                        <button class="btn btn-primary" id="apply-filters">Apply Filters</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-exchange-alt"></i>
                        <span>All Transactions</span>
                    </div>

                    <?php if (count($transactions) > 0): ?>
                        <table class="transaction-table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Cryptocurrency</th>
                                    <th>Amount</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?></td>
                                        <td>
                                            <span class="transaction-type type-<?php echo $transaction['type']; ?>">
                                                <?php echo ucfirst($transaction['type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <img src="../assets/icons/<?php echo strtolower($transaction['symbol']); ?>.png" width="24" height="24" onerror="this.style.display='none'">
                                                <?php echo $transaction['symbol']; ?>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($transaction['crypto_amount'], 8); ?></td>
                                        <td>$<?php echo number_format($transaction['price'], 2); ?></td>
                                        <td>$<?php echo number_format($transaction['total'], 2); ?></td>
                                        <td>
                                            <span class="transaction-status status-<?php echo $transaction['status']; ?>">
                                                <?php echo ucfirst($transaction['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($transaction['type'] === 'send' || $transaction['type'] === 'receive'): ?>
                                                <button class="btn btn-outline btn-small" onclick="viewTransactionDetails(<?php echo $transaction['id']; ?>)">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-exchange-alt"></i>
                            <h3>No transactions yet</h3>
                            <p>Your transaction history will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter transactions
        document.getElementById('apply-filters').addEventListener('click', function() {
            const type = document.getElementById('filter-type').value;
            const crypto = document.getElementById('filter-crypto').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            // Simple client-side filtering (for small datasets)
            // For large datasets, we would need server-side filtering
            const rows = document.querySelectorAll('.transaction-table tbody tr');
            rows.forEach(row => {
                let show = true;
                const rowType = row.cells[1].textContent.trim().toLowerCase();
                const rowCrypto = row.cells[2].textContent.trim().toLowerCase();
                const rowDate = new Date(row.cells[0].textContent);

                if (type && rowType !== type.toLowerCase()) {
                    show = false;
                }

                if (crypto && rowCrypto !== crypto.toLowerCase()) {
                    show = false;
                }

                if (dateFrom) {
                    const fromDate = new Date(dateFrom);
                    if (rowDate < fromDate) {
                        show = false;
                    }
                }

                if (dateTo) {
                    const toDate = new Date(dateTo);
                    toDate.setDate(toDate.getDate() + 1); // Include the entire day
                    if (rowDate >= toDate) {
                        show = false;
                    }
                }

                row.style.display = show ? '' : 'none';
            });
        });

        function viewTransactionDetails(transactionId) {
            // Here we can show a modal with transaction details
            alert('Transaction details for ID: ' + transactionId);
            // In a real application, we would fetch the details via AJAX and display in a modal
        }
    </script>
</body>

</html>