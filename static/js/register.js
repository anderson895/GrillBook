$(document).ready(function () {

  $("#frmRegister").submit(function (e) {
    e.preventDefault();

    // Get password values
    var password = $("#password").val();
    var confirmPassword = $("#confirm_password").val();

    // Confirm password validation
    if (password !== confirmPassword) {
      alertify.error("Passwords do not match.");
      return; // Stop form submission
    }

    $('#spinner').show();
    $('#btnLogin').prop('disabled', true);

    var formData = $(this).serializeArray();
    formData.push({ name: 'requestType', value: 'RegisterCustomer' });
    var serializedData = $.param(formData);

    $.ajax({
      type: "POST",
      url: "controller/end-points/controller.php",
      data: serializedData,
      dataType: 'json',
      success: function (response) {
        console.log(response.status);

        if (response.status === "success") {
          alertify.success('Registration Successful');
          setTimeout(function () {
            window.location.href = "login";
          }, 1000);
        } else {
          $('#spinner').hide();
          $('#btnLogin').prop('disabled', false);
          console.log(response);
          alertify.error(response.message);
        }
      },
      error: function () {
        $('#spinner').hide();
        $('#btnLogin').prop('disabled', false);
        alertify.error('An error occurred. Please try again.');
      }
    });
  });

});
