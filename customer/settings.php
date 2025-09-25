<?php
include "../src/components/customer/header.php";
include "../src/components/customer/nav.php";
?>

<!-- Page Container -->
<div class="flex flex-col items-center justify-start min-h-screen bg-gray-100 pt-24">
  <!-- START OF MAIN content wrapper -->
  <div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8">
    




  
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




   <!-- END OF MAIN -->
  </div>
</div>

  


<?php
include "../src/components/customer/footer.php";
?>


<script src="../static/js/customer/settings.js"></script>
