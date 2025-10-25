$(document).ready(function () {



$(document).ready(function () {
  // Show initial warning on load
  $("#seats-warning").show().text("Please enter number of seats.");

  $("#seats").on("input", function () {
    let val = $(this).val().trim();

    // Always hide the warning when user starts typing
    $("#seats-warning").hide();

    // If input is empty → show warning and stop
    if (val === "") {
      $("#seats-warning").show().text("Please enter number of seats.");
      $("#table-legend").empty();
      return;
    }

    // If not a number → show warning
    if (isNaN(val)) {
      $("#seats-warning").show().text("Please enter a valid number.");
      $("#table-legend").empty();
      return;
    }

    val = parseInt(val, 10);

    // Restrict between 1 and 6
    if (val > 6) {
      $("#seats-warning").show().text("Maximum number of seats is 6.");
      val = 6;
    } else if (val < 1) {
      $("#seats-warning").show().text("Minimum number of seats is 1.");
      val = 1;
    }

    // Apply corrected value back to input
    $(this).val(val);

    // Update table legend
    const legendContainer = $("#table-legend");
    legendContainer.empty();
    for (let i = 0; i < val; i++) {
      legendContainer.append('<span class="material-icons text-yellow-500 text-3xl">person</span>');
    }
  });

  // On load, if no input yet → show warning
  if ($("#seats").val().trim() === "") {
    $("#seats-warning").show().text("Please enter number of seats.");
  } else {
    $("#seats-warning").hide();
  }
});



    // Open modal with fadeIn
  $(document).on("click", "#payment_qr", function () {
    let imgSrc = $(this).attr("src");
    $("#modal_img").attr("src", imgSrc);
    $("#payment_img_modal").fadeIn(300); // show with animation
    console.log("Modal opened with image:", imgSrc);
  });

  // Close modal with fadeOut
  $(document).on("click", "#close_modal, #payment_img_modal", function (e) {
    if (e.target.id === "payment_img_modal" || e.target.id === "close_modal") {
      $("#payment_img_modal").fadeOut(300); // hide with animation
    }
  });

  // Payment method logic (unchanged)
  $("#payment_method").on("change", function () {
    const selected = $(this).val();
    let qrSrc = "";
    let text = "";

    if (selected === "gcash") {
      text = "Send payment to GCash number: 0917-123-4567 (Juan Dela Cruz)";
      qrSrc = "../static/qr/grillbook_gcash_qr.jpg";
    } 
    else if (selected === "bpi") {
      text = "Transfer to BPI Account: 1234-5678-90 (Juan Dela Cruz)";
      qrSrc = "../static/qr/bpi.webp";
    } 
    else if (selected === "maya") {
      text = "Send payment to Maya account: 0917-987-6543 (Juan Dela Cruz)";
      qrSrc = "../static/qr/maya.jpg";
    } 
    else if (selected === "paypal") {
      text = "Pay via PayPal: paypal.me/juanpay";
      qrSrc = "../static/qr/paypal.webp";
    }

    if (qrSrc) {
      $("#payment_text").text(text);
      $("#payment_qr").attr("src", qrSrc);
      $("#download_qr_btn").attr("href", qrSrc);
      $("#payment_details").fadeIn(300).removeClass("hidden");
    } else {
      $("#payment_details").fadeOut(300, function () {
        $(this).addClass("hidden");
      });
    }
  });
});








