<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
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

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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

/* View Button Enhancement */
.viewDetailsBtn {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
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
    border: 2px solid #1d4ed8;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 140px;
    height: 44px;
}

.viewDetailsBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
}

/* Inclusions Button */
.inclusionsBtn {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
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
    border: 2px solid #6D28D9;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    height: 44px;
}

.inclusionsBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
    background: linear-gradient(135deg, #7C3AED, #6D28D9);
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
    
    .viewDetailsBtn,
    .removeBtn,
    .inclusionsBtn {
        padding: 8px 12px;
        font-size: 0.75rem;
        min-width: 100px;
        height: 40px;
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

/* Orange Icons */
.orange-icon {
    color: #d97706 !important;
}

.orange-icon-bg {
    background-color: #d97706 !important;
}

/* Fix for non-scrollable layout */
.admin-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

/* Ensure proper modal sizing */
.fixed.inset-0 {
    overflow: hidden;
}

/* Modal content max height fix */
.enhanced-modal.max-h-\[90vh\] {
    max-height: calc(100vh - 100px);
    overflow-y: auto;
}

/* Menu Selection Styles */
.menu-selection-container {
    max-height: 400px;
    overflow-y: auto;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    background: #f8fafc;
}

.menu-item-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.menu-item-card:hover {
    border-color: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.menu-item-card.selected {
    border-color: #d97706;
    background: #fefce8;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
}

.menu-item-image {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.price-input {
    width: 120px;
    padding: 8px 12px;
    border: 2px solid #cbd5e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.price-input:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2);
    outline: none;
}

/* Deal Summary Styles */
.deal-summary {
    background: #fefce8;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    padding: 16px;
    margin-top: 16px;
}

.deal-summary-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #fef3c7;
}

.deal-summary-item:last-child {
    border-bottom: none;
}

/* Menu Filter Styles */
.menu-filter {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    border: 2px solid #cbd5e0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}

.filter-btn.active {
    background: #d97706;
    color: white;
    border-color: #b45309;
}

.filter-btn:hover:not(.active) {
    border-color: #d97706;
    color: #d97706;
}

/* Menu Search */
.menu-search {
    position: relative;
    margin-bottom: 16px;
}

.menu-search input {
    padding-left: 40px;
}

.menu-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
}

/* Consistent Button Heights */
.btn-consistent {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Menu Selection Checkbox */
.menu-checkbox {
    width: 20px;
    height: 20px;
    border: 2px solid #cbd5e0;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu-checkbox.checked {
    background: #d97706;
    border-color: #d97706;
    color: white;
}

.menu-checkbox.checked::after {
    content: '✓';
    font-size: 14px;
    font-weight: bold;
}

/* Top Bar Icon Visibility */
.top-bar-icon {
    color: #d97706 !important;
    font-size: 1.5rem;
}

.top-bar-icon-bg {
    background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
    border: 2px solid #fbbf24 !important;
}

/* Search Icon Enhancement */
.search-container {
    position: relative;
}

.search-container .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    color: #d97706;
    font-size: 1.125rem;
}

.search-container input {
    padding-left: 48px !important;
}

/* Form Validation */
.invalid-input {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

.validation-error {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 4px;
    display: block;
}

/* Error Message Styling */
.error-message {
    background: #fee2e2;
    border: 2px solid #ef4444;
    border-radius: 12px;
    padding: 16px;
    color: #991b1b;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Success Message Styling */
.success-message {
    background: #d1fae5;
    border: 2px solid #10b981;
    border-radius: 12px;
    padding: 16px;
    color: #065f46;
    font-weight: 600;
    margin-bottom: 20px;
    animation: fadeIn 0.5s ease;
}

/* Image Preview Styling */
.image-preview {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
}

/* Modal Header Enhancement */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e2e8f0;
}

/* Section Spacing */
.section-spacing {
    margin-bottom: 24px;
}

/* Required Field Indicator */
.required-field::after {
    content: " *";
    color: #ef4444;
}

/* Card Hover Effects */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
}

/* Form Group Spacing */
.form-group {
    margin-bottom: 24px;
}

/* Success Check Animation */
.success-check {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    animation: checkmark 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
}

.success-check i {
    color: white;
    font-size: 40px;
}

/* Button Icons */
.btn-icon {
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
}

/* Cancel Button Professional */
.cancel-btn {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    border: 2px solid #4b5563;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.cancel-btn:hover {
    background: linear-gradient(135deg, #4b5563, #374151);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(107, 114, 128, 0.3);
}

/* Submit Button Professional */
.submit-btn {
    background: linear-gradient(135deg, #d97706, #b45309);
    border: 2px solid #92400e;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.submit-btn:hover {
    background: linear-gradient(135deg, #b45309, #92400e);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
}

/* Professional Button Styles */
.professional-btn {
    padding: 14px 28px;
    font-size: 1.125rem;
    font-weight: 600;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 52px;
    min-width: 160px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.professional-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s;
}

.professional-btn:hover::before {
    left: 100%;
}

.professional-btn i, .professional-btn svg {
    margin-right: 12px;
    font-size: 1.25rem;
}

/* Status Indicators */
.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-left: 8px;
}

.status-active-indicator {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.status-pending-indicator {
    background: #fef3c7;
    color: #92400e;
    border: 2px solid #d97706;
}

/* Enhanced Table Row Animation */
@keyframes fadeInTableRow {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-row-animate {
    animation: fadeInTableRow 0.3s ease forwards;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-overlay-content {
    text-align: center;
    animation: fadeIn 0.3s ease;
}

.loading-overlay-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #d97706;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

/* Success Toast */
.success-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 10000;
    animation: fadeIn 0.3s ease;
    max-width: 400px;
}

.success-toast i {
    font-size: 24px;
    animation: checkmark 0.5s ease;
}

.success-toast-content {
    flex: 1;
}

.success-toast-title {
    font-weight: 600;
    font-size: 1.125rem;
    margin-bottom: 4px;
}

.success-toast-message {
    font-size: 0.875rem;
    opacity: 0.9;
}

/* Banner Image Display */
.banner-display {
    width: 100px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.banner-display:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* SweetAlert Customization */
.swal2-success {
    border-color: #10b981 !important;
    color: #10b981 !important;
}

.swal2-success [class^=swal2-success-circular-line] {
    background-color: #10b981 !important;
}

.swal2-success [class^=swal2-success-line] {
    background-color: #10b981 !important;
}

/* Custom Swal Styles */
.custom-swal-success .swal2-title {
    color: #065f46 !important;
}

.custom-swal-success .swal2-html-container {
    color: #047857 !important;
}

.custom-swal-success .swal2-confirm {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    border: 2px solid #047857 !important;
}

.custom-swal-error .swal2-title {
    color: #991b1b !important;
}

.custom-swal-error .swal2-html-container {
    color: #dc2626 !important;
}

.custom-swal-error .swal2-confirm {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    border: 2px solid #b91c1c !important;
}

/* Inclusions Modal Styles */
.inclusions-modal {
    max-width: 800px !important;
    width: 90% !important;
}

.inclusion-item {
    display: flex;
    align-items: center;
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 12px;
    background: #f8fafc;
    transition: all 0.3s ease;
}

.inclusion-item:hover {
    border-color: #d97706;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.inclusion-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 16px;
    border: 2px solid #e2e8f0;
}

.inclusion-details {
    flex: 1;
}

.inclusion-name {
    font-weight: 700;
    font-size: 1.125rem;
    color: #2d3748;
    margin-bottom: 4px;
}

.inclusion-category {
    display: inline-block;
    background: #dbeafe;
    color: #1e40af;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.inclusion-price {
    font-weight: 700;
    font-size: 1.25rem;
    color: #d97706;
    margin-top: 4px;
}

.inclusion-description {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-top: 8px;
}

.no-inclusions {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.no-inclusions i {
    font-size: 48px;
    color: #9ca3af;
    margin-bottom: 16px;
}

/* Deal Info Header */
.deal-info-header {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    border: 2px solid #fbbf24;
}

.deal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #92400e;
    margin-bottom: 8px;
}

.deal-description {
    color: #6b7280;
    font-size: 1rem;
    line-height: 1.6;
}

.deal-price-tag {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1.25rem;
    display: inline-flex;
    align-items: center;
    margin-top: 12px;
}

.deal-banner-preview {
    width: 150px;
    height: 100px;
    object-fit: cover;
    border-radius: 12px;
    border: 3px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
}

.deal-banner-preview:hover {
    transform: scale(1.05);
    border-color: #d97706;
}

/* Total Price Summary */
.total-price-summary {
    background: #fefce8;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-label {
    font-size: 1.125rem;
    font-weight: 600;
    color: #92400e;
}

.total-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #d97706;
}

/* Loading Animation for Inclusions */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
}

/* Tab Styles for Item Types */
.item-type-tabs {
    display: flex;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 20px;
}

.tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    font-size: 1rem;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

.tab-btn:hover {
    color: #d97706;
}

.tab-btn.active {
    color: #d97706;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: #d97706;
    border-radius: 3px 3px 0 0;
}

/* Item Type Badges */
.menu-badge {
    background: #dbeafe;
    color: #1e40af;
    border: 2px solid #93c5fd;
}

.drink-badge {
    background: #fce7f3;
    color: #be185d;
    border: 2px solid #f9a8d4;
}

/* Item Type Icons */
.menu-icon {
    color: #3b82f6 !important;
}

.drink-icon {
    color: #db2777 !important;
}
</style>

<!-- Main Content Container -->
<div class="admin-container">
    <!-- Enhanced Top Bar -->
    <div class="glass-card mb-6 animate-fadeInUp">
        <div class="flex justify-between items-center p-6">
            <div class="flex items-center space-x-4">
                <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                    <i class="fas fa-users top-bar-icon text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Group Deals Management</h2>
                    <p class="text-gray-600 xl-text mt-2">Create and manage your restaurant group deals and packages</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="location.reload()" title="Refresh Data">
                        <i class="fas fa-sync-alt top-bar-icon group-hover:text-white text-xl"></i>
                    </button>
                    <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full pulse-dot border-2 border-white"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="glass-card p-6 rounded-2xl mb-6 animate-fadeInUp" style="animation-delay: 0.1s">
        <div class="flex flex-wrap gap-4 justify-between items-center">
            <div class="flex items-center space-x-4">
                <h3 class="text-xl font-semibold text-[#92400e] high-contrast-text">Quick Actions</h3>
                <div class="h-8 w-px bg-gray-300"></div>
                <button id="addBtn" class="professional-btn submit-btn flex items-center gap-3 group">
                    <i class="fas fa-plus-circle group-hover:scale-110 transition-transform duration-200"></i>
                    <span>Create New Group Deal</span>
                </button>
            </div>
            <div class="flex items-center space-x-3 xl-text text-gray-600">
                <i class="fas fa-history orange-icon"></i>
                <span>Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
            </div>
        </div>
    </div>

    <!-- Enhanced Table Container -->
    <div class="glass-card rounded-2xl p-6 text-gray-800 animate-fadeInUp" style="animation-delay: 0.2s">
        <!-- Search and Filter Section -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                <div class="search-container flex-1 w-full md:max-w-md">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        type="text"
                        id="searchInput"
                        class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-300 rounded-xl
                               text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced xl-text"
                        placeholder="Search group deals by name or description..."
                    />
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-600 bg-gray-100 px-4 py-2 rounded-lg">
                        <i class="fas fa-chart-bar orange-icon mr-2"></i>
                        <span id="totalDealsCount">0</span> Total Deals
                    </span>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="max-h-[500px] overflow-y-auto overflow-x-hidden rounded-xl border-2 border-gray-200 custom-scrollbar">
            <table class="w-full enhanced-table">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th class="text-center w-16">#</th>
                        <th class="text-left">Deal Name</th>
                        <th class="text-left">Description</th>
                        <th class="text-center w-32">Banner</th>
                        <th class="text-center w-80">Actions</th>
                    </tr>
                </thead>
                <tbody id="outputBody" class="divide-y divide-gray-100">
                    <!-- Dynamic Data -->
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                                <div>
                                    <p class="xxl-text font-semibold high-contrast-text">Loading Group Deals</p>
                                    <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="mt-6 flex justify-between items-center text-sm text-gray-600">
            <div>
                <i class="fas fa-info-circle orange-icon mr-2"></i>
                <span>Click on any row to view more details</span>
            </div>
            <div>
                <i class="fas fa-filter orange-icon mr-2"></i>
                <span>Use the search bar above to filter results</span>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Spinner Overlay -->
<div id="spinner" class="loading-overlay hidden">
    <div class="loading-overlay-content">
        <div class="loading-overlay-spinner"></div>
        <p class="xxl-text font-semibold high-contrast-text">Processing Request</p>
        <p class="xl-text text-gray-600 mt-2">Please wait while we process your request...</p>
    </div>
</div>

<!-- Enhanced Add Group Deal Modal -->
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md hidden">
    <div class="enhanced-modal w-full max-w-4xl mx-4 p-8 text-gray-800 relative max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="flex items-center space-x-4">
                <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                    <i class="fas fa-plus-circle top-bar-icon text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text">Create New Group Deal</h3>
                    <p class="xl-text text-gray-600">Design a package deal with multiple menu items and drinks</p>
                </div>
            </div>
            <button id="closeAddModal" class="text-gray-600 hover:text-gray-800 text-2xl font-bold transition focus:outline-none focus:ring-4 focus:ring-[#d97706] rounded-full p-2">
                ×
            </button>
        </div>

        <form id="frmCreateEntry" class="space-y-8">
            <!-- Group Information Section -->
            <div class="section-spacing">
                <h4 class="text-xl font-bold text-[#92400e] mb-6 high-contrast-text border-b pb-3 flex items-center">
                    <i class="fas fa-info-circle mr-3 orange-icon"></i>
                    Group Information
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="entryName" class="block mb-3 xl-text font-semibold text-[#92400e] required-field">Deal Name</label>
                        <input type="text" id="entryName" name="entryName" class="w-full form-input-enhanced" placeholder="Enter group deal name" required>
                        <span id="nameError" class="validation-error hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>Deal name is required
                        </span>
                    </div>
                    
                    <div class="form-group">
                        <label for="dealPrice" class="block mb-3 xl-text font-semibold text-[#92400e] required-field">Deal Price</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-600 font-semibold">₱</span>
                            <input type="number" id="dealPrice" name="dealPrice" class="w-full form-input-enhanced pl-10" placeholder="0.00" min="0" step="0.01" required>
                        </div>
                        <span id="priceError" class="validation-error hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>Valid price is required
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="entryDescription" class="block mb-3 xl-text font-semibold text-[#92400e] required-field">Description</label>
                    <textarea id="entryDescription" name="entryDescription" class="w-full form-input-enhanced resize-none" rows="4" placeholder="Describe this group deal package..." required></textarea>
                    <span id="descriptionError" class="validation-error hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>Description is required
                    </span>
                </div>
            </div>

            <!-- Items Selection Section -->
            <div class="section-spacing">
                <h4 class="text-xl font-bold text-[#92400e] mb-6 high-contrast-text border-b pb-3 flex items-center">
                    <i class="fas fa-utensils mr-3 orange-icon"></i>
                    Package Items Selection
                </h4>
                
                <!-- Item Type Tabs -->
                <div class="item-type-tabs mb-6">
                    <button type="button" class="tab-btn active" data-type="all">
                        <i class="fas fa-layer-group mr-2"></i>All Items
                    </button>
                    <button type="button" class="tab-btn" data-type="menu">
                        <i class="fas fa-utensils mr-2 menu-icon"></i>Menu Items
                    </button>
                    <button type="button" class="tab-btn" data-type="drink">
                        <i class="fas fa-wine-glass-alt mr-2 drink-icon"></i>Drinks
                    </button>
                </div>
                
                <div class="form-group">
                    <label class="block mb-4 xl-text font-semibold text-[#92400e] required-field">Select Items for Package</label>
                    
                    <!-- Items Filter and Search -->
                    <div class="menu-filter">
                        <div class="flex items-center space-x-3">
                            <button type="button" class="filter-btn active" data-category="all">
                                <i class="fas fa-layer-group mr-2"></i>All Items
                            </button>
                            <button type="button" class="filter-btn" data-category="active">
                                <i class="fas fa-check-circle mr-2"></i>Active Only
                            </button>
                            <span class="text-sm text-gray-600 bg-gray-100 px-3 py-2 rounded-lg">
                                <i class="fas fa-utensils mr-2 orange-icon"></i>
                                <span id="totalItemsCount">0</span> items available
                            </span>
                        </div>
                        <div class="menu-search flex-1">
                            <div class="relative">
                                <i class="fas fa-search absolute left-4 top-1/2 transform -translateY-1/2 orange-icon"></i>
                                <input type="text" id="itemSearch" class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:border-[#d97706] focus:ring-2 focus:ring-[#d97706]" placeholder="Search items...">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Items Container -->
                    <div class="menu-selection-container custom-scrollbar">
                        <div id="itemsContainer" class="space-y-4">
                            <div class="text-center py-12">
                                <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin mx-auto"></div>
                                <p class="mt-6 xl-text text-gray-600 font-semibold">Loading Items</p>
                                <p class="text-sm text-gray-500 mt-2">Please wait while we load available items</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected Items Summary -->
                    <div id="selectedItemsSummary" class="deal-summary hidden animate-fadeIn">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-lg text-[#92400e] flex items-center">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Selected Items (<span id="selectedCount">0</span>)
                            </h4>
                            <button type="button" onclick="clearAllSelections()" class="text-sm text-red-600 hover:text-red-800 font-semibold flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                Clear All
                            </button>
                        </div>
                        <div id="selectedItemsList" class="space-y-3">
                            <!-- Selected items will appear here -->
                        </div>
                        <div class="mt-6 pt-4 border-t border-yellow-300">
                            <div class="flex justify-between items-center font-bold text-lg">
                                <span class="text-[#92400e] flex items-center">
                                    <i class="fas fa-receipt mr-2"></i>
                                    Total Deal Price:
                                </span>
                                <span id="totalDealPrice" class="text-2xl text-[#d97706]">₱0.00</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 flex items-center">
                                <i class="fas fa-info-circle mr-2 orange-icon"></i>
                                Individual item prices can be adjusted above
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Image Section -->
            <div class="section-spacing">
                <h4 class="text-xl font-bold text-[#92400e] mb-6 high-contrast-text border-b pb-3 flex items-center">
                    <i class="fas fa-image mr-3 orange-icon"></i>
                    Banner Image
                </h4>
                <div class="form-group">
                    <label class="block mb-4 xl-text font-semibold text-[#92400e]">Upload Banner Image</label>
                    <div class="space-y-4">
                        <!-- Image Preview -->
                        <div id="imagePreviewContainer" class="hidden">
                            <div class="relative">
                                <img id="imagePreview" class="image-preview" alt="Banner preview">
                                <button type="button" onclick="removeImage()" class="absolute top-3 right-3 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-all" title="Remove Image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 text-center flex items-center justify-center">
                                <i class="fas fa-mouse-pointer mr-2 orange-icon"></i>
                                Click the image to change
                            </p>
                        </div>
                        
                        <!-- Upload Area -->
                        <div id="uploadArea" class="mt-1">
                            <label for="entryImage" class="flex flex-col items-center justify-center w-full h-48 border-3 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-[#d97706] transition-all duration-300 group">
                                <div class="flex flex-col items-center justify-center pt-8 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4 group-hover:text-[#d97706] transition-colors duration-300"></i>
                                    <p class="xl-text text-gray-600 group-hover:text-[#92400e] transition-colors duration-300 font-semibold">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG (Max. 5MB)</p>
                                </div>
                                <input id="entryImage" name="entryImage" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <span id="imageError" class="validation-error hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Please upload a valid image file
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-300">
                <button type="button" id="cancelBtn" class="professional-btn cancel-btn">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="submit" class="professional-btn submit-btn">
                    <i class="fas fa-plus-circle"></i>
                    Create Group Deal
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "../src/components/admin/footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>

<script>
// Global variables
let allMenuItems = [];
let allDrinkItems = [];
let allItems = [];
let selectedItems = [];
let currentTab = 'all';

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initEnhancedInteractions();
    loadGroupDeals();
});

function initEnhancedInteractions() {
    // Enhanced search with debouncing
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterGroupDeals(e.target.value);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    }

    // Image upload handling
    const fileUpload = document.getElementById('entryImage');
    const uploadLabel = document.querySelector('label[for="entryImage"]');
    
    if (fileUpload && uploadLabel) {
        fileUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showError('Please upload a valid image file (JPEG, JPG, PNG, WEBP)');
                    this.value = '';
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showError('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                    document.getElementById('uploadArea').classList.add('hidden');
                    document.getElementById('imageError').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Click image preview to upload new
    document.addEventListener('click', function(e) {
        if (e.target.id === 'imagePreview') {
            document.getElementById('entryImage').click();
        }
    });

    // Cancel button functionality
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            hideModal();
            resetForm();
        });
    }

    // Close modal button
    const closeAddModal = document.getElementById('closeAddModal');
    if (closeAddModal) {
        closeAddModal.addEventListener('click', function() {
            hideModal();
            resetForm();
        });
    }

    // Item search functionality
    const itemSearch = document.getElementById('itemSearch');
    if (itemSearch) {
        itemSearch.addEventListener('input', function() {
            filterItems(this.value);
        });
    }

    // Menu filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const category = this.dataset.category;
            filterItemsByCategory(category);
        });
    });

    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentTab = this.dataset.type;
            filterItemsByTab(currentTab);
        });
    });

    // Click outside modal to close
    $(document).on("click", "#addModal", function (e) {
        if ($(e.target).is("#addModal")) {
            hideModal();
            resetForm();
        }
    });

    // Form submission
    $('#frmCreateEntry').on('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            createGroupDeal();
        }
    });

    // Add button click
    $('#addBtn').click(function() {
        showModal();
        loadAllItems();
    });
}

