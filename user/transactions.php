<?php require_once '../app/config/language.php'; ?>
<?php
// transactions.php - Enhanced with advanced filtering and trading features
require_once '../app/config/session.php';
require_once '../app/func/functions.php';

// Get filter parameters
$filter_type = $_GET['type'] ?? 'all';
$filter_crypto = $_GET['crypto'] ?? 'all';
$filter_status = $_GET['status'] ?? 'all';
$filter_date = $_GET['date'] ?? 'all';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build the base query
$query = "
    SELECT t.*, c.symbol, c.name
    FROM transactions t 
    JOIN cryptocurrencies c ON t.crypto_id = c.id 
    WHERE t.user_id = :user_id
";

$count_query = "
    SELECT COUNT(*) as total
    FROM transactions t 
    JOIN cryptocurrencies c ON t.crypto_id = c.id 
    WHERE t.user_id = :user_id
";

// Add filters
$params = [':user_id' => $_SESSION['user_id']];
$count_params = [':user_id' => $_SESSION['user_id']];

if ($filter_type !== 'all') {
    $query .= " AND t.type = :type";
    $count_query .= " AND t.type = :type";
    $params[':type'] = $filter_type;
    $count_params[':type'] = $filter_type;
}

if ($filter_crypto !== 'all') {
    $query .= " AND c.symbol = :crypto";
    $count_query .= " AND c.symbol = :crypto";
    $params[':crypto'] = $filter_crypto;
    $count_params[':crypto'] = $filter_crypto;
}

if ($filter_status !== 'all') {
    $query .= " AND t.status = :status";
    $count_query .= " AND t.status = :status";
    $params[':status'] = $filter_status;
    $count_params[':status'] = $filter_status;
}

if ($filter_date !== 'all') {
    $date_condition = "";
    switch ($filter_date) {
        case 'today':
            $date_condition = "DATE(t.created_at) = CURDATE()";
            break;
        case 'week':
            $date_condition = "t.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $date_condition = "t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $date_condition = "t.created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
            break;
    }
    if ($date_condition) {
        $query .= " AND $date_condition";
        $count_query .= " AND $date_condition";
    }
}

$query .= " ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset";

// Get transactions
$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    if ($key === ':limit') {
        $stmt->bindValue($key, $limit, PDO::PARAM_INT);
    } elseif ($key === ':offset') {
        $stmt->bindValue($key, $offset, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $value);
    }
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$count_stmt = $db->prepare($count_query);
foreach ($count_params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_count / $limit);

// Get available cryptocurrencies for filter
$crypto_query = "SELECT DISTINCT symbol, name FROM cryptocurrencies WHERE is_active = TRUE";
$crypto_stmt = $db->prepare($crypto_query);
$crypto_stmt->execute();
$cryptocurrencies = $crypto_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate transaction statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful_trades,
        SUM(CASE WHEN type = 'buy' THEN total ELSE 0 END) as total_buy_volume,
        SUM(CASE WHEN type = 'sell' THEN total ELSE 0 END) as total_sell_volume,
        SUM(total) as total_volume
    FROM transactions 
    WHERE user_id = :user_id
