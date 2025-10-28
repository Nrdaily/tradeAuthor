<?php require_once '../app/config/language.php'; ?>
<?php
// Enhanced index.php with complete orange-branded trading dashboard
require_once '../app/config/session.php';
require_once '../app/func/functions.php';

// Get live cryptocurrency prices
$livePrices = getLiveCryptoPrices();

// Get user portfolio data
$query = "
    SELECT c.symbol, c.name, c.current_price, 
           u.usdt_balance, u.usdc_balance, u.btc_balance, u.eth_balance, u.shib_balance
    FROM users u
    CROSS JOIN cryptocurrencies c
    WHERE u.id = :user_id
";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$portfolio = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total portfolio value with live prices
$totalPortfolioValue = 0;
$assets = [];

foreach ($portfolio as $crypto) {
    $symbol = $crypto['symbol'];
    $balance = $crypto[strtolower($symbol) . '_balance'];
    $current_price = $livePrices[$symbol]['price'] ?? $crypto['current_price'];
    $value = $balance * $current_price;
    $totalPortfolioValue += $value;

    $assets[] = [
        'symbol' => $symbol,
        'name' => $crypto['name'],
        'current_price' => $current_price,
        'balance' => $balance,
        'value' => $value,
        'change' => $livePrices[$symbol]['change'] ?? 0
    ];
}

// Get recent transactions
$query = "
    SELECT t.*, c.symbol, c.name
    FROM transactions t
    JOIN cryptocurrencies c ON t.crypto_id = c.id
    WHERE t.user_id = :user_id
    ORDER BY t.created_at DESC
    LIMIT 5
";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

function generateRealisticTraders($marketType = 'general') {
    $distributions = [
        'small' => ['min' => 1, 'max' => 10, 'peak' => 5],
        'medium' => ['min' => 10, 'max' => 50, 'peak' => 25],
        'large' => ['min' => 50, 'max' => 200, 'peak' => 100],
        'crypto' => ['min' => 100, 'max' => 1000, 'peak' => 300],
        'forex' => ['min' => 500, 'max' => 5000, 'peak' => 1500]
    ];
    
    $config = $distributions[$marketType] ?? $distributions['medium'];
    
    // Normal distribution around the peak for more realism
    $mean = $config['peak'];
    $stdDev = ($config['max'] - $config['min']) / 6; // 99.7% within min-max
    
    do {
        $traders = (int) round(random_normal($mean, $stdDev));
    } while ($traders < $config['min'] || $traders > $config['max']);
    
    return $traders;
}

// Helper function for normal distribution
function random_normal($mean, $stdDev) {
    $x = mt_rand() / mt_getrandmax();
    $y = mt_rand() / mt_getrandmax();
    
    return sqrt(-2 * log($x)) * cos(2 * pi() * $y) * $stdDev + $mean;
}

$traders = generateRealisticTraders();

function generateRandomTraders($min = 1, $max = 20) {
    return rand($min, $max);
}

// Usage
$justJoined = generateRandomTraders();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Professional Trading Platform</title>
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .trading-view-container {
            width: 100%;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .chart-controls {
            display: flex;
            gap: 10px;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
        }

        .timeframe-controls {
            display: flex;
            gap: 5px;
        }

        .live-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--success);
            font-weight: 600;
            margin-left: auto;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 1;
            }
        }

        .order-book {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 16px;
            max-height: 400px;
            overflow-y: auto;
        }

        .order-book-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .order-price.bid {
            color: var(--success);
        }

        .order-price.ask {
            color: var(--danger);
        }

        .order-amount {
            color: var(--text-secondary);
        }

        .order-total {
            font-weight: 600;
        }

        .recent-trades {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
        }

        .trade-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .trade-price.buy {
            color: var(--success);
        }

        .trade-price.sell {
            color: var(--danger);
        }

        .trade-amount {
            color: var(--text-secondary);
        }

        .trade-time {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .trading-view-container {
                width: 90vw;
                overflow: hidden;
            }
        }
    </style>
</head>

