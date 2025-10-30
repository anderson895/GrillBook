<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
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
      <div  id="reservationsChart"></div>
    </div>
    <div class="bg-[#1A1A1A] shadow rounded-xl p-4">
      <h2 class="text-lg font-semibold mb-4 flex items-center space-x-2 text-white">
        <span class="material-icons text-[#FFD700]">bar_chart</span>
        <span>Sales Overview</span>
      </h2>
      <div id="salesChart"></div>
    </div>
  </div>








  
<!--START TABLE LAYOUT -->
<!-- Page Container -->
<div class="flex flex-col items-center justify-start min-h-screen bg-[#1A1A1A] shadow rounded-xl mt-8">
  <!-- Main content wrapper -->
  <div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8 " >

<!-- Legends Section -->
<div class="flex flex-wrap gap-4 mt-4 mb-12 p-8">
  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-yellow-200 border border-yellow-500 rounded"></div>
    <span class="font-medium text-gray-700">Pending</span>
  </div>
  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-green-200 border border-green-500 rounded"></div>
    <span class="font-medium text-gray-700">Confirmed</span>
  </div>
  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-red-200 border border-red-500 rounded"></div>
    <span class="font-medium text-gray-700">Cancelled</span>
  </div>
  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-orange-200 border border-orange-500 rounded"></div>
    <span class="font-medium text-gray-700">Request Cancel</span>
  </div>
  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-purple-200 border border-purple-500 rounded"></div>
    <span class="font-medium text-gray-700">Request New Schedule</span>
  </div>

  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-blue-200 border border-blue-500 rounded"></div>
    <span class="font-medium text-gray-700">Available</span>
  </div>
  <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer">
    <div class="w-5 h-5 bg-gray-300 border border-gray-500 rounded"></div>
    <span class="font-medium text-gray-700">Walk-in</span>
  </div>


</div>


   <!-- Scrollable Grid Only -->
