 $("#addMenuBtn").click(function (e) { 
        e.preventDefault();
        $("#addMenuModal").fadeIn();
    });

     $("#closeAddGettableModal").click(function (e) { 
        e.preventDefault();
        $("#addMenuModal").fadeOut();
    });








    

    $('#frmAddMenu').on('submit', function(e) {
        e.preventDefault();

        var menuName = $('#menuName').val().trim();
        if (!menuName) {
            alertify.error("Please enter menu name.");
            return;
        }

        var menuDescription = $('#menuDescription').val().trim();
        if (!menuDescription) {
            alertify.error("Please enter description.");
            return;
        }
        
        var menuImage = $('#menuImage').val();
           if (menuImage === "") {
               alertify.error("Please upload an image.");
               return; 
           }

        var menuPrice = $('#menuPrice').val().trim();

        // Check kung walang laman
        if (!menuPrice) {
            alertify.error("Please enter a price.");
            return;
        }

        // Check kung number
        if (isNaN(menuPrice)) {
            alertify.error("Price must be a valid number.");
            return;
        }

        // Convert to float
        var priceValue = parseFloat(menuPrice);

        // Check kung less than or equal to 0
        if (priceValue <= 0) {
            alertify.error("Price must be greater than zero.");
            return;
        }


        $('.spinner').show();
        $('#frmAddMenu button[type="submit"]').prop('disabled', true);

        var formData = new FormData(this);
        formData.append('requestType', 'AddMenu');

        $.ajax({
            type: "POST",
            url: "../controller/end-points/controller.php",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
            $('.spinner').hide();
            $('#frmAddMenu button[type="submit"]').prop('disabled', false);

            if (response.status === 200) {
                Swal.fire('Success!', response.message, 'success').then(() => {
                window.location.href = 'menu';
                });
            } else {
                Swal.fire('Error', response.message || 'Something went wrong.', 'error');
            }
            }
        });
        });















        

   $.ajax({
    url: "../controller/end-points/controller.php",
    method: "GET",
    data: { requestType: "fetch_all_menu" },
    dataType: "json",
    success: function (res) {
        if (res.status === 200) {
            let count = 1;

            // Clear previous content (optional)
            $('#menuTableBody').empty();

            // Check if there is data
            if (res.data.length > 0) {
                res.data.forEach(menu => {



                    $('#menuTableBody').append(`
                        <tr class="hover:bg-[#2B2B2B] transition-colors">
                            <td class="p-3 text-center font-mono">${count++}</td>
                            <td class="p-3 text-center font-mono">${menu.menu_name}</td>
                            <td class="p-3 text-center font-semibold">
                            ${menu.menu_description.length > 60 ? menu.menu_description.substring(0, 60) + '...' : menu.menu_description}
                            </td>

                            <td class="p-3 text-center font-semibold">${menu.menu_price}</td>

                            <!-- Banner Image Column -->
                           <td class="p-3">
                                <div class="flex justify-center items-center">
                                    ${menu.menu_image_banner ? 
                                        `<img src="../static/upload/${menu.menu_image_banner}" alt="Banner" class="w-20 h-12 object-cover rounded" />`
                                        : 
                                        '<span class="text-white-500 p-3 text-center font-semibold">N/A</span>'}
                                </div>
                            </td>


                            <td class="p-3 text-center">
                                <button class="viewDetailsBtn bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded text-xs font-semibold transition"
                                data-menu_id ='${menu.menu_id}'
                                data-menu_name='${menu.menu_name}'
                                data-menu_description='${menu.menu_description}'
                                data-menu_price='${menu.menu_price}'
                                
                                >Details</button>
                                <button class="removeBtn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition"
                                data-menu_id='${menu.menu_id}'
                                data-menu_name='${menu.menu_name}'>Remove</button>
                            </td>
                            </tr>

                    `);
                });
            } else {
                $('#menuTableBody').append(`
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



  // Search filter
  $('#searchInput').on('input', function () {
    const term = $(this).val().toLowerCase();
    $('#eventTableBody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(term));
    });
  });




  

$(document).on("click", ".viewDetailsBtn", function () {
  const menu_id = $(this).data("menu_id");
  const menu_name = $(this).data("menu_name");
  const menu_description = $(this).data("menu_description");
  const menu_price = $(this).data("menu_price");


  $("#menu_id").val(menu_id);
  $("#menu_name_update").val(menu_name);
  $("#menu_description_update").val(menu_description);
  $("#menu_price_update").val(menu_price);


 $('#menuDetailsModal').fadeIn();

});

// Close modal
$(document).on("click", "#closeMenuDetailsModal", function () {
  $('#menuDetailsModal').fadeOut();
});















$(document).on("submit", "#frmUpdatMenu", function (e) {
  e.preventDefault();

  const form = this;
  const formData = new FormData(form);
  formData.append("requestType", "UpdatMenu");

  $.ajax({
    url: "../controller/end-points/controller.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.status === 200) {
        Swal.fire('Success!', response.message || 'Event info updated.', 'success').then(() => {
            location.reload();
        });
      } else {
        alertify.error(response.message || "Error updating.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Update error:", error);
      alertify.error("Failed to update. Please try again.");
    }
  });
});














// ALL REQUEST
    $(document).on('click', '.removeBtn', function(e) {
        e.preventDefault();
        const menu_id = $(this).data("menu_id");
        const menu_name = $(this).data("menu_name");
        
        console.log(menu_id);
    
        Swal.fire({
            title: `Are you sure to Remove <span style="color:red;">${menu_name}</span> ?`,
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
                    data: { menu_id: menu_id, requestType: 'removeMenu' },
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
