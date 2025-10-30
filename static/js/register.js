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
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = $(this).serializeArray();
        formData.push({ name: 'requestType', value: 'RegisterCustomer' });

        $.ajax({
            type: "POST",
            url: "controller/end-points/verification_mailer.php",
            data: $.param(formData),
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
                        window.location.href = "verification"; // redirect
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
