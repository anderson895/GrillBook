<?php
include "../src/components/customer/header.php";
include "../src/components/customer/nav.php";
?>

<!-- Page Container -->
<div class="flex flex-col items-center justify-start min-h-screen bg-gray-100 pt-24">
  <!-- Main content wrapper -->
  <div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8">
    
    <!-- Card -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
      
      <!-- Scrollable Grid Only -->
      <div class="overflow-auto">
        <div class="grid grid-cols-12 grid-rows-[repeat(10,minmax(0,1fr))] gap-2 sm:gap-4 p-2 sm:p-4 min-w-[768px] h-[80vh] min-h-[600px]">
          
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

    </div>

  </div>
</div>

<?php
include "../src/components/customer/footer.php";
?>



<!-- Modal -->
<div 
  id="scheduleModal" 
  role="dialog" 
  aria-modal="true" 
  aria-labelledby="modalTitle" 
  tabindex="-1"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden"
>
  <div 
    class="bg-[#1A1A1A]/95 backdrop-blur-lg rounded-xl shadow-2xl w-full max-w-4xl mx-auto p-8 text-[#E0E0E0] relative max-h-[95vh] overflow-y-auto"
    role="document"
  >
    <!-- Sticky Header -->
    <div class=" top-0 bg-[#1A1A1A]/95 backdrop-blur-lg z-10 flex justify-between items-center pb-3 border-b border-gray-700 mb-6">
      <h2 id="modalTitle" class="text-2xl font-bold text-yellow-400 whitespace-nowrap">
        Reservation for Table:<span id="table_code_label" class="ml-1 text-white"></span>
      </h2>

      <button 
        id="closeAddModal" 
        aria-label="Close modal" 
        class="text-white hover:text-red-500 text-3xl font-bold transition"
      >&times;</button>
    </div>

    <form id="frmRequestReservation" class="space-y-6" enctype="multipart/form-data" autocomplete="off" novalidate>
      <input type="hidden" id="table_code" name="table_code" />

      <!-- Seats -->
      <div>
        <label for="seats" class="block mb-1 font-semibold">How many seats</label>
        <input 
          type="number" 
          id="seats" 
          name="seats" 
          min="1" 
          placeholder="Enter number of seats" 
          required 
          class="w-full rounded-lg bg-[#2A2A2A] text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400" 
        />
      </div>

      <!-- Date -->
      <div>
        <label for="date_schedule" class="block mb-1 font-semibold">Date Schedule</label>
        <input 
          type="date" 
          id="date_schedule" 
          name="date_schedule" 
          required 
          class="w-full rounded-lg bg-[#2A2A2A] text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400" 
        />
      </div>

      <!-- Time -->
      <div>
        <label for="time_schedule" class="block mb-1 font-semibold">Time</label>
        <input 
          type="time" 
          id="time_schedule" 
          name="time_schedule" 
          required 
          class="w-full rounded-lg bg-[#2A2A2A] text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400" 
        />
      </div>

      <!-- Check Availability -->
      <div class="text-center">
        <button 
          type="button" 
          id="btnCheckAvailability" 
          class="bg-yellow-400 text-black font-semibold px-8 py-3 rounded-lg hover:bg-yellow-500 transition"
        >
          Check Availability
        </button>
      </div>

<!-- Menu Section -->
<section class="py-16 px-6 bg-[#1A1A1A]">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-3xl text-center font-bold text-[#FFD700] uppercase mb-10">Menu List</h2>
    <div class="swiper menuSwiper">
      <div class="swiper-wrapper" id="menuContainer">
        <!-- Slides injected by JS -->
      </div>
    </div>
  </div>
</section>

<!-- Promo Deals Section -->
<section class="py-16 px-6 bg-[#1A1A1A]" id="promo_section">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-3xl text-center font-bold text-[#FFD700] uppercase mb-10">Promo Deals</h2>
    <div class="swiper promoSwiper">
      <div class="swiper-wrapper" id="promoContainer">
        <!-- Slides injected by JS -->
      </div>
    </div>
  </div>
</section>

<!-- Group Deals Section -->
<section class="py-16 px-6 bg-[#1A1A1A]">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-3xl text-center font-bold text-[#FFD700] uppercase mb-10">Group Deals</h2>
    <div class="swiper groupSwiper">
      <div class="swiper-wrapper" id="groupContainer">
        <!-- Slides injected by JS -->
      </div>
    </div>
  </div>
</section>



      <!-- Terms -->
      <div class="flex items-start space-x-3">
        <input 
          type="checkbox" 
          id="terms" 
          name="terms" 
          required 
          class="mt-1 rounded text-yellow-400 focus:ring-yellow-400 w-5 h-5" 
        />
        <label for="terms" class="text-sm select-none">
          I agree to the <a href="../static/resources/terms_and_condition.pdf" target="_blank" download class="underline text-yellow-400 hover:text-yellow-300">Terms and Conditions</a>
        </label>
      </div>

      <!-- File Upload -->
      <div>
        <label for="payment_proof" class="block mb-1 font-semibold">Upload Proof of Payment</label>
        <input
          type="file"
          id="payment_proof"
          name="payment_proof"
          accept="image/*,.pdf"
          disabled
          required
          class="w-full rounded-lg bg-[#2A2A2A] text-white px-4 py-3 cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-yellow-400"
        />
        <p id="fileNamePreview" class="mt-1 text-yellow-400 text-sm"></p>
      </div>

      <!-- Submit -->
      <div class="text-center">
        <button
          type="submit"
          id="submitBtn"
          disabled
          class="bg-yellow-400 text-black font-semibold px-8 py-3 rounded-lg cursor-not-allowed opacity-50 hover:bg-yellow-500 transition"
        >
          Submit Reservation
        </button>
      </div>
    </form>
  </div>
</div>




<script src="../static/js/customer/home.js"></script>
<script src="../static/js/customer/table_design.js"></script>
