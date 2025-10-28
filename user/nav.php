<?php
// nav.php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit;
}
?>

<!-- Professional Trading Header -->
<header class="trading-header">
    <div class="header-left">
        <div class="logo">
            <img src="../assets/icons/favicon.png" width="32" alt="Trade Author">
            <span>TradeAuthor</span>
            <!-- <div class="logo-badge">PRO</div> -->
        </div>

        <nav class="trading-nav">
            <a href="index" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" data-page="dashboard">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="buy" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'buy.php') ? 'active' : ''; ?>" data-page="buy">
                <i class="fas fa-exchange-alt"></i>
                <span>Trade</span>
            </a>
            <a href="portfolio" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'portfolio.php') ? 'active' : ''; ?>" data-page="portfolio">
                <i class="fas fa-wallet"></i>
                <span>Portfolio</span>
            </a>
            <a href="transactions" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'active' : ''; ?>" data-page="transactions">
                <i class="fas fa-history"></i>
                <span>History</span>
            </a>
            <a href="payment-requests" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'payment-requests.php') ? 'active' : ''; ?>" data-page="requests">
                <i class="fas fa-paper-plane"></i>
                <span>Transfers</span>
            </a>
        </nav>
    </div>

    <div class="header-right">
        <div class="trading-actions">
            <div class="language-selector">
                <button class="action-btn" id="language-toggle">
                    <i class="fas fa-globe"></i>
                </button>
                <div class="language-dropdown" id="language-dropdown">
                    <?php
                    require_once '../app/config/language.php';
                    $current_language = getCurrentLanguage();
                    foreach ($available_languages as $code => $language):
                    ?>
                        <div class="language-option <?php echo $current_language === $code ? 'active' : ''; ?>"
                            data-lang="<?php echo $code; ?>">
                            <img src="<?php echo $language['flag']; ?>"
                                alt="<?php echo $language['name']; ?>"
                                class="language-flag">
                            <span><?php echo $language['name']; ?></span>
                            <?php if ($current_language === $code): ?>
                                <i class="fas fa-check" style="margin-left: auto; color: var(--primary);"></i>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="action-btn" id="theme-toggle">
                <i class="fas fa-moon"></i>
            </button>

            <!-- <button class="action-btn" id="notifications-toggle">
                <i class="fas fa-bell"></i>
                <div class="notification-badge">3</div>
            </button> -->
        </div>

        <div class="user-profile" id="user-profile">
            <div class="avatar">
                <img src="../assets/icons/user.jpg" alt="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" width="36" style="border-radius: 50%;">
            </div>
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <i class="fas fa-chevron-down"></i>

            <div class="user-dropdown" id="user-dropdown">
                <a href="profile">
                    <i class="fas fa-user"></i>
                    <span data-i18n="profile">Profile</span>
                </a>
                <a href="settings">
                    <i class="fas fa-cog"></i>
                    <span data-i18n="settings">Settings</span>
                </a>
                <a href="change-password">
                    <i class="fas fa-key"></i>
                    <span data-i18n="security">Security</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="mailto:support@tradeauthor.com">
                    <i class="fas fa-headset"></i>
                    <span data-i18n="support">Support</span>
                </a>
                <a href="../logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span data-i18n="logout">Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Market Data Ticker -->
<div class="market-ticker">
    <div class="ticker-container" id="market-ticker">
        <!-- Ticker items will be populated by JavaScript -->
    </div>
</div>

<!-- Mobile Navigation -->
<nav class="mobile-nav">
    <div class="mobile-nav-items">
        <a href="index" class="mobile-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" data-page="dashboard">
            <i class="fas fa-chart-line"></i>
            <span data-i18n="dashboard">Dashboard</span>
        </a>
        <a href="buy" class="mobile-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'buy.php') ? 'active' : ''; ?>" data-page="trade">
            <i class="fas fa-exchange-alt"></i>
            <span data-i18n="trade">Trade</span>
        </a>
        <a href="portfolio" class="mobile-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'portfolio.php') ? 'active' : ''; ?>" data-page="portfolio">
            <i class="fas fa-wallet"></i>
            <span data-i18n="portfolio">Portfolio</span>
        </a>
        <a href="transactions" class="mobile-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'active' : ''; ?>" data-page="history">
            <i class="fas fa-history"></i>
            <span data-i18n="history">History</span>
        </a>
        <a href="payment-requests" class="mobile-nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'payment-requests.php') ? 'active' : ''; ?>" data-page="more">
            <i class="fas fa-ellipsis-h"></i>
            <span data-i18n="more">More</span>
        </a>
    </div>
</nav>

<script src="../assets/js/settings.js"></script>