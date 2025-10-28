// realtime-markets.js
class RealTimeMarkets {
    constructor() {
        this.cryptoData = {};
        this.selectedCrypto = 'bitcoin';
        this.init();
    }

    async init() {
        await this.loadAllMarkets();
        this.setupEventListeners();
        this.startAutoRefresh();
        this.startChartAnimation();
    }

    async loadAllMarkets() {
        const cryptos = ['bitcoin', 'ethereum', 'binancecoin', 'ripple', 'cardano', 'solana', 'dogecoin'];
        
        try {
            const response = await fetch(
                `https://api.coingecko.com/api/v3/simple/price?ids=${cryptos.join(',')}&vs_currencies=usd&include_24hr_change=true`
            );
            this.cryptoData = await response.json();
            this.updateTicker();
            this.updateMarketGrid();
        } catch (error) {
            console.error('Error loading market data:', error);
            this.useFallbackData();
        }
    }

    useFallbackData() {
        // Fallback data
        this.cryptoData = {
            bitcoin: { usd: 29156.34, usd_24h_change: 1.8 },
            ethereum: { usd: 1832.45, usd_24h_change: -0.4 },
            binancecoin: { usd: 234.56, usd_24h_change: 0.7 },
            ripple: { usd: 0.5234, usd_24h_change: -1.2 },
            cardano: { usd: 0.2456, usd_24h_change: 2.1 },
            solana: { usd: 23.45, usd_24h_change: 3.4 },
            dogecoin: { usd: 0.0789, usd_24h_change: -0.8 }
        };
        this.updateTicker();
        this.updateMarketGrid();
    }

