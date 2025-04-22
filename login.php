<?php
//My libraries
require_once 'includes/error_handler.php';
require_once 'includes/session.php';
//Secure session start and set security headers
secureSessionStart();
setSecurityHeaders();

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

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
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        }
    }
}

// Include required files
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security.php';

$csrf_token = generateCSRFToken();

$errors = [];
$username = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid form submission";
    } else {
        // Validate and sanitize input
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        
        // Check if account is locked due to too many failed attempts
        if (isAccountLocked($username)) {
            $errors[] = "Account is temporarily locked due to multiple failed login attempts. Please try again later.";
        } else {
            // Validate login
            $user = validateLogin($username, $password);
            
            if ($user) {
                // Track successful login
                trackLoginAttempt($username, true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Handle "Remember Me"
                if (isset($_POST['remember-me'])) {
                    // Generate token and set cookie (30 days expiry)
                    $token = generateRememberMeToken();
                    $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));
                    
                    storeRememberMeToken($user['id'], $token, $expiry);
                    
                    // Set cookie
                    setcookie(
                        'remember_me', 
                        $user['id'] . ':' . $token, 
                        time() + (30 * 24 * 60 * 60),
                        '/',
                        '',
                        true,   // Secure
                        true    // HttpOnly
                    );
                }
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Track failed login
                trackLoginAttempt($username, false);
                
                $errors[] = "Invalid username or password";
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
    <title>Login - Dynamic Web Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Login</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="form-container">
            <h2>Log In to Your Account</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="error-container">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" action="login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <span class="error" id="username-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error" id="password-error"></span>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember-me" name="remember-me">
                    <label for="remember-me">Remember me</label>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Dynamic Web Application</p>
    </footer>
    
    <script src="js/validation.js"></script>
</body>
</html>