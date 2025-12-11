$(document).ready(function () {

  $("#frmLogin").submit(function (e) {
    e.preventDefault();

    $('#spinner').show();
    $('#btnLogin').prop('disabled', true);

    var formData = $(this).serializeArray();
    formData.push({ name: 'requestType', value: 'Login' });
    var serializedData = $.param(formData);

    $.ajax({
      type: "POST",
      url: "controller/end-points/controller.php",
      data: serializedData,
      dataType: 'json',
     success: function (response) {
    console.log(response.status);

    if (response.status === "success") {

        const position = response.user_position;
        const routes = {
            admin: "admin/dashboard",
            headstaff: "headstaff/dashboard",
            customer: "customer/home"
        };

        // ✅ Show Swal with colored text
          Swal.fire({
            title: 'Login Successful',
            icon: 'success',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed && routes[position]) {
              window.location.href = routes[position];
            }
          });

        } else {
          $('#spinner').hide();
          $('#btnLogin').prop('disabled', false);
          Swal.fire({
            title: 'Login Failed',
            text: response.message,
            icon: 'error',
            confirmButtonText: 'Try Again'
          });
        }
      },
      error: function () {
        $('#spinner').hide();
        $('#btnLogin').prop('disabled', false);
        Swal.fire({
          title: 'Error',
          text: 'An error occurred. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    });
  });
});
