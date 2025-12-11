<?php
include "../src/components/customer/header.php";
include "../src/components/customer/nav.php";
?>

<!-- Page Container -->
<div class="grill-background">
    <div class="grill-pattern"></div>
    <div class="grill-overlay"></div>
</div>

<div class="flex flex-col items-center justify-start min-h-screen pt-24">
    <!-- START OF MAIN content wrapper -->
    <div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Account Settings Section -->
        <section id="accountSettings" class="max-w-xl mx-auto mt-8 p-8 bg-[#1A1A1A] rounded-2xl border-2 border-[#FFD700]/30 text-[#E5E5E5] shadow-2xl shadow-[#FFD700]/10">
            <div class="flex items-center mb-6 space-x-3">
                <span class="material-icons text-[#FFD700] text-2xl">manage_accounts</span>
                <h3 class="text-xl font-bold text-[#FFD700]">ACCOUNT SETTINGS</h3>
            </div>

            <form id="frmUpdateAccount" method="POST" class="space-y-6">
                <input type="text" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" hidden>

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-[#FFD700] mb-2">First Name</label>
                    <input type="text" value="<?= $On_Session[0]['user_fname'] ?>" id="first_name" name="first_name" 
                           class="w-full px-4 py-3 bg-[#0D0D0D] border-2 border-[#FFD700]/30 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700] focus:border-transparent transition-all duration-300 hover:border-[#FFD700]/50" 
                           placeholder="Enter your first name" required>
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-semibold text-[#FFD700] mb-2">Last Name</label>
                    <input type="text" value="<?= $On_Session[0]['user_lname'] ?>" id="last_name" name="last_name" 
                           class="w-full px-4 py-3 bg-[#0D0D0D] border-2 border-[#FFD700]/30 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700] focus:border-transparent transition-all duration-300 hover:border-[#FFD700]/50" 
                           placeholder="Enter your last name" required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#FFD700] mb-2">Email</label>
                    <input type="email" value="<?= $On_Session[0]['user_email'] ?>" id="email" name="email" 
                           class="w-full px-4 py-3 bg-[#0D0D0D] border-2 border-[#FFD700]/30 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700] focus:border-transparent transition-all duration-300 hover:border-[#FFD700]/50" 
                           placeholder="Enter your email" required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-[#FFD700] mb-2">Password</label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-4 py-3 bg-[#0D0D0D] border-2 border-[#FFD700]/30 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFD700] focus:border-transparent transition-all duration-300 hover:border-[#FFD700]/50" 
                           placeholder="Enter a new password">
                    <p class="text-xs text-gray-400 mt-2 italic">Leave blank if you don't want to change your password.</p>
                </div>

                <!-- Save Button -->
                <div class="text-right pt-4">
                    <button type="submit" 
                            class="bg-gradient-to-r from-[#FFD700] to-[#B8860B] text-white font-bold px-6 py-3 rounded-xl hover:from-yellow-400 hover:to-[#D4AF37] transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl hover:shadow-[#FFD700]/30 flex items-center space-x-2">
                        <span class="material-icons text-lg">save</span>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </section>
        <!-- END OF MAIN -->
    </div>
</div>

<style>
:root {
    --primary-gold: #FFD700;
    --dark-gold: #B8860B;
    --light-gold: #F5E8C8;
    --dark-bg: #0A0A0A;
    --card-bg: #1A1A1A;
    --text-light: #E5E5E5;
    --text-muted: #A3A3A3;
    --grill-dark: #2A2A2A;
    --grill-light: #3A3A3A;
}

.grill-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
}

.grill-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(90deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px),
        linear-gradient(0deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px);
    background-size: 50px 50px;
    opacity: 0.1;
}

.grill-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(184, 134, 11, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(212, 175, 55, 0.08) 0%, transparent 50%);
}

/* Animation for form inputs */
@keyframes glow {
    0% {
        box-shadow: 0 0 5px var(--primary-gold);
    }
    50% {
        box-shadow: 0 0 20px var(--primary-gold);
    }
    100% {
        box-shadow: 0 0 5px var(--primary-gold);
    }
}

input:focus {
    animation: glow 2s infinite;
}

/* Smooth transitions */
* {
    transition: all 0.3s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--dark-bg);
}

::-webkit-scrollbar-thumb {
    background: var(--primary-gold);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--dark-gold);
}
</style>

<?php
include "../src/components/customer/footer.php";
?>

<script src="../static/js/customer/settings.js"></script>