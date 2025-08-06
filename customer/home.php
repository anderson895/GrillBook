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
        <div class="col-start-1 row-start-1">G6</div>
        <div class="col-start-2 row-start-1">G5</div>
        <div class="col-start-7 row-start-1">Take out 1</div>
        <div class="col-start-8 row-start-1">Take out 2</div>
        <div class="col-start-10 row-start-1">F3</div>
        <div class="col-start-11 row-start-1">F4</div>

        <div class="col-start-1 row-start-2">G4</div>
        <div class="col-start-2 row-start-2">G3</div>
        <div class="col-start-4 row-start-2">E4</div>
        <div class="col-start-5 row-start-2">E8</div>
        <div class="col-start-10 row-start-2">F1</div>
        <div class="col-start-11 row-start-2">F2</div>

        <div class="col-start-1 row-start-3">G2</div>
        <div class="col-start-2 row-start-3">G1</div>
        <div class="col-start-4 row-start-3">E3</div>
        <div class="col-start-5 row-start-3">E7</div>
        <div class="col-start-7 row-start-3">C6</div>
        <div class="col-start-8 row-start-3">D6</div>
        <div class="col-start-10 col-end-12 row-start-3">DJ</div>

        <div class="col-start-4 row-start-4">E2</div>
        <div class="col-start-5 row-start-4">E6</div>
        <div class="col-start-7 row-start-4">C5</div>
        <div class="col-start-8 row-start-4">D5</div>
        <div class="col-start-10 col-end-12 row-start-4">SOUNDECT</div>

        <div class="col-start-1 row-start-5">A5</div>
        <div class="col-start-2 row-start-5">B6</div>
        <div class="col-start-4 row-start-5">E1</div>
        <div class="col-start-5 row-start-5">E5</div>
        <div class="col-start-7 row-start-5">C4</div>
        <div class="col-start-8 row-start-5">D4</div>
        <div class="col-start-10 col-end-12 row-start-5">ACOUSTIC</div>

        <div class="col-start-1 row-start-6">A4</div>
        <div class="col-start-2 row-start-6">B5</div>
        <div class="col-start-7 row-start-6">C3</div>
        <div class="col-start-8 row-start-6">D3</div>
        <div class="col-start-10 row-start-6">VIP 3</div>
        <div class="col-start-11 row-start-6">VIP 2</div>

        <div class="col-start-1 row-start-7">A3</div>
        <div class="col-start-2 row-start-7">B4</div>
        <div class="col-start-4 col-end-6 row-start-7">RESERV.</div>
        <div class="col-start-7 row-start-7 border-amber-500 text-amber-900 font-semibold">C2</div>
        <div class="col-start-8 row-start-7">D2</div>
        <div class="col-start-10 col-end-12 row-start-7">BILLIARDS</div>

        <div class="col-start-1 row-start-8 border-amber-500 text-amber-900 font-semibold">A2</div>
        <div class="col-start-2 row-start-8">B3</div>
        <div class="col-start-4 col-end-6 row-start-8">MEETING</div>
        <div class="col-start-7 row-start-8">C1</div>
        <div class="col-start-8 row-start-8">D1</div>
        <div class="col-start-10 col-end-12 row-start-8">VIP 1</div>

        <div class="col-start-1 row-start-9">A1</div>
        <div class="col-start-2 row-start-9">B2</div>
        <div class="col-start-4 col-end-6 row-start-9">COMPLI</div>

        <div class="col-start-2 row-start-10 border-amber-500 text-amber-900 font-semibold">B1</div>
       </div>
      </div>

    </div>

  </div>
</div>

<?php
include "../src/components/customer/footer.php";
?>


<script src="../static/js/customer/home.js"></script>
<script src="../static/js/customer/table_design.js"></script>
