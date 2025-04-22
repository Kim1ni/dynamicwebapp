<?php
// Add at the top of each main PHP file
require_once 'includes/error_handler.php';
require_once 'includes/session.php';
secureSessionStart();
setSecurityHeaders();

// Auto-login from remember_me cookie
if (!isLoggedIn() && isset($_COOKIE['remember_me'])) {
    list($userId, $token) = explode(':', $_COOKIE['remember_me']);
    
    if (validateRememberMeToken($userId, $token)) {
        // Get user details
        $sql = "SELECT id, username FROM users WHERE id = ?";
        $result = executeQuery($sql, "i", [$userId]);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Regenerate the session ID for security
            session_regenerate_id(true);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Web Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to Our Web Application</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="intro">
            <h2>About This Application</h2>
            <p>This is a dynamic web application where users can register, log in, and submit forms.</p>
            
            <?php if (!isLoggedIn()): ?>
                <div class="cta-buttons">
                    <a href="register.php" class="btn">Register Now</a>
                    <a href="login.php" class="btn">Login</a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="dashboard.php" class="btn">Go to Dashboard</a>
                    <a href="form.php" class="btn">Submit a Form</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Dynamic Web Application</p>
    </footer>
    
    <script src="js/main.js"></script>
</body>
</html>