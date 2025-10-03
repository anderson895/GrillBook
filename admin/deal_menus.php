<?php


include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";

$deal_name=$_GET['deal_name'];
$deal_id=$_GET['deal_id'];
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-lg shadow-lg">
  <!-- Title -->
  <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wider">
    List of Menu on <?= htmlspecialchars($deal_name) ?>
  </h2>

  <!-- Admin Initial Circle -->
  <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-semibold shadow-md">
    <?= strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
  </div>
</div>




<!-- Table Card -->
<div class="bg-[#0D0D0D] rounded-lg shadow-lg p-6 text-[#CCCCCC]">

  <!-- Search Input -->
 <div class="mb-4 flex justify-between items-center">

  <!-- Add Button -->
  <button
    id="addBtn"
    class="mr-4 cursor-pointer bg-[#FFD700] text-black font-semibold px-4 py-2 rounded-md hover:bg-yellow-500 transition"
  >
    + Add Menu
  </button>
</div>
</div>


<!-- Container where the result will be shown -->
<div 
  id="outputBody" 
  class="mt-6 p-4 sm:p-6 rounded-lg text-[#CCCCCC] bg-[#0D0D0D] w-full overflow-x-auto"
  data-deal_id="<?=$deal_id?>"
>
</div>



<!-- Modal -->
<div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" style="display:none;">
  <div class="bg-[#1A1A1A]/90 backdrop-blur-md rounded-lg shadow-xl w-full max-w-xl mx-auto p-6 text-[#CCCCCC] relative">
    <button id="closeAddModal" class="cursor-pointer absolute top-2 right-2 text-white hover:text-red-500 text-xl">&times;</button>

    <div id="modalContent" class="space-y-2">
      <form id="frmAddMenuDeals" class="space-y-4">
        
        <!-- Contestant fields wrapper -->
        <div>
                <input type="hidden" id="deal_id" name="deal_id" value="<?=$deal_id?>">
        
            <div class="contestant-group flex items-center gap-2">
                <select id="menuSelect" name="menu" class="select-menu w-full bg-[#0D0D0D] border border-gray-600 text-sm rounded-md focus:ring-2 focus:ring-[#FFD700] focus:outline-none">
                    <option value="">Select Menu</option>
                </select>
            </div>
        </div>

        <div class="text-center">
          <button type="submit" class="bg-[#FFD700] cursor-pointer text-black font-semibold px-4 py-2 rounded-md hover:bg-yellow-500">Add Menu</button>
        </div>
      </form>
    </div>
  </div>
</div>




<!-- Custom Styles for Select2 to match Tailwind input -->

<link rel="stylesheet" href="../src/selectto.css">




<!-- Update Contestant Modal -->
<div id="updateContestantModal" class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm flex items-center justify-center p-4" style="display:none;">
  <form 
    id="frmUpdateContestants" 
    class="bg-[#1A1A1A] p-4 sm:p-6 rounded-lg shadow-lg w-full max-w-2xl max-h-screen overflow-y-auto relative"
  >
    <input type="hidden" id="update_pc_id" name="pc_id">

    <h2 class="text-lg sm:text-xl font-bold text-[#FFD700] mb-4">Update Contestants</h2>
    
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2 mb-4">
      <button 
        type="button" 
        id="addUpdateContestantField" 
        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-3 py-1 rounded-md w-full sm:w-auto"
      >
        + Add Another Contestant
      </button>
      <button 
        type="button" 
        id="selectAllUpdateContestants" 
        class="bg-yellow-500 hover:bg-yellow-600 text-black text-sm font-semibold px-3 py-1 rounded-md w-full sm:w-auto"
      >
        Select All Contestants
      </button>
    </div>

    <div id="contestant_list" class="space-y-2"></div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4">
      <button 
        type="button" 
        id="closeUpdateContestantModal" 
        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded w-full sm:w-auto"
      >
        Cancel
      </button>
      <button 
        type="submit" 
        class="bg-green-500 hover:bg-green-600 text-black font-semibold px-4 py-2 rounded w-full sm:w-auto"
      >
        Update
      </button>
    </div>
  </form>
</div>





<?php include "../src/components/admin/footer.php"; ?>










<script src="../static/js/admin/deal_menus.js"></script>