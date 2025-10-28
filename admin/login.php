<?php
// admin/login.php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /index');
    exit;
}

// Include database connection
require_once '../app/config/database.php';
$database = new Database();
$db = $database->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Check admin credentials
    $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <title>Trade Author - Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #ff6b35;
            --accent-dark: #e55a2b;
            --text: #e2e8f0;
            --text-muted: #b8a194ff;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --border: #553e33ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background-color: var(--secondary);
            border-radius: 12px;
            padding: 30px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .logo img {
            width: 180px;
            /* height: 50px; */
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
        }

        .logo-text span {
            color: var(--accent);
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
            background: rgba(13, 13, 13, 0.5);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(233, 91, 14, 0.2);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(233, 98, 14, 0.3);
        }

        .error-message {
            color: var(--danger);
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background-color: rgba(239, 68, 68, 0.1);
            border-radius: 8px;
            border: 1px solid var(--danger);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <img src="../assets/icons/logo.png" alt="">
                <!-- <div class="logo-text">Crypto<span>Base</span></div> -->
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>

                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>