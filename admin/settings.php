<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";

// Database connection using MySQLi (same as your other file)
$host = "localhost";
 $username = "u777088444_grillbook";
    $password = "Grillbook123@";
    $database = "u777088444_grillbook";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['first_name'])) {
        // Update account information
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        
        try {
            $stmt = $conn->prepare("UPDATE user SET user_fname = ?, user_lname = ?, user_email = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
            $stmt->execute();
            
            // Update session data
            $_SESSION['user_fname'] = $first_name;
            $_SESSION['user_lname'] = $last_name;
            $_SESSION['user_email'] = $email;
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_user = $result->fetch_assoc();
            
            $account_success = "Account information updated successfully!";
        } catch (Exception $e) {
            $account_error = "Error updating account information: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['current_password'])) {
        // Update password
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        if (password_verify($current_password, $current_user['user_password'])) {
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    if (strlen($new_password) >= 8) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        try {
                            $stmt = $conn->prepare("UPDATE user SET user_password = ? WHERE user_id = ?");
                            $stmt->bind_param("si", $hashed_password, $user_id);
                            $stmt->execute();
                            $password_success = "Password updated successfully!";
                        } catch (Exception $e) {
                            $password_error = "Error updating password: " . $e->getMessage();
                        }
                    } else {
                        $password_error = "New password must be at least 8 characters long";
                    }
                } else {
                    $password_error = "New passwords do not match";
                }
            } else {
                $password_error = "Please enter a new password";
            }
        } else {
            $password_error = "Current password is incorrect";
        }
    }
}
?>

<style>
/* Enhanced Main Background - Professional Version */
body {
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 255, 240, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 248, 225, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 250, 230, 0.2) 0%, transparent 50%),
        linear-gradient(135deg, #f8f4e5 0%, #fff9e6 30%, #f5f1e0 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
    overflow-y: auto;
    margin: 0;
    padding: 0;
}

/* Remove body overflow and ensure proper fit */
html, body {
    overflow-x: hidden;
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
}

/* Remove any footer spacing */
.container, .main-content {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Enhanced Typography for Professional Readability */
body {
    font-family: 'Segoe UI', 'Arial', sans-serif;
    line-height: 1.6;
    color: #1a202c;
    font-weight: 400;
}

.high-contrast-text {
    color: #000000;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.9);
}

.xl-text {
    font-size: 1.125rem;
    line-height: 1.6;
    font-weight: 500;
}

.xxl-text {
    font-size: 1.25rem;
    line-height: 1.5;
    font-weight: 600;
}

/* Enhanced Glass Morphism Effect - Professional Version */
.glass-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.08),
        0 4px 12px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.glass-card:hover {
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.12),
        0 6px 16px rgba(0, 0, 0, 0.05);
}

/* Enhanced Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #d97706, #b45309);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #b45309, #92400e);
}

/* Enhanced Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse-gold {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(217, 119, 6, 0);
    }
}

.pulse-dot {
    animation: pulse-gold 2s infinite;
}

/* Enhanced Interactive Elements */
.quick-action-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    border: 2px solid;
    border-color: #92400e;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.25);
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.35);
}

/* Enhanced Focus States */
input:focus, textarea:focus, select:focus {
    transform: scale(1.02);
    box-shadow: 
        0 0 0 3px rgba(217, 119, 6, 0.15), 
        0 4px 20px rgba(0, 0, 0, 0.08);
    border-color: #d97706;
    outline: none;
}

/* Professional Button Enhancements */
.btn-primary {
    background: linear-gradient(135deg, #d97706, #b45309);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    border: 2px solid #92400e;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.25);
    padding: 1rem 2rem;
    border-radius: 12px;
    font-size: 1.125rem;
    min-width: 200px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    cursor: pointer;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.35);
    background: linear-gradient(135deg, #b45309, #92400e);
}

/* Enhanced Form Elements */
.form-input-enhanced {
    background: white;
    border: 2px solid #e2e8f0;
    color: #1a202c;
    padding: 1rem 1.5rem 1rem 4rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    width: 100%;
    font-size: 1.125rem;
    font-weight: 400;
}

.form-input-enhanced:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
    outline: none;
    background: #fffdf6;
}

