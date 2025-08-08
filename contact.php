<?php
include "src/components/header.php";
?>
<!-- Page Wrapper -->
<div class="min-h-screen flex items-center justify-center bg-black px-4 pt-24 pb-8">
    <div class="max-w-6xl w-full bg-gray-900 rounded-2xl shadow-lg p-8 grid md:grid-cols-2 gap-8 text-white">
        
        <!-- Left Side - Contact Info -->
        <div>
            <h2 class="text-3xl font-bold mb-4">Contact Us</h2>
            <p class="text-gray-300 mb-6">We’d love to hear from you! Reach out through any of the options below.</p>
            
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-lg">Email Address</h3>
                    <p class="text-gray-400">ultimateliempohausmarikina@gmail.com</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Phone Number</h3>
                    <p class="text-gray-400">09985486389</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Location</h3>
                    <p class="text-gray-400">Lot 2D, Mayor Gil Fernando Ave., Brgy. Sto. Niño, Marikina City 1800</p>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="mt-6">
                <h3 class="font-semibold text-lg mb-3">Follow Us</h3>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/ultimateliempomarikina" target="_blank" class="text-blue-500 hover:text-blue-400 text-2xl">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/ulh_marikina/?igsh=Y290MGZtam50NzVu#" target="_blank" class="text-pink-500 hover:text-pink-400 text-2xl">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@ulh.official?_t=ZS-8ybFExRFgMp&_r=1" target="_blank" class="text-white hover:text-gray-300 text-2xl">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Contact Form -->
        <div>
            <form action="https://api.web3forms.com/submit" method="POST" class="space-y-4">
                <input type="hidden" name="access_key" value="e5d8fbc9-63d8-4642-8a2e-5b107053acf0" />

                <!-- Honeypot field for spam protection -->
                <input type="text" name="botcheck" class="hidden" style="display:none">

                <div>
                    <label class="block text-sm font-medium text-gray-300">Name</label>
                    <input type="text" name="name" required class="mt-1 block w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300">Email</label>
                    <input type="email" name="email" required class="mt-1 block w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300">Subject</label>
                    <input type="text" name="subject" required placeholder="Example: Inquiry about services" class="mt-1 block w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300">Message</label>
                    <textarea name="message" rows="4" required placeholder="Write your detailed message here..." class="mt-1 block w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-semibold py-3 rounded-lg transition">
                    Send Message
                </button>
            </form>

        </div>
    </div>
</div>

<!-- Font Awesome for Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php
include "src/components/footer.php";
?>
