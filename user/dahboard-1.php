<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Advanced Trading</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Enhanced Trading Styles */
        .advanced-trading {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            height: calc(100vh - 180px);
        }

        .chart-section {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .trading-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .market-info {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border);
        }

        .price-display {
            text-align: center;
            padding: 20px 0;
        }

        .current-price {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .price-change {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
        }

        .price-change.positive {
            background: rgba(0, 200, 83, 0.1);
            color: var(--success);
        }

        .price-change.negative {
            background: rgba(255, 61, 0, 0.1);
            color: var(--danger);
        }

        .market-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }

        .stat-value {
            font-weight: 600;
            font-size: 1rem;
        }

        .order-panel {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .order-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
        }

        .order-tab {
            flex: 1;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            background: transparent;
            border: none;
            color: var(--text-secondary);
        }

        .order-tab.active {
            background: var(--primary);
            color: white;
        }

        .order-form {
            padding: 20px;
        }

        .balance-info {
            background: rgba(255, 255, 255, 0.03);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .input-row .form-control {
            flex: 1;
        }

        .percentage-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .percentage-btn {
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .percentage-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .buy-btn, .sell-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .buy-btn {
            background: var(--success);
            color: white;
        }

        .buy-btn:hover {
            background: #00b248;
            transform: translateY(-2px);
        }

        .sell-btn {
            background: var(--danger);
            color: white;
        }

        .sell-btn:hover {
            background: #e03500;
            transform: translateY(-2px);
        }

        .trading-pairs {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px;
        }

        .pair-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .pair-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s;
        }

        .pair-item:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .pair-item.active {
            background: rgba(255, 107, 53, 0.1);
            border-left: 3px solid var(--primary);
        }

        .pair-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pair-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 107, 53, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .pair-name {
            font-weight: 600;
        }

        .pair-symbol {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .pair-price {
            text-align: right;
        }

        .pair-change {
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Depth Chart */
        .depth-chart {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px;
            margin-top: 20px;
        }

        .chart-container {
            height: 200px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .bid-depth {
            position: absolute;
            bottom: 0;
            left: 0;
            background: rgba(0, 200, 83, 0.3);
            height: 60%;
            width: 50%;
            transition: all 0.3s;
        }

        .ask-depth {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(255, 61, 0, 0.3);
            height: 40%;
            width: 50%;
            transition: all 0.3s;
        }

        .depth-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Open Orders */
        .open-orders {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px;
            margin-top: 20px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .orders-table th {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .order-type.buy {
            color: var(--success);
        }

        .order-type.sell {
            color: var(--danger);
        }

        .cancel-order {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .cancel-order:hover {
            border-color: var(--danger);
            color: var(--danger);
        }
    </style>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
    <!-- Trading Header (Same as before) -->
    <header class="trading-header">
        <div class="header-left">
            <div class="logo">
                <i class="fas fa-chart-line"></i>
                <span>TradeAuthor</span>
                <div class="logo-badge">PRO</div>
            </div>
            <nav class="trading-nav">
                <div class="nav-item" data-page="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </div>
                <div class="nav-item active" data-page="trade">
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
            <!-- Ticker items same as before -->
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="advanced-trading">
                <!-- Left Column: Chart and Trading Pairs -->
                <div class="chart-section">
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
                                <h3>Advanced Trading Chart</h3>
                                <p>Real-time charts with technical indicators</p>
                                <button class="btn btn-primary" style="margin-top: 16px;">
                                    <i class="fas fa-rocket"></i> Activate Professional Charts
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Depth Chart -->
                    <div class="depth-chart">
                        <div class="card-title">
                            <span>Market Depth</span>
                            <span>BTC/USD</span>
                        </div>
                        <div class="chart-container">
                            <div class="bid-depth"></div>
                            <div class="ask-depth"></div>
                        </div>
                        <div class="depth-labels">
                            <span>Bids</span>
                            <span>Asks</span>
                        </div>
                    </div>

                    <!-- Open Orders -->
                    <div class="open-orders">
                        <div class="card-title">
                            <span>Open Orders</span>
                            <span>3 Active</span>
                        </div>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Pair</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Price</th>
                                    <th>Filled</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>BTC/USD</td>
                                    <td class="order-type buy">Buy Limit</td>
                                    <td>0.5 BTC</td>
                                    <td>$42,000.00</td>
                                    <td>0%</td>
                                    <td><button class="cancel-order">Cancel</button></td>
                                </tr>
                                <tr>
                                    <td>ETH/USD</td>
                                    <td class="order-type sell">Sell Market</td>
                                    <td>2.0 ETH</td>
                                    <td>$2,350.00</td>
                                    <td>100%</td>
                                    <td><button class="cancel-order">Close</button></td>
                                </tr>
                                <tr>
                                    <td>BTC/USD</td>
                                    <td class="order-type sell">Sell Stop</td>
                                    <td>0.1 BTC</td>
                                    <td>$43,000.00</td>
                                    <td>0%</td>
                                    <td><button class="cancel-order">Cancel</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Trading Panel -->
                <div class="trading-panel">
                    <!-- Market Info -->
                    <div class="market-info">
                        <div class="price-display">
                            <div class="current-price">$42,567.89</div>
                            <div class="price-change positive">+2.34% (+$968.42)</div>
                        </div>
                        <div class="market-stats">
                            <div class="stat-item">
                                <div class="stat-label">24H High</div>
                                <div class="stat-value">$42,789.12</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">24H Low</div>
                                <div class="stat-value">$41,234.56</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">24H Volume</div>
                                <div class="stat-value">$2.45B</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Market Cap</div>
                                <div class="stat-value">$835.2B</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Panel -->
                    <div class="order-panel">
                        <div class="order-tabs">
                            <button class="order-tab active">Limit</button>
                            <button class="order-tab">Market</button>
                            <button class="order-tab">Stop</button>
                        </div>
                        <div class="order-form">
                            <div class="balance-info">
                                Available: $15,284.67 | BTC: 1.2345
                            </div>

                            <div class="input-group">
                                <label class="form-label">Price (USD)</label>
                                <input type="text" class="form-control" value="42,567.89">
                            </div>

                            <div class="input-group">
                                <label class="form-label">Amount (BTC)</label>
                                <input type="text" class="form-control" value="0.1">
                                <div class="percentage-buttons">
                                    <button class="percentage-btn">25%</button>
                                    <button class="percentage-btn">50%</button>
                                    <button class="percentage-btn">75%</button>
                                    <button class="percentage-btn">100%</button>
                                </div>
                            </div>

                            <div class="input-group">
                                <label class="form-label">Total (USD)</label>
                                <input type="text" class="form-control" value="4,256.79" readonly>
                            </div>

                            <button class="buy-btn">
                                <i class="fas fa-arrow-up"></i> Buy BTC
                            </button>
                            <button class="sell-btn">
                                <i class="fas fa-arrow-down"></i> Sell BTC
                            </button>
                        </div>
                    </div>

                    <!-- Trading Pairs -->
                    <div class="trading-pairs">
                        <div class="card-title">
                            <span>Markets</span>
                            <span>24H Change</span>
                        </div>
                        <div class="pair-list">
                            <div class="pair-item active">
                                <div class="pair-info">
                                    <div class="pair-icon">
                                        <i class="fab fa-bitcoin"></i>
                                    </div>
                                    <div>
                                        <div class="pair-name">Bitcoin</div>
                                        <div class="pair-symbol">BTC/USD</div>
                                    </div>
                                </div>
                                <div class="pair-price">
                                    <div>$42,567.89</div>
                                    <div class="pair-change positive">+2.34%</div>
                                </div>
                            </div>
                            <div class="pair-item">
                                <div class="pair-info">
                                    <div class="pair-icon">
                                        <i class="fab fa-ethereum"></i>
                                    </div>
                                    <div>
                                        <div class="pair-name">Ethereum</div>
                                        <div class="pair-symbol">ETH/USD</div>
                                    </div>
                                </div>
                                <div class="pair-price">
                                    <div>$2,345.67</div>
                                    <div class="pair-change positive">+1.23%</div>
                                </div>
                            </div>
                            <div class="pair-item">
                                <div class="pair-info">
                                    <div class="pair-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div>
                                        <div class="pair-name">Tether</div>
                                        <div class="pair-symbol">USDT/USD</div>
                                    </div>
                                </div>
                                <div class="pair-price">
                                    <div>$1.00</div>
                                    <div class="pair-change">0.00%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Trading functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Order type tabs
            const orderTabs = document.querySelectorAll('.order-tab');
            orderTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    orderTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                });
            });

            // Trading pair selection
            const pairItems = document.querySelectorAll('.pair-item');
            pairItems.forEach(item => {
                item.addEventListener('click', () => {
                    pairItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    
                    // Update market data for selected pair
                    const pairName = item.querySelector('.pair-name').textContent;
                    const price = item.querySelector('.pair-price div').textContent;
                    const change = item.querySelector('.pair-change').textContent;
                    
                    document.querySelector('.current-price').textContent = price;
                    document.querySelector('.price-change').textContent = change;
                    document.querySelector('.price-change').className = 
                        `price-change ${change.includes('+') ? 'positive' : 'negative'}`;
                });
            });

            // Percentage buttons
            const percentageBtns = document.querySelectorAll('.percentage-btn');
            percentageBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const percentage = parseFloat(btn.textContent) / 100;
                    const availableBalance = 1.2345; // Example BTC balance
                    const calculatedAmount = availableBalance * percentage;
                    
                    document.querySelector('input[type="text"]').value = calculatedAmount.toFixed(4);
                    updateOrderTotal();
                });
            });

            // Update order total when amount changes
            function updateOrderTotal() {
                const amount = parseFloat(document.querySelector('input[type="text"]').value) || 0;
                const price = parseFloat(document.querySelector('.current-price').textContent.replace('$', '').replace(',', '')) || 0;
                const total = amount * price;
                
                document.querySelector('input[readonly]').value = total.toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'USD'
                });
            }

            // Buy/Sell button actions
            document.querySelector('.buy-btn').addEventListener('click', function() {
                const amount = document.querySelector('input[type="text"]').value;
                const price = document.querySelector('.current-price').textContent;
                alert(`Buy order placed: ${amount} BTC at ${price}`);
            });

            document.querySelector('.sell-btn').addEventListener('click', function() {
                const amount = document.querySelector('input[type="text"]').value;
                const price = document.querySelector('.current-price').textContent;
                alert(`Sell order placed: ${amount} BTC at ${price}`);
            });

            // Cancel order buttons
            const cancelButtons = document.querySelectorAll('.cancel-order');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.style.opacity = '0.5';
                    this.textContent = 'Cancelling...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        row.remove();
                    }, 1000);
                });
            });
        });
    </script>
</body>
</html>