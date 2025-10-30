<?php
session_start();
include "src/components/header.php";

// Redirect if no registration data
if (!isset($_SESSION['register_data'])) {
    header('Location: register.php');
    exit;
}

$user = $_SESSION['register_data'];

// Calculate remaining time in seconds
$timeLeft = max(0, 300 - (time() - $user['code_generated_time'])); // 5 minutes = 300s
?>

<div class="min-h-screen flex flex-col items-center justify-center bg-black px-4">

    <p class="mb-4 text-yellow-400 text-lg">
        Verification code expires in: <span id="timer"></span>
    </p>

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
    const $btnVerify = $('#btnVerify');
    const $timer = $('#timer');

    // Remaining time passed from PHP
    let timeLeft = <?php echo $timeLeft; ?>; // seconds

    if (timeLeft <= 0) {
        // Already expired
        $timer.text('00:00');
        alertify.error('Verification code expired.');
        $otpInputs.prop('disabled', true);
        $btnVerify.prop('disabled', true);
        return;
    }

    // Countdown timer
    const countdown = setInterval(function() {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        $timer.text(`${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`);

        if (timeLeft <= 0) {
            clearInterval(countdown);
            alertify.error('Verification code expired.');
            $otpInputs.prop('disabled', true);
            $btnVerify.prop('disabled', true);
        }

        timeLeft--;
    }, 1000);

    // Auto-focus next input
    $otpInputs.on('input', function() {
        const nextInput = $(this).next('.otp-box');
        if (this.value.length === 1 && nextInput.length) nextInput.focus();
    });

    // Handle backspace
    $otpInputs.on('keydown', function(e) {
        if (e.key === "Backspace" && !this.value) {
            $(this).prev('.otp-box').focus();
        }
    });

    // Form submission
    $("#frmVerify").submit(function(e) {
        e.preventDefault();

        let code = '';
        $otpInputs.each(function() { code += $(this).val().trim(); });

        if (code.length < 6) {
            alertify.error('Please enter the full verification code.');
            return;
        }

        $btnVerify.prop('disabled', true).text('Verifying...');

        $.ajax({
            type: "POST",
            url: "controller/end-points/controller.php",
            data: { verification_code: code, requestType: "RegisterCustomer" },
            dataType: 'json',
            success: function(response) {
                $btnVerify.prop('disabled', false).text('Verify');

                if (response.status === 'success') {
                    alertify.success(response.message);
                    setTimeout(() => { window.location.href = 'login'; }, 1500);
                } else {
                    alertify.error(response.message);
                    $otpInputs.val('');
                    $otpInputs.first().focus();
                }
            },
            error: function() {
                $btnVerify.prop('disabled', false).text('Verify');
                alertify.error('An error occurred. Please try again.');
            }
        });
    });
});
</script>
