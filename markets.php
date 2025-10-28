<?php include 'header.php'; ?>
<!-- Markets specific styles -->
<style>
  .markets-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
  }

  .markets-header {
    margin-bottom: 40px;
    text-align: center;
  }

  .markets-title {
    font-size: 2.5rem;
    margin-bottom: 15px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .markets-description {
    color: var(--text-secondary);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
  }

  .markets-tabs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 40px;
    flex-wrap: wrap;
  }

  .market-tab {
    padding: 12px 24px;
    border-radius: 25px;
    background: transparent;
    color: var(--text-secondary);
    border: 1px solid var(--border);
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
  }

  .market-tab.active {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
    border-color: var(--primary);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
  }

  .markets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
  }

  .market-card {
    background: var(--card-bg);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s;
    border: 1px solid var(--border);
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

  .market-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 107, 53, 0.3);
  }

  .market-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }

  .market-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 107, 53, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 22px;
    color: var(--primary);
  }

  .market-name {
    font-weight: 600;
    font-size: 1.2rem;
  }

  .market-symbol {
    color: var(--text-secondary);
    font-size: 0.9rem;
    text-transform: uppercase;
  }

  .market-price {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 8px;
    font-family: 'Roboto Mono', monospace;
  }

  .market-change {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 1rem;
  }

  .price-up {
    color: var(--success);
  }

  .price-down {
    color: var(--danger);
  }

  .market-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
  }

  .stat {
    text-align: center;
  }

  .stat-value {
    font-weight: 600;
    font-size: 0.95rem;
    font-family: 'Roboto Mono', monospace;
  }

  .stat-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-top: 4px;
  }

  .loading-spinner {
    text-align: center;
    padding: 40px;
    color: var(--text-secondary);
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255, 107, 53, 0.2);
    border-top: 4px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  @media (max-width: 768px) {
    .markets-grid {
      grid-template-columns: 1fr;
    }
    
    .markets-title {
      font-size: 2rem;
    }
  }
</style>

<main>
  <div class="markets-container">
    <div class="markets-header">
      <h1 class="markets-title">Live Crypto Markets</h1>
      <p class="markets-description">Real-time cryptocurrency prices, market caps, and trading volumes</p>
    </div>
    
    <div class="markets-tabs">
      <button class="market-tab active" data-category="all">All Cryptos</button>
      <button class="market-tab" data-category="top">Top 50</button>
      <button class="market-tab" data-category="defi">DeFi</button>
      <button class="market-tab" data-category="layer1">Layer 1</button>
      <button class="market-tab" data-category="meme">Meme</button>
    </div>
    
    <div class="loading-spinner" id="loadingSpinner">
      <div class="spinner"></div>
      <div>Loading market data...</div>
    </div>
    
    <div class="markets-grid" id="marketsGrid">
      <!-- Market data will be populated by JavaScript -->
    </div>
  </div>
</main>

<script>
// Crypto data with categories
const cryptoCategories = {
  'all': ['bitcoin', 'ethereum', 'binancecoin', 'ripple', 'cardano', 'solana', 'polkadot', 'dogecoin', 'shiba-inu', 'avalanche-2', 'chainlink', 'litecoin', 'uniswap', 'matic-network', 'stellar', 'cosmos', 'monero', 'ethereum-classic', 'bitcoin-cash', 'filecoin', 'tron', 'eos', 'aave', 'compound-governance-token', 'maker', 'theta-token', 'tezos', 'algorand', 'dash', 'zcash'],
  'top': ['bitcoin', 'ethereum', 'binancecoin', 'ripple', 'cardano', 'solana', 'polkadot', 'dogecoin', 'shiba-inu', 'avalanche-2'],
  'defi': ['uniswap', 'aave', 'compound-governance-token', 'maker', 'sushi', 'curve-dao-token', 'yearn-finance', 'balancer', 'synthetix-network-token'],
  'layer1': ['ethereum', 'solana', 'cardano', 'polkadot', 'avalanche-2', 'cosmos', 'algorand', 'tezos', 'near', 'flow'],
  'meme': ['dogecoin', 'shiba-inu', 'dogelon-mars', 'shiba-prediction', 'floki']
};

