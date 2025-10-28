
<?php
// verify_email.php
session_start();
require_once 'app/config/database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Check if token and user ID are provided
if (isset($_GET['token']) && isset($_GET['id'])) {
    $token = $_GET['token'];
    $userId = intval($_GET['id']);
    
    // Validate token and user
    $query = "SELECT id, first_name, email_verification_token, email_verification_expires, email_verified 
              FROM users WHERE id = :id AND email_verified = 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user['email_verification_token'] === $token) {
            if (strtotime($user['email_verification_expires']) > time()) {
                // Mark email as verified
                $updateQuery = "UPDATE users SET email_verified = 1, email_verification_token = NULL, email_verification_expires = NULL WHERE id = :id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':id', $userId);
                
                if ($updateStmt->execute()) {
                    $success = "Email verified successfully! You can now login to your Trade Author account.";
                } else {
                    $error = "Failed to verify email. Please try again.";
                }
            } else {
                $error = "This verification link has expired. Please request a new verification email.";
            }
        } else {
            $error = "Invalid verification token.";
        }
    } else {
        $error = "Invalid user or email already verified.";
    }
} else {
    $error = "Missing verification parameters.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Trade Author</title>
    <style>
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
            max-width: 500px;
            border: 1px solid rgba(255, 107, 53, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .auth-header {
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

        .icon-success {
            font-size: 4rem;
            color: #00c853;
            margin-bottom: 20px;
        }

        .icon-error {
            font-size: 4rem;
            color: #ff3d00;
            margin-bottom: 20px;
        }

        .error-message {
            color: #ff3d00;
            background: rgba(255, 61, 0, 0.1);
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #ff3d00;
            margin-bottom: 20px;
        }

        .success-message {
            color: #00c853;
            background: rgba(0, 200, 83, 0.1);
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #00c853;
            margin-bottom: 20px;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b35, #ff8e53);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">Trade Author</h2>
            <p>Email Verification</p>
        </div>

        <?php if ($success): ?>
            <div class="icon-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-message">
                <h3>Verification Successful!</h3>
                <p><?php echo $success; ?></p>
            </div>
            <a href="login.php" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Proceed to Login
            </a>
        <?php elseif ($error): ?>
            <div class="icon-error">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="error-message">
                <h3>Verification Failed</h3>
                <p><?php echo $error; ?></p>
            </div>
            <a href="login.php" class="btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
