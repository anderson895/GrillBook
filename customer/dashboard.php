<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Map</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="grid grid-cols-12 gap-2 bg-white p-4 rounded-lg shadow-lg">

      <!-- Column blocks -->
      <!-- Left side tables -->
      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">G6</div>
        <div class="table-box">G4</div>
        <div class="table-box relative">G2 <span class="dot bg-orange-500"></span></div>
        <div class="table-box relative">A4 <span class="dot bg-green-500"></span></div>
        <div class="table-box">A2</div>
        <div class="table-box">A1</div>
      </div>

      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">G5</div>
        <div class="table-box">G3</div>
        <div class="table-box">G1</div>
        <div class="table-box">A5</div>
        <div class="table-box">A3</div>
        <div class="table-box">A2</div>
        <div class="table-box">A1</div>
      </div>

      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">E4</div>
        <div class="table-box">E3</div>
        <div class="table-box">E2</div>
        <div class="table-box">E1</div>
        <div class="table-box font-bold">RESERV</div>
        <div class="table-box font-bold">MEETING</div>
        <div class="table-box font-bold">COMPLI</div>
      </div>

      <!-- C Tables -->
      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">E8</div>
        <div class="table-box">E7</div>
        <div class="table-box">E6</div>
        <div class="table-box">E5</div>
      </div>

      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box font-bold">TAKE OUT 1</div>
        <div class="table-box font-bold">TAKE OUT 2</div>
        <div class="table-box relative">C6 <span class="dot bg-orange-500"></span></div>
        <div class="table-box relative border-2 border-red-500">C5 <span class="dot bg-orange-500"></span></div>
        <div class="table-box">C4</div>
        <div class="table-box">C3</div>
        <div class="table-box">C2</div>
        <div class="table-box">C1</div>
      </div>

      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">D6</div>
        <div class="table-box">D5</div>
        <div class="table-box">D4</div>
        <div class="table-box relative">D3 <span class="dot bg-green-500"></span></div>
        <div class="table-box">D2</div>
        <div class="table-box relative">D1 <span class="dot bg-orange-500"></span></div>
      </div>

      <!-- Right side labels -->
      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">F2</div>
        <div class="table-box">F1</div>
        <div class="table-box font-bold">DJ</div>
        <div class="table-box font-bold relative">SOUNDECT <span class="dot bg-orange-500"></span></div>
        <div class="table-box font-bold">VIP2</div>
        <div class="table-box font-bold">BILLIARD</div>
      </div>

      <div class="flex flex-col space-y-2 col-span-1">
        <div class="table-box">F4</div>
        <div class="table-box">F3</div>
        <div class="table-box font-bold">ACOUSTIC</div>
        <div class="table-box font-bold">VIP3</div>
        <div class="table-box font-bold">VIP1</div>
      </div>

    </div>
  </div>

  <style>
    .table-box {
      @apply w-20 h-16 bg-white rounded flex items-center justify-center text-sm shadow relative;
    }
    .dot {
      @apply w-3 h-3 rounded-full absolute;
    }
  </style>
</body>
</html>