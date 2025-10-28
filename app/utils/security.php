
<?php
class Security {
    
    // Generate cryptographically secure token
    public function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    // Create remember token for device
    public function createRememberToken($userId, $db) {
        $token = $this->generateToken(64);
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
        
        // Remove existing tokens for this user
        $deleteQuery = "DELETE FROM login_sessions WHERE user_id = :user_id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':user_id', $userId);
        $deleteStmt->execute();
        
        // Insert new token
        $query = "INSERT INTO login_sessions (user_id, session_token, expires_at, device_info, ip_address, user_agent) 
                  VALUES (:user_id, :token, :expires_at, :device_info, :ip_address, :user_agent)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $hashedToken);
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->bindParam(':device_info', $_SERVER['HTTP_USER_AGENT']);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        
        if ($stmt->execute()) {
            return $token;
        }
        return false;
    }
    
    // Validate remember token
    public function validateRememberToken($token, $db) {
        $query = "SELECT ls.*, u.id, u.first_name, u.last_name, u.email, u.email_verified 
                  FROM login_sessions ls 
                  JOIN users u ON ls.user_id = u.id 
                  WHERE ls.expires_at > NOW() AND u.banned = 0";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($sessions as $session) {
            if (password_verify($token, $session['session_token'])) {
                // Update last activity
                $this->updateSessionActivity($session['id'], $db);
                return $session;
            }
        }
        
        return false;
    }
    
    // Update session activity
    private function updateSessionActivity($sessionId, $db) {
        $query = "UPDATE login_sessions SET last_activity = NOW() WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $sessionId);
        $stmt->execute();
    }
    
    // Check if account is locked
    public function isAccountLocked($email, $db) {
        $query = "SELECT account_locked_until FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                return true;
            }
        }
        return false;
    }
    
    // Increment login attempts
    public function incrementLoginAttempts($userId, $db) {
        $query = "UPDATE users SET login_attempts = login_attempts + 1 WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    // Reset login attempts
    public function resetLoginAttempts($userId, $db) {
        $query = "UPDATE users SET login_attempts = 0, account_locked_until = NULL WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    // Lock account
    public function lockAccount($userId, $db) {
        $lockUntil = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 minutes
        $query = "UPDATE users SET account_locked_until = :lock_until WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':lock_until', $lockUntil);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    // Update last login
    public function updateLastLogin($userId, $db) {
        $query = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    // Initiate password reset
    public function initiatePasswordReset($email, $db, $emailService) {
        $query = "SELECT id, first_name FROM users WHERE email = :email AND email_verified = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $token = $this->generateToken(32);
            $expiresAt = date('Y-m-d H:i:s', time() + (1 * 60 * 60)); // 1 hour
            
            // Delete existing reset tokens
            $deleteQuery = "DELETE FROM password_resets WHERE email = :email";
            $deleteStmt = $db->prepare($deleteQuery);
            $deleteStmt->bindParam(':email', $email);
            $deleteStmt->execute();
            
            // Insert new reset token
            $insertQuery = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':token', $token);
            $insertStmt->bindParam(':expires_at', $expiresAt);
            
            if ($insertStmt->execute()) {
                // Send reset email
                $sent = $emailService->sendPasswordResetEmail($email, $user['first_name'], $token);
                if ($sent) {
                    return ['success' => true, 'message' => 'Password reset instructions have been sent to your email.'];
                } else {
                    return ['success' => false, 'message' => 'Failed to send reset email. Please try again later.'];
                }
            }
        }
        
        return ['success' => false, 'message' => 'Email not found or not verified.'];
    }
    
    // Resend verification email
    public function resendVerificationEmail($email, $db, $emailService) {
        $query = "SELECT id, first_name, email_verified FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user['email_verified']) {
                return ['success' => false, 'message' => 'Email is already verified.'];
            }
            
            $token = $this->generateToken(32);
            $expiresAt = date('Y-m-d H:i:s', time() + (24 * 60 * 60)); // 24 hours
            
            // Update verification token
            $updateQuery = "UPDATE users SET email_verification_token = :token, email_verification_expires = :expires WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':token', $token);
            $updateStmt->bindParam(':expires', $expiresAt);
            $updateStmt->bindParam(':id', $user['id']);
            
            if ($updateStmt->execute()) {
                $sent = $emailService->sendVerificationEmail($email, $user['first_name'], $token, $user['id']);
                if ($sent) {
                    return ['success' => true, 'message' => 'Verification email has been resent. Please check your inbox.'];
                } else {
                    return ['success' => false, 'message' => 'Failed to send verification email. Please try again later.'];
                }
            }
        }
        
        return ['success' => false, 'message' => 'Email not found.'];
    }
}
?>