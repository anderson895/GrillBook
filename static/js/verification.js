$("#frmVerify").submit(function(e) {
    e.preventDefault();

    // Collect OTP from all boxes
    let code = '';
    $('.otp-box').each(function() {
        code += $(this).val().trim();
    });

    if (code.length < 6) {
        alertify.error('Please enter the full verification code.');
        return;
    }

    $('#btnVerify').prop('disabled', true).text('Verifying...');

    // Always call server to verify code securely
    $.ajax({
        type: "POST",
        url: "controller/end-points/controller.php",
        data: {
            verification_code: code,
            requestType: "RegisterCustomer"
        },
        dataType: 'json',
        success: function(response) {
            $('#btnVerify').prop('disabled', false).text('Verify');

            if (response.status === 'success') {
                alertify.success(response.message);
                setTimeout(function() {
                    window.location.href = 'login';
                }, 1500);
            } else {
                alertify.error(response.message);
                // Clear boxes on wrong code
                $('.otp-box').val('');
                $('.otp-box').first().focus();
            }
        },
        error: function() {
            $('#btnVerify').prop('disabled', false).text('Verify');
            alertify.error('An error occurred. Please try again.');
        }
    });
});
