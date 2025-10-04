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









<div id="rescheduleModal" class="fixed inset-0 hidden items-center justify-center z-50 bg-black/40">
  <div class="bg-[#1A1A1A] rounded-xl shadow-lg w-11/12 max-w-sm p-6 mx-4 text-[#CCCCCC] relative">
    <h2 class="text-lg font-semibold mb-4 text-center text-[#FFD700]">Request New Schedule</h2>

    <!-- Spinner Overlay -->
    <div id="spinner" class="absolute inset-0 flex items-center justify-center z-50 bg-[#1A1A1A]/70" style="display:none;">
      <div class="w-12 h-12 border-4 border-[#FFD700] border-t-transparent rounded-full animate-spin"></div>
    </div>

    <form id="rescheduleForm">
      <input type="hidden" id="rescheduleReservationId" name="reservationId">

      <!-- Seats -->
      <div class="mb-4">
        <label for="seats" class="block mb-1 font-semibold">How many seats</label>
        <input 
          type="number" 
          id="seats" 
          name="seats" 
          min="1" 
          max="6" 
          placeholder="Enter number of seats" 
          required 
          class="w-full rounded-lg bg-[#2A2A2A] text-[#CCCCCC] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#FFD700]"
        />
        <p id="seats-warning" class="text-red-500 text-sm mt-1">Maximum number of seats is 6.</p>
      </div>

      <!-- Reason -->
      <div class="mb-4">
        <label class="block mb-1 font-semibold" for="rescheduleReason">Reason</label>
        <select id="rescheduleReason" name="reason" 
          class="w-full bg-[#2A2A2A] text-[#CCCCCC] border border-[#333] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
          <option value="" disabled selected>Select reason</option>
          <option value="personal">Personal reason</option>
          <option value="sick">Sick</option>
          <option value="work">Work-related</option>
          <option value="other">Other</option>
        </select>
      </div>

     


      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">



       <!-- Date -->
      <div class="mb-4">
        <label class="block mb-1 font-semibold" for="rescheduleDate">New Date</label>
        <input type="date" id="rescheduleDate" name="newDate"
          class="w-full bg-[#2A2A2A] text-[#CCCCCC] border border-[#333] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
      </div>
   


       <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
          flatpickr("#rescheduleDate", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            minDate: "today", // disable past dates
            onOpen: function(selectedDates, dateStr, instance) {
              // Enlarge the calendar popup
              instance.calendarContainer.style.transform = "scale(1.5)";
              instance.calendarContainer.style.transformOrigin = "top left";
            }
          });
        </script>



      <!-- Time -->
      <div class="mb-6">
        <label class="block mb-1 font-semibold" for="rescheduleTime">New Time</label>
        <input type="time" id="rescheduleTime" name="newTime"
          class="w-full bg-[#2A2A2A] text-[#CCCCCC] border border-[#333] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-3">
        <button type="button" id="closeRescheduleModal" class="px-4 py-2 bg-[#333] hover:bg-[#444] rounded-md text-sm text-[#CCCCCC]">Cancel</button>
        <button type="submit" id="submitReschedule" class="px-4 py-2 bg-[#FFD700] hover:bg-[#e6c200] rounded-md text-sm text-[#1A1A1A]">Submit</button>
      </div>
    </form>
  </div>
</div>











<!-- Modal for Enlarged QR -->
<div id="payment_img_modal" 
     class="fixed inset-0 flex items-center justify-center z-[9999] bg-black/30 backdrop-blur-sm" 
     style="display:none;">
  <div class="relative max-w-3xl">
    <img id="modal_img" src="" alt="Enlarged QR" class="max-h-screen max-w-full rounded-lg shadow-lg" />
    <!-- Close button -->
    <button id="close_modal" 
            class="cursor-pointer absolute top-2 right-2 text-white bg-gray-800 rounded-full p-2 hover:bg-gray-600">
      ✕
    </button>
  </div>
</div>








<?php
include "../src/components/customer/footer.php";
?>


<script src="../static/js/customer/reservation.js"></script>
