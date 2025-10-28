
<?php
// create_preferences_table.php
// Run this script once to create the user_preferences table

require_once '../app/config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `user_preferences` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `theme` varchar(20) NOT NULL DEFAULT 'auto',
        `language` varchar(10) NOT NULL DEFAULT 'en',
        `notifications` tinyint(1) NOT NULL DEFAULT '1',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_id` (`user_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sql);
    echo "✅ user_preferences table created successfully!\n";
    
    // Migrate any existing session preferences to the database
    $check_users = "SELECT id FROM users";
    $users_stmt = $db->query($check_users);
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        $check_prefs = "SELECT COUNT(*) FROM user_preferences WHERE user_id = :user_id";
        $check_stmt = $db->prepare($check_prefs);
        $check_stmt->bindParam(':user_id', $user['id']);
        $check_stmt->execute();
        
        if ($check_stmt->fetchColumn() == 0) {
            // Insert default preferences for user
            $insert_sql = "INSERT INTO user_preferences (user_id) VALUES (:user_id)";
            $insert_stmt = $db->prepare($insert_sql);
            $insert_stmt->bindParam(':user_id', $user['id']);
            $insert_stmt->execute();
        }
    }
    
    echo "✅ Default preferences created for all users!\n";
    
} catch (PDOException $e) {
    echo "❌ Error creating table: " . $e->getMessage() . "\n";
}
?>