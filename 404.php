<?php
// 404.php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Trade Author</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #080808ff;
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 30px;
        }
        
        .error-icon {
            font-size: 80px;
            color: #ef4444;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .error-message {
            font-size: 18px;
            color: #b8a294ff;
            margin-bottom: 30px;
        }
        
        .home-link {
            display: inline-block;
            padding: 12px 24px;
            background-color: #e9780eff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        
        .home-link:hover {
            background-color: #a14a03ff;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">The page you're looking for doesn't exist or has been moved.</p>
        <a href="/" class="home-link">
            <i class="fas fa-home"></i> Return to Home
        </a>
    </div>
</body>
</html>