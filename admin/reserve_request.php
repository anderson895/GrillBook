<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>


<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide">Reservation Request</h2>
  <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
  </div>
</div>




<!-- Table Card -->
<div class="bg-[#1A1A1A] rounded-lg shadow-lg p-6 text-[#CCCCCC]">

  <!-- Search Input -->
<div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
  <!-- Search Input -->
  <input
    type="text"
    id="searchInput"
    class="w-full sm:max-w-xs px-4 py-2 rounded-md bg-[#0D0D0D] border border-gray-600 
           text-[#CCCCCC] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700]"
    placeholder="Search..."
  />


</div>


<!-- Table Container -->
<div class="overflow-x-auto rounded-md">
  <table class="w-full text-sm text-left text-[#CCCCCC]">
    <thead class="bg-[#0D0D0D] text-[#FFD700] uppercase text-xs">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3 text-center">Date</th>
        <th class="p-3 text-center">Table Code</th>
        <th class="p-3 text-center">Schedule</th>
        <th class="p-3 text-center">Time</th>
        <th class="p-3 text-center">Total</th>
        <th class="p-3 text-center">Action</th>
      </tr>
    </thead>
    <tbody id="outputTableBody" class="divide-y divide-gray-700">
      <!-- Dynamic Data -->
    </tbody>
  </table>
</div>

<!-- Pagination -->
<div id="pagination" class="mt-4 flex justify-center gap-2"></div>



<!-- Spinner Overlay -->
<div id="spinner" class="absolute inset-0 flex items-center justify-center z-50" style="display:none; background-color: rgba(255, 255, 255, 0.7);">
    <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
</div>







<?php
include "../src/components/admin/footer.php";
?>


<script src="../static/js/admin/reserve_request.js"></script>
