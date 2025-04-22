<?php
require_once 'includes/error_handler.php';
require_once 'includes/session.php';
secureSessionStart();
setSecurityHeaders();

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Include required files
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security.php';

$csrf_token = generateCSRFToken();

$errors = [];
$success = false;
$title = $category = $content = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid form submission";
    } else if (!checkFormSubmissionRate($_SESSION['user_id'])) {
        $errors[] = "You have reached the maximum number of form submissions per hour. Please try again later.";
    } else {
        // Validate and sanitize input
        $title = sanitizeInput($_POST['title']);
        $category = sanitizeInput($_POST['category']);
        $content = sanitizeInput($_POST['content']);
        
        // Validate title
        if (strlen($title) < 3) {
            $errors[] = "Title must be at least 3 characters";
        }
        
        // Validate category
        $validCategories = ['general', 'technical', 'feedback', 'other'];
        if (!in_array($category, $validCategories)) {
            $errors[] = "Please select a valid category";
        }
        
        // Validate content
        if (strlen($content) < 10) {
            $errors[] = "Content must be at least 10 characters";
        }
        
        // If no errors, add form submission
        if (empty($errors)) {
            $userId = $_SESSION['user_id'];
            $submissionId = addFormSubmission($userId, $title, $category, $content);
            
            if ($submissionId) {
                $success = true;
                $title = $category = $content = "";
                
                // Refresh CSRF token after successful submission
                $csrf_token = refreshCSRFToken();
            } else {
                $errors[] = "Form submission failed. Please try again.";
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
    <title>Submit Form - Dynamic Web Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Submit Form</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="form.php">Submit Form</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="form-container">
            <h2>Submit a New Form</h2>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <p>Form submitted successfully!</p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-container">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form id="dataForm" action="form.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                    <span class="error" id="title-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="general" <?php if ($category == 'general') echo 'selected'; ?>>General</option>
                        <option value="technical" <?php if ($category == 'technical') echo 'selected'; ?>>Technical</option>
                        <option value="feedback" <?php if ($category == 'feedback') echo 'selected'; ?>>Feedback</option>
                        <option value="other" <?php if ($category == 'other') echo 'selected'; ?>>Other</option>
                    </select>
                    <span class="error" id="category-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($content); ?></textarea>
                    <span class="error" id="content-error"></span>
                </div>
                
                <button type="submit" class="btn">Submit Form</button>
            </form>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Dynamic Web Application</p>
    </footer>
    
    <script src="js/validation.js"></script>
</body>
</html>