function showModal() {
    document.getElementById('addModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function showSuccessToast(message) {
    const toast = document.getElementById('successToast');
    const toastMessage = document.getElementById('successToastMessage');
    
    if (!toast) {
        // Create toast if it doesn't exist
        const toastHTML = `
            <div id="successToast" class="success-toast">
                <i class="fas fa-check-circle text-2xl"></i>
                <div class="success-toast-content">
                    <div class="success-toast-title">Success!</div>
                    <div id="successToastMessage" class="success-toast-message">${message}</div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', toastHTML);
    } else {
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
    }
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        if (toast) toast.classList.add('hidden');
    }, 5000);
}

function showSuccess(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        iconColor: '#10b981',
        confirmButtonColor: '#10b981',
        background: '#fff',
        color: '#2d3748',
        customClass: {
            popup: 'custom-swal-success',
            title: 'swal2-title',
            htmlContainer: 'swal2-html-container',
            confirmButton: 'swal2-confirm'
        },
        showConfirmButton: true,
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
        willClose: () => {
            showSuccessToast(message);
        }
    });
}

function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        iconColor: '#ef4444',
        confirmButtonColor: '#ef4444',
        background: '#fff',
        color: '#2d3748',
        customClass: {
            popup: 'custom-swal-error',
            title: 'swal2-title',
            htmlContainer: 'swal2-html-container',
            confirmButton: 'swal2-confirm'
        },
        showConfirmButton: true,
        confirmButtonText: 'OK'
    });
}

// Item Management Functions
function loadAllItems() {
    $('#spinner').show();
    
    // Load both menu items and drinks
    $.when(
        // Load menu items
        $.ajax({
            url: "../controller/end-points/controller.php",
            method: "GET",
            data: { 
                requestType: "fetch_menus_for_deals"
            },
            dataType: "json"
        }),
        // Load drink items
        $.ajax({
            url: "../controller/end-points/controller.php",
            method: "GET",
            data: { 
                requestType: "fetch_drinks_for_deals"
            },
            dataType: "json"
        })
    ).done(function(menuResponse, drinkResponse) {
        $('#spinner').hide();
        
        // Process menu items
        if (menuResponse[0].status === 200) {
            allMenuItems = menuResponse[0].data || [];
            // Add type identifier to menu items
            allMenuItems.forEach(item => {
                item.item_type = 'menu';
                item.item_id = item.menu_id;
                item.item_name = item.menu_name;
                item.item_description = item.menu_description;
                item.item_price = item.menu_price;
                item.item_image = item.menu_image_banner;
                item.item_category = item.menu_category;
                item.item_status = item.menu_status;
            });
        }
        
        // Process drink items
        if (drinkResponse[0].status === 200) {
            allDrinkItems = drinkResponse[0].data || [];
            // Add type identifier to drink items
            allDrinkItems.forEach(item => {
                item.item_type = 'drink';
                item.item_id = item.drink_id;
                item.item_name = item.drink_name;
                item.item_description = item.drink_description;
                item.item_price = item.drink_price;
                item.item_image = item.drink_image_banner;
                item.item_category = item.drink_category;
                item.item_status = item.is_available;
            });
        }
        
        // Combine all items
        allItems = [...allMenuItems, ...allDrinkItems];
        
        // Render items
        renderItems(allItems);
        document.getElementById('totalItemsCount').textContent = allItems.length;
        
    }).fail(function() {
        $('#spinner').hide();
        $('#itemsContainer').html(`
            <div class="text-center py-8 text-red-600">
                <i class="fas fa-exclamation-circle text-4xl mb-4 orange-icon"></i>
                <p class="xl-text font-semibold">Error loading items</p>
                <p class="text-sm text-gray-600 mt-2">Unable to load items. Please check your connection.</p>
            </div>
        `);
    });
}

function renderItems(items) {
    if (!items || items.length === 0) {
        $('#itemsContainer').html(`
            <div class="text-center py-8 text-gray-600">
                <i class="fas fa-utensils text-4xl mb-4 orange-icon"></i>
                <p class="xl-text font-semibold">No items found</p>
                <p class="text-sm text-gray-500 mt-2">Add menu items or drinks first to create group deals</p>
            </div>
        `);
        return;
    }

    let html = '';
    
    items.forEach(item => {
        if (!item || !item.item_id) return;
        
        const isActive = parseInt(item.item_status) === 1;
        const statusClass = isActive ? 'status-active' : 'status-inactive';
        const statusText = isActive ? 'Active' : 'Inactive';
        const isSelected = selectedItems.some(selected => 
            selected.item_id == item.item_id && selected.item_type === item.item_type
        );
        
        // Determine item type badge and icon
        const typeBadge = item.item_type === 'menu' ? 'menu-badge' : 'drink-badge';
        const typeIcon = item.item_type === 'menu' ? 'menu-icon' : 'drink-icon';
        const typeText = item.item_type === 'menu' ? 'Menu Item' : 'Drink';
        
        html += `
            <div class="menu-item-card ${!isActive ? 'opacity-60' : ''} ${isSelected ? 'selected' : ''} card-hover" 
                 data-item-id="${item.item_id}" 
                 data-item-type="${item.item_type}"
                 data-category="${item.item_category || ''}" 
                 data-status="${item.item_status}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="menu-checkbox ${isSelected ? 'checked' : ''}" 
                             onclick="toggleItemSelection('${item.item_type}', ${item.item_id})"></div>
                        ${item.item_image ? 
                            `<img src="../static/upload/${item.item_image}" alt="${item.item_name || 'Item'}" 
                                  class="menu-item-image object-cover" onerror="this.src='../static/images/default-menu.jpg'">`
                            : 
                            `<div class="menu-item-image bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                <i class="fas ${item.item_type === 'menu' ? 'fa-utensils' : 'fa-wine-glass-alt'} text-gray-400 text-xl"></i>
                            </div>`
                        }
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-1">
                                <h4 class="font-bold text-lg text-[#92400e] truncate">${item.item_name || 'Unnamed Item'}</h4>
                                <span class="status-badge ${typeBadge} text-xs flex items-center">
                                    <i class="fas ${item.item_type === 'menu' ? 'fa-utensils' : 'fa-wine-glass-alt'} mr-1 ${typeIcon}"></i>
                                    ${typeText}
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2">${item.item_description || 'No description available'}</p>
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                <span class="font-bold text-[#d97706] bg-yellow-50 px-3 py-1 rounded-lg">₱${parseFloat(item.item_price || 0).toFixed(2)}</span>
                                <span class="status-badge ${statusClass} text-xs">${statusText}</span>
                                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">${item.item_category || 'Uncategorized'}</span>
                            </div>
                        </div>
                    </div>
                    ${isActive ? `
                    <div class="ml-4">
                        <input type="number" 
                               class="price-input ${isSelected ? '' : 'hidden'}" 
                               id="price_${item.item_type}_${item.item_id}" 
                               placeholder="Price" 
                               min="0" 
                               step="0.01"
                               value="${parseFloat(item.item_price || 0).toFixed(2)}"
                               onchange="updateItemPrice('${item.item_type}', ${item.item_id}, this.value)"
                               onblur="validatePriceInput('${item.item_type}', ${item.item_id}, this)">
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    });

    $('#itemsContainer').html(html);
}

function toggleItemSelection(itemType, itemId) {
    const itemsArray = itemType === 'menu' ? allMenuItems : allDrinkItems;
    const item = itemsArray.find(i => i.item_id == itemId);
    
    if (!item || parseInt(item.item_status) !== 1) {
        showError('Only active items can be selected');
        return;
    }

    const index = selectedItems.findIndex(selected => 
        selected.item_id == itemId && selected.item_type === itemType
    );
    
    const card = document.querySelector(`.menu-item-card[data-item-id="${itemId}"][data-item-type="${itemType}"]`);
    const checkbox = card.querySelector('.menu-checkbox');
    const priceInput = document.getElementById(`price_${itemType}_${itemId}`);

    if (index === -1) {
        selectedItems.push({
            ...item,
            item_type: itemType,
            item_id: itemId,
            deal_price: parseFloat(item.item_price || 0).toFixed(2)
        });
        checkbox.classList.add('checked');
        card.classList.add('selected');
        if (priceInput) {
            priceInput.classList.remove('hidden');
            priceInput.value = parseFloat(item.item_price || 0).toFixed(2);
        }
    } else {
        selectedItems.splice(index, 1);
        checkbox.classList.remove('checked');
        card.classList.remove('selected');
        if (priceInput) {
            priceInput.classList.add('hidden');
            priceInput.value = '';
        }
    }

    updateSelectedItemsSummary();
}

function validatePriceInput(itemType, itemId, input) {
    const value = parseFloat(input.value);
    if (isNaN(value) || value < 0) {
        input.value = '0.00';
        updateItemPrice(itemType, itemId, '0.00');
        showError('Please enter a valid price (minimum 0)');
    }
}

function updateItemPrice(itemType, itemId, price) {
    const index = selectedItems.findIndex(item => 
        item.item_id == itemId && item.item_type === itemType
    );
    
    if (index !== -1) {
        selectedItems[index].deal_price = parseFloat(price || 0).toFixed(2);
        updateSelectedItemsSummary();
    }
}

function updateSelectedItemsSummary() {
    const summaryContainer = document.getElementById('selectedItemsSummary');
    const selectedItemsList = document.getElementById('selectedItemsList');
    const totalDealPrice = document.getElementById('totalDealPrice');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedItems.length === 0) {
        summaryContainer.classList.add('hidden');
        return;
    }

    summaryContainer.classList.remove('hidden');
    selectedCount.textContent = selectedItems.length;

    let html = '';
    let total = 0;

    selectedItems.forEach(item => {
        const itemPrice = parseFloat(item.deal_price) || 0;
        total += itemPrice;
        
        const typeIcon = item.item_type === 'menu' ? 'fa-utensils menu-icon' : 'fa-wine-glass-alt drink-icon';
        const typeText = item.item_type === 'menu' ? 'Menu' : 'Drink';
        
        html += `
            <div class="deal-summary-item bg-white p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <i class="fas ${typeIcon}"></i>
                            <span class="font-semibold text-gray-800">${item.item_name || 'Unnamed Item'}</span>
                            <span class="text-xs ${item.item_type === 'menu' ? 'text-blue-600 bg-blue-50' : 'text-pink-600 bg-pink-50'} px-2 py-1 rounded">${typeText}</span>
                        </div>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-sm text-gray-600">Original: ₱${parseFloat(item.item_price || 0).toFixed(2)}</span>
                            <span class="text-sm text-[#d97706] font-semibold">→ Deal: ₱${itemPrice.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="number" 
                               class="price-input text-sm w-24" 
                               value="${itemPrice.toFixed(2)}"
                               min="0" 
                               step="0.01"
                               onchange="updateItemPrice('${item.item_type}', ${item.item_id}, this.value)">
                        <button type="button" class="text-red-500 hover:text-red-700 p-2" 
                                onclick="removeSelectedItem('${item.item_type}', ${item.item_id})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    selectedItemsList.innerHTML = html;
    totalDealPrice.textContent = `₱${total.toFixed(2)}`;

    // Update the deal price input if it exists
    const dealPriceInput = document.getElementById('dealPrice');
    if (dealPriceInput && !dealPriceInput.value && total > 0) {
        dealPriceInput.value = total.toFixed(2);
    }
}

function removeSelectedItem(itemType, itemId) {
    const index = selectedItems.findIndex(item => 
        item.item_id == itemId && item.item_type === itemType
    );
    
    if (index !== -1) {
        selectedItems.splice(index, 1);
        
        const card = document.querySelector(`.menu-item-card[data-item-id="${itemId}"][data-item-type="${itemType}"]`);
        if (card) {
            const checkbox = card.querySelector('.menu-checkbox');
            const priceInput = document.getElementById(`price_${itemType}_${itemId}`);
            
            checkbox.classList.remove('checked');
            card.classList.remove('selected');
            if (priceInput) {
                priceInput.classList.add('hidden');
                priceInput.value = '';
            }
        }
        
        updateSelectedItemsSummary();
    }
}

function clearAllSelections() {
    Swal.fire({
        title: 'Clear All Selections?',
        text: 'This will remove all selected items',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, clear all',
        cancelButtonText: 'Cancel',
        background: '#fff',
        color: '#2d3748'
    }).then((result) => {
        if (result.isConfirmed) {
            selectedItems = [];
            
            // Clear all UI selections
            document.querySelectorAll('.menu-item-card.selected').forEach(card => {
                card.classList.remove('selected');
                const checkbox = card.querySelector('.menu-checkbox');
                if (checkbox) checkbox.classList.remove('checked');
                const itemId = card.dataset.itemId;
                const itemType = card.dataset.itemType;
                const priceInput = document.getElementById(`price_${itemType}_${itemId}`);
                if (priceInput) {
                    priceInput.classList.add('hidden');
                    priceInput.value = '';
                }
            });
            
            updateSelectedItemsSummary();
        }
    });
}

function filterItems(searchTerm) {
    let filteredItems = allItems;
    
    // Filter by tab first
    if (currentTab !== 'all') {
        filteredItems = allItems.filter(item => item.item_type === currentTab);
    }
    
    // Then filter by search term
    if (searchTerm) {
        filteredItems = filteredItems.filter(item => 
            item && (
                (item.item_name && item.item_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.item_description && item.item_description.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.item_category && item.item_category.toLowerCase().includes(searchTerm.toLowerCase()))
            )
        );
    }
    
    renderItems(filteredItems);
}

function filterItemsByCategory(category) {
    let filteredItems = allItems;
    
    // Filter by tab first
    if (currentTab !== 'all') {
        filteredItems = allItems.filter(item => item.item_type === currentTab);
    }
    
    // Then filter by category
    if (category === 'active') {
        filteredItems = filteredItems.filter(item => item && parseInt(item.item_status) === 1);
    }
    
    renderItems(filteredItems);
}

function filterItemsByTab(tab) {
    let filteredItems = allItems;
    
    if (tab !== 'all') {
        filteredItems = allItems.filter(item => item.item_type === tab);
    }
    
    // Apply current search filter if any
    const searchTerm = document.getElementById('itemSearch').value;
    if (searchTerm) {
        filteredItems = filteredItems.filter(item => 
            item && (
                (item.item_name && item.item_name.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.item_description && item.item_description.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (item.item_category && item.item_category.toLowerCase().includes(searchTerm.toLowerCase()))
            )
        );
    }
    
    renderItems(filteredItems);
}

// Form Validation Functions
function validateForm() {
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('.validation-error').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.invalid-input').forEach(el => el.classList.remove('invalid-input'));
    
    // Validate group name
    const groupName = document.getElementById('entryName').value.trim();
    if (!groupName) {
        document.getElementById('nameError').classList.remove('hidden');
        document.getElementById('entryName').classList.add('invalid-input');
        isValid = false;
    }
    
    // Validate deal price
    const dealPrice = parseFloat(document.getElementById('dealPrice').value);
    if (isNaN(dealPrice) || dealPrice < 0) {
        document.getElementById('priceError').classList.remove('hidden');
        document.getElementById('dealPrice').classList.add('invalid-input');
        isValid = false;
    }
    
    // Validate description
    const description = document.getElementById('entryDescription').value.trim();
    if (!description) {
        document.getElementById('descriptionError').classList.remove('hidden');
        document.getElementById('entryDescription').classList.add('invalid-input');
        isValid = false;
    }
    
    // Validate selected items
    if (selectedItems.length === 0) {
        showError('Please select at least one item for the group deal');
        isValid = false;
    }
    
    return isValid;
}

function removeImage() {
    document.getElementById('entryImage').value = '';
    document.getElementById('imagePreviewContainer').classList.add('hidden');
    document.getElementById('uploadArea').classList.remove('hidden');
}

function resetForm() {
    // Reset form fields
    document.getElementById('frmCreateEntry').reset();
    selectedItems = [];
    
    // Clear validation errors
    document.querySelectorAll('.validation-error').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.invalid-input').forEach(el => el.classList.remove('invalid-input'));
    
    // Reset image
    document.getElementById('entryImage').value = '';
    document.getElementById('imagePreviewContainer').classList.add('hidden');
    document.getElementById('uploadArea').classList.remove('hidden');
    
    // Clear item selections
    document.querySelectorAll('.menu-item-card.selected').forEach(card => {
        card.classList.remove('selected');
        const checkbox = card.querySelector('.menu-checkbox');
        if (checkbox) checkbox.classList.remove('checked');
        const itemId = card.dataset.itemId;
        const itemType = card.dataset.itemType;
        const priceInput = document.getElementById(`price_${itemType}_${itemId}`);
        if (priceInput) {
            priceInput.classList.add('hidden');
            priceInput.value = '';
        }
    });
    
    // Hide summary
    document.getElementById('selectedItemsSummary').classList.add('hidden');
    
    // Reset tabs and filters
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.dataset.type === 'all') btn.classList.add('active');
        else btn.classList.remove('active');
    });
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.category === 'all') btn.classList.add('active');
        else btn.classList.remove('active');
    });
    
    document.getElementById('itemSearch').value = '';
    currentTab = 'all';
}

