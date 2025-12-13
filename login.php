<?php
session_start();

// Only redirect if user is already logged in AND not trying to access login page
if (isset($_SESSION['user_id']) && isset($_SESSION['user_position']) && !isset($_GET['force_login'])) {
    $position = $_SESSION['user_position'];
    if ($position === 'customer') {
        header('Location: customer/home.php');
        exit;
    } else if ($position === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    } else if ($position === 'headstaff') {
        header('Location: headstaff/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ultimate Liempo Haus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-900">
    
    <?php include 'src/components/header.php'; ?>

    <div class="grill-background">
        <div class="grill-pattern"></div>
        <div class="grill-overlay"></div>
        <div class="grill-elements">
            <div class="grill-element"></div>
            <div class="grill-element"></div>
            <div class="grill-element"></div>
            <div class="smoke"></div>
            <div class="smoke"></div>
            <div class="smoke"></div>
        </div>
    </div>

    <div class="min-h-screen flex items-center justify-center px-4 py-8 pt-24">
        <div class="login-card w-full max-w-md p-8 space-y-8 animate-fadeInUp">
            
            <div class="flex flex-col items-center space-y-4">
                <img src="static/logo.jpg" alt="Ultimate Liempo Haus Logo" 
                     class="logo-image w-24 h-24 rounded-full animate-float">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-yellow-400 mb-2">Welcome Back</h1>
                    <p class="text-yellow-200 text-sm">Sign in to your Ultimate Liempo Haus account</p>
                </div>
            </div>

            <form method="POST" class="space-y-6" id="loginForm">
                <input type="hidden" name="login" value="1">
                
                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" required
                           class="form-input" 
                           placeholder="Enter your email address"
                           autocomplete="email">
                    <div class="error-message" id="emailError"></div>
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" required
                           class="form-input" 
                           placeholder="Enter your password"
                           autocomplete="current-password">
                    <div class="error-message" id="passwordError"></div>
                </div>

                <button type="submit" class="btn-primary w-full" id="submitBtn">
                    <span class="material-icons mr-2">login</span>
                    <span>Sign In</span>
                </button>
            </form>

            <div class="text-center text-sm text-yellow-400 pt-4 border-t border-gray-700">
                <p class="mb-2">Don't have an account?</p>
                <a href="register.php" class="auth-link">
                    <span class="material-icons mr-1 text-sm">person_add</span>
                    Create Account Here
                </a>
            </div>

        </div>
    </div>

    <?php include 'src/components/footer.php'; ?>

    <style>
        :root {
            --primary-gold: #D4AF37;
            --dark-gold: #B8860B;
            --light-gold: #F5E8C8;
            --dark-bg: #0A0A0A;
            --card-bg: #1A1A1A;
            --text-light: #E5E5E5;
            --text-muted: #A3A3A3;
            --grill-dark: #2A2A2A;
            --grill-light: #3A3A3A;
            --error-red: #EF4444;
            --success-green: #10B981;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-10px) rotate(2deg);
            }
            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        @keyframes grillGlow {
            0%, 100% {
                opacity: 0.3;
                box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
            }
            50% {
                opacity: 1;
                box-shadow: 0 0 20px rgba(212, 175, 55, 0.8);
            }
        }

        @keyframes smoke {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100vh) scale(3);
                opacity: 0;
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        .grill-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1a1a 100%);
        }

        .grill-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(90deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px),
                linear-gradient(0deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px);
            background-size: 50px 50px;
            opacity: 0.08;
        }

        .grill-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(184, 134, 11, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(212, 175, 55, 0.06) 0%, transparent 50%);
        }

        .grill-elements {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .grill-element {
            position: absolute;
            background: linear-gradient(45deg, var(--primary-gold), var(--dark-gold));
            border-radius: 2px;
            animation: grillGlow 3s ease-in-out infinite;
        }

        .grill-element:nth-child(1) {
            width: 80px;
            height: 3px;
            top: 20%;
            left: 15%;
            animation-delay: 0s;
        }

        .grill-element:nth-child(2) {
            width: 3px;
            height: 60px;
            top: 30%;
            right: 20%;
            animation-delay: 1s;
        }

        .grill-element:nth-child(3) {
            width: 70px;
            height: 3px;
            bottom: 25%;
            left: 25%;
            animation-delay: 2s;
        }

        .smoke {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(212, 175, 55, 0.25);
            border-radius: 50%;
            animation: smoke 6s linear infinite;
        }

        .smoke:nth-child(4) { left: 20%; bottom: 15%; animation-delay: 0s; }
        .smoke:nth-child(5) { left: 30%; bottom: 20%; animation-delay: 2s; }
        .smoke:nth-child(6) { right: 25%; bottom: 18%; animation-delay: 4s; }

        .login-card {
            background: linear-gradient(135deg, var(--card-bg), var(--grill-dark));
            border-radius: 20px;
            border: 1px solid rgba(212, 175, 55, 0.25);
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(212, 175, 55, 0.15);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .logo-image {
            border: 3px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            transition: all 0.3s ease;
        }

        .login-card:hover .logo-image {
            border-color: var(--primary-gold);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.4);
        }

        .form-input {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: var(--text-light);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
            transform: translateY(-2px);
        }

        .form-input:hover {
            border-color: rgba(212, 175, 55, 0.5);
        }

        .form-input.error {
            border-color: var(--error-red);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .form-label {
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1rem;
        }

        .error-message {
            color: var(--error-red);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            color: var(--dark-bg);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.4);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(10, 10, 10, 0.3);
            border-top: 2px solid var(--dark-bg);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .auth-link {
            color: var(--primary-gold);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .auth-link:hover {
            color: var(--light-gold);
            transform: translateY(-1px);
        }

        .alert-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: linear-gradient(135deg, var(--error-red), #dc2626);
            color: white;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-green), #0da172);
            color: white;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        @media (max-width: 768px) {
            .grill-element, .smoke {
                display: none;
            }
            .alert-message {
                top: 10px;
                right: 10px;
                left: 10px;
            }
            .login-card {
                margin: 1rem;
            }
        }

        .material-icons {
            font-size: 1.2em;
        }
    </style>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let isValid = true;

            document.getElementById('emailError').classList.remove('show');
            document.getElementById('passwordError').classList.remove('show');

            if (!email) {
                document.getElementById('emailError').textContent = 'Email is required';
                document.getElementById('emailError').classList.add('show');
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                document.getElementById('emailError').textContent = 'Please enter a valid email address';
                document.getElementById('emailError').classList.add('show');
                isValid = false;
            }

            if (!password) {
                document.getElementById('passwordError').textContent = 'Password is required';
                document.getElementById('passwordError').classList.add('show');
                isValid = false;
            }

            if (!isValid) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="spinner"></div><span class="ml-2">Signing In...</span>';
            
            const formData = new FormData(this);
            formData.append('requestType', 'Login');
            
            fetch('controller/end-points/controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                // return response.json();
                console.log(response);
            })
            .then(data => {
                console.log('Login response:', data);
                
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    
                    setTimeout(() => {
                        if (data.user_position === 'customer') {
                            window.location.href = 'customer/home.php';
                        } else if (data.user_position === 'admin') {
                            window.location.href = 'admin/dashboard.php';
                        } else if (data.user_position === 'headstaff') {
                            window.location.href = 'headstaff/dashboard.php';
                        } else {
                            window.location.href = 'customer/home.php';
                        }
                    }, 1000);
                } else {
                    showAlert(data.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-icons mr-2">login</span><span>Sign In</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Login failed. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-icons mr-2">login</span><span>Sign In</span>';
            });
        });

        function showAlert(message, type) {
            document.querySelectorAll('.alert-message').forEach(alert => alert.remove());
            
            const alert = document.createElement('div');
            alert.className = `alert-message alert-${type}`;
            
            const icon = document.createElement('span');
            icon.className = 'material-icons';
            icon.textContent = type === 'error' ? 'error' : 'check_circle';
            
            alert.appendChild(icon);
            alert.appendChild(document.createTextNode(message));
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('animate-pulse');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('animate-pulse');
            });
        });
    </script>
</body>
</html>