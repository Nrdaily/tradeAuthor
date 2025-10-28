<div class="user-actions mobile">
    <div class="theme-toggle" id="theme-toggle">
        <i class="fas fa-moon"></i>
    </div>
    <div class="user-profile" id="user-profile">
        <div class="avatar"><img src="../assets/icons/user.jpg" alt="" width="40" style="border-radius: 50%;"></div>
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <i class="fas fa-chevron-down"></i>
        <div class="user-dropdown" id="user-dropdown">
            <a href="profile"><i class="fas fa-user"></i> Profile</a>
            <a href="change-password"><i class="fas fa-key"></i> Change Password</a>
            <a href="mailto:support@tradeauthor.com">
                <i class="fas fa-question-circle"></i>
                <span>Support</span>
            </a>
            <a href="../logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>