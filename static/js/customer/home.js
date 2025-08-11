$(function () {
  // ===== Global variables to track state =====
  let isTableAvailable = false;
  let isTermsAccepted = false;

  // ===== Open Modal & Load All Data =====
  $(document).on("click", ".setSchedule", function () {
    const table_code = $(this).data("value");
    $("#table_code").val(table_code);
    $("#table_code_label").text(table_code);

    // Reset availability state when opening modal
    isTableAvailable = false;
    updateFormState();

    fetchMenus();
    fetchPromos();
    fetchGroups();

    $("#scheduleModal").fadeIn();
  });

  // ===== Close Modal =====
  $("#closeAddModal").click(function () {
    closeModal();
  });

  $(window).click(function (e) {
    if ($(e.target).is("#scheduleModal")) {
      closeModal();
    }
  });

  function closeModal() {
    $("#scheduleModal").fadeOut();
    $("#frmRequestReservation")[0].reset();
    $("#fileNamePreview").text("");
    
    // Reset state
    isTableAvailable = false;
    isTermsAccepted = false;
    updateFormState();
  }

  // ===== Function to update form state based on availability and terms =====
  function updateFormState() {
    const shouldEnable = isTableAvailable && isTermsAccepted;
    
    $("#payment_proof")
      .prop("disabled", !shouldEnable)
      .toggleClass("cursor-not-allowed", !shouldEnable)
      .toggleClass("cursor-pointer", shouldEnable);

    $("#submitBtn")
      .prop("disabled", !shouldEnable)
      .toggleClass("cursor-not-allowed opacity-50", !shouldEnable)
      .toggleClass("opacity-100 cursor-pointer", shouldEnable);
  }

  // ===== Terms Checkbox Handling =====
  $("#terms").on("change", function () {
    isTermsAccepted = $(this).is(":checked");
    updateFormState();
  });

  // ===== File Upload Preview =====
  $("#payment_proof").on("change", function () {
    const fileName = $(this).val().split("\\").pop();
    $("#fileNamePreview").text(fileName ? `Selected file: ${fileName}` : "");
  });

  // ===== Check Availability =====
  $("#btnCheckAvailability").on("click", function () {
    const table_code = $("#table_code").val();
    const date = $("#date_schedule").val();
    const time = $("#time_schedule").val();

    if (!table_code || !date || !time) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all fields: Table Code, Date, and Time'
        });
        return;
    }

    $(this).prop('disabled', true).text('Checking...');

    $.ajax({
        url: "../controller/end-points/controller.php",
        method: "GET",
        dataType: 'json',
        data: {
            requestType: 'checkAvailability',
            table_code: table_code,
            date_schedule: date,
            time_schedule: time  // added time
        },
        success: function(response) {
            console.log(response);

            if (response.status === 200) {
                // Use response.available if your backend returns that
                // or adjust to response.availability if it's boolean
                if (response.available === true || response.availability === true) {
                    isTableAvailable = true;
                    updateFormState();

                    // Hide the instruction once the table is available
                    $("#availabilityInstruction").hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Available!',
                        text: `Table ${table_code} is available on ${date} at ${time}. You can now proceed with payment upload.`,
                        confirmButtonText: 'Continue Reservation'
                    });

                    $("#reservationForm input, #reservationForm select").prop('disabled', false);

                } else {
                    isTableAvailable = false;
                    updateFormState();

                       // Keep showing the instruction if not available
                    $("#availabilityInstruction").show();

                    let conflictHTML = `<p>Table <b>${table_code}</b> is not available on <b>${date}</b> at <b>${time}</b>.</p>`;

                    if (response.conflicts && response.conflicts.length > 0) {
                        conflictHTML += `<h4>Existing reservations:</h4><ul>`;
                        response.conflicts.forEach(conflict => {
                            conflictHTML += `<li>
                                <b>${conflict.time_schedule}</b> - Status: ${conflict.status}
                                <br><small>Ends: ${conflict.end_time}</small>
                            </li>`;
                        });
                        conflictHTML += `</ul>`;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Not Available',
                        html: conflictHTML + `
                            <br><br>
                            <strong>Our Available Hours Are:</strong><br>
                            Sunday: 5 PM – 3 AM<br>
                            Monday: 5 PM – 2 AM<br>
                            Tuesday: 5 PM – 2 AM<br>
                            Wednesday: 5 PM – 2 AM<br>
                            Thursday: 5 PM – 2 AM<br>
                            Friday: 7 PM – 4 AM<br>
                            Saturday: 7 PM – 4 AM
                        `,
                        confirmButtonText: 'Choose Different Time'
                    });

                    $("#reservationForm input, #reservationForm select").prop('disabled', true);
                }
            } else {
                isTableAvailable = false;
                updateFormState();

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to check availability'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            isTableAvailable = false;
            updateFormState();

            Swal.fire({
                icon: 'error',
                title: 'Connection Error',
                text: 'Failed to connect to server. Please try again.'
            });
        },
        complete: function() {
            $("#btnCheckAvailability").prop('disabled', false).text('Check Availability');
        }
    });
});


  // ===== Reset availability when schedule fields change =====
  $("#date_schedule, #time_schedule").on("change", function() {
    isTableAvailable = false;
    updateFormState();
    
    // Clear file selection when availability changes
    $("#payment_proof").val("");
    $("#fileNamePreview").text("");
    
    // Show message that availability needs to be checked again
    if ($("#date_schedule").val() && $("#time_schedule").val()) {
      Swal.fire({
        icon: 'info',
        title: 'Schedule Changed',
        text: 'Please check availability again for the new date/time.',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
    }
  });

  // ===== Submit Form =====
  $("#frmRequestReservation").on("submit", function (e) {
    e.preventDefault();

    // Double-check availability before submitting
    if (!isTableAvailable) {
      Swal.fire({
        icon: 'warning',
        title: 'Availability Required',
        text: 'Please check table availability first before submitting your reservation.'
      });
      return;
    }

    var payment_proof = $('#payment_proof').val();
    if (payment_proof === "") {
        alertify.error("Please upload payment receipt.");
        return; 
    }

    const formData = new FormData(this);
    formData.append("requestType", "RequestReservation");

    // Collect selected menu items
    const selectedMenus = [];
    $('input[name="menu_select[]"]:checked').each(function() {
      const menuId = $(this).val();
      const menuCard = $(this).closest('.swiper-slide');
      const menuName = menuCard.find('h3').text();
      const menuPriceText = menuCard.find('p:contains("Price:")').text();
      const menuPrice = menuPriceText.replace('Price: ₱', '').replace('Price: ', '');
      
      selectedMenus.push({
        id: menuId,
        name: menuName,
        price: parseFloat(menuPrice) || 0,
        type: 'menu'
      });
    });

    // Collect selected promo deals
    const selectedPromos = [];
    $('#promoContainer input[type="checkbox"]:checked').each(function() {
      const dealId = $(this).val();
      const dealCard = $(this).closest('.swiper-slide');
      const dealName = dealCard.find('h3').text();
      const dealPriceText = dealCard.find('p:contains("Total Price:")').text();
      const dealPrice = dealPriceText.replace('Total Price: ₱', '').replace('Total Price: ', '');
      
      selectedPromos.push({
        id: dealId,
        name: dealName,
        price: parseFloat(dealPrice) || 0,
        type: 'promo_deal'
      });
    });

    // Collect selected group deals
    const selectedGroups = [];
    $('#groupContainer input[type="checkbox"]:checked').each(function() {
      const dealId = $(this).val();
      const dealCard = $(this).closest('.swiper-slide');
      const dealName = dealCard.find('h3').text();
      const dealPriceText = dealCard.find('p:contains("Total Price:")').text();
      const dealPrice = dealPriceText.replace('Total Price: ₱', '').replace('Total Price: ', '');
      
      selectedGroups.push({
        id: dealId,
        name: dealName,
        price: parseFloat(dealPrice) || 0,
        type: 'group_deal'
      });
    });

    // Calculate total price
    const totalMenuPrice = selectedMenus.reduce((sum, item) => sum + item.price, 0);
    const totalPromoPrice = selectedPromos.reduce((sum, item) => sum + item.price, 0);
    const totalGroupPrice = selectedGroups.reduce((sum, item) => sum + item.price, 0);
    const grandTotal = totalMenuPrice + totalPromoPrice + totalGroupPrice;

    // Add selected items data to form
    formData.append("selected_menus", JSON.stringify(selectedMenus));
    formData.append("selected_promos", JSON.stringify(selectedPromos));
    formData.append("selected_groups", JSON.stringify(selectedGroups));
    formData.append("menu_total", totalMenuPrice.toFixed(2));
    formData.append("promo_total", totalPromoPrice.toFixed(2));
    formData.append("group_total", totalGroupPrice.toFixed(2));
    formData.append("grand_total", grandTotal.toFixed(2));

    // Optional: Show summary before submitting
    if (selectedMenus.length > 0 || selectedPromos.length > 0 || selectedGroups.length > 0) {
      let summary = "Selected Items:\n\n";
      
      if (selectedMenus.length > 0) {
        summary += "Menus:\n";
        selectedMenus.forEach(item => {
          summary += `- ${item.name}: ₱${item.price.toFixed(2)}\n`;
        });
        summary += `Menu Total: ₱${totalMenuPrice.toFixed(2)}\n\n`;
      }
      
      if (selectedPromos.length > 0) {
        summary += "Promo Deals:\n";
        selectedPromos.forEach(item => {
          summary += `- ${item.name}: ₱${item.price.toFixed(2)}\n`;
        });
        summary += `Promo Total: ₱${totalPromoPrice.toFixed(2)}\n\n`;
      }
      
      if (selectedGroups.length > 0) {
        summary += "Group Deals:\n";
        selectedGroups.forEach(item => {
          summary += `- ${item.name}: ₱${item.price.toFixed(2)}\n`;
        });
        summary += `Group Total: ₱${totalGroupPrice.toFixed(2)}\n\n`;
      }
      
      summary += `Grand Total: ₱${grandTotal.toFixed(2)}`;
      
      if (!confirm(summary + "\n\nProceed with reservation?")) {
        return;
      }
    }

    // Show loading state
    $("#submitBtn").prop('disabled', true).text('Submitting...');

    $.ajax({
      url: "../controller/end-points/controller.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        // Handle success response
        Swal.fire({
          icon: 'success',
          title: 'Reservation Submitted!',
          text: 'Your reservation has been submitted successfully.',
          confirmButtonText: 'OK'
        }).then(() => {
          closeModal();
        });
      },
      error: function(xhr, status, error) {
        console.error("Submission Error:", status, error);
        Swal.fire({
          icon: 'error',
          title: 'Submission Failed',
          text: 'Error submitting reservation. Please try again.'
        });
      },
      complete: function() {
        $("#submitBtn").prop('disabled', false).text('Submit Reservation');
        updateFormState(); // Re-apply proper state
      }
    });
  });

  // ===== Initialize Swiper functions (keeping your existing functions) =====
  function initSwiper(selector) {
    return new Swiper(selector, {
      loop: true,
      slidesPerView: 1.2,
      spaceBetween: 20,
      breakpoints: {
        640: { slidesPerView: 1.5 },
        768: { slidesPerView: 2.5 },
        1024: { slidesPerView: 3 },
      },
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
    });
  }

  // ===== Fetch functions (keeping your existing functions) =====
  function fetchMenus() {
    $.ajax({
      url: "../controller/end-points/controller.php",
      method: "GET",
      data: { requestType: "fetch_all_menu" },
      dataType: "json",
      success: function (response) {
        if (response.status === 200 && response.data.length > 0) {
          const container = $("#menuContainer").empty();

          response.data.forEach(menu => {
            container.append(`
            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] w-72 shadow-lg relative text-center">
    
              <!-- Checkbox -->
              <div class="absolute top-4 left-4">
                  <input type="checkbox" 
                  class="w-5 h-5 accent-[#FFD700] cursor-pointer"
                  value="${menu.menu_id}" 
                  name="menu_select[]" />
              </div>

              <!-- Button instead of link -->
              <button type="button" 
                  class="w-full text-left focus:outline-none" 
                  data-id="${menu.menu_id}">
                  <img src="../static/upload/${menu.menu_image_banner}" 
                      alt="${menu.menu_name}" 
                      class="w-full h-40 object-cover rounded-lg mb-4" />
                  <h3 class="text-xl font-bold text-[#FFD700] mb-1">${menu.menu_name}</h3>
                  <p class="text-[#CCCCCC] text-sm mb-1">Price: ${menu.menu_price}</p>
              </button>

              </div>
            `);
          });

          initSwiper(".menuSwiper");
        } else {
          console.error("No menu found or bad response.");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
      }
    });
  }

  function fetchPromos() {
    $.ajax({
      url: "../controller/end-points/controller.php",
      method: "GET",
      data: { requestType: "fetch_all_deals_and_menu", deal_type: "promo_deals" },
      dataType: "json",
      success: function (res) {
        const container = $("#promoContainer").empty();

        if (res.status === 200 && res.data.length > 0) {
          res.data.forEach(deal => {
            let menusHTML = '';
            if (deal.menus && deal.menus.length > 0) {
              menusHTML = `
                <div class="mt-3 space-y-2 hidden menu-list" id="menuList-${deal.deal_id}">
                  ${deal.menus.map(menu => `
                    <div class="flex items-center bg-[#1E1E1E] p-2 rounded-lg">
                      <img src="../static/upload/${menu.menu_image_banner}" 
                           alt="${menu.menu_name}" 
                           class="w-12 h-12 object-cover rounded-md mr-3" />
                      <div class="flex-1">
                        <p class="text-[#FFD700] font-semibold">${menu.menu_name}</p>
                        <p class="text-[#CCCCCC] text-sm">₱${parseFloat(menu.menu_price).toFixed(2)}</p>
                      </div>
                    </div>
                  `).join('')}
                </div>
              `;
            }

            container.append(`
              <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] w-72 shadow-lg relative text-center">
                <input type="checkbox" 
                    class="absolute top-3 left-3 w-5 h-5 accent-[#FFD700] cursor-pointer" 
                    value="${deal.deal_id}" 
                    name="promo_select[]" />
                <button type="button" 
                    class="w-full text-left focus:outline-none" 
                    data-id="${deal.deal_id}">
                    <img src="../static/upload/${deal.deal_img_banner}" 
                        alt="${deal.deal_name}" 
                        class="w-full h-40 object-cover rounded-lg mb-4" />
                    <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                    <p class="text-[#CCCCCC] text-sm mb-1">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
                </button>
                <button type="button" class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm" 
                    data-id="${deal.deal_id}">
                    Show Menu
                </button>
                ${menusHTML}
              </div>
            `);
          });

          initSwiper(".promoSwiper");

          $(".toggle-menu-btn").off("click").on("click", function () {
            const id = $(this).data("id");
            const menuList = $(`#menuList-${id}`);
            const isHidden = menuList.hasClass("hidden");

            if (isHidden) {
              menuList.removeClass("hidden");
              $(this).text("Hide Menu");
            } else {
              menuList.addClass("hidden");
              $(this).text("Show Menu");
            }
          });

        } else {
          $("#promo_section").hide();
        }
      },
      error: function(xhr, status, error) {
        console.error("Error fetching promos:", status, error);
      }
    });
  }

  function fetchGroups() {
    $.ajax({
      url: "../controller/end-points/controller.php",
      method: "GET",
      data: { requestType: "fetch_all_deals_and_menu", deal_type: "group_deals" },
      dataType: "json",
      success: function (res) {
        const container = $("#groupContainer").empty();

        if (res.status === 200 && res.data.length > 0) {
          res.data.forEach(deal => {
            let menusHTML = '';
            if (deal.menus && deal.menus.length > 0) {
              menusHTML = `
                <div class="mt-3 space-y-2 hidden menu-list" id="menuList-${deal.deal_id}">
                  ${deal.menus.map(menu => `
                    <div class="flex items-center bg-[#1E1E1E] p-2 rounded-lg">
                      <img src="../static/upload/${menu.menu_image_banner}" 
                           alt="${menu.menu_name}" 
                           class="w-12 h-12 object-cover rounded-md mr-3" />
                      <div class="flex-1">
                        <p class="text-[#FFD700] font-semibold">${menu.menu_name}</p>
                        <p class="text-[#CCCCCC] text-sm">₱${parseFloat(menu.menu_price).toFixed(2)}</p>
                      </div>
                    </div>
                  `).join('')}
                </div>
              `;
            }

            container.append(`
              <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] w-72 shadow-lg relative text-center">
                <input type="checkbox" 
                    class="absolute top-3 left-3 w-5 h-5 accent-[#FFD700] cursor-pointer" 
                    value="${deal.deal_id}" 
                    name="group_select[]" />
                <button type="button" 
                    class="w-full text-left focus:outline-none" 
                    data-id="${deal.deal_id}">
                    <img src="../static/upload/${deal.deal_img_banner}" 
                        alt="${deal.deal_name}" 
                        class="w-full h-40 object-cover rounded-lg mb-4" />
                    <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                    <p class="text-[#CCCCCC] text-sm mb-1">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
                </button>
                <button type="button" class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm" 
                    data-id="${deal.deal_id}">
                    Show Menu
                </button>
                ${menusHTML}
              </div>
            `);
          });

          initSwiper(".groupSwiper");

          $(".toggle-menu-btn").off("click").on("click", function () {
            const id = $(this).data("id");
            const menuList = $(`#menuList-${id}`);
            const isHidden = menuList.hasClass("hidden");

            if (isHidden) {
              menuList.removeClass("hidden");
              $(this).text("Hide Menu");
            } else {
              menuList.addClass("hidden");
              $(this).text("Show Menu");
            }
          });

        } else {
          container.html('<p class="text-gray-400">No group deals found.</p>');
        }
      },
      error: function(xhr, status, error) {
        console.error("Error fetching group deals:", status, error);
      }
    });
  }

});