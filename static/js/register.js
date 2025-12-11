$(document).ready(function () {

    // ✅ Validate specific field dynamically
    function validateField(fieldId) {
        const value = $(fieldId).val().trim();
        const nameRegex = /^[A-Za-z\s]+$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

        let isValid = true;

        switch (fieldId) {
            case "#first_name":
                if (value === "") {
                    $("#first_name_msg").text("This field is required").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else if (!nameRegex.test(value)) {
                    $("#first_name_msg").text("First name should contain letters only").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else {
                    $("#first_name_msg").addClass("hidden");
                    $(fieldId).addClass("border-yellow-400").removeClass("border-red-500");
                }
                break;

            case "#last_name":
                if (value === "") {
                    $("#last_name_msg").text("This field is required").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else if (!nameRegex.test(value)) {
                    $("#last_name_msg").text("Last name should contain letters only").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else {
                    $("#last_name_msg").addClass("hidden");
                    $(fieldId).addClass("border-yellow-400").removeClass("border-red-500");
                }
                break;

            case "#email":
                if (value === "") {
                    $("#email_msg").text("This field is required").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else if (!emailRegex.test(value)) {
                    $("#email_msg").text("Please enter a valid email address").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else {
                    $("#email_msg").addClass("hidden");
                    $(fieldId).addClass("border-yellow-400").removeClass("border-red-500");
                }
                break;

            case "#password":
                const password = $(fieldId).val().trim();
                if (password === "") {
                    $("#password_msg").text("This field is required").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else if (!passwordRegex.test(password)) {
                    $("#password_msg")
                        .text("Password must include letters, numbers, and special characters, minimum 8 characters")
                        .removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else {
                    $("#password_msg").addClass("hidden");
                    $(fieldId).addClass("border-yellow-400").removeClass("border-red-500");
                }
                break;

            case "#confirm_password":
                const confirmPassword = $(fieldId).val().trim();
                const mainPassword = $("#password").val().trim();

                if (confirmPassword === "") {
                    $("#confirm_password_msg").text("This field is required").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else if (confirmPassword !== mainPassword) {
                    $("#confirm_password_msg").text("Passwords do not match").removeClass("hidden");
                    $(fieldId).addClass("border-red-500").removeClass("border-yellow-400");
                    isValid = false;
                } else {
                    $("#confirm_password_msg").addClass("hidden");
                    $(fieldId).addClass("border-yellow-400").removeClass("border-red-500");
                }
                break;
        }

        return isValid;
    }

    // 🔁 Validate as user types (real-time)
    $("#first_name, #last_name, #email, #password, #confirm_password").on("input", function () {
        validateField("#" + $(this).attr("id"));
    });

    // 👀 Validate on blur (user skips field)
    $("#first_name, #last_name, #email, #password, #confirm_password").on("blur", function () {
        validateField("#" + $(this).attr("id"));
    });

    // 🧠 Hide message when focusing invalid input
    $("#first_name, #last_name, #email, #password, #confirm_password").on("focus", function () {
        const fieldId = "#" + $(this).attr("id");
        $(fieldId + "_msg").addClass("hidden");
    });

    // 🚀 Submit form (original logic intact)
    $("#frmRegister").submit(function (e) {
        e.preventDefault();

        let allValid = true;
        const fields = ["#first_name", "#last_name", "#email", "#password", "#confirm_password"];
        fields.forEach(field => {
            const valid = validateField(field);
            if (!valid) allValid = false;
        });

        if (!allValid) return;

        // ✅ Original SweetAlert loader
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we register your account.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        // ✅ Original AJAX logic (unchanged)
        const formData = $(this).serializeArray();
        formData.push({ name: 'requestType', value: 'RegisterCustomer' });

        $.ajax({
            type: "POST",
            url: "controller/end-points/verification_mailer.php",
            data: $.param(formData),
            dataType: 'json',
            success: function (response) {
                Swal.close();

                if (response.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verification Code Sent',
                        text: 'Your 6-digit verification code has been sent to your email. Please check your inbox to continue.',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        window.location.href = "verification";
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
                Swal.close();
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

