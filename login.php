<?php
// login_enhanced.php
session_start();
require_once 'app/config/database.php';
require_once 'app/utils/email_service.php';
require_once 'app/utils/security.php';

$database = new Database();
$db = $database->getConnection();
$security = new Security();
$emailService = new EmailService();
// $security->cleanupExpiredData($db);

$error = '';
$success = '';
$showVerificationNotice = false;

// Check for remember me token
if (!isset($_POST['login']) && !isset($_POST['register'])) {
    if (isset($_COOKIE['remember_token'])) {
        $rememberToken = $_COOKIE['remember_token'];
        $user = $security->validateRememberToken($rememberToken, $db);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['remember_device'] = true;

            // Update last login
            $security->updateLastLogin($user['id'], $db);

            header('Location: user/index.php');
            exit;
        } else {
            // Invalid token, clear cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Process login with enhanced security
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $remember = isset($_POST['remember']) ? true : false;

        // Check if account is temporarily locked
        if ($security->isAccountLocked($email, $db)) {
            $error = "Account temporarily locked due to too many failed attempts. Please try again later.";
        } elseif (!empty($email) && !empty($password)) {
            $query = "SELECT id, first_name, last_name, email, password_hash, email_verified, login_attempts, account_locked_until 
                      FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password_hash'])) {
                    // Check if email is verified
                    if (!$user['email_verified']) {
                        $showVerificationNotice = true;
                        $error = "Please verify your email address before logging in. <a href='#' onclick='resendVerification(\"$email\")'>Resend verification email</a>";
                    } else {
                        // Successful login
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

                        // Reset login attempts
                        $security->resetLoginAttempts($user['id'], $db);

                        // Update last login
                        $security->updateLastLogin($user['id'], $db);

                        // Handle remember me
                        if ($remember) {
                            $token = $security->createRememberToken($user['id'], $db);
                            if ($token) {
                                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                                $_SESSION['remember_device'] = true;
                            }
                        }

                        header('Location: user/index.php');
                        exit;
                    }
                } else {
                    // Failed login attempt
                    $security->incrementLoginAttempts($user['id'], $db);
                    $remainingAttempts = 5 - ($user['login_attempts'] + 1);

                    if ($remainingAttempts <= 0) {
                        $error = "Account temporarily locked due to too many failed attempts. Please try again in 15 minutes.";
                        $security->lockAccount($user['id'], $db);
                    } else {
                        $error = "Invalid email or password. $remainingAttempts attempts remaining.";
                    }
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Please fill in all fields.";
        }
    } elseif (isset($_POST['register'])) {
        // Enhanced registration process with email verification
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

        // Enhanced validation
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            $error = "Please fill in all required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
            $error = "Password must contain at least one uppercase letter, one number, and one special character.";
        } else {
            // Check if email already exists
            $query = "SELECT id FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Email already exists.";
            } else {
                // Create new user with email verification
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $emailVerificationToken = $security->generateToken(32);

                $query = "INSERT INTO users (first_name, last_name, email, password_hash, phone, email_verification_token, email_verification_expires, created_at) 
                          VALUES (:first_name, :last_name, :email, :password_hash, :phone, :verification_token, DATE_ADD(NOW(), INTERVAL 24 HOUR), NOW())";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':first_name', $firstName);
                $stmt->bindParam(':last_name', $lastName);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password_hash', $passwordHash);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':verification_token', $emailVerificationToken);

                if ($stmt->execute()) {
                    $userId = $db->lastInsertId();

                    // Send verification email
                    $verificationSent = $emailService->sendVerificationEmail($email, $firstName, $emailVerificationToken, $userId);

                    if ($verificationSent) {
                        $success = "Account created successfully! Please check your email to verify your account. The verification link expires in 24 hours.";
                    } else {
                        $success = "Account created successfully! However, we encountered an issue sending the verification email. Please contact support to verify your account.";
                        // You might want to log this for admin attention
                        error_log("Verification email failed for user: " . $email);
                    }

                    // Switch to login form
                    echo '<script>document.addEventListener("DOMContentLoaded", function() { showLoginForm(); });</script>';
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    } elseif (isset($_POST['forgot_password'])) {
        // Forgot password request
        $email = trim($_POST['forgot_email']);

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = $security->initiatePasswordReset($email, $db, $emailService);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        } else {
            $error = "Please enter a valid email address.";
        }
    } elseif (isset($_POST['resend_verification'])) {
        // Resend verification email
        $email = trim($_POST['resend_email']);
        $result = $security->resendVerificationEmail($email, $db, $emailService);
        if ($result['success']) {
            $success = "Verification email has been resent. Please check your inbox.";
        } else {
            $error = $result['message'];
        }
    }
}
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
        color: #f5f5f5;
        line-height: 1.6;
        overflow-x: hidden;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: rgba(26, 26, 38, 0.95);
        border-radius: 16px;
        padding: 30px;
        width: 90%;
        max-width: 400px;
        border: 1px solid rgba(255, 107, 53, 0.3);
        backdrop-filter: blur(10px);
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        color: #b0b0b0;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.3s;
    }

    .modal-close:hover {
        color: #ff6b35;
    }

    .verification-notice {
        background: rgba(255, 171, 0, 0.1);
        border: 1px solid rgba(255, 171, 0, 0.3);
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        text-align: center;
    }

    .verification-notice i {
        color: #ffab00;
        margin-right: 8px;
    }

    .security-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
        font-size: 0.8rem;
        color: #00c853;
    }

    .security-badge i {
        font-size: 1rem;
    }

    /* Trading background elements */
    .trading-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .chart-line {
        position: absolute;
        background: rgba(255, 107, 53, 0.1);
        height: 2px;
        transform-origin: left;
    }

    .candlestick {
        position: absolute;
        width: 4px;
        background: rgba(0, 200, 83, 0.3);
        border-radius: 2px;
    }

    .candlestick.red {
        background: rgba(255, 61, 0, 0.3);
    }

    .market-indicator {
        position: absolute;
        color: rgba(255, 255, 255, 0.05);
        font-size: 0.7rem;
        font-family: 'Courier New', monospace;
    }

    /* Main Content */
    main {
        margin-top: 300px;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 80px);
    }

    /* Auth Container */
    .auth-container {
        width: 100%;
        max-width: 480px;
        padding: 40px 20px;
    }

    .auth-card {
        background: rgba(26, 26, 38, 0.95);
        border-radius: 16px;
        padding: 40px;
        width: 100%;
        border: 1px solid rgba(255, 107, 53, 0.2);
        backdrop-filter: blur(10px);
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.5),
            0 0 0 1px rgba(255, 107, 53, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }

    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #ff6b35, #00c853, #2962ff, #ff6b35);
        background-size: 400% 400%;
        animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        background: linear-gradient(135deg, #ff6b35, #ff8e53);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .auth-subtitle {
        color: #b0b0b0;
        font-size: 0.95rem;
    }

    .platform-stats {
        display: flex;
        justify-content: space-around;
        margin: 20px 0;
        padding: 15px;
        background: rgba(255, 107, 53, 0.05);
        border-radius: 10px;
        border: 1px solid rgba(255, 107, 53, 0.1);
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #ff6b35;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #b0b0b0;
        margin-top: 5px;
    }

    .auth-tabs {
        display: flex;
        border-bottom: 1px solid rgba(255, 107, 53, 0.3);
        margin-bottom: 30px;
        background: rgba(255, 107, 53, 0.05);
        border-radius: 10px 10px 0 0;
        overflow: hidden;
    }

    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        color: #b0b0b0;
        position: relative;
        background: transparent;
    }

    .auth-tab.active {
        color: #ff6b35;
        background: rgba(255, 107, 53, 0.1);
    }

    .auth-tab.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #ff6b35, #ff8e53);
    }

    .auth-form {
        display: none;
    }

    .auth-form.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        color: #e0e0e0;
    }

    .input-with-icon {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 15px 15px 15px 45px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 107, 53, 0.3);
        border-radius: 10px;
        color: #f5f5f5;
        font-size: 16px;
        transition: all 0.3s;
        font-family: 'Courier New', monospace;
    }

    .form-input:focus {
        outline: none;
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.2);
        background: rgba(255, 255, 255, 0.08);
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #ff6b35;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #b0b0b0;
        cursor: pointer;
        transition: color 0.3s;
    }

    .password-toggle:hover {
        color: #ff6b35;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .checkbox-container input {
        width: 18px;
        height: 18px;
        accent-color: #ff6b35;
    }

    .forgot-password {
        color: #ff6b35;
        font-weight: 500;
        transition: all 0.3s;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    .btn-auth {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #ff6b35, #ff8e53);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
    }

    .btn-auth::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-auth:hover::before {
        left: 100%;
    }

    .trading-features {
        background: rgba(255, 107, 53, 0.1);
        border-left: 3px solid #ff6b35;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin: 25px 0;
        font-size: 0.85rem;
    }

    .trading-features i {
        color: #ff6b35;
        margin-right: 8px;
    }

    .password-strength {
        margin-top: 8px;
        font-size: 0.8rem;
        color: #b0b0b0;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .password-strength.weak {
        color: #ff3d00;
    }

    .password-strength.medium {
        color: #ffab00;
    }

    .password-strength.strong {
        color: #00c853;
    }

    .error-message {
        color: #ff3d00;
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 10px;
        background: rgba(255, 61, 0, 0.1);
        border-radius: 5px;
        border-left: 3px solid #ff3d00;
    }

    .success-message {
        color: #00c853;
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 10px;
        background: rgba(0, 200, 83, 0.1);
        border-radius: 5px;
        border-left: 3px solid #00c853;
    }

    .market-status {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(26, 26, 38, 0.95);
        padding: 10px 0;
        border-top: 1px solid rgba(255, 107, 53, 0.3);
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        z-index: 100;
    }

    .market-ticker {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .ticker-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ticker-symbol {
        font-weight: 600;
    }

    .ticker-price {
        color: #00c853;
    }

    .ticker-price.negative {
        color: #ff3d00;
    }

    .ticker-change {
        font-size: 0.7rem;
        opacity: 0.8;
    }

    /* Responsive Styles */
    @media (max-width: 968px) {
        .auth-card {
            padding: 30px 25px;
        }

        .market-ticker {
            flex-wrap: wrap;
            gap: 15px;
        }
    }

    @media (max-width: 768px) {
        .auth-container {
            padding: 20px;
        }

        .auth-title {
            font-size: 1.8rem;
        }

        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .platform-stats {
            flex-direction: column;
            gap: 10px;
        }
    }

    @media (max-width: 480px) {
        .auth-card {
            padding: 25px 20px;
        }

        .auth-tab {
            padding: 12px;
            font-size: 0.9rem;
        }
    }
</style>

<title>Login - Trade Author | Advanced Crypto & Stock Trading Platform</title>

<?php include 'header.php'; ?>

<!-- Trading Background Elements -->
<div class="trading-bg" id="tradingBg"></div>

<main>
    <div class="container auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2 class="auth-title">Trade Author</h2>
                <p class="auth-subtitle">Professional Trading Platform</p>

                <div class="platform-stats">
                    <div class="stat-item">
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">Trading</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">100+</div>
                        <div class="stat-label">Markets</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">0.1%</div>
                        <div class="stat-label">Fees</div>
                    </div>
                </div>
            </div>

            <div class="auth-tabs">
                <div class="auth-tab active" data-tab="login">Trader Login</div>
                <div class="auth-tab" data-tab="signup">Open Account</div>
            </div>

            <!-- Display PHP error messages -->
            <?php if (!empty($error)): ?>
                <div class="error-message" style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message" style="margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form class="auth-form active" id="login-form" method="post" action="">
                <input type="hidden" name="login" value="1">

                <div class="form-group">
                    <label for="login-email" class="form-label">Trader ID / Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-tie input-icon"></i>
                        <input type="email" id="login-email" class="form-input" name="email" placeholder="trader@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="login-password" class="form-label">Trading Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" id="login-password" name="password" class="form-input" placeholder="••••••••" required>
                        <i class="fas fa-eye password-toggle" id="toggle-login-password"></i>
                    </div>
                </div>

                <div class="trading-features">
                    <p><i class="fas fa-chart-line"></i> Real-time market data & Advanced charting tools</p>
                    <p><i class="fas fa-shield-alt"></i> Bank-level security & SSL encryption</p>
                    <p><i class="fas fa-bolt"></i> Instant order execution & Low latency</p>
                </div>

                <div class="form-options">
                    <div class="checkbox-container">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">
                            <i class="fas fa-shield-alt"></i> Remember this device
                            <span class="security-badge">
                                <i class="fas fa-lock"></i> Secured with token-based authentication
                            </span>
                        </label>
                    </div>
                    <a href="#" class="forgot-password" onclick="showForgotPasswordModal(); return false;">Forgot password?</a>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-sign-in-alt"></i> Access Trading Desk
                </button>

                <div class="auth-footer">
                    New to trading? <a href="#" id="switch-to-signup">Open live account</a>
                </div>
            </form>

            <!-- Signup Form -->
            <form class="auth-form" id="signup-form" method="post" action="">
                <input type="hidden" name="register" value="1">

                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="text" id="first_name" name="first_name" class="form-input" placeholder="John" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Smith" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signup-email" class="form-label">Professional Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="signup-email" name="email" class="form-input" placeholder="trader@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Mobile Number</label>
                    <div class="input-with-icon">
                        <i class="fas fa-mobile-alt input-icon"></i>
                        <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567">
                    </div>
                </div>

                <div class="form-group">
                    <label for="signup-password" class="form-label">Secure Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="signup-password" class="form-input" placeholder="••••••••" required>
                        <i class="fas fa-eye password-toggle" id="toggle-signup-password"></i>
                    </div>
                    <div id="password-strength" class="password-strength">
                        <i class="fas fa-info-circle"></i> Minimum 8 characters with uppercase, number, and symbol
                    </div>
                </div>

                <div class="form-group">
                    <label for="signup-confirm" class="form-label">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="confirm_password" id="signup-confirm" class="form-input" placeholder="••••••••" required>
                    </div>
                    <div id="password-match" class="error-message" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i> Trading passwords do not match
                    </div>
                </div>

                <div class="trading-features">
                    <p><i class="fas fa-rocket"></i> Get started with $10,000 demo account</p>
                    <p><i class="fas fa-graduation-cap"></i> Free trading education & Webinars</p>
                    <p><i class="fas fa-headset"></i> 24/7 dedicated support team</p>
                </div>

                <div class="form-group">
                    <div class="checkbox-container">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="terms.html" style="color: #ff6b35;">Trading Agreement</a> and <a href="privacy.html" style="color: #ff6b35;">Risk Disclosure</a></label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-container">
                        <input type="checkbox" id="newsletter" name="newsletter" checked>
                        <label for="newsletter">Receive market analysis & trading signals</label>
                    </div>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-chart-bar"></i> Start Trading
                </button>

                <div class="auth-footer">
                    Already have an account? <a href="#" id="switch-to-login">Trader login</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Add this modal for forgot password -->
    <div id="forgotPasswordModal" class="modal" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeForgotPasswordModal()">&times;</button>
            <div class="modal-header">
                <h2>Reset Your Password</h2>
                <p>Enter your email address and we'll send you instructions to reset your password.</p>
            </div>
            <form method="POST" id="forgot-password-form">
                <input type="hidden" name="forgot_password" value="1">
                <div class="form-group">
                    <label for="forgot_email" class="form-label">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="forgot_email" name="forgot_email" class="form-input" placeholder="your.email@example.com" required>
                    </div>
                </div>
                <button type="submit" class="btn-auth">
                    <i class="fas fa-paper-plane"></i> Send Reset Instructions
                </button>
            </form>
            <div class="auth-footer">
                Remember your password? <a href="#" onclick="closeForgotPasswordModal(); showLoginForm();">Back to login</a>
            </div>
        </div>
    </div>

    <!-- Add this modal for verification resend -->
    <div id="resendVerificationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" onclick="closeResendVerificationModal()">&times;</button>
            <div class="modal-header">
                <h2>Resend Verification Email</h2>
                <p>Enter your email address to receive a new verification link.</p>
            </div>
            <form method="POST" id="resend-verification-form">
                <input type="hidden" name="resend_verification" value="1">
                <div class="form-group">
                    <label for="resend_email" class="form-label">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="resend_email" name="resend_email" class="form-input" placeholder="your.email@example.com" required>
                    </div>
                </div>
                <button type="submit" class="btn-auth">
                    <i class="fas fa-redo"></i> Resend Verification
                </button>
            </form>
        </div>
    </div>
</main>

<!-- Market Status Ticker -->
<div class="market-status">
    <div class="market-ticker">
        <div class="ticker-item">
            <span class="ticker-symbol">BTC/USD</span>
            <span class="ticker-price">$42,150</span>
            <span class="ticker-change">+2.3%</span>
        </div>
        <div class="ticker-item">
            <span class="ticker-symbol">ETH/USD</span>
            <span class="ticker-price">$2,850</span>
            <span class="ticker-change">+1.7%</span>
        </div>
        <div class="ticker-item">
            <span class="ticker-symbol">SPY</span>
            <span class="ticker-price">$455.20</span>
            <span class="ticker-change">+0.8%</span>
        </div>
        <div class="ticker-item">
            <span class="ticker-symbol">AAPL</span>
            <span class="ticker-price">$185.43</span>
            <span class="ticker-change negative">-0.3%</span>
        </div>
        <div class="ticker-item">
            <span class="ticker-symbol">GOLD</span>
            <span class="ticker-price">$2,034</span>
            <span class="ticker-change">+0.5%</span>
        </div>
    </div>
</div>

<script>
    // Trading background animation
    function createTradingBackground() {
        const bg = document.getElementById('tradingBg');
        const width = window.innerWidth;
        const height = window.innerHeight;

        // Create chart lines
        for (let i = 0; i < 8; i++) {
            const line = document.createElement('div');
            line.className = 'chart-line';
            line.style.top = Math.random() * height + 'px';
            line.style.left = Math.random() * width + 'px';
            line.style.width = Math.random() * 200 + 100 + 'px';
            line.style.transform = `rotate(${Math.random() * 30 - 15}deg)`;
            bg.appendChild(line);
        }

        // Create candlesticks
        for (let i = 0; i < 15; i++) {
            const candle = document.createElement('div');
            candle.className = Math.random() > 0.5 ? 'candlestick' : 'candlestick red';
            candle.style.left = Math.random() * width + 'px';
            candle.style.top = Math.random() * height + 'px';
            candle.style.height = Math.random() * 40 + 10 + 'px';
            bg.appendChild(candle);
        }

        // Create market indicators
        const indicators = ['RSI: 45', 'MACD: 0.02', 'VOL: 2.4M', 'BB: 1.2', 'ATR: 0.8'];
        for (let i = 0; i < 10; i++) {
            const indicator = document.createElement('div');
            indicator.className = 'market-indicator';
            indicator.textContent = indicators[Math.floor(Math.random() * indicators.length)];
            indicator.style.left = Math.random() * width + 'px';
            indicator.style.top = Math.random() * height + 'px';
            bg.appendChild(indicator);
        }
    }

    // Login/Register form switching
    const loginTab = document.querySelector('[data-tab="login"]');
    const signupTab = document.querySelector('[data-tab="signup"]');
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const switchToSignup = document.getElementById('switch-to-signup');
    const switchToLogin = document.getElementById('switch-to-login');

    function showLoginForm() {
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
        loginForm.classList.add('active');
        signupForm.classList.remove('active');
    }

    function showSignupForm() {
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
        signupForm.classList.add('active');
        loginForm.classList.remove('active');
    }

    loginTab.addEventListener('click', showLoginForm);
    signupTab.addEventListener('click', showSignupForm);
    switchToSignup.addEventListener('click', function(e) {
        e.preventDefault();
        showSignupForm();
    });
    switchToLogin.addEventListener('click', function(e) {
        e.preventDefault();
        showLoginForm();
    });

    // Password toggle functionality
    function setupPasswordToggle(passwordId, toggleId) {
        const passwordField = document.getElementById(passwordId);
        const toggleIcon = document.getElementById(toggleId);

        toggleIcon.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
    }

    // Setup password toggles
    setupPasswordToggle('login-password', 'toggle-login-password');
    setupPasswordToggle('signup-password', 'toggle-signup-password');

    // Password strength checker
    const passwordField = document.getElementById('signup-password');
    const passwordStrength = document.getElementById('password-strength');
    const confirmPassword = document.getElementById('signup-confirm');
    const passwordMatch = document.getElementById('password-match');

    passwordField.addEventListener('input', function() {
        const password = this.value;
        let strength = 'weak';
        let message = 'Password is weak';
        let icon = 'fa-times-circle';
        let colorClass = 'weak';

        // Check password strength
        if (password.length >= 8) {
            strength = 'medium';
            message = 'Password is medium';
            icon = 'fa-exclamation-circle';
            colorClass = 'medium';

            if (password.match(/[a-z]/) && password.match(/[A-Z]/) && password.match(/[0-9]/) && password.match(/[^a-zA-Z0-9]/)) {
                strength = 'strong';
                message = 'Password is strong';
                icon = 'fa-check-circle';
                colorClass = 'strong';
            }
        }

        // Update strength indicator
        passwordStrength.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
        passwordStrength.className = `password-strength ${colorClass}`;
    });

    // Password match checker
    confirmPassword.addEventListener('input', function() {
        if (this.value !== passwordField.value) {
            passwordMatch.style.display = 'flex';
        } else {
            passwordMatch.style.display = 'none';
        }
    });

    // Initialize trading background
    document.addEventListener('DOMContentLoaded', function() {
        createTradingBackground();

        // Animate market ticker
        const tickerItems = document.querySelectorAll('.ticker-item');
        tickerItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.5}s`;
        });
    });

    // Simulate live market data updates
    function updateMarketData() {
        const prices = document.querySelectorAll('.ticker-price');
        const changes = document.querySelectorAll('.ticker-change');

        prices.forEach(price => {
            const current = parseFloat(price.textContent.replace(/[^\d.]/g, ''));
            const change = (Math.random() - 0.5) * 0.1;
            const newPrice = current * (1 + change);
            price.textContent = '$' + newPrice.toFixed(2);

            // Update parent change indicator
            const changeElement = price.nextElementSibling;
            if (change >= 0) {
                changeElement.textContent = `+${(change * 100).toFixed(2)}%`;
                changeElement.classList.remove('negative');
            } else {
                changeElement.textContent = `${(change * 100).toFixed(2)}%`;
                changeElement.classList.add('negative');
            }
        });
    }

    // Modal functions
    function showForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').style.display = 'flex';
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgotPasswordModal').style.display = 'none';
    }

    function showResendVerificationModal() {
        document.getElementById('resendVerificationModal').style.display = 'flex';
    }

    function closeResendVerificationModal() {
        document.getElementById('resendVerificationModal').style.display = 'none';
    }

    function resendVerification(email) {
        document.getElementById('resend_email').value = email;
        showResendVerificationModal();
    }

    // Enhanced password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) strength++;
        else feedback.push("at least 8 characters");

        if (/[A-Z]/.test(password)) strength++;
        else feedback.push("one uppercase letter");

        if (/[0-9]/.test(password)) strength++;
        else feedback.push("one number");

        if (/[^A-Za-z0-9]/.test(password)) strength++;
        else feedback.push("one special character");

        return {
            strength,
            feedback
        };
    }

    // Update password strength display
    passwordField.addEventListener('input', function() {
        const password = this.value;
        const {
            strength,
            feedback
        } = checkPasswordStrength(password);

        let strengthText = 'Weak';
        let strengthClass = 'weak';
        let icon = 'fa-times-circle';

        if (strength >= 4) {
            strengthText = 'Strong';
            strengthClass = 'strong';
            icon = 'fa-check-circle';
        } else if (strength >= 3) {
            strengthText = 'Medium';
            strengthClass = 'medium';
            icon = 'fa-exclamation-circle';
        }

        let message = `Password strength: ${strengthText}`;
        if (feedback.length > 0) {
            message += `. Needs: ${feedback.join(', ')}`;
        }

        passwordStrength.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
        passwordStrength.className = `password-strength ${strengthClass}`;
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const forgotModal = document.getElementById('forgotPasswordModal');
        const verifyModal = document.getElementById('resendVerificationModal');

        if (event.target === forgotModal) {
            closeForgotPasswordModal();
        }
        if (event.target === verifyModal) {
            closeResendVerificationModal();
        }
    });

    // Update forgot password link in login form
    document.addEventListener('DOMContentLoaded', function() {
        const forgotPasswordLink = document.querySelector('.forgot-password');
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                showForgotPasswordModal();
            });
        }

        // Show verification notice if needed
        <?php if ($showVerificationNotice): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showResendVerificationModal();
            });
        <?php endif; ?>
    });

    // Update market data every 10 seconds
    setInterval(updateMarketData, 10000);
</script>
</body>

</html>