<body class="theme-transition">
    <?php include "nav.php"; ?>

    <div class="main-content">
        <div class="container">
            <!-- Dashboard Overview -->
            <div class="dashboard-overview">
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="overview-value">$<?php echo number_format($totalPortfolioValue, 2); ?></div>
                    <div class="overview-label">Total Portfolio Value</div>
                    <!-- <div class="overview-change positive">+2.34% Today</div> -->
                </div>
                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="overview-value">$0</div>
                    <div class="overview-label">24h Profit/Loss</div>
                    <div class="overview-change positive">+$0.56</div>
                </div>

                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="overview-value"><?= $traders ?></div>
                    <div class="overview-label">Active Trades</div>
                    <div class="overview-change positive">+<?= $justJoined ?> This Week</div>
                </div>

                <div class="overview-card">
                    <div class="overview-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="overview-value">98.7%</div>
                    <div class="overview-label">Success Rate</div>
                    <div class="overview-change positive">+2.1% This Month</div>
                </div>
            </div>

            <!-- Trading Grid -->
            <div class="dashboard-grid">
                <!-- Main Trading Chart -->
                <div class="trading-view-container">
                    <div class="view-header">
                        <div>
                            <h3>BTC/USDT Live Chart</h3>
                            <p>Bitcoin / Tether</p>
                        </div>
                        <div class="live-indicator">
                            <div class="live-dot"></div>
                            <span>LIVE</span>
                        </div>
                    </div>

                    <div class="chart-controls">
                        <div class="timeframe-controls">
                            <button class="timeframe-btn active" data-timeframe="1">1H</button>
                            <button class="timeframe-btn" data-timeframe="4">4H</button>
                            <button class="timeframe-btn" data-timeframe="24">1D</button>
                            <button class="timeframe-btn" data-timeframe="168">1W</button>
                            <button class="timeframe-btn" data-timeframe="720">1M</button>
                        </div>
                    </div>

                    <div style="height: 400px; padding: 20px;">
                        <canvas id="tradingChart"></canvas>
                    </div>
                </div>

                <!-- Trading Sidebar -->
                <div class="trading-sidebar">
                    <!-- Trading Widget -->
                    <div class="trading-widget">
                        <div class="widget-header">
                            <div onclick="toggleWidgeet()" class="widget-tab active">Buy</div>
                            <div onclick="toggleWidgeet()" class="widget-tab">Sell</div>
                        </div>

                        <div class="widget-content">
                            <div class="form-group">
                                <label class="form-label">Asset</label>
                                <select class="form-control">
                                    <option>BTC/USDT</option>
                                    <option>ETH/USDT</option>
                                    <option>SOL/USDT</option>
                                    <option>ADA/USDT</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Amount</label>
                                <input type="number" class="form-control" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Price</label>
                                <input type="number" class="form-control" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Total</label>
                                <input type="number" class="form-control" placeholder="0.00" readonly>
                            </div>

                            <button class="btn btn-primary" style="width: 100%;" onclick="upgradeToPro()">
                                <i class="fas fa-bolt"></i> Execute Trade
                            </button>
                        </div>
                    </div>

                    <!-- Order Book -->
                    <div class="order-book">
                        <div class="order-book-title">Order Book - BTC/USDT</div>
                        <div class="order-rows">
                            <!-- Bids -->
                            <div class="order-row">
                                <span class="order-price bid">$45,218.34</span>
                                <span class="order-amount">2.45 BTC</span>
                                <span class="order-total">$110,784.93</span>
                            </div>
                            <div class="order-row">
                                <span class="order-price bid">$45,216.78</span>
                                <span class="order-amount">1.23 BTC</span>
                                <span class="order-total">$55,616.64</span>
                            </div>
                            <div class="order-row">
                                <span class="order-price bid">$45,215.42</span>
                                <span class="order-amount">0.87 BTC</span>
                                <span class="order-total">$39,337.42</span>
                            </div>

                            <!-- Spread -->
                            <div class="order-row" style="background: rgba(255, 107, 53, 0.1); font-weight: 600;">
                                <span class="order-price">$45,220.15</span>
                                <span class="order-amount">Spread: $1.81</span>
                                <span class="order-total">0.004%</span>
                            </div>

                            <!-- Asks -->
                            <div class="order-row">
                                <span class="order-price ask">$45,221.96</span>
                                <span class="order-amount">1.56 BTC</span>
                                <span class="order-total">$70,546.26</span>
                            </div>
                            <div class="order-row">
                                <span class="order-price ask">$45,223.45</span>
                                <span class="order-amount">0.92 BTC</span>
                                <span class="order-total">$41,605.57</span>
                            </div>
                            <div class="order-row">
                                <span class="order-price ask">$45,225.12</span>
                                <span class="order-amount">3.21 BTC</span>
                                <span class="order-total">$145,172.63</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Trades -->
                    <div class="recent-trades">
                        <div class="order-book-title">Recent Trades</div>
                        <div class="trade-list">
                            <div class="trade-item">
                                <span class="trade-price buy">$45,218.34</span>
                                <span class="trade-amount">0.125 BTC</span>
                                <span class="trade-time">12:45:23</span>
                            </div>
                            <div class="trade-item">
                                <span class="trade-price sell">$45,219.87</span>
                                <span class="trade-amount">0.087 BTC</span>
                                <span class="trade-time">12:45:19</span>
                            </div>
                            <div class="trade-item">
                                <span class="trade-price buy">$45,217.56</span>
                                <span class="trade-amount">0.234 BTC</span>
                                <span class="trade-time">12:45:15</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-bolt"></i>
                    <span>Quick Actions</span>
                </div>
                <div class="quick-actions">
                    <div class="action-btn-large" onclick="location.href='buy'">
                        <div class="action-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <span>Buy Crypto</span>
                    </div>
                    <div class="action-btn-large" onclick="location.href='portfolio'">
                        <div class="action-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <span>Sell Crypto</span>
                    </div>
                    <div class="action-btn-large" onclick="location.href='portfolio#send'">
                        <div class="action-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <span>Send Funds</span>
                    </div>
                    <div class="action-btn-large" onclick="location.href='portfolio#receive'">
                        <div class="action-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <span>Receive Funds</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Market Overview -->
            <div class="trading-grid" style="margin-top: 30px;">
                <!-- Recent Transactions -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Recent Transactions</span>
                    </div>
                    <?php if (count($transactions) > 0): ?>
                        <ul class="activity-list">
                            <?php foreach ($transactions as $transaction): ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-<?php echo $transaction['type'] === 'buy' ? 'shopping-cart' : 'exchange-alt'; ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h4><?php echo ucfirst($transaction['type']); ?> <?php echo $transaction['symbol']; ?></h4>
                                        <p><?php echo date('M j, Y g:i A', strtotime($transaction['created_at'])); ?></p>
                                    </div>
                                    <div class="activity-amount <?php echo $transaction['type   '] === 'buy' ? 'negative' : 'positive'; ?>">
                                        <?php echo ($transaction['type'] === 'buy' ? '-' : '+'); ?><?php echo number_format($transaction['crypto_amount'], 8); ?> <?php echo $transaction['symbol']; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-exchange-alt"></i>
                            <h3>No transactions yet</h3>
                            <p>Your transaction history will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Market Overview -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        <span>Market Overview</span>
                    </div>
                    <div class="assets-grid">
                        <?php foreach ($assets as $asset): ?>
                            <div class="asset-card">
                                <div class="asset-header">
                                    <div class="asset-icon">
                                        <?php if ($asset['symbol'] === 'USDT'): ?>
                                            <img src="../assets/icons/usdt.png" width="40">
                                        <?php elseif ($asset['symbol'] === 'USDC'): ?>
                                            <img src="../assets/icons/usdc.png" width="40">
                                        <?php elseif ($asset['symbol'] === 'BTC'): ?>
                                            <img src="../assets/icons/btc.png" width="40">
                                        <?php elseif ($asset['symbol'] === 'ETH'): ?>
                                            <img src="../assets/icons/eth.png" width="40">
                                        <?php elseif ($asset['symbol'] === 'SHIB'): ?>
                                            <img src="../assets/icons/shib.png" width="40">
                                        <?php endif; ?>
                                    </div>
                                    <div class="asset-info">
                                        <h3><?php echo $asset['name']; ?></h3>
                                        <p><?php echo $asset['symbol']; ?></p>
                                    </div>
                                </div>
                                <div class="asset-price">
                                    <div class="value">$<?php echo number_format($asset['current_price'], $asset['symbol'] === 'SHIB' ? 8 : 2); ?></div>
                                    <div class="asset-change <?php echo $asset['change'] >= 0 ? 'positive' : 'negative'; ?>">
                                        <?php echo ($asset['change'] >= 0 ? '+' : '') . number_format($asset['change'], 2); ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Trading Chart Implementation
        const tradingCtx = document.getElementById('tradingChart').getContext('2d');

        // Simulated trading data (replace with real API data)
        const tradingData = {
            labels: Array.from({
                length: 50
            }, (_, i) => i),
            datasets: [{
                label: 'BTC/USDT',
                data: Array.from({
                    length: 50
                }, () => Math.random() * 1000 + 45000),
                borderColor: '#ff6b35',
                backgroundColor: 'rgba(255, 107, 53, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };

        const tradingChart = new Chart(tradingCtx, {
            type: 'line',
            data: tradingData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Timeframe controls
        document.querySelectorAll('.timeframe-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.timeframe-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // In real implementation, fetch new data for selected timeframe
                updateChartData(this.dataset.timeframe);
            });
        });

        function updateChartData(timeframe) {
            // Simulate API call to fetch new chart data
            console.log('Fetching data for timeframe:', timeframe);
            // This would be replaced with actual API call
            showToast(`Fetching data for timeframe: ${timeframe}`, 'success');
        }
        function toggleWidgeet(){
            const widget = document.querySelectorAll('.widget-tab');
            widget.forEach(btn => {
                btn.classList.toggle('active');
            })
        }

        // Simulate live data updates
        setInterval(() => {
            if (tradingChart.data.datasets[0].data.length > 100) {
                tradingChart.data.labels.shift();
                tradingChart.data.datasets[0].data.shift();
            }

            const lastValue = tradingChart.data.datasets[0].data[tradingChart.data.datasets[0].data.length - 1];
            const newValue = lastValue + (Math.random() - 0.5) * 100;

            tradingChart.data.labels.push(tradingChart.data.labels.length);
            tradingChart.data.datasets[0].data.push(newValue);

            tradingChart.update('quiet');
        }, 5000);
    </script>
</body>

</html>