<div class="overflow-auto scrollbar-hidden">
  <div class="table-grid grid grid-cols-12 grid-rows-[repeat(10,minmax(0,1fr))] gap-2 sm:gap-4 p-2 sm:p-4 min-w-[768px] h-[80vh] min-h-[600px]">
    
    <!-- Template -->
    <template id="table-template">
      <div class="flex flex-col items-center justify-center p-2 bg-white rounded-md shadow hover:-translate-y-1 hover:shadow-lg hover:border-teal-500 border-2 border-transparent transition-all duration-200 cursor-pointer">
        <div class="font-semibold text-sm text-gray-800 table-name">TABLE</div>
        <div class="text-xs text-gray-500 table-orders">Orders: 0</div>
      </div>
    </template>

    <!-- Tables -->
    <div class="col-start-1 row-start-1 setSchedule" data-value="G6">G6</div>
    <div class="col-start-2 row-start-1 setSchedule" data-value="G5">G5</div>
    <div class="col-start-7 row-start-1 setSchedule" data-value="Take out 1">Take out 1</div>
    <div class="col-start-8 row-start-1 setSchedule" data-value="Take out 2">Take out 2</div>
    <div class="col-start-10 row-start-1 setSchedule" data-value="F3">F3</div>
    <div class="col-start-11 row-start-1 setSchedule" data-value="F4">F4</div>

    <div class="col-start-1 row-start-2 setSchedule" data-value="G4">G4</div>
    <div class="col-start-2 row-start-2 setSchedule" data-value="G3">G3</div>
    <div class="col-start-4 row-start-2 setSchedule" data-value="E4">E4</div>
    <div class="col-start-5 row-start-2 setSchedule" data-value="E8">E8</div>
    <div class="col-start-10 row-start-2 setSchedule" data-value="F1">F1</div>
    <div class="col-start-11 row-start-2 setSchedule" data-value="F2">F2</div>

    <div class="col-start-1 row-start-3 setSchedule" data-value="G2">G2</div>
    <div class="col-start-2 row-start-3 setSchedule" data-value="G1">G1</div>
    <div class="col-start-4 row-start-3 setSchedule" data-value="E3">E3</div>
    <div class="col-start-5 row-start-3 setSchedule" data-value="E7">E7</div>
    <div class="col-start-7 row-start-3 setSchedule" data-value="C6">C6</div>
    <div class="col-start-8 row-start-3 setSchedule" data-value="D6">D6</div>
    <div class="col-start-10 col-end-12 row-start-3 setSchedule" data-value="DJ">DJ</div>

    <div class="col-start-4 row-start-4 setSchedule" data-value="E2">E2</div>
    <div class="col-start-5 row-start-4 setSchedule" data-value="E6">E6</div>
    <div class="col-start-7 row-start-4 setSchedule" data-value="C5">C5</div>
    <div class="col-start-8 row-start-4 setSchedule" data-value="D5">D5</div>
    <div class="col-start-10 col-end-12 row-start-4 setSchedule" data-value="SOUNDECT">SOUNDECT</div>

    <div class="col-start-1 row-start-5 setSchedule" data-value="A5">A5</div>
    <div class="col-start-2 row-start-5 setSchedule" data-value="B6">B6</div>
    <div class="col-start-4 row-start-5 setSchedule" data-value="E1">E1</div>
    <div class="col-start-5 row-start-5 setSchedule" data-value="E5">E5</div>
    <div class="col-start-7 row-start-5 setSchedule" data-value="C4">C4</div>
    <div class="col-start-8 row-start-5 setSchedule" data-value="D4">D4</div>
    <div class="col-start-10 col-end-12 row-start-5 setSchedule" data-value="ACOUSTIC">ACOUSTIC</div>

    <div class="col-start-1 row-start-6 setSchedule" data-value="A4">A4</div>
    <div class="col-start-2 row-start-6 setSchedule" data-value="B5">B5</div>
    <div class="col-start-7 row-start-6 setSchedule" data-value="C3">C3</div>
    <div class="col-start-8 row-start-6 setSchedule" data-value="D3">D3</div>
    <div class="col-start-10 row-start-6 setSchedule" data-value="VIP 3">VIP 3</div>
    <div class="col-start-11 row-start-6 setSchedule" data-value="VIP 2">VIP 2</div>

    <div class="col-start-1 row-start-7 setSchedule" data-value="A3">A3</div>
    <div class="col-start-2 row-start-7 setSchedule" data-value="B4">B4</div>
    <div class="col-start-4 col-end-6 row-start-7 setSchedule" data-value="RESERV.">RESERV.</div>
    <div class="col-start-7 row-start-7 font-semibold setSchedule" data-value="C2">C2</div>
    <div class="col-start-8 row-start-7 setSchedule" data-value="D2">D2</div>
    <div class="col-start-10 col-end-12 row-start-7 setSchedule" data-value="BILLIARDS">BILLIARDS</div>

    <div class="col-start-1 row-start-8 font-semibold setSchedule" data-value="A2">A2</div>
    <div class="col-start-2 row-start-8 setSchedule" data-value="B3">B3</div>
    <div class="col-start-4 col-end-6 row-start-8 setSchedule" data-value="MEETING">MEETING</div>
    <div class="col-start-7 row-start-8 setSchedule" data-value="C1">C1</div>
    <div class="col-start-8 row-start-8 setSchedule" data-value="D1">D1</div>
    <div class="col-start-10 col-end-12 row-start-8 setSchedule" data-value="VIP 1">VIP 1</div>

    <div class="col-start-1 row-start-9 setSchedule" data-value="A1">A1</div>
    <div class="col-start-2 row-start-9 setSchedule" data-value="B2">B2</div>
    <div class="col-start-4 col-end-6 row-start-9 setSchedule" data-value="COMPLI">COMPLI</div>

    <div class="col-start-2 row-start-10 font-semibold setSchedule" data-value="B1">B1</div>

  </div>
</div>
<!--END TABLE LAYOUT -->






</div>


























<?php
include "../src/components/admin/footer.php";
?>


<!-- MODAL -->

<!-- Modal background -->
<div id="detailsModal" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm hidden">

  <!-- Modal content -->
  <div class="bg-[#1A1A1A]/90 backdrop-blur-md rounded-lg shadow-xl w-full max-w-2xl mx-4 p-8
              text-[#CCCCCC] relative max-h-[90vh] overflow-y-auto scrollbar-hidden
              border border-gray-700">

    <!-- Close button -->
    <button id="closeModal" 
      class="absolute top-4 cursor-pointer right-4 text-[#FFD700] hover:text-yellow-400 text-3xl font-bold transition
             focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded"
      aria-label="Close modal">&times;</button>

    <h2 class="text-3xl font-extrabold mb-6 text-[#FFD700] tracking-wide select-none">Reservation Details</h2>
    
    <hr class="border-gray-600 mb-6">

    <div id="modalContent" class="space-y-6 text-base leading-relaxed max-w-full">
      <!-- Dynamic content injected here -->
    </div>
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="../static/js/admin/dashboard.js"></script>
<script src="../static/js/admin/table_design.js"></script>