    updateTicker() {
        const ticker = document.getElementById('marketsTicker');
        if (!ticker) return;

        const cryptos = [
            { id: 'bitcoin', name: 'Bitcoin', symbol: 'BTC' },
            { id: 'ethereum', name: 'Ethereum', symbol: 'ETH' },
            { id: 'binancecoin', name: 'BNB', symbol: 'BNB' },
            { id: 'ripple', name: 'XRP', symbol: 'XRP' },
            { id: 'cardano', name: 'Cardano', symbol: 'ADA' },
            { id: 'solana', name: 'Solana', symbol: 'SOL' }
        ];

        ticker.innerHTML = cryptos.map(crypto => {
            const data = this.cryptoData[crypto.id];
            if (!data) return '';
            
            const change = data.usd_24h_change;
            const isPositive = change >= 0;
            const isActive = crypto.id === this.selectedCrypto;
            
            return `
                <div class="ticker-item ${isActive ? 'active' : ''}" data-crypto="${crypto.id}">
                    <div class="ticker-info">
                        <div class="ticker-icon">
                            <i class="fab fa-${crypto.id === 'bitcoin' ? 'bitcoin' : crypto.id === 'ethereum' ? 'ethereum' : 'coins'}"></i>
                        </div>
                        <div>
                            <div class="ticker-name">${crypto.name}</div>
                            <div class="ticker-symbol">${crypto.symbol}</div>
                        </div>
                    </div>
                    <div class="ticker-price">$${data.usd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 6 })}</div>
                    <div class="ticker-change ${isPositive ? 'positive' : 'negative'}">
                        <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
                        <span>${isPositive ? '+' : ''}${change?.toFixed(2) || '0.00'}%</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    updateMarketGrid() {
        const grid = document.getElementById('cryptoGrid');
        if (!grid) return;

        const cryptos = [
            { id: 'bitcoin', name: 'Bitcoin', symbol: 'BTC' },
            { id: 'ethereum', name: 'Ethereum', symbol: 'ETH' },
            { id: 'binancecoin', name: 'BNB', symbol: 'BNB' },
            { id: 'solana', name: 'Solana', symbol: 'SOL' },
            { id: 'ripple', name: 'XRP', symbol: 'XRP' },
            { id: 'cardano', name: 'Cardano', symbol: 'ADA' }
        ];

        grid.innerHTML = cryptos.map(crypto => {
            const data = this.cryptoData[crypto.id];
            if (!data) return '';
            
            const change = data.usd_24h_change;
            const isPositive = change >= 0;
            
            return `
                <div class="market-card">
                    <div class="market-header">
                        <div class="market-icon">
                            <i class="fab fa-${crypto.id === 'bitcoin' ? 'bitcoin' : crypto.id === 'ethereum' ? 'ethereum' : 'coins'}"></i>
                        </div>
                        <div>
                            <div class="market-name">${crypto.name}</div>
                            <div class="market-symbol">${crypto.symbol}/USD</div>
                        </div>
                    </div>
                    <div class="market-price">$${data.usd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 6 })}</div>
                    <div class="market-change ${isPositive ? 'price-up' : 'price-down'}">
                        <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
                        <span>${isPositive ? '+' : ''}${change?.toFixed(2) || '0.00'}%</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    setupEventListeners() {
        // Ticker item clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.ticker-item')) {
                const cryptoId = e.target.closest('.ticker-item').dataset.crypto;
                this.selectCrypto(cryptoId);
            }
        });

        // Refresh button
        const refreshBtn = document.getElementById('refreshMarkets');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshData();
            });
        }

        // Market tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                // Filter logic would go here
            });
        });
    }

    selectCrypto(cryptoId) {
        this.selectedCrypto = cryptoId;
        this.updateTicker();
        this.updatePreview();
    }

    updatePreview() {
        const data = this.cryptoData[this.selectedCrypto];
        if (!data) return;

        const priceElement = document.getElementById('previewPrice');
        const changeElement = document.getElementById('previewChange');
        
        if (priceElement) {
            priceElement.textContent = `$${data.usd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 6 })}`;
        }
        
        if (changeElement) {
            const change = data.usd_24h_change;
            const isPositive = change >= 0;
            changeElement.textContent = `${isPositive ? '+' : ''}${change?.toFixed(2) || '0.00'}%`;
            changeElement.className = `preview-change ${isPositive ? '' : 'negative'}`;
        }
    }

    async refreshData() {
        const refreshBtn = document.getElementById('refreshMarkets');
        if (refreshBtn) {
            refreshBtn.classList.add('loading');
        }
        
        await this.loadAllMarkets();
        
        if (refreshBtn) {
            setTimeout(() => {
                refreshBtn.classList.remove('loading');
            }, 1000);
        }
    }

    startAutoRefresh() {
        setInterval(() => {
            this.loadAllMarkets();
        }, 30000); // Refresh every 30 seconds
    }

    startChartAnimation() {
        const canvas = document.getElementById('miniChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const width = canvas.width = canvas.offsetWidth;
        const height = canvas.height = canvas.offsetHeight;

        // Simple animated line chart
        let points = Array.from({ length: 50 }, (_, i) => ({
            x: (i / 49) * width,
            y: height / 2 + Math.sin(i * 0.3) * 20
        }));

        function animate() {
            ctx.clearRect(0, 0, width, height);
            
            // Draw gradient line
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            
            points.forEach((point, i) => {
                if (i > 0) {
                    ctx.lineTo(point.x, point.y);
                }
            });
            
            const gradient = ctx.createLinearGradient(0, 0, width, 0);
            gradient.addColorStop(0, 'rgba(255, 107, 53, 0.6)');
            gradient.addColorStop(1, 'rgba(255, 107, 53, 0.2)');
            
            ctx.strokeStyle = gradient;
            ctx.lineWidth = 3;
            ctx.stroke();

            // Animate points
            points = points.map((point, i) => ({
                x: point.x,
                y: height / 2 + Math.sin(i * 0.3 + Date.now() * 0.001) * 20
            }));

            requestAnimationFrame(animate);
        }

        animate();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new RealTimeMarkets();
});