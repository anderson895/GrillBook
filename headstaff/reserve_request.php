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

/* Action Button Enhancements */
.btn-view {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #1d4ed8;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
    color: white !important;
}

.btn-approve {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: white !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #166534;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
}

.btn-approve:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
    background: linear-gradient(135deg, #15803d, #166534);
    color: white !important;
}

.btn-decline {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #b91c1c;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-decline:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    background: linear-gradient(135deg, #DC2626, #b91c1c);
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

/* Enhanced Status Colors */
.status-badge {
    padding: 8px 16px;
    border-radius: 9999px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.875rem;
    border-width: 2px;
}

.status-pending {
    background: #fffbeb;
    color: #d97706;
    border-color: #f59e0b;
    animation: pulse 2s infinite;
}

.status-confirmed {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #22c55e;
}

.status-cancelled {
    background: #fef2f2;
    color: #dc2626;
    border-color: #ef4444;
}

.status-request-cancel {
    background: #fff7ed;
    color: #ea580c;
    border-color: #f97316;
    animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
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
.text-small .btn-view,
.text-small .btn-approve,
.text-small .btn-decline {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
}

.text-medium .xl-text { font-size: 1.125rem; }
.text-medium .xxl-text { font-size: 1.25rem; }

.text-large .xl-text { font-size: 1.25rem; }
.text-large .xxl-text { font-size: 1.5rem; }
.text-large .btn-view,
.text-large .btn-approve,
.text-large .btn-decline {
    padding: 1rem 2rem;
    font-size: 1rem;
}

.text-xlarge .xl-text { font-size: 1.5rem; }
.text-xlarge .xxl-text { font-size: 1.75rem; }
.text-xlarge .btn-view,
.text-xlarge .btn-approve,
.text-xlarge .btn-decline {
    padding: 1.25rem 2.5rem;
    font-size: 1.125rem;
}

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

/* Sortable Headers */
.sortable {
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
}

.sortable:hover {
    color: #d97706 !important;
}

.sort-indicator {
    transition: transform 0.3s ease;
}

.sort-asc .sort-indicator {
    transform: rotate(180deg);
}

.sort-desc .sort-indicator {
    transform: rotate(0deg);
}

/* Priority Indicators */
.priority-high {
    border-left: 4px solid #ef4444;
}

.priority-medium {
    border-left: 4px solid #f59e0b;
}

.priority-low {
    border-left: 4px solid #22c55e;
}

/* Search Enhancements */
.search-container {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.search-input {
    padding-left: 40px !important;
}

.filter-btn {
    transition: all 0.3s ease;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
}

.filter-btn.active {
    background: #d97706;
    color: white;
}

/* Notification Badge */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
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
                <i class="fas fa-clock text-[#d97706] text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Reservation Requests</h2>
                <p class="text-gray-600 xl-text">Review and manage pending reservation requests</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="stats-card p-4 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 xl-text">Total Requests:</span>
                    <span id="totalRequests" class="text-[#92400e] font-bold text-xl">0</span>
                </div>
            </div>
            <div class="relative group">
                <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="refreshRequests()">
                    <i class="fas fa-sync-alt text-xl text-[#d97706] hover:text-white"></i>
                </button>
                <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full pulse-dot border-2 border-white"></div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Bar -->
<div class="glass-card p-6 rounded-2xl mb-6">
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex items-center space-x-4">
            <h3 class="text-xl font-semibold text-[#92400e] high-contrast-text">Quick Actions</h3>
            <div class="h-8 w-px bg-gray-300"></div>
            <button id="refreshBtn" class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg">
                <i class="fas fa-sync-alt group-hover:scale-110 transition-transform duration-200"></i>
                Refresh List
            </button>
        </div>
        <div class="flex items-center space-x-3 xl-text text-gray-600">
            <i class="fas fa-history icon-primary"></i>
            <span>Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
        </div>
    </div>
</div>

<!-- Interactive Controls Section -->
<div class="glass-card rounded-2xl p-6 mb-6">
  <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
    <!-- Search and Filter Section -->
    <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
      <!-- Search Input -->
      <div class="search-container relative flex-1 sm:max-w-md">
        <div class="search-icon">
          <i class="fas fa-search text-gray-500 text-lg"></i>
        </div>
        <input
          type="text"
          id="searchInput"
          class="w-full search-input pl-12 pr-4 py-4 bg-white border-2 border-gray-300 rounded-xl
                 text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced xl-text"
          placeholder="Search reservations..."
        />
      </div>

      <!-- Quick Filters -->
      <div class="flex gap-2">
        <button onclick="filterRequests('all')" class="filter-btn bg-[#d97706] text-white interactive-hover">
          <i class="fas fa-layer-group mr-2"></i>All Requests
        </button>
        <button onclick="filterRequests('pending')" class="filter-btn bg-gray-300 text-gray-800 interactive-hover">
          <i class="fas fa-clock mr-2 icon-warning"></i>Pending
        </button>
        <button onclick="filterRequests('urgent')" class="filter-btn bg-gray-300 text-gray-800 interactive-hover">
          <i class="fas fa-exclamation-triangle mr-2 icon-danger"></i>Urgent
        </button>
        <button onclick="filterRequests('today')" class="filter-btn bg-gray-300 text-gray-800 interactive-hover">
          <i class="fas fa-calendar-day mr-2 icon-primary"></i>Today
        </button>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="flex items-center space-x-4 text-sm text-gray-400">
      <div class="flex items-center space-x-2 bg-yellow-100 px-3 py-1 rounded-full">
        <i class="fas fa-inbox text-[#d97706]"></i>
        <span class="text-[#92400e]">Total: <span id="totalCount" class="font-semibold">0</span></span>
      </div>
      <div id="pendingCount" class="flex items-center space-x-2 bg-yellow-100 px-3 py-1 rounded-full">
        <i class="fas fa-clock text-[#d97706]"></i>
        <span class="text-[#92400e]"><span id="pendingRequests">0</span> Pending</span>
      </div>
    </div>
  </div>
</div>

<!-- Results Info -->
<div id="resultsInfo" class="mb-6 p-6 stats-card rounded-xl">
    <div class="flex items-center gap-6 xl-text">
        <div class="flex items-center gap-3">
            <i class="fas fa-list icon-primary"></i>
            <span class="text-gray-600">Showing:</span>
            <span id="shownCount" class="text-[#92400e] font-semibold">0</span>
            <span class="text-gray-600">of</span>
            <span id="totalCount" class="text-[#92400e] font-semibold">0</span>
            <span class="text-gray-600">reservations</span>
        </div>
        <div class="h-6 w-px bg-gray-300"></div>
        <div class="text-gray-500 text-lg">
            <i class="fas fa-info-circle icon-info mr-2"></i>Use search to filter results • Click headers to sort
        </div>
    </div>
</div>

<!-- Interactive Table Section -->
<div class="glass-card rounded-2xl p-6">
  <!-- Table Container -->
  <div class="max-h-[600px] overflow-y-auto overflow-x-hidden rounded-xl border-2 border-gray-200 custom-scrollbar">
    <table class="w-full enhanced-table">
      <thead class="sticky top-0 z-10">
        <tr>
          <th class="cursor-pointer sortable text-center" data-sort="reservation_id">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-hashtag icon-primary"></i>
              <span>#</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="reservation_date">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-calendar icon-primary"></i>
              <span>Date</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-left" data-sort="customer_name">
            <div class="flex items-center gap-2">
              <i class="fas fa-user icon-primary"></i>
              <span>Customer Name</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="reservation_code">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-qrcode icon-warning"></i>
              <span>Reservation Code</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="table_number">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-utensils icon-success"></i>
              <span>Table</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="schedule">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-clock icon-info"></i>
              <span>Schedule</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="reservation_time">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-clock icon-warning"></i>
              <span>Time</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="total_amount">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-tag icon-success"></i>
              <span>Total</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="status">
            <div class="flex items-center justify-center gap-2">
              <i class="fas fa-info-circle icon-info"></i>
              <span>Status</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="text-center">
            <i class="fas fa-cogs icon-primary"></i> Actions
          </th>
        </tr>
      </thead>
      <tbody id="outputTableBody" class="divide-y divide-gray-100">
        <!-- Loading State -->
        <tr>
          <td colspan="10" class="p-8 text-center text-gray-600">
            <div class="flex flex-col items-center justify-center space-y-4">
              <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
              <div>
                <p class="xxl-text font-semibold high-contrast-text">Loading reservation requests...</p>
                <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- No Results Message -->
<div id="noResults" class="hidden mt-8 p-12 text-center stats-card rounded-xl">
    <div class="flex flex-col items-center justify-center space-y-6">
        <i class="fas fa-check-circle text-6xl text-gray-400"></i>
        <div>
            <p class="xxl-text font-semibold high-contrast-text">No reservation requests found</p>
            <p class="xl-text text-gray-600 mt-2">Try adjusting your search terms or filters</p>
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
            <p class="xxl-text font-semibold high-contrast-text">Loading Reservation Requests</p>
            <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data...</p>
        </div>
    </div>
</div>

<!-- Enhanced Details Modal -->
<div id="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md hidden">
    <div class="enhanced-modal w-full max-w-4xl mx-4 p-8 relative max-h-[90vh] overflow-y-auto">
        <button id="closeModal" class="absolute top-6 right-6 text-gray-600 hover:text-gray-800 text-3xl font-bold transition focus:outline-none focus:ring-4 focus:ring-[#d97706] rounded-full p-2">
            &times;
        </button>

        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <div class="p-4 bg-gradient-to-r from-[#d97706] to-[#b45309] rounded-2xl shadow-lg">
                    <i class="fas fa-file-invoice text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text">Reservation Details</h3>
                    <p class="xl-text text-gray-600">Complete reservation information and management</p>
                </div>
            </div>

            <hr class="border-gray-300">

            <div id="modalContent" class="space-y-6">
                <!-- Dynamic content will be injected here -->
            </div>

            <hr class="border-gray-300 my-6">

            <!-- Action Section -->
            <input type="hidden" id="reservation_id" name="reservation_id">
            
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                <div class="text-sm text-gray-600 xl-text">
                    <i class="fas fa-info-circle icon-primary mr-2"></i>
                    Review all details before taking action
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="declineReservation()" 
                            class="btn-decline px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>
                        <span>Decline</span>
                    </button>
                    <button type="button" onclick="approveReservation()" 
                            class="btn-approve px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 transition-all duration-300">
                        <i class="fas fa-check mr-2"></i>
                        <span>Approve</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Payment Image Modal -->
<div id="payment_img_modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md hidden">
    <div class="enhanced-modal w-full max-w-2xl mx-4 p-8 relative">
        <button id="close_modal" class="absolute top-6 right-6 text-gray-600 hover:text-gray-800 text-3xl font-bold transition focus:outline-none focus:ring-4 focus:ring-[#d97706] rounded-full p-2">
            &times;
        </button>
        <div class="text-center mb-4">
            <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text">
                <i class="fas fa-receipt icon-primary mr-2"></i>Payment Receipt
            </h3>
            <p class="xl-text text-gray-600">Proof of payment submitted by customer</p>
        </div>
        <img id="modal_img" src="" alt="Payment Receipt" class="w-full h-auto rounded-lg border-2 border-gray-300 max-h-[70vh] object-contain" />
    </div>
</div>

<?php include "../src/components/headstaff/footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Enhanced Interactive Features with Text Size Controls
document.addEventListener('DOMContentLoaded', function() {
    initEnhancedInteractions();
    initTextSizeControls();
    // Initialize your reservation management functionality
    if (typeof initializeReservationManagement === 'function') {
        initializeReservationManagement();
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
    // Enhanced search with debouncing
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (typeof handleSearch === 'function') {
                    handleSearch(e);
                }
            }, 300);
        });

        // Search focus animation
        searchInput.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    }

    // Refresh button functionality
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.classList.add('animate-spin');
            setTimeout(() => {
                this.classList.remove('animate-spin');
            }, 1000);
            if (typeof refreshRequests === 'function') {
                refreshRequests();
            }
        });
    }

    // Sort functionality
    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            const currentSort = this.classList.contains('sort-asc') ? 'desc' : 'asc';
            
            // Reset other headers
            sortableHeaders.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Set current header
            this.classList.add(`sort-${currentSort}`);
            if (typeof sortReservations === 'function') {
                sortReservations(sortBy, currentSort);
            }
        });
    });
}

