<?php
include "../src/components/headstaff/header.php";
include "../src/components/headstaff/nav.php";
?>

<style>
/* Enhanced Main Background - Lightened Version */
body {
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 255, 240, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 248, 225, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 250, 230, 0.2) 0%, transparent 50%),
        linear-gradient(135deg, #f8f4e5 0%, #fff9e6 30%, #f5f1e0 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* Enhanced Typography for Better Readability */
body {
    font-family: 'Arial', 'Helvetica', sans-serif;
    line-height: 1.6;
    color: #2d3748;
}

.high-contrast-text {
    color: #2d3748;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

.xl-text {
    font-size: 1.125rem;
    line-height: 1.6;
}

.xxl-text {
    font-size: 1.25rem;
    line-height: 1.5;
}

/* Enhanced Glass Morphism Effect - Light Version */
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 
        0 10px 25px -5px rgba(0, 0, 0, 0.1),
        0 4px 6px -2px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
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
        box-shadow: 0 0 0 10px rgba(217, 119, 6, 0);
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
    border: 3px solid;
    border-color: #92400e;
    color: white;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
}

.interactive-modal {
    background: white;
    backdrop-filter: blur(20px);
    border: 2px solid #e2e8f0;
    animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Enhanced Focus States */
input:focus, textarea:focus, select:focus {
    transform: scale(1.02);
    box-shadow: 
        0 0 0 3px rgba(217, 119, 6, 0.2), 
        0 4px 20px rgba(0, 0, 0, 0.1);
    border-color: #d97706;
    outline: none;
}

/* Button Enhancements */
.btn-primary {
    background: linear-gradient(135deg, #d97706, #b45309);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    border: 3px solid #92400e;
    color: white;
    font-weight: 700;
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
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);
}

/* Enhanced Form Elements */
.form-input-enhanced {
    background: white;
    border: 2px solid #cbd5e0;
    color: #2d3748;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    width: 100%;
    font-size: 1.125rem;
}

.form-input-enhanced:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2);
    outline: none;
}

.form-input-enhanced::placeholder {
    color: #718096;
}

/* Text Size Controls - Positioned at Bottom */
.text-size-controls {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    background: white;
    border-radius: 12px;
    padding: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 2px solid #e2e8f0;
}

.text-size-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #cbd5e0;
    background: white;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.text-size-btn:hover {
    background: #f7fafc;
    border-color: #d97706;
}

.text-size-btn.active {
    background: #d97706;
    color: white;
    border-color: #b45309;
}

/* Enhanced Modal Styling */
.enhanced-modal {
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 3px solid #e2e8f0;
}

/* Color Variables for Consistency */
:root {
    --primary-gold: #d97706;
    --dark-gold: #b45309;
    --light-gold: #fef3c7;
    --text-dark: #2d3748;
    --text-muted: #718096;
    --border-light: #e2e8f0;
    --background-light: #f8f4e5;
}

/* Responsive Typography */
@media (max-width: 768px) {
    .xl-text {
        font-size: 1rem;
    }
    
    .xxl-text {
        font-size: 1.125rem;
    }
    
    .text-size-controls {
        bottom: 10px;
        right: 10px;
        padding: 8px;
    }
    
    .text-size-btn {
        width: 35px;
        height: 35px;
        font-size: 0.875rem;
    }
}

/* Focus Management for Accessibility */
*:focus {
    outline: 3px solid #d97706;
    outline-offset: 2px;
}

/* Selection Styling */
::selection {
    background: #d97706;
    color: white;
}

/* Loading States */
.loading-spinner {
    display: inline-block;
    width: 24px;
    height: 24px;
    border: 3px solid rgba(217, 119, 6, 0.3);
    border-top: 3px solid #d97706;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Text Size Classes */
.text-small { font-size: 14px; }
.text-medium { font-size: 16px; }
.text-large { font-size: 18px; }
.text-xlarge { font-size: 20px; }

.text-small .xl-text { font-size: 1rem; }
.text-small .xxl-text { font-size: 1.125rem; }

.text-medium .xl-text { font-size: 1.125rem; }
.text-medium .xxl-text { font-size: 1.25rem; }

.text-large .xl-text { font-size: 1.25rem; }
.text-large .xxl-text { font-size: 1.5rem; }

.text-xlarge .xl-text { font-size: 1.5rem; }
.text-xlarge .xxl-text { font-size: 1.75rem; }

/* Stats Cards Enhancement */
.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 2px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(217, 119, 6, 0.1), transparent);
    transition: left 0.5s;
}

