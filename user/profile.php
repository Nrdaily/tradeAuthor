<?php require_once '../app/config/language.php'; ?>
<?php
// Enhanced profile.php with complete orange-branded theme system
require_once '../app/config/session.php';

// Get user data
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        
        $update_query = "UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :user_id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':first_name', $first_name);
        $update_stmt->bindParam(':last_name', $last_name);
        $update_stmt->bindParam(':user_id', $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Failed to update profile.";
        }
    }
    
    if (isset($_POST['update_preferences'])) {
        $theme = $_POST['theme'];
        $language = $_POST['language'];
        
        // Update user preferences in session
        $_SESSION['user_preferences'] = [
            'theme' => $theme,
            'language' => $language
        ];
        
        $success = "Preferences updated successfully!";
    }
}

// Get current preferences
$preferences = $_SESSION['user_preferences'] ?? ['theme' => 'auto', 'language' => 'en'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Author - Profile</title>
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-header {
            background: var(--gradient);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.2);
            margin: 0 auto 15px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .preference-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .preference-option:hover {
            border-color: var(--primary);
            background: rgba(255, 107, 53, 0.05);
        }
        
        .preference-info h4 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .preference-info p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border);
            transition: .4s;
            border-radius: 34px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: var(--primary);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .verification-badge {
            background: var(--success);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .security-level {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .security-bar {
            flex: 1;
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .security-progress {
            height: 100%;
            background: var(--gradient);
            border-radius: 3px;
            transition: width 0.3s;
        }
        
        .theme-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .theme-sample {
            width: 60px;
            height: 30px;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
            position: relative;
        }
        
        .theme-sample.active {
            border-color: var(--primary);
        }
        
        .theme-sample.dark {
            background: #131722;
            border: 1px solid #2a2f42;
        }
        
        .theme-sample.light {
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }
        
        .theme-sample.auto {
            background: linear-gradient(90deg, #131722 50%, #ffffff 50%);
            border: 1px solid #2a2f42;
        }
        
        .theme-tooltip {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-primary);
            color: var(--background);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .theme-sample:hover .theme-tooltip {
            opacity: 1;
        }
    </style>
</head>
<body class="theme-transition">
    <?php include "nav.php"; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="../assets/icons/user.jpg" alt="Profile" width="100" style="border-radius: 50%;">
                </div>
                <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <div class="verification-badge">
                    <i class="fas fa-shield-check"></i>
                    Verified Trader
                </div>
            </div>

            <!-- Display success/error messages -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success" style="background: rgba(0, 200, 83, 0.1); border: 1px solid var(--success); color: var(--success); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error" style="background: rgba(255, 61, 0, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-stats">
                <div class="stat-card">
                    <div class="stat-value">12</div>
                    <div class="stat-label">Total Trades</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">98.7%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">30d</div>
                    <div class="stat-label">Trading Since</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">A+</div>
                    <div class="stat-label">Trust Score</div>
                </div>
            </div>
            
            <div class="card-grid">
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-user-circle"></i>
                        <span>Personal Information</span>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small style="color: var(--text-secondary); margin-top: 5px; display: block;">Email cannot be changed for security reasons</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-cogs"></i>
                        <span>Preferences</span>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="update_preferences" value="1">
                        
                        <div class="preference-option">
                            <div class="preference-info">
                                <h4>Theme</h4>
                                <p>Choose your preferred interface theme</p>
                                <div class="theme-preview">
                                    <div class="theme-sample dark <?php echo ($preferences['theme'] ?? 'auto') == 'dark' ? 'active' : ''; ?>" data-theme="dark" onclick="selectTheme('dark')">
                                        <div class="theme-tooltip">Dark</div>
                                    </div>
                                    <div class="theme-sample light <?php echo ($preferences['theme'] ?? 'auto') == 'light' ? 'active' : ''; ?>" data-theme="light" onclick="selectTheme('light')">
                                        <div class="theme-tooltip">Light</div>
                                    </div>
                                    <div class="theme-sample auto <?php echo ($preferences['theme'] ?? 'auto') == 'auto' ? 'active' : ''; ?>" data-theme="auto" onclick="selectTheme('auto')">
                                        <div class="theme-tooltip">Auto (System)</div>
                                    </div>
                                </div>
                            </div>
                            <select name="theme" id="theme-select" class="form-control" style="width: auto;">
                                <option value="auto" <?php echo ($preferences['theme'] ?? 'auto') == 'auto' ? 'selected' : ''; ?>>Auto (System)</option>
                                <option value="dark" <?php echo ($preferences['theme'] ?? 'auto') == 'dark' ? 'selected' : ''; ?>>Dark</option>
                                <option value="light" <?php echo ($preferences['theme'] ?? 'auto') == 'light' ? 'selected' : ''; ?>>Light</option>
                            </select>
                        </div>
                        
                        <div class="preference-option">
                            <div class="preference-info">
                                <h4>Language</h4>
                                <p>Select your preferred language</p>
                            </div>
                            <select name="language" class="form-control" style="width: auto;">
                                <option value="en" <?php echo ($preferences['language'] ?? 'en') == 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="es" <?php echo ($preferences['language'] ?? 'en') == 'es' ? 'selected' : ''; ?>>Español</option>
                                <option value="fr" <?php echo ($preferences['language'] ?? 'en') == 'fr' ? 'selected' : ''; ?>>Français</option>
                                <option value="de" <?php echo ($preferences['language'] ?? 'en') == 'de' ? 'selected' : ''; ?>>Deutsch</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Save Preferences</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-shield-alt"></i>
                    <span>Security</span>
                </div>
                
                <div class="security-level">
                    <span>Security Score:</span>
                    <div class="security-bar">
                        <div class="security-progress" style="width: 85%;"></div>
                    </div>
                    <strong>85%</strong>
                </div>
                
                <div style="margin-top: 20px;">
                    <div class="preference-option">
                        <div class="preference-info">
                            <h4>Two-Factor Authentication</h4>
                            <p>Add an extra layer of security to your account</p>
                        </div>
                        <button class="btn btn-outline">Enable</button>
                    </div>
                    
                    <div class="preference-option">
                        <div class="preference-info">
                            <h4>Login Activity</h4>
                            <p>Review recent account access</p>
                        </div>
                        <button class="btn btn-outline">View</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectTheme(theme) {
            document.querySelectorAll('.theme-sample').forEach(sample => {
                sample.classList.remove('active');
            });
            document.querySelector(`.theme-sample[data-theme="${theme}"]`).classList.add('active');
            document.getElementById('theme-select').value = theme;
            
            // Preview theme change
            if (theme === 'auto') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('light-mode', !prefersDark);
            } else {
                document.documentElement.classList.toggle('light-mode', theme === 'light');
            }
        }

        // Initialize theme based on current preference
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.getElementById('theme-select').value;
            selectTheme(currentTheme);
        });
    </script>
</body>
</html>