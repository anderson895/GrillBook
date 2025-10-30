const sessionVerificationCode = sessionStorage.getItem('verificationCode');

$("#frmVerify").submit(function(e) {
    e.preventDefault();

    const code = $('input[name="verification_code"]').val().trim();

    if (!code) {
        alertify.error('Please enter the verification code.');
        return;
    }

    // Check against session-stored code first
    if (code !== sessionVerificationCode) {
        alertify.error('Invalid verification code.');
        return;
    }

    $('#btnVerify').prop('disabled', true).text('Verifying...');

    // Only call controller.php if the code matches
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
            }
        },
        error: function() {
            $('#btnVerify').prop('disabled', false).text('Verify');
            alertify.error('An error occurred. Please try again.');
        }
    });
});