// Group Deals Management Functions
function loadGroupDeals() {
    $('#spinner').show();
    
    $.ajax({
        url: "../controller/end-points/controller.php",
        method: "GET",
        data: { 
            requestType: "fetch_group_deals"
        },
        dataType: "json",
        success: function(response) {
            $('#spinner').hide();
            if (response.status === 200 && response.data) {
                renderGroupDeals(response.data);
                document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
                document.getElementById('totalDealsCount').textContent = response.data.length;
            } else {
                $('#outputBody').html(`
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <i class="fas fa-users text-6xl text-gray-400 orange-icon"></i>
                                <div>
                                    <p class="xxl-text font-semibold high-contrast-text">No Group Deals Found</p>
                                    <p class="xl-text text-gray-600 mt-2">Click "Create Group" to get started</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `);
                document.getElementById('totalDealsCount').textContent = '0';
            }
        },
        error: function(xhr, status, error) {
            $('#spinner').hide();
            $('#outputBody').html(`
                <tr>
                    <td colspan="5" class="p-8 text-center text-red-600">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <i class="fas fa-exclamation-circle text-6xl orange-icon"></i>
                            <div>
                                <p class="xxl-text font-semibold high-contrast-text">Error Loading Group Deals</p>
                                <p class="xl-text text-gray-600 mt-2">Please try refreshing the page</p>
                            </div>
                        </div>
                    </td>
                </tr>
            `);
            document.getElementById('totalDealsCount').textContent = '0';
            console.error('Error loading group deals:', error);
        }
    });
}

