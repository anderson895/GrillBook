<?php
include "src/components/header.php";
?>


<!-- Hero Section -->
<section class="bg-gray-900 pt-32 pb-20 text-center px-4">
  <h2 class="text-3xl sm:text-4xl font-extrabold text-yellow-300 mb-4">Book a Table at Your Favorite Restaurant</h2>
  <p class="text-base sm:text-lg text-yellow-200 max-w-2xl mx-auto mb-6">Reserve your seat and avoid the wait. Quick, easy, and convenient online table reservations.</p>
  <a href="#" class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition">Reserve Now</a>
</section>

<!-- Menu Section -->
<section class="py-16 bg-black px-4">
  <div class="max-w-6xl mx-auto">
    <h3 class="text-2xl sm:text-3xl font-bold text-center text-yellow-400 mb-10">Our Menu Highlights</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">

      <!-- Menu Card 1 -->
      <div class="bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition text-yellow-300">
        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=800&q=80" alt="Grilled Steak" class="rounded-lg mb-4 w-full h-48 object-cover">
        <h4 class="text-xl font-semibold">Grilled Steak</h4>
        <p class="mt-2">Juicy steak grilled to perfection, served with garlic mashed potatoes.</p>
      </div>

      <!-- Menu Card 2 -->
      <div class="bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition text-yellow-300">
        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=800&q=80" alt="Pasta Alfredo" class="rounded-lg mb-4 w-full h-48 object-cover">
        <h4 class="text-xl font-semibold">Pasta Alfredo</h4>
        <p class="mt-2">Creamy Alfredo pasta topped with grilled chicken and parmesan.</p>
      </div>

      <!-- Menu Card 3 -->
      <div class="bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition text-yellow-300">
        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=800&q=80" alt="Classic Cheesecake" class="rounded-lg mb-4 w-full h-48 object-cover">
        <h4 class="text-xl font-semibold">Classic Cheesecake</h4>
        <p class="mt-2">Smooth and rich cheesecake with a buttery graham crust.</p>
      </div>

    </div>
  </div>
</section>

<?php
include "src/components/footer.php";
?>