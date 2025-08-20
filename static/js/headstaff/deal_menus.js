

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













$(document).ready(function () {
    fetchDealById();
});