$(function () {
  // ===== Global variables to track state =====
  let isTableAvailable = false;
  let isTermsAccepted = false;

  // ===== Open Modal & Load All Data =====
  $(document).on("click", ".setSchedule", function () {
    const table_code = $(this).data("value");

     let today = new Date();
    today.setDate(today.getDate() + 1);
    // format YYYY-MM-DD
    let yyyy = today.getFullYear();
    let mm = String(today.getMonth() + 1).padStart(2, '0');
    let dd = String(today.getDate()).padStart(2, '0');

    let tomorrow = `${yyyy}-${mm}-${dd}`;

    // set as default value
    $('#date_schedule').val(tomorrow);



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

  // Payment Proof input
  $("#payment_proof")
    .prop("disabled", !shouldEnable)
    .toggleClass("cursor-not-allowed", !shouldEnable)
    .toggleClass("cursor-pointer", shouldEnable);

  // Submit Button
  $("#submitBtn")
    .prop("disabled", !shouldEnable)
    .toggleClass("cursor-not-allowed opacity-50", !shouldEnable)
    .toggleClass("opacity-100 cursor-pointer", shouldEnable);

  // Availability Instruction message
  if (shouldEnable) {
    $("#availabilityInstruction")
      .removeClass("bg-red-500")
      .addClass("bg-green-600")
      .html(`
        <span class="material-icons mr-2">check_circle</span>
        Table is available and terms accepted. You may now submit your reservation.
      `);
  } else {
    $("#availabilityInstruction")
      .removeClass("bg-green-600")
      .addClass("bg-red-500")
      .html(`
        <span class="material-icons mr-2">event_busy</span>
        Please check the availability of your date schedule before submitting.
      `);
  }
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
            time_schedule: time 
        },
    success: function(response) {
    const availability = response.availability; // unwrap

    const chosenTimeFormatted = formatTime24to12($("#time_schedule").val());
    const openTimeFormatted = formatTime24to12(availability.open_time);
    const closeTimeFormatted = formatTime24to12(availability.close_time);

    if (availability.available === true) {
    Swal.fire({
        icon: 'success',
        title: 'Available!',
        html: `
            <p>Table <b>${$("#table_code").val()}</b> is available on 
            <b>${availability.dayOfWeek}, ${$("#date_schedule").val()}</b> at <b>${chosenTimeFormatted}</b>.</p>
            <br>
            <strong>Business Hours (${availability.dayOfWeek}):</strong><br>
            <span style="color:green; font-weight:bold">
                ${openTimeFormatted} – ${closeTimeFormatted}
            </span>
        `,
        confirmButtonText: 'Continue Reservation'
    });

    // ✅ Mark as available
    isTableAvailable = true;
    updateFormState();

    // Enable other fields (if meron kang gusto pang i-enable agad)
    $("#reservationForm input, #reservationForm select").prop('disabled', false);

} else {
    // kapag hindi available
    isTableAvailable = false;
    updateFormState();
    
    let reasonText = '';
    if (availability.reason === "outside_hours") {
        reasonText = `<p style="color:red"><b>Your chosen time is outside our business hours.</b></p>`;
    } else if (availability.reason === "conflict") {
        reasonText = `<p style="color:red"><b>There is already a confirmed reservation.</b></p>`;
    }

    let conflictHTML = '';
    if (availability.conflicts && availability.conflicts.length > 0) {
        conflictHTML += `<h4>Existing reservations:</h4><ul>`;
        availability.conflicts.forEach(conflict => {
            conflictHTML += `<li>
                <b>${formatTime24to12(conflict.time_schedule)}</b> - Status: ${conflict.status}
            </li>`;
        });
        conflictHTML += `</ul>`;
    }

    Swal.fire({
        icon: 'error',
        title: 'Not Available',
        html: `
            <p>Table <b>${$("#table_code").val()}</b> is not available on 
            <b>${availability.dayOfWeek}, ${$("#date_schedule").val()}</b> at <b>${chosenTimeFormatted}</b>.</p>
            ${reasonText}
            ${conflictHTML}
            <br>
            <strong>Business Hours (${availability.dayOfWeek}):</strong><br>
            <span style="color:red; font-weight:bold">
                ${openTimeFormatted} – ${closeTimeFormatted}
            </span>
        `,
        confirmButtonText: 'Choose Different Time'
    });

    $("#reservationForm input, #reservationForm select").prop('disabled', true);
}

}, error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
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





function formatTime24to12(time24) {
    if (!time24) return '';
    const [hour, minute] = time24.split(':');
    let h = parseInt(hour, 10);
    const m = minute;
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12; // convert 0 → 12
    return `${h}:${m} ${ampm}`;
}






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



$("#frmRequestReservation").on("submit", function (e) {
    e.preventDefault();

    const seats = $('#seats').val();

    // === Validation ===
    if (!isTableAvailable) {
        Swal.fire({
            icon: 'warning',
            title: 'Availability Required',
            text: 'Please check table availability first before submitting your reservation.'
        });
        return;
    }

    if (isNaN(seats) || seats < 1 || seats > 6) {
        alertify.error("Please enter a valid number of seats (1–6).");
        return;
    }

    if (!$('#payment_proof').val()) {
        alertify.error("Please upload payment receipt.");
        return; 
    }

    if (!$('#termsFileSigned').val()) {
        alertify.error("Please upload term with signed.");
        return; 
    }

    // === Collect selected items with qty ===
    const selectedMenus = [];
    $('input[name="menu_select[]"]:checked').each(function() {
        const menuCard = $(this).closest('.swiper-slide');
        const qty = parseInt(menuCard.find('input[name^="menu_quantity"]').val()) || 1;
        const price = parseFloat(menuCard.find('p:contains("Price:")').text().replace(/[^0-9.]/g, '')) || 0;

        selectedMenus.push({
            id: $(this).val(),
            name: menuCard.find('h3').text(),
            price: price,
            qty: qty,
            total: price * qty,
            type: 'menu'
        });
    });

    const selectedPromos = [];
    $('#promoContainer input[name="promo_select[]"]:checked').each(function() {
        const dealCard = $(this).closest('.swiper-slide');
        const qty = parseInt(dealCard.find('input[name^="promo_quantity"]').val()) || 1;
        const price = parseFloat(dealCard.find('p:contains("Total Price:")').text().replace(/[^0-9.]/g, '')) || 0;

        selectedPromos.push({
            id: $(this).val(),
            name: dealCard.find('h3').text(),
            price: price,
            qty: qty,
            total: price * qty,
            type: 'promo_deal'
        });
    });

    const selectedGroups = [];
    $('#groupContainer input[name="group_select[]"]:checked').each(function() {
        const dealCard = $(this).closest('.swiper-slide');
        const qty = parseInt(dealCard.find('input[name^="group_quantity"]').val()) || 1;
        const price = parseFloat(dealCard.find('p:contains("Total Price:")').text().replace(/[^0-9.]/g, '')) || 0;

        selectedGroups.push({
            id: $(this).val(),
            name: dealCard.find('h3').text(),
            price: price,
            qty: qty,
            total: price * qty,
            type: 'group_deal'
        });
    });

    if (selectedMenus.length === 0 && selectedPromos.length === 0 && selectedGroups.length === 0) {
        alertify.error("Please select at least one Menu, Promo, or Group deal before submitting.");
        return;
    }

    // === Totals ===
    const totalMenuPrice = selectedMenus.reduce((sum, item) => sum + item.total, 0);
    const totalPromoPrice = selectedPromos.reduce((sum, item) => sum + item.total, 0);
    const totalGroupPrice = selectedGroups.reduce((sum, item) => sum + item.total, 0);
    const grandTotal = totalMenuPrice + totalPromoPrice + totalGroupPrice;

    // === FormData ===
    const formData = new FormData(this);
    formData.append("requestType", "RequestReservation");
    formData.append("selected_menus", JSON.stringify(selectedMenus));
    formData.append("selected_promos", JSON.stringify(selectedPromos));
    formData.append("selected_groups", JSON.stringify(selectedGroups));
    formData.append("menu_total", totalMenuPrice.toFixed(2));
    formData.append("promo_total", totalPromoPrice.toFixed(2));
    formData.append("group_total", totalGroupPrice.toFixed(2));
    formData.append("grand_total", grandTotal.toFixed(2));

    // === SweetAlert Summary ===
    let summaryHTML = `<div style="text-align:left;">`;
    if (selectedMenus.length) {
        summaryHTML += `<h4 style="color:#FFD700;">Menus:</h4><ul>`;
        selectedMenus.forEach(item => summaryHTML += `<li>• ${item.name} (x${item.qty}) - ₱${item.total.toFixed(2)}</li>`);
        summaryHTML += `</ul><p><b>Menu Total:</b> ₱${totalMenuPrice.toFixed(2)}</p><br>`;
    }
    if (selectedPromos.length) {
        summaryHTML += `<h4 style="color:#FFD700;">Promo Deals:</h4><ul>`;
        selectedPromos.forEach(item => summaryHTML += `<li>• ${item.name} (x${item.qty}) - ₱${item.total.toFixed(2)}</li>`);
        summaryHTML += `</ul><p><b>Promo Total:</b> ₱${totalPromoPrice.toFixed(2)}</p><br>`;
    }
    if (selectedGroups.length) {
        summaryHTML += `<h4 style="color:#FFD700;">Group Deals:</h4><ul>`;
        selectedGroups.forEach(item => summaryHTML += `<li>• ${item.name} (x${item.qty}) - ₱${item.total.toFixed(2)}</li>`);
        summaryHTML += `</ul><p><b>Group Total:</b> ₱${totalGroupPrice.toFixed(2)}</p><br>`;
    }
    summaryHTML += `<hr><h3>Grand Total: ₱${grandTotal.toFixed(2)}</h3></div>`;

    Swal.fire({
        title: 'Reservation Summary',
        html: summaryHTML,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Proceed',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#FFD700',
        background: '#2B2B2B',
        color: '#FFFFFF'
    }).then((result) => {
        if (result.isConfirmed) {
            submitReservation(formData);
        }
    });
});


// === AJAX submission function ===
function submitReservation(formData) {
    $("#submitBtn").prop('disabled', true).text('Submitting...');

    $.ajax({
        url: "../controller/end-points/controller.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Reservation Submitted!',
                text: 'Your reservation has been submitted successfully.',
                confirmButtonText: 'OK'
            }).then(() => {
                // closeModal();
                location.reload();
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
            updateFormState();
        }
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
            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] shadow-lg relative text-center w-72">
              <!-- Checkbox -->
              <div class="absolute top-4 left-4">
                <input type="checkbox" class="w-5 h-5 accent-[#FFD700] cursor-pointer"
                  value="${menu.menu_id}" name="menu_select[]" />
              </div>

              <!-- Menu Info -->
              <button type="button" class="w-full text-left focus:outline-none" data-id="${menu.menu_id}">
                <img src="../static/upload/${menu.menu_image_banner}" 
                  alt="${menu.menu_name}" class="w-full h-40 object-cover rounded-lg mb-4" />
                <h3 class="text-xl font-bold text-[#FFD700] mb-1">${menu.menu_name}</h3>
                <p class="text-[#CCCCCC] text-sm mb-2">Price: ₱${menu.menu_price}</p>
              </button>

              <!-- Quantity Input -->
              <div class="flex items-center justify-center mt-2 space-x-2">
                  <button 
                    type="button" 
                    class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition"
                    data-target="menu-${menu.menu_id}" 
                    data-change="-1"
                  >−</button>

                  <input 
                    type="number" 
                    min="1" 
                    value="1" 
                    id="menu-${menu.menu_id}"
                    name="menu_quantity[${menu.menu_id}]" 
                    class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1" 
                  />

                  <button 
                    type="button" 
                    class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition"
                    data-target="menu-${menu.menu_id}" 
                    data-change="1"
                  >+</button>
                </div>

            </div>
          `);
        });

        initSwiper(".menuSwiper");
        attachQtyHandlers();
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

              <button type="button" class="w-full text-left focus:outline-none" data-id="${deal.deal_id}">
                <img src="../static/upload/${deal.deal_img_banner}" 
                    alt="${deal.deal_name}" 
                    class="w-full h-40 object-cover rounded-lg mb-4" />
                <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                <p class="text-[#CCCCCC] text-sm mb-2">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
              </button>

              <!-- Quantity Input -->
              <div class="flex items-center justify-center mt-2 space-x-2">
                <button 
                  type="button" 
                  class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                  data-target="promo-${deal.deal_id}" 
                  data-change="-1"
                >−</button>

                <input 
                  type="number" 
                  min="1" 
                  value="1" 
                  id="promo-${deal.deal_id}"
                  name="promo_quantity[${deal.deal_id}]" 
                  class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1" 
                />

                <button 
                  type="button" 
                  class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                  data-target="promo-${deal.deal_id}" 
                  data-change="1"
                >+</button>
              </div>

              <button 
                type="button" 
                class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                data-id="${deal.deal_id}"
              >
                Show Menu
              </button>

              ${menusHTML}
            </div>
          `);
        });

        initSwiper(".promoSwiper");
        attachQtyHandlers();

        $(".toggle-menu-btn").off("click").on("click", function () {
          const id = $(this).data("id");
          const menuList = $(`#menuList-${id}`);
          const isHidden = menuList.hasClass("hidden");
          $(this).text(isHidden ? "Hide Menu" : "Show Menu");
          menuList.toggleClass("hidden");
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

              <button type="button" class="w-full text-left focus:outline-none" data-id="${deal.deal_id}">
                <img src="../static/upload/${deal.deal_img_banner}" 
                    alt="${deal.deal_name}" 
                    class="w-full h-40 object-cover rounded-lg mb-4" />
                <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                <p class="text-[#CCCCCC] text-sm mb-2">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
              </button>

              <!-- Quantity Input -->
              <div class="flex items-center justify-center mt-2 space-x-2">
                <button 
                  type="button" 
                  class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                  data-target="group-${deal.deal_id}" 
                  data-change="-1"
                >−</button>

                <input 
                  type="number" 
                  min="1" 
                  value="1" 
                  id="group-${deal.deal_id}"
                  name="group_quantity[${deal.deal_id}]" 
                  class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1" 
                />

                <button 
                  type="button" 
                  class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                  data-target="group-${deal.deal_id}" 
                  data-change="1"
                >+</button>
              </div>

              <button 
                type="button" 
                class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                data-id="${deal.deal_id}"
              >
                Show Menu
              </button>

              ${menusHTML}
            </div>
          `);
        });

        initSwiper(".groupSwiper");
        attachQtyHandlers();

        $(".toggle-menu-btn").off("click").on("click", function () {
          const id = $(this).data("id");
          const menuList = $(`#menuList-${id}`);
          const isHidden = menuList.hasClass("hidden");
          $(this).text(isHidden ? "Hide Menu" : "Show Menu");
          menuList.toggleClass("hidden");
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



/* ✅ Universal quantity button handler */
function attachQtyHandlers() {
  $(".qty-btn").off("click").on("click", function () {
    const targetId = $(this).data("target");
    const input = $(`#${targetId}`);
    let value = parseInt(input.val()) || 1;
    const change = parseInt($(this).data("change"));
    value = Math.max(1, value + change);
    input.val(value);
  });
}





});




function initSwiper(selector) {
  new Swiper(selector, {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true, // infinite swipe
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      640: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  });
}
