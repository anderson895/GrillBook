<!-- Navbar -->
<nav class="bg-black shadow-lg fixed w-full z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">

    <!-- Logo and Title -->
    <div class="flex items-center space-x-3">
      <img src="../static/logo.jpg" alt="GrillBook Logo" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-yellow-400">
      <a href="home.php"><h1 class="text-xl sm:text-2xl font-extrabold text-yellow-400 tracking-wide">GrillBook</h1></a>
    </div>

    <!-- Centered Navigation Links -->
    <div class="hidden md:flex flex-1 justify-center space-x-6 items-center">
      <a href="home.php" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Home</a>
      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_position'] === 'customer'): ?>
        <a href="reservation.php" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Reservation</a>
      <?php else: ?>
        <!-- Show registration link instead for non-logged-in users -->
        <a href="registration.php" class="text-yellow-400 hover:text-white transition duration-300 font-medium" onclick="return showReservationMessage(event)">Reservation</a>
      <?php endif; ?>
    </div>

    <!-- Login/Register (Right Side) -->
    <div class="hidden md:flex space-x-6 items-center">
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="settings.php" class="bg-yellow-400 hover:bg-yellow-300 text-black px-5 py-2 rounded-full font-semibold shadow-md transition duration-300">
    <?php 
    if (isset($_SESSION['user_fname']) && !empty($_SESSION['user_fname'])) {
        echo htmlspecialchars($_SESSION['user_fname']);
    } elseif (isset($_SESSION['email'])) {
        // Show first part of email if no name is set
        $emailParts = explode('@', $_SESSION['email']);
        echo htmlspecialchars($emailParts[0]);
    } else {
        echo 'Profile';
    }
    ?>
</a>
        <a href="logout.php" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Logout</a>
      <?php else: ?>
        <a href="login.php" class="text-yellow-400 hover:text-white transition duration-300 font-medium">Login</a>
        <a href="registration.php" class="bg-yellow-400 hover:bg-yellow-300 text-black px-5 py-2 rounded-full font-semibold shadow-md transition duration-300">Register</a>
      <?php endif; ?>
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

  <!-- Navigation Buttons -->
  <a href="home.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Home</a>
  
  <?php if (isset($_SESSION['user_id']) && $_SESSION['user_position'] === 'customer'): ?>
    <a href="reservation.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Reservation</a>
  <?php else: ?>
    <a href="registration.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition" onclick="return showReservationMessage(event)">Reservation</a>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['user_id'])): ?>
    <a href="settings.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition"><?=htmlspecialchars($_SESSION['user_fname'])?></a>
    <a href="logout.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Logout</a>
  <?php else: ?>
    <a href="login.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Login</a>
    <a href="registration.php" class="w-full max-w-xs bg-yellow-400 text-black text-lg py-2 rounded-full font-bold text-center hover:bg-yellow-300 transition">Register</a>
  <?php endif; ?>
</div>

<script>
  // Mobile menu toggle
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-menu');
  const closeBtn = document.getElementById('close-menu');

  toggle.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });

  closeBtn.addEventListener('click', () => {
    menu.classList.add('hidden');
  });

  // Show reservation message for non-logged-in users
  function showReservationMessage(event) {
    event.preventDefault();
    
    // Check if SweetAlert2 is loaded
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Reservation Access',
        html: 'To make a reservation, you need to <strong>register as a customer</strong>.<br><br>' +
              'Our reservation system is available to registered customers only.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Register Now',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d4af37',
        cancelButtonColor: '#6b7280',
        background: '#1a1a1a',
        color: '#e5e5e5'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'registration.php';
        }
      });
    } else {
      // Fallback to AlertifyJS if SweetAlert2 is not available
      alertify.confirm(
        'Reservation Access',
        'To make a reservation, you need to register as a customer. Our reservation system is available to registered customers only.',
        function() {
          window.location.href = 'registration.php';
        },
        function() {
          // Do nothing on cancel
        }
      );
    }
    
    return false;
  }
</script>