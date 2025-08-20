<?php
include "../src/components/headstaff/header.php";
include "../src/components/headstaff/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg">
    <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide">Menu List</h2>
    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
        <?php echo strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
    </div>
</div>


<!-- Table Card -->
<div class="bg-[#1A1A1A] rounded-lg shadow-lg p-6  text-[#CCCCCC]">

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
          <th class="p-3 text-center">Name</th>
          <th class="p-3 text-center">Category</th>
          <th class="p-3 text-center">Description</th>
          <th class="p-3 text-center">Price</th>
          <th class="p-3 text-center">Image</th>
          
          <th class="p-3 text-center">Action</th>
        </tr>
      </thead>
      <tbody id="menuTableBody" class="divide-y divide-gray-700">
        <!-- Dynamic Data -->
      </tbody>
    </table>
  </div>
</div>




<!-- Spinner Overlay -->
<div id="spinner" class="absolute inset-0 flex items-center justify-center z-50" style="display:none; background-color: rgba(255, 255, 255, 0.7);">
    <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
</div>
























<div id="menuDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" style="display:none;">
  <div class="bg-[#1A1A1A]/90 backdrop-blur-md rounded-lg shadow-xl w-full max-w-xl mx-auto p-6 text-[#CCCCCC] relative">
    <button id="closeMenuDetailsModal" class="absolute top-2 right-2 text-white hover:text-red-500 text-xl">&times;</button>

    <div id="modalContent" class="space-y-4">
      <h3 class="text-lg font-bold text-[#FFD700]">DETAILS</h3>
      <form class="space-y-4">


        <input type="hidden" id="menu_id" name="menu_id">
        <!-- Menu Name -->
        <div>
          <label for="menu_name_update" class="block text-sm font-medium text-[#FFD700]">Menu Name</label>
          <input type="text" id="menu_name_update" name="menu_name" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
        </div>
        <!-- Menu Category -->
         <div>
          <label for="menu_category_update" class="block text-sm font-medium text-[#FFD700]">Category</label>
          <select id="menu_category_update" name="menuCategory" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
            <option value="">-- Select category --</option>
            <option value="dessert">Dessert</option>
            <option value="appetizer">Appetizer</option>
            <option value="soup">Soup</option>
            <option value="salad">Salad</option>
            <option value="main course">Main Course</option>
            <option value="side dish">Side Dish</option>
            <option value="beverages">Beverages</option>
          </select>
        </div>

        <!-- Description -->
        <div>
          <label for="menu_description_update" class="block text-sm font-medium text-[#FFD700]">Description</label>
          <textarea id="menu_description_update" name="menu_description" rows="3" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-[#FFD700]"></textarea>
        </div>

        <div>
          <label for="menu_image_update" class="block text-sm font-medium text-[#FFD700]">Upload Banner</label>
          <input type="file" id="menu_image_update" name="menu_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#FFD700] file:text-black hover:file:bg-yellow-400">
        </div>

        <!-- Description -->
        <div>
          <label for="menu_price_update" class="block text-sm font-medium text-[#FFD700]">Menu Price</label>
          <input type="text" id="menu_price_update" name="menu_price" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
        </div>
       

      </form>
    </div>
  </div>
</div>













<?php
include "../src/components/headstaff/footer.php";
?>


<script src="../static/js/headstaff/menu.js"></script>