function renderGroupDeals(deals) {
    if (!deals || !Array.isArray(deals) || deals.length === 0) {
        $('#outputBody').html(`
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-600">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <i class="fas fa-users text-6xl text-gray-400 orange-icon"></i>
                        <div>
                            <p class="xxl-text font-semibold high-contrast-text">No Group Deals Created Yet</p>
                            <p class="xl-text text-gray-600 mt-2">Click "Create Group" to add your first group deal</p>
                        </div>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    
    deals.forEach((deal, index) => {
        if (!deal) return;
        
        const bannerUrl = deal.deal_img_banner ? `../static/upload/${deal.deal_img_banner}` : '../static/images/default-banner.jpg';
        
        html += `
            <tr class="table-row-hover table-row-animate" style="animation-delay: ${index * 0.1}s">
                <td class="text-center font-semibold xl-text">${index + 1}</td>
                <td class="font-bold text-[#92400e] xl-text">${deal.deal_name || 'Unnamed Deal'}</td>
                <td class="xl-text">
                    <div class="max-w-md">
                        ${deal.deal_description ? (deal.deal_description.length > 60 ? deal.deal_description.substring(0, 60) + '...' : deal.deal_description) : 'No description'}
                    </div>
                </td>
                <td class="text-center">
                    <div class="flex justify-center items-center">
                        <img src="${bannerUrl}" 
                             alt="Deal Banner" 
                             class="banner-display object-cover cursor-pointer hover:scale-110 transition-transform duration-300"
                             onerror="this.src='../static/images/default-banner.jpg'"
                             onclick="showBannerPreview('${bannerUrl}', '${deal.deal_name || 'Deal Banner'}')">
                    </div>
                </td>
                <td class="text-center space-x-3">
                    <button class="inclusionsBtn cursor-pointer font-semibold transition"
                            data-deal_id="${deal.deal_id}"
                            data-deal_name="${deal.deal_name || 'Deal'}">
                        <i class="fas fa-list mr-2"></i>
                        Inclusions
                    </button>
                    <button class="viewDetailsBtn cursor-pointer font-semibold transition"
                            data-deal_id="${deal.deal_id}"
                            data-deal_name="${deal.deal_name || 'Deal'}">
                        <i class="fas fa-eye mr-2"></i>
                        View Details
                    </button>
                    <button class="removeBtn cursor-pointer font-semibold transition"
                            data-deal_id="${deal.deal_id}"
                            data-deal_name="${deal.deal_name || 'Deal'}">
                        <i class="fas fa-trash mr-2"></i>
                        Remove
                    </button>
                </td>
            </tr>
        `;
    });

    $('#outputBody').html(html);

    // Add button functionality
    $('.inclusionsBtn').off('click').on('click', function() {
        const dealId = $(this).data('deal_id');
        const dealName = $(this).data('deal_name');
        viewDealInclusions(dealId, dealName);
    });

    $('.viewDetailsBtn').off('click').on('click', function() {
        const dealId = $(this).data('deal_id');
        const dealName = $(this).data('deal_name');
        viewGroupDetails(dealId, dealName);
    });

    $('.removeBtn').off('click').on('click', function() {
        const dealId = $(this).data('deal_id');
        const dealName = $(this).data('deal_name');
        removeGroupDeal(dealId, dealName);
    });
}

function showBannerPreview(imageUrl, title) {
    Swal.fire({
        title: title,
        html: `<img src="${imageUrl}" alt="Banner Preview" class="w-full h-auto rounded-lg" onerror="this.src='../static/images/default-banner.jpg'">`,
        showConfirmButton: false,
        showCloseButton: true,
        width: '600px',
        background: '#fff',
        customClass: {
            popup: 'rounded-2xl'
        }
    });
}

function filterGroupDeals(searchTerm) {
    const rows = $('#outputBody tr');
    rows.each(function() {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.includes(searchTerm.toLowerCase()));
    });
}

function createGroupDeal() {
    if (selectedItems.length === 0) {
        showError('Please select at least one item for the group deal');
        return;
    }

    const formData = new FormData(document.getElementById('frmCreateEntry'));
    formData.append('requestType', 'create_group_deal');
    
    // Add selected items as JSON
    const itemsData = selectedItems.map(item => ({
        item_type: item.item_type,
        item_id: item.item_id,
        deal_price: item.deal_price
    }));
    formData.append('selected_items', JSON.stringify(itemsData));

    $('#spinner').show();

    $.ajax({
        url: "../controller/end-points/controller.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            $('#spinner').hide();
            if (response.status === 200) {
                showSuccess(response.message || 'Group deal created successfully!');
                hideModal();
                resetForm();
                loadGroupDeals();
            } else {
                showError(response.message || 'Error creating group deal');
            }
        },
        error: function(xhr, status, error) {
            $('#spinner').hide();
            showError('Error creating group deal. Please try again.');
            console.error('Error creating group deal:', error);
        }
    });
}

function viewGroupDetails(dealId, dealName) {
    window.location.href = `group_deal_menus.php?deal_id=${dealId}&deal_name=${encodeURIComponent(dealName)}`;
}

function removeGroupDeal(dealId, dealName) {
    Swal.fire({
        title: 'Remove Group Deal?',
        html: `Are you sure you want to remove <strong>${dealName}</strong>?<br><br>
              <small class="text-gray-600">This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel',
        background: '#fff',
        color: '#2d3748',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "../controller/end-points/controller.php",
                    method: "POST",
                    data: {
                        requestType: "remove_group_deal",
                        deal_id: dealId
                    },
                    dataType: "json",
                    success: function(response) {
                        resolve(response);
                    },
                    error: function() {
                        reject();
                    }
                });
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.status === 200) {
                showSuccess(response.message || 'Group deal removed successfully');
                loadGroupDeals();
            } else {
                showError(response.message || 'Error removing group deal');
            }
        }
    });
}

