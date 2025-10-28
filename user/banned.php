<?php require_once '../app/config/language.php'; ?>
<?php
// banned.php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['ban_reason'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "favicon.php"?>
    <title>Account Banned - Trade Author</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1b12 100%);
            color: #e2e8f0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .banned-container {
            text-align: center;
            background: rgba(30, 30, 30, 0.9);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid #ef4444;
            max-width: 500px;
        }
        .banned-icon {
            font-size: 64px;
            color: #ef4444;
            margin-bottom: 20px;
        }
        .ban-reason {
            background: rgba(239, 68, 68, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="banned-container">
        <div class="banned-icon">
            <i class="fas fa-ban"></i>
        </div>
        <h1>Account Temporarily Suspended</h1>
        
        <div class="ban-reason">
            <h3>Reason for Suspension:</h3>
            <p><?php echo htmlspecialchars($_SESSION['ban_reason'] ?: 'Violation of terms of service'); ?></p>
        </div>
        
        <div class="contact-info">
            <h3>Contact Support</h3>
            <p>If you believe this is a mistake, please contact our support team:</p>
            <p><i class="fas fa-envelope"></i> support@tradeauthor.com</p>
            <!-- <p><i class="fab fa-telegram"></i> @tradeauthor_support</p> -->
        </div>
        
        <p style="margin-top: 20px; font-size: 14px; color: #94a3b8;">
            To get unbanned, please provide a valid explanation of the issue and ensure compliance with our terms.
        </p>
        
        <a href="logout.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 5px;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</body>
</html>