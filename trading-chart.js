// trading-chart.js
class TradingChart {
    constructor() {
        this.chart = null;
        this.currentPrice = 29156.34;
        this.isUpdating = false;
        this.init();
    }

    init() {
        const ctx = document.getElementById('priceChart').getContext('2d');
        
        // Generate initial price data
        const data = this.generatePriceData(50, this.currentPrice, 200);
        
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'BTC/USD',
                    data: data.prices,
                    borderColor: '#ff6b35',
                    backgroundColor: 'rgba(255, 107, 53, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#ff6b35',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        display: false,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        display: false,
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                }
            }
        });

        this.startLiveUpdates();
        this.setupTimeframeControls();
    }

    generatePriceData(count, basePrice, volatility) {
        const prices = [basePrice];
        const labels = [];
        
        for (let i = 0; i < count; i++) {
            const change = (Math.random() - 0.5) * volatility;
            const newPrice = Math.max(prices[i] + change, basePrice * 0.9);
            prices.push(newPrice);
            labels.push('');
        }
        
        return { prices, labels };
    }

    startLiveUpdates() {
        setInterval(() => {
            if (!this.isUpdating) {
                this.updateChart();
            }
        }, 2000);
    }

    updateChart() {
        this.isUpdating = true;
        
        const lastPrice = this.chart.data.datasets[0].data[this.chart.data.datasets[0].data.length - 1];
        const change = (Math.random() - 0.48) * 100; // Slight bullish bias
        const newPrice = Math.max(lastPrice + change, this.currentPrice * 0.9);
        
        // Update chart data
        this.chart.data.labels.push('');
        this.chart.data.datasets[0].data.push(newPrice);
        
        // Remove first point to keep fixed length
        if (this.chart.data.datasets[0].data.length > 50) {
            this.chart.data.labels.shift();
            this.chart.data.datasets[0].data.shift();
        }
        
        this.chart.update('quiet');
        
        // Update price display with animation
        this.updatePriceDisplay(newPrice, lastPrice);
        
        this.isUpdating = false;
    }

    updatePriceDisplay(newPrice, oldPrice) {
        const priceElement = document.querySelector('.current-price');
        const changeElement = document.querySelector('.price-change');
        
        const change = ((newPrice - oldPrice) / oldPrice) * 100;
        const isPositive = change >= 0;
        
        priceElement.textContent = `$${newPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        priceElement.classList.add('price-update');
        
        changeElement.textContent = `${isPositive ? '+' : ''}${change.toFixed(2)}%`;
        changeElement.className = `price-change ${isPositive ? 'positive' : 'negative'}`;
        
        setTimeout(() => {
            priceElement.classList.remove('price-update');
        }, 600);
    }

    setupTimeframeControls() {
        const timeframes = document.querySelectorAll('.timeframe');
        timeframes.forEach(tf => {
            tf.addEventListener('click', () => {
                timeframes.forEach(t => t.classList.remove('active'));
                tf.classList.add('active');
                this.changeTimeframe(tf.textContent);
            });
        });
    }

    changeTimeframe(timeframe) {
        // Simulate different volatility based on timeframe
        let volatility;
        switch(timeframe) {
            case '1H': volatility = 50; break;
            case '4H': volatility = 100; break;
            case '1D': volatility = 200; break;
            default: volatility = 100;
        }
        
        const newData = this.generatePriceData(50, this.currentPrice, volatility);
        this.chart.data.datasets[0].data = newData.prices;
        this.chart.update();
    }
}

// Initialize chart when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new TradingChart();
});