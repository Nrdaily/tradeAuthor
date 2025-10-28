<?php require_once '../app/config/language.php'; ?>
<?php
// settings.php
require_once '../app/config/session.php';
require_once '../app/config/encryption.php';

// Get user data
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle settings updates
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_security'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match";
        } elseif (strlen($new_password) < 8) {
            $error = "New password must be at least 8 characters long";
        } else {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = :password WHERE id = :user_id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':password', $hashed_password);
            $update_stmt->bindParam(':user_id', $_SESSION['user_id']);

            if ($update_stmt->execute()) {
                $success = "Password updated successfully!";
            } else {
                $error = "Failed to update password";
            }
        }
    }

    if (isset($_POST['update_notifications'])) {
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $push_notifications = isset($_POST['push_notifications']) ? 1 : 0;
        $price_alerts = isset($_POST['price_alerts']) ? 1 : 0;
        $security_alerts = isset($_POST['security_alerts']) ? 1 : 0;

        // In a real application, you'd update these in a user_settings table
        $_SESSION['user_notifications'] = [
            'email' => $email_notifications,
            'push' => $push_notifications,
            'price_alerts' => $price_alerts,
            'security_alerts' => $security_alerts
        ];

        $success = "Notification preferences updated successfully!";
    }

    if (isset($_POST['update_privacy'])) {
        $profile_visibility = $_POST['profile_visibility'];
        $data_sharing = isset($_POST['data_sharing']) ? 1 : 0;
        $marketing_emails = isset($_POST['marketing_emails']) ? 1 : 0;

        // In a real application, you'd update these in a user_privacy table
        $_SESSION['user_privacy'] = [
            'profile_visibility' => $profile_visibility,
            'data_sharing' => $data_sharing,
            'marketing_emails' => $marketing_emails
        ];

        $success = "Privacy settings updated successfully!";
    }
}

// Get current notification settings
$notifications = $_SESSION['user_notifications'] ?? [
    'email' => 1,
    'push' => 1,
    'price_alerts' => 1,
    'security_alerts' => 1
];

