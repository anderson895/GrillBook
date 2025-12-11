<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>

<style>
body {
    background: #ffffff;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    width: 100%;
    position: relative;
    font-family: 'Arial', 'Helvetica', sans-serif;
    line-height: 1.6;
    color: #2d3748;
}

body::-webkit-scrollbar {
    display: none;
}

body {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

.container, .wrapper, .max-w-7xl, .max-w-5xl {
    overflow: visible !important;
    max-height: none !important;
}

.mx-auto, .container, .main-content-wrapper {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

body > *:last-child {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
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

.table-container {
    max-height: calc(100vh - 400px);
    overflow-y: auto;
    overflow-x: hidden;
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
    padding: 12px 24px;
    font-size: 1rem;
    min-width: 180px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
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

input:focus, textarea:focus, select:focus {
    transform: scale(1.02);
    box-shadow: 
        0 0 0 3px rgba(217, 119, 6, 0.2), 
        0 4px 20px rgba(0, 0, 0, 0.1);
    border-color: #d97706;
    outline: none;
}

.btn-primary {
    background: linear-gradient(135deg, #d97706, #b45309);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    border: 3px solid #92400e;
    color: white;
    font-weight: 700;
    padding: 12px 24px;
    font-size: 1rem;
    min-width: 140px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
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

.viewDetailsBtn {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 10px 18px;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #1d4ed8;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    height: 44px;
    margin-right: 12px;
}

.viewDetailsBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
    color: white !important;
}

.removeBtn {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 10px 18px;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #b91c1c;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    height: 44px;
    margin-left: 12px;
}

.removeBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    background: linear-gradient(135deg, #DC2626, #b91c1c);
}

.form-input-enhanced {
    background: white;
    border: 2px solid #cbd5e0;
    color: #2d3748;
    padding: 1rem 1.5rem 1rem 3.5rem;
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

.search-input-container {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-input-container .search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
    font-size: 1.1rem;
    z-index: 10;
    pointer-events: none;
}

.search-input-container input {
    padding-left: 3.5rem !important;
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

.enhanced-modal {
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 3px solid #e2e8f0;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    max-height: 90vh;
    overflow-y: auto;
}

:root {
    --primary-gold: #d97706;
    --dark-gold: #b45309;
    --light-gold: #fef3c7;
    --text-dark: #2d3748;
    --text-muted: #718096;
    --border-light: #e2e8f0;
    --background-light: #f8f4e5;
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
    
    .viewDetailsBtn,
    .removeBtn {
        padding: 8px 12px;
        font-size: 0.75rem;
        min-width: 100px;
        height: 40px;
        margin-right: 8px;
        margin-left: 8px;
    }

    .enhanced-modal {
        width: 95%;
        margin: 0 auto;
    }
    
    .search-input-container {
        min-width: 100%;
    }
}

*:focus {
    outline: 3px solid #d97706;
    outline-offset: 2px;
}

::selection {
    background: #d97706;
    color: white;
}

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

.orange-icon {
    color: #d97706 !important;
}

.orange-icon-bg {
    background-color: #d97706 !important;
}

.top-bar-icon {
    color: #d97706 !important;
    font-size: 1.5rem;
}

.top-bar-icon-bg {
    background: #fef3c7 !important;
    border: 2px solid #fbbf24 !important;
}

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

.interactive-file-upload label {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(248, 250, 252, 0.8);
    border: 2px solid #e2e8f0;
}

.interactive-file-upload label.dragover {
    border-color: #d97706;
    background: rgba(217, 119, 6, 0.05);
    transform: scale(1.02);
}

.toast-progress {
    animation: toastProgress 5s linear forwards;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}

.price-currency {
    color: #d97706;
    font-weight: bold;
}

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

.btn-consistent {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 24px;
    padding: 8px 0;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 32px;
}

.cancel-btn {
    background: #6b7280;
    color: white;
    border: 2px solid #4b5563;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cancel-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.add-btn {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: white;
    border: 2px solid #92400e;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-btn:hover {
    background: linear-gradient(135deg, #b45309, #92400e);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
}

.analytics-modal {
    width: 90%;
    max-width: 800px;
    max-height: 85vh;
}

.export-modal {
    width: 90%;
    max-width: 500px;
    max-height: 70vh;
}

.chart-container {
    width: 100%;
    height: 300px;
    margin: 20px 0;
}

.export-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    margin: 10px 0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.export-option:hover {
    border-color: #d97706;
    background: #fef3c7;
}

.export-option i {
    font-size: 1.5rem;
    margin-right: 15px;
    color: #d97706;
}

.export-progress {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 10px;
}

.export-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #d97706, #b45309);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.stats-card-non-clickable {
    cursor: default !important;
}

.stats-card-non-clickable:hover {
    transform: none !important;
    border-color: #e2e8f0 !important;
    box-shadow: none !important;
}

.stats-card-non-clickable:hover::before {
    left: -100% !important;
}

.stats-card-non-clickable .group-hover\:scale-110 {
    transform: none !important;
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

.fas, .fa-utensils, .fa-sync-alt, .fa-chart-bar, .fa-download, .fa-history, 
.fa-search, .fa-filter, .fa-chevron-left, .fa-chevron-right, .fa-plus, 
.fa-plus-circle, .fa-edit, .fa-eye, .fa-trash, .fa-cloud-upload-alt, 
.fa-check-circle, .fa-save, .fa-times, .fa-ice-cream, .fa-carrot, 
.fa-drumstick-bite, .fa-glass-whiskey, .fa-layer-group, .fa-tags, 
.fa-file-csv, .fa-file-pdf, .fa-file-excel, .fa-code, .fa-exclamation-circle,
.fa-exclamation-triangle, .fa-info-circle, .fa-check {
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

.filter-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 8px;
    width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border: 2px solid #e2e8f0;
    z-index: 50;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.filter-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.filter-dropdown-content {
    padding: 20px;
}

.filter-option {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin: 6px 0;
    background: #f8fafc;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-option:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.filter-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    accent-color: #d97706;
    cursor: pointer;
}

.menu-image-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.menu-image {
    width: 96px;
    height: 96px;
    object-fit: cover;
    border-radius: 12px;
    border: 3px solid #fef3c7;
    transition: all 0.3s ease;
}

.menu-image:hover {
    transform: scale(1.05);
    border-color: #d97706;
}
</style>

<div class="glass-card mb-6">
    <div class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-4">
            <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                <i class="fas fa-utensils top-bar-icon"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Menu Management</h2>
                <p class="text-gray-600 xl-text">Manage your restaurant menu items with style</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative group">
                <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="location.reload()">
                    <i class="fas fa-sync-alt top-bar-icon group-hover:text-white"></i>
                </button>
                <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full pulse-dot border-2 border-white"></div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Total Items</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="totalMenuCount">0</h3>
            </div>
            <div class="p-3 bg-[#d97706]/10 rounded-xl">
                <i class="fas fa-utensils top-bar-icon text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-green-600 text-sm">
            <i class="fas fa-arrow-up mr-1"></i>
            <span>Live Count</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Available</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="activeMenuCount">0</h3>
            </div>
            <div class="p-3 bg-green-500/10 rounded-xl">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-green-600 text-sm">
            <i class="fas fa-eye mr-1"></i>
            <span>All Active</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Categories</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="categoryCount">0</h3>
            </div>
            <div class="p-3 bg-blue-500/10 rounded-xl">
                <i class="fas fa-layer-group text-blue-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-blue-600 text-sm">
            <i class="fas fa-tags mr-1"></i>
            <span>Diverse Menu</span>
        </div>
    </div>
</div>

<div class="glass-card p-6 rounded-2xl mb-6">
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex items-center space-x-4">
            <h3 class="text-xl font-semibold text-[#92400e] high-contrast-text">Quick Actions</h3>
            <div class="h-8 w-px bg-gray-300"></div>
            <button class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg" onclick="showAnalytics()">
                <i class="fas fa-chart-bar"></i>
                Analytics
            </button>
            <button class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg" onclick="showExportOptions()">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
        <div class="flex items-center space-x-3 xl-text text-gray-600">
            <i class="fas fa-history top-bar-icon"></i>
            <span>Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
        </div>
    </div>
</div>

<div class="glass-card rounded-2xl p-6 text-gray-800">
    <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
            <div class="search-input-container">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
                <input
                    type="text"
                    id="searchInput"
                    class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-300 rounded-xl
                           text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced xl-text"
                    placeholder="Search menu items..."
                />
            </div>

            <div class="relative" id="filterContainer">
                <button id="filterBtn" class="flex items-center gap-3 bg-white text-gray-800 px-6 py-4 rounded-xl hover:bg-gray-50 transition-all duration-300 border-2 border-gray-300 form-input-enhanced xl-text btn-consistent">
                    <i class="fas fa-filter top-bar-icon"></i>
                    <span>Filter</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="filterArrow"></i>
                </button>
                <div class="filter-dropdown" id="filterDropdown">
                    <div class="filter-dropdown-content">
                        <h4 class="text-[#d97706] font-semibold mb-4 text-lg">Filter by Category</h4>
                        <div class="space-y-2">
                            <div class="filter-option">
                                <input type="checkbox" id="filter-dessert" class="filter-checkbox" value="dessert">
                                <label for="filter-dessert" class="cursor-pointer flex items-center text-gray-700">
                                    <i class="fas fa-ice-cream text-purple-500 mr-3"></i>
                                    Dessert
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filter-appetizer" class="filter-checkbox" value="appetizer">
                                <label for="filter-appetizer" class="cursor-pointer flex items-center text-gray-700">
                                    <i class="fas fa-carrot text-orange-500 mr-3"></i>
                                    Appetizer
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filter-main" class="filter-checkbox" value="main course">
                                <label for="filter-main" class="cursor-pointer flex items-center text-gray-700">
                                    <i class="fas fa-drumstick-bite text-red-500 mr-3"></i>
                                    Main Course
                                </label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filter-beverage" class="filter-checkbox" value="beverages">
                                <label for="filter-beverage" class="cursor-pointer flex items-center text-gray-700">
                                    <i class="fas fa-glass-whiskey text-blue-500 mr-3"></i>
                                    Beverages
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button id="addMenuBtn" class="w-full lg:w-auto quick-action-btn px-8 py-4 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg btn-consistent">
            <i class="fas fa-plus group-hover:scale-110 transition-transform duration-200"></i>
            Add New Menu Item
        </button>
    </div>

    <div class="max-h-[600px] overflow-y-auto overflow-x-hidden rounded-xl border-2 border-gray-200 custom-scrollbar">
        <table class="w-full enhanced-table">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-left">Menu Item</th>
                    <th class="text-center">Category</th>
                    <th class="text-left">Description</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Image</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="menuTableBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-600">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                            <div>
                                <p class="xxl-text font-semibold high-contrast-text">Loading menu items...</p>
                                <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-300">
        <div class="text-sm text-gray-600 mb-4 sm:mb-0 xl-text">
            Showing <span class="text-[#92400e] font-semibold" id="showingFrom">0</span> to 
            <span class="text-[#92400e] font-semibold" id="showingTo">0</span> of 
            <span class="text-[#92400e] font-semibold" id="totalItems">0</span> results
        </div>
        <div class="flex items-center space-x-2">
            <button id="prevPage" class="p-3 pagination-btn rounded-lg hover:bg-[#d97706] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div id="paginationNumbers" class="flex space-x-1">
            </div>
            <button id="nextPage" class="p-3 pagination-btn rounded-lg hover:bg-[#d97706] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<div id="spinner" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90 backdrop-blur-sm hidden">
    <div class="enhanced-modal p-10 flex flex-col items-center space-y-6">
        <div class="w-20 h-20 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
        <div class="text-center">
            <p class="xxl-text font-semibold high-contrast-text">Processing Request</p>
            <p class="xl-text text-gray-600 mt-2">Please wait while we process your request...</p>
        </div>
    </div>
</div>

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

<div id="exportProgressModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal p-8 max-w-md">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                <h3 class="text-xl font-bold text-[#92400e]">Exporting Data</h3>
                <p class="text-gray-600 text-center">Preparing your export file...</p>
                <div class="w-full export-progress">
                    <div class="export-progress-bar" id="exportProgressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="analyticsModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal analytics-modal p-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 top-bar-icon-bg rounded-xl">
                        <i class="fas fa-chart-bar top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e]">Menu Analytics</h3>
                        <p class="text-gray-600">Detailed analysis of your menu items</p>
                    </div>
                </div>
                <button onclick="closeAnalytics()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="glass-card p-6">
                    <h4 class="font-semibold text-[#92400e] mb-4">Category Distribution</h4>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <h4 class="font-semibold text-[#92400e] mb-4">Price Range Analysis</h4>
                    <div class="chart-container">
                        <canvas id="priceChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-6">
                <h4 class="font-semibold text-[#92400e] mb-4">Key Metrics</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-[#fef3c7] rounded-lg">
                        <p class="text-sm text-gray-600">Total Items</p>
                        <p class="text-2xl font-bold text-[#92400e]" id="analyticsTotal">0</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600">Categories</p>
                        <p class="text-2xl font-bold text-green-600" id="analyticsCategories">0</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">Avg Price</p>
                        <p class="text-2xl font-bold text-blue-600" id="analyticsAvgPrice">₱0</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm text-gray-600">Price Range</p>
                        <p class="text-2xl font-bold text-purple-600" id="analyticsPriceRange">₱0-₱0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="exportModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal export-modal p-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 top-bar-icon-bg rounded-xl">
                        <i class="fas fa-download top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e]">Export Menu Data</h3>
                        <p class="text-gray-600">Choose your preferred export format</p>
                    </div>
                </div>
                <button onclick="closeExport()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="export-option" onclick="exportToCSV()">
                    <i class="fas fa-file-csv"></i>
                    <div>
                        <h4 class="font-semibold">CSV Format</h4>
                        <p class="text-sm text-gray-600">Compatible with Excel, Google Sheets</p>
                    </div>
                </div>
                
                <div class="export-option" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <h4 class="font-semibold">PDF Format</h4>
                        <p class="text-sm text-gray-600">Printable document with formatting</p>
                    </div>
                </div>
                
                <div class="export-option" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i>
                    <div>
                        <h4 class="font-semibold">Excel Format</h4>
                        <p class="text-sm text-gray-600">Native Excel file with formatting</p>
                    </div>
                </div>
                
                <div class="export-option" onclick="exportToJSON()">
                    <i class="fas fa-code"></i>
                    <div>
                        <h4 class="font-semibold">JSON Format</h4>
                        <p class="text-sm text-gray-600">For developers and API integration</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Export will include all menu items</span>
                    <button onclick="closeExport()" class="cancel-btn">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addMenuModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal w-full max-w-2xl mx-4 p-8 text-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                        <i class="fas fa-plus-circle top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text">Add New Menu Item</h3>
                        <p class="xl-text text-gray-600">Create a new menu item for your restaurant</p>
                    </div>
                </div>

                <hr class="border-gray-300">

                <form id="frmAddMenu" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="menuName" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Menu Item Name</label>
                            <input type="text" id="menuName" name="menuName" class="w-full form-input-enhanced" placeholder="Enter menu item name">
                        </div>

                        <div>
                            <label for="menuCategory" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Category</label>
                            <select id="menuCategory" name="menuCategory" class="w-full form-input-enhanced">
                                <option value="">Select Category</option>
                                <option value="dessert">🍰 Dessert</option>
                                <option value="appetizer">🥗 Appetizer</option>
                                <option value="soup">🍲 Soup</option>
                                <option value="salad">🥬 Salad</option>
                                <option value="main course">🍖 Main Course</option>
                                <option value="side dish">🍟 Side Dish</option>
                                <option value="beverages">🥤 Beverages</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="menuDescription" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Description</label>
                        <textarea id="menuDescription" name="menuDescription" rows="4" class="w-full form-input-enhanced resize-none" placeholder="Enter menu item description"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="menuPrice" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Price (<span class="price-currency">₱</span>)</label>
                            <input type="text" id="menuPrice" name="menuPrice" class="w-full form-input-enhanced" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Menu Image</label>
                            <div class="mt-1 flex items-center justify-center w-full interactive-file-upload">
                                <label for="menuImage" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-[#d97706] transition-all duration-300 group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3 group-hover:text-[#d97706] transition-colors duration-300"></i>
                                        <p class="xl-text text-gray-600 group-hover:text-[#92400e] transition-colors duration-300">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG (Max. 5MB)</p>
                                    </div>
                                    <input id="menuImage" name="menuImage" type="file" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="button" id="cancelAddMenu" class="cancel-btn">
                            Cancel
                        </button>
                        <button type="submit" class="add-btn">
                            <i class="fas fa-plus-circle mr-3"></i>Add Menu Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="menuDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal w-full max-w-2xl mx-4 p-8 text-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                        <i class="fas fa-edit top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text">Edit Menu Item</h3>
                        <p class="xl-text text-gray-600">Update menu item details</p>
                    </div>
                </div>

                <hr class="border-gray-300">

                <form id="frmUpdatMenu" class="space-y-6">
                    <input type="hidden" id="menu_id" name="menu_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="menu_name_update" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Menu Item Name</label>
                            <input type="text" id="menu_name_update" name="menu_name" class="w-full form-input-enhanced" placeholder="Enter menu item name">
                        </div>

                        <div>
                            <label for="menu_category_update" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Category</label>
                            <select id="menu_category_update" name="menuCategory" class="w-full form-input-enhanced">
                                <option value="">Select Category</option>
                                <option value="dessert">🍰 Dessert</option>
                                <option value="appetizer">🥗 Appetizer</option>
                                <option value="soup">🍲 Soup</option>
                                <option value="salad">🥬 Salad</option>
                                <option value="main course">🍖 Main Course</option>
                                <option value="side dish">🍟 Side Dish</option>
                                <option value="beverages">🥤 Beverages</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="menu_description_update" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Description</label>
                        <textarea id="menu_description_update" name="menu_description" rows="4" class="w-full form-input-enhanced resize-none" placeholder="Enter menu item description"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="menu_price_update" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Price (<span class="price-currency">₱</span>)</label>
                            <input type="text" id="menu_price_update" name="menu_price" class="w-full form-input-enhanced" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Update Image</label>
                            <div class="mt-1 flex items-center justify-center w-full interactive-file-upload">
                                <label for="menu_image_update" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-[#d97706] transition-all duration-300 group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-sync-alt text-gray-400 text-3xl mb-3 group-hover:text-[#d97706] transition-colors duration-300"></i>
                                        <p class="xl-text text-gray-600 group-hover:text-[#92400e] transition-colors duration-300">Click to change image</p>
                                        <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG (Max. 5MB)</p>
                                    </div>
                                    <input id="menu_image_update" name="menu_image" type="file" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="button" id="cancelUpdateMenu" class="cancel-btn">
                            Cancel
                        </button>
                        <button type="submit" class="add-btn">
                            <i class="fas fa-save mr-3"></i>Update Menu Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "../src/components/admin/footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    
    initEnhancedInteractions();
    initializeMenuSystem();
    initFilterDropdown();
});

let allMenuData = [];
let currentPage = 1;
let itemsPerPage = 10;
let filteredMenuData = [];

function initFilterDropdown() {
    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const filterArrow = document.getElementById('filterArrow');
    
    if (!filterBtn || !filterDropdown) return;
    
    filterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        filterDropdown.classList.toggle('show');
        if (filterDropdown.classList.contains('show')) {
            filterArrow.style.transform = 'rotate(180deg)';
        } else {
            filterArrow.style.transform = 'rotate(0deg)';
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!filterContainer.contains(e.target)) {
            filterDropdown.classList.remove('show');
            filterArrow.style.transform = 'rotate(0deg)';
        }
    });
    
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            filterMenuItems(document.getElementById('searchInput').value);
        });
    });
}

function initEnhancedInteractions() {
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterMenuItems(e.target.value);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    }

    const fileUpload = document.querySelectorAll('.interactive-file-upload input[type="file"]');
    fileUpload.forEach(upload => {
        const label = upload.previousElementSibling;
        
        upload.addEventListener('change', function() {
            if (this.files.length > 0) {
                label.innerHTML = `
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-3"></i>
                        <p class="xl-text text-green-600 font-semibold">${this.files[0].name}</p>
                        <p class="text-sm text-gray-500 mt-2">Click to change image</p>
                    </div>
                `;
                label.classList.add('border-green-400', 'bg-green-50');
            }
        });

        label.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        label.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });
        
        label.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
    });

    const cancelButtons = document.querySelectorAll('#cancelAddMenu, #cancelUpdateMenu');
    cancelButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('addMenuModal').classList.add('hidden');
            document.getElementById('menuDetailsModal').classList.add('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            document.getElementById('addMenuModal').classList.add('hidden');
            document.getElementById('menuDetailsModal').classList.add('hidden');
            document.getElementById('analyticsModal').classList.add('hidden');
            document.getElementById('exportModal').classList.add('hidden');
        }
    });

    document.getElementById('closeToast')?.addEventListener('click', hideEnhancedToast);
}

