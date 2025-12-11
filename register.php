<?php
include "src/components/header.php";
?>

<style>
    .verification-step {
        display: none;
    }
    
    .verification-step.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }
    
    .registration-step {
        display: block;
    }
    
    .registration-step.hidden {
        display: none;
    }
    
    .verification-input {
        width: 100%;
        text-align: center;
        font-size: 1.2rem;
        font-weight: bold;
        letter-spacing: 5px;
    }
    
    .verification-info {
        background: rgba(212, 175, 55, 0.1);
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 12px;
        padding: 1rem;
        margin: 1rem 0;
        text-align: center;
    }
    
    .resend-code {
        color: var(--primary-gold);
        background: none;
        border: none;
        cursor: pointer;
        text-decoration: underline;
        font-size: 0.9rem;
        margin-top: 1rem;
    }
    
    .resend-code:hover {
        color: var(--light-gold);
    }
    
    .resend-code:disabled {
        color: var(--text-muted);
        cursor: not-allowed;
    }
    
    .timer {
        color: var(--primary-gold);
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    
    .step-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--text-muted);
        transition: all 0.3s ease;
    }
    
    .step-dot.active {
        background: var(--primary-gold);
        transform: scale(1.2);
    }

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
        --success-green: #10B981;
        --error-red: #EF4444;
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
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
    }

    .animate-fadeIn {
        animation: fadeIn 1s ease-out forwards;
        opacity: 0;
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

    .register-card {
        background: linear-gradient(135deg, var(--card-bg), var(--grill-dark));
        border-radius: 20px;
        border: 1px solid rgba(212, 175, 55, 0.25);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-width: 420px;
        width: 100%;
    }

    .register-card:hover {
        transform: translateY(-5px);
        box-shadow: 
            0 25px 50px rgba(212, 175, 55, 0.15),
            0 0 0 1px rgba(212, 175, 55, 0.2);
        border-color: rgba(212, 175, 55, 0.4);
    }

    .form-input {
        background: rgba(26, 26, 26, 0.8);
        border: 1px solid rgba(212, 175, 55, 0.3);
        color: var(--text-light);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        width: 100%;
        backdrop-filter: blur(10px);
        font-size: 1rem;
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-gold);
        box-shadow: 
            0 0 0 3px rgba(212, 175, 55, 0.15),
            0 0 20px rgba(212, 175, 55, 0.1);
        transform: translateY(-2px);
        background: rgba(26, 26, 26, 0.95);
    }

    .form-input.error {
        border-color: var(--error-red);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }

    .form-input.success {
        border-color: var(--success-green);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }

    .form-label {
        color: var(--text-light);
        font-weight: 500;
        margin-bottom: 0.75rem;
        display: block;
        font-size: 1rem;
    }

    .required-star {
        color: var(--error-red);
    }

    .password-container {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        transition: color 0.3s ease;
        font-size: 1.1rem;
    }

    .toggle-password:hover {
        color: var(--primary-gold);
    }

    .error-message {
        color: var(--error-red);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .error-message.show {
        display: block;
    }

    .success-message {
        color: var(--success-green);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .success-message.show {
        display: block;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
        color: var(--dark-bg);
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(212, 175, 55, 0.3);
        box-shadow: 
            0 4px 15px rgba(212, 175, 55, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 15px 30px rgba(212, 175, 55, 0.4),
            0 0 20px rgba(212, 175, 55, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        color: var(--dark-bg);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-light);
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .spinner-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        background: rgba(10, 10, 10, 0.8);
        backdrop-filter: blur(5px);
        border-radius: 20px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .spinner-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .spinner {
        width: 3rem;
        height: 3rem;
        border: 3px solid rgba(212, 175, 55, 0.3);
        border-top: 3px solid var(--primary-gold);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .auth-link {
        color: var(--primary-gold);
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .auth-link:hover {
        color: var(--light-gold);
    }

    .content-container {
        position: relative;
        z-index: 1;
    }

    .terms-checkbox {
        width: 1.1rem;
        height: 1.1rem;
        accent-color: var(--primary-gold);
    }

    .verification-code-container {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
    }

    .verification-code-input {
        flex: 1;
    }

    .send-code-btn {
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .register-card {
            margin: 1rem;
        }
    }

    @media (max-width: 640px) {
        .register-card {
            padding: 2rem;
        }
        
        .verification-code-container {
            flex-direction: column;
        }
        
        .send-code-btn {
            width: 100%;
        }
    }
</style>

<div class="grill-background">
    <div class="grill-pattern"></div>
</div>

<div class="min-h-screen flex items-center justify-center px-4 pt-24 pb-8 content-container">
    <div class="register-card p-8 space-y-6 animate-fadeInUp">
        
        <div id="spinner" class="spinner-overlay">
            <div class="spinner"></div>
        </div>

        <div class="text-center">
            <h1 class="text-3xl font-bold text-yellow-400 mb-2" id="formTitle">Create Account</h1>
            <p class="text-yellow-200 text-sm" id="formSubtitle">Join Ultimate Liempo Haus</p>
        </div>

        <!-- REGISTRATION STEP -->
        <div class="registration-step" id="registrationStep">
            <form id="frmRegister" class="space-y-5">
                <!-- EMAIL -->
                <div>
                    <label for="email" class="form-label">EMAIL:</label>
                    <input type="email" id="email" name="email" 
                           class="form-input" 
                           placeholder="Enter your email"
                           required>
                    <div id="email_msg" class="error-message">Please enter a valid email address</div>
                </div>

                <!-- PASSWORD -->
                <div>
                    <label for="password" class="form-label">PASSWORD:</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" 
                               class="form-input pr-12" 
                               placeholder="Enter your password"
                               required
                               minlength="6">
                        <button type="button" class="toggle-password" id="togglePassword">
                            👁️
                        </button>
                    </div>
                    <div id="password_msg" class="error-message">Password must be at least 6 characters long and contain both letters and numbers</div>
                </div>

                <!-- VERIFICATION CODE -->
                <div>
                    <label class="form-label">VERIFICATION CODE:</label>
                    <div class="verification-code-container">
                        <input type="text" id="verification_code" name="verification_code" 
                               class="form-input verification-code-input" 
                               placeholder="Enter code"
                               maxlength="6">
                        <button type="button" id="btnSendCode" class="btn-primary send-code-btn">
                            SEND CODE
                        </button>
                    </div>
                    <div id="verification_msg" class="error-message"></div>
                    <div class="text-center mt-2">
                        <button type="button" id="btnResend" class="resend-code" disabled>
                            Resend code <span id="timer">(60s)</span>
                        </button>
                    </div>
                </div>

                <!-- TERMS -->
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="terms" name="terms" required
                           class="terms-checkbox mt-1">
                    <label for="terms" class="text-sm text-yellow-200">
                        I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#" class="auth-link">Privacy Policy</a>
                    </label>
                </div>
                <div id="terms_msg" class="error-message">You must accept the Terms of Service and Privacy Policy</div>

                <!-- REGISTER BUTTON -->
                <button type="submit" id="btnRegister" class="btn-primary w-full">
                    <span>CREATE ACCOUNT</span>
                </button>
            </form>
        </div>

        <!-- SIGN IN LINK -->
        <div class="text-center text-sm text-yellow-400 pt-4 border-t border-gray-700">
            <p class="mb-2">Already have an account?</p>
            <a href="login.php" class="auth-link">SIGN IN</a>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form elements
        const form = document.getElementById('frmRegister');
        const spinner = document.getElementById('spinner');
        const submitButton = document.getElementById('btnRegister');
        const sendCodeButton = document.getElementById('btnSendCode');
        const resendButton = document.getElementById('btnResend');
        const timerElement = document.getElementById('timer');

        // Input fields
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        const verificationField = document.getElementById('verification_code');
        const termsCheckbox = document.getElementById('terms');
        const togglePasswordBtn = document.getElementById('togglePassword');

        // Error messages
        const emailMsg = document.getElementById('email_msg');
        const passwordMsg = document.getElementById('password_msg');
        const verificationMsg = document.getElementById('verification_msg');
        const termsMsg = document.getElementById('terms_msg');

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let resendTimer = 60;
        let resendInterval;
        let verificationCode = '';

        // Event listeners
        emailField.addEventListener('input', validateEmailField);
        passwordField.addEventListener('input', validatePasswordField);
        termsCheckbox.addEventListener('change', validateTerms);
        verificationField.addEventListener('input', validateVerificationCode);
        togglePasswordBtn.addEventListener('click', function() {
            togglePasswordVisibility(passwordField, this);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateForm()) {
                verifyAndRegister();
            }
        });

        sendCodeButton.addEventListener('click', sendVerificationCode);
        resendButton.addEventListener('click', resendVerificationCode);

        // Functions
        function togglePasswordVisibility(field, button) {
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            button.textContent = type === 'password' ? '👁️' : '🔒';
        }

        function validateEmailField() {
            const value = emailField.value.trim();
            
            if (value === '') {
                setFieldState(emailField, false);
                emailMsg.classList.remove('show');
                return false;
            } else if (emailPattern.test(value)) {
                setFieldState(emailField, true);
                emailMsg.classList.remove('show');
                return true;
            } else {
                setFieldState(emailField, false);
                emailMsg.textContent = 'Please enter a valid email address';
                emailMsg.classList.add('show');
                return false;
            }
        }

        function validatePasswordField() {
            const value = passwordField.value;
            
            if (value === '') {
                setFieldState(passwordField, false);
                passwordMsg.classList.remove('show');
                return false;
            } else if (value.length < 6) {
                setFieldState(passwordField, false);
                passwordMsg.textContent = 'Password must be at least 6 characters long';
                passwordMsg.classList.add('show');
                return false;
            } else if (!hasNumberAndLetter(value)) {
                setFieldState(passwordField, false);
                passwordMsg.textContent = 'Password must contain both letters and numbers';
                passwordMsg.classList.add('show');
                return false;
            } else {
                setFieldState(passwordField, true);
                passwordMsg.classList.remove('show');
                return true;
            }
        }

        function hasNumberAndLetter(password) {
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            return hasLetter && hasNumber;
        }

        function validateVerificationCode() {
            const value = verificationField.value.replace(/[^0-9]/g, '');
            verificationField.value = value;
            
            if (value === '') {
                setFieldState(verificationField, false);
                verificationMsg.classList.remove('show');
                return false;
            } else if (value.length === 6) {
                setFieldState(verificationField, true);
                verificationMsg.classList.remove('show');
                return true;
            } else {
                setFieldState(verificationField, false);
                verificationMsg.textContent = 'Verification code must be 6 digits';
                verificationMsg.classList.add('show');
                return false;
            }
        }

        function validateTerms() {
            if (termsCheckbox.checked) {
                termsMsg.classList.remove('show');
                return true;
            } else {
                termsMsg.classList.add('show');
                return false;
            }
        }

        function setFieldState(field, isValid) {
            field.classList.remove('error', 'success');
            if (isValid) {
                field.classList.add('success');
            } else {
                field.classList.add('error');
            }
        }

        function validateForm() {
            const isEmailValid = validateEmailField();
            const isPasswordValid = validatePasswordField();
            const isVerificationValid = validateVerificationCode();
            const isTermsValid = validateTerms();
            
            return isEmailValid && isPasswordValid && isVerificationValid && isTermsValid;
        }

        function showLoadingState() {
            spinner.classList.add('active');
        }

        function hideLoadingState() {
            spinner.classList.remove('active');
        }

        function sendVerificationCode() {
            if (!validateEmailField()) {
                showErrorMessage('Please enter a valid email address first');
                return;
            }

            showLoadingState();
            sendCodeButton.disabled = true;
            sendCodeButton.innerHTML = 'SENDING...';

            const formData = new FormData();
            formData.append('requestType', 'SendVerificationCode');
            formData.append('email', emailField.value.trim());

            fetch('../controller/end-points/controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.log('Send code response:', text);
                
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success' || data.status === 'verification_required') {
                        verificationCode = data.verification_code || '123456'; // Fallback for testing
                        showSuccessMessage('Verification code sent to your email!');
                        startResendTimer();
                        verificationField.focus();
                    } else {
                        showErrorMessage(data.message || 'Failed to send verification code');
                    }
                } catch (e) {
                    if (text.includes('success') || text.includes('verification')) {
                        showSuccessMessage('Verification code sent!');
                        startResendTimer();
                        verificationField.focus();
                    } else {
                        showErrorMessage('Failed to send verification code');
                    }
                }
            })
            .catch(error => {
                showErrorMessage('Network error: ' + error.message);
            })
            .finally(() => {
                hideLoadingState();
                sendCodeButton.disabled = false;
                sendCodeButton.innerHTML = 'SEND CODE';
            });
        }

        function verifyAndRegister() {
            showLoadingState();
            submitButton.disabled = true;
            submitButton.innerHTML = 'CREATING ACCOUNT...';

            const formData = new FormData();
            formData.append('requestType', 'RegisterWithVerification');
            formData.append('email', emailField.value.trim());
            formData.append('password', passwordField.value);
            formData.append('verification_code', verificationField.value);

            fetch('../controller/end-points/controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.log('Register response:', text);
                
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        showSuccessMessage('Account created successfully! Redirecting...');
                        // Use redirect provided by server if available, else fall back to login page
                        const redirectUrl = data.redirect || 'login.php';
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                    } else {
                        showErrorMessage(data.message || 'Registration failed');
                    }
                } catch (e) {
                    if (text.includes('success')) {
                        showSuccessMessage('Account created successfully! Redirecting...');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showErrorMessage('Registration failed. Please try again.');
                    }
                }
            })
            .catch(error => {
                showErrorMessage('Network error: ' + error.message);
            })
            .finally(() => {
                hideLoadingState();
                submitButton.disabled = false;
                submitButton.innerHTML = 'CREATE ACCOUNT';
            });
        }

        function resendVerificationCode() {
            sendVerificationCode();
        }

        function startResendTimer() {
            resendTimer = 60;
            resendButton.disabled = true;
            sendCodeButton.disabled = true;
            clearInterval(resendInterval);
            
            resendInterval = setInterval(() => {
                resendTimer--;
                timerElement.textContent = `(${resendTimer}s)`;
                
                if (resendTimer <= 0) {
                    clearInterval(resendInterval);
                    resendButton.disabled = false;
                    sendCodeButton.disabled = false;
                    timerElement.textContent = '';
                }
            }, 1000);
        }

        function showSuccessMessage(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg z-50';
            successDiv.textContent = message;
            document.body.appendChild(successDiv);
            
            setTimeout(() => {
                successDiv.remove();
            }, 5000);
        }

        function showErrorMessage(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-600 text-white p-4 rounded-lg shadow-lg z-50';
            errorDiv.textContent = 'Error: ' + message;
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }

        // Initialize form validation
        validateEmailField();
        validatePasswordField();
        validateTerms();

        // Enable/disable register button based on form validity
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const isFormValid = validateForm();
                submitButton.disabled = !isFormValid;
            });
        });
    });
</script>

<?php
include "src/components/footer.php";
?>