<?php


include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";


?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide">Group Deals</h2>
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

  <!-- Add Button -->
  <button
    id="addBtn"
    class="w-full sm:w-auto bg-[#FFD700] text-black font-semibold 
           text-sm sm:text-base px-3 py-1.5 sm:px-4 sm:py-2 rounded-md 
           hover:bg-yellow-500 transition"
  >
    + Create Group
  </button>
</div>



  <!-- Table Container -->
  <div class="overflow-x-auto rounded-md">
    <table class="w-full text-sm text-left text-[#CCCCCC]">
      <thead class="bg-[#0D0D0D] text-[#FFD700] uppercase text-xs">
        <tr>

          <th class="p-3">#</th>
          <th class="p-3 ">Group Name</th>
          <th class="p-3 ">Description</th>
          <th class="p-3 ">Banner</th>
          
          <th class="p-3 text-center">Action</th>
        </tr>
      </thead>
      <tbody id="outputBody" class="divide-y divide-gray-700">
        <!-- Dynamic Data -->
      </tbody>
    </table>
  </div>
</div>



<!-- Spinner Overlay -->
    <div id="spinner" class="absolute inset-0 flex items-center justify-center z-50" style="display:none; background-color: rgba(255, 255, 255, 0.7);">
        <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

<!-- Modal -->
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" style="display:none;">
  <div class="bg-[#1A1A1A]/90 backdrop-blur-md rounded-lg shadow-xl w-full max-w-xl mx-auto p-6 text-[#CCCCCC] relative">
    <button id="closeAddModal" class="absolute top-2 right-2 text-white hover:text-red-500 text-xl">&times;</button>

    <div id="modalContent" class="space-y-2">
      <h3 class="text-lg font-bold text-[#FFD700]">Create Entry</h3>
      <form id="frmCreateEntry" class="space-y-4">
        <div>
          <label for="entryName" class="block text-sm font-medium">Name</label>
          <input type="text" id="entryName" name="entryName" class="w-full px-3 py-2 rounded-md bg-[#0D0D0D] border border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#FFD700]">
        </div>
        <div>
          <label for="entryDescription" class="block text-sm font-medium">Description</label>
          <textarea id="entryDescription" name="entryDescription" class="w-full px-3 py-2 rounded-md bg-[#0D0D0D] border border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#FFD700]"></textarea>
        </div>

        <!-- Upload Image -->
        <div>
          <label for="entryImage" class="block text-sm font-medium text-[#FFD700]">Upload Image</label>
          <input type="file" id="entryImage" name="entryImage" accept="image/*" class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#FFD700] file:text-black hover:file:bg-yellow-400">
        </div>

        <div class="text-right">
          <button type="submit" class="bg-[#FFD700] text-black font-semibold px-4 py-2 rounded-md hover:bg-yellow-500">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>







<?php include "../src/components/admin/footer.php"; ?>
<script src="../static/js/admin/group_deals.js"></script>