function filterMenuItems(searchTerm) {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox:checked');
    const selectedCategories = Array.from(filterCheckboxes).map(cb => cb.value);
    
    filteredMenuData = allMenuData.filter(item => {
        const matchesSearch = searchTerm === '' || 
            item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            item.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
            item.category.toLowerCase().includes(searchTerm.toLowerCase());
        
        const matchesCategory = selectedCategories.length === 0 || 
            selectedCategories.includes(item.category.toLowerCase());
        
        return matchesSearch && matchesCategory;
    });
    
    currentPage = 1;
    renderTable();
    updatePagination();
}

function renderTable() {
    const tableBody = document.getElementById('menuTableBody');
    if (!tableBody) return;
    
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentItems = filteredMenuData.slice(startIndex, endIndex);
    
    if (currentItems.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-600">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <i class="fas fa-utensils text-6xl text-gray-400"></i>
                        <div>
                            <p class="xxl-text font-semibold high-contrast-text">No Menu Items Found</p>
                            <p class="xl-text text-gray-600 mt-2">Try adjusting your search or filter</p>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    } else {
        tableBody.innerHTML = '';
        currentItems.forEach((item, index) => {
            const actualIndex = startIndex + index + 1;
            
            let imageHtml = '';
            if (item.image) {
                imageHtml = `
                    <div class="menu-image-container">
                        <img src="../static/upload/menu/${item.image}" 
                             alt="${item.name}"
                             class="menu-image"
                             onerror="this.onerror=null; this.src='../static/upload/default_menu.jpg';">
                    </div>`;
            } else {
                imageHtml = `
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl mb-1"></i>
                        <span class="text-gray-500 xl-text text-xs">No image</span>
                    </div>`;
            }
            
            tableBody.innerHTML += `
                <tr class="table-row-hover">
                    <td class="text-center font-semibold xl-text">${actualIndex}</td>
                    <td class="font-bold text-[#92400e] xl-text">${item.name}</td>
                    <td class="text-center capitalize xl-text">${item.category}</td>
                    <td class="xl-text">
                        ${item.description.length > 60 ? item.description.substring(0, 60) + '...' : item.description}
                    </td>
                    <td class="text-center font-bold text-[#92400e] xl-text"><span class="price-currency">₱</span>${parseFloat(item.price).toFixed(2)}</td>
                    <td class="text-center">
                        ${imageHtml}
                    </td>
                    <td class="text-center">
                        <div class="action-buttons">
                            <button class="viewDetailsBtn cursor-pointer font-semibold transition"
                                    data-menu_id="${item.id}"
                                    data-menu_name="${item.name}"
                                    data-menu_category="${item.category}"
                                    data-menu_description="${item.description}"
                                    data-menu_price="${item.price}">
                                <i class="fas fa-eye mr-2"></i>
                                View
                            </button>
                            <button class="removeBtn cursor-pointer font-semibold transition"
                                    data-menu_id="${item.id}"
                                    data-menu_name="${item.name}">
                                <i class="fas fa-trash mr-2"></i>
                                Remove
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    document.getElementById('showingFrom').textContent = startIndex + 1;
    document.getElementById('showingTo').textContent = Math.min(endIndex, filteredMenuData.length);
    document.getElementById('totalItems').textContent = filteredMenuData.length;
}

function updatePagination() {
    const totalPages = Math.ceil(filteredMenuData.length / itemsPerPage);
    const paginationNumbers = document.getElementById('paginationNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (!paginationNumbers) return;
    
    paginationNumbers.innerHTML = '';
    
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `p-3 pagination-btn rounded-lg hover:bg-[#d97706] hover:text-white transition-colors duration-200 ${i === currentPage ? 'active' : ''}`;
        pageBtn.textContent = i;
        pageBtn.addEventListener('click', () => {
            currentPage = i;
            renderTable();
            updatePagination();
        });
        paginationNumbers.appendChild(pageBtn);
    }
    
    prevBtn.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
            updatePagination();
        }
    };
    
    nextBtn.onclick = () => {
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
            updatePagination();
        }
    };
}

