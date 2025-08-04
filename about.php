<?php
include "src/components/header.php";
?>

<!-- Page Wrapper -->
<div class="min-h-screen bg-black px-4 pt-24 pb-12">
  <div class="max-w-6xl mx-auto text-white">
    <!-- Heading -->
  
    <h2 class="text-3xl font-semibold text-center mb-10 ">About Us</h2>

    <!-- Mission & Vision Section -->
    <div class="grid md:grid-cols-2 gap-10 mb-20 items-center">
      <!-- Left Image -->
      <div class="w-full">
        <img src="static/image/4.jpg" alt="Dining Experience" class="w-full h-72 md:h-80 object-cover rounded-2xl shadow-xl">
      </div>

      <!-- Right Text -->
      <div>
        <div class="mb-10">
          <h2 class="text-2xl font-semibold mb-3">Mission</h2>
          <p class="text-gray-300 leading-relaxed">
            We aim to deliver the ultimate version of food that we offer and provide an utmost service dining experience to our customers.
          </p>
        </div>
        <div>
          <h2 class="text-2xl font-semibold mb-3">Vision</h2>
          <p class="text-gray-300 leading-relaxed">
            We intend to be present in every city in Metro Manila, including in certain neighboring areas by 2027.
          </p>
        </div>
      </div>
    </div>

    <!-- Our Place Section -->
    <div>
      <h2 class="text-3xl font-semibold text-center mb-10">Explore Our Place</h2>
      <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">
        <!-- Place Card -->
        <div class="bg-gray-900 p-5 rounded-2xl text-center shadow-lg hover:scale-105 transition duration-300">
          <img src="static/image/3.jpg" alt="Dining Area" class="rounded-xl mb-4 w-full h-48 object-cover">
          <h3 class="text-xl font-semibold text-white">Cozy Indoor Dining</h3>
          <p class="text-gray-400 text-sm mt-1">Perfect ambiance for families and friends.</p>
        </div>

        <!-- Place Card -->
        <div class="bg-gray-900 p-5 rounded-2xl text-center shadow-lg hover:scale-105 transition duration-300">
          <img src="static/image/2.jpg" alt="Outdoor Area" class="rounded-xl mb-4 w-full h-48 object-cover">
          <h3 class="text-xl font-semibold text-white">Al Fresco Vibes</h3>
          <p class="text-gray-400 text-sm mt-1">Breezy outdoor seating for casual meals.</p>
        </div>

        <!-- Place Card -->
        <div class="bg-gray-900 p-5 rounded-2xl text-center shadow-lg hover:scale-105 transition duration-300">
          <img src="static/image/1.jpg" alt="Bar Section" class="rounded-xl mb-4 w-full h-48 object-cover">
          <h3 class="text-xl font-semibold text-white">Chic Bar Corner</h3>
          <p class="text-gray-400 text-sm mt-1">Enjoy refreshments in style.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include "src/components/footer.php";
?>
