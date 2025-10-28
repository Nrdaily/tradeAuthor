<?php require_once '../app/config/language.php'; ?>
<?php
// portfolio.php - Enhanced Trading Interface
require_once "ini.portfolio.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Portfolio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Enhanced Portfolio Styles */
        .portfolio-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            margin-top: 24px;
        }

        .portfolio-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .portfolio-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
        }

        .portfolio-value-main {
            text-align: center;
            margin-bottom: 30px;
        }

        .total-value {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .portfolio-change {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .change-positive {
            color: var(--success);
        }

        .change-negative {
            color: var(--danger);
        }

        .portfolio-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .stat-change.positive {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .stat-change.negative {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 25px 0;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            border-color: var(--primary);
            background: rgba(255, 107, 53, 0.05);
            transform: translateY(-3px);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .action-card:hover .action-icon {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .action-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .action-desc {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        /* Assets Grid */
        .assets-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-actions {
            display: flex;
            gap: 10px;
        }

        .assets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }

        .asset-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .asset-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .asset-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.15);
        }

        .asset-card:hover::before {
            transform: scaleX(1);
        }

        .asset-card.zero-balance {
            opacity: 0.6;
        }

        .asset-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .asset-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--primary);
        }

        .asset-info {
            flex: 1;
        }

        .asset-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .asset-symbol {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .asset-balance {
            margin-bottom: 15px;
        }

        .asset-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .asset-amount {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .asset-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .asset-change {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .asset-change.positive {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .asset-change.negative {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        .asset-actions {
            display: flex;
            gap: 8px;
        }

        .asset-btn {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .asset-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .asset-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .asset-btn:disabled:hover {
            border-color: var(--border);
            color: var(--text-secondary);
        }

        /* Performance Charts */
        .performance-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .chart-container {
            height: 300px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .chart-placeholder {
            text-align: center;
            color: var(--text-secondary);
        }

        .chart-icon {
            font-size: 3rem;
            margin-bottom: 16px;
            color: var(--primary);
            opacity: 0.7;
        }

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
            display: none;
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

        .crypto-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .crypto-option {
            padding: 15px;
            border: 1px solid #554633ff;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .crypto-option:hover {
            background-color: rgba(233, 160, 14, 0.1);
            border-color: #ff9f29;
        }

        .crypto-option.selected {
            background-color: rgba(233, 145, 14, 0.2);
            border-color: #ff9f29;
        }

        .crypto-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ff9f29;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 10px;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .qr-code img {
            max-width: 200px;
            border-radius: 8px;
        }

        .address-box {
            background-color: #131313ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .address-value {
            word-break: break-all;
            font-family: monospace;
            margin-bottom: 15px;
            font-size: 14px;
            line-height: 1.4;
            user-select: all;
        }

        .address-actions {
            display: flex;
            gap: 10px;
        }

        /* Send form styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #554833ff;
            border-radius: 6px;
            background-color: #1a1a1aff;
            color: #e2e8f0;
        }

        .warning-message,
        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 5px;
        }

        .success-message {
            color: #10b981;
            font-size: 14px;
            margin-top: 5px;
        }

        .transaction-status {
            display: inline-block;
            padding: 4px 8px;
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

        .status-cancelled {
            background-color: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        .speed-up-btn {
            background-color: #f69c3bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }

        .speed-up-btn:disabled {
            background-color: #6b7280;
            cursor: not-allowed;
        }

        .cancel-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }

        /* Transaction summary styles */
        .transaction-summary {
            background-color: #1a1a1aff;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-label {
            color: #b8b194ff;
        }

        .summary-value {
            font-weight: 500;
        }

        .speed-up-section {
            background-color: rgba(246, 143, 59, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #f6b23bff;
        }

        .speed-up-option {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .speed-up-cost {
            margin-left: 10px;
            font-weight: 500;
            color: #f6993bff;
        }

        /* Selection modal styles */
        .selection-modal {
            text-align: center;
        }

        .selection-title {
            margin-bottom: 20px;
        }

        .selection-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .selection-option {
            padding: 20px;
            border: 2px solid #554633ff;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .selection-option:hover {
            border-color: #ff9f29;
            transform: translateY(-3px);
        }

        .selection-icon {
            font-size: 32px;
            margin-bottom: 10px;
            color: #e9ab0eff;
        }

        .selection-text {
            font-weight: 500;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Error and success messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            color: #10b981;
        }

        /* Processing Modal Styles */
        .processing-spinner {
            font-size: 40px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .processing-content {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        #close-processing-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #b8ab94ff;
            font-size: 20px;
            cursor: pointer;
            z-index: 1001;
        }

        /* Transactions Section */
        .transactions-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table th {
            text-align: left;
            padding: 16px 12px;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border);
        }

        .transactions-table td {
            padding: 16px 12px;
            border-bottom: 1px solid var(--border);
        }

        .transaction-item {
            transition: all 0.3s ease;
        }

        .transaction-item:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .transaction-type {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .transaction-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .transaction-icon.buy {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .transaction-icon.sell {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        .transaction-icon.send {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary);
        }

        .transaction-details h4 {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .transaction-details p {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .transaction-amount {
            text-align: right;
            font-weight: 600;
        }

        .transaction-amount.positive {
            color: var(--success);
        }

        .transaction-amount.negative {
            color: var(--danger);
        }

        .transaction-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-failed {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .transaction-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .action-speedup,
        .action-cancel {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-speedup {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary);
        }

        .action-speedup:hover {
            background: var(--primary);
            color: white;
        }

        .action-cancel {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .action-cancel:hover {
            background: #ef4444;
            color: white;
        }

        /* Sidebar Styles */
        .portfolio-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .allocation-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .allocation-header {
            margin-bottom: 20px;
        }

        .allocation-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .allocation-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
        }

        .allocation-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .allocation-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .allocation-details {
            flex: 1;
        }

        .allocation-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .allocation-percentage {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .allocation-bar {
            flex: 2;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin: 0 15px;
            overflow: hidden;
        }

        .allocation-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .allocation-value {
            font-weight: 600;
            min-width: 80px;
            text-align: right;
        }

        .market-overview {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .market-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .market-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .market-item:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .market-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .market-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .market-details h4 {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .market-details p {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .market-price {
            text-align: right;
        }

        .market-value {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .market-change {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .market-change.positive {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .market-change.negative {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .portfolio-container {
                grid-template-columns: 1fr;
            }

            .portfolio-sidebar {
                grid-template-columns: repeat(2, 1fr);
                display: grid;
            }
        }

        @media (max-width: 768px) {
            .portfolio-stats {
                grid-template-columns: 1fr;
            }

            .quick-actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .assets-grid {
                grid-template-columns: 1fr;
            }

            .portfolio-sidebar {
                grid-template-columns: 1fr;
            }

            .total-value {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .quick-actions-grid {
                grid-template-columns: 1fr;
            }

            .section-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <!-- Trading Header -->
    <?php include 'nav.php' ?>


    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <header style="margin-bottom: 8px;">
                <div class="header-title">
                    <h1>Portfolio</h1>
                    <p>Manage your cryptocurrency investments and track performance</p>
                </div>
            </header>

            <!-- Display success/error messages -->
            <?php if (!empty($send_success)): ?>
                <div class="alert success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $send_success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($send_errors)): ?>
                <div class="alert error-message">
                    <?php foreach ($send_errors as $error): ?>
                        <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="portfolio-container">
                <!-- Main Content -->
                <div class="portfolio-main">
                    <!-- Portfolio Header -->
                    <div class="portfolio-header">
                        <div class="portfolio-value-main">
                            <div class="total-value">$<?php echo number_format($totalPortfolioValue, 2); ?></div>
                            <div class="portfolio-change change-positive">
                                <i class="fas fa-arrow-up"></i>
                                +$2,000.04 (+1.00% today)
                            </div>
                        </div>

                        <div class="portfolio-stats">
                            <div class="stat-card">
                                <div class="stat-value">$<?php echo number_format($totalPortfolioValue * 0.6, 2); ?></div>
                                <div class="stat-label">Total Profit</div>
                                <div class="stat-change positive">+12.5%</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">$<?php echo number_format($totalPortfolioValue * 0.3, 2); ?></div>
                                <div class="stat-label">24H Change</div>
                                <div class="stat-change positive">+1.2%</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo count($assets); ?></div>
                                <div class="stat-label">Assets</div>
                                <div class="stat-change positive">+2</div>
                            </div>
                        </div>

                        <div class="quick-actions-grid">
                            <div class="action-card" onclick="openBuyModal()">
                                <div class="action-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="action-title">Buy</div>
                                <div class="action-desc">Purchase Crypto</div>
                            </div>
                            <div class="action-card" id="select-send">
                                <div class="action-icon">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="action-title">Send</div>
                                <div class="action-desc">Transfer Funds</div>
                            </div>
                            <div class="action-card" id="select-receive">
                                <div class="action-icon">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div class="action-title">Receive</div>
                                <div class="action-desc">Get Crypto</div>
                            </div>
                            <div class="action-card" onclick="openSwapModal(), upgradeToPro()">
                                <div class="action-icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="action-title">Swap</div>
                                <div class="action-desc">Exchange Assets</div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <div class="performance-section">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-chart-line"></i>
                                Portfolio Performance
                            </div>
                            <div class="section-actions">
                                <select class="form-control" style="width: auto;">
                                    <option>1 Week</option>
                                    <option>1 Month</option>
                                    <option>3 Months</option>
                                    <option selected>1 Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-container">
                            <div class="chart-placeholder">
                                <i class="fas fa-chart-line chart-icon"></i>
                                <h3>Portfolio Analytics</h3>
                                <p>Track your investment performance over time</p>
                                <button class="btn btn-primary" style="margin-top: 16px;" onclick="analiticsMsg(), upgradeToPro()">
                                    <i class="fas fa-chart-bar"></i> Enable Advanced Analytics
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Assets Section -->
                    <div class="assets-section">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-coins"></i>
                                Your Assets
                            </div>
                            <div class="section-actions">
                                <button class="btn btn-outline">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button class="btn btn-outline">
                                    <i class="fas fa-sort"></i> Sort
                                </button>
                                <button class="btn btn-outline" onclick="refreshPortfolio()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <?php if (count($assets) > 0): ?>
                            <div class="assets-grid">
                                <?php foreach ($assets as $asset): ?>
                                    <div class="asset-card <?php echo $asset['balance'] == 0 ? 'zero-balance' : ''; ?>"
                                        onclick="viewAssetDetails('<?php echo $asset['symbol']; ?>')" data-asset="<?php echo strtolower($asset['symbol']); ?>">
                                        <div class="asset-header">
                                            <div class="asset-icon">
                                                <?php if ($asset['symbol'] === 'USDT'): ?>
                                                    <img src="../assets/icons/usdt.png" width="50" alt="USDT">
                                                <?php elseif ($asset['symbol'] === 'USDC'): ?>
                                                    <img src="../assets/icons/usdc.png" width="50" alt="USDC">
                                                <?php elseif ($asset['symbol'] === 'BTC'): ?>
                                                    <img src="../assets/icons/btc.png" width="50" alt="BTC">
                                                <?php elseif ($asset['symbol'] === 'ETH'): ?>
                                                    <img src="../assets/icons/eth.png" width="50" alt="ETH">
                                                <?php elseif ($asset['symbol'] === 'SHIB'): ?>
                                                    <img src="../assets/icons/shib.png" width="50" alt="SHIB">
                                                <?php else: ?>
                                                    <?php echo $asset['symbol'][0]; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="asset-info">
                                                <div class="asset-name"><?php echo $asset['name']; ?></div>
                                                <div class="asset-symbol"><?php echo $asset['symbol']; ?></div>
                                            </div>
                                        </div>
                                        <div class="asset-balance">
                                            <div class="asset-value">$<?php echo number_format($asset['value'], 2); ?></div>
                                            <div class="asset-amount"><?php echo number_format($asset['balance'], 8); ?> <?php echo $asset['symbol']; ?></div>
                                        </div>
                                        <div class="asset-footer">
                                            <div class="asset-change <?php echo $asset['value'] > 0 ? 'positive' : 'negative'; ?>">
                                                <?php echo $asset['value'] > 0 ? '+2.34%' : '-1.23%'; ?>
                                            </div>
                                            <div class="asset-actions">
                                                <button class="btn btn-outline btn-small send-btn" data-asset="<?php echo strtolower($asset['symbol']); ?>" data-asset-id="<?php echo $asset['id']; ?>" <?php echo $asset['balance'] == 0 ? 'disabled' : ''; ?>>
                                                    Send
                                                </button>
                                                <button class="btn btn-outline btn-small receive-btn" data-asset="<?php echo strtolower($asset['symbol']); ?>" data-asset-id="<?php echo $asset['id']; ?>">
                                                    Receive
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-wallet empty-icon"></i>
                                <h3>No Assets Found</h3>
                                <p>Start building your portfolio by purchasing cryptocurrency</p>
                                <button class="btn btn-primary" style="margin-top: 20px;" onclick="openBuyModal()">
                                    <i class="fas fa-shopping-cart"></i> Buy Crypto
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Transactions Section -->
                    <div class="transactions-section">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-exchange-alt"></i>
                                Recent Transactions
                            </div>
                            <a href="transaction-history.php" class="btn btn-outline">
                                <i class="fas fa-history"></i> View All
                            </a>
                        </div>

                        <?php if (count($transactions) > 0): ?>
                            <table class="transactions-table">
                                <thead>
                                    <tr>
                                        <th>Transaction</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr class="transaction-item">
                                            <td>
                                                <div class="transaction-type">
                                                    <div class="transaction-icon <?php echo $transaction['type']; ?>">
                                                        <i class="fas fa-<?php echo $transaction['type'] === 'buy' ? 'shopping-cart' : ($transaction['type'] === 'send' ? 'paper-plane' : 'exchange-alt'); ?>"></i>
                                                    </div>
                                                    <div class="transaction-details">
                                                        <h4><?php echo ucfirst($transaction['type']); ?> <?php echo $transaction['symbol']; ?></h4>
                                                        <p><?php echo $transaction['type'] === 'send' ? 'To: External Wallet' : 'From: Credit Card'; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="transaction-amount <?php echo $transaction['type'] === 'buy' ? 'negative' : 'positive'; ?>">
                                                <?php echo ($transaction['type'] === 'buy' ? '-' : '+'); ?><?php echo number_format($transaction['crypto_amount'], 8); ?> <?php echo $transaction['symbol']; ?>
                                            </td>
                                            <td>
                                                <?php echo date('M j, Y', strtotime($transaction['created_at'])); ?><br>
                                                <small style="color: var(--text-secondary);"><?php echo date('g:i A', strtotime($transaction['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <span class="transaction-status status-<?php echo $transaction['status']; ?>">
                                                    <?php echo ucfirst($transaction['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($transaction['status'] === 'pending'): ?>
                                                    <button class="speed-up-btn" data-transaction-id="<?php echo $transaction['id']; ?>">
                                                        Speed Up
                                                    </button>
                                                    <button class="cancel-btn" data-transaction-id="<?php echo $transaction['id']; ?>">
                                                        Cancel
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-exchange-alt empty-icon"></i>
                                <h3>No Transactions Yet</h3>
                                <p>Your transaction history will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="portfolio-sidebar">
                    <!-- Allocation Chart -->
                    <div class="allocation-card">
                        <div class="allocation-header">
                            <div class="section-title">
                                <i class="fas fa-chart-pie"></i>
                                Asset Allocation
                            </div>
                        </div>
                        <div class="allocation-list">
                            <?php
                            $colors = ['#ff6b35', '#00c853', '#ffab00', '#00b8d4', '#8e44ad'];
                            $i = 0;
                            foreach ($assets as $asset):
                                $percentage = ($asset['value'] / $totalPortfolioValue) * 100;
                            ?>
                                <div class="allocation-item">
                                    <div class="allocation-info">
                                        <div class="allocation-color" style="background: <?php echo $colors[$i % count($colors)]; ?>"></div>
                                        <div class="allocation-details">
                                            <div class="allocation-name"><?php echo $asset['symbol']; ?></div>
                                            <div class="allocation-percentage"><?php echo number_format($percentage, 1); ?>%</div>
                                        </div>
                                    </div>
                                    <div class="allocation-bar">
                                        <div class="allocation-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $colors[$i % count($colors)]; ?>"></div>
                                    </div>
                                    <div class="allocation-value">$<?php echo number_format($asset['value'], 2); ?></div>
                                </div>
                            <?php $i++;
                            endforeach; ?>
                        </div>
                    </div>

                    <!-- Market Overview -->
                    <div class="market-overview">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-globe"></i>
                                Market Overview
                            </div>
                        </div>
                        <div class="market-list">
                            <div class="market-item" onclick="viewMarket('BTC')">
                                <div class="market-info">
                                    <div class="market-icon">
                                        <img src="../assets/icons/btc.png" width="50" alt="">
                                    </div>
                                    <div class="market-details">
                                        <h4>Bitcoin</h4>
                                        <p>BTC/USD</p>
                                    </div>
                                </div>
                                <div class="market-price">
                                    <div class="market-value">$42,567.89</div>
                                    <div class="market-change positive">+2.34%</div>
                                </div>
                            </div>
                            <div class="market-item" onclick="viewMarket('ETH')">
                                <div class="market-info">
                                    <div class="market-icon">
                                        <img src="../assets/icons/eth.png" width="50" alt="">
                                    </div>
                                    <div class="market-details">
                                        <h4>Ethereum</h4>
                                        <p>ETH/USD</p>
                                    </div>
                                </div>
                                <div class="market-price">
                                    <div class="market-value">$2,345.67</div>
                                    <div class="market-change positive">+1.23%</div>
                                </div>
                            </div>
                            <div class="market-item" onclick="viewMarket('USDT')">
                                <div class="market-info">
                                    <div class="market-icon">
                                        <img src="../assets/icons/usdc.png" width="50" alt="">
                                    </div>
                                    <div class="market-details">
                                        <h4>Tether</h4>
                                        <p>USDT/USD</p>
                                    </div>
                                </div>
                                <div class="market-price">
                                    <div class="market-value">$1.00</div>
                                    <div class="market-change">0.00%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toast-message">Success!</span>
    </div>

    <!-- Action Selection Modal -->
    <div class="modal" id="action-selection-modal">
        <div class="modal-content selection-modal">
            <button class="modal-close" id="close-selection-modal">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2>Choose Action</h2>
                <p>What would you like to do?</p>
            </div>

            <div class="selection-options">
                <div class="selection-option" id="select-send">
                    <div class="selection-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="selection-text">Send Crypto</div>
                </div>

                <div class="selection-option" id="select-receive">
                    <div class="selection-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="selection-text">Receive Crypto</div>
                </div>
            </div>

            <p>Select an action to continue</p>
        </div>
    </div>

    <!-- Crypto Selection Modal -->
    <div class="modal" id="crypto-selection-modal">
        <div class="modal-content">
            <button class="modal-close" id="close-crypto-selection-modal">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2 id="selection-modal-title">Select Cryptocurrency</h2>
                <p id="selection-modal-description">Choose a cryptocurrency to continue</p>
            </div>

            <div class="crypto-selector">
                <?php foreach ($assets as $asset): ?>
                    <div class="crypto-option" data-crypto-id="<?php echo $asset['id']; ?>" data-symbol="<?php echo $asset['symbol']; ?>">
                        <div class="crypto-icon">
                            <?php if ($asset['symbol'] === 'USDT'): ?>
                                <img src="../assets/icons/usdt.png" width="50">
                            <?php elseif ($asset['symbol'] === 'USDC'): ?>
                                <img src="../assets/icons/usdc.png" width="80">
                            <?php elseif ($asset['symbol'] === 'BTC'): ?>
                                <img src="../assets/icons/btc.png" width="50">
                            <?php elseif ($asset['symbol'] === 'ETH'): ?>
                                <img src="../assets/icons/eth.png" width="50">
                            <?php elseif ($asset['symbol'] === 'SHIB'): ?>
                                <img src="../assets/icons/shib.png" width="50">
                            <?php else: ?>
                                <?php echo $asset['symbol'][0]; ?>
                            <?php endif; ?>
                        </div>
                        <div class="crypto-name"><?php echo $asset['name']; ?></div>
                        <div class="crypto-symbol"><?php echo $asset['symbol']; ?></div>
                        <div class="crypto-amount"><?php echo number_format($asset['balance'], 8); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="btn btn-primary" id="confirm-crypto-selection" disabled>
                Continue
            </button>
        </div>
    </div>

    <!-- Send Crypto Modal -->
    <div class="modal" id="send-crypto-modal">
        <div class="modal-content">
            <button class="modal-close" id="close-send-modal">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2>Send <span id="send-crypto-name"></span></h2>
                <p>Send cryptocurrency to an external wallet</p>
            </div>

            <form method="POST" action="portfolio.php">
                <input type="hidden" name="send_crypto" value="1">
                <input type="hidden" id="selected-crypto-id" name="crypto_id" value="">

                <div class="form-group">
                    <label for="wallet-address" class="form-label">Recipient Wallet Address</label>
                    <input type="text" id="wallet-address" name="wallet_address" class="form-control" placeholder="Enter external wallet address" required>
                </div>

                <div class="form-group">
                    <label for="send-amount" class="form-label">Amount</label>
                    <input type="number" id="send-amount" name="amount" class="form-control" step="0.00000001" min="0.00000001" placeholder="Enter amount" required>
                    <div class="crypto-amount" id="available-balance">Available: 0.00000000</div>
                </div>

                <!-- Transaction Summary -->
                <div class="transaction-summary">
                    <h3>Transaction Summary</h3>
                    <div class="summary-item">
                        <span class="summary-label">Amount to Send:</span>
                        <span class="summary-value" id="summary-amount">0.00000000</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Network Fee:</span>
                        <span class="summary-value" id="summary-fee">0.00000000</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">You Will Receive:</span>
                        <span class="summary-value" id="summary-net">0.00000000</span>
                    </div>
                </div>

                <!-- Speed Up Option -->
                <div class="speed-up-section">
                    <h3>Speed Up Transaction</h3>
                    <p>Priority processing for faster confirmation</p>
                    <div class="speed-up-option">
                        <input type="checkbox" id="speed-up-transaction" name="speed_up" value="1">
                        <label for="speed-up-transaction">Enable Priority Processing</label>
                        <span class="speed-up-cost" id="speed-up-cost">+ 0.0005 ETH</span>
                    </div>
                    <small>This will prioritize your transaction for faster network confirmation</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Confirm Send
                </button>
            </form>

            <div class="warning-message" style="margin-top: 20px;">
                <p>
                    <i class="fas fa-exclamation-triangle"></i> Please double-check the wallet address.
                    Cryptocurrency transactions are irreversible once confirmed on the blockchain.
                </p>
            </div>
        </div>
    </div>

    <!-- Receive Crypto Modal -->
    <div class="modal" id="receive-crypto-modal">
        <div class="modal-content">
            <button class="modal-close" id="close-receive-modal">
                <i class="fas fa-times"></i>
            </button>

            <div class="modal-header">
                <h2 id="receive-title">Receive <span id="receive-crypto-name"></span></h2>
                <p>Share your address to receive funds</p>
            </div>

            <div class="qr-code">
                <img id="receive-qr-code" src="" alt="QR Code" style="max-width: 200px; border: 1px solid #ddd; padding: 10px; background: white;">
            </div>

            <div class="address-box">
                <h3 class="address-value" id="receive-address">
                    Select a cryptocurrency to view address
                </h3>
                <div class="address-actions">
                    <button class="btn btn-outline" id="copy-address-btn">
                        <i class="fas fa-copy"></i> Copy Address
                    </button>
                    <button class="btn btn-outline" id="share-address-btn">
                        <i class="fas fa-share-alt"></i> Share Address
                    </button>
                </div>
            </div>

            <div class="warning-message">
                <p id="receive-warning">
                    <i class="fas fa-exclamation-triangle"></i> Only send the selected cryptocurrency to this address.
                </p>
            </div>
        </div>
    </div>

    <!--  Processing Modal -->
    <div class="modal" id="processing-modal">
        <div class="modal-content">
            <button class="modal-close" id="close-processing-modal">
                <i class="fas fa-times"></i>
            </button>
            <div class="processing-content">
                <div class="processing-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>

                <h2>Processing Withdrawal</h2>
                <p>Your transaction is being processed on the blockchain</p>

                <div class="confirmation-count" id="send-confirmation-count">
                    0/12 Confirmations
                </div>

                <!-- Transaction Details -->
                <div class="transaction-summary">
                    <div class="summary-item">
                        <span class="summary-label">Transaction ID:</span>
                        <span class="summary-value" id="processing-id">#<?php echo isset($transaction_id) ? $transaction_id : ''; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Cryptocurrency:</span>
                        <span class="summary-value" id="processing-crypto"><?php echo isset($selectedCrypto) ? $selectedCrypto['symbol'] : ''; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Amount:</span>
                        <span class="summary-value" id="processing-amount"><?php echo isset($amount) ? number_format($amount, 8) : ''; ?> <?php echo isset($selectedCrypto) ? $selectedCrypto['symbol'] : ''; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Network Fee:</span>
                        <span class="summary-value" id="processing-fee"><?php echo isset($network_fee) ? number_format($network_fee, 8) : ''; ?> <?php echo isset($selectedCrypto) ? $selectedCrypto['symbol'] : ''; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Recipient:</span>
                        <span class="summary-value" id="processing-recipient"><?php echo isset($wallet_address) ? substr($wallet_address, 0, 20) . '...' : ''; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Status:</span>
                        <span class="summary-value status-pending">Pending Approval</span>
                    </div>
                </div>

                <div class="speed-up-section">
                    <h3>Transaction Options</h3>
                    <p>Your transaction is being processed. You can:</p>

                    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                        <button class="btn btn-primary" id="processing-speed-up-btn" data-transaction-id="<?php echo isset($transaction_id) ? $transaction_id : ''; ?>">
                            <i class="fas fa-bolt"></i> Speed Up Transaction
                        </button>
                        <button class="btn btn-outline" id="processing-cancel-btn" data-transaction-id="<?php echo isset($transaction_id) ? $transaction_id : ''; ?>">
                            <i class="fas fa-times"></i> Cancel Transaction
                        </button>
                    </div>

                    <small style="display: block; margin-top: 10px;">
                        <i class="fas fa-info-circle"></i>
                        Transactions typically take 15-30 minutes to process. Speed up option prioritizes your transaction.
                    </small>
                </div>

                <div style="margin-top: 20px;">
                    <button class="btn btn-primary" onclick="closeSendProcessingModal()" style="margin-top: 20px;">
                        <i class="fas fa-check"></i> OK, I'll Wait
                    </button>
                    <button class="btn btn-outline" onclick="viewTransactionHistory()">
                        <i class="fas fa-history"></i> View Transaction History
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Portfolio functionality
        function refreshPortfolio() {
            const refreshBtn = document.querySelector('.btn-outline i.fa-sync-alt');
            refreshBtn.classList.add('fa-spin');

            // Simulate API call
            setTimeout(() => {
                refreshBtn.classList.remove('fa-spin');
                showNotification('Portfolio updated successfully', 'success');

                // Update values with slight random changes to simulate real data
                updatePortfolioValues();
            }, 1500);
        }

        function updatePortfolioValues() {
            // Update asset values with random changes
            const assetValues = document.querySelectorAll('.asset-value');
            assetValues.forEach(valueEl => {
                const currentValue = parseFloat(valueEl.textContent.replace('$', '').replace(',', ''));
                const randomChange = (Math.random() - 0.5) * currentValue * 0.02; // ±1% change
                const newValue = currentValue + randomChange;

                valueEl.textContent = `$${newValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            });

            // Update portfolio total
            const totalValueEl = document.querySelector('.total-value');
            const currentTotal = parseFloat(totalValueEl.textContent.replace('$', '').replace(',', ''));
            const newTotal = currentTotal + (Math.random() - 0.5) * currentTotal * 0.01;
            totalValueEl.textContent = `$${newTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }

        function viewAssetDetails(symbol) {
            // In a real app, this would navigate to asset details or show a modal
            console.log(`Viewing details for ${symbol}`);
            showNotification(`Can't open ${symbol} details right now!`, 'info');
        }

        function viewMarket(symbol) {
            console.log(`Viewing market for ${symbol}`);
            showNotification(`Opening ${symbol} market`, 'info');
            setTimeout(() => {
                openBuyModal();
            }, 2000);
        }

        function openBuyModal() {
            // This would open the buy crypto modal
            window.location.href = 'buy.php';
        }

        function openSendModal() {
            // This would open the send crypto modal
            document.getElementById('action-selection-modal').classList.add('active');
        }

        function openReceiveModal() {
            // This would open the receive crypto modal
            document.getElementById('action-selection-modal').classList.add('active');
        }

        function openSwapModal() {
            // This would open the swap assets modal
            showNotification('Swap functionality coming soon', 'info');
            setTimeout(() => {
                showNotification('Upgrade to pro for this feature', 'info');
            }, 3000);
        }

        function analiticsMsg() {
            // This would open the swap assets modal
            setTimeout(() => {
                showNotification('Upgrade to pro for this feature', 'info');
            }, 2000);
        }

        function openSendAssetModal(symbol, assetId) {
            // This would open the send modal for a specific asset
            console.log(`Sending ${symbol} with ID ${assetId}`);
            // Implementation from original portfolio.php
        }

        function openReceiveAssetModal(symbol, assetId) {
            // This would open the receive modal for a specific asset
            console.log(`Receiving ${symbol} with ID ${assetId}`);
            // Implementation from original portfolio.php
        }

        // Notification function
        function showNotification(message, type) {
            // Implementation from original app.js
            console.log(`${type}: ${message}`);
        }

        // Update portfolio values every 30 seconds to simulate real-time updates
        setInterval(updatePortfolioValues, 30000);
    </script>
    <script src="../assets/js/app.js"></script>
    <?php require_once "script_portfolio.php" ?>
</body>

</html>