function showEnhancedToast(message, type = 'success', duration = 5000) {
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
}

function hideEnhancedToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(100%)';
    }
}

function showExportOptions() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExport() {
    document.getElementById('exportModal').classList.add('hidden');
}

function showExportProgress() {
    document.getElementById('exportProgressModal').classList.remove('hidden');
    const progressBar = document.getElementById('exportProgressBar');
    let width = 0;
    const interval = setInterval(() => {
        if (width >= 100) {
            clearInterval(interval);
        } else {
            width += 10;
            progressBar.style.width = width + '%';
        }
    }, 50);
}

function hideExportProgress() {
    document.getElementById('exportProgressModal').classList.add('hidden');
    document.getElementById('exportProgressBar').style.width = '0%';
}

function exportToCSV() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        const rows = filteredMenuData;
        let csvContent = "data:text/csv;charset=utf-8,";
        
        const headers = ['ID', 'Name', 'Category', 'Description', 'Price', 'Image'];
        csvContent += headers.join(",") + "\n";
        
        rows.forEach((item, index) => {
            const rowData = [
                index + 1,
                `"${item.name.replace(/"/g, '""')}"`,
                item.category,
                `"${item.description.replace(/"/g, '""')}"`,
                `₱${parseFloat(item.price).toFixed(2)}`,
                item.image ? 'Has Image' : 'No Image'
            ];
            csvContent += rowData.join(",") + "\n";
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `menu_export_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        hideExportProgress();
        showEnhancedToast('CSV file downloaded successfully!', 'success');
    }, 1000);
}

function exportToPDF() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');
        
        doc.setFontSize(20);
        doc.setTextColor(146, 64, 14);
        doc.text('Menu Items Report', 14, 20);
        
        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        doc.text(`Generated on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}`, 14, 28);
        
        const headers = ['ID', 'Name', 'Category', 'Description', 'Price', 'Image'];
        const rows = filteredMenuData.map((item, index) => [
            index + 1,
            item.name,
            item.category,
            item.description.length > 50 ? item.description.substring(0, 50) + '...' : item.description,
            `₱${parseFloat(item.price).toFixed(2)}`,
            item.image ? '✓' : '✗'
        ]);
        
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 35,
            theme: 'grid',
            styles: {
                fontSize: 9,
                cellPadding: 3,
                overflow: 'linebreak',
                cellWidth: 'wrap'
            },
            headStyles: {
                fillColor: [217, 119, 6],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [248, 244, 229]
            },
            margin: { top: 35 }
        });
        
        doc.save(`menu_export_${new Date().toISOString().split('T')[0]}.pdf`);
        
        hideExportProgress();
        showEnhancedToast('PDF file downloaded successfully!', 'success');
    }, 1000);
}

function exportToExcel() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        const wb = XLSX.utils.book_new();
        wb.Props = {
            Title: "Menu Items Export",
            Subject: "Menu Data",
            Author: "Restaurant Management System",
            CreatedDate: new Date()
        };
        
        const ws_data = [['ID', 'Name', 'Category', 'Description', 'Price', 'Image']];
        
        filteredMenuData.forEach((item, index) => {
            ws_data.push([
                index + 1,
                item.name,
                item.category,
                item.description,
                parseFloat(item.price).toFixed(2),
                item.image ? 'Yes' : 'No'
            ]);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        
        const wscols = [
            {wch: 5},
            {wch: 25},
            {wch: 15},
            {wch: 40},
            {wch: 12},
            {wch: 10}
        ];
        ws['!cols'] = wscols;
        
        XLSX.utils.book_append_sheet(wb, ws, "Menu Items");
        
        XLSX.writeFile(wb, `menu_export_${new Date().toISOString().split('T')[0]}.xlsx`);
        
        hideExportProgress();
        showEnhancedToast('Excel file downloaded successfully!', 'success');
    }, 1000);
}

function exportToJSON() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        const jsonData = {
            export_date: new Date().toISOString(),
            total_items: filteredMenuData.length,
            menu_items: filteredMenuData
        };
        
        const jsonString = JSON.stringify(jsonData, null, 2);
        const blob = new Blob([jsonString], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `menu_export_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        hideExportProgress();
        showEnhancedToast('JSON file downloaded successfully!', 'success');
    }, 1000);
}

