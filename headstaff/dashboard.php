<?php
include "../src/components/headstaff/header.php";
include "../src/components/headstaff/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide">Dashboard</h2>
  <div class="w-10 h-10 bg-[#1A1A1A] rounded-full flex items-center justify-center text-[#FFD700] font-bold shadow-md">
    <?php echo strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-[#0D0D0D] min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-[#FFD700] flex items-center space-x-2">
    <span class="material-icons text-[#FFD700]">insert_chart</span>
    <span>Admin Dashboard</span>
    </h1>


  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">person</span>
      <div>
        <p class="text-gray-400">Users</p>
        <h2 class="text-3xl font-bold text-white" id="totalUsers">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">event</span>
      <div>
        <p class="text-gray-400">Reservations</p>
        <h2 class="text-3xl font-bold text-white" id="totalReservations">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">hourglass_empty</span>
      <div>
        <p class="text-gray-400">Pending</p>
        <h2 class="text-3xl font-bold text-yellow-400" id="pendingReservations">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">check_circle</span>
      <div>
        <p class="text-gray-400">Confirmed</p>
        <h2 class="text-3xl font-bold text-green-400" id="confirmedReservations">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">flag</span>
      <div>
        <p class="text-gray-400">Completed</p>
        <h2 class="text-3xl font-bold text-blue-400" id="completedReservations">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">restaurant_menu</span>
      <div>
        <p class="text-gray-400">Active Menu</p>
        <h2 class="text-3xl font-bold text-white" id="activeMenuItems">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">local_offer</span>
      <div>
        <p class="text-gray-400">Promos</p>
        <h2 class="text-3xl font-bold text-white" id="totalPromos">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">groups</span>
      <div>
        <p class="text-gray-400">Group Deals</p>
        <h2 class="text-3xl font-bold text-white" id="totalGroupDeals">0</h2>
      </div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-[#FFD700] text-4xl">₱</span>
      <div>
        <p class="text-gray-400">Total Sales</p>
        <h2 class="text-3xl font-bold text-green-400" id="totalSales">₱0.00</h2>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4">
      <h2 class="text-lg font-semibold mb-4 flex items-center space-x-2 text-white">
        <span class="material-icons text-[#FFD700]">donut_large</span>
        <span>Reservations by Status</span>
      </h2>
      <div id="reservationsChart"></div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4">
      <h2 class="text-lg font-semibold mb-4 flex items-center space-x-2 text-white">
        <span class="material-icons text-[#FFD700]">bar_chart</span>
        <span>Sales Overview</span>
      </h2>
      <div id="salesChart"></div>
    </div>
  </div>
</div>

<?php
include "../src/components/admin/footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="../static/js/admin/dashboard.js"></script>
