<?php
include "../src/components/headstaff/header.php";
include "../src/components/headstaff/nav.php";

$deal_name = $_GET['deal_name'];
$deal_id = $_GET['deal_id'];
?>

<style>
/* Professional Interactive Styles */
.glass-card {
  background: rgba(26, 26, 26, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.form-input {
  background: #0D0D0D;
  border: 1px solid #333333;
  color: #CCCCCC;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #FFD700;
  box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
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

.pulse-glow {
  animation: pulseGlow 2s infinite;
}

@keyframes pulseGlow {
  0%, 100% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.3); }
  50% { box-shadow: 0 0 30px rgba(255, 215, 0, 0.5); }
}

.real-time-clock {
  font-family: 'Courier New', monospace;
  background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 100%);
  border: 2px solid #FFD700;
}

/* Menu Card Styles */
.menu-card {
  background: linear-gradient(135deg, rgba(26, 26, 26, 0.9) 0%, rgba(13, 13, 13, 0.9) 100%);
  border: 1px solid rgba(255, 215, 0, 0.2);
  transition: all 0.3s ease;
  cursor: pointer;
}

.menu-card:hover {
  transform: translateY(-5px);
  border-color: rgba(255, 215, 0, 0.4);
  box-shadow: 0 12px 40px rgba(255, 215, 0, 0.1);
}

/* Action Buttons */
.action-btn {
  transition: all 0.3s ease;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
}

.action-btn:hover {
  transform: translateY(-2px);
}

