<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Professional Trading Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Enhanced Trading Platform Styles */
        :root {
            --primary: #ff6b35;
            --primary-dark: #e55a2b;
            --secondary: #ff8e53;
            --success: #00c853;
            --danger: #ff3d00;
            --warning: #ffab00;
            --info: #00b8d4;
            --background: #0a0b0d;
            --card-bg: #131722;
            --card-hover: #1a1e2c;
            --text-primary: #e8eaed;
            --text-secondary: #8a94a6;
            --border: #2a2f42;
            --border-light: #363c51;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            --header-height: 70px;
            --sidebar-width: 280px;
            --bottom-nav-height: 70px;
        }

        .light-mode {
            --background: #f8fafc;
            --card-bg: #ffffff;
            --card-hover: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --border-light: #cbd5e1;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        body {
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Professional Trading Header */
        .trading-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            backdrop-filter: blur(20px);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            font-family: 'JetBrains Mono', monospace;
        }

        .logo-badge {
            background: var(--gradient);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .trading-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text-secondary);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gradient);
            transition: all 0.3s;
            transform: translateX(-50%);
        }

        .nav-item:hover {
            color: var(--text-primary);
            background: rgba(255, 107, 53, 0.05);
        }

        .nav-item:hover::before,
        .nav-item.active::before {
            width: 80%;
        }

        .nav-item.active {
            color: var(--primary);
            background: rgba(255, 107, 53, 0.1);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .trading-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid transparent;
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--border);
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Market Data Ticker */
        .market-ticker {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 15px 0;
            overflow: hidden;
            position: relative;
            margin-top: 70px;
        }

        .ticker-container {
            display: flex;
            gap: 40px;
            animation: tickerScroll 60s linear infinite;
        }

        @keyframes tickerScroll {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .ticker-item {
            display: flex;
            align-items: center;
            gap: 15px;
            white-space: nowrap;
        }

        .ticker-symbol {
            font-weight: 600;
            color: var(--text-primary);
        }

        .ticker-price {
            font-weight: 600;
        }

        .ticker-change {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .ticker-change.positive {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .ticker-change.negative {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        /* Main Content */
        .main-content {
            margin-top: calc(var(--header-height) + 60px);
            padding: 20px;
            min-height: calc(100vh - var(--header-height) - 60px);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Trading Grid Layout */
        .trading-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            margin-bottom: 30px;
        }

        .main-chart {
            grid-column: 1;
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .trading-sidebar {
            grid-column: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow);
            transition: all 0.3s;
        }

        .card:hover {
            border-color: var(--border-light);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-primary);
        }

        .card-title i {
            color: var(--primary);
        }

        /* Portfolio Value */
        .portfolio-value {
            text-align: center;
            padding: 30px 0;
        }

        .portfolio-value .value {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .portfolio-change {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Buy/Sell Widget */
        .trading-widget {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .widget-header {
            display: flex;
            border-bottom: 1px solid var(--border);
        }

        .widget-tab {
            flex: 1;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .widget-tab.active {
            background: var(--primary);
            color: white;
        }

        .widget-content {
            padding: 24px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-primary);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Asset Grid */
        .assets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .asset-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .asset-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(255, 107, 53, 0.15);
        }

        .asset-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .asset-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .asset-info h3 {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .asset-info p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .asset-price {
            text-align: right;
        }

        .asset-price .value {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .asset-change {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
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

        /* Mobile Navigation */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card-bg);
            border-top: 1px solid var(--border);
            z-index: 1000;
            padding: 8px 0;
            backdrop-filter: blur(20px);
        }

        .mobile-nav-items {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            height: 100%;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            padding: 8px 0;
            transition: all 0.3s;
            position: relative;
        }

        .mobile-nav-item.active {
            color: var(--primary);
        }

        .mobile-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary);
            border-radius: 0 0 3px 3px;
        }

        .mobile-nav-item i {
            font-size: 1.3rem;
            margin-bottom: 4px;
        }

        .mobile-nav-item span {
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Trading View Styles */
        .trading-view {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .view-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .view-controls {
            display: flex;
            gap: 10px;
        }

        .timeframe-btn {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .timeframe-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .trading-grid {
                grid-template-columns: 1fr;
            }

            .trading-sidebar {
                grid-column: 1;
                grid-template-columns: repeat(2, 1fr);
                display: grid;
            }
        }

        @media (max-width: 768px) {
            .trading-header {
                padding: 0 15px;
            }

            .header-left {
                gap: 15px;
            }

            .trading-nav {
                display: none;
            }

            .main-content {
                padding: 20px 15px;
            }

            .mobile-nav {
                display: block;
            }

            .assets-grid {
                grid-template-columns: 1fr;
            }

            .trading-sidebar {
                grid-template-columns: 1fr;
            }

            .portfolio-value .value {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .header-right .user-name {
                display: none;
            }

            .logo span {
                display: none;
            }

            .logo {
                font-size: 1.2rem;
            }
        }

        /* Enhanced Trading Features */
        .order-book {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 16px;
        }

        .bid-ask-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bid-ask-table td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
        }

        .bid-price {
            color: var(--success);
            font-weight: 600;
        }

        .ask-price {
            color: var(--danger);
            font-weight: 600;
        }

        /* Price movement animations */
        .price-up {
            animation: priceUp 1s ease;
        }

        .price-down {
            animation: priceDown 1s ease;
        }

        @keyframes priceUp {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: rgba(0, 200, 83, 0.2);
            }

            100% {
                background-color: transparent;
            }
        }

        @keyframes priceDown {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: rgba(255, 61, 0, 0.2);
            }

            100% {
                background-color: transparent;
            }
        }
    </style>
</head>

<body>
    <!-- Trading Header -->
    <header class="trading-header">
        <div class="header-left">
            <div class="logo">
                <i class="fas fa-chart-line"></i>
                <span>TradeAuthor</span>
                <div class="logo-badge">PRO</div>
            </div>
            <nav class="trading-nav">
                <div class="nav-item active" data-page="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </div>
                <div class="nav-item" data-page="trade">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Trade</span>
                </div>
                <div class="nav-item" data-page="portfolio">
                    <i class="fas fa-wallet"></i>
                    <span>Portfolio</span>
                </div>
                <div class="nav-item" data-page="buy">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Buy Crypto</span>
                </div>
                <div class="nav-item" data-page="payments">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Payments</span>
                </div>
            </nav>
        </div>
        <div class="header-right">
            <div class="trading-actions">
                <button class="action-btn" id="theme-toggle">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="action-btn" id="notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
            </div>
            <div class="user-profile" id="user-profile">
                <div class="avatar">JS</div>
                <span class="user-name">John Smith</span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </header>

    <!-- Market Ticker -->
    <div class="market-ticker">
        <div class="ticker-container">
            <div class="ticker-item">
                <span class="ticker-symbol">BTC/USD</span>
                <span class="ticker-price">$42,567.89</span>
                <span class="ticker-change positive">+2.34%</span>
            </div>
            <div class="ticker-item">
                <span class="ticker-symbol">ETH/USD</span>
                <span class="ticker-price">$2,345.67</span>
                <span class="ticker-change positive">+1.23%</span>
            </div>
            <div class="ticker-item">
                <span class="ticker-symbol">USDT/USD</span>
                <span class="ticker-price">$1.00</span>
                <span class="ticker-change">0.00%</span>
            </div>
            <div class="ticker-item">
                <span class="ticker-symbol">USDC/USD</span>
                <span class="ticker-price">$1.00</span>
                <span class="ticker-change">0.00%</span>
            </div>
            <div class="ticker-item">
                <span class="ticker-symbol">SHIB/USD</span>
                <span class="ticker-price">$0.00001234</span>
                <span class="ticker-change negative">-0.56%</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Trading Grid Layout -->
            <div class="trading-grid">
                <!-- Main Chart Area -->
                <div class="main-chart">
                    <div class="trading-view">
                        <div class="view-header">
                            <div class="view-title">
                                <h2>BTC/USD Chart</h2>
                                <p>Bitcoin / US Dollar</p>
                            </div>
                            <div class="view-controls">
                                <button class="timeframe-btn active">1H</button>
                                <button class="timeframe-btn">4H</button>
                                <button class="timeframe-btn">1D</button>
                                <button class="timeframe-btn">1W</button>
                                <button class="timeframe-btn">1M</button>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 400px; background: var(--card-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center;">
                                <i class="fas fa-chart-line" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 16px;"></i>
                                <h3>Live Trading Chart</h3>
                                <p>Advanced charting powered by TradingView</p>
                                <button class="btn btn-primary" style="margin-top: 16px;">
                                    <i class="fas fa-rocket"></i> Activate Live Charts
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trading Sidebar -->
                <div class="trading-sidebar">
                    <!-- Buy/Sell Widget -->
                    <div class="trading-widget">
                        <div class="widget-header">
                            <div class="widget-tab active">Buy</div>
                            <div class="widget-tab">Sell</div>
                        </div>
                        <div class="widget-content">
                            <div class="form-group">
                                <label class="form-label">Asset</label>
                                <select class="form-control">
                                    <option>BTC/USD</option>
                                    <option>ETH/USD</option>
                                    <option>USDT/USD</option>
                                    <option>USDC/USD</option>
                                    <option>SHIB/USD</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Order Type</label>
                                <select class="form-control">
                                    <option>Market</option>
                                    <option>Limit</option>
                                    <option>Stop Loss</option>
                                    <option>Take Profit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Amount</label>
                                <input type="text" class="form-control" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Price</label>
                                <input type="text" class="form-control" placeholder="0.00">
                            </div>
                            <button class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-shopping-cart"></i> Buy BTC
                            </button>
                        </div>
                    </div>

                    <!-- Order Book -->
                    <div class="order-book">
                        <div class="card-title">
                            <span>Order Book</span>
                            <span>BTC/USD</span>
                        </div>
                        <table class="bid-ask-table">
                            <tr>
                                <td>Price (USD)</td>
                                <td>Size</td>
                                <td>Total</td>
                            </tr>
                            <!-- Bids -->
                            <tr>
                                <td class="bid-price">42,567.50</td>
                                <td>1.25</td>
                                <td>53,209.38</td>
                            </tr>
                            <tr>
                                <td class="bid-price">42,567.25</td>
                                <td>0.85</td>
                                <td>36,182.16</td>
                            </tr>
                            <tr>
                                <td class="bid-price">42,567.00</td>
                                <td>2.10</td>
                                <td>89,390.70</td>
                            </tr>
                            <!-- Asks -->
                            <tr>
                                <td class="ask-price">42,568.00</td>
                                <td>1.50</td>
                                <td>63,852.00</td>
                            </tr>
                            <tr>
                                <td class="ask-price">42,568.25</td>
                                <td>0.75</td>
                                <td>31,926.19</td>
                            </tr>
                            <tr>
                                <td class="ask-price">42,568.50</td>
                                <td>1.20</td>
                                <td>51,082.20</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Portfolio Overview -->
            <div class="card">
                <div class="card-title">
                    <span>Your Portfolio</span>
                    <button class="btn btn-outline">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="assets-grid">
                    <div class="asset-card">
                        <div class="asset-header">
                            <div class="asset-icon">
                                <i class="fab fa-bitcoin"></i>
                            </div>
                            <div class="asset-info">
                                <h3>Bitcoin</h3>
                                <p>BTC</p>
                            </div>
                        </div>
                        <div class="asset-balance">
                            <div class="value">$42,567.89</div>
                            <div class="crypto-amount">1.23456789 BTC</div>
                        </div>
                        <div class="asset-change positive">+2.34%</div>
                    </div>
                    <div class="asset-card">
                        <div class="asset-header">
                            <div class="asset-icon">
                                <i class="fab fa-ethereum"></i>
                            </div>
                            <div class="asset-info">
                                <h3>Ethereum</h3>
                                <p>ETH</p>
                            </div>
                        </div>
                        <div class="asset-balance">
                            <div class="value">$23,456.78</div>
                            <div class="crypto-amount">10.12345678 ETH</div>
                        </div>
                        <div class="asset-change positive">+1.23%</div>
                    </div>
                    <div class="asset-card">
                        <div class="asset-header">
                            <div class="asset-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="asset-info">
                                <h3>Tether</h3>
                                <p>USDT</p>
                            </div>
                        </div>
                        <div class="asset-balance">
                            <div class="value">$15,000.00</div>
                            <div class="crypto-amount">15,000.00 USDT</div>
                        </div>
                        <div class="asset-change">0.00%</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Navigation -->
    <nav class="mobile-nav">
        <div class="mobile-nav-items">
            <div class="mobile-nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </div>
            <div class="mobile-nav-item">
                <i class="fas fa-exchange-alt"></i>
                <span>Trade</span>
            </div>
            <div class="mobile-nav-item">
                <i class="fas fa-wallet"></i>
                <span>Wallet</span>
            </div>
            <div class="mobile-nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Markets</span>
            </div>
            <div class="mobile-nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </div>
        </div>
    </nav>

    <script>
        // Add this to activate live charts
        new TradingView.widget({
            "width": "100%",
            "height": 400,
            "symbol": "BINANCE:BTCUSDT",
            "interval": "D",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "hide_top_toolbar": true,
            "hide_legend": true,
            "save_image": false,
            "container_id": "tradingview_chart"
        });
        // Theme toggle functionality
        const themeToggle = document.getElementById("theme-toggle");
        const themeIcon = themeToggle.querySelector("i");

        themeToggle.addEventListener("click", () => {
            document.body.classList.toggle("light-mode");
            if (document.body.classList.contains("light-mode")) {
                themeIcon.classList.remove("fa-moon");
                themeIcon.classList.add("fa-sun");
                localStorage.setItem("theme", "light");
            } else {
                themeIcon.classList.remove("fa-sun");
                themeIcon.classList.add("fa-moon");
                localStorage.setItem("theme", "dark");
            }
        });

        // Check for saved theme preference
        if (localStorage.getItem("theme") === "light") {
            document.body.classList.add("light-mode");
            themeIcon.classList.remove("fa-moon");
            themeIcon.classList.add("fa-sun");
        }

        // Navigation functionality
        const navItems = document.querySelectorAll(".nav-item");
        const mobileNavItems = document.querySelectorAll(".mobile-nav-item");

        function navigateTo(page) {
            // Remove active class from all items
            navItems.forEach(item => item.classList.remove("active"));
            mobileNavItems.forEach(item => item.classList.remove("active"));

            // Add active class to clicked item
            event.currentTarget.classList.add("active");

            // Here you would typically load the appropriate page content
            console.log(`Navigating to ${page}`);
        }

        // Add click events to navigation items
        navItems.forEach(item => {
            item.addEventListener("click", () => {
                const page = item.getAttribute("data-page");
                navigateTo(page);
            });
        });

        mobileNavItems.forEach(item => {
            item.addEventListener("click", () => {
                const page = item.querySelector("span").textContent.toLowerCase();
                navigateTo(page);
            });
        });

        // Simulate price updates
        function updatePrices() {
            const prices = document.querySelectorAll(".ticker-price");
            const changes = document.querySelectorAll(".ticker-change");

            prices.forEach(price => {
                const current = parseFloat(price.textContent.replace('$', '').replace(',', ''));
                const change = (Math.random() - 0.5) * 100;
                const newPrice = current + change;

                price.textContent = `$${newPrice.toFixed(2)}`;

                // Add animation class
                price.classList.add(change >= 0 ? 'price-up' : 'price-down');
                setTimeout(() => {
                    price.classList.remove('price-up', 'price-down');
                }, 1000);
            });

            changes.forEach(change => {
                const currentChange = parseFloat(change.textContent);
                const newChange = (Math.random() - 0.5) * 2;

                change.textContent = `${newChange >= 0 ? '+' : ''}${newChange.toFixed(2)}%`;
                change.className = `ticker-change ${newChange >= 0 ? 'positive' : 'negative'}`;
            });
        }

        // Update prices every 5 seconds
        setInterval(updatePrices, 5000);
    </script>
</body>

</html>