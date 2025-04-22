<?php
// Add at the top of each main PHP file
require_once 'includes/error_handler.php';
require_once 'includes/session.php';
secureSessionStart();
setSecurityHeaders();

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Include required files
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

$csrf_token = generateCSRFToken();

$errors = [];
$username = $email = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid form submission";
    } else {
        // Validate and sanitize input
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];
        
        // Validate username
        if (strlen($username) < 4) {
            $errors[] = "Username must be at least 4 characters";
        } elseif (usernameExists($username)) {
            $errors[] = "Username already exists";
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (emailExists($email)) {
            $errors[] = "Email already exists";
        }
        
        // Validate password
        $passwordValidation = validatePasswordStrength($password);
        if ($passwordValidation !== true) {
            $errors[] = $passwordValidation;
        }

        
        // Validate password confirmation
        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }
        
        // If no errors, register the user
        if (empty($errors)) {
            $userId = registerUser($username, $email, $password);
            
            if ($userId) {
                // Set session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Registration failed. Please try again.";
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
    <title>Register - Dynamic Web Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Register</h1>
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
            <h2>Create an Account</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="error-container">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form id="registerForm" action="register.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <span class="error" id="username-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span class="error" id="email-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error" id="password-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                    <span class="error" id="confirm-password-error"></span>
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Dynamic Web Application</p>
    </footer>
    
    <script src="js/validation.js"></script>
</body>
</html>