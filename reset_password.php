
<?php
// reset_password.php
session_start();
require_once 'app/config/database.php';
require_once 'app/utils/security.php';

$database = new Database();
$db = $database->getConnection();
$security = new Security();

$error = '';
$success = '';
$validToken = false;
$email = '';

// Check if token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Validate token
    $query = "SELECT email, expires_at, used FROM password_resets WHERE token = :token AND used = 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (strtotime($resetRequest['expires_at']) > time()) {
            $validToken = true;
            $email = $resetRequest['email'];
        } else {
            $error = "This password reset link has expired.";
        }
    } else {
        $error = "Invalid or used password reset link.";
    }
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate token again
    $query = "SELECT email, expires_at, used FROM password_resets WHERE token = :token AND used = 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (strtotime($resetRequest['expires_at']) > time()) {
            // Validate passwords
            if (empty($password) || empty($confirmPassword)) {
                $error = "Please fill in all fields.";
            } elseif ($password !== $confirmPassword) {
                $error = "Passwords do not match.";
            } elseif (strlen($password) < 8) {
                $error = "Password must be at least 8 characters.";
            } else {
                // Update password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET password_hash = :password_hash WHERE email = :email";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':password_hash', $passwordHash);
                $updateStmt->bindParam(':email', $resetRequest['email']);
                
                if ($updateStmt->execute()) {
                    // Mark token as used
                    $markUsedQuery = "UPDATE password_resets SET used = 1 WHERE token = :token";
                    $markUsedStmt = $db->prepare($markUsedQuery);
                    $markUsedStmt->bindParam(':token', $token);
                    $markUsedStmt->execute();
                    
                    $success = "Password reset successfully! You can now login with your new password.";
                    $validToken = false; // Hide the form
                } else {
                    $error = "Failed to reset password. Please try again.";
                }
            }
        } else {
            $error = "This password reset link has expired.";
        }
    } else {
        $error = "Invalid or used password reset link.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Trade Author</title>
    <style>
        /* Add the same styling as login page */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            color: #f5f5f5;
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .auth-card {
            background: rgba(26, 26, 38, 0.95);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 107, 53, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #e0e0e0;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 107, 53, 0.3);
            border-radius: 10px;
            color: #f5f5f5;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.2);
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
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        .error-message {
            color: #ff3d00;
            background: rgba(255, 61, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #ff3d00;
            margin-bottom: 20px;
        }

        .success-message {
            color: #00c853;
            background: rgba(0, 200, 83, 0.1);
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #00c853;
            margin-bottom: 20px;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #ff6b35;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Trade Author</h2>
            <p>Reset Your Password</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($validToken && empty($success)): ?>
            <form method="POST" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <input type="hidden" name="reset_password" value="1">
                
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter new password" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Confirm new password" required minlength="8">
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </form>
        <?php elseif (empty($success)): ?>
            <div class="error-message">
                <p>Unable to process password reset. Please request a new reset link.</p>
            </div>
        <?php endif; ?>

        <div class="back-to-login">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <script>
        // Password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            function validatePasswords() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = '#ff3d00';
                } else {
                    confirmPassword.style.borderColor = '#00c853';
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html>
