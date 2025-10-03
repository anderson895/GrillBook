// DYNAMIC PART
  
  $("#addBtn").click(function (e) { 
    e.preventDefault();
    $("#addModal").fadeIn();

      fetchMenus();

  });


  $("#closeAddModal").click(function (e) { 
  e.preventDefault();

    // Hide modal
    $("#addModal").fadeOut();

   
  });


function fetchMenus() {
  $.ajax({
    url: "../controller/end-points/controller.php",
    method: "GET",
    data: { requestType: "fetch_all_menu" },
    dataType: "json",
    success: function (data) {
      console.log("Server response:", data);

      const $menuSelect = $("#menuSelect");
      $menuSelect.empty().append(`<option value="">Select Menu</option>`);

      if (Array.isArray(data.data)) {
        data.data.forEach(menu => {
          $menuSelect.append(
            `<option value="${menu.menu_id}">${menu.menu_name}</option>`
          );
        });

        // Reinitialize Select2 after populating options
        $menuSelect.select2({
          placeholder: "Search or select a menu",
          allowClear: true,
          width: '100%' 
        });
      } else {
        alertify.error("No menu data found.");
      }
    }
  });
}









  $('#frmAddMenuDeals').on('submit', function (e) {
      e.preventDefault();
      
      const menuSelect = $('#menuSelect').val();
      if (menuSelect === "") {
        alertify.error("Please Select Menu.");
        return;
      }

      $('.spinner').show();

      const formData = new FormData(this);
      formData.append('requestType', 'AddMenuDeals');

      $.ajax({
        type: "POST",
        url: "../controller/end-points/controller.php",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
          $('.spinner').hide();

          if (response.status === 200) {
            Swal.fire('Success!', response.message, 'success').then(() => {
              location.reload();
            });
          } else {
            Swal.fire('Error', response.message || 'Something went wrong.', 'error');
          }
        }
      });
    });






function fetchDealById() {
    const dealId = $('#outputBody').data('deal_id'); 

    $.ajax({
        url: "../controller/end-points/controller.php",
        type: 'GET',
        data: {
            requestType: 'GetAllDealsWithMenus_byId',
            deal_id: dealId
        },
        dataType: 'json',
        success: function(response) {
        console.log(response); // For debugging
        const outputDiv = $('#outputBody');
        outputDiv.empty();

        if (response.status === 200 && response.data) {
            const deal = response.data;

            if (deal.menus && deal.menus.length > 0) {
                let html = `
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">`;

                deal.menus.forEach(menu => {
                    html += `
                        <div class="bg-gray-800 rounded-lg shadow-md p-4 hover:shadow-lg transition duration-300 relative">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-bold text-[#FFD700]">${menu.menu_name}</h3>
                                <button 
                                    class="removeBtn cursor-pointer text-red-500 hover:text-red-700 transition"
                                    data-menu_id="${menu.menu_id}" 
                                    data-menu_name="${menu.menu_name}" 
                                    title="Remove"
                                >
                                    <span class="material-icons">close</span>
                                </button>
                            </div>

                            <p class="text-sm text-gray-300 mb-1"><span class="font-semibold">Category:</span> ${menu.menu_category}</p>
                            <p class="text-sm text-gray-400 mb-2">${menu.menu_description}</p>
                            <p class="text-sm text-white font-medium mb-3">₱${menu.menu_price}</p>

                            ${menu.menu_image_banner 
                                ? `<img src="../static/upload/${menu.menu_image_banner}" alt="${menu.menu_name}" class="w-full h-40 object-cover rounded-md mb-4">`
                                : `<div class="w-full h-40 bg-gray-700 rounded-md flex items-center justify-center text-gray-400 text-sm mb-4">No Image</div>`
                            }
                        </div>`;
                });

                html += `</div>`;
                outputDiv.append(html);
            } else {
                // No menus
               outputDiv.html(`
                  <div class="text-center py-8">
                    <p class="text-lg text-yellow-400 font-medium">No menu items are currently available.</p>
                  </div>
                `);

            }

        } else {
            outputDiv.html('<p class="text-gray-500">Deal not found.</p>');
        }
    }

    });
}





    $(document).on('click', '.removeBtn', function(e) {
        e.preventDefault();
        var dealId = $('#outputBody').data('deal_id'); 
        var menu_id = $(this).data('menu_id');
        var menu_name = $(this).data('menu_name');

        Swal.fire({
            title: `Remove <span style="color:#f87171;">${menu_name}</span> ?`,
            html: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'No, cancel!',
            background: '#1f2937', // dark gray
            color: '#f3f4f6', // light text
            iconColor: '#f87171', // red icon
            confirmButtonColor: '#ef4444', // red confirm button
            cancelButtonColor: '#6b7280' // gray cancel button
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "../controller/end-points/controller.php",
                    type: 'POST',
                    data: { menu_id: menu_id,dealId:dealId, requestType: 'remove_deal_ids' },
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














$(document).ready(function () {
    fetchDealById();
});