// Quick Filter Functions
function filterRequests(filterType) {
    showEnhancedToast(`Filtering: ${filterType} requests`, 'info');
    
    // Update active filter button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-[#d97706]', 'text-white');
        btn.classList.add('bg-gray-300', 'text-gray-800');
    });
    
    // Activate current filter
    const activeBtn = event?.currentTarget;
    if (activeBtn) {
        activeBtn.classList.add('bg-[#d97706]', 'text-white');
        activeBtn.classList.remove('bg-gray-300', 'text-gray-800');
    }
    
    // Call your existing filter function
    if (typeof loadReservations === 'function') {
        loadReservations(filterType);
    }
}

// Refresh Function
function refreshRequests() {
    const button = event?.currentTarget;
    if (button) {
        const icon = button.querySelector('i');
        // Add loading animation
        icon.className = 'fas fa-sync-alt animate-spin icon-primary';
        
        // Reset icon after 2 seconds
        setTimeout(() => {
            icon.className = 'fas fa-sync-alt icon-primary';
        }, 2000);
    }
    
    showEnhancedToast('Refreshing reservation requests...', 'info');
    
    // Call your existing refresh function
    if (typeof loadReservations === 'function') {
        loadReservations();
    }
}

// Modal Functions
function openDetailsModal(reservationId) {
    // This would be called from your existing row click handler
    showEnhancedToast('Loading reservation details...', 'info');
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
}