";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->bindParam(':user_id', $_SESSION['user_id']);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$success_rate = $stats['total_transactions'] > 0 ? ($stats['successful_trades'] / $stats['total_transactions']) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Transaction History</title>
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Modal styles */
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
            /* display: none; */
        }

        @keyframes modals {
            from {
                /* transform: scale(0); */
                opacity: 0;
            }

            to {
                /* transform: scale(1); */
                opacity: 1;
            }
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 100vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #b8ab94ff;
            font-size: 20px;
            cursor: pointer;
        }


        .transaction-details-modal.active {
            display: flex;
        }

        .transaction-details-content {
            background-color: #1a1a1a;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .transaction-details-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #b8b194ff;
            font-size: 20px;
            cursor: pointer;
        }

        .details-grid {
            display: grid;
            gap: 15px;
            margin-top: 20px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #2a2a2a;
        }

        .detail-label {
            color: #b8b194ff;
            font-weight: 500;
        }

        .detail-value {
            text-align: right;
        }

        .transactions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .transaction-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
        }

        .stat-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .filters-container {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .filter-select {
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-select:focus {
            border-color: var(--primary);
            outline: none;
        }

        .transactions-table {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr 1fr;
            gap: 15px;
            padding: 20px 24px;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--text-secondary);
        }

        .transaction-row {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr 1fr;
            gap: 15px;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            transition: all 0.3s;
            align-items: center;
        }

        .transaction-row:hover {
            background: rgba(255, 107, 53, 0.05);
        }

        .transaction-row:last-child {
            border-bottom: none;
        }

        .transaction-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .transaction-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .transaction-info h4 {
            margin-bottom: 4px;
            font-weight: 600;
        }

        .transaction-info p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .transaction-amount {
            font-weight: 600;
            font-size: 1rem;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            text-transform: capitalize;
        }

        .status-completed {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .status-pending {
            background: rgba(255, 171, 0, 0.1);
            color: var(--warning);
        }

        .status-failed {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        .status-cancelled {
            background: rgba(107, 114, 128, 0.1);
            color: var(--text-secondary);
        }

        .type-buy {
            background: var(--success);
        }

        .type-sell {
            background: var(--danger);
        }

        .type-send {
            background: var(--primary);
        }

        .type-receive {
            background: var(--info);
        }

        .export-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .page-btn {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: transparent;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s;
        }

        .page-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .transaction-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.8rem;
        }

        .action-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .mobile-transaction-card {
            display: none;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 15px;
        }

        .mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .mobile-card-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .table-header {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .transaction-row {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .mobile-hidden {
                display: none;
            }
        }

        @media (max-width: 768px) {

            .table-header,
            .transaction-row {
                display: none;
            }

            .mobile-transaction-card {
                display: block;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .transaction-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .transaction-stats {
                grid-template-columns: 1fr;
            }

            .export-actions {
                flex-direction: column;
            }
        }

        .view-details-btn {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary);
            border: 1px solid rgba(255, 107, 53, 0.3);
        }

        .view-details-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Enhanced Modal Styles */
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
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(20px);
            animation: modalFadeIn 0.3s ease;
            padding: 20px;
            box-sizing: border-box;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(1.05);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 0;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: modalContentSlideIn 0.3s ease;
        }

        @keyframes modalContentSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 50%;
            color: var(--text-secondary);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .modal-close:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: rotate(90deg);
        }

        .modal-header {
            padding: 30px 30px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, transparent 100%);
            position: relative;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient);
            border-radius: 20px 20px 0 0;
        }

        .modal-header h2 {
            margin: 0 0 8px 0;
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-header p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Transaction Summary Styles */
        .transaction-summary {
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item span:first-child {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .summary-item span:last-child {
            font-weight: 600;
            text-align: right;
            color: var(--text-primary);
        }

        /* Status Badge in Modal */
        .summary-item .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        /* Speed Up Section */
        .speed-up-section {
            padding: 20px 30px;
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .speed-up-section h3 {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        /* Processing Spinner */
        .processing-spinner {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .processing-spinner .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Action Buttons in Modal */
        .modal-actions {
            padding: 20px 30px;
            display: flex;
            gap: 12px;
            flex-direction: column;
        }

        .modal-actions .btn {
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .modal-actions .btn-outline {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--text-primary);
        }

        .modal-actions .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .modal-actions .btn-primary {
            background: var(--primary);
            border: 2px solid var(--primary);
            color: white;
        }

        .modal-actions .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
        }

        /* Transaction Type Icons in Modal */
        .transaction-type-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .type-icon-large {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .type-icon-large.type-buy {
            background: linear-gradient(135deg, var(--success), #00c853);
        }

        .type-icon-large.type-sell {
            background: linear-gradient(135deg, var(--danger), #ff3d00);
        }

        .type-icon-large.type-send {
            background: linear-gradient(135deg, var(--primary), #ff6b35);
        }

        .type-icon-large.type-receive {
            background: linear-gradient(135deg, var(--info), #2196f3);
        }

        .type-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .type-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Progress Bar for Pending Transactions */
        .progress-section {
            padding: 20px 30px;
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid var(--border);
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .progress-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .progress-value {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        /* Network Information */
        .network-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .network-item {
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .network-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .network-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Responsive Design for Modal */
        @media (max-width: 768px) {
            .modal {
                padding: 10px;
            }

            .modal-content {
                max-width: 100%;
                border-radius: 16px;
            }

            .modal-header {
                padding: 25px 20px 15px;
            }

            .transaction-summary,
            .speed-up-section,
            .modal-actions {
                padding: 20px;
            }

            .summary-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .summary-item span:last-child {
                text-align: left;
            }

            .network-info {
                grid-template-columns: 1fr;
            }

            .transaction-type-header {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .modal-header h2 {
                font-size: 1.3rem;
            }

            .type-icon-large {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }

            .modal-actions .btn {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }

        /* Scrollbar Styling for Modal */
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>

<body class="theme-transition">
    <?php include "nav.php"; ?>

    <div class="main-content">
        <div class="container">
            <div class="transactions-header">
                <div>
                    <h1>Transaction History</h1>
                    <p>View and manage all your trading activities</p>
                </div>
                <div class="export-actions">
                    <button class="btn btn-outline" onclick="exportToCSV()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    <button class="btn btn-outline" onclick="printTransactions()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            <!-- Transaction Statistics -->
            <div class="transaction-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['total_transactions']); ?></div>
                    <div class="stat-label">Total Transactions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">$<?php echo number_format($stats['total_volume'], 2); ?></div>
                    <div class="stat-label">Total Volume</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($success_rate, 1); ?>%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['successful_trades']); ?></div>
                    <div class="stat-label">Successful Trades</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-container">
                <h3 style="margin-bottom: 20px;">Filter Transactions</h3>
                <form method="GET" id="filter-form">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Transaction Type</label>
                            <select name="type" class="filter-select" onchange="this.form.submit()">
                                <option value="all" <?php echo $filter_type === 'all' ? 'selected' : ''; ?>>All Types</option>
                                <option value="buy" <?php echo $filter_type === 'buy' ? 'selected' : ''; ?>>Buy</option>
                                <option value="sell" <?php echo $filter_type === 'sell' ? 'selected' : ''; ?>>Sell</option>
                                <option value="send" <?php echo $filter_type === 'send' ? 'selected' : ''; ?>>Send</option>
                                <option value="receive" <?php echo $filter_type === 'receive' ? 'selected' : ''; ?>>Receive</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Cryptocurrency</label>
                            <select name="crypto" class="filter-select" onchange="this.form.submit()">
                                <option value="all" <?php echo $filter_crypto === 'all' ? 'selected' : ''; ?>>All Cryptocurrencies</option>
                                <?php foreach ($cryptocurrencies as $crypto): ?>
                                    <option value="<?php echo $crypto['symbol']; ?>" <?php echo $filter_crypto === $crypto['symbol'] ? 'selected' : ''; ?>>
                                        <?php echo $crypto['name']; ?> (<?php echo $crypto['symbol']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Status</label>
                            <select name="status" class="filter-select" onchange="this.form.submit()">
                                <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="failed" <?php echo $filter_status === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Time Period</label>
                            <select name="date" class="filter-select" onchange="this.form.submit()">
                                <option value="all" <?php echo $filter_date === 'all' ? 'selected' : ''; ?>>All Time</option>
                                <option value="today" <?php echo $filter_date === 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="week" <?php echo $filter_date === 'week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo $filter_date === 'month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="year" <?php echo $filter_date === 'year' ? 'selected' : ''; ?>>This Year</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <button type="button" class="btn btn-outline" onclick="resetFilters()">Reset Filters</button>
                        <span style="color: var(--text-secondary); font-size: 0.9rem;">
                            Showing <?php echo count($transactions); ?> of <?php echo $total_count; ?> transactions
                        </span>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="transactions-table">
                <?php if (count($transactions) > 0): ?>
                    <!-- Desktop Table -->
                    <div class="table-header">
                        <span>Transaction</span>
                        <span>Amount</span>
                        <span class="mobile-hidden">Price</span>
                        <span class="mobile-hidden">Total</span>
                        <span>Status</span>
                        <span>Actions</span>
                    </div>

                    <?php foreach ($transactions as $transaction): ?>
                        <!-- Desktop Row -->
                        <div class="transaction-row">
                            <div class="transaction-details">
                                <div class="transaction-icon type-<?php echo $transaction['type']; ?>">
                                    <i class="fas fa-<?php
                                                        echo $transaction['type'] === 'buy' ? 'shopping-cart' : ($transaction['type'] === 'sell' ? 'dollar-sign' : ($transaction['type'] === 'send' ? 'paper-plane' : 'download'));
                                                        ?>"></i>
                                </div>
                                <div class="transaction-info">
                                    <h4><?php echo ucfirst($transaction['type']); ?> <?php echo $transaction['symbol']; ?></h4>
                                    <p><?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?></p>
                                    <?php if (!empty($transaction['recipient_address'])): ?>
                                        <p style="font-size: 0.8rem; color: var(--text-secondary);">
                                            To: <?php echo substr($transaction['recipient_address'], 0, 20) . '...'; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="transaction-amount">
                                <?php echo number_format($transaction['crypto_amount'], 8); ?> <?php echo $transaction['symbol']; ?>
                            </div>

                            <div class="mobile-hidden">
                                $<?php echo number_format($transaction['price'], 2); ?>
                            </div>

                            <div class="mobile-hidden">
                                <div style="font-weight: 600;">
                                    $<?php echo number_format($transaction['total'], 2); ?>
                                </div>
                                <?php if ($transaction['fee'] > 0): ?>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                        Fee: $<?php echo number_format($transaction['fee'], 2); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div class="status-badge status-<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </div>
                            </div>

                            <div class="transaction-actions">
                                <button class="action-btn view-details-btn" onclick="viewTransactionDetails(<?php echo $transaction['id']; ?>)">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                                <?php if ($transaction['type'] === 'send' && $transaction['status'] === 'pending'): ?>
                                    <button class="action-btn" onclick="speedUpTransaction(<?php echo $transaction['id']; ?>)">
                                        <i class="fas fa-bolt"></i> Speed Up
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Mobile Card -->
                        <div class="mobile-transaction-card">
                            <div class="mobile-card-header">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="transaction-icon type-<?php echo $transaction['type']; ?>" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <i class="fas fa-<?php
                                                            echo $transaction['type'] === 'buy' ? 'shopping-cart' : ($transaction['type'] === 'sell' ? 'dollar-sign' : ($transaction['type'] === 'send' ? 'paper-plane' : 'download'));
                                                            ?>"></i>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0; font-size: 1rem;"><?php echo ucfirst($transaction['type']); ?> <?php echo $transaction['symbol']; ?></h4>
                                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.8rem;">
                                            <?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="status-badge status-<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </div>
                            </div>

                            <div class="mobile-card-details">
                                <div class="detail-row">
                                    <span class="detail-label">Amount:</span>
                                    <span class="transaction-amount"><?php echo number_format($transaction['crypto_amount'], 8); ?> <?php echo $transaction['symbol']; ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Price:</span>
                                    <span>$<?php echo number_format($transaction['price'], 2); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Total:</span>
                                    <span style="font-weight: 600;">$<?php echo number_format($transaction['total'], 2); ?></span>
                                </div>
                                <?php if ($transaction['fee'] > 0): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Fee:</span>
                                        <span>$<?php echo number_format($transaction['fee'], 2); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($transaction['recipient_address'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Recipient:</span>
                                        <span style="font-size: 0.8rem;"><?php echo substr($transaction['recipient_address'], 0, 15) . '...'; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="transaction-actions" style="margin-top: 12px;">
                                <button class="action-btn view-details-btn" onclick="viewTransactionDetails(<?php echo $transaction['id']; ?>)" style="flex: 1;">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <?php if ($transaction['type'] === 'send' && $transaction['status'] === 'pending'): ?>
                                    <button class="action-btn" onclick="speedUpTransaction(<?php echo $transaction['id']; ?>)" style="flex: 1;">
                                        <i class="fas fa-bolt"></i> Speed Up
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-exchange-alt"></i>
                        <h3>No Transactions Found</h3>
                        <p>
                            <?php if ($filter_type !== 'all' || $filter_crypto !== 'all' || $filter_status !== 'all' || $filter_date !== 'all'): ?>
                                Try adjusting your filters to see more results
                            <?php else: ?>
                                Your transaction history will appear here once you start trading
                            <?php endif; ?>
                        </p>
                        <?php if ($filter_type !== 'all' || $filter_crypto !== 'all' || $filter_status !== 'all' || $filter_date !== 'all'): ?>
                            <button class="btn btn-primary" onclick="resetFilters()" style="margin-top: 15px;">
                                Reset Filters
                            </button>
                        <?php else: ?>
                            <a href="buy" class="btn btn-primary" style="margin-top: 15px;">
                                Start Trading
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <button class="page-btn" onclick="changePage(<?php echo $page - 1; ?>)" <?php echo $page <= 1 ? 'disabled' : ''; ?>>
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <button class="page-btn <?php echo $i == $page ? 'active' : ''; ?>" onclick="changePage(<?php echo $i; ?>)">
                            <?php echo $i; ?>
                        </button>
                    <?php endfor; ?>

                    <button class="page-btn" onclick="changePage(<?php echo $page + 1; ?>)" <?php echo $page >= $total_pages ? 'disabled' : ''; ?>>
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal" id="details-modal" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <h2>Transaction Details</h2>
                <p>Complete information about this transaction</p>
            </div>
            <div id="transaction-details-content">
                <!-- Details will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script>
        function resetFilters() {
            window.location.href = 'transactions';
        }

        function changePage(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }

        function exportToCSV() {
            // Show loading state
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
            btn.disabled = true;

            // Simulate CSV export
            setTimeout(() => {
                // In real implementation, this would generate and download a CSV file
                // alert('CSV export functionality would be implemented here. This would download a file containing all your transaction data.');
                setTimeout(() => {
                    showToast('Could not download your transaction data now.', 'error');
                    setTimeout(() => {
                        showToast('PLease try again later.', 'error');
                    }, 2500);
                }, 2000);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        }

        function printTransactions() {
            window.print();
        }

        function viewTransactionDetails(transactionId) {
            // Show loading state
            const modal = document.getElementById('details-modal');
            const content = document.getElementById('transaction-details-content');

            content.innerHTML = `
        <div style="text-align: center; padding: 40px 30px;">
            <div class="processing-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p style="color: var(--text-secondary); margin-top: 15px;">Loading transaction details...</p>
        </div>
    `;

            modal.style.display = 'flex';

            // Fetch actual transaction data from the API
            fetch(`get_transaction_details.php?id=${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.transaction) {
                        const tx = data.transaction;
                        displayTransactionDetails(tx);
                    } else {
                        content.innerHTML = `
                    <div style="text-align: center; padding: 40px 30px; color: var(--danger);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px;"></i>
                        <h3>Error Loading Transaction</h3>
                        <p>${data.message || 'Unable to load transaction details'}</p>
                        <button class="btn btn-outline" onclick="closeDetailsModal()" style="margin-top: 15px;">
                            Close
                        </button>
                    </div>
                `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching transaction details:', error);
                    content.innerHTML = `
                <div style="text-align: center; padding: 40px 30px; color: var(--danger);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px;"></i>
                    <h3>Network Error</h3>
                    <p>Failed to load transaction details. Please try again.</p>
                    <button class="btn btn-outline" onclick="closeDetailsModal()" style="margin-top: 15px;">
                        Close
                    </button>
                </div>
            `;
                });
        }

        function displayTransactionDetails(transaction) {
            const content = document.getElementById('transaction-details-content');

            // Parse metadata if it exists
            let metadata = {};
            if (transaction.metadata) {
                try {
                    metadata = JSON.parse(transaction.metadata);
                } catch (e) {
                    console.error('Error parsing metadata:', e);
                    showToast('Error parsing metadata:', 'error');
                }
            }

            // Get transaction type icon and color
            const typeConfig = getTransactionTypeConfig(transaction.type);

            // Format dates and amounts
            const createdDate = new Date(transaction.created_at).toLocaleString();
            const cryptoAmount = parseFloat(transaction.crypto_amount).toFixed(8);
            const price = parseFloat(transaction.price).toFixed(2);
            const total = parseFloat(transaction.total).toFixed(2);
            const fee = parseFloat(transaction.fee).toFixed(2);

            content.innerHTML = `
        <div class="transaction-type-header">
            <div class="type-icon-large ${typeConfig.class}">
                <i class="${typeConfig.icon}"></i>
            </div>
            <div class="type-info">
                <h3>${typeConfig.label} ${transaction.symbol}</h3>
                <p>Transaction #${transaction.id}</p>
            </div>
        </div>
        
        <div class="transaction-summary">
            <div class="summary-item">
                <span>Transaction ID:</span>
                <span>#${transaction.id}</span>
            </div>
            <div class="summary-item">
                <span>Type:</span>
                <span style="text-transform: capitalize;">${transaction.type}</span>
            </div>
            <div class="summary-item">
                <span>Cryptocurrency:</span>
                <span>${getCryptoFullName(transaction.symbol)} (${transaction.symbol})</span>
            </div>
            <div class="summary-item">
                <span>Amount:</span>
                <span>${cryptoAmount} ${transaction.symbol}</span>
            </div>
            <div class="summary-item">
                <span>Price per coin:</span>
                <span>$${price}</span>
            </div>
            <div class="summary-item">
                <span>Total Amount:</span>
                <span style="color: var(--success); font-size: 1.1rem;">$${total}</span>
            </div>
            ${transaction.fee > 0 ? `
            <div class="summary-item">
                <span>${transaction.type === 'send' ? 'Network Fee' : 'Transaction Fee'}:</span>
                <span>${fee} ${transaction.type === 'send' ? transaction.symbol : 'USD'}</span>
            </div>
            ` : ''}
            ${transaction.type === 'send' && metadata.net_amount ? `
            <div class="summary-item">
                <span>Net Amount Sent:</span>
                <span style="color: var(--primary); font-weight: 600;">
                    ${parseFloat(metadata.net_amount).toFixed(8)} ${transaction.symbol}
                </span>
            </div>
            ` : ''}
            <div class="summary-item">
                <span>Status:</span>
                <span class="status-badge status-${transaction.status}">
                    ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                </span>
            </div>
            <div class="summary-item">
                <span>Date & Time:</span>
                <span>${createdDate}</span>
            </div>
            ${transaction.payment_method ? `
            <div class="summary-item">
                <span>Payment Method:</span>
                <span>
                    ${transaction.payment_method === 'credit_card' ? 'Credit Card' : transaction.payment_method}
                    ${transaction.card_last_four ? ` (**** ${transaction.card_last_four})` : ''}
                </span>
            </div>
            ` : ''}
            ${transaction.priority ? `
            <div class="summary-item">
                <span>Priority:</span>
                <span style="text-transform: capitalize;">${transaction.priority}</span>
            </div>
            ` : ''}
        </div>
        
        ${transaction.type === 'send' && metadata.wallet_address ? `
        <div class="speed-up-section">
            <h3>Transfer Details</h3>
            <div class="summary-item">
                <span>Recipient Address:</span>
                <span style="font-family: monospace; font-size: 0.8rem; word-break: break-all;">
                    ${metadata.wallet_address}
                </span>
            </div>
            ${metadata.network_fee ? `
            <div class="summary-item">
                <span>Network Fee:</span>
                <span>${parseFloat(metadata.network_fee).toFixed(8)} ${transaction.symbol}</span>
            </div>
            ` : ''}
            ${metadata.speed_up ? `
            <div class="summary-item">
                <span>Speed Up:</span>
                <span>${metadata.speed_up ? 'Enabled' : 'Disabled'}</span>
            </div>
            ` : ''}
        </div>
        ` : ''}
        
        ${getTransactionStatusSection(transaction)}
        
        <div class="modal-actions">
            ${transaction.type === 'send' ? `
            <a href="#" class="btn btn-outline" onclick="viewOnBlockchainExplorer('${transaction.symbol}', '${metadata.wallet_address || ''}')">
                <i class="fas fa-external-link-alt"></i> View on Blockchain Explorer
            </a>
            ` : ''}
            ${transaction.type === 'send' && transaction.status === 'pending' ? `
            <button class="btn btn-primary" onclick="speedUpTransaction(${transaction.id})">
                <i class="fas fa-bolt"></i> Speed Up Transaction
            </button>
            ` : `
            <button class="btn btn-primary" onclick="closeDetailsModal()">
                <i class="fas fa-check"></i> Got it
            </button>
            `}
        </div>
    `;
        }

        function getTransactionTypeConfig(type) {
            const configs = {
                'buy': {
                    class: 'type-buy',
                    icon: 'fas fa-shopping-cart',
                    label: 'Buy'
                },
                'sell': {
                    class: 'type-sell',
                    icon: 'fas fa-dollar-sign',
                    label: 'Sell'
                },
                'send': {
                    class: 'type-send',
                    icon: 'fas fa-paper-plane',
                    label: 'Send'
                },
                'receive': {
                    class: 'type-receive',
                    icon: 'fas fa-download',
                    label: 'Receive'
                }
            };
            return configs[type] || configs.buy;
        }

        function getCryptoFullName(symbol) {
            const names = {
                'BTC': 'Bitcoin',
                'ETH': 'Ethereum',
                'USDT': 'Tether',
                'USDC': 'USD Coin',
                'SHIB': 'Shiba Inu'
            };
            return names[symbol] || symbol;
        }

        function getTransactionStatusSection(transaction) {
            if (transaction.status === 'pending' && transaction.type === 'send') {
                return `
                    <div class="progress-section">
                        <div class="progress-info">
                            <span class="progress-label">Network Confirmation</span>
                            <span class="progress-value">Processing...</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 30%"></div>
                        </div>
                        <div class="network-info">
                            <div class="network-item">
                                <div class="network-value">${transaction.priority === 'high' ? 'Fast' : 'Standard'}</div>
                                <div class="network-label">Priority</div>
                            </div>
                            <div class="network-item">
                                <div class="network-value">~${transaction.priority === 'high' ? '5-15' : '15-30'} min</div>
                                <div class="network-label">Est. Time</div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (transaction.status === 'completed' && transaction.type === 'send') {
                return `
                    <div class="progress-section">
                        <div class="progress-info">
                            <span class="progress-label">Network Confirmation</span>
                            <span class="progress-value">12/12 Confirmations</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 100%"></div>
                        </div>
                    </div>
                `;
            }
            return '';
        }

        function viewOnBlockchainExplorer(symbol, address) {
            // Map cryptocurrency symbols to their respective blockchain explorers
            const explorers = {
                'BTC': `https://www.blockchain.com/explorer/addresses/btc/${address}`,
                'ETH': `https://etherscan.io/address/${address}`,
                'USDT': `https://etherscan.io/token/0xdac17f958d2ee523a2206206994597c13d831ec7?a=${address}`,
                'USDC': `https://etherscan.io/token/0xa0b86991c6218b36c1d19d4a2e9eb0ce3606eb48?a=${address}`,
                'SHIB': `https://etherscan.io/token/0x95ad61b0a150d79219dcf64e1e6cc01f0b64c4ce?a=${address}`
            };

            const url = explorers[symbol];
            if (url) {
                window.open(url, '_blank');
            } else {
                alert('Blockchain explorer not available for this cryptocurrency');
                setTimeout(() => {
                    showToast('Blockchain explorer not available for this cryptocurrency', 'error');
                }, 2000);
            }
        }

        // Update the speedUpTransaction function to use your actual API
        function speedUpTransaction(transactionId) {
            if (confirm('Speed up this transaction? This will increase the network fee for faster confirmation.')) {
                // Show loading state
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                btn.disabled = true;

                // Call the actual speed up API
                fetch('speed_up_transaction.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            setTimeout(() => {
                                showToast(data.message, 'success');
                            }, 2000);
                            // Refresh the transaction details
                            viewTransactionDetails(transactionId);
                        } else {
                            alert(data.error || 'Failed to speed up transaction');
                            setTimeout(() => {
                                showToast(data.message || 'Failed to speed up transaction', 'error');
                            }, 2000);
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error speeding up transaction:', error);
                        alert('Network error. Please try again.');
                        setTimeout(() => {
                            showToast('Network error. Please try again.', 'error');
                        }, 2000);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            }
        }

        function closeDetailsModal() {
            document.getElementById('details-modal').style.display = 'none';
        }

        function speedUpTransaction(transactionId) {
            if (confirm('Speed up this transaction? This will increase the network fee for faster confirmation.')) {
                showToast('Speed up this transaction? This will increase the network fee for faster confirmation.', 'success');

                // Show loading state
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                btn.disabled = true;

                // Simulate speed up process
                setTimeout(() => {
                    // alert(`Transaction #${transactionId} has been prioritized for faster processing.`);
                    // setTimeout(() => {
                    showToast(`Transaction #${transactionId} has been prioritized for faster processing.`, 'success');
                    // }, 2500);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    // In real implementation, this would update the transaction status
                }, 2200);
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });

        // Initialize transactions page
        document.addEventListener('DOMContentLoaded', function() {
            // Add any transactions-specific initialization here
            console.log('Transactions page loaded');
        });
    </script>
</body>

</html>