<?php
// admin/create_admin.php
require_once 'session.php';

// Check if user is admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login');
    exit;
}

// Include database connection
require_once '../app/config/database.php';
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Check if username already exists
        $stmt = $db->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new admin
            $stmt = $db->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
            
            if ($stmt->execute([$username, $password_hash])) {
                $success = "Admin created successfully!";
                // Clear form
                $_POST = array();
            } else {
                $error = "Failed to create admin. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Create Admin</title>
    <link rel="shortcut icon" href="../assets/icons/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 600px;
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
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid #553f33ff;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 16px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #e9660eff;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .success-message {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #10b981;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 14px;
            color: #94a3b8;
        }
        
        .password-strength.strong {
            color: #10b981;
        }
        
        .password-strength.weak {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php require_once "nav.php" ?>


    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="header-title">
                <h1>Create New Admin</h1>
            </div>

            <div class="user-actions">
                <div class="user-profile">
                    <div class="avatar">
                        <img src="../assets/icons/user.jpg" alt="">
                    </div>
                    <span><?php echo $_SESSION['admin_username']; ?></span>
                </div>
            </div>
        </header>

        <div class="admin-container">
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-input" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                        <div class="password-strength" id="password-strength">Minimum 8 characters</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                        <div class="error-message" id="password-match-message"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create Admin
                    </button>
                </form>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h3>Admin Security Guidelines</h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li>Use strong, unique passwords for admin accounts</li>
                    <li>Limit admin access to trusted personnel only</li>
                    <li>Regularly review admin account activity</li>
                    <li>Remove admin access for employees who no longer need it</li>
                    <li>Enable two-factor authentication if available</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('password-strength');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatchMessage = document.getElementById('password-match-message');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                passwordStrength.textContent = 'Minimum 8 characters';
                passwordStrength.className = 'password-strength';
            } else if (password.length < 8) {
                passwordStrength.textContent = 'Too short';
                passwordStrength.className = 'password-strength weak';
            } else {
                // Check password strength
                let strength = 'Weak';
                let className = 'weak';
                
                if (password.length >= 12) {
                    strength = 'Strong';
                    className = 'strong';
                } else if (password.length >= 8) {
                    strength = 'Medium';
                }
                
                passwordStrength.textContent = strength;
                passwordStrength.className = 'password-strength ' + className;
            }
        });
        
        // Password match validation
        confirmPasswordInput.addEventListener('input', function() {
            if (passwordInput.value !== this.value) {
                passwordMatchMessage.textContent = 'Passwords do not match';
            } else {
                passwordMatchMessage.textContent = '';
            }
        });
    </script>
</body>
</html>