// Icon mapping
const cryptoIcons = {
  'bitcoin': 'fab fa-bitcoin',
  'ethereum': 'fab fa-ethereum',
  'binancecoin': 'fas fa-coins',
  'ripple': 'fas fa-bolt',
  'cardano': 'fas fa-shield-alt',
  'solana': 'fas fa-sun',
  'polkadot': 'fas fa-circle',
  'dogecoin': 'fas fa-dog',
  'shiba-inu': 'fas fa-paw',
  'avalanche-2': 'fas fa-mountain',
  'chainlink': 'fas fa-link',
  'litecoin': 'fab fa-bitcoin',
  'uniswap': 'fas fa-exchange-alt',
  'matic-network': 'fas fa-layer-group',
  'stellar': 'fas fa-star',
  'cosmos': 'fas fa-atom',
  'monero': 'fas fa-user-secret',
  'ethereum-classic': 'fab fa-ethereum',
  'bitcoin-cash': 'fab fa-btc',
  'default': 'fas fa-coins'
};

class MarketsPage {
  constructor() {
    this.currentCategory = 'all';
    this.marketData = {};
    this.init();
  }

  async init() {
    this.setupEventListeners();
    await this.loadMarketData();
    this.startAutoRefresh();
  }

  setupEventListeners() {
    // Tab switching
    document.querySelectorAll('.market-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.market-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        this.currentCategory = tab.dataset.category;
        this.renderMarkets();
      });
    });
  }

  async loadMarketData() {
    try {
      const symbols = cryptoCategories[this.currentCategory].join(',');
      const response = await fetch(
        `https://api.coingecko.com/api/v3/simple/price?ids=${symbols}&vs_currencies=usd&include_24hr_change=true&include_market_cap=true&include_24hr_vol=true`
      );
      
      if (!response.ok) throw new Error('API request failed');
      
      const data = await response.json();
      this.marketData = data;
      this.renderMarkets();
      
      // Hide loading spinner
      document.getElementById('loadingSpinner').style.display = 'none';
    } catch (error) {
      console.error('Error loading market data:', error);
      this.showError('Failed to load market data. Please try again.');
    }
  }

  renderMarkets() {
    const grid = document.getElementById('marketsGrid');
    const cryptos = cryptoCategories[this.currentCategory];
    
    grid.innerHTML = '';
    
    cryptos.forEach(cryptoId => {
      const data = this.marketData[cryptoId];
      if (!data) return;
      
      const change = data.usd_24h_change;
      const isPositive = change >= 0;
      const icon = cryptoIcons[cryptoId] || cryptoIcons.default;
      const name = this.formatCryptoName(cryptoId);
      const symbol = cryptoId.split('-')[0].toUpperCase();
      
      const marketCap = data.usd_market_cap ? this.formatMarketCap(data.usd_market_cap) : 'N/A';
      const volume = data.usd_24h_vol ? this.formatVolume(data.usd_24h_vol) : 'N/A';
      
      const card = document.createElement('div');
      card.className = 'market-card';
      card.innerHTML = `
        <div class="market-header">
          <div class="market-icon">
            <i class="${icon}"></i>
          </div>
          <div>
            <div class="market-name">${name}</div>
            <div class="market-symbol">${symbol}/USD</div>
          </div>
        </div>
        <div class="market-price">$${data.usd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 6 })}</div>
        <div class="market-change ${isPositive ? 'price-up' : 'price-down'}">
          <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
          <span>${isPositive ? '+' : ''}${change?.toFixed(2) || '0.00'}%</span>
        </div>
        <div class="market-stats">
          <div class="stat">
            <div class="stat-value">${marketCap}</div>
            <div class="stat-label">Market Cap</div>
          </div>
          <div class="stat">
            <div class="stat-value">${volume}</div>
            <div class="stat-label">Volume (24h)</div>
          </div>
        </div>
      `;
      
      grid.appendChild(card);
    });
  }

  formatCryptoName(id) {
    return id.split('-')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ')
      .replace('2', '')
      .trim();
  }

  formatMarketCap(cap) {
    if (cap >= 1e12) return `$${(cap / 1e12).toFixed(2)}T`;
    if (cap >= 1e9) return `$${(cap / 1e9).toFixed(2)}B`;
    if (cap >= 1e6) return `$${(cap / 1e6).toFixed(2)}M`;
    return `$${cap.toLocaleString()}`;
  }

  formatVolume(volume) {
    if (volume >= 1e9) return `$${(volume / 1e9).toFixed(2)}B`;
    if (volume >= 1e6) return `$${(volume / 1e6).toFixed(2)}M`;
    return `$${volume.toLocaleString()}`;
  }

  showError(message) {
    const grid = document.getElementById('marketsGrid');
    grid.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--danger);">
        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px;"></i>
        <div>${message}</div>
      </div>
    `;
  }

  startAutoRefresh() {
    setInterval(() => {
      this.loadMarketData();
    }, 30000); // Refresh every 30 seconds
  }
}

// Initialize markets page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new MarketsPage();
});
</script>
<?php include 'footer.php'; ?>