.btn-remove {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.btn-remove:hover {
  background: rgba(239, 68, 68, 0.3);
}

.btn-add {
  background: rgba(34, 197, 94, 0.2);
  color: #22c55e;
  border: 1px solid rgba(34, 197, 94, 0.3);
}

.btn-add:hover {
  background: rgba(34, 197, 94, 0.3);
}

/* Loading States */
.loading-shimmer {
  background: linear-gradient(90deg, #1A1A1A 25%, #2A2A2A 50%, #1A1A1A 75%);
  background-size: 200% 100%;
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Toast Notifications */
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  background: rgba(26, 26, 26, 0.95);
  border: 1px solid rgba(255, 215, 0, 0.3);
  border-radius: 12px;
  padding: 16px;
  color: white;
  z-index: 10000;
  transform: translateX(100%);
  transition: transform 0.3s ease;
  min-width: 300px;
}

.toast.show {
  transform: translateX(0);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-state i {
  font-size: 4rem;
  color: #666;
  margin-bottom: 1rem;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #1A1A1A;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #FFD700;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #FFA500;
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

/* Menu Grid */
.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

@media (max-width: 768px) {
  .menu-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<!-- Professional Header with Interactive Elements -->
<div class="glass-card rounded-2xl mb-8 fade-in">
  <div class="flex justify-between items-center p-6">
    <div class="flex items-center space-x-4">
      <div class="p-3 bg-gradient-to-br from-[#FFD700] to-[#FFA500] rounded-xl shadow-lg pulse-glow">
        <i class="fas fa-utensils text-black text-xl"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-[#FFD700] uppercase tracking-wide">Deal Menu Management</h1>
        <p class="text-gray-400 text-sm mt-1">Manage menus for: <?= htmlspecialchars($deal_name) ?></p>
      </div>
    </div>
    <div class="flex items-center space-x-4">
      <!-- Real-time Clock -->
      <div class="real-time-clock px-4 py-2 rounded-xl text-center min-w-[180px]">
        <div class="text-sm text-gray-400 uppercase tracking-wider">Current Time</div>
        <div id="realTimeClock" class="text-xl font-bold text-[#FFD700] font-mono"></div>
        <div id="realTimeDate" class="text-xs text-gray-400 mt-1"></div>
      </div>
      
      <!-- Refresh Button -->
      <button onclick="refreshMenus()" class="p-3 glass-card rounded-xl hover:bg-[#FFD700] hover:text-black transition-all duration-300 interactive-hover" title="Refresh Menus">
        <i class="fas fa-sync-alt"></i>
      </button>
      
      <!-- Add Menu Button -->
      <button onclick="openAddModal()" class="p-3 glass-card rounded-xl hover:bg-[#FFD700] hover:text-black transition-all duration-300 interactive-hover" title="Add Menu">
        <i class="fas fa-plus"></i>
      </button>
      
      <!-- User Avatar -->
      <div class="w-10 h-10 bg-gradient-to-br from-[#FFD700] to-[#FFA500] rounded-full flex items-center justify-center text-black font-bold shadow-lg ring-2 ring-yellow-400/30 interactive-hover">
        <?= strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
      </div>
    </div>
  </div>
</div>

<!-- Interactive Controls Section -->
<div class="glass-card rounded-2xl p-6 mb-6 fade-in">
  <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
    <!-- Search Section -->
    <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
      <div class="search-container relative flex-1 sm:max-w-xs">
        <div class="search-icon">
          <i class="fas fa-search"></i>
        </div>
        <input
          type="text"
          id="searchInput"
          class="w-full search-input px-4 py-3 form-input rounded-xl placeholder-gray-400 transition-all duration-300"
          placeholder="Search menus..."
          onkeyup="handleSearch(event)"
        />
      </div>

      <!-- Quick Stats -->
      <div class="flex items-center space-x-4 text-sm text-gray-400">
        <div class="flex items-center space-x-2 bg-gray-800/50 px-3 py-1 rounded-full">
          <i class="fas fa-utensils text-[#FFD700]"></i>
          <span>Total Menus: <span id="totalCount" class="text-[#FFD700] font-semibold">0</span></span>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2">
      <button onclick="showAllMenus()" class="px-4 py-2 bg-[#FFD700] text-black rounded-lg font-semibold interactive-hover">
        All Menus
      </button>
      <button onclick="showAvailableMenus()" class="px-4 py-2 bg-gray-700 text-white rounded-lg font-semibold interactive-hover">
        Available
      </button>
    </div>
  </div>
</div>

<!-- Interactive Menu Grid Container -->
<div class="glass-card rounded-2xl p-6 fade-in">
  <div id="outputBody" 
       class="menu-grid custom-scrollbar"
       data-deal_id="<?= $deal_id ?>">
    
    <!-- Loading State -->
    <div class="col-span-full text-center py-12">
      <div class="flex flex-col items-center justify-center space-y-3">
        <div class="w-12 h-12 border-4 border-[#FFD700] border-t-transparent rounded-full animate-spin"></div>
        <p class="text-lg text-gray-400">Loading menu data...</p>
        <p class="text-sm text-gray-500">Please wait while we fetch the menus</p>
      </div>
    </div>
  </div>

  <!-- Empty State -->
  <div id="emptyState" class="empty-state hidden">
    <div class="flex flex-col items-center justify-center space-y-4">
      <i class="fas fa-utensils text-gray-600"></i>
      <div>
        <h3 class="text-lg font-semibold text-gray-400 mb-2">No Menus Found</h3>
        <p class="text-gray-500 text-sm">No menus are currently added to this deal.</p>
        <button onclick="openAddModal()" class="mt-4 px-6 py-2 bg-[#FFD700] text-black rounded-lg font-semibold interactive-hover">
          <i class="fas fa-plus mr-2"></i>Add First Menu
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Add Menu Modal -->
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
  <div class="glass-card rounded-2xl shadow-2xl w-full max-w-md mx-4 p-8 text-[#CCCCCC] relative slide-in">
    <!-- Close Button -->
    <button id="closeAddModal" class="absolute top-6 right-6 p-2 hover:bg-gray-800 rounded-xl transition-all duration-200 interactive-hover">
      <i class="fas fa-times text-xl text-gray-400 hover:text-white"></i>
    </button>

    <!-- Modal Header -->
    <div class="flex items-center space-x-4 mb-6">
      <div class="p-3 bg-gradient-to-br from-[#FFD700] to-[#FFA500] rounded-xl">
        <i class="fas fa-plus text-black text-xl"></i>
      </div>
      <div>
        <h2 class="text-2xl font-bold text-[#FFD700]">Add Menu to Deal</h2>
        <p class="text-gray-400 text-sm">Select a menu to add to <?= htmlspecialchars($deal_name) ?></p>
      </div>
    </div>

    <hr class="border-gray-700 mb-6">

    <!-- Modal Content -->
    <div id="modalContent">
      <form id="frmAddMenuDeals" class="space-y-6">
        <input type="hidden" id="deal_id" name="deal_id" value="<?= $deal_id ?>">
        
        <div class="space-y-4">
          <label class="block text-sm font-semibold text-[#FFD700]">Select Menu</label>
          <div class="relative">
            <div class="input-icon">
              <i class="fas fa-utensils"></i>
            </div>
            <select id="menuSelect" name="menu" class="w-full pl-10 pr-4 py-3 form-input rounded-xl focus:outline-none transition-all duration-300">
              <option value="">Choose a menu...</option>
            </select>
          </div>
          <p class="text-xs text-gray-500">Select from available menus to add to this deal</p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
          <button type="button" onclick="closeAddModal()" class="px-6 py-3 bg-gray-600 text-white rounded-xl font-semibold interactive-hover">
            Cancel
          </button>
          <button type="submit" class="px-6 py-3 bg-[#FFD700] text-black rounded-xl font-semibold interactive-hover">
            <i class="fas fa-plus mr-2"></i>Add Menu
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enhanced Loading Overlay -->
<div id="spinnerOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
  <div class="glass-card p-8 rounded-2xl flex flex-col items-center space-y-4">
    <div class="w-16 h-16 border-4 border-[#FFD700] border-t-transparent rounded-full animate-spin"></div>
    <div class="text-center">
      <p class="text-white text-lg font-semibold">Processing Request</p>
      <p class="text-gray-400 text-sm mt-1">Please wait while we process your action...</p>
    </div>
  </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<?php include "../src/components/headstaff/footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

<script>
// Interactive Functions - Preserves your existing deal_menus.js functionality

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
        if (typeof filterMenus === 'function') {
            filterMenus(searchTerm);
        }
        showToast(`Searching for: ${searchTerm}`, 'info');
    }, 500);
}

// Refresh Function
function refreshMenus() {
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
    
    showToast('Refreshing menus...', 'info');
    
    // Call your existing refresh function
    if (typeof loadMenus === 'function') {
        loadMenus();
    }
}

// Modal Functions
function openAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.remove('hidden');
    showToast('Add menu modal opened', 'info');
    
    // Call your existing function to load menu options
    if (typeof loadMenuOptions === 'function') {
        loadMenuOptions();
    }
}

function closeAddModal() {
    const modal = document.getElementById('addModal');
    modal.classList.add('hidden');
}

// Filter Functions
function showAllMenus() {
    showToast('Showing all menus', 'info');
    // Call your existing function
    if (typeof loadMenus === 'function') {
        loadMenus();
    }
}

function showAvailableMenus() {
    showToast('Showing available menus', 'info');
    // Call your existing function with filter
    if (typeof filterMenus === 'function') {
        filterMenus('available');
    }
}

// Toast Notification System
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    const typeConfig = {
        info: { icon: 'info-circle', color: 'text-blue-500' },
        success: { icon: 'check-circle', color: 'text-green-500' },
        warning: { icon: 'exclamation-triangle', color: 'text-yellow-500' },
        error: { icon: 'exclamation-circle', color: 'text-red-500' }
    };
    
    const config = typeConfig[type] || typeConfig.info;
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `glass-card p-4 rounded-xl text-white transform translate-x-full transition-transform duration-500`;
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="fas ${config.icon} ${config.color} text-xl"></i>
            <div class="flex-1">
                <p class="font-semibold">${message}</p>
                <p class="text-gray-400 text-xs">Just now</p>
            </div>
            <button onclick="dismissToast('${toastId}')" class="text-gray-400 hover:text-white interactive-hover">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto dismiss after 4 seconds
    setTimeout(() => {
        dismissToast(toastId);
    }, 4000);
}

function dismissToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            toast.remove();
        }, 500);
    }
}