function openPaymentModal(imageSrc) {
    const modal = document.getElementById('payment_img_modal');
    const modalImg = document.getElementById('modal_img');
    
    modalImg.src = imageSrc;
    modal.classList.remove('hidden');
    showEnhancedToast('Payment receipt opened', 'info');
}

function closePaymentModal() {
    const modal = document.getElementById('payment_img_modal');
    modal.classList.add('hidden');
}

// Action Functions
function approveReservation() {
    const reservationId = document.getElementById('reservation_id').value;
    if (!reservationId) {
        showEnhancedToast('Reservation ID not found', 'error');
        return;
    }
    
    showEnhancedToast('Approving reservation...', 'info');
    // Call your existing approve function
    if (typeof updateReservationStatus === 'function') {
        updateReservationStatus(reservationId, 'confirmed');
    }
}

function declineReservation() {
    const reservationId = document.getElementById('reservation_id').value;
    if (!reservationId) {
        showEnhancedToast('Reservation ID not found', 'error');
        return;
    }
    
    showEnhancedToast('Declining reservation...', 'info');
    // Call your existing decline function
    if (typeof updateReservationStatus === 'function') {
        updateReservationStatus(reservationId, 'cancelled');
    }
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

// Update statistics display
function updateStats(total, pending = 0) {
    const totalElement = document.getElementById('totalRequests');
    const totalCountElement = document.getElementById('totalCount');
    const shownCountElement = document.getElementById('shownCount');
    const pendingElement = document.getElementById('pendingRequests');
    
    if (totalElement) totalElement.textContent = total;
    if (totalCountElement) totalCountElement.textContent = total;
    if (shownCountElement) shownCountElement.textContent = total;
    if (pendingElement) pendingElement.textContent = pending;
    
    // Update last updated time
    document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
    
    // Show/hide empty state
    const noResults = document.getElementById('noResults');
    const outputTableBody = document.getElementById('outputTableBody');
    
    if (noResults && outputTableBody) {
        if (total === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
}

// Initialize interactive elements when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Modal close buttons
    document.getElementById('closeModal')?.addEventListener('click', closeDetailsModal);
    document.getElementById('close_modal')?.addEventListener('click', closePaymentModal);
    
    // Add staggered animation to table rows when they load
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.tagName === 'TR') {
                        node.classList.add('table-row-hover');
                    }
                });
            }
        });
    });
    
    const outputTableBody = document.getElementById('outputTableBody');
    if (outputTableBody) {
        observer.observe(outputTableBody, { childList: true });
    }
    
    showEnhancedToast('Reservation requests management loaded', 'success');
});

// Export functions for your existing reserve_request.js
window.reservationInteractions = {
    refreshRequests,
    filterRequests,
    handleSearch,
    openDetailsModal,
    closeDetailsModal,
    openPaymentModal,
    closePaymentModal,
    approveReservation,
    declineReservation,
    showEnhancedToast,
    updateStats
};
</script>

<script src="../static/js/headstaff/reserve_request.js"></script>