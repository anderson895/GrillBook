<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>
<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg">
    <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide">Group Deals</h2>
    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
        <?php echo strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
    </div>
</div>



<?php
include "../src/components/admin/footer.php";
?>


<script src="../static/js/admin/group_deals.js"></script>
