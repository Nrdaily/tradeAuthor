
<?php
// set_language.php
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../config/language.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$language = $_POST['language'] ?? 'en';

if (setLanguage($language)) {
    echo json_encode(['success' => true, 'message' => 'Language updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid language']);
}

// Validate language
$allowed_languages = ['en', 'es', 'fr', 'de', 'zh', 'ar'];
if (!in_array($language, $allowed_languages)) {
    echo json_encode(['success' => false, 'message' => 'Invalid language']);
    exit;
}

try {
    // Save to database if table exists
    $query = "INSERT INTO user_preferences (user_id, language) 
              VALUES (:user_id, :language) 
              ON DUPLICATE KEY UPDATE language = :language";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
} catch (PDOException $e) {
    // Table doesn't exist, save to session only
    error_log("User preferences table not available: " . $e->getMessage());
}

// Save to session
$_SESSION['language'] = $language;

echo json_encode(['success' => true, 'message' => 'Language updated']);
?>