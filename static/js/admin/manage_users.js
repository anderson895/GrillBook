 $.ajax({
    url: "../controller/end-points/controller.php",
    method: "GET",
    data: { requestType: "fetch_all_users" },
    dataType: "json",
    success: function (res) {
        if (res.status === 200) {
            let count = 1;

            // Clear previous content (optional)
            $('#userTableBody').empty();

            // Check if there is data
            if (res.data.length > 0) {
                res.data.forEach(data => {



                    $('#userTableBody').append(`
                        <tr class="hover:bg-[#2B2B2B] transition-colors">
                            <td class="p-3 text-center font-mono">${count++}</td>
                            <td class="p-3 text-center font-mono">${data.user_fname} ${data.user_lname}</td>
                            <td class="p-3 text-center font-mono capitalize">${data.user_email}</td>
                            <td class="p-3 text-center font-mono capitalize">${data.user_position}</td>

                          

                           


                            <td class="p-3 text-center">
                                
                                <button class="removeBtn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition"
                                data-user_id='${data.user_id }'
                                data-user_fname='${data.user_fname}'>Remove</button>
                            </td>
                            </tr>

                    `);
                });
            } else {
                $('#userTableBody').append(`
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-400 italic">
                            No record found
                        </td>
                    </tr>
                `);
            }
        }
    }
});





// ALL REQUEST
    $(document).on('click', '.removeBtn', function(e) {
        e.preventDefault();
        const user_id = $(this).data("user_id");
        const user_fname = $(this).data("user_fname");
        
        console.log(user_id);
    
        Swal.fire({
            title: `Are you sure to Remove <span style="color:red;">${user_fname}</span> ?`,
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "../controller/end-points/controller.php",
                    type: 'POST',
                    data: { user_id: user_id, requestType: 'removeUser' },
                    dataType: 'json', 
                    success: function(response) {
                      console.log(response);
                        if (response.status === 200) {
                            Swal.fire(
                                'Removed!',
                                response.message, 
                                'success'
                            ).then(() => {
                                location.reload(); 
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message, 
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'There was a problem with the request.',
                            'error'
                        );
                    }
                });
            }
        });
    });