function viewDealInclusions(dealId, dealName) {
    $('#spinner').show();
    
    $.ajax({
        url: "../controller/end-points/controller.php",
        method: "POST",
        data: {
            requestType: "fetch_deal_inclusions",
            deal_id: dealId
        },
        dataType: "json",
        success: function(response) {
            $('#spinner').hide();
            if (response.status === 200) {
                const deal = response.deal || {};
                const inclusions = response.inclusions || [];
                
                showInclusionsModal(deal, inclusions, dealName);
            } else {
                showError(response.message || 'Error loading deal inclusions');
            }
        },
        error: function(xhr, status, error) {
            $('#spinner').hide();
            showError('Unable to load deal inclusions. Please try again.');
            console.error('Error loading deal inclusions:', error);
        }
    });
}

function showInclusionsModal(deal, inclusions, dealName) {
    // Get banner URL
    const bannerUrl = deal.deal_img_banner ? 
        `../static/upload/${deal.deal_img_banner}` : 
        '../static/images/default-banner.jpg';
    
    // Build inclusions HTML
    let inclusionsHtml = '';
    let totalPrice = 0;
    
    if (inclusions && inclusions.length > 0) {
        inclusions.forEach(item => {
            const itemPrice = parseFloat(item.item_price || 0);
            totalPrice += itemPrice;
            
            const imageUrl = item.item_image ? 
                `../static/upload/${item.item_image}` : 
                '../static/images/default-menu.jpg';
            
            const typeIcon = item.item_type === 'menu' ? 'fa-utensils menu-icon' : 'fa-wine-glass-alt drink-icon';
            const typeText = item.item_type === 'menu' ? 'Menu Item' : 'Drink';
            const typeBadge = item.item_type === 'menu' ? 'menu-badge' : 'drink-badge';
            
            inclusionsHtml += `
                <div class="inclusion-item">
                    <img src="${imageUrl}" 
                         alt="${item.item_name || 'Item'}" 
                         class="inclusion-image"
                         onerror="this.src='../static/images/default-menu.jpg'">
                    <div class="inclusion-details">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="inclusion-name">${item.item_name || 'Unnamed Item'}</div>
                            <span class="status-badge ${typeBadge} text-xs flex items-center">
                                <i class="fas ${typeIcon} mr-1"></i>
                                ${typeText}
                            </span>
                        </div>
                        <div class="inclusion-category">${item.item_category || 'Uncategorized'}</div>
                        <div class="inclusion-price">₱${itemPrice.toFixed(2)}</div>
                        ${item.item_description ? `
                            <div class="inclusion-description">
                                ${item.item_description.length > 100 ? 
                                    item.item_description.substring(0, 100) + '...' : 
                                    item.item_description}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
    } else {
        inclusionsHtml = `
            <div class="no-inclusions">
                <i class="fas fa-utensils text-gray-400"></i>
                <div class="mt-4">
                    <p class="font-semibold text-gray-600">No Items Included</p>
                    <p class="text-sm text-gray-500 mt-2">This deal doesn't contain any items yet.</p>
                </div>
            </div>
        `;
    }
    
    // Calculate savings if deal price exists
    let savingsHtml = '';
    if (deal.deal_price) {
        const dealPrice = parseFloat(deal.deal_price);
        const savings = totalPrice - dealPrice;
        if (savings > 0) {
            savingsHtml = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-piggy-bank text-green-600 mr-3 text-xl"></i>
                            <div>
                                <div class="font-semibold text-green-800">Customer Saves: ₱${savings.toFixed(2)}</div>
                                <div class="text-sm text-green-600">(${((savings / totalPrice) * 100).toFixed(1)}% off regular price)</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    Swal.fire({
        title: `<strong>${dealName}</strong>`,
        html: `
            <div class="deal-info-header">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="deal-title">${dealName}</div>
                        <div class="deal-description">${deal.deal_description || 'No description available'}</div>
                        ${deal.deal_price ? `
                            <div class="mt-3">
                                <span class="deal-price-tag">
                                    <i class="fas fa-tag mr-2"></i>
                                    Deal Price: ₱${parseFloat(deal.deal_price).toFixed(2)}
                                </span>
                            </div>
                        ` : ''}
                    </div>
                    <div class="ml-6">
                        <img src="${bannerUrl}" 
                             alt="${dealName}" 
                             class="deal-banner-preview"
                             onclick="showBannerPreview('${bannerUrl}', '${dealName}')"
                             onerror="this.src='../static/images/default-banner.jpg'">
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="font-bold text-lg text-[#92400e] mb-4 flex items-center">
                    <i class="fas fa-utensils mr-2"></i>
                    Included Items (${inclusions.length})
                </h3>
                <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar">
                    ${inclusionsHtml}
                </div>
            </div>
            
            ${inclusions.length > 0 ? `
                <div class="total-price-summary">
                    <div class="total-label">
                        <i class="fas fa-calculator mr-2"></i>
                        Total Regular Price:
                    </div>
                    <div class="total-value">₱${totalPrice.toFixed(2)}</div>
                </div>
                ${savingsHtml}
            ` : ''}
        `,
        showConfirmButton: false,
        showCloseButton: true,
        width: '800px',
        background: '#fff',
        customClass: {
            popup: 'inclusions-modal',
            htmlContainer: 'text-left'
        },
        didOpen: () => {
            // Add click event to banner preview
            const bannerImg = document.querySelector('.deal-banner-preview');
            if (bannerImg) {
                bannerImg.addEventListener('click', function() {
                    showBannerPreview(bannerUrl, dealName);
                });
            }
        }
    });
}
</script>