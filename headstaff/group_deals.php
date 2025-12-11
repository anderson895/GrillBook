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

/* Table Enhancements */
.table-row {
    transition: all 0.3s ease;
}

.table-row:hover {
    background: linear-gradient(90deg, rgba(217,119,6,0.05) 0%, rgba(217,119,6,0.02) 50%, rgba(217,119,6,0.05) 100%);
    transform: translateX(4px);
}

/* Action Buttons */
.action-btn {
    transition: all 0.3s ease;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.btn-view {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white;
    border: 2px solid #1d4ed8;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-view:hover {
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.btn-edit {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: white;
    border: 2px solid #92400e;
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
}

.btn-edit:hover {
    background: linear-gradient(135deg, #b45309, #92400e);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.4);
}

.btn-delete {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    border: 2px solid #b91c1c;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-delete:hover {
    background: linear-gradient(135deg, #DC2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

/* Status Badges */
.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 2px solid;
}

.status-active {
    background: rgba(34, 197, 94, 0.2);
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.3);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.3);
}

/* Banner Image Styles */
.banner-image {
    width: 80px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid rgba(217, 119, 6, 0.3);
    transition: all 0.3s ease;
    cursor: pointer;
}

.banner-image:hover {
    transform: scale(1.1);
    border-color: rgba(217, 119, 6, 0.6);
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

.loading-shimmer {
    background: linear-gradient(90deg, #f8f4e5 25%, #f0e6c3 50%, #f8f4e5 75%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Toast Progress Animation */
.toast-progress {
    animation: toastProgress 5s linear forwards;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #9ca3af;
    margin-bottom: 1rem;
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
    color: #6b7280;
}

.search-input {
    padding-left: 40px !important;
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

/* Quick Filter Buttons */
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

/* Text Size Classes */
.text-small { font-size: 14px; }
.text-medium { font-size: 16px; }
.text-large { font-size: 18px; }
.text-xlarge { font-size: 20px; }

.text-small .xl-text { font-size: 1rem; }
.text-small .xxl-text { font-size: 1.125rem; }
.text-small .action-btn {
    padding: 6px 12px;
    font-size: 12px;
}

.text-medium .xl-text { font-size: 1.125rem; }
.text-medium .xxl-text { font-size: 1.25rem; }

.text-large .xl-text { font-size: 1.25rem; }
.text-large .xxl-text { font-size: 1.5rem; }
.text-large .action-btn {
    padding: 10px 20px;
    font-size: 16px;
}

.text-xlarge .xl-text { font-size: 1.5rem; }
.text-xlarge .xxl-text { font-size: 1.75rem; }
.text-xlarge .action-btn {
    padding: 12px 24px;
    font-size: 18px;
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
.fas, .fa-users, .fa-sync-alt, .fa-plus, .fa-search, 
.fa-times, .fa-check-circle, .fa-exclamation-circle, 
.fa-exclamation-triangle, .fa-info-circle, .fa-arrow-up,
.fa-calendar-day, .fa-clock, .fa-money-bill-wave, .fa-chart-bar,
.fa-download, .fa-history, .fa-filter, .fa-chevron-down,
.fa-eye, .fa-edit, .fa-trash, .fa-layer-group, .fa-peso-sign, 
.fa-chart-line {
    color: inherit !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>

<!-- Enhanced Top Bar -->
<div class="glass-card mb-6 fade-in">
    <div class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-4">
            <div class="p-4 bg-gradient-to-br from-[#d97706] to-[#b45309] rounded-2xl shadow-lg pulse-glow">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Group Deals Management</h2>
                <p class="text-gray-600 xl-text">Manage and monitor group deals and promotions</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Real-time Clock -->
            <div class="real-time-clock px-4 py-2 rounded-xl text-center min-w-[180px]">
                <div class="text-sm text-gray-600 uppercase tracking-wider">Current Time</div>
                <div id="realTimeClock" class="text-xl font-bold text-[#92400e] font-mono"></div>
                <div id="realTimeDate" class="text-xs text-gray-600 mt-1"></div>
            </div>
            
            <!-- Add Deal Button -->
            <button onclick="openAddDealModal()" class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl interactive-hover" title="Add New Deal">
                <i class="fas fa-plus text-xl"></i>
            </button>
            
            <!-- Refresh Button -->
            <button onclick="refreshDeals()" class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl interactive-hover" title="Refresh Deals">
                <i class="fas fa-sync-alt text-xl"></i>
            </button>           
        </div>
    </div>
</div>

<!-- Enhanced Stats Cards with Analytics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stats-card p-6 rounded-2xl group cursor-pointer" onclick="filterDeals('all')">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Deals</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="totalDealCount">0</h3>
            </div>
            <div class="p-3 bg-[#d97706]/10 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-[#d97706] text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-green-600 text-sm">
            <i class="fas fa-arrow-up mr-1"></i>
            <span>Live Count</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl group cursor-pointer" onclick="filterDeals('active')">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-600 text-sm font-medium">Active Deals</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="activeDealCount">0</h3>
            </div>
            <div class="p-3 bg-green-500/10 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-green-600 text-sm">
            <i class="fas fa-eye mr-1"></i>
            <span>All Active</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl group cursor-pointer" onclick="showCategoryBreakdown()">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-600 text-sm font-medium">Categories</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="categoryCount">0</h3>
            </div>
            <div class="p-3 bg-blue-500/10 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-layer-group text-blue-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-blue-600 text-sm">
            <i class="fas fa-tags mr-1"></i>
            <span>Diverse Offers</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl group cursor-pointer" onclick="showPerformanceAnalytics()">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-600 text-sm font-medium">Avg. Value</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="avgValue"><span class="price-currency">₱</span>0.00</h3>
            </div>
            <div class="p-3 bg-purple-500/10 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-peso-sign text-purple-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-purple-600 text-sm">
            <i class="fas fa-chart-line mr-1"></i>
            <span>Performance</span>
        </div>
    </div>
</div>

<!-- Quick Actions Bar -->
<div class="glass-card p-6 rounded-2xl mb-6">
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex items-center space-x-4">
            <h3 class="text-xl font-semibold text-[#92400e] high-contrast-text">Quick Actions</h3>
            <div class="h-8 w-px bg-gray-300"></div>
            <button class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg" onclick="showQuickStats()">
                <i class="fas fa-chart-bar"></i>
                Analytics
            </button>
            <button class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg" onclick="exportDealData()">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
        <div class="flex items-center space-x-3 xl-text text-gray-600">
            <i class="fas fa-history"></i>
            <span>Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
        </div>
    </div>
</div>

<!-- Interactive Controls Section -->
<div class="glass-card rounded-2xl p-6 mb-6 fade-in">
    <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <!-- Search and Filter Section -->
        <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
            <!-- Enhanced Search Input -->
            <div class="relative flex-1 sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-500 text-lg"></i>
                </div>
                <input
                    type="text"
                    id="searchInput"
                    class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-300 rounded-xl
                           text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced xl-text"
                    placeholder="Search group deals..."
                    onkeyup="handleSearch(event)"
                />
            </div>

            <!-- Quick Filters -->
            <div class="flex gap-2">
                <button onclick="filterDeals('all')" class="filter-btn bg-[#d97706] text-white interactive-hover xl-text">
                    All Deals
                </button>
                <button onclick="filterDeals('active')" class="filter-btn bg-gray-600 text-white interactive-hover xl-text">
                    Active
                </button>
                <button onclick="filterDeals('inactive')" class="filter-btn bg-gray-600 text-white interactive-hover xl-text">
                    Inactive
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <div class="flex items-center space-x-2 bg-[#fef3c7] px-3 py-1 rounded-full">
                <i class="fas fa-chart-bar text-[#d97706]"></i>
                <span class="xl-text">Total: <span id="totalCount" class="text-[#d97706] font-semibold">0</span></span>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Table Section -->
<div class="glass-card rounded-2xl p-6 fade-in">
    <!-- Table Container -->
    <div class="max-h-[600px] overflow-y-auto overflow-x-hidden rounded-xl border-2 border-gray-200 custom-scrollbar">
        <table class="w-full enhanced-table">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-left">Group Name</th>
                    <th class="text-left">Description</th>
                    <th class="text-center">Banner</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="outputBody" class="divide-y divide-gray-100">
                <!-- Loading State -->
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-600">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <div class="loading-spinner w-12 h-12"></div>
                            <p class="xxl-text font-semibold high-contrast-text">Loading group deals...</p>
                            <p class="xl-text text-gray-600">Please wait while we fetch the latest deals</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="empty-state hidden">
        <div class="flex flex-col items-center justify-center space-y-4">
            <i class="fas fa-users text-gray-600"></i>
            <div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2 high-contrast-text">No Group Deals Found</h3>
                <p class="text-gray-500 text-sm xl-text">No group deals match your current search or filters.</p>
                <button onclick="openAddDealModal()" class="mt-4 px-6 py-2 bg-[#d97706] text-white rounded-lg font-semibold interactive-hover xl-text">
                    <i class="fas fa-plus mr-2"></i>Create First Deal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Banner Preview Modal -->
<div id="bannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="enhanced-modal w-full max-w-2xl mx-4 p-6 relative">
        <button onclick="closeBannerModal()" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-xl transition-all duration-200 interactive-hover">
            <i class="fas fa-times text-xl text-gray-600 hover:text-gray-800"></i>
        </button>
        <div class="text-center mb-4">
            <h3 class="text-lg font-semibold text-[#92400e] high-contrast-text">Banner Preview</h3>
            <p class="text-gray-600 text-sm xl-text">Group deal banner image</p>
        </div>
        <img id="bannerPreview" src="" alt="Banner Preview" class="w-full h-auto rounded-lg border-2 border-gray-300 max-h-[70vh] object-contain" />
    </div>
</div>

<!-- Enhanced Loading Overlay -->
<div id="spinnerOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="glass-card p-8 rounded-2xl flex flex-col items-center space-y-4">
        <div class="loading-spinner w-16 h-16"></div>
        <div class="text-center">
            <p class="text-gray-800 text-lg font-semibold high-contrast-text">Processing Request</p>
            <p class="text-gray-600 text-sm mt-1 xl-text">Please wait while we process your action...</p>
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

<!-- Text Size Adjustment Controls - Positioned at Bottom Right -->
<div class="text-size-controls">
  <div class="flex space-x-1">
    <button id="textSmall" class="text-size-btn" title="Small Text">A</button>
    <button id="textMedium" class="text-size-btn active" title="Medium Text">A</button>
    <button id="textLarge" class="text-size-btn" title="Large Text">A</button>
    <button id="textXLarge" class="text-size-btn" title="Extra Large Text">A</button>
  </div>
</div>

<?php include "../src/components/headstaff/footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Enhanced Interactive Features with Text Size Controls
document.addEventListener('DOMContentLoaded', function() {
    initEnhancedInteractions();
    initTextSizeControls();
    initializeGroupDeals();
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

    // Close modal on backdrop click
    document.addEventListener('click', function(e) {
        if (e.target.id === 'bannerModal') {
            closeBannerModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBannerModal();
        }
    });

    // Enhanced search with debouncing
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                handleSearch(e);
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

    // Add staggered animation to table rows when they load
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.tagName === 'TR') {
                        node.classList.add('slide-in');
                    }
                });
            }
        });
    });
    
    const outputBody = document.getElementById('outputBody');
    if (outputBody) {
        observer.observe(outputBody, { childList: true });
    }
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

// Search Functionality with Debouncing
let searchTimeout;
function handleSearch(event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchTerm = event.target.value;
        // Call your existing search function
        if (typeof filterDeals === 'function') {
            filterDeals(searchTerm);
        }
        showEnhancedToast(`Searching for: ${searchTerm}`, 'info');
    }, 500);
}

