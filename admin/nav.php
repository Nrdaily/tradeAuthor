    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/icons/logo.png" alt="">
            <!-- <div class="logo-text">Crypto<span>Base</span></div> -->
        </div>

        <div class="nav-menu">
            <a href="index" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index') ? 'active' : ''; ?> active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="users" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'users') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="cryptocurrencies" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'cryptocurrencies') ? 'active' : ''; ?>">
                <i class="fas fa-coins"></i>
                <span>Cryptocurrencies</span>
            </a>
            <a href="transactions" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'transactions') ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Transactions</span>
            </a>
            <a href="create_admin" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'create_admin') ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i>
                <span>Create Admin</span>
            </a>
            <a href="run_snapshot" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'run_snapshot') ? 'active' : ''; ?>">
                <i class="fas fa-sync-alt"></i>
                <span>Run Snapshots</span>
            </a>
            <a href="snapshot_logs" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'snapshot_logs') ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Snapshot Logs</span>
            </a>
            <a href="payment_cards" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'payment_cards') ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i>
                <span>Payment Cards</span>
            </a>
            <a href="add_funds" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'add_funds') ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i>
                <span>Add Funds</span>
            </a>
            <a href="logout" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>