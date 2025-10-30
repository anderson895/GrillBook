$(document).ready(function () {

    $("#frmRegister").submit(function (e) {
        e.preventDefault();

        const password = $("#password").val();
        const confirmPassword = $("#confirm_password").val();

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords do not match',
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        // Show Swal loader
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we register your account.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        // Serialize form data
        const formData = $(this).serializeArray();
        formData.push({ name: 'requestType', value: 'RegisterCustomer' }); // ensure requestType is sent

        $.ajax({
            type: "POST",
            url: "controller/end-points/verification_mailer.php",
            data: $.param(formData), // send properly encoded
            dataType: 'json',
            success: function (response) {
                Swal.close(); // close the loader

                if (response.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        text: 'Verification code sent!',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        window.location.href = "verification"; // redirect to verification page
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message,
                        confirmButtonColor: '#3085d6'
                    });
                }
            },
            error: function () {
                Swal.close(); // close the loader
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });

});
