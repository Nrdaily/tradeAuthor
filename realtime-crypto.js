// realtime-crypto.js
class CryptoPriceTracker {
    constructor() {
        this.cache = {};
        this.cacheTimeout = 60000; // 1 minute cache
    }

    async fetchCryptoPrices(symbols = ['bitcoin', 'ethereum']) {
        try {
            const response = await fetch(
                `https://api.coingecko.com/api/v3/simple/price?ids=${symbols.join(',')}&vs_currencies=usd&include_24hr_change=true`
            );
            const data = await response.json();
            this.cache = {
                data,
                timestamp: Date.now()
            };
            return data;
        } catch (error) {
            console.error('Error fetching crypto prices:', error);
            return this.getFallbackData();
        }
    }

    getFallbackData() {
        // Fallback data in case API fails
        return {
            bitcoin: {
                usd: 29156.34,
                usd_24h_change: 1.8
            },
            ethereum: {
                usd: 1832.45,
                usd_24h_change: -0.4
            }
        };
    }

    async getPrices() {
        if (this.cache.data && Date.now() - this.cache.timestamp < this.cacheTimeout) {
            return this.cache.data;
        }
        return await this.fetchCryptoPrices();
    }

    formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);
    }

    formatChange(change) {
        const isPositive = change >= 0;
        return {
            value: `${isPositive ? '+' : ''}${change.toFixed(2)}%`,
            class: isPositive ? 'price-up' : 'price-down',
            icon: isPositive ? 'fa-arrow-up' : 'fa-arrow-down'
        };
    }
}

// Initialize crypto tracker
const cryptoTracker = new CryptoPriceTracker();

// Update crypto prices in real-time
async function updateCryptoPrices() {
    const prices = await cryptoTracker.getPrices();
    
    // Update Bitcoin
    if (prices.bitcoin) {
        const btcChange = cryptoTracker.formatChange(prices.bitcoin.usd_24h_change);
        document.querySelectorAll('.bitcoin-price').forEach(el => {
            el.textContent = cryptoTracker.formatPrice(prices.bitcoin.usd);
        });
        document.querySelectorAll('.bitcoin-change').forEach(el => {
            el.innerHTML = `<i class="fas ${btcChange.icon}"></i><span>${btcChange.value}</span>`;
            el.className = `market-change ${btcChange.class}`;
        });
    }

    // Update Ethereum
    if (prices.ethereum) {
        const ethChange = cryptoTracker.formatChange(prices.ethereum.usd_24h_change);
        document.querySelectorAll('.ethereum-price').forEach(el => {
            el.textContent = cryptoTracker.formatPrice(prices.ethereum.usd);
        });
        document.querySelectorAll('.ethereum-change').forEach(el => {
            el.innerHTML = `<i class="fas ${ethChange.icon}"></i><span>${ethChange.value}</span>`;
            el.className = `market-change ${ethChange.class}`;
        });
    }
}

// Update prices every 30 seconds
setInterval(updateCryptoPrices, 30000);

// Initial update
updateCryptoPrices();