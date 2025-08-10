<?php
include "../src/components/customer/header.php";
include "../src/components/customer/nav.php";
?>

<!-- Page Container -->
<div class="flex flex-col items-center justify-start min-h-screen bg-gray-100 pt-24">
  <!-- START OF MAIN content wrapper -->
  <div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8">
    






<style>
  /* Hide scrollbar but keep scroll */
.scrollbar-hidden::-webkit-scrollbar {
  display: none;
}
.scrollbar-hidden {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;     /* Firefox */
}

</style>

<div class="max-w-7xl mx-auto p-6">
  <div class="bg-[#1A1A1A] rounded-lg shadow-lg p-6">
    <!-- Search Input -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <input
        type="text"
        id="searchInput"
        class="w-full sm:max-w-xs px-4 py-2 rounded-md bg-[#0D0D0D] border border-gray-600
               text-[#CCCCCC] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700]"
        placeholder="Search..."
      />
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-md border border-gray-700">
      <table class="w-full text-sm text-left text-[#CCCCCC]">
        <thead class="bg-[#0D0D0D] text-[#FFD700] uppercase text-xs">
          <tr>
            <th class="p-3">#</th>
            <th class="p-3 text-center">Date</th>
            <th class="p-3 text-center">Table Code</th>
            <th class="p-3 text-center">Schedule</th>
            <th class="p-3 text-center">Time</th>
            <th class="p-3 text-center">Total</th>
            <th class="p-3 text-center">Status</th>
            <th class="p-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody id="outputTableBody" class="divide-y divide-gray-700">
          <!-- Dynamic Data -->
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-6 flex justify-center gap-3"></div>
  </div>
</div>

<!-- Spinner Overlay -->
<div
  id="spinner"
  class="absolute inset-0 flex items-center justify-center z-50"
  style="display:none; background-color: rgba(255, 255, 255, 0.7);"
>
  <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
</div>










   <!-- END OF MAIN -->
  </div>
</div>

  













<!-- Modal background -->
<div id="detailsModal" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm hidden">

  <!-- Modal content -->
  <div class="bg-[#1A1A1A]/90 backdrop-blur-md rounded-lg shadow-xl w-full max-w-2xl mx-4 p-8
              text-[#CCCCCC] relative max-h-[90vh] overflow-y-auto scrollbar-hidden
              border border-gray-700">

    <!-- Close button -->
    <button id="closeModal" 
      class="absolute top-4 right-4 text-[#FFD700] hover:text-yellow-400 text-3xl font-bold transition
             focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded"
      aria-label="Close modal">&times;</button>

    <h2 class="text-3xl font-extrabold mb-6 text-[#FFD700] tracking-wide select-none">Reservation Details</h2>
    
    <hr class="border-gray-600 mb-6">

    <div id="modalContent" class="space-y-6 text-base leading-relaxed max-w-full">
      <!-- Dynamic content injected here -->
    </div>
  </div>
</div>




<?php
include "../src/components/customer/footer.php";
?>


<script src="../static/js/customer/reservation.js"></script>