// Enhanced Modal Interactions
function setupModalInteractions() {
    // Close modal on backdrop click
    document.addEventListener('click', function(e) {
        if (e.target.id === 'addModal') {
            closeAddModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
        }
    });

    // Modal close buttons
    document.getElementById('closeAddModal')?.addEventListener('click', closeAddModal);
}

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

// Menu Card Interactions
function setupMenuCardInteractions() {
    document.addEventListener('click', function(e) {
        const menuCard = e.target.closest('.menu-card');
        if (menuCard && !e.target.closest('button')) {
            // Add visual feedback for card clicks
            menuCard.style.transform = 'scale(0.98)';
            setTimeout(() => {
                menuCard.style.transform = '';
            }, 300);
            
            showToast('Menu details clicked', 'info');
        }
    });
}

// Initialize interactive elements when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupModalInteractions();
    setupMenuCardInteractions();
    
    // Add staggered animation to menu cards when they load
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList.contains('menu-card')) {
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
    
    showToast('Deal menus management loaded', 'success');
});

// Export functions for your existing deal_menus.js
window.dealMenuInteractions = {
    refreshMenus,
    openAddModal,
    closeAddModal,
    showAllMenus,
    showAvailableMenus,
    handleSearch,
    showToast,
    updateTotalCount
};
</script>

<script src="../static/js/headstaff/deal_menus.js"></script>