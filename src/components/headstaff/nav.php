
<!-- Layout Wrapper -->
<div class="min-h-screen flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-[#0D0D0D] shadow-lg w-64 lg:w-1/5 xl:w-1/6 p-6 space-y-6 lg:static fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- Sidebar Header -->
    <div class="flex flex-wrap justify-center items-center space-x-4 p-4 bg-[#1A1A1A] rounded-lg shadow-inner hover:shadow-xl transition-shadow duration-300 max-w-full">
      <img src="../static/logo.jpg" alt="Mega Tech" class="w-20 h-20 rounded-full border-2 border-gray-700 shadow-sm transform transition-transform duration-300 hover:scale-105">
      <h1 class="text-base sm:text-lg md:text-xl font-bold text-[#FFD700] tracking-tight text-center">
        Head Staff
      </h1>
    </div>

    <!-- Navigation -->
    <nav class="space-y-4 text-left text-[#CCCCCC]">
      
      <a href="dashboard" class="nav-link flex items-center space-x-3 hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">dashboard</span>
        <span>Dashboard</span>
      </a>

      <!--  Dropdown Button -->
      <button id="toggleDeals" class="w-full flex items-center justify-between text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <div class="flex items-center space-x-3">
          <span class="material-icons">sell</span>
          <span>Deals</span>
        </div>
        <span id="deals_dropdownIcon" class="material-icons transition-transform duration-300">expand_more</span>
      </button>

      <!-- Dropdown Menu -->
      <div id="dealsDropdown" class="ml-8 space-y-2" style="display: none;">
        <a href="group_deals" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Group Deals</a>
        <a href="promo_deals" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Promo Deals</a>
      </div>


    
      <!--  Dropdown Button -->
      <button id="toggleReservation" class="w-full flex items-center justify-between text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <div class="flex items-center space-x-3">
          <span class="material-icons">event</span>
          <span>Reservation</span>
        </div>
        <span id="reserve_dropdownIcon" class="material-icons transition-transform duration-300">expand_more</span>
      </button>

      <!-- Dropdown Menu -->
      <div id="reserveDropdown" class="ml-8 space-y-2" style="display: none;">
        <a href="reserve_request" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Request</a>
        <a href="all_reserved" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">All</a>
      </div>


      <a href="menu" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">local_dining</span>
        <span>Menu</span>
      </a>




      <a href="settings" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">settings</span>
        <span>Settings</span>
      </a>

      <a href="logout">
        <button type="submit" class="cursor-pointer flex items-center space-x-3 text-[#CCCCCC] hover:text-red-500 hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
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
  $("#toggleDeals").click(function () {
    $("#dealsDropdown").slideToggle(300);
    const icon = $("#deals_dropdownIcon");
    icon.text(icon.text() === "expand_more" ? "expand_less" : "expand_more");
  });



   $("#toggleReservation").click(function () {
    $("#reserveDropdown").slideToggle(300);
    const icon = $("#reserve_dropdownIcon");
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
    if (currentPath === linkHref || currentPath.endsWith(linkHref)) {
      link.classList.add('text-[#FFD700]', 'bg-white/10');

      

      if (link.closest('#dealsDropdown')) {
        document.getElementById('dealsDropdown').style.display = 'block';
        document.getElementById('deals_dropdownIcon').textContent = 'expand_less';
      }

      if (link.closest('#reserveDropdown')) {
        document.getElementById('reserveDropdown').style.display = 'block';
        document.getElementById('reserve_dropdownIcon').textContent = 'expand_less';
      }

      console.log(linkHref);
   
    }
  });
</script>