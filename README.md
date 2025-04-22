# Dynamic Web Application

A secure web application that allows users to register, log in, and submit forms that store data in a MySQL database.

## Features

- User registration and authentication
- Secure login with "Remember Me" functionality
- Form submission and storage
- Client-side and server-side validation
- Protection against common security threats (XSS, CSRF, SQL Injection)
- Session management using secure cookies
- Responsive design for various devices

## Technical Implementation

### Security Measures

- Password hashing using PHP's password_hash function
- CSRF protection with tokens
- Prepared statements to prevent SQL injection
- Input sanitization to prevent XSS attacks
- Secure cookie handling for sessions
- Brute force protection with account lockouts
- Content Security Policy implementation

### Technologies Used

- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL
- Server: Apache (XAMPP)

## Installation

1. Clone or download the repository
2. Place the files in your web server's document root
3. Create a MySQL database named `dynamic_web_app`
4. Import the database structure from `database.sql`
5. Configure database connection in `includes/config.php`
6. Access the application through your web browser

## File Structure

- `index.php` - Home page
- `register.php` - User registration
- `login.php` - User login
- `dashboard.php` - User dashboard
- `form.php` - Form submission
- `logout.php` - User logout
- `includes/` - PHP function files and configuration
- `css/` - Stylesheets
- `js/` - JavaScript files