// Quick Filter Functions
function filterDeals(filterType) {
    showEnhancedToast(`Filtering: ${filterType} deals`, 'info');
    
    // Update active filter button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-[#d97706]', 'text-white');
        btn.classList.add('bg-gray-600', 'text-white');
    });
    
    // Activate current filter
    const activeBtn = event?.currentTarget;
    if (activeBtn) {
        activeBtn.classList.add('bg-[#d97706]', 'text-white');
        activeBtn.classList.remove('bg-gray-600', 'text-white');
    }
    
    // Call your existing filter function
    if (typeof loadDeals === 'function') {
        loadDeals(filterType);
    }
}

// Refresh Function
function refreshDeals() {
    const button = event?.currentTarget;
    if (button) {
        const icon = button.querySelector('i');
        // Add loading animation
        icon.className = 'fas fa-sync-alt animate-spin';
        
        // Reset icon after 2 seconds
        setTimeout(() => {
            icon.className = 'fas fa-sync-alt';
        }, 2000);
    }
    
    showEnhancedToast('Refreshing group deals...', 'info');
    
    // Call your existing refresh function
    if (typeof loadDeals === 'function') {
        loadDeals();
    }
}

// Banner Modal Functions
function openBannerModal(bannerSrc) {
    const modal = document.getElementById('bannerModal');
    const bannerPreview = document.getElementById('bannerPreview');
    
    bannerPreview.src = bannerSrc;
    modal.classList.remove('hidden');
    showEnhancedToast('Banner preview opened', 'info');
}

