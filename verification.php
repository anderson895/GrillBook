<?php
session_start(); // Make sure session is started
include "src/components/header.php";

// Redirect if no registration data
if (!isset($_SESSION['register_data'])) {
    header('Location: register.php');
    exit;
}

$user = $_SESSION['register_data'];
?>

<div class="min-h-screen flex items-center justify-center bg-black px-4">
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg w-full max-w-md text-center">
        <h2 class="text-3xl font-bold text-yellow-400 mb-6">Verify Your Account</h2>

        <form id="frmVerify" class="space-y-6">
            <div class="flex justify-between gap-2">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" maxlength="1" class="otp-box w-12 h-12 text-center text-yellow-400 bg-black border-2 border-yellow-400 rounded-lg text-2xl focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                <?php endfor; ?>
            </div>

            <button 
                type="submit" 
                id="btnVerify" 
                class="cursor-pointer w-full bg-yellow-400 hover:bg-yellow-300 text-black font-bold py-3 rounded-lg transition-colors duration-200"
            >
                Verify
            </button>
        </form>
    </div>
</div>



<?php
include "src/components/footer.php";
?>




    <script>
        $(document).ready(function() {
            const $otpInputs = $('.otp-box');

            // Move focus automatically
            $otpInputs.on('input', function() {
                const nextInput = $(this).next('.otp-box');
                if (this.value.length === 1 && nextInput.length) {
                    nextInput.focus();
                }
            });

            // Handle backspace
            $otpInputs.on('keydown', function(e) {
                if (e.key === "Backspace" && !this.value) {
                    $(this).prev('.otp-box').focus();
                }
            });

            
        });
    </script>


<script src="static/js/verification.js"></script>