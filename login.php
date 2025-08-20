<?php
include "src/components/header.php";
?>

<!-- Page Wrapper -->
<div class="min-h-screen flex items-center justify-center bg-black px-4 py-8">

  <!-- Login Card -->
  <div class="w-full max-w-md bg-zinc-900 border border-yellow-400 rounded-2xl shadow-lg p-8 space-y-6">

    <!-- Logo and Title -->
    <div class="flex flex-col items-center space-y-3">
      <img src="static/logo.jpg" alt="GrillBook Logo" class="w-20 h-20 rounded-full border-2 border-yellow-400 shadow">
      <h1 class="text-3xl font-extrabold text-yellow-300 tracking-wide">GrillBook Login</h1>
    </div>

    <!-- Spinner Overlay -->
        <div id="spinner" class="absolute inset-0 flex items-center justify-center z-50  bg-white/70" style="display:none;">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
    <!-- Login Form -->
    <form id="frmLogin" method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-yellow-400 font-semibold mb-1">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <div>
        <label for="password" class="block text-yellow-400 font-semibold mb-1">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-2 rounded-lg bg-zinc-800 text-white border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300"/>
      </div>

      <button type="submit"
              class="w-full bg-yellow-400 text-black font-bold py-2 rounded-full hover:bg-yellow-300 transition">
        Login
      </button>
    </form>

    <!-- Footer / Links -->
    <div class="text-center text-sm text-yellow-400">
      Don't have an account?
      <a href="register" class="text-yellow-300 font-bold hover:underline">Register</a>
    </div>

  </div>

</div>

<?php
include "src/components/footer.php";
?>
<script src="static/js/login.js"></script>