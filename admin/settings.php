<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>
<style>
  /* Hide scrollbar but keep scroll */
.scrollbar-hidden::-webkit-scrollbar {
  display: none;
}
.scrollbar-hidden {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;     /* Firefox */
}

</style>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide">Account Settings</h2>
  <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($_SESSION['user_fname'], 0, 1)); ?>
  </div>
</div>







<!-- Account Settings Section (Not Modal) -->
<section id="accountSettings" class="max-w-xl mx-auto mt-8 p-6 bg-black rounded-lg  text-[#CCCCCC]">
  
  <h3 class="text-lg font-bold text-[#FFD700] mb-6">ACCOUNT SETTINGS</h3>

  <form id="frmUpdateAccount" method="POST" class="space-y-4">

    <input type="text" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" hidden>

    <!-- First Name -->
    <div>
      <label for="first_name" class="block text-sm font-medium text-[#FFD700]">First Name</label>
      <input type="text" value="<?=$On_Session[0]['user_fname']?>" id="first_name" name="first_name" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700]" placeholder="Enter your first name" required>
    </div>

    <!-- Last Name -->
    <div>
      <label for="last_name" class="block text-sm font-medium text-[#FFD700]">Last Name</label>
      <input type="text" value="<?=$On_Session[0]['user_lname']?>" id="last_name" name="last_name" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700]" placeholder="Enter your last name" required>
    </div>

    <!-- Email -->
    <div>
      <label for="email" class="block text-sm font-medium text-[#FFD700]">Email</label>
      <input type="email" value="<?=$On_Session[0]['user_email']?>" id="email" name="email" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700]" placeholder="Enter your email" required>
    </div>

    <!-- Password -->
    <div>
      <label for="password" class="block text-sm font-medium text-[#FFD700]">Password</label>
      <input type="password" id="password" name="password" class="mt-1 w-full px-3 py-2 bg-[#0D0D0D] border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700]" placeholder="Enter a new password">
      <p class="text-xs text-gray-500 mt-1">Leave blank if you don’t want to change your password.</p>
    </div>

    <!-- Save Button -->
    <div class="text-right">
      <button type="submit" class="bg-[#FFD700] text-black font-semibold px-4 py-2 rounded hover:bg-yellow-400 transition">Save Changes</button>
    </div>

  </form>
</section>





<?php
include "../src/components/admin/footer.php";
?>


<script src="../static/js/admin/settings.js"></script>