function closeBannerModal() {
    const modal = document.getElementById('bannerModal');
    modal.classList.add('hidden');
}

// Add Deal Modal Function (placeholder - you can implement this)
function openAddDealModal() {
    showEnhancedToast('Add deal feature would open here', 'info');
    // Implement your add deal modal logic here
    // This would typically open a form to create new group deals
}

// Additional interactive functions
function showCategoryBreakdown() {
    showEnhancedToast('Showing category breakdown...', 'info');
}

function showPerformanceAnalytics() {
    showEnhancedToast('Displaying performance analytics...', 'info');
}

function showQuickStats() {
    showEnhancedToast('Opening analytics dashboard...', 'info');
}

function exportDealData() {
    showEnhancedToast('Exporting deal data...', 'info');
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

// Update total count display
function updateTotalCount(count) {
    const totalElement = document.getElementById('totalCount');
    if (totalElement) {
        totalElement.textContent = count;
        
        // Show/hide empty state
        const emptyState = document.getElementById('emptyState');
        const outputBody = document.getElementById('outputBody');
        
        if (emptyState && outputBody) {
            if (count === 0) {
                emptyState.classList.remove('hidden');
                outputBody.classList.add('hidden');
            } else {
                emptyState.classList.add('hidden');
                outputBody.classList.remove('hidden');
            }
        }
    }
}

// Update stats cards
function updateStatsCards(dealsData) {
    if (dealsData && dealsData.length > 0) {
        const totalItems = dealsData.length;
        const activeItems = dealsData.filter(deal => deal.status === 'active').length;
        const categories = [...new Set(dealsData.map(deal => deal.category))].length;
        
        // Update DOM
        document.getElementById('totalDealCount').textContent = totalItems;
        document.getElementById('activeDealCount').textContent = activeItems;
        document.getElementById('categoryCount').textContent = categories;
        
        // Update last updated time
        document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
    }
}

// Row Selection and Interactions
function setupTableInteractions() {
    document.addEventListener('click', function(e) {
        const row = e.target.closest('tr');
        if (row && !e.target.closest('button') && !e.target.classList.contains('banner-image')) {
            // Add visual feedback for row clicks
            row.style.backgroundColor = 'rgba(217, 119, 6, 0.1)';
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 300);
        }
    });
}

// Initialize group deals system
function initializeGroupDeals() {
    setupTableInteractions();
    showEnhancedToast('Group deals management loaded', 'success');
}

// Export functions for your existing group_deals.js
window.groupDealInteractions = {
    refreshDeals,
    filterDeals,
    handleSearch,
    openBannerModal,
    closeBannerModal,
    openAddDealModal,
    showEnhancedToast,
    updateTotalCount,
    updateStatsCards
};
</script>

<script src="../static/js/headstaff/group_deals.js"></script>