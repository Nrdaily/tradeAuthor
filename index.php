<?php include 'header.php'; ?>
<title>Trade Author | Advanced Crypto & Stock Trading Platform</title>
<style>
    /* Main Content */
    main {
        padding-top: 80px;
    }
    .section,
    #features {
      padding: 60px;
    }
    /* Updated Hero Section */
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(10, 10, 10, 0.9) 0%, rgba(26, 26, 26, 0.8) 100%);
      padding: 60px;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(255, 107, 53, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255, 107, 53, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(255, 107, 53, 0.08) 0%, transparent 50%);
        z-index: 1;
    }

    .hero .container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .hero-content {
        max-width: 100%;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        line-height: 1.1;
        background: linear-gradient(135deg, #fff 0%, var(--primary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        color: var(--text-secondary);
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        margin-bottom: 50px;
    }

    .hero-stats {
        display: flex;
        gap: 40px;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary);
        font-family: 'Roboto Mono', monospace;
        line-height: 1;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-top: 5px;
    }

    /* Trading Widget */
    .hero-visual {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid var(--border);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(20px);
    }

    .trading-widget {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .widget-title {
        font-size: 1.3rem;
        font-weight: 700;
    }

    .widget-refresh {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        cursor: pointer;
        transition: all 0.3s;
    }

    .widget-refresh:hover {
        background: rgba(255, 107, 53, 0.2);
        transform: rotate(180deg);
    }

    .widget-refresh.loading {
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

    .markets-ticker {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 25px;
        max-height: 200px;
        overflow-y: auto;
    }

    .ticker-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.3s;
        cursor: pointer;
    }

    .ticker-item:hover {
        border-color: rgba(255, 107, 53, 0.3);
        background: rgba(255, 107, 53, 0.05);
        transform: translateX(5px);
    }

    .ticker-item.active {
        border-color: var(--primary);
        background: rgba(255, 107, 53, 0.1);
    }

    .ticker-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ticker-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 16px;
    }

    .ticker-name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .ticker-symbol {
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .ticker-price {
        font-weight: 700;
        font-family: 'Roboto Mono', monospace;
        font-size: 0.95rem;
    }

    .ticker-change {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .ticker-change.positive {
        color: var(--success);
    }

    .ticker-change.negative {
        color: var(--danger);
    }

    /* Trading Preview */
    .trading-preview {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 15px;
        padding: 20px;
        border: 1px solid var(--border);
    }

    .preview-chart {
        height: 120px;
        margin-bottom: 15px;
    }

    #miniChart {
        width: 100%;
        height: 100%;
    }

    .preview-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .preview-price {
        font-size: 1.4rem;
        font-weight: 700;
        font-family: 'Roboto Mono', monospace;
    }

    .preview-change {
        font-size: 1rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        background: var(--success);
        color: white;
    }

    .preview-change.negative {
        background: var(--danger);
    }

    /* Responsive Design */
    @media (max-width: 968px) {
        .hero .container {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .hero-title {
            font-size: 2.8rem;
        }

        .hero-visual {
            order: -1;
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.2rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .hero-stats {
            flex-direction: column;
            gap: 20px;
        }

        .stat-value {
            font-size: 2rem;
        }
    }

    /* Enhanced market cards */
    .market-card {
        position: relative;
        overflow: hidden;
    }

    .market-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 107, 53, 0.1), transparent);
        transition: left 0.5s;
    }

    .market-card:hover::before {
        left: 100%;
    }

    /* Updated hero styles for better alignment */
    .hero-content {
        max-width: 600px;
        position: relative;
        z-index: 2;
        margin-right: 50%;
    }

    @media (max-width: 968px) {
        .hero-content {
            margin-right: 0;
            max-width: 100%;
        }

        .hero-visual {
            position: relative;
            width: 100%;
            height: 400px;
            margin-top: 40px;
            transform: none;
        }
    }

    /* Features Section */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .feature-card {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        transition: transform 0.3s;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 107, 53, 0.3);
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: var(--accent);
    }

    .feature-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .feature-description {
        color: var(--text-muted);
    }

    /* Markets Section */
    .markets {
        background: var(--secondary);
    }

    .markets-tabs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .tab {
        padding: 12px 24px;
        border-radius: 25px;
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border);
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .tab.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    }

    .markets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .market-card {
        background: var(--card-bg);
        border-radius: 15px;
        padding: 20px;
        transition: transform 0.3s;
        border: 1px solid var(--border);
        backdrop-filter: blur(10px);
    }

    .market-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border-color: rgba(255, 107, 53, 0.3);
    }

    .market-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .market-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 107, 53, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 20px;
        color: var(--accent);
    }

    .market-name {
        font-weight: 600;
    }

    .market-symbol {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .market-price {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        font-family: 'Roboto Mono', monospace;
    }

    .market-change {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
    }

    .price-up {
        color: var(--success);
    }

    .price-down {
        color: var(--danger);
    }


    /* Responsive Styles */
    @media (max-width: 968px) {
        .hero-title {
            font-size: 2.8rem;
        }

        .hero-visual {
            position: relative;
            width: 100%;
            height: 300px;
            margin-top: 40px;
            transform: none;
        }

        .hero {
            padding: 30px;
            min-height: auto;
        }

        .hero-content {
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.2rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .cta-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: 1.8rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .hero-stats {
            flex-direction: column;
            gap: 15px;
        }

        .feature-card {
            padding: 20px;
        }
    }
</style>

<main>
    <!-- Updated Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Advanced <span>Trading</span> Platform</h1>
                <p class="hero-subtitle">Trade cryptocurrencies with professional tools, real-time data, and enterprise security.</p>

                <div class="hero-buttons">
                    <a href="login" class="btn btn-primary">Get Started</a>
                    <a href="#features" class="btn btn-outline">Learn More</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-value" id="activeTraders">500K+</div>
                        <div class="stat-label">Active Traders</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="dailyVolume">$4.2B+</div>
                        <div class="stat-label">Daily Volume</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="countries">120+</div>
                        <div class="stat-label">Countries</div>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="trading-widget">
                    <div class="widget-header">
                        <div class="widget-title">Live Markets</div>
                        <div class="widget-refresh" id="refreshMarkets">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                    </div>
                    <div class="markets-ticker" id="marketsTicker">
                        <!-- Real-time data will be populated here -->
                    </div>
                    <div class="trading-preview">
                        <div class="preview-chart" id="previewChart">
                            <canvas id="miniChart"></canvas>
                        </div>
                        <div class="preview-info">
                            <div class="preview-price" id="previewPrice">$29,156.34</div>
                            <div class="preview-change" id="previewChange">+1.8%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose Trade Author?</h2>
            <p class="section-subtitle">We provide everything you need for successful trading in both cryptocurrency and traditional markets.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure Trading</h3>
                    <p class="feature-description">Bank-level security with multi-factor authentication and cold storage for your assets.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Advanced Charts</h3>
                    <p class="feature-description">Professional trading tools with real-time market data and technical indicators.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">Fast Execution</h3>
                    <p class="feature-description">High-speed trade execution with low latency and minimal slippage.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3 class="feature-title">Multi-Asset Support</h3>
                    <p class="feature-description">Trade thousands of cryptocurrencies and stocks all in one platform.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile App</h3>
                    <p class="feature-description">Trade on the go with our iOS and Android mobile applications.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">Our support team is available around the clock to assist you.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Markets Section -->
    <section class="section markets">
        <div class="container">
            <h2 class="section-title">Popular Cryptocurrencies</h2>
            <p class="section-subtitle">Trade the most popular cryptocurrencies with competitive fees and real-time data.</p>

            <!-- <div class="markets-tabs">
                <button class="tab active" data-category="all">All</button>
                <button class="tab" data-category="majors">Majors</button>
                <button class="tab" data-category="defi">DeFi</button>
                <button class="tab" data-category="metaverse">Metaverse</button>
            </div> -->

            <div class="markets-grid" id="cryptoGrid">
                <!-- Real-time data will be populated here -->
            </div>
            <br>
            <div class="section-footer">
                <a href="markets" class="btn btn-outline">View All Markets</a>
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="section cta">
        <div class="container">
            <h2 class="cta-title">Ready to Start Trading?</h2>
            <p class="cta-subtitle">Join thousands of traders who have already chosen Trade Author for their trading journey.</p>

            <div class="cta-buttons">
                <a href="login" class="btn btn-primary">Create Free Account</a>
                <a href="contact" class="btn btn-outline">Schedule a Demo</a>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<?php include_once "footer.php" ?>