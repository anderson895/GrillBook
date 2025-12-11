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

/* View Button Enhancement */
.viewDetailsBtn {
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

.viewDetailsBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
    color: white !important;
}

/* Remove Button Enhancement */
.removeBtn {
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

.removeBtn:hover {
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

.status-active {
    background: #d1fae5;
    color: #065f46;
    border-color: #10b981;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
    border-color: #ef4444;
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
.text-small .viewDetailsBtn,
.text-small .removeBtn {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
}

.text-medium .xl-text { font-size: 1.125rem; }
.text-medium .xxl-text { font-size: 1.25rem; }

.text-large .xl-text { font-size: 1.25rem; }
.text-large .xxl-text { font-size: 1.5rem; }
.text-large .viewDetailsBtn,
.text-large .removeBtn {
    padding: 1rem 2rem;
    font-size: 1rem;
}

.text-xlarge .xl-text { font-size: 1.5rem; }
.text-xlarge .xxl-text { font-size: 1.75rem; }
.text-xlarge .viewDetailsBtn,
.text-xlarge .removeBtn {
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

/* Enhanced Pagination */
.pagination-btn {
    background: white;
    border: 2px solid #e2e8f0;
    color: #2d3748;
    transition: all 0.3s ease;
}

.pagination-btn:hover {
    background: #d97706;
    color: white;
    border-color: #b45309;
}

.pagination-btn.active {
    background: #d97706;
    color: white;
    border-color: #b45309;
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

/* Promo-specific styles */
.promo-card {
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.promo-card:hover {
    transform: translateY(-5px);
    border-color: rgba(217, 119, 6, 0.4);
    box-shadow: 0 12px 40px rgba(217, 119, 6, 0.1);
}

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

.expiration-date {
    font-size: 12px;
    font-weight: 600;
}

.expiration-soon {
    color: #f59e0b;
    animation: pulse 2s infinite;
}

.expiration-normal {
    color: #22c55e;
}

.expiration-passed {
    color: #ef4444;
    text-decoration: line-through;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.view-toggle {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    padding: 4px;
}

.view-toggle-btn {
    padding: 6px 12px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.view-toggle-btn.active {
    background: #d97706;
    color: white;
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
</style>

<!-- Enhanced Top Bar -->
<div class="glass-card mb-6">
    <div class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-4">
            <div class="p-4 bg-white rounded-2xl shadow-lg border-2 border-[#d97706]">
                <i class="fas fa-percentage text-[#d97706] text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Promo Deals Management</h2>
                <p class="text-gray-600 xl-text">Manage and monitor promotional deals and discounts</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="stats-card p-4 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 xl-text">Total Promos:</span>
                    <span id="totalPromos" class="text-[#92400e] font-bold text-xl">0</span>
                </div>
            </div>
            <div class="relative group">
                <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="refreshPromos()">
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
            <button onclick="openAddPromoModal()" class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg">
                <i class="fas fa-plus group-hover:scale-110 transition-transform duration-200"></i>
                Add New Promo
            </button>
            <button id="refreshBtn" class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg">
                <i class="fas fa-sync-alt group-hover:scale-110 transition-transform duration-200"></i>
                Refresh List
            </button>
        </div>
        <div class="flex items-center space-x-3 xl-text text-gray-600">
            <i class="fas fa-history"></i>
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
          placeholder="Search promo deals..."
        />
      </div>

      <!-- Quick Filters -->
      <div class="flex gap-2">
        <button onclick="filterPromos('all')" class="filter-btn bg-[#d97706] text-white interactive-hover">
          All Promos
        </button>
        <button onclick="filterPromos('active')" class="filter-btn bg-gray-300 text-gray-800 interactive-hover">
          Active
        </button>
        <button onclick="filterPromos('expired')" class="filter-btn bg-gray-300 text-gray-800 interactive-hover">
          Expired
        </button>
        <button onclick="filterPromos('upcoming')" class="filter-btn bg-gray-300 text-gray-800 interactive-hover">
          Upcoming
        </button>
      </div>
    </div>

    <!-- View Toggle -->
    <div class="view-toggle flex items-center">
      <button id="tableViewBtn" onclick="toggleView('table')" class="view-toggle-btn active interactive-hover">
        <i class="fas fa-table"></i>
      </button>
      <button id="gridViewBtn" onclick="toggleView('grid')" class="view-toggle-btn interactive-hover">
        <i class="fas fa-th-large"></i>
      </button>
    </div>
  </div>
</div>

<!-- Results Info -->
<div id="resultsInfo" class="mb-6 p-6 stats-card rounded-xl">
    <div class="flex items-center gap-6 xl-text">
        <div class="flex items-center gap-3">
            <span class="text-gray-600">Showing:</span>
            <span id="shownCount" class="text-[#92400e] font-semibold">0</span>
            <span class="text-gray-600">of</span>
            <span id="totalCount" class="text-[#92400e] font-semibold">0</span>
            <span class="text-gray-600">promos</span>
        </div>
        <div class="h-6 w-px bg-gray-300"></div>
        <div class="flex items-center space-x-4 text-sm text-gray-400">
            <div class="flex items-center space-x-2 bg-green-100 px-3 py-1 rounded-full">
                <i class="fas fa-play-circle text-green-500"></i>
                <span class="text-green-800"><span id="activePromos">0</span> Active</span>
            </div>
        </div>
        <div class="text-gray-500 text-lg">
            Use search to filter results • Click headers to sort
        </div>
    </div>
</div>

<!-- Table View -->
<div id="tableView" class="glass-card rounded-2xl p-6">
  <!-- Table Container -->
  <div class="max-h-[600px] overflow-y-auto overflow-x-hidden rounded-xl border-2 border-gray-200 custom-scrollbar">
    <table class="w-full enhanced-table">
      <thead class="sticky top-0 z-10">
        <tr>
          <th class="cursor-pointer sortable text-center" data-sort="promo_id">
            <div class="flex items-center justify-center gap-2">
              <span>#</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-left" data-sort="promo_name">
            <div class="flex items-center gap-2">
              <span>Promo Name</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-left" data-sort="promo_description">
            <div class="flex items-center gap-2">
              <span>Description</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="text-center">Banner</th>
          <th class="cursor-pointer sortable text-center" data-sort="expiration_date">
            <div class="flex items-center justify-center gap-2">
              <span>Expiration</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="cursor-pointer sortable text-center" data-sort="status">
            <div class="flex items-center justify-center gap-2">
              <span>Status</span>
              <i class="fas fa-sort sort-indicator text-gray-500"></i>
            </div>
          </th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="outputBody" class="divide-y divide-gray-100">
        <!-- Loading State -->
        <tr>
          <td colspan="7" class="p-8 text-center text-gray-600">
            <div class="flex flex-col items-center justify-center space-y-4">
              <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
              <div>
                <p class="xxl-text font-semibold high-contrast-text">Loading promo deals...</p>
                <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Grid View -->
<div id="gridView" class="hidden">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="gridContainer">
    <!-- Promo cards will be dynamically inserted here -->
    <div class="col-span-full text-center py-12">
      <div class="flex flex-col items-center justify-center space-y-4">
        <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
        <div>
          <p class="xxl-text font-semibold high-contrast-text">Loading promo deals...</p>
          <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- No Results Message -->
<div id="noResults" class="hidden mt-8 p-12 text-center stats-card rounded-xl">
    <div class="flex flex-col items-center justify-center space-y-6">
        <i class="fas fa-percentage text-6xl text-gray-400"></i>
        <div>
            <p class="xxl-text font-semibold high-contrast-text">No promo deals found</p>
            <p class="xl-text text-gray-600 mt-2">Try adjusting your search terms or filters</p>
            <button onclick="openAddPromoModal()" class="mt-4 quick-action-btn px-6 py-3 rounded-xl font-semibold">
                <i class="fas fa-plus mr-2"></i>Create First Promo
            </button>
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
            <p class="xxl-text font-semibold high-contrast-text">Loading Promo Deals</p>
            <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data...</p>
        </div>
    </div>
</div>

<!-- Banner Preview Modal -->
<div id="bannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md hidden">
    <div class="enhanced-modal w-full max-w-2xl mx-4 p-8 relative">
        <button onclick="closeBannerModal()" class="absolute top-6 right-6 text-gray-600 hover:text-gray-800 text-3xl font-bold transition focus:outline-none focus:ring-4 focus:ring-[#d97706] rounded-full p-2">
            &times;
        </button>
        <div class="text-center mb-4">
            <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text">Promo Banner Preview</h3>
            <p class="xl-text text-gray-600">Promotional banner image</p>
        </div>
        <img id="bannerPreview" src="" alt="Banner Preview" class="w-full h-auto rounded-lg border-2 border-gray-300 max-h-[70vh] object-contain" />
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
    // Initialize your promo management functionality
    if (typeof initializePromoManagement === 'function') {
        initializePromoManagement();
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
            if (typeof refreshPromos === 'function') {
                refreshPromos();
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
            if (typeof sortPromos === 'function') {
                sortPromos(sortBy, currentSort);
            }
        });
    });
}

// View Toggle Functionality
let currentView = 'table';

function toggleView(viewType) {
    currentView = viewType;
    
    // Update button states
    document.getElementById('tableViewBtn').classList.toggle('active', viewType === 'table');
    document.getElementById('gridViewBtn').classList.toggle('active', viewType === 'grid');
    
    // Show/hide views
    document.getElementById('tableView').classList.toggle('hidden', viewType !== 'table');
    document.getElementById('gridView').classList.toggle('hidden', viewType !== 'grid');
    
    showEnhancedToast(`Switched to ${viewType} view`, 'info');
    
    // Refresh data in current view
    if (typeof loadPromos === 'function') {
        loadPromos();
    }
}

// Quick Filter Functions
function filterPromos(filterType) {
    showEnhancedToast(`Filtering: ${filterType} promos`, 'info');
    
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
    if (typeof loadPromos === 'function') {
        loadPromos(filterType);
    }
}

// Refresh Function
function refreshPromos() {
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
    
    showEnhancedToast('Refreshing promo deals...', 'info');
    
    // Call your existing refresh function
    if (typeof loadPromos === 'function') {
        loadPromos();
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

// Add Promo Modal Function (placeholder - you can implement this)
function openAddPromoModal() {
    showEnhancedToast('Add promo feature would open here', 'info');
    // Implement your add promo modal logic here
}

// Status Calculation Helper
function getPromoStatus(expirationDate) {
    const now = new Date();
    const expDate = new Date(expirationDate);
    const timeDiff = expDate - now;
    const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    
    if (daysDiff < 0) return 'expired';
    if (daysDiff <= 3) return 'expiring-soon';
    if (daysDiff <= 7) return 'expiring';
    return 'active';
}

function getExpirationClass(expirationDate) {
    const status = getPromoStatus(expirationDate);
    switch(status) {
        case 'expired': return 'expiration-passed';
        case 'expiring-soon': return 'expiration-soon';
        case 'expiring': return 'expiration-normal';
        default: return 'expiration-normal';
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
function updateStats(total, active = 0) {
    const totalElement = document.getElementById('totalPromos');
    const totalCountElement = document.getElementById('totalCount');
    const shownCountElement = document.getElementById('shownCount');
    const activeElement = document.getElementById('activePromos');
    
    if (totalElement) totalElement.textContent = total;
    if (totalCountElement) totalCountElement.textContent = total;
    if (shownCountElement) shownCountElement.textContent = total;
    if (activeElement) activeElement.textContent = active;
    
    // Update last updated time
    document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
    
    // Show/hide empty state
    const noResults = document.getElementById('noResults');
    const outputBody = document.getElementById('outputBody');
    const gridContainer = document.getElementById('gridContainer');
    
    if (noResults && outputBody && gridContainer) {
        if (total === 0) {
            noResults.classList.remove('hidden');
            outputBody.innerHTML = '';
            gridContainer.innerHTML = '';
        } else {
            noResults.classList.add('hidden');
        }
    }
}

// Initialize interactive elements when page loads
document.addEventListener('DOMContentLoaded', function() {
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
    
    const outputBody = document.getElementById('outputBody');
    if (outputBody) {
        observer.observe(outputBody, { childList: true });
    }
    
    showEnhancedToast('Promo deals management loaded', 'success');
});

// Export functions for your existing promo_deals.js
window.promoInteractions = {
    refreshPromos,
    filterPromos,
    openBannerModal,
    closeBannerModal,
    openAddPromoModal,
    toggleView,
    getPromoStatus,
    getExpirationClass,
    showEnhancedToast,
    updateStats
};
</script>

<script src="../static/js/headstaff/promo_deals.js"></script>