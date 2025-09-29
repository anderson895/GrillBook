<?php
include "src/components/header.php";
?>


<!-- Hero Section -->
<section class="bg-gray-900 pt-32 pb-20 text-center px-4">
  <h2 class="text-3xl sm:text-4xl font-extrabold text-yellow-300 mb-4">Book a Table at Your Favorite Restaurant</h2>
  <p class="text-base sm:text-lg text-yellow-200 max-w-2xl mx-auto mb-6">Reserve your seat and avoid the wait. Quick, easy, and convenient online table reservations.</p>
  <a href="login" class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition">Reserve Now</a>
</section>


<!-- Menu Section -->
<section class="py-16 bg-black px-4">
  <div class="max-w-6xl mx-auto">
    <h3 class="text-2xl sm:text-3xl font-bold text-center text-yellow-400 mb-10">Our Menu Highlights</h3>

    <div id="menuContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
      <!-- Cards will be inserted here by jQuery -->
    </div>

  </div>
</section>


<?php
include "src/components/footer.php";
?>

<script src="static/js/index.js"></script>