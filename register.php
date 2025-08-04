<?php
include "src/components/header.php";
?>

<!-- Page Wrapper -->
<div class="min-h-screen flex items-center justify-center bg-black px-4 pt-24 pb-8">

  <!-- Register Card -->
  <div class="w-full max-w-md bg-zinc-900 border border-yellow-400 rounded-2xl shadow-lg p-8 space-y-6">

    <!-- Logo and Title -->
    <div class="flex flex-col items-center space-y-3">
      <img src="static/logo.jpg" alt="GrillBook Logo" class="w-20 h-20 rounded-full border-2 border-yellow-400 shadow">
      <h1 class="text-3xl font-extrabold text-yellow-300 tracking-wide">GrillBook Register</h1>
    </div>

    <!-- Register Form -->
    <form action="#" method="POST" class="space-y-5">

      <!-- First Name -->
      <div>
        <label for="first_name" class="block text-yellow-400 font-semibold mb-1">First Name</label>
        <input type="text" id="first_name" name="first_name" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <!-- Last Name -->
      <div>
        <label for="last_name" class="block text-yellow-400 font-semibold mb-1">Last Name</label>
        <input type="text" id="last_name" name="last_name" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-yellow-400 font-semibold mb-1">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-yellow-400 font-semibold mb-1">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="confirm_password" class="block text-yellow-400 font-semibold mb-1">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <!-- Submit Button -->
      <button type="submit"
              class="w-full bg-yellow-400 text-black font-bold py-2 rounded-full hover:bg-yellow-300 transition">
        Register
      </button>
    </form>

    <!-- Footer / Links -->
    <div class="text-center text-sm text-yellow-400">
      Already have an account?
      <a href="login.php" class="text-yellow-300 font-bold hover:underline">Login</a>
    </div>

  </div>

</div>

<?php
include "src/components/footer.php";
?>
