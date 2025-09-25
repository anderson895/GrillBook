$('#frmUpdateAccount').on('submit', function(e) {
    e.preventDefault(); 
    var formData = new FormData(this);
    formData.append('requestType', 'UpdateAccount'); // corrected

    $.ajax({
        url: "../controller/end-points/controller.php",
        type: 'POST',
        data: formData,
        contentType: false,  
        processData: false,  
        success: function(response) {
            console.log(response);
            alertify.success('Profile updated successfully!');
            setTimeout(function() {
                location.reload();
            }, 1000);  // 1000 milliseconds = 1 second
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alertify.error('Failed to update profile.');
        }
    });
});
