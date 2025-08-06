
<!-- Layout Wrapper -->
<div class="min-h-screen flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-[#0D0D0D] shadow-lg w-64 lg:w-1/5 xl:w-1/6 p-6 space-y-6 lg:static fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- Sidebar Header -->
    <div class="flex flex-wrap justify-center items-center space-x-4 p-4 bg-[#1A1A1A] rounded-lg shadow-inner hover:shadow-xl transition-shadow duration-300 max-w-full">
      <img src="../static/logo.jpg" alt="Mega Tech" class="w-20 h-20 rounded-full border-2 border-gray-700 shadow-sm transform transition-transform duration-300 hover:scale-105">
      <h1 class="text-base sm:text-lg md:text-xl font-bold text-[#FFD700] tracking-tight text-center">
        Administrator
      </h1>
    </div>

    <!-- Navigation -->
    <nav class="space-y-4 text-left text-[#CCCCCC]">
      
      <a href="dashboard" class="nav-link flex items-center space-x-3 hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">dashboard</span>
        <span>Dashboard</span>
      </a>

      <!-- Bullies Dropdown Button -->
      <button id="toggleAssets" class="w-full flex items-center justify-between text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <div class="flex items-center space-x-3">
          <span class="material-icons">pets</span>
          <span>Bullies</span>
        </div>
        <span id="dropdownIcon" class="material-icons transition-transform duration-300">expand_more</span>
      </button>

      <!-- Dropdown Menu -->
      <div id="assetsDropdown" class="ml-8 space-y-2" style="display: none;">
        <a href="registration" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Registration</a>
        <a href="registered" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Registered</a>
      </div>

      <a href="gettable" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">storefront</span>
        <span>Gettable</span>
      </a>

      <a href="events" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">event</span>
        <span>Events</span>
      </a>


    <a href="pointchart" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
      <span class="material-icons">bar_chart</span>
      <span>Points Chart</span>
    </a>



      <a href="settings" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">settings</span>
        <span>Settings</span>
      </a>

      <a href="logout">
        <button type="submit" class="flex items-center space-x-3 text-[#CCCCCC] hover:text-red-500 hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
          <span class="material-icons">logout</span>
          <span>Logout</span>
        </button>
      </a>
    </nav>
  </aside>

  <!-- Overlay for Mobile Sidebar -->
  <div id="overlay" class="fixed inset-0 bg-black opacity-50 hidden lg:hidden z-40"></div>

  <!-- Main Content -->
  <main class="flex-1 bg-[#1A1A1A] p-8 lg:p-12">
    <!-- Mobile menu button -->
    <button id="menuButton" class="lg:hidden text-[#FFD700] bg-white/10 hover:bg-white/20 p-2 rounded-md transition duration-300 mb-4">
      <span class="material-icons">menu</span> 
    </button>

  

   


   
<!-- JavaScript -->
<script>
  // Dropdown toggle logic
  $("#toggleAssets").click(function () {
    $("#assetsDropdown").slideToggle(300);
    const icon = $("#dropdownIcon");
    icon.text(icon.text() === "expand_more" ? "expand_less" : "expand_more");
  });

  // Mobile menu logic
  const menuButton = document.getElementById('menuButton');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  menuButton.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  });

  // Active URL highlighting
  const links = document.querySelectorAll('.nav-link');
  const currentPath = window.location.pathname;

  links.forEach(link => {
    const linkHref = link.getAttribute('href');
    if (currentPath.includes(linkHref)) {
      link.classList.add('text-[#FFD700]', 'bg-white/10');

      // Auto-expand Bullies dropdown if a child link is active
      if (link.closest('#assetsDropdown')) {
        document.getElementById('assetsDropdown').style.display = 'block';
        document.getElementById('dropdownIcon').textContent = 'expand_less';
      }
    }
  });
</script>