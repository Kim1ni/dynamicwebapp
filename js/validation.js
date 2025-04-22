document.addEventListener('DOMContentLoaded', function() {
    // Check which form is on the page
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');
    const dataForm = document.getElementById('dataForm');
    
    // Register form validation
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Username validation (at least 4 characters)
            const username = document.getElementById('username');
            const usernameError = document.getElementById('username-error');
            
            if (username.value.length < 4) {
                usernameError.textContent = 'Username must be at least 4 characters';
                isValid = false;
            } else {
                usernameError.textContent = '';
            }
            
            // Email validation (using regex for basic format check)
            const email = document.getElementById('email');
            const emailError = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email.value)) {
                emailError.textContent = 'Please enter a valid email address';
                isValid = false;
            } else {
                emailError.textContent = '';
            }
            
            // Password validation (at least 8 characters with 1 number, 1 uppercase)
            const password = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
            
            if (!passwordRegex.test(password.value)) {
                passwordError.textContent = 'Password must be at least 8 characters with 1 number and 1 uppercase letter';
                isValid = false;
            } else {
                passwordError.textContent = '';
            }
            
            // Password confirmation
            const confirmPassword = document.getElementById('confirm-password');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            
            if (password.value !== confirmPassword.value) {
                confirmPasswordError.textContent = 'Passwords do not match';
                isValid = false;
            } else {
                confirmPasswordError.textContent = '';
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Login form validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Simple presence check
            const username = document.getElementById('username');
            const usernameError = document.getElementById('username-error');
            
            if (username.value.trim() === '') {
                usernameError.textContent = 'Username is required';
                isValid = false;
            } else {
                usernameError.textContent = '';
            }
            
            const password = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            
            if (password.value === '') {
                passwordError.textContent = 'Password is required';
                isValid = false;
            } else {
                passwordError.textContent = '';
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Data form validation
    if (dataForm) {
        dataForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Title validation (non-empty and at least 3 characters)
            const title = document.getElementById('title');
            const titleError = document.getElementById('title-error');
            
            if (title.value.trim().length < 3) {
                titleError.textContent = 'Title must be at least 3 characters';
                isValid = false;
            } else {
                titleError.textContent = '';
            }
            
            // Category validation (must select an option)
            const category = document.getElementById('category');
            const categoryError = document.getElementById('category-error');
            
            if (category.value === '') {
                categoryError.textContent = 'Please select a category';
                isValid = false;
            } else {
                categoryError.textContent = '';
            }
            
            // Content validation (at least 10 characters)
            const content = document.getElementById('content');
            const contentError = document.getElementById('content-error');
            
            if (content.value.trim().length < 10) {
                contentError.textContent = 'Content must be at least 10 characters';
                isValid = false;
            } else {
                contentError.textContent = '';
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});