function showAnalytics() {
    document.getElementById('analyticsModal').classList.remove('hidden');
    updateAnalyticsCharts();
}

function closeAnalytics() {
    document.getElementById('analyticsModal').classList.add('hidden');
}

function updateAnalyticsCharts() {
    const menuData = filteredMenuData;
    
    if (menuData.length === 0) return;
    
    document.getElementById('analyticsTotal').textContent = menuData.length;
    
    const categories = [...new Set(menuData.map(item => item.category))];
    document.getElementById('analyticsCategories').textContent = categories.length;
    
    const totalPrice = menuData.reduce((sum, item) => sum + parseFloat(item.price), 0);
    const avgPrice = totalPrice / menuData.length;
    document.getElementById('analyticsAvgPrice').textContent = '₱' + avgPrice.toFixed(2);
    
    const prices = menuData.map(item => parseFloat(item.price));
    const minPrice = Math.min(...prices);
    const maxPrice = Math.max(...prices);
    document.getElementById('analyticsPriceRange').textContent = `₱${minPrice.toFixed(2)}-₱${maxPrice.toFixed(2)}`;
    
    const categoryCounts = {};
    menuData.forEach(item => {
        categoryCounts[item.category] = (categoryCounts[item.category] || 0) + 1;
    });
    
    const ctx1 = document.getElementById('categoryChart');
    if (ctx1 && ctx1.chart) {
        ctx1.chart.destroy();
    }
    
    if (ctx1) {
        ctx1.chart = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: Object.keys(categoryCounts),
                datasets: [{
                    data: Object.values(categoryCounts),
                    backgroundColor: [
                        '#d97706', '#b45309', '#92400e', '#f59e0b', 
                        '#fbbf24', '#fcd34d', '#fef3c7'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
    
    const ctx2 = document.getElementById('priceChart');
    if (ctx2 && ctx2.chart) {
        ctx2.chart.destroy();
    }
    
    if (ctx2) {
        ctx2.chart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Under ₱100', '₱100-₱200', '₱200-₱300', '₱300-₱500', '₱500+'],
                datasets: [{
                    label: 'Number of Items',
                    data: [
                        menuData.filter(item => parseFloat(item.price) < 100).length,
                        menuData.filter(item => parseFloat(item.price) >= 100 && parseFloat(item.price) < 200).length,
                        menuData.filter(item => parseFloat(item.price) >= 200 && parseFloat(item.price) < 300).length,
                        menuData.filter(item => parseFloat(item.price) >= 300 && parseFloat(item.price) < 500).length,
                        menuData.filter(item => parseFloat(item.price) >= 500).length
                    ],
                    backgroundColor: '#d97706',
                    borderColor: '#b45309',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
}

function initializeMenuSystem() {
    $("#addMenuBtn").click(function (e) { 
        e.preventDefault();
        $("#addMenuModal").removeClass('hidden');
    });

    $('#frmAddMenu').on('submit', function(e) {
        e.preventDefault();

        var menuName = $('#menuName').val().trim();
        if (!menuName) {
            showEnhancedToast("Please enter menu name.", 'error');
            return;
        }

        var menuCategory = $('#menuCategory').val().trim();
        if (!menuCategory) {
            showEnhancedToast("Please select category.", 'error');
            return;
        }
        
        var menuImage = $('#menuImage').val();
        if (menuImage === "") {
            showEnhancedToast("Please upload an image.", 'error');
            return; 
        }

        var menuPrice = $('#menuPrice').val().trim();

        if (!menuPrice) {
            showEnhancedToast("Please enter a price.", 'error');
            return;
        }

        if (isNaN(menuPrice)) {
            showEnhancedToast("Price must be a valid number.", 'error');
            return;
        }

        var priceValue = parseFloat(menuPrice);

        if (priceValue <= 0) {
            showEnhancedToast("Price must be greater than zero.", 'error');
            return;
        }

        $('#spinner').show();
        $('#frmAddMenu button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        formData.append('requestType', 'AddMenu');

        $.ajax({
            type: "POST",
            url: "../controller/end-points/controller.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
                $('#spinner').hide();
                $('#frmAddMenu button[type="submit"]').prop('disabled', false);

                if (response.status === 200) {
                    showEnhancedToast('Menu item added successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'menu';
                    }, 1500);
                } else {
                    showEnhancedToast(response.message || 'Something went wrong.', 'error');
                }
            },
            error: function() {
                $('#spinner').hide();
                $('#frmAddMenu button[type="submit"]').prop('disabled', false);
                showEnhancedToast('Failed to add menu item. Please try again.', 'error');
            }
        });
    });

    $.ajax({
        url: "../controller/end-points/controller.php",
        method: "GET",
        data: { requestType: "fetch_all_menu" },
        dataType: "json",
        success: function (res) {
            if (res.status === 200) {
                allMenuData = [];
                
                if (res.data.length > 0) {
                    res.data.forEach((menu, index) => {
                        allMenuData.push({
                            id: menu.menu_id,
                            name: menu.menu_name,
                            category: menu.menu_category,
                            description: menu.menu_description,
                            price: menu.menu_price,
                            image: menu.menu_image_banner || null
                        });
                    });
                    
                    filteredMenuData = [...allMenuData];
                    renderTable();
                    updatePagination();
                    updateStatsCards(res.data);
                    document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
                } else {
                    $('#menuTableBody').html(`
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-600">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <i class="fas fa-utensils text-6xl text-gray-400"></i>
                                    <div>
                                        <p class="xxl-text font-semibold high-contrast-text">No Menu Items Found</p>
                                        <p class="xl-text text-gray-600 mt-2">Click "Add New Menu Item" to get started</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            } else {
                $('#menuTableBody').html(`
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <i class="fas fa-exclamation-triangle text-6xl text-red-400"></i>
                                <div>
                                    <p class="xxl-text font-semibold high-contrast-text">Error Loading Menu</p>
                                    <p class="xl-text text-gray-600 mt-2">${res.message || 'Failed to load menu items'}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            $('#menuTableBody').html(`
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-600">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <i class="fas fa-exclamation-triangle text-6xl text-red-400"></i>
                            <div>
                                <p class="xxl-text font-semibold high-contrast-text">Connection Error</p>
                                <p class="xl-text text-gray-600 mt-2">Failed to load menu items. Please check your connection.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            `);
        }
    });

    $(document).on("click", ".viewDetailsBtn", function () {
        const menu_id = $(this).data("menu_id");
        const menu_name = $(this).data("menu_name");
        const menu_description = $(this).data("menu_description");
        const menu_price = $(this).data("menu_price");
        const menu_category = $(this).data("menu_category");

        $("#menu_id").val(menu_id);
        $("#menu_name_update").val(menu_name);
        $("#menu_category_update").val(menu_category);
        $("#menu_description_update").val(menu_description);
        $("#menu_price_update").val(menu_price);

        $('#menuDetailsModal').removeClass('hidden');
    });

    $(document).on("click", "#cancelUpdateMenu", function () {
        $('#menuDetailsModal').addClass('hidden');
    });

    $(document).on("submit", "#frmUpdatMenu", function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        formData.append("requestType", "UpdatMenu");

        $('#spinner').show();

        $.ajax({
            url: "../controller/end-points/controller.php",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                $('#spinner').hide();
                if (response.status === 200) {
                    showEnhancedToast('Menu item updated successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showEnhancedToast(response.message || "Error updating.", 'error');
                }
            },
            error: function (xhr, status, error) {
                $('#spinner').hide();
                showEnhancedToast("Failed to update. Please try again.", 'error');
            }
        });
    });

    $(document).on('click', '.removeBtn', function(e) {
        e.preventDefault();
        const menu_id = $(this).data("menu_id");
        const menu_name = $(this).data("menu_name");

        Swal.fire({
            title: `Are you sure to Remove <span style="color:#ef4444;">${menu_name}</span>?`,
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#6b7280',
            background: '#fff',
            color: '#2d3748'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#spinner').show();
                $.ajax({
                    url: "../controller/end-points/controller.php",
                    type: 'POST',
                    data: { menu_id: menu_id, requestType: 'removeMenu' },
                    dataType: 'json', 
                    success: function(response) {
                        $('#spinner').hide();
                        if (response.status === 200) {
                            showEnhancedToast('Menu item removed successfully!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showEnhancedToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        $('#spinner').hide();
                        showEnhancedToast('There was a problem with the request.', 'error');
                    }
                });
            }
        });
    });
}

function updateStatsCards(menuData) {
    if (menuData && menuData.length > 0) {
        const totalItems = menuData.length;
        const activeItems = menuData.length;
        const categories = [...new Set(menuData.map(item => item.menu_category))].length;
        
        document.getElementById('totalMenuCount').textContent = totalItems;
        document.getElementById('activeMenuCount').textContent = activeItems;
        document.getElementById('categoryCount').textContent = categories;
        document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
        
        if (!document.getElementById('analyticsModal').classList.contains('hidden')) {
            updateAnalyticsCharts();
        }
    }
}
</script>