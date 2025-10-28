<?php require_once '../app/config/language.php'; ?>
<?php require_once '../app/config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Payment Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Enhanced Payments Styles */
        .payments-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            margin-top: 24px;
        }

        .payments-header {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .payments-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
        }

        .payments-stats {
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

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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

        /* Enhanced Tabs */
        .tabs-enhanced {
            display: flex;
            background: var(--card-bg);
            border-radius: 12px;
            padding: 8px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .tab-enhanced {
            flex: 1;
            padding: 16px 24px;
            text-align: center;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            color: var(--text-secondary);
            position: relative;
            overflow: hidden;
        }

        .tab-enhanced::before {
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

        .tab-enhanced:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.03);
        }

        .tab-enhanced.active {
            color: var(--primary);
            background: rgba(255, 107, 53, 0.1);
        }

        .tab-enhanced.active::before {
            transform: scaleX(1);
        }

        .tab-badge {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        /* Requests Grid */
        .requests-grid {
            display: grid;
            /* grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); */
            gap: 16px;
            margin-bottom: 24px;
        }

        .request-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .request-card::before {
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

        .request-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.15);
        }

        .request-card:hover::before {
            transform: scaleX(1);
        }

        .request-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .request-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--primary);
        }

        .user-info h4 {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .user-info p {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .request-amount {
            text-align: right;
        }

        .amount-value {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .amount-crypto {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .request-details {
            margin-bottom: 15px;
        }

        .request-message {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .request-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .request-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
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

        .status-expired {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
        }

        .request-actions {
            display: flex;
            gap: 8px;
        }

        .btn-pay {
            flex: 1;
            padding: 10px 16px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            background: #00b248;
            transform: translateY(-2px);
        }

        .btn-decline {
            padding: 10px 16px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-decline:hover {
            border-color: var(--danger);
            color: var(--danger);
        }

        /* Request Form */
        .request-form-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-subtitle {
            color: var(--text-secondary);
        }

        .request-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group-enhanced {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label-enhanced {
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control-enhanced {
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control-enhanced:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-control-enhanced::placeholder {
            color: var(--text-secondary);
        }

        .user-search-results {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-top: 8px;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .user-result {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border);
        }

        .user-result:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .user-result:last-child {
            border-bottom: none;
        }

        .amount-preview {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-top: 8px;
        }

        .preview-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .preview-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Sent Requests */
        .sent-request {
            border-left: 4px solid var(--primary);
        }

        .sent-request .request-actions {
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 10px 16px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            border-color: var(--danger);
            color: var(--danger);
        }

        /* Empty States */
        .empty-state-enhanced {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--text-secondary);
            opacity: 0.5;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .empty-description {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        /* Sidebar Styles */
        .payments-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .recent-activity {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .activity-icon.received {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .activity-icon.sent {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary);
        }

        .activity-details {
            flex: 1;
        }

        .activity-details h4 {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .activity-details p {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .activity-amount {
            font-weight: 600;
            text-align: right;
        }

        .activity-amount.positive {
            color: var(--success);
        }

        .activity-amount.negative {
            color: var(--danger);
        }

        .help-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .help-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 16px;
        }

        .help-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .help-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .help-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .help-text {
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .payments-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .payments-stats {
                grid-template-columns: 1fr;
            }

            .quick-actions-grid {
                grid-template-columns: 1fr;
            }

            .requests-grid {
                grid-template-columns: 1fr;
            }

            .tabs-enhanced {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <header style="margin-bottom: 8px;">
                <div class="header-title">
                    <h1>Payment Requests</h1>
                    <p>Send and receive payment requests with other traders</p>
                </div>
            </header>

            <div class="payments-container">
                <!-- Main Content -->
                <div class="payments-main">
                    <!-- Payments Header -->
                    <div class="payments-header">
                        <div class="payments-stats">
                            <div class="stat-card">
                                <div class="stat-value">$2,450.00</div>
                                <div class="stat-label">Pending Requests</div>
                                <div class="stat-change positive">+12.5%</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">$15,280.50</div>
                                <div class="stat-label">Completed This Month</div>
                                <div class="stat-change positive">+8.2%</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">42</div>
                                <div class="stat-label">Active Contacts</div>
                                <div class="stat-change positive">+5</div>
                            </div>
                        </div>

                        <div class="quick-actions-grid">
                            <div class="action-card" onclick="showRequestForm()">
                                <div class="action-icon">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="action-title">Request Payment</div>
                                <div class="action-desc">Send payment request to users</div>
                            </div>
                            <div class="action-card" onclick="showQuickPay(), upgradeToPro()">
                                <div class="action-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="action-title">Quick Pay</div>
                                <div class="action-desc">Send instant payment</div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Tabs -->
                    <div class="tabs-enhanced">
                        <div class="tab-enhanced active" data-tab="received">
                            Received Requests
                            <span class="tab-badge">0</span>
                        </div>
                        <div class="tab-enhanced" data-tab="sent">
                            Sent Requests
                            <span class="tab-badge">0</span>
                        </div>
                    </div>

                    <!-- Received Requests -->
                    <div id="received-requests" class="requests-section">
                        <div class="requests-grid">
                            <!-- Request Card 1 -->
                            <div class="request-card" style="display: none;">
                                <div class="request-header">
                                    <div class="request-user">
                                        <div class="user-avatar">AS</div>
                                        <div class="user-info">
                                            <h4>Alex Johnson</h4>
                                            <p>@alexj</p>
                                        </div>
                                    </div>
                                    <div class="request-amount">
                                        <div class="amount-value">$250.00</div>
                                        <div class="amount-crypto">≈ 0.0058 BTC</div>
                                    </div>
                                </div>
                                <div class="request-details">
                                    <p class="request-message">For the freelance design work completed last week</p>
                                    <div class="request-meta">
                                        <span>2 hours ago</span>
                                        <span class="request-status status-pending">Pending</span>
                                    </div>
                                </div>
                                <div class="request-actions">
                                    <button class="btn-pay" onclick="payRequest(1)">
                                        <i class="fas fa-check"></i> Pay Now
                                    </button>
                                    <button class="btn-decline" onclick="declineRequest(1)">
                                        Decline
                                    </button>
                                </div>
                            </div>

                            <!-- Request Card 2 -->
                            <div class="request-card">
                                <div class="empty-state-enhanced">
                                    <i class="fas fa-inbox empty-icon"></i>
                                    <h3 class="empty-title">No Payment Requests</h3>
                                    <p class="empty-description">Requests from other users will appear here when they send them.</p>
                                    <button class="btn btn-primary" onclick="showRequestForm()">
                                        <i class="fas fa-paper-plane"></i> Send Your First Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sent Requests (Hidden by default) -->
                    <div id="sent-requests" class="requests-section" style="display: none;">
                        <div class="requests-grid">
                            <!-- Sent Request Card 1 -->
                            <div class="request-card sent-request" style="display: none;">
                                <div class="request-header">
                                    <div class="request-user">
                                        <div class="user-avatar">RS</div>
                                        <div class="user-info">
                                            <h4>Robert Smith</h4>
                                            <p>@roberts</p>
                                        </div>
                                    </div>
                                    <div class="request-amount">
                                        <div class="amount-value">$500.00</div>
                                        <div class="amount-crypto">≈ 500 USDC</div>
                                    </div>
                                </div>
                                <div class="request-details">
                                    <p class="request-message">Invoice for website maintenance services</p>
                                    <div class="request-meta">
                                        <span>5 hours ago</span>
                                        <span class="request-status status-pending">Pending</span>
                                    </div>
                                </div>
                                <div class="request-actions">
                                    <button class="btn-cancel" onclick="cancelRequest(1)">
                                        Cancel Request
                                    </button>
                                </div>
                            </div>

                            <!-- Sent Request Card 2 -->
                            <div class="request-card">
                                <div class="empty-state-enhanced">
                                    <i class="fas fa-inbox empty-icon"></i>
                                    <h3 class="empty-title">No Payment Requests</h3>
                                    <p class="empty-description">Requests from other users will appear here when they send them.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Request Form Section -->
                    <div id="request-form-section" class="request-form-section" style="display: none;">
                        <div class="form-header">
                            <div class="form-title">
                                <i class="fas fa-paper-plane"></i>
                                Request Payment
                            </div>
                            <p class="form-subtitle">Send a payment request to another user</p>
                        </div>

                        <form class="request-form" id="payment-request-form">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced" for="user-search">Recipient</label>
                                <input type="text" id="user-search" class="form-control-enhanced" placeholder="Search by username, email, or user ID...">
                                <div class="user-search-results" id="user-results">
                                    <div class="user-result" onclick="selectUser('Alex Johnson', '@alexj')">
                                        <strong>Alex Johnson</strong> - @alexj
                                    </div>
                                    <div class="user-result" onclick="selectUser('Maria Garcia', '@mariag')">
                                        <strong>Maria Garcia</strong> - @mariag
                                    </div>
                                    <div class="user-result" onclick="selectUser('Thomas Wang', '@thomasw')">
                                        <strong>Thomas Wang</strong> - @thomasw
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced" for="cryptocurrency">Cryptocurrency</label>
                                <select id="cryptocurrency" class="form-control-enhanced">
                                    <option value="">Select cryptocurrency</option>
                                    <option value="btc">Bitcoin (BTC)</option>
                                    <option value="eth">Ethereum (ETH)</option>
                                    <option value="usdt">Tether (USDT)</option>
                                    <option value="usdc">USD Coin (USDC)</option>
                                    <option value="shib">Shiba Inu (SHIB)</option>
                                </select>
                            </div>

                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced" for="amount">Amount (USD)</label>
                                <input type="text" id="amount" class="form-control-enhanced" value="0.00" placeholder="Enter amount in USD">
                                <div class="amount-preview">
                                    <div class="preview-label">You'll receive approximately</div>
                                    <div class="preview-value" id="crypto-preview">0.00000000 BTC</div>
                                </div>
                            </div>

                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced" for="message">Message (Optional)</label>
                                <textarea id="message" class="form-control-enhanced" placeholder="Add a message to your payment request..." rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn-pay" style="width: 100%; padding: 16px;">
                                <i class="fas fa-paper-plane"></i> Send Payment Request
                            </button>
                        </form>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="empty-state-enhanced" style="display: none;">
                        <i class="fas fa-inbox empty-icon"></i>
                        <h3 class="empty-title">No Payment Requests</h3>
                        <p class="empty-description">Requests from other users will appear here when they send them.</p>
                        <button class="btn btn-primary" onclick="showRequestForm()">
                            <i class="fas fa-paper-plane"></i> Send Your First Request
                        </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="payments-sidebar">
                    <!-- Recent Activity -->
                    <div class="recent-activity">
                        <div class="section-title">
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon received">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div class="activity-details">
                                    <h4>Payment Received</h4>
                                    <p>From Alex Johnson</p>
                                </div>
                                <div class="activity-amount positive">+$00.00</div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon sent">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="activity-details">
                                    <h4>Request Sent</h4>
                                    <p>To Robert Smith</p>
                                </div>
                                <div class="activity-amount negative">-$00.00</div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon received">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div class="activity-details">
                                    <h4>Payment Received</h4>
                                    <p>From Maria Garcia</p>
                                </div>
                                <div class="activity-amount positive">+$00.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="help-section">
                        <div class="section-title">
                            <i class="fas fa-question-circle"></i>
                            Need Help?
                        </div>
                        <div class="help-list">
                            <div class="help-item" onclick="showHelp('How to send requests')">
                                <div class="help-icon">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="help-text">How to send payment requests</div>
                            </div>
                            <div class="help-item" onclick="showHelp('Payment security')">
                                <div class="help-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="help-text">Payment security tips</div>
                            </div>
                            <div class="help-item" onclick="showHelp('Troubleshooting')">
                                <div class="help-icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="help-text">Troubleshooting common issues</div>
                            </div>
                            <div class="help-item" onclick="showHelp('Contact support')">
                                <div class="help-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div class="help-text">Contact support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Payments functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab-enhanced');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));

                    // Add active class to clicked tab
                    tab.classList.add('active');

                    // Show corresponding content
                    const tabName = tab.getAttribute('data-tab');
                    showTabContent(tabName);
                });
            });

            // User search functionality
            const userSearch = document.getElementById('user-search');
            const userResults = document.getElementById('user-results');

            userSearch.addEventListener('focus', () => {
                userResults.style.display = 'block';
            });

            userSearch.addEventListener('input', () => {
                if (userSearch.value.length > 0) {
                    userResults.style.display = 'block';
                } else {
                    userResults.style.display = 'none';
                }
            });

            // Close search results when clicking outside
            document.addEventListener('click', (e) => {
                if (!userSearch.contains(e.target) && !userResults.contains(e.target)) {
                    userResults.style.display = 'none';
                }
            });

            // Amount conversion
            const amountInput = document.getElementById('amount');
            const cryptoSelect = document.getElementById('cryptocurrency');
            const cryptoPreview = document.getElementById('crypto-preview');

            function updateCryptoPreview() {
                const amount = parseFloat(amountInput.value) || 0;
                const crypto = cryptoSelect.value;

                // Mock conversion rates
                const rates = {
                    'btc': 43000,
                    'eth': 2300,
                    'usdt': 1,
                    'usdc': 1,
                    'shib': 0.000012
                };

                if (crypto && rates[crypto]) {
                    const cryptoAmount = amount / rates[crypto];
                    const symbol = crypto.toUpperCase();
                    cryptoPreview.textContent = `${cryptoAmount.toFixed(8)} ${symbol}`;
                } else {
                    cryptoPreview.textContent = '0.00000000';
                }
            }

            amountInput.addEventListener('input', updateCryptoPreview);
            cryptoSelect.addEventListener('change', updateCryptoPreview);

            // Form submission
            const requestForm = document.getElementById('payment-request-form');
            requestForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const recipient = userSearch.value;
                const cryptocurrency = cryptoSelect.value;
                const amount = amountInput.value;
                const message = document.getElementById('message').value;

                if (!recipient || !cryptocurrency || !amount || amount === '0.00') {
                    alert('Please fill in all required fields');
                    setTimeout(() => {
                        showNotification('Please fill in all required fields!', 'info');
                        setTimeout(() => {
                            showToast('Please fill in all required fields!', 'info');
                        }, 1000);
                    }, 2000);

                    return;
                }

                // Show success message
                showNotification('Payment request could not be sent!', 'info');
                setTimeout(() => {
                    showNotification('Please contact customer support!', 'info');
                }, 300);

                // Reset form
                requestForm.reset();
                cryptoPreview.textContent = '0.00000000';

                // Hide form and show requests
                hideRequestForm();
            });
        });

        function showTabContent(tabName) {
            // Hide all sections
            document.getElementById('received-requests').style.display = 'none';
            document.getElementById('sent-requests').style.display = 'none';
            document.getElementById('empty-state').style.display = 'none';

            // Show selected section
            if (tabName === 'received') {
                document.getElementById('received-requests').style.display = 'block';
            } else if (tabName === 'sent') {
                document.getElementById('sent-requests').style.display = 'block';
            }
        }

        function showRequestForm() {
            // Hide other sections
            document.getElementById('received-requests').style.display = 'none';
            document.getElementById('sent-requests').style.display = 'none';
            document.getElementById('empty-state').style.display = 'none';

            // Show form
            document.getElementById('request-form-section').style.display = 'block';
        }

        function hideRequestForm() {
            document.getElementById('request-form-section').style.display = 'none';
            showTabContent('sent');
        }

        function selectUser(name, username) {
            document.getElementById('user-search').value = `${name} (${username})`;
            document.getElementById('user-results').style.display = 'none';
        }

        function payRequest(requestId) {
            if (confirm('Are you sure you want to pay this request?')) {
                // Simulate payment processing
                showNotification('Processing payment...', 'info');

                setTimeout(() => {
                    showNotification('Payment could not be completed!', 'info');
                    setTimeout(() => {
                        showNotification('Please contact customer support!', 'info');
                    }, 3000);
                    // Remove the request card
                    const requestCard = document.querySelector(`[onclick="payRequest(${requestId})"]`).closest('.request-card');
                    requestCard.style.opacity = '0.5';
                    setTimeout(() => {
                        requestCard.remove();
                        // Check if no more requests
                        if (document.querySelectorAll('.request-card').length === 0) {
                            document.getElementById('empty-state').style.display = 'block';
                        }
                    }, 500);
                }, 2000);
            }
        }

        function declineRequest(requestId) {
            if (confirm('Are you sure you want to decline this request?')) {
                // Remove the request card
                const requestCard = document.querySelector(`[onclick="declineRequest(${requestId})"]`).closest('.request-card');
                requestCard.style.opacity = '0.5';
                setTimeout(() => {
                    requestCard.remove();
                    showNotification('Request declined', 'info');
                    // Check if no more requests
                    if (document.querySelectorAll('.request-card').length === 0) {
                        document.getElementById('empty-state').style.display = 'block';
                    }
                }, 500);
            }
        }

        function cancelRequest(requestId) {
            if (confirm('Are you sure you want to cancel this request?')) {
                // Remove the request card
                const requestCard = document.querySelector(`[onclick="cancelRequest(${requestId})"]`).closest('.request-card');
                requestCard.style.opacity = '0.5';
                setTimeout(() => {
                    requestCard.remove();
                    showNotification('Request cancelled', 'info');
                }, 500);
            }
        }

        function showQuickPay() {
            setTimeout(() => {
                showNotification('Quick Pay feature coming soon!', 'info');
            }, 3000);
            setTimeout(() => {
                showNotification('Contact support to subscribe to pro or go to settings!', 'info');
            }, 4500);
        }

        function showHelp(topic) {
            showNotification(`Help: ${topic} - Documentation will open shortly`, 'info');
            setTimeout(() => {
                showNotification('Documentation could not be open. Please contact customer support!', 'info');
            }, 3000);
            setTimeout(() => {
                showToast('Documentation could not be open. Please contact customer support!', 'info');
            }, 5000);
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.position = 'fixed';
            notification.style.bottom = '20px';
            notification.style.right = '20px';
            notification.style.padding = '16px 24px';
            notification.style.borderRadius = '12px';
            notification.style.color = 'white';
            notification.style.fontWeight = '600';
            notification.style.zIndex = '1000';
            notification.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
            notification.style.transform = 'translateY(100px)';
            notification.style.opacity = '0';
            notification.style.transition = 'all 0.3s ease';

            if (type === 'success') {
                notification.style.background = 'var(--success)';
            } else if (type === 'error') {
                notification.style.background = 'var(--danger)';
            } else {
                notification.style.background = 'var(--primary)';
            }

            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateY(0)';
                notification.style.opacity = '1';
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateY(100px)';
                notification.style.opacity = '0';

                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</body>

</html>