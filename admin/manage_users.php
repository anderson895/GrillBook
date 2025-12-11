<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>

<style>
/* CLEAN WHITE BACKGROUND - NO SCROLL UNLESS NEEDED */
body {
    background: #ffffff;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    width: 100%;
    position: relative;
}

/* Remove all scrollbars from body */
body::-webkit-scrollbar {
    display: none;
}

body {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

/* Ensure full height layout */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Main content container */
.main-content {
    width: calc(100% - 250px); /* Adjust based on sidebar width */
    margin-left: 250px; /* Match sidebar width */
    min-height: 100vh;
    padding: 20px;
    box-sizing: border-box;
    position: relative;
}

/* Fix for any nested containers */
.container, .wrapper, .max-w-7xl, .max-w-5xl {
    overflow: visible !important;
    max-height: none !important;
}

/* Enhanced Typography */
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

.fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
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

.quick-action-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    border: 2px solid #92400e;
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.25);
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.35);
}

.table-row-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.table-row-hover:hover {
    background: #fefce8;
    transform: translateX(8px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.enhanced-modal {
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 3px solid #e2e8f0;
}

.form-input-enhanced {
    background: white;
    border: 2px solid #e2e8f0;
    color: #1a202c;
    padding: 1rem 1.5rem;
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

.viewDetailsBtn {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: 2px solid #1d4ed8;
    cursor: pointer;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25);
}

.viewDetailsBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
}

.removeBtn {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: 2px solid #b91c1c;
    cursor: pointer;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25);
}

.removeBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.35);
    background: linear-gradient(135deg, #DC2626, #b91c1c);
}

.search-container {
    position: relative;
    flex: 1;
    max-width: 500px;
}

.search-icon {
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    color: #4a5568 !important;
    z-index: 10;
    width: 18px;
    height: 18px;
}

.search-input {
    width: 100%;
    padding: 1rem 1.5rem 1rem 4rem !important;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1.125rem;
    transition: all 0.3s ease;
    color: #1a202c;
}

.search-input:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
    outline: none;
    background: #fffdf6;
}

.search-input::placeholder {
    color: #4a5568;
    font-weight: 400;
}

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

.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 2px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-8px);
    border-color: rgba(217, 119, 6, 0.3);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 30px rgba(217, 119, 6, 0.1);
}

.position-badge {
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: capitalize;
    border: 2px solid;
}

.position-admin {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
    color: white;
    border-color: #7C3AED;
}

.position-headstaff {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white;
    border-color: #2563EB;
}