// Get current privacy settings
$privacy = $_SESSION['user_privacy'] ?? [
    'profile_visibility' => 'private',
    'data_sharing' => 0,
    'marketing_emails' => 0
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="settings_title">Trade Author - Settings</title>
    <?php include 'favicon.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .settings-sidebar {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .settings-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .settings-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--text-secondary);
            text-decoration: none;
        }

        .settings-nav-item:hover,
        .settings-nav-item.active {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary);
        }

        .settings-nav-item i {
            width: 20px;
            text-align: center;
        }

        .settings-content {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 30px;
        }

        .settings-section {
            margin-bottom: 40px;
        }

        .settings-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
            color: var(--primary);
        }

        .security-indicator {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .security-level {
            flex: 1;
            height: 8px;
            background: var(--border);
            border-radius: 4px;
            overflow: hidden;
        }

        .security-progress {
            height: 100%;
            background: var(--gradient);
            border-radius: 4px;
            transition: width 0.3s;
        }

        .preference-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 15px;
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

        input:checked+.toggle-slider {
            background-color: var(--primary);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(30px);
        }

        .danger-zone {
            background: rgba(255, 61, 0, 0.05);
            border: 1px solid var(--danger);
            border-radius: 12px;
            padding: 24px;
            margin-top: 40px;
        }

        .danger-zone h3 {
            color: var(--danger);
            margin-bottom: 15px;
        }

        .danger-zone p {
            color: var(--text-secondary);
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }

            .settings-sidebar {
                position: static;
            }

            .settings-nav {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 10px;
            }

            .settings-nav-item {
                white-space: nowrap;
            }
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .security-feature {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .security-feature.enabled {
            border-color: var(--success);
            background: rgba(0, 200, 83, 0.05);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .feature-info {
            flex: 1;
        }

        .feature-info h4 {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .feature-info p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="theme-transition">
    <?php include "nav.php"; ?>

    <div class="main-content">
        <div class="container">
            <div style="margin-bottom: 30px;">
                <h1 data-i18n="settings">Settings</h1>
                <p data-i18n="settings_desc">Manage your account preferences and security settings</p>
            </div>

            <!-- Display success/error messages -->
            <?php if ($success): ?>
                <div class="alert alert-success" style="background: rgba(0, 200, 83, 0.1); border: 1px solid var(--success); color: var(--success); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error" style="background: rgba(255, 61, 0, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="settings-container">
                <!-- Settings Sidebar -->
                <div class="settings-sidebar">
                    <div class="settings-nav">
                        <a href="#security" class="settings-nav-item active" onclick="showSection('security')">
                            <i class="fas fa-shield-alt"></i>
                            <span data-i18n="security">Security</span>
                        </a>
                        <a href="#notifications" class="settings-nav-item" onclick="showSection('notifications')">
                            <i class="fas fa-bell"></i>
                            <span data-i18n="notifications">Notifications</span>
                        </a>
                        <a href="#privacy" class="settings-nav-item" onclick="showSection('privacy')">
                            <i class="fas fa-lock"></i>
                            <span data-i18n="privacy">Privacy</span>
                        </a>
                        <a href="#api" class="settings-nav-item" onclick="showSection('api')">
                            <i class="fas fa-code"></i>
                            <span data-i18n="api">API Keys</span>
                        </a>
                        <a href="#appearance" class="settings-nav-item" onclick="showSection('appearance')">
                            <i class="fas fa-palette"></i>
                            <span data-i18n="appearance">Appearance</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="settings-content">
                    <!-- Security Section -->
                    <div id="security-section" class="settings-section">
                        <h2 class="section-title" data-i18n="security_settings">Security Settings</h2>

                        <div class="security-indicator">
                            <span data-i18n="security_level">Security Level:</span>
                            <div class="security-level">
                                <div class="security-progress" style="width: 85%;"></div>
                            </div>
                            <strong>85%</strong>
                        </div>

                        <div class="security-feature enabled">
                            <div class="feature-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="feature-info">
                                <h4 data-i18n="email_verification">Email Verification</h4>
                                <p data-i18n="email_verified">Your email address is verified</p>
                            </div>
                            <div class="status-badge status-completed" data-i18n="enabled">Enabled</div>
                        </div>

                        <div class="security-feature">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="feature-info">
                                <h4 data-i18n="two_factor">Two-Factor Authentication</h4>
                                <p data-i18n="two_factor_desc">Add an extra layer of security</p>
                            </div>
                            <button class="btn btn-outline" data-i18n="enable">Enable</button>
                        </div>

                        <form method="POST" action="change-password.php">
                            <input type="hidden" name="update_security" value="1">
                            <h3 style="margin: 30px 0 20px 0;" data-i18n="change_password">Change Password</h3>

                            <div class="form-group">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" id="current_password" name="current_password" class="form-control <?php echo isset($errors['current_password']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($errors['current_password'])): ?>
                                    <div class="error-message"><?php echo $errors['current_password']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="form-control <?php echo isset($errors['new_password']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($errors['new_password'])): ?>
                                    <div class="error-message"><?php echo $errors['new_password']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo isset($errors['confirm_password']) ? 'input-error' : ''; ?>" required>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="error-message"><?php echo $errors['confirm_password']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" data-i18n="update_password">Update Password</button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifications Section -->
                    <div id="notifications-section" class="settings-section" style="display: none;">
                        <h2 class="section-title" data-i18n="notification_settings">Notification Settings</h2>

                        <form method="POST">
                            <input type="hidden" name="update_notifications" value="1">

                            <div class="preference-option">
                                <div class="preference-info">
                                    <h4 data-i18n="email_notifications">Email Notifications</h4>
                                    <p data-i18n="email_notifications_desc">Receive important updates via email</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="email_notifications" <?php echo $notifications['email'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="preference-option">
                                <div class="preference-info">
                                    <h4 data-i18n="push_notifications">Push Notifications</h4>
                                    <p data-i18n="push_notifications_desc">Receive browser push notifications</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="push_notifications" <?php echo $notifications['push'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="preference-option">
                                <div class="preference-info">
                                    <h4 data-i18n="price_alerts">Price Alerts</h4>
                                    <p data-i18n="price_alerts_desc">Get notified when prices reach your targets</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="price_alerts" <?php echo $notifications['price_alerts'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="preference-option">
                                <div class="preference-info">
                                    <h4 data-i18n="security_alerts">Security Alerts</h4>
                                    <p data-i18n="security_alerts_desc">Get notified about security events</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="security_alerts" <?php echo $notifications['security_alerts'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" data-i18n="save_changes">Save Changes</button>
                            </div>
                        </form>
                    </div>

                    <!-- Privacy Section -->
                    <div id="privacy-section" class="settings-section" style="display: none;">
                        <h2 class="section-title" data-i18n="privacy_settings">Privacy Settings</h2>

                        <form method="POST">
                            <input type="hidden" name="update_privacy" value="1">

                            <div class="form-group">
                                <label class="form-label" data-i18n="profile_visibility">Profile Visibility</label>
                                <select name="profile_visibility" class="form-control">
                                    <option value="public" <?php echo $privacy['profile_visibility'] === 'public' ? 'selected' : ''; ?> data-i18n="public">Public</option>
                                    <option value="private" <?php echo $privacy['profile_visibility'] === 'private' ? 'selected' : ''; ?> data-i18n="private">Private</option>
                                    <option value="friends" <?php echo $privacy['profile_visibility'] === 'friends' ? 'selected' : ''; ?> data-i18n="friends_only">Friends Only</option>
                                </select>
                            </div>

                            <div class="preference-option">
                                <div class="preference-info">
                                    <h4 data-i18n="data_sharing">Data Sharing</h4>
                                    <p data-i18n="data_sharing_desc">Help improve our service by sharing anonymous data</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="data_sharing" <?php echo $privacy['data_sharing'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="preference-option">
                                <div class="preference-info">
                                    <h4 data-i18n="marketing_emails">Marketing Emails</h4>
                                    <p data-i18n="marketing_emails_desc">Receive emails about new features and promotions</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="marketing_emails" <?php echo $privacy['marketing_emails'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" data-i18n="save_changes">Save Changes</button>
                            </div>
                        </form>
                    </div>

                    <!-- API Section -->
                    <div id="api-section" class="settings-section" style="display: none;">
                        <h2 class="section-title" data-i18n="api_keys">API Keys</h2>
                        <p data-i18n="api_keys_desc">Manage your API keys for programmatic trading</p>

                        <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                            <i class="fas fa-code" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;"></i>
                            <h3 data-i18n="no_api_keys">No API Keys Generated</h3>
                            <p data-i18n="api_keys_coming_soon">API key management coming soon</p>
                            <button class="btn btn-primary" style="margin-top: 15px;" disabled data-i18n="generate_api_key">
                                Generate API Key
                            </button>
                        </div>
                    </div>

                    <!-- Appearance Section -->
                    <div id="appearance-section" class="settings-section" style="display: none;">
                        <h2 class="section-title" data-i18n="appearance_settings">Appearance Settings</h2>
                        <p data-i18n="appearance_settings_desc">Customize how the platform looks and feels</p>

                        <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                            <i class="fas fa-palette" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;"></i>
                            <h3 data-i18n="customization_coming_soon">Customization Coming Soon</h3>
                            <p data-i18n="appearance_coming_soon">Advanced appearance customization will be available in a future update</p>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="danger-zone">
                        <h3 data-i18n="danger_zone">Danger Zone</h3>
                        <p data-i18n="danger_zone_desc">Permanent and destructive actions. Proceed with caution.</p>

                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <button class="btn btn-outline" onclick="showDeleteModal()" data-i18n="delete_account">
                                <i class="fas fa-trash"></i>
                                Delete Account
                            </button>
                            <button class="btn btn-outline" onclick="exportAllData()" data-i18n="export_all_data">
                                <i class="fas fa-download"></i>
                                Export All Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Settings navigation
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('.settings-section').forEach(sec => {
                sec.style.display = 'none';
            });

            // Show selected section
            document.getElementById(section + '-section').style.display = 'block';

            // Update active nav item
            document.querySelectorAll('.settings-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Danger zone actions
        function showDeleteModal() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                // alert('Account deletion would be processed here');
                showToast('Could not delete your account now.', 'error');
                setTimeout(() => {
                    showToast('Please try again later', 'error');
                }, 1000);
            }
        }

        function exportAllData() {
            alert('All your data would be exported and downloaded here');
            showToast('All your data would be exported and downloaded here', 'error');

        }

        // Initialize settings page
        document.addEventListener('DOMContentLoaded', function() {
            // Show security section by default
            showSection('security');
        });
    </script>
</body>

</html>