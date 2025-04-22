<?php
// Add at the top of each main PHP file
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

// Get user information
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's form submissions
$formSubmissions = getUserFormSubmissions($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dynamic Web Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="form.php">Submit Form</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>This is your personal dashboard.</p>
        </section>
        
        <section class="user-data">
            <h3>Your Submitted Forms</h3>
            <div id="form-submissions">
                <?php if (empty($formSubmissions)): ?>
                    <p>No forms submitted yet.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formSubmissions as $submission): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($submission['title']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['category']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Dynamic Web Application</p>
    </footer>
</body>
</html>