.stats-card:hover::before {
    left: 100%;
}

.stats-card:hover {
    transform: translateY(-8px);
    border-color: rgba(217, 119, 6, 0.3);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 30px rgba(217, 119, 6, 0.1);
}

/* Toast Progress Animation */
.toast-progress {
    animation: toastProgress 5s linear forwards;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Input Group Styling */
.input-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    z-index: 10;
}

.input-with-icon {
    padding-left: 40px !important;
}

/* Password Strength Indicator */
.password-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 8px;
    transition: all 0.3s ease;
}

.strength-weak { background: #DC2626; width: 25%; }
.strength-fair { background: #F59E0B; width: 50%; }
.strength-good { background: #10B981; width: 75%; }
.strength-strong { background: #059669; width: 100%; }

/* Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
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
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #d97706;
}

input:checked + .toggle-slider:before {
    transform: translateX(30px);
}

/* Form Validation */
.input-error {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
}

.input-success {
    border-color: #22c55e !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2) !important;
}

.error-message {
    color: #ef4444;
    font-size: 14px;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.success-message {
    color: #22c55e;
    font-size: 14px;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Section Cards */
.section-card {
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.section-card:hover {
    border-color: rgba(217, 119, 6, 0.4);
    transform: translateY(-2px);
}

/* Interactive Hover Effects */
.interactive-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.interactive-hover:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
}

/* Password Toggle */
.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: #d97706;
}

/* Icon Colors */
.icon-primary {
    color: #d97706;
}

.icon-secondary {
    color: #b45309;
}

.icon-success {
    color: #16a34a;
}

.icon-warning {
    color: #eab308;
}

.icon-danger {
    color: #dc2626;
}

.icon-info {
    color: #2563eb;
}
</style>

<!-- Enhanced Top Bar -->
<div class="glass-card mb-6">
    <div class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-4">
            <div class="p-4 bg-white rounded-2xl shadow-lg border-2 border-[#d97706]">
                <i class="fas fa-user-cog text-[#d97706] text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Account Settings</h2>
                <p class="text-gray-600 xl-text">Manage your account information and preferences</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="stats-card p-4 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 xl-text">Account Status:</span>
                    <span class="text-[#92400e] font-bold text-xl">Active</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Settings Section -->
<div class="max-w-4xl mx-auto space-y-6 fade-in">
  <!-- Personal Information Card -->
  <div class="section-card rounded-2xl p-6">
    <div class="flex items-center space-x-3 mb-6">
      <div class="p-3 bg-white rounded-xl border-2 border-[#d97706]">
        <i class="fas fa-user text-[#d97706] text-lg"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Personal Information</h2>
        <p class="text-gray-600 text-sm">Update your basic account details</p>
      </div>
    </div>

    <form id="frmUpdateAccount" method="POST" class="space-y-6">
      <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- First Name -->
        <div>
          <label for="first_name" class="block mb-2 xl-text font-bold text-[#92400e]">
            <i class="fas fa-user icon-primary mr-2"></i>First Name
            <span class="text-red-500">*</span>
          </label>
          <div class="input-group">
            <div class="input-icon">
              <i class="fas fa-user icon-primary"></i>
            </div>
            <input 
              type="text" 
              value="<?= htmlspecialchars($On_Session[0]['user_fname']) ?>" 
              id="first_name" 
              name="first_name" 
              class="w-full input-with-icon form-input-enhanced"
              placeholder="Enter your first name" 
              required
              oninput="validateField(this, 'name')"
            >
          </div>
          <div id="first_name_error" class="error-message hidden">
            <i class="fas fa-exclamation-circle"></i>
            <span>Please enter a valid first name</span>
          </div>
        </div>

        <!-- Last Name -->
        <div>
          <label for="last_name" class="block mb-2 xl-text font-bold text-[#92400e]">
            <i class="fas fa-user icon-primary mr-2"></i>Last Name
            <span class="text-red-500">*</span>
          </label>
          <div class="input-group">
            <div class="input-icon">
              <i class="fas fa-user icon-primary"></i>
            </div>
            <input 
              type="text" 
              value="<?= htmlspecialchars($On_Session[0]['user_lname']) ?>" 
              id="last_name" 
              name="last_name" 
              class="w-full input-with-icon form-input-enhanced"
              placeholder="Enter your last name" 
              required
              oninput="validateField(this, 'name')"
            >
          </div>
          <div id="last_name_error" class="error-message hidden">
            <i class="fas fa-exclamation-circle"></i>
            <span>Please enter a valid last name</span>
          </div>
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block mb-2 xl-text font-bold text-[#92400e]">
          <i class="fas fa-envelope icon-primary mr-2"></i>Email Address
          <span class="text-red-500">*</span>
        </label>
        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-envelope icon-primary"></i>
          </div>
          <input 
            type="email" 
            value="<?= htmlspecialchars($On_Session[0]['user_email']) ?>" 
            id="email" 
            name="email" 
            class="w-full input-with-icon form-input-enhanced"
            placeholder="Enter your email address" 
            required
            oninput="validateField(this, 'email')"
          >
        </div>
        <div id="email_error" class="error-message hidden">
          <i class="fas fa-exclamation-circle"></i>
          <span>Please enter a valid email address</span>
        </div>
      </div>

      <div class="pt-4 border-t border-gray-300">
        <button type="submit" id="saveProfileBtn" class="btn-primary px-8 py-3 rounded-xl font-semibold flex items-center space-x-2 transition-all duration-300 interactive-hover">
          <i class="fas fa-save mr-2"></i>
          <span>Save Changes</span>
        </button>
      </div>
    </form>
  </div>

  <!-- Security Settings Card -->
  <div class="section-card rounded-2xl p-6">
    <div class="flex items-center space-x-3 mb-6">
      <div class="p-3 bg-white rounded-xl border-2 border-[#d97706]">
        <i class="fas fa-shield-alt text-[#d97706] text-lg"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Security Settings</h2>
        <p class="text-gray-600 text-sm">Manage your password and security preferences</p>
      </div>
    </div>

    <form id="frmUpdatePassword" method="POST" class="space-y-6">
      <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
      
      <!-- Current Password -->
      <div>
        <label for="current_password" class="block mb-2 xl-text font-bold text-[#92400e]">
          <i class="fas fa-lock icon-primary mr-2"></i>Current Password
          <span class="text-red-500">*</span>
        </label>
        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-lock icon-primary"></i>
          </div>
          <input 
            type="password" 
            id="current_password" 
            name="current_password" 
            class="w-full input-with-icon form-input-enhanced"
            placeholder="Enter your current password"
            required
            oninput="validatePassword()"
          >
          <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div id="current_password_error" class="error-message hidden">
          <i class="fas fa-exclamation-circle"></i>
          <span>Current password is required</span>
        </div>
      </div>

      <!-- New Password -->
      <div>
        <label for="new_password" class="block mb-2 xl-text font-bold text-[#92400e]">
          <i class="fas fa-key icon-primary mr-2"></i>New Password
        </label>
        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-key icon-primary"></i>
          </div>
          <input 
            type="password" 
            id="new_password" 
            name="new_password" 
            class="w-full input-with-icon form-input-enhanced"
            placeholder="Enter new password (leave blank to keep current)"
            oninput="validatePassword()"
          >
          <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div id="passwordStrength" class="mt-2 hidden">
          <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Password strength:</span>
            <span id="strengthText">Weak</span>
          </div>
          <div class="password-strength strength-weak" id="strengthBar"></div>
        </div>
        <p class="text-sm text-gray-600 mt-2">
          <i class="fas fa-info-circle icon-info mr-1"></i>
          Password must be at least 8 characters with uppercase, lowercase, numbers, and symbols.
        </p>
      </div>

      <!-- Confirm New Password -->
      <div>
        <label for="confirm_password" class="block mb-2 xl-text font-bold text-[#92400e]">
          <i class="fas fa-key icon-primary mr-2"></i>Confirm New Password
        </label>
        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-key icon-primary"></i>
          </div>
          <input 
            type="password" 
            id="confirm_password" 
            name="confirm_password" 
            class="w-full input-with-icon form-input-enhanced"
            placeholder="Confirm your new password"
            oninput="validatePassword()"
          >
          <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div id="passwordMatch" class="mt-2 hidden">
          <p class="text-sm flex items-center space-x-1 text-green-600">
            <i class="fas fa-check"></i>
            <span>Passwords match</span>
          </p>
        </div>
        <div id="passwordMismatch" class="error-message hidden">
          <i class="fas fa-exclamation-circle"></i>
          <span>Passwords do not match</span>
        </div>
      </div>

      <div class="pt-4 border-t border-gray-300">
        <button type="submit" id="updatePasswordBtn" class="btn-primary px-8 py-3 rounded-xl font-semibold flex items-center space-x-2 transition-all duration-300 interactive-hover">
          <i class="fas fa-lock mr-2"></i>
          <span>Update Password</span>
        </button>
      </div>
    </form>
  </div>

  <!-- Preferences Card -->
  <div class="section-card rounded-2xl p-6">
    <div class="flex items-center space-x-3 mb-6">
      <div class="p-3 bg-white rounded-xl border-2 border-[#d97706]">
        <i class="fas fa-cog text-[#d97706] text-lg"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Preferences</h2>
        <p class="text-gray-600 text-sm">Customize your experience</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Email Notifications -->
      <div class="flex items-center justify-between py-3">
        <div>
          <h3 class="font-semibold text-[#92400e] xl-text">
            <i class="fas fa-envelope icon-primary mr-2"></i>Email Notifications
          </h3>
          <p class="text-gray-600 text-sm">Receive email updates about reservations</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked onchange="togglePreference('email_notifications', this.checked)">
          <span class="toggle-slider"></span>
        </label>
      </div>

      <!-- Auto-refresh -->
      <div class="flex items-center justify-between py-3">
        <div>
          <h3 class="font-semibold text-[#92400e] xl-text">
            <i class="fas fa-sync-alt icon-primary mr-2"></i>Auto-refresh
          </h3>
          <p class="text-gray-600 text-sm">Automatically refresh dashboard data</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" onchange="togglePreference('auto_refresh', this.checked)">
          <span class="toggle-slider"></span>
        </label>
      </div>

      <!-- Sound Notifications -->
      <div class="flex items-center justify-between py-3">
        <div>
          <h3 class="font-semibold text-[#92400e] xl-text">
            <i class="fas fa-bell icon-primary mr-2"></i>Sound Notifications
          </h3>
          <p class="text-gray-600 text-sm">Play sound for new notifications</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked onchange="togglePreference('sound_notifications', this.checked)">
          <span class="toggle-slider"></span>
        </label>
      </div>
    </div>
  </div>
</div>

<!-- Text Size Adjustment Controls - Positioned at Bottom Right -->
<div class="text-size-controls">
  <div class="flex space-x-1">
    <button id="textSmall" class="text-size-btn" title="Small Text">A</button>
    <button id="textMedium" class="text-size-btn active" title="Medium Text">A</button>
    <button id="textLarge" class="text-size-btn" title="Large Text">A</button>
    <button id="textXLarge" class="text-size-btn" title="Extra Large Text">A</button>
  </div>
</div>

<!-- Enhanced Spinner Overlay -->
<div id="spinner" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90 backdrop-blur-sm hidden">
    <div class="enhanced-modal p-10 flex flex-col items-center space-y-6">
        <div class="w-20 h-20 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
        <div class="text-center">
            <p class="xxl-text font-semibold high-contrast-text">Saving Changes</p>
            <p class="xl-text text-gray-600 mt-2">Please wait while we update your settings...</p>
        </div>
    </div>
</div>

<!-- Enhanced Toast Notification -->
<div id="toast" class="fixed top-6 right-6 glass-card text-gray-800 p-4 rounded-xl shadow-2xl z-50 transform translate-x-full transition-transform duration-500">
    <div class="flex items-center space-x-4">
        <div class="p-2 rounded-lg bg-green-500/20">
            <i id="toastIcon" class="fas fa-check-circle text-green-500 text-xl"></i>
        </div>
        <div class="flex-1">
            <p id="toastMessage" class="font-semibold">Action completed successfully!</p>
            <p class="text-xs text-gray-600 mt-1">Just now</p>
        </div>
        <button id="closeToast" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="w-full bg-gray-300 rounded-full h-1 mt-3">
        <div class="bg-green-500 h-1 rounded-full toast-progress"></div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<?php
include "../src/components/headstaff/footer.php";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Enhanced Interactive Features with Text Size Controls
document.addEventListener('DOMContentLoaded', function() {
    initEnhancedInteractions();
    initTextSizeControls();
    // Initialize your settings functionality
    if (typeof initializeSettings === 'function') {
        initializeSettings();
    }
});

function initTextSizeControls() {
    const textSmall = document.getElementById('textSmall');
    const textMedium = document.getElementById('textMedium');
    const textLarge = document.getElementById('textLarge');
    const textXLarge = document.getElementById('textXLarge');

    function setTextSize(size) {
        document.body.classList.remove('text-small', 'text-medium', 'text-large', 'text-xlarge');
        document.body.classList.add(`text-${size}`);
        
        [textSmall, textMedium, textLarge, textXLarge].forEach(btn => btn.classList.remove('active'));
        document.getElementById(`text${size.charAt(0).toUpperCase() + size.slice(1)}`).classList.add('active');
        
        localStorage.setItem('textSizePreference', size);
    }

    textSmall.addEventListener('click', () => setTextSize('small'));
    textMedium.addEventListener('click', () => setTextSize('medium'));
    textLarge.addEventListener('click', () => setTextSize('large'));
    textXLarge.addEventListener('click', () => setTextSize('xlarge'));

    // Load saved preference
    const savedSize = localStorage.getItem('textSizePreference') || 'medium';
    setTextSize(savedSize);
}

function initEnhancedInteractions() {
    // Enhanced focus animations
    const inputs = document.querySelectorAll('.form-input-enhanced');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

// Form Validation Functions
function validateField(field, type) {
    const value = field.value.trim();
    const errorElement = document.getElementById(field.id + '_error');
    
    let isValid = true;
    let errorMessage = '';
    
    switch(type) {
        case 'name':
            isValid = /^[a-zA-Z\s]{2,}$/.test(value);
            errorMessage = 'Please enter a valid name (letters and spaces only, min 2 characters)';
            break;
        case 'email':
            isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            errorMessage = 'Please enter a valid email address';
            break;
    }
    
    if (!isValid && value !== '') {
        field.classList.add('input-error');
        field.classList.remove('input-success');
        if (errorElement) {
            errorElement.classList.remove('hidden');
            errorElement.querySelector('span').textContent = errorMessage;
        }
    } else if (value === '') {
        field.classList.remove('input-error', 'input-success');
        if (errorElement) errorElement.classList.add('hidden');
    } else {
        field.classList.add('input-success');
        field.classList.remove('input-error');
        if (errorElement) errorElement.classList.add('hidden');
    }
    
    return isValid;
}

// Password Strength Checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordStrength() {
    const password = document.getElementById('new_password').value;
    const strengthBar = document.getElementById('passwordStrength');
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    
    if (password.length === 0) {
        strengthBar.classList.add('hidden');
        return;
    }
    
    strengthBar.classList.remove('hidden');
    
    const strength = checkPasswordStrength(password);
    const classes = ['strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
    const texts = ['Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['text-red-500', 'text-yellow-500', 'text-green-500', 'text-green-600'];
    
    bar.className = `password-strength ${classes[strength - 1] || classes[0]}`;
    text.textContent = texts[strength - 1] || texts[0];
    text.className = `text-sm ${colors[strength - 1] || colors[0]}`;
}

// Password Validation
function validatePassword() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchDiv = document.getElementById('passwordMatch');
    const mismatchDiv = document.getElementById('passwordMismatch');
    
    updatePasswordStrength();
    
    if (confirmPassword.length === 0) {
        matchDiv.classList.add('hidden');
        mismatchDiv.classList.add('hidden');
        return;
    }
    
    if (newPassword === confirmPassword) {
        matchDiv.classList.remove('hidden');
        mismatchDiv.classList.add('hidden');
    } else {
        matchDiv.classList.add('hidden');
        mismatchDiv.classList.remove('hidden');
    }
}

// Password Toggle Visibility
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
        button.classList.add('text-[#d97706]');
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
        button.classList.remove('text-[#d97706]');
    }
}

// Preference Toggle
function togglePreference(preference, enabled) {
    showEnhancedToast(`${preference.replace('_', ' ')} ${enabled ? 'enabled' : 'disabled'}`, 'info');
    // You can add AJAX call here to save preferences to database
}

// Enhanced toast system
window.showEnhancedToast = function(message, type = 'success', duration = 5000) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    if (toast && toastMessage && toastIcon) {
        // Reset animation
        toast.style.animation = 'none';
        void toast.offsetHeight;
        
        // Set content and style
        toastMessage.textContent = message;
        
        const toastConfig = {
            success: { icon: 'fa-check-circle', color: 'green' },
            error: { icon: 'fa-exclamation-circle', color: 'red' },
            warning: { icon: 'fa-exclamation-triangle', color: 'yellow' },
            info: { icon: 'fa-info-circle', color: 'blue' }
        };
        
        const config = toastConfig[type] || toastConfig.success;
        toastIcon.className = `fas ${config.icon} text-${config.color}-500 text-xl`;
        toastIcon.parentElement.className = `p-2 rounded-lg bg-${config.color}-500/20`;
        
        // Show with animation
        toast.style.transform = 'translateX(0)';
        
        // Auto hide
        setTimeout(() => {
            hideEnhancedToast();
        }, duration);
    }
};

window.hideEnhancedToast = function() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(100%)';
    }
};

// Close toast button
const closeToast = document.getElementById('closeToast');
if (closeToast) {
    closeToast.addEventListener('click', hideEnhancedToast);
}

// Form Submission Enhancements
function setupFormSubmissions() {
    // Profile form submission
    document.getElementById('frmUpdateAccount')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all fields
        const firstNameValid = validateField(document.getElementById('first_name'), 'name');
        const lastNameValid = validateField(document.getElementById('last_name'), 'name');
        const emailValid = validateField(document.getElementById('email'), 'email');
        
        if (!firstNameValid || !lastNameValid || !emailValid) {
            showEnhancedToast('Please fix the validation errors before saving', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('saveProfileBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="loading-spinner"></div>Saving Changes...';
        submitBtn.disabled = true;
        
        // Show loading overlay
        document.getElementById('spinner').classList.remove('hidden');
        
        // Simulate API call (replace with your actual form submission)
        setTimeout(() => {
            document.getElementById('spinner').classList.add('hidden');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            showEnhancedToast('Profile updated successfully!', 'success');
        }, 2000);
    });
    
    // Password form submission
    document.getElementById('frmUpdatePassword')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (!currentPassword) {
            showEnhancedToast('Current password is required', 'error');
            return;
        }
        
        if (newPassword && newPassword !== confirmPassword) {
            showEnhancedToast('New passwords do not match', 'error');
            return;
        }
        
        if (newPassword && checkPasswordStrength(newPassword) < 3) {
            showEnhancedToast('Please choose a stronger password', 'warning');
            return;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('updatePasswordBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="loading-spinner"></div>Updating Password...';
        submitBtn.disabled = true;
        
        // Show loading overlay
        document.getElementById('spinner').classList.remove('hidden');
        
        // Simulate API call (replace with your actual form submission)
        setTimeout(() => {
            document.getElementById('spinner').classList.add('hidden');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Clear password fields
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
            document.getElementById('passwordStrength').classList.add('hidden');
            document.getElementById('passwordMatch').classList.add('hidden');
            document.getElementById('passwordMismatch').classList.add('hidden');
            
            showEnhancedToast('Password updated successfully!', 'success');
        }, 2000);
    });
}

// Initialize interactive elements when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupFormSubmissions();
    
    // Add input event listeners for real-time validation
    document.getElementById('first_name')?.addEventListener('input', () => validateField(document.getElementById('first_name'), 'name'));
    document.getElementById('last_name')?.addEventListener('input', () => validateField(document.getElementById('last_name'), 'name'));
    document.getElementById('email')?.addEventListener('input', () => validateField(document.getElementById('email'), 'email'));
    document.getElementById('new_password')?.addEventListener('input', validatePassword);
    document.getElementById('confirm_password')?.addEventListener('input', validatePassword);
    
    showEnhancedToast('Account settings loaded', 'success');
});

// Export functions for your existing settings.js
window.accountInteractions = {
    validateField,
    validatePassword,
    togglePassword,
    togglePreference,
    showEnhancedToast
};
</script>

<script src="../static/js/headstaff/settings.js"></script>