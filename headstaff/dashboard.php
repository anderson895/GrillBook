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
    font-family: 'Arial', 'Helvetica', sans-serif;
    line-height: 1.6;
    color: #2d3748;
}

/* Enhanced Typography for Better Readability */
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

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 2px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(217, 119, 6, 0.1), transparent);
    transition: left 0.5s;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-8px);
    border-color: rgba(217, 119, 6, 0.3);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 30px rgba(217, 119, 6, 0.1);
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

.pulse-glow {
    animation: pulseGlow 2s infinite;
}

@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(217, 119, 6, 0.3); }
    50% { box-shadow: 0 0 30px rgba(217, 119, 6, 0.5); }
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

.interactive-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.interactive-hover:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
}

.table-row-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.table-row-hover:hover {
    background: #fefce8;
    transform: translateX(8px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.interactive-modal {
    background: white;
    backdrop-filter: blur(20px);
    border: 2px solid #e2e8f0;
    animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.fade-in {
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-in {
    animation: slideIn 0.4s ease-out;
}

@keyframes slideIn {
    from { transform: translateX(-20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
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

/* Enhanced Table Styling */
.enhanced-table {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

.enhanced-table th {
    background: linear-gradient(135deg, #f8f4e5 0%, #f0e6c3 100%);
    color: #2d3748;
    font-size: 1.125rem;
    font-weight: 700;
    padding: 20px;
    border-bottom: 3px solid #e2e8f0;
}

.enhanced-table td {
    padding: 20px;
    font-size: 1.125rem;
    border-bottom: 2px solid #f7fafc;
    vertical-align: middle;
}

.enhanced-table tr:hover {
    background: #fefce8;
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

/* Real-time Clock */
.real-time-clock {
    font-family: 'Courier New', monospace;
    background: linear-gradient(135deg, #f8f4e5 0%, #f0e6c3 100%);
    border: 2px solid #d97706;
    color: #2d3748;
}

.value-change {
    animation: valueChange 0.5s ease-in-out;
}

@keyframes valueChange {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); color: #d97706; }
    100% { transform: scale(1); }
}

.loading-pulse {
    animation: loadingPulse 1.5s ease-in-out infinite;
}

@keyframes loadingPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.chart-container {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.chart-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

/* Notification badge */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #EF4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Status indicators */
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-live {
    background: #10B981;
    animation: pulse 2s infinite;
}

/* Activity feed scrollbar */
.activity-feed {
    scrollbar-width: thin;
    scrollbar-color: #d97706 #f8f4e5;
}

.activity-feed::-webkit-scrollbar {
    width: 6px;
}

.activity-feed::-webkit-scrollbar-track {
    background: #f8f4e5;
}

.activity-feed::-webkit-scrollbar-thumb {
    background: #d97706;
    border-radius: 3px;
}

/* Filter Button Styles */
.filter-btn {
    background: white;
    border: 2px solid #e2e8f0;
    color: #2d3748;
    transition: all 0.3s ease;
    font-weight: 600;
}

.filter-btn:hover {
    background: #f7fafc;
    border-color: #d97706;
}

.filter-btn.active {
    background: #d97706;
    color: white;
    border-color: #b45309;
}

/* Currency Symbol Styling */
.price-currency {
    color: #d97706;
    font-weight: bold;
}

/* Toast Progress Animation */
.toast-progress {
    animation: toastProgress 5s linear forwards;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
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

/* Responsive Typography */
@media (max-width: 768px) {
    .xl-text {
        font-size: 1rem;
    }
    
    .xxl-text {
        font-size: 1.125rem;
    }
    
    .enhanced-table th,
    .enhanced-table td {
        padding: 16px;
        font-size: 1rem;
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

/* Ensure icons are visible */
.fas, .fa-chart-bar, .fa-sync-alt, .fa-calendar-day, .fa-calendar-week, 
.fa-calendar-alt, .fa-calendar, .fa-layer-group, .fa-filter, .fa-download, 
.fa-print, .fa-chart-pie, .fa-chart-line, .fa-calendar-check, .fa-peso-sign, 
.fa-crown, .fa-star, .fa-history, .fa-times, .fa-exclamation-circle, 
.fa-inbox, .fa-check-circle, .fa-exclamation-triangle, .fa-info-circle,
.fa-chart-line, .fa-money-bill-wave, .fa-clock, .fa-flag-checkered,
.fa-calendar-day, .fa-bolt, .fa-wallet, .fa-users, .fa-arrow-up,
.fa-exclamation-circle, .fa-pause, .fa-play, .fa-spinner {
    color: inherit !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>

<!-- Enhanced Header with Interactive Elements -->
<div class="glass-card rounded-2xl mb-8 fade-in">
  <div class="flex justify-between items-center p-6">
    <div class="flex items-center space-x-4">
      <div class="p-3 bg-gradient-to-br from-[#d97706] to-[#b45309] rounded-xl shadow-lg pulse-glow">
        <i class="fas fa-chart-line text-white text-xl"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Head Staff Dashboard</h1>
        <p class="text-gray-600 text-sm mt-1 xl-text">Real-time overview of restaurant operations</p>
      </div>
    </div>
    <div class="flex items-center space-x-4">
      <!-- Real-time Clock -->
      <div class="real-time-clock px-4 py-2 rounded-xl text-center min-w-[180px]">
        <div class="text-sm text-gray-600 uppercase tracking-wider">Current Time</div>
        <div id="realTimeClock" class="text-xl font-bold text-[#92400e] font-mono"></div>
        <div id="realTimeDate" class="text-xs text-gray-600 mt-1"></div>
      </div>
      
      <!-- Refresh Button -->
      <button onclick="refreshDashboard()" class="p-3 glass-card rounded-xl hover:bg-[#d97706] hover:text-white transition-all duration-300 interactive-hover" title="Refresh Dashboard">
        <i class="fas fa-sync-alt"></i>
      </button>
      
      <!-- User Avatar -->
      <div class="w-10 h-10 bg-gradient-to-br from-[#d97706] to-[#b45309] rounded-full flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-yellow-400/30 interactive-hover">
        <?php echo strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
      </div>
    </div>
  </div>
</div>

<!-- Interactive Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
  <!-- Total Reservations -->
  <div class="stat-card rounded-2xl p-6 flex items-center space-x-4 slide-in interactive-hover" onclick="handleStatClick('total')">
    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
      <i class="fas fa-calendar-check text-white text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-600 text-sm font-medium uppercase tracking-wide xl-text">Total Reservations</p>
      <h2 class="text-3xl font-bold text-[#92400e] mt-1" id="totalReservations">
        <div class="loading-spinner"></div>
      </h2>
      <p class="text-green-600 text-xs mt-1 flex items-center">
        <i class="fas fa-arrow-up mr-1"></i>
        <span id="reservationsTrend" class="xl-text">Loading...</span>
      </p>
    </div>
  </div>

  <!-- Pending Reservations -->
  <div class="stat-card rounded-2xl p-6 flex items-center space-x-4 slide-in interactive-hover" onclick="handleStatClick('pending')" style="animation-delay: 0.1s">
    <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg">
      <i class="fas fa-clock text-white text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-600 text-sm font-medium uppercase tracking-wide xl-text">Pending Approval</p>
      <h2 class="text-3xl font-bold text-yellow-600 mt-1" id="pendingReservations">
        <div class="loading-spinner"></div>
      </h2>
      <p class="text-yellow-600 text-xs mt-1 flex items-center">
        <i class="fas fa-exclamation-circle mr-1"></i>
        <span class="xl-text">Requires attention</span>
      </p>
    </div>
  </div>

  <!-- Confirmed Reservations -->
  <div class="stat-card rounded-2xl p-6 flex items-center space-x-4 slide-in interactive-hover" onclick="handleStatClick('confirmed')" style="animation-delay: 0.2s">
    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
      <i class="fas fa-check-circle text-white text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-600 text-sm font-medium uppercase tracking-wide xl-text">Confirmed</p>
      <h2 class="text-3xl font-bold text-green-600 mt-1" id="confirmedReservations">
        <div class="loading-spinner"></div>
      </h2>
      <p class="text-green-600 text-xs mt-1 flex items-center">
        <i class="fas fa-users mr-1"></i>
        <span id="confirmedTrend" class="xl-text">Loading...</span>
      </p>
    </div>
  </div>

  <!-- Completed Reservations -->
  <div class="stat-card rounded-2xl p-6 flex items-center space-x-4 slide-in interactive-hover" onclick="handleStatClick('completed')" style="animation-delay: 0.3s">
    <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg">
      <i class="fas fa-flag-checkered text-white text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-600 text-sm font-medium uppercase tracking-wide xl-text">Completed</p>
      <h2 class="text-3xl font-bold text-purple-600 mt-1" id="completedReservations">
        <div class="loading-spinner"></div>
      </h2>
      <p class="text-purple-600 text-xs mt-1 flex items-center">
        <i class="fas fa-chart-line mr-1"></i>
        <span id="completedTrend" class="xl-text">Loading...</span>
      </p>
    </div>
  </div>

  <!-- Today's Reservations -->
  <div class="stat-card rounded-2xl p-6 flex items-center space-x-4 slide-in interactive-hover" onclick="handleStatClick('today')" style="animation-delay: 0.4s">
    <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg">
      <i class="fas fa-calendar-day text-white text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-600 text-sm font-medium uppercase tracking-wide xl-text">Today's Reservations</p>
      <h2 class="text-3xl font-bold text-orange-600 mt-1" id="todaysReservations">
        <div class="loading-spinner"></div>
      </h2>
      <p class="text-orange-600 text-xs mt-1 flex items-center">
        <i class="fas fa-bolt mr-1"></i>
        <span class="xl-text">Scheduled for today</span>
      </p>
    </div>
  </div>

  <!-- Total Sales -->
  <div class="stat-card rounded-2xl p-6 flex items-center space-x-4 slide-in interactive-hover" onclick="handleStatClick('revenue')" style="animation-delay: 0.5s">
    <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg">
      <i class="fas fa-money-bill-wave text-white text-xl"></i>
    </div>
    <div class="flex-1">
      <p class="text-gray-600 text-sm font-medium uppercase tracking-wide xl-text">Total Revenue</p>
      <h2 class="text-3xl font-bold text-emerald-600 mt-1" id="totalSales">
        <div class="loading-spinner"></div>
      </h2>
      <p class="text-emerald-600 text-xs mt-1 flex items-center">
        <i class="fas fa-wallet mr-1"></i>
        <span id="revenueTrend" class="xl-text">Loading...</span>
      </p>
    </div>
  </div>
</div>

<!-- Interactive Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
  <!-- Reservations Chart -->
  <div class="chart-container rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-[#d97706]/20 rounded-lg">
          <i class="fas fa-chart-pie text-[#d97706] text-lg"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Reservations Overview</h2>
          <p class="text-gray-600 text-sm xl-text">Distribution by status</p>
        </div>
      </div>
      <div class="flex items-center space-x-2 text-sm text-gray-600">
        <span class="status-indicator status-live"></span>
        <span class="xl-text">Live</span>
        <button onclick="refreshCharts()" class="ml-2 interactive-hover">
          <i class="fas fa-sync-alt hover:text-[#d97706] transition-colors"></i>
        </button>
      </div>
    </div>
    <div id="reservationsChart" class="h-80">
        <div class="flex items-center justify-center h-full text-gray-600">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#d97706] mb-2"></div>
                <p class="xl-text">Loading chart data...</p>
            </div>
        </div>
    </div>
  </div>

  <!-- Sales Chart -->
  <div class="chart-container rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-[#d97706]/20 rounded-lg">
          <i class="fas fa-chart-bar text-[#d97706] text-lg"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Revenue Analytics</h2>
          <p class="text-gray-600 text-sm xl-text">Monthly revenue performance</p>
        </div>
      </div>
      <div class="flex items-center space-x-2 text-sm text-gray-600">
        <span class="status-indicator status-live"></span>
        <span class="xl-text">Live</span>
      </div>
    </div>
    <div id="salesChart" class="h-80">
        <div class="flex items-center justify-center h-full text-gray-600">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#d97706] mb-2"></div>
                <p class="xl-text">Loading chart data...</p>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- Interactive Recent Activity Section -->
<div class="glass-card rounded-2xl p-6 fade-in">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center space-x-3">
      <div class="p-2 bg-[#d97706]/20 rounded-lg">
        <i class="fas fa-history text-[#d97706] text-lg"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Live Activity Feed</h2>
        <p class="text-gray-600 text-sm xl-text">Real-time reservation updates</p>
      </div>
    </div>
    <div class="flex items-center space-x-3">
      <button id="pauseActivity" class="text-sm text-[#d97706] hover:text-[#b45309] transition-colors interactive-hover xl-text">
        <i class="fas fa-pause mr-1"></i> Pause
      </button>
      <button onclick="viewAllActivity()" class="text-sm text-[#d97706] hover:text-[#b45309] transition-colors interactive-hover xl-text">
        View All
      </button>
    </div>
  </div>
  <div id="recentActivity" class="space-y-3 max-h-64 overflow-y-auto activity-feed">
    <div class="text-center py-8 text-gray-600">
      <i class="fas fa-spinner animate-spin text-2xl mb-2"></i>
      <p class="xl-text">Loading live activity feed...</p>
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

<!-- Enhanced Toast Notification -->
<div id="toast" class="fixed top-6 right-6 glass-card text-gray-800 p-4 rounded-xl shadow-2xl z-50 transform translate-x-full transition-transform duration-500">
    <div class="flex items-center space-x-4">
        <div class="p-2 rounded-lg bg-green-500/20">
            <i id="toastIcon" class="fas fa-check-circle text-green-500 text-xl"></i>
        </div>
        <div class="flex-1">
            <p id="toastMessage" class="font-semibold xl-text">Action completed successfully!</p>
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

<?php
include "../src/components/headstaff/footer.php";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Enhanced Interactive Features with Text Size Controls
document.addEventListener('DOMContentLoaded', function() {
    initEnhancedInteractions();
    initTextSizeControls();
    initializeDashboard();
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
    // Close toast button
    const closeToast = document.getElementById('closeToast');
    if (closeToast) {
        closeToast.addEventListener('click', hideEnhancedToast);
    }

    // Activity feed controls
    let isActivityPaused = false;
    document.getElementById('pauseActivity').addEventListener('click', function() {
        isActivityPaused = !isActivityPaused;
        const icon = this.querySelector('i');
        
        if (isActivityPaused) {
            icon.className = 'fas fa-play mr-1';
            this.innerHTML = '<i class="fas fa-play mr-1"></i> Resume';
            showEnhancedToast('Activity feed paused', 'info');
        } else {
            icon.className = 'fas fa-pause mr-1';
            this.innerHTML = '<i class="fas fa-pause mr-1"></i> Pause';
            showEnhancedToast('Activity feed resumed', 'success');
        }
    });

    // Add staggered animation delays to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
}

// Real-time Clock
function updateRealTimeClock() {
    const now = new Date();
    const timeOptions = { 
        hour12: true, 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    };
    const dateOptions = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    document.getElementById('realTimeClock').textContent = now.toLocaleTimeString('en-US', timeOptions);
    document.getElementById('realTimeDate').textContent = now.toLocaleDateString('en-US', dateOptions);
}

// Initialize clock and update every second
updateRealTimeClock();
setInterval(updateRealTimeClock, 1000);

// Interactive Stat Card Handling
function handleStatClick(metricType) {
    const card = event.currentTarget;
    
    // Add click animation
    card.style.transform = 'scale(0.98)';
    setTimeout(() => {
        card.style.transform = '';
    }, 150);
    
    // Show toast notification based on metric type
    const messages = {
        'total': 'Viewing all reservations',
        'pending': 'Navigating to pending approvals',
        'confirmed': 'Showing confirmed reservations', 
        'completed': 'Displaying completed reservations',
        'today': 'Viewing today\'s schedule',
        'revenue': 'Generating revenue report'
    };
    
    showEnhancedToast(messages[metricType] || 'Action triggered');
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

// Refresh Functions
function refreshDashboard() {
    const button = event.currentTarget;
    const icon = button.querySelector('i');
    
    // Add loading animation
    icon.className = 'fas fa-sync-alt animate-spin';
    
    showEnhancedToast('Refreshing dashboard data...', 'info');
    
    // Reload dashboard data
    loadDashboardData();
    
    // Reset icon after 2 seconds
    setTimeout(() => {
        icon.className = 'fas fa-sync-alt';
    }, 2000);
}

function refreshCharts() {
    showEnhancedToast('Refreshing charts...', 'info');
    
    // Reload chart data
    loadChartsData();
}

function viewAllActivity() {
    showEnhancedToast('Opening full activity log...', 'info');
    // Add your navigation logic here
    // window.location.href = '/activity-log';
}

// Value Change Animation
function animateValueChange(elementId, newValue) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.add('value-change');
        setTimeout(() => {
            element.innerHTML = newValue;
            setTimeout(() => {
                element.classList.remove('value-change');
            }, 500);
        }, 250);
    }
}

// Initialize dashboard
function initializeDashboard() {
    showEnhancedToast('Dashboard loaded successfully', 'success');
    
    // Load initial data
    loadDashboardData();
    loadChartsData();
    loadActivityData();
    
    // Start periodic updates
    setInterval(loadDashboardData, 30000); // Update every 30 seconds
    setInterval(loadActivityData, 15000); // Update activity every 15 seconds
}

// Load dashboard statistics from database
function loadDashboardData() {
    $.ajax({
        url: "../controller/end-points/dashboard_controller.php",
        method: "POST",
        data: {
            action: 'get_dashboard_stats'
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                
                // Update statistics with animation
                animateValueChange('totalReservations', stats.total_reservations || '0');
                animateValueChange('pendingReservations', stats.pending_reservations || '0');
                animateValueChange('confirmedReservations', stats.confirmed_reservations || '0');
                animateValueChange('completedReservations', stats.completed_reservations || '0');
                animateValueChange('todaysReservations', stats.todays_reservations || '0');
                animateValueChange('totalSales', `<span class="price-currency">₱</span>${parseFloat(stats.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
                
                // Update trend indicators
                document.getElementById('reservationsTrend').textContent = stats.reservations_trend || 'No trend data';
                document.getElementById('confirmedTrend').textContent = stats.confirmed_trend || 'No trend data';
                document.getElementById('completedTrend').textContent = stats.completed_trend || 'No trend data';
                document.getElementById('revenueTrend').textContent = stats.revenue_trend || 'No trend data';
                
            } else {
                showEnhancedToast('Error loading dashboard data', 'error');
            }
        },
        error: function() {
            showEnhancedToast('Error connecting to server', 'error');
        }
    });
}

// Load charts data from database
function loadChartsData() {
    $.ajax({
        url: "../controller/end-points/dashboard_controller.php",
        method: "POST",
        data: {
            action: 'get_charts_data'
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                initializeCharts(response.data);
            } else {
                showEnhancedToast('Error loading charts data', 'error');
            }
        },
        error: function() {
            showEnhancedToast('Error loading charts data', 'error');
        }
    });
}

// Load activity data from database
function loadActivityData() {
    $.ajax({
        url: "../controller/end-points/dashboard_controller.php",
        method: "POST",
        data: {
            action: 'get_recent_activity'
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                updateActivityFeed(response.data);
            }
        },
        error: function() {
            // Silent fail for activity updates
        }
    });
}

// Initialize charts with real data
function initializeCharts(chartData) {
    // Reservations Chart - Status Distribution
    const reservationsChart = new ApexCharts(document.querySelector("#reservationsChart"), {
        series: chartData.reservations_by_status?.series || [0, 0, 0, 0],
        chart: {
            type: 'donut',
            height: 350
        },
        labels: chartData.reservations_by_status?.labels || ['Confirmed', 'Pending', 'Completed', 'Cancelled'],
        colors: ['#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    });

    // Sales Chart - Monthly Revenue
    const salesChart = new ApexCharts(document.querySelector("#salesChart"), {
        series: [{
            name: 'Revenue',
            data: chartData.monthly_revenue?.data || Array(12).fill(0)
        }],
        chart: {
            type: 'line',
            height: 350,
            zoom: {
                enabled: false
            }
        },
        colors: ['#d97706'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: chartData.monthly_revenue?.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '₱' + value.toLocaleString();
                }
            }
        }
    });

    reservationsChart.render();
    salesChart.render();
}

// Update activity feed with real data
function updateActivityFeed(activities) {
    const activityFeed = document.getElementById('recentActivity');
    
    if (!activities || activities.length === 0) {
        activityFeed.innerHTML = `
            <div class="text-center py-8 text-gray-600">
                <i class="fas fa-inbox text-2xl mb-2"></i>
                <p class="xl-text">No recent activity</p>
            </div>
        `;
        return;
    }
    
    let activityHTML = '';
    activities.forEach(activity => {
        activityHTML += `
            <div class="flex items-center space-x-3 p-3 bg-[#fefce8] rounded-lg border border-[#e2e8f0] slide-in">
                <div class="status-indicator status-live"></div>
                <div class="flex-1">
                    <p class="text-gray-800 text-sm xl-text">${activity.message}</p>
                    <p class="text-gray-600 text-xs">${activity.timestamp}</p>
                </div>
            </div>
        `;
    });
    
    activityFeed.innerHTML = activityHTML;
}

// Export functions for your existing dashboard.js
window.dashboardInteractions = {
    handleStatClick,
    refreshDashboard,
    refreshCharts,
    showEnhancedToast,
    animateValueChange
};
</script>