.form-input-enhanced::placeholder {
    color: #4a5568;
    font-weight: 400;
}

/* Professional Color Variables */
:root {
    --primary-gold: #d97706;
    --dark-gold: #b45309;
    --darker-gold: #92400e;
    --light-gold: #fef3c7;
    --text-dark: #000000;
    --text-dark-alt: #1a202c;
    --text-muted: #2d3748;
    --border-light: #e2e8f0;
    --background-light: #f8f4e5;
}

/* Professional Icon Styling - Enhanced Visibility */
.icon-container {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    padding: 12px;
    background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
}

.icon-container i {
    color: white !important;
    font-size: 1.25rem;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}

/* Input Icons with Better Visibility and Spacing */
.input-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #4a5568 !important;
    z-index: 10;
    font-size: 1.1rem;
    opacity: 1 !important;
    visibility: visible !important;
    width: 24px;
    text-align: center;
}

.input-icon i {
    color: #4a5568 !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Enhanced Password Toggle Button */
.password-toggle {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #4a5568 !important;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.3s ease;
    opacity: 1 !important;
    visibility: visible !important;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    background: #f7fafc;
    color: var(--primary-gold) !important;
}

.password-toggle i {
    color: inherit !important;
    opacity: 1 !important;
    visibility: visible !important;
    font-size: 1.1rem;
}

/* Responsive Typography */
@media (max-width: 768px) {
    .xl-text {
        font-size: 1rem;
    }
    
    .xxl-text {
        font-size: 1.125rem;
    }
    
    .icon-container {
        padding: 10px;
    }
    
    .icon-container i {
        font-size: 1.1rem;
    }
    
    .btn-primary {
        min-width: 180px;
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
    
    .form-input-enhanced {
        padding: 0.875rem 1.25rem 0.875rem 3.5rem;
    }
}

/* Professional Focus Management */
*:focus {
    outline: 3px solid rgba(217, 119, 6, 0.3);
    outline-offset: 2px;
}

/* Enhanced Selection Styling */
::selection {
    background: #d97706;
    color: white;
    text-shadow: none;
}

/* Professional Loading States */
.loading-spinner {
    display: inline-block;
    width: 24px;
    height: 24px;
    border: 3px solid rgba(217, 119, 6, 0.2);
    border-top: 3px solid #d97706;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Professional Password Strength Indicator */
.password-strength {
    height: 6px;
    border-radius: 3px;
    transition: all 0.3s ease;
    margin-top: 8px;
}

.strength-weak { 
    background: linear-gradient(90deg, #dc2626, #ef4444); 
    width: 25%; 
}
.strength-fair { 
    background: linear-gradient(90deg, #f59e0b, #fbbf24); 
    width: 50%; 
}
.strength-good { 
    background: linear-gradient(90deg, #10b981, #34d399); 
    width: 75%; 
}
.strength-strong { 
    background: linear-gradient(90deg, #059669, #10b981); 
    width: 100%; 
}

/* Professional Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 32px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e0;
    transition: .4s;
    border-radius: 34px;
    border: 2px solid transparent;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

input:checked + .toggle-slider {
    background-color: #d97706;
    border-color: #b45309;
}

input:checked + .toggle-slider:before {
    transform: translateX(28px);
}

/* Enhanced Input Group Styling */
.input-group {
    position: relative;
    margin-bottom: 1.5rem;
}

/* Required Field Indicator */
.required-field::after {
    content: " *";
    color: #ef4444;
    font-weight: 600;
}

/* Professional Status Messages */
.status-success {
    color: #059669;
    font-weight: 500;
}

.status-error {
    color: #dc2626;
    font-weight: 500;
}

.status-warning {
    color: #d97706;
    font-weight: 500;
}

.status-info {
    color: #2563eb;
    font-weight: 500;
}

/* Toast Progress Animation */
.toast-progress {
    animation: toastProgress 5s linear forwards;
    height: 3px;
    border-radius: 2px;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Professional Icon Visibility - CRITICAL FIX */
.fas, .fa-user-cog, .fa-user, .fa-envelope, .fa-save, .fa-shield-alt, 
.fa-lock, .fa-key, .fa-eye, .fa-eye-slash, .fa-check, .fa-times, 
.fa-exclamation-circle, .fa-cog, .fa-sync-alt, .fa-check-circle,
.fa-exclamation-triangle, .fa-info-circle {
    color: inherit !important;
    opacity: 1 !important;
    visibility: visible !important;
    font-style: normal;
    font-weight: 900;
}

/* Ensure all icons are properly visible in different contexts */
.btn-primary i,
.quick-action-btn i,
.password-toggle i,
.input-icon i,
.icon-container i {
    opacity: 1 !important;
    visibility: visible !important;
    color: inherit !important;
}

/* Professional Header Styling */
.section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.section-subtitle {
    font-size: 0.95rem;
    color: var(--text-muted);
    margin: 0.25rem 0 0 0;
}

/* Professional Form Group Spacing */
.form-group {
    margin-bottom: 1.75rem;
}

.form-label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: var(--text-dark-alt);
    font-size: 1.05rem;
}

/* Enhanced Security Section - Adjusted spacing for info icon */
.security-notice {
    background: linear-gradient(135deg, #fef3c7, #fef7cd);
    border: 1px solid #fcd34d;
    border-radius: 12px;
    padding: 1.25rem 1.25rem 1.25rem 1rem;
    margin: 1.5rem 0;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.security-notice i {
    color: #d97706 !important;
    font-size: 1.1rem;
    margin-top: 0.1rem;
    flex-shrink: 0;
}

.security-notice p {
    margin: 0;
    line-height: 1.5;
    color: #1a202c;
    font-weight: 500;
}

/* Section Spacing */
.section-spacing {
    margin-bottom: 2rem;
}

.card-spacing {
    margin-bottom: 2.5rem;
}

.form-section-spacing {
    margin-bottom: 2rem;
}

.input-group-spacing {
    margin-bottom: 1.75rem;
}

.button-section-spacing {
    margin-top: 2rem;
    padding-top: 1.5rem;
}

.preference-item-spacing {
    margin-bottom: 1.5rem;
    padding: 1rem 0.5rem;
}

/* Remove footer and fix layout */
footer {
    display: none !important;
}

.footer {
    display: none !important;
}

/* Ensure content fits properly without extra scroll */
.main-content-wrapper {
    min-height: calc(100vh - 80px);
    padding-bottom: 0;
    margin-bottom: 0;
    overflow: hidden;
}

/* Remove any extra padding/margin that causes scroll */
body > *:last-child {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* COMPLETELY REMOVE SCROLLBAR */
body::-webkit-scrollbar {
    display: none;
}

body {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

/* Ensure main container doesn't create scroll */
.main-container {
    overflow: hidden;
    height: 100vh;
}

/* Fix for any nested containers that might cause scroll */
div[style*="overflow"], 
.container, 
.wrapper {
    overflow: hidden !important;
}

/* Specific fix for the account settings container */
.max-w-5xl.mx-auto {
    padding-bottom: 2rem;
    overflow: visible;
}

/* Enhanced text readability */
.text-gray-600 {
    color: #2d3748 !important;
}

.text-gray-400 {
    color: #4a5568 !important;
}

.text-gray-700 {
    color: #1a202c !important;
}

.text-gray-800 {
    color: #000000 !important;
}

/* Success and Error Messages */
.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1px solid #10b981;
    color: #065f46;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-error {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 1px solid #ef4444;
    color: #7f1d1d;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
</style>

<!-- Professional Top Bar -->
<div class="glass-card mb-8">
    <div class="flex justify-between items-center p-8">
        <div class="flex items-center space-x-6">
            <div class="icon-container">
                <i class="fas fa-user-cog"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-[#92400e] uppercase tracking-tight high-contrast-text">Account Settings</h1>
                <p class="text-[#2d3748] xl-text mt-2">Manage your account information and security preferences</p>
            </div>
        </div>
        <div class="flex items-center space-x-6">
            <div class="text-right">
                <p class="text-sm text-[#2d3748] font-medium">Last login</p>
                <p class="text-[#92400e] text-sm font-semibold">
                    <?php 
                    if(isset($_SESSION['last_login'])) {
                        echo date('M j, Y g:i A', strtotime($_SESSION['last_login']));
                    } else {
                        echo 'Just now';
                    }
                    ?>
                </p>
            </div>
            <div class="relative group">
                <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="location.reload()" title="Refresh Page">
                    <i class="fas fa-sync-alt text-xl"></i>
                </button>
                <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full pulse-dot border-2 border-white"></div>
            </div>
        </div>
    </div>
</div>

<!-- Professional Account Settings Section -->
<div class="max-w-5xl mx-auto space-y-8 pb-8">
    <!-- Display Success/Error Messages -->
    <?php if (isset($account_success)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($account_success); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($account_error)): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($account_error); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($password_success)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($password_success); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($password_error)): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($password_error); ?></span>
        </div>
    <?php endif; ?>

    <!-- Personal Information Card -->
    <div class="glass-card rounded-2xl p-8 section-spacing">
        <div class="section-header">
            <div class="icon-container">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h2 class="section-title">Personal Information</h2>
                <p class="section-subtitle">Update your basic account details and contact information</p>
            </div>
        </div>

        <form id="frmUpdateAccount" method="POST" class="space-y-8">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 form-section-spacing">
                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name" class="form-label required-field">
                        First Name
                    </label>
                    <div class="input-group input-group-spacing">
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <input 
                            type="text" 
                            value="<?= htmlspecialchars($current_user['user_fname']) ?>" 
                            id="first_name" 
                            name="first_name" 
                            class="form-input-enhanced"
                            placeholder="Enter your first name" 
                            required
                        >
                    </div>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name" class="form-label required-field">
                        Last Name
                    </label>
                    <div class="input-group input-group-spacing">
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <input 
                            type="text" 
                            value="<?= htmlspecialchars($current_user['user_lname']) ?>" 
                            id="last_name" 
                            name="last_name" 
                            class="form-input-enhanced"
                            placeholder="Enter your last name" 
                            required
                        >
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group form-section-spacing">
                <label for="email" class="form-label required-field">
                    Email Address
                </label>
                <div class="input-group input-group-spacing">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input 
                        type="email" 
                        value="<?= htmlspecialchars($current_user['user_email']) ?>" 
                        id="email" 
                        name="email" 
                        class="form-input-enhanced"
                        placeholder="Enter your email address" 
                        required
                    >
                </div>
            </div>

            <div class="pt-6 border-t border-gray-200 button-section-spacing">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Security Settings Card -->
    <div class="glass-card rounded-2xl p-8 section-spacing">
        <div class="section-header">
            <div class="icon-container">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h2 class="section-title">Security Settings</h2>
                <p class="section-subtitle">Manage your password and security preferences</p>
            </div>
        </div>

        <div class="security-notice">
            <i class="fas fa-info-circle text-xl"></i>
            <p class="text-sm font-medium">For security reasons, please enter your current password to make changes.</p>
        </div>

        <form id="frmUpdatePassword" method="POST" class="space-y-8">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <!-- Current Password -->
            <div class="form-group form-section-spacing">
                <label for="current_password" class="form-label required-field">
                    Current Password
                </label>
                <div class="input-group input-group-spacing">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="form-input-enhanced"
                        placeholder="Enter your current password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- New Password -->
            <div class="form-group form-section-spacing">
                <label for="new_password" class="form-label">
                    New Password
                </label>
                <div class="input-group input-group-spacing">
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        class="form-input-enhanced"
                        placeholder="Enter new password (leave blank to keep current)"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordStrength" class="hidden mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-[#1a202c]">Password strength:</span>
                        <span id="strengthText" class="text-sm font-semibold">Weak</span>
                    </div>
                    <div class="password-strength" id="strengthBar"></div>
                </div>
                <p class="text-sm text-[#2d3748] mt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Password must be at least 8 characters with uppercase, lowercase, numbers, and symbols.
                </p>
            </div>

            <!-- Confirm New Password -->
            <div class="form-group form-section-spacing">
                <label for="confirm_password" class="form-label">
                    Confirm New Password
                </label>
                <div class="input-group input-group-spacing">
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input-enhanced"
                        placeholder="Confirm your new password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordMatch" class="hidden mt-3">
                    <p class="text-sm flex items-center space-x-2">
                        <i class="fas fa-check"></i>
                        <span class="status-success">Passwords match</span>
                    </p>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-200 button-section-spacing">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-lock"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Preferences Card -->
    <div class="glass-card rounded-2xl p-8 section-spacing">
        <div class="section-header">
            <div class="icon-container">
                <i class="fas fa-cog"></i>
            </div>
            <div>
                <h2 class="section-title">Preferences</h2>
                <p class="section-subtitle">Customize your experience and notification settings</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Email Notifications -->
            <div class="flex items-center justify-between py-4 px-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 preference-item-spacing">
                <div class="flex-1">
                    <h3 class="font-semibold text-[#000000] xl-text">Email Notifications</h3>
                    <p class="text-sm text-[#2d3748] mt-1">Receive email updates about reservations and system alerts</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Professional Toast Notification -->
<div id="toast" class="fixed top-8 right-8 glass-card text-[#1a202c] p-6 rounded-2xl shadow-2xl z-50 transform translate-x-full transition-transform duration-500 max-w-md">
    <div class="flex items-center space-x-4">
        <div id="toastIconContainer" class="p-3 rounded-xl bg-green-500/20">
            <i id="toastIcon" class="fas fa-check-circle text-green-500 text-xl"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p id="toastMessage" class="font-semibold xl-text truncate">Settings updated successfully!</p>
            <p class="text-sm text-[#2d3748] mt-1" id="toastTimestamp">Just now</p>
        </div>
        <button id="closeToast" class="text-[#4a5568] hover:text-[#1a202c] transition-colors duration-200 p-2 rounded-lg hover:bg-gray-100">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-4 overflow-hidden">
        <div class="bg-green-500 h-1.5 rounded-full toast-progress"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Professional Interactive Features with Enhanced Icon Visibility
document.addEventListener('DOMContentLoaded', function() {
    initProfessionalInteractions();
    initSettingsUI();
    removeScrollBars();
});

function removeScrollBars() {
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    
    const containers = document.querySelectorAll('body, html, .container, .main-content');
    containers.forEach(container => {
        container.style.overflow = 'hidden';
        container.style.overflowX = 'hidden';
        container.style.overflowY = 'hidden';
    });
}

function initProfessionalInteractions() {
    const formInputs = document.querySelectorAll('.form-input-enhanced');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
            this.style.background = '#fffdf6';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
            this.style.background = 'white';
        });
    });

    const closeToast = document.getElementById('closeToast');
    if (closeToast) {
        closeToast.addEventListener('click', hideProfessionalToast);
    }

    document.querySelectorAll('i').forEach(icon => {
        icon.style.opacity = '1';
        icon.style.visibility = 'visible';
        icon.style.color = 'inherit';
    });
}

function initSettingsUI() {
    const newPassword = document.getElementById('new_password');
    if (newPassword) {
        newPassword.addEventListener('input', checkPasswordStrength);
    }

    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', checkPasswordMatch);
    }

    setupFormValidation();
}

function setupFormValidation() {
    const forms = ['frmUpdateAccount', 'frmUpdatePassword'];
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                }
            });
        }
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    
    return isValid;
}

function showFieldError(input, message) {
    clearFieldError(input);
    input.classList.add('border-red-500');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-2 flex items-center space-x-2';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${message}</span>`;
    
    input.parentNode.appendChild(errorDiv);
}

function clearFieldError(input) {
    input.classList.remove('border-red-500');
    const errorDiv = input.parentNode.querySelector('.text-red-500');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
        button.style.color = '#d97706 !important';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
        button.style.color = '#4a5568 !important';
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('new_password').value;
    const strengthBar = document.getElementById('passwordStrength');
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    
    if (!strengthBar || !bar || !text) return;
    
    if (password.length === 0) {
        strengthBar.classList.add('hidden');
        return;
    }
    
    strengthBar.classList.remove('hidden');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    const classes = ['strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
    const texts = ['Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['text-red-500', 'text-yellow-500', 'text-green-500', 'text-green-600'];
    
    bar.className = `password-strength ${classes[strength - 1] || classes[0]}`;
    text.textContent = texts[strength - 1] || texts[0];
    text.className = `text-sm font-semibold ${colors[strength - 1] || colors[0]}`;
}

function checkPasswordMatch() {
    const password = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (!matchDiv) return;
    
    if (confirm.length === 0) {
        matchDiv.classList.add('hidden');
        return;
    }
    
    if (password === confirm) {
        matchDiv.classList.remove('hidden');
        matchDiv.querySelector('span').textContent = 'Passwords match';
        matchDiv.querySelector('span').className = 'status-success';
        matchDiv.querySelector('i').className = 'fas fa-check status-success';
    } else {
        matchDiv.classList.remove('hidden');
        matchDiv.querySelector('span').textContent = 'Passwords do not match';
        matchDiv.querySelector('span').className = 'status-error';
        matchDiv.querySelector('i').className = 'fas fa-times status-error';
    }
}

// Professional toast system
window.showProfessionalToast = function(message, type = 'success', duration = 5000) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    const toastIconContainer = document.getElementById('toastIconContainer');
    
    if (toast && toastMessage && toastIcon && toastIconContainer) {
        toast.style.animation = 'none';
        void toast.offsetHeight;
        
        toastMessage.textContent = message;
        
        const toastConfig = {
            success: { 
                icon: 'fa-check-circle', 
                color: 'green',
                bgColor: 'bg-green-500/20'
            },
            error: { 
                icon: 'fa-exclamation-circle', 
                color: 'red',
                bgColor: 'bg-red-500/20'
            },
            warning: { 
                icon: 'fa-exclamation-triangle', 
                color: 'yellow',
                bgColor: 'bg-yellow-500/20'
            },
            info: { 
                icon: 'fa-info-circle', 
                color: 'blue',
                bgColor: 'bg-blue-500/20'
            }
        };
        
        const config = toastConfig[type] || toastConfig.success;
        toastIcon.className = `fas ${config.icon} text-${config.color}-500 text-xl`;
        toastIconContainer.className = `p-3 rounded-xl ${config.bgColor}`;
        
        const progressBar = toast.querySelector('.toast-progress');
        if (progressBar) {
            progressBar.className = `bg-${config.color}-500 h-1.5 rounded-full toast-progress`;
        }
        
        toast.style.transform = 'translateX(0)';
        
        setTimeout(() => {
            hideProfessionalToast();
        }, duration);
    }
};

window.hideProfessionalToast = function() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(100%)';
    }
};

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>