.position-customer {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    border-color: #059669;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}

.top-bar-icon-container {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    padding: 12px;
    background: linear-gradient(135deg, #d97706, #b45309);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
}

.top-bar-icon-container i {
    color: white !important;
    font-size: 1.25rem;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
}

.orange-icon {
    color: #d97706 !important;
}

.refresh-icon {
    color: #d97706 !important;
    transition: all 0.3s ease;
}

.refresh-icon:hover {
    color: #b45309 !important;
    transform: rotate(180deg);
}

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
    
    .search-container {
        max-width: 100%;
    }
}

*:focus {
    outline: 3px solid rgba(217, 119, 6, 0.3);
    outline-offset: 2px;
}

::selection {
    background: #d97706;
    color: white;
    text-shadow: none;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

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

.fas, .fa-users, .fa-sync-alt, .fa-history, .fa-magnifying-glass, 
.fa-sort, .fa-eye, .fa-trash, .fa-user, .fa-check-circle, 
.fa-exclamation-circle, .fa-exclamation-triangle, .fa-info-circle {
    color: inherit !important;
    opacity: 1 !important;
    visibility: visible !important;
    font-style: normal;
    font-weight: 900;
}

.viewDetailsBtn i,
.removeBtn i,
.refresh-icon i,
.search-icon i {
    opacity: 1 !important;
    visibility: visible !important;
    color: inherit !important;
}

/* Table container specific fixes */
.enhanced-table-container {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
    overflow-x: hidden;
}

/* Remove extra padding/margin from containers */
.mx-auto, .container, .main-content-wrapper {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Fix for any extra spacing */
body > *:last-child {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Ensure table doesn't overflow unnecessarily */
.enhanced-table {
    min-height: auto;
}

/* Toast positioning fix */
#toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}
</style>

<div class="glass-card mb-6">
    <div class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-4">
            <div class="top-bar-icon-container">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">User Management</h2>
                <p class="text-gray-600 xl-text">Manage all system users and their permissions</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="stats-card p-4 rounded-xl">
                <div class="flex items-center gap-2">
                    <i class="fas fa-users orange-icon"></i>
                    <span class="text-gray-600 xl-text">Total Users:</span>
                    <span id="totalUsers" class="text-[#92400e] font-bold text-xl">0</span>
                </div>
            </div>
            <div class="relative group">
                <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="location.reload()">
                    <i class="fas fa-sync-alt refresh-icon text-xl"></i>
                </button>
                <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full pulse-dot border-2 border-white"></div>
            </div>
        </div>
    </div>
</div>

<div class="glass-card p-6 rounded-2xl mb-6">
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex items-center space-x-4">
            <h3 class="text-xl font-semibold text-[#92400e] high-contrast-text">Quick Actions</h3>
            <div class="h-8 w-px bg-gray-300"></div>
        </div>
        <div class="flex items-center space-x-3 xl-text text-gray-600">
            <i class="fas fa-history orange-icon"></i>
            <span>Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
        </div>
    </div>
</div>

<div class="glass-card rounded-2xl p-6 text-gray-800">
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div class="search-container">
            <svg class="search-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="magnifying-glass" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill="currentColor" d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"></path>
            </svg>
            <input
                type="text"
                id="searchInput"
                class="search-input"
                placeholder="Search users by name, email, or position..."
            />
        </div>
    </div>

    <div id="resultsInfo" class="mb-6 p-6 stats-card rounded-xl">
        <div class="flex items-center gap-6 xl-text">
            <div class="flex items-center gap-3">
                <span class="text-gray-600">Showing:</span>
                <span id="shownCount" class="text-[#92400e] font-semibold">0</span>
                <span class="text-gray-600">of</span>
                <span id="totalCount" class="text-[#92400e] font-semibold">0</span>
                <span class="text-gray-600">users</span>
            </div>
            <div class="h-6 w-px bg-gray-300"></div>
        </div>
    </div>

    <div class="enhanced-table-container rounded-xl border-2 border-gray-200 custom-scrollbar">
        <table class="w-full enhanced-table">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="cursor-pointer sortable text-center" data-sort="user_id">
                        <div class="flex items-center justify-center gap-2">
                            <span>ID</span>
                            <i class="fas fa-sort sort-indicator text-gray-500"></i>
                        </div>
                    </th>
                    <th class="cursor-pointer sortable text-left" data-sort="user_fname">
                        <div class="flex items-center gap-2">
                            <span>User Information</span>
                            <i class="fas fa-sort sort-indicator text-gray-500"></i>
                        </div>
                    </th>
                    <th class="cursor-pointer sortable text-left" data-sort="user_email">
                        <div class="flex items-center gap-2">
                            <span>Email Address</span>
                            <i class="fas fa-sort sort-indicator text-gray-500"></i>
                        </div>
                    </th>
                    <th class="cursor-pointer sortable text-center" data-sort="user_position">
                        <div class="flex items-center justify-center gap-2">
                            <span>Role</span>
                            <i class="fas fa-sort sort-indicator text-gray-500"></i>
                        </div>
                    </th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-600">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                            <div>
                                <p class="xxl-text font-semibold high-contrast-text">Loading users...</p>
                                <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="noResults" class="hidden mt-8 p-12 text-center stats-card rounded-xl">
        <div class="flex flex-col items-center justify-center space-y-6">
            <i class="fas fa-search text-6xl text-gray-400"></i>
            <div>
                <p class="xxl-text font-semibold high-contrast-text">No users found</p>
                <p class="xl-text text-gray-600 mt-2">Try adjusting your search terms or filters</p>
            </div>
        </div>
    </div>
</div>

<div id="spinner" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90 backdrop-blur-sm hidden">
    <div class="enhanced-modal p-10 flex flex-col items-center space-y-6">
        <div class="w-20 h-20 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
        <div class="text-center">
            <p class="xxl-text font-semibold high-contrast-text">Loading Users</p>
            <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data...</p>
        </div>
    </div>
</div>

<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md hidden">
    <div class="enhanced-modal w-full max-w-2xl mx-4 p-8 text-gray-800 relative max-h-[90vh] overflow-y-auto">
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <div class="top-bar-icon-container p-4 rounded-2xl">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text" id="modalTitle">User Details</h3>
                    <p class="xl-text text-gray-600">View and manage user information</p>
                </div>
            </div>

            <hr class="border-gray-300">

            <div id="modalContent" class="space-y-6">
            </div>

            <hr class="border-gray-300">

            <div class="flex justify-end space-x-4 pt-6">
                <button type="button" onclick="document.getElementById('userModal').classList.add('hidden')" 
                        class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300 font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Add this JavaScript to remove any extra scroll
document.addEventListener('DOMContentLoaded', function() {
    // Remove body scroll
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    
    // Only allow scroll on table container when needed
    const tableContainer = document.querySelector('.enhanced-table-container');
    if (tableContainer) {
        tableContainer.style.overflowY = 'auto';
    }
    
    // Initialize other functions
    initEnhancedInteractions();
    initializeUserManagement();
});

function initEnhancedInteractions() {
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterUsers(e.target.value);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    }

    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.classList.add('animate-spin');
            setTimeout(() => {
                this.classList.remove('animate-spin');
            }, 1000);
            loadUsers();
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.id === 'userModal') {
            document.getElementById('userModal').classList.add('hidden');
        }
    });

    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            const currentSort = this.classList.contains('sort-asc') ? 'desc' : 'asc';
            
            sortableHeaders.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            this.classList.add(`sort-${currentSort}`);
            sortUsers(sortBy, currentSort);
        });
    });

    const closeToast = document.getElementById('closeToast');
    if (closeToast) {
        closeToast.addEventListener('click', hideEnhancedToast);
    }
}

window.showEnhancedToast = function(message, type = 'success', duration = 5000) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    if (toast && toastMessage && toastIcon) {
        toast.style.animation = 'none';
        void toast.offsetHeight;
        
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
        
        toast.style.transform = 'translateX(0)';
        
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

function initializeUserManagement() {
    loadUsers();

    document.getElementById('searchInput').addEventListener('input', function(e) {
        filterUsers(e.target.value);
    });
}

function loadUsers() {
    document.getElementById('spinner').classList.remove('hidden');
    
    fetch("../controller/end-points/controller.php?requestType=fetch_all_users")
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            document.getElementById('spinner').classList.add('hidden');
            if (response.status === 200) {
                renderUsers(response.data);
                document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
                showEnhancedToast('Users loaded successfully', 'success');
            } else {
                showUserError('No Users Found', 'Unable to load user data');
            }
        })
        .catch(error => {
            document.getElementById('spinner').classList.add('hidden');
            console.error('Error:', error);
            showUserError('Error Loading Users', 'Please try refreshing the page');
        });
}

function renderUsers(users) {
    const userTableBody = document.getElementById('userTableBody');
    
    if (!users || users.length === 0) {
        userTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-600">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <i class="fas fa-users text-6xl text-gray-400"></i>
                        <div>
                            <p class="xxl-text font-semibold high-contrast-text">No Users Found</p>
                            <p class="xl-text text-gray-600 mt-2">No users are currently registered in the system</p>
                        </div>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('noResults').classList.remove('hidden');
        return;
    }

    document.getElementById('noResults').classList.add('hidden');
    let html = '';
    
    users.forEach((user, index) => {
        const positionClass = `position-${user.user_position ? user.user_position.toLowerCase().replace(' ', '') : 'customer'}`;
        const positionDisplay = user.user_position ? user.user_position.charAt(0).toUpperCase() + user.user_position.slice(1) : 'Customer';
        
        html += `
            <tr class="table-row-hover">
                <td class="text-center font-semibold xl-text">${user.user_id || index + 1}</td>
                <td>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#d97706] to-[#b45309] rounded-full flex items-center justify-center text-white font-bold text-lg">
                            ${user.user_fname ? user.user_fname.charAt(0).toUpperCase() : 'U'}
                        </div>
                        <div>
                            <p class="font-bold text-[#92400e] xl-text">${user.user_fname || 'Unknown'} ${user.user_lname || ''}</p>
                            <p class="text-gray-600 xl-text">${user.user_phone || 'No phone'}</p>
                        </div>
                    </div>
                </td>
                <td class="xl-text font-semibold">${user.user_email || 'No email'}</td>
                <td class="text-center">
                    <span class="position-badge ${positionClass}">${positionDisplay}</span>
                </td>
                <td class="text-center space-x-3">
                    <button class="viewDetailsBtn" onclick="showUserDetails(${user.user_id || 0}, '${(user.user_fname || 'Unknown').replace(/'/g, "\\'")}', '${(user.user_lname || '').replace(/'/g, "\\'")}', '${(user.user_email || '').replace(/'/g, "\\'")}', '${((user.user_phone || '')).replace(/'/g, "\\'")}', '${(user.user_position || 'customer').replace(/'/g, "\\'")}')">
                        <i class="fas fa-eye mr-2"></i>
                        View Details
                    </button>
                    <button class="removeBtn" onclick="removeUser(${user.user_id || 0}, '${(user.user_fname || 'Unknown').replace(/'/g, "\\'")} ${(user.user_lname || '').replace(/'/g, "\\'")}')">
                        <i class="fas fa-trash mr-2"></i>
                        Remove
                    </button>
                </td>
            </tr>
        `;
    });

    userTableBody.innerHTML = html;
    updateUserStats(users);
}

