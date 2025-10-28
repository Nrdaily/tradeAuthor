<?php require_once '../app/config/language.php'; ?>
<?php
// change-password.php
require_once '../app/config/session.php';


$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($current_password)) {
        $errors['current_password'] = "Current password is required";
    }

    if (empty($new_password)) {
        $errors['new_password'] = "New password is required";
    } elseif (strlen($new_password) < 8) {
        $errors['new_password'] = "Password must be at least 8 characters long";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your new password";
    } elseif ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // If no errors, verify current password and update
    if (empty($errors)) {
        try {
            // Get current user's password hash
            $query = "SELECT password_hash FROM users WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($current_password, $user['password_hash'])) {
                // Current password is correct, update to new password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $query = "UPDATE users SET password_hash = :password_hash WHERE id = :user_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':password_hash', $new_password_hash);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);

                if ($stmt->execute()) {
                    $success = "Password changed successfully!";
                } else {
                    $errors[] = "Failed to update password. Please try again.";
                }
            } else {
                $errors['current_password'] = "Current password is incorrect";
            }
        } catch (PDOException $e) {
            error_log("Password change error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'favicon.php'; ?>
    <title>Trade Author - Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .password-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .password-strength {
            margin-top: 10px;
            padding: 15px;
            border-radius: 10px;
            background: var(--card-bg);
            border: 1px solid var(--border);
        }

        .strength-meter {
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            margin: 10px 0;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .strength-weak {
            background: var(--danger);
            width: 25%;
        }

        .strength-medium {
            background: var(--warning);
            width: 50%;
        }

        .strength-strong {
            background: var(--success);
            width: 75%;
        }

        .strength-very-strong {
            background: var(--success);
            width: 100%;
        }

        .requirements {
            margin-top: 20px;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .requirement.met {
            color: var(--success);
        }

        .requirement i {
            width: 16px;
        }

        .security-tips {
            background: rgba(255, 107, 53, 0.05);
            border: 1px solid var(--primary);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .security-tips h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }

        .tip {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .tip i {
            color: var(--primary);
            margin-top: 2px;
        }

        .change-password-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid #334155;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 16px;
        }

        .form-input:focus {
            outline: none;
            border-color: #0ea5e9;
        }

        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 5px;
        }

        .input-error {
            border-color: #ef4444 !important;
        }

        .success-message {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #10b981;
        }
    </style>
</head>

<body>

    <?php include "nav.php"; ?>

    <div class="main-content">
        <div class="container">

            <div class="change-password-container">
                <?php if (!empty($success)): ?>
                    <div class="success-message">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors) && is_array($errors)): ?>
                    <div class="error-message">
                        <?php foreach ($errors as $key => $error): ?>
                            <?php if (!in_array($key, ['current_password', 'new_password', 'confirm_password'])): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <h2 class="section-title" data-i18n="security_settings">Security Settings</h2>

                    <form method="POST" action="change-password.php">
                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-input <?php echo isset($errors['current_password']) ? 'input-error' : ''; ?>" required>
                            <?php if (isset($errors['current_password'])): ?>
                                <div class="error-message"><?php echo $errors['current_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-input <?php echo isset($errors['new_password']) ? 'input-error' : ''; ?>" required>
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="error-message"><?php echo $errors['new_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input <?php echo isset($errors['confirm_password']) ? 'input-error' : ''; ?>" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="error-message"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>

                <div class="security-tips">
                    <h3 data-i18n="security_tips">Security Tips</h3>
                    <div class="tip">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong data-i18n="tip_unique">Use a unique password</strong>
                            <p data-i18n="tip_unique_desc">Don't reuse passwords from other websites</p>
                        </div>
                    </div>
                    <div class="tip">
                        <i class="fas fa-sync-alt"></i>
                        <div>
                            <strong data-i18n="tip_regular">Change regularly</strong>
                            <p data-i18n="tip_regular_desc">Update your password every 3-6 months</p>
                        </div>
                    </div>
                    <div class="tip">
                        <i class="fas fa-user-lock"></i>
                        <div>
                            <strong data-i18n="tip_2fa">Enable two-factor authentication</strong>
                            <p data-i18n="tip_2fa_desc">Add an extra layer of security to your account</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('new-password').value;
            const strengthDiv = document.getElementById('password-strength');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');

            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }

            strengthDiv.style.display = 'block';

            // Check requirements
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

            // Update requirement indicators
            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-lowercase', hasLowercase);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-special', hasSpecial);

            // Calculate strength
            let strength = 0;
            if (hasLength) strength++;
            if (hasUppercase) strength++;
            if (hasLowercase) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;

            // Update strength meter
            strengthFill.className = 'strength-fill';
            if (strength <= 1) {
                strengthFill.classList.add('strength-weak');
                strengthText.textContent = 'Weak';
                strengthText.style.color = 'var(--danger)';
            } else if (strength <= 3) {
                strengthFill.classList.add('strength-medium');
                strengthText.textContent = 'Medium';
                strengthText.style.color = 'var(--warning)';
            } else if (strength <= 4) {
                strengthFill.classList.add('strength-strong');
                strengthText.textContent = 'Strong';
                strengthText.style.color = 'var(--success)';
            } else {
                strengthFill.classList.add('strength-very-strong');
                strengthText.textContent = 'Very Strong';
                strengthText.style.color = 'var(--success)';
            }
        }

        function updateRequirement(elementId, met) {
            const element = document.getElementById(elementId);
            if (met) {
                element.classList.add('met');
                element.innerHTML = '<i class="fas fa-check-circle"></i>' + element.textContent;
            } else {
                element.classList.remove('met');
                element.innerHTML = '<i class="fas fa-circle" style="font-size: 0.5rem;"></i>' + element.textContent;
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const matchDiv = document.getElementById('password-match');

            if (confirmPassword.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }

            if (password === confirmPassword) {
                matchDiv.innerHTML = '<i class="fas fa-check-circle" style="color: var(--success);"></i> <span data-i18n="passwords_match">Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<i class="fas fa-times-circle" style="color: var(--danger);"></i> <span data-i18n="passwords_dont_match">Passwords do not match</span>';
            }
        }

        // Initialize password page
        document.addEventListener('DOMContentLoaded', function() {
            // Add any initialization here
        });
    </script>
    <script src="../assets/js/app.js"></script>
</body>

</html>