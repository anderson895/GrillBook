<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>

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

  <select
    name="filterStatus"
    id="filterStatus"
    class="block w-full md:w-60 px-4 py-2 border border-gray-700 rounded-lg bg-gray-800 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition hover:border-gray-500"
  >
    <option value="all">All</option>
    <option value="pending">Pending</option>
    <option value="request cancel">Request Cancel</option>
  </select>




</div>


<!-- Table Container -->
<div class="max-h-[500px] overflow-y-auto overflow-x-hidden rounded-md border border-gray-700">
  <table class="w-full text-sm text-left text-[#CCCCCC]">
    <thead class="bg-[#0D0D0D] text-[#FFD700] uppercase text-xs sticky top-0 z-10">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3 text-center">Date</th>
        <th class="p-3 text-center">Customer Name</th>
        <th class="p-3 text-center">Reservation Code</th>
        <th class="p-3 text-center">Table</th>
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
<div id="pagination" class="mt-4 flex justify-center gap-2"></div>




<div id="spinnerOverlay" 
     class="fixed inset-0 flex items-center justify-center z-[9999] hidden"
     style="background-color: rgba(0, 0, 0, 0.3);">
  <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
  </svg>
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
      class="cursor-pointer absolute top-4 right-4 text-[#FFD700] hover:text-yellow-400 text-3xl font-bold transition
             focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded"
      aria-label="Close modal">&times;</button>

    <h2 class="text-3xl font-extrabold mb-6 text-[#FFD700] tracking-wide select-none">Reservation Details</h2>
    
    <hr class="border-gray-600 mb-6">

    <div id="modalContent" class="space-y-6 text-base leading-relaxed max-w-full">
      <!-- Dynamic content injected here -->
    </div>

    <hr class="border-gray-600 mt-6 mb-8">

   
      <input type="hidden" id="reservation_id" name="reservation_id">

      <!-- Action buttons -->
      <div class="flex justify-end space-x-4">
        <button type="submit" id="btnApprove" 
          class="px-8 cursor-pointer py-3 bg-[#FFD700] text-black rounded-md font-semibold shadow-lg
                 hover:bg-yellow-400 transition duration-200 ease-in-out
                 focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:ring-offset-2"
                 data-action="confirmed"
                 >
          Confirmed
        </button>
        <button type="button" id="btnCancel" 
          class="px-8 cursor-pointer py-3 bg-red-700 text-[#F3F3F3] rounded-md font-semibold shadow-lg
                 hover:bg-red-600 transition duration-200 ease-in-out
                 focus:outline-none focus:ring-4 focus:ring-red-600 focus:ring-offset-2"
                 data-action="cancelled"
                 >
          Cancelled
        </button>
      </div>

  </div>
</div>













     <!-- Modal for Enlarged QR -->
<div id="payment_img_modal" class="fixed inset-0 flex items-center justify-center z-50 bg-transparent" style="display:none;">
  <div class="relative max-w-3xl">
    <img id="modal_img" src="" alt="Enlarged QR" class="max-h-screen max-w-full rounded-lg shadow-lg" />
    <!-- Close button -->
    <button id="close_modal" class="cursor-pointer absolute top-2 right-2 text-white bg-gray-800 rounded-full p-2 hover:bg-gray-600">
      ✕
    </button>
  </div>
</div>








<?php
include "../src/components/admin/footer.php";
?>


<script src="../static/js/admin/reserve_request.js"></script>