function showUserError(title, message) {
    document.getElementById('userTableBody').innerHTML = `
        <tr>
            <td colspan="5" class="p-8 text-center text-red-600">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <i class="fas fa-exclamation-circle text-6xl"></i>
                    <div>
                        <p class="xxl-text font-semibold high-contrast-text">${title}</p>
                        <p class="xl-text text-gray-600 mt-2">${message}</p>
                    </div>
                </div>
            </td>
        </tr>
    `;
}

function updateUserStats(users) {
    const totalUsers = users.length;
    document.getElementById('totalUsers').textContent = totalUsers;
    document.getElementById('totalCount').textContent = totalUsers;
    document.getElementById('shownCount').textContent = totalUsers;
}

function filterUsers(searchTerm) {
    const rows = document.querySelectorAll('#userTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isVisible = text.includes(searchTerm.toLowerCase());
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });

    document.getElementById('shownCount').textContent = visibleCount;
    
    if (visibleCount === 0 && searchTerm) {
        document.getElementById('noResults').classList.remove('hidden');
    } else {
        document.getElementById('noResults').classList.add('hidden');
    }
}

function sortUsers(sortBy, direction) {
    console.log(`Sorting by ${sortBy} in ${direction} order`);
}

function showUserDetails(userId, fname, lname, email, phone, position) {
    const modalContent = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block mb-2 xl-text font-bold text-[#92400e]">First Name</label>
                <p class="form-input-enhanced bg-gray-50">${fname}</p>
            </div>
            <div>
                <label class="block mb-2 xl-text font-bold text-[#92400e]">Last Name</label>
                <p class="form-input-enhanced bg-gray-50">${lname}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 xl-text font-bold text-[#92400e]">Email Address</label>
                <p class="form-input-enhanced bg-gray-50">${email}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 xl-text font-bold text-[#92400e]">Role</label>
                <p class="form-input-enhanced bg-gray-50 capitalize">${position}</p>
            </div>
        </div>
    `;

    document.getElementById('modalTitle').textContent = `User Details - ${fname} ${lname}`;
    document.getElementById('modalContent').innerHTML = modalContent;
    document.getElementById('userModal').classList.remove('hidden');
}

function removeUser(userId, userName) {
    Swal.fire({
        title: `Remove User?`,
        html: `Are you sure you want to remove <strong>${userName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove user!',
        cancelButtonText: 'Cancel',
        background: '#fff',
        color: '#2d3748',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('spinner').classList.remove('hidden');
            
            const formData = new FormData();
            formData.append('requestType', 'removeUser');
            formData.append('user_id', userId);
            
            fetch("../controller/end-points/controller.php", {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                document.getElementById('spinner').classList.add('hidden');
                
                if (response.status === 200 || response.success === true || response.message) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'User account deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#d97706',
                        background: '#fff',
                        color: '#2d3748'
                    });
                    loadUsers();
                } else {
                    throw new Error(response.message || 'Failed to delete user account');
                }
            })
            .catch(error => {
                document.getElementById('spinner').classList.add('hidden');
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Error removing user account',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    background: '#fff',
                    color: '#2d3748'
                });
            });
        }
    });
}
</script>