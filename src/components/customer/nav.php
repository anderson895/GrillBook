<!-- Navbar -->
<nav class="bg-black shadow-lg fixed w-full z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">

    <!-- Logo and Title -->
    <div class="flex items-center space-x-3">
      <img src="../static/logo.jpg" alt="GrillBook Logo" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-yellow-400">
      <a href="admin/../"><h1 class="text-xl sm:text-2xl font-extrabold text-yellow-400 tracking-wide">GrillBook</h1></a>
    </div>

    <!-- Centered Navigation Links -->
    <div class="hidden md:flex flex-1 justify-center space-x-6 items-center">
      <a href="home" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Home</a>
      <a href="reservation" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Reservation</a>
    </div>

    <!-- Login/Register (Right Side) -->
    <div class="hidden md:flex space-x-6 items-center">
      <a href="settings" class="bg-yellow-400 hover:bg-yellow-300 text-black px-5 py-2 rounded-full font-semibold shadow-md transition duration-300"><?=$_SESSION['user_fname'];?></a>
      <a href="logout" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Logout</a>
    
    </div>

    <!-- Mobile Hamburger Menu Button -->
    <div class="md:hidden">
      <button id="menu-toggle" class="text-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300 rounded">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>
</nav>

<!-- Fullscreen Mobile Menu -->
<div id="mobile-menu" class="md:hidden fixed inset-0 bg-black text-yellow-400 hidden z-40 flex flex-col items-center justify-center space-y-6 px-4 transition duration-300 ease-in-out">

  <!-- Close Button -->
  <button id="close-menu" class="absolute top-5 right-5 text-yellow-400 hover:text-yellow-200 text-3xl focus:outline-none">
    &times;
  </button>

  <!-- Logo and Title -->
  <div class="flex flex-col items-center space-y-2 mb-4">
    <img src="../static/logo.jpg" alt="GrillBook Logo" class="w-20 h-20 rounded-full border-2 border-yellow-400 shadow">
    <h2 class="text-2xl font-extrabold tracking-wide text-yellow-300">GrillBook</h2>
  </div>

  <!-- Navigation Buttons (Centered by default on mobile) -->
  <a href="home" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Home</a>
  <a href="reservation" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Reservation</a>
  <a href="settings" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition"><?=$_SESSION['user_fname'];?></a>
  <a href="logout" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Logout</a>
</div>

<!-- JS toggle -->
<script>
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-menu');
  const closeBtn = document.getElementById('close-menu');

  toggle.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });

  closeBtn.addEventListener('click', () => {
    menu.classList.add('hidden');
  });
</script>





