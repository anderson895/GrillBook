$(function () {
  // ===== Open Modal & Load All Data =====
  $(document).on("click", ".setSchedule", function () {
    const table_code = $(this).data("value");
    $("#table_code").val(table_code);
    $("#table_code_label").text(table_code);

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
    $("#payment_proof")
      .prop("disabled", true)
      .addClass("cursor-not-allowed")
      .removeClass("cursor-pointer");
    $("#submitBtn")
      .prop("disabled", true)
      .addClass("cursor-not-allowed opacity-50")
      .removeClass("opacity-100 cursor-pointer");
  }



  // ===== Fetch Menus =====
// ====== Helper: Initialize Swiper ======
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


// ====== Fetch Menus ======
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


// ====== Fetch Promo Deals ======
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

          // Build menu list HTML (hidden by default)
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

          // Append deal card
          container.append(`
            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] w-72 shadow-lg relative text-center">

              <!-- Checkbox -->
              <input type="checkbox" 
                  class="absolute top-3 left-3 w-5 h-5 accent-[#FFD700] cursor-pointer" 
                  value="${deal.deal_id}" />

              <!-- Deal Info -->
              <button type="button" 
                  class="w-full text-left focus:outline-none" 
                  data-id="${deal.deal_id}">
                  <img src="../static/upload/${deal.deal_img_banner}" 
                      alt="${deal.deal_name}" 
                      class="w-full h-40 object-cover rounded-lg mb-4" />
                  <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                  <p class="text-[#CCCCCC] text-sm mb-1">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
              </button>

              <!-- Toggle Button -->
              <button type="button" class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm" 
                  data-id="${deal.deal_id}">
                  Show Menu
              </button>

              ${menusHTML}
            </div>
          `);
        });

        // Init Swiper after append
        initSwiper(".promoSwiper");

        // Attach Toggle Event
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

          // Build menu list HTML (hidden by default)
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

              <!-- Checkbox -->
              <input type="checkbox" 
                  class="absolute top-3 left-3 w-5 h-5 accent-[#FFD700] cursor-pointer" 
                  value="${deal.deal_id}" />

              <!-- Deal Info -->
              <button type="button" 
                  class="w-full text-left focus:outline-none" 
                  data-id="${deal.deal_id}">
                  <img src="../static/upload/${deal.deal_img_banner}" 
                      alt="${deal.deal_name}" 
                      class="w-full h-40 object-cover rounded-lg mb-4" />
                  <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                  <p class="text-[#CCCCCC] text-sm mb-1">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
              </button>

              <!-- Toggle Button -->
              <button type="button" class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm" 
                  data-id="${deal.deal_id}">
                  Show Menu
              </button>

              ${menusHTML}
            </div>
          `);
        });

        initSwiper(".groupSwiper");

        // Attach Toggle Event
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
    }
  });
}








  // ===== File Upload Preview =====
  $("#payment_proof").on("change", function () {
    const fileName = $(this).val().split("\\").pop();
    $("#fileNamePreview").text(fileName ? `Selected file: ${fileName}` : "");
  });

  // ===== Terms Checkbox Handling =====
  $("#terms").on("change", function () {
    const checked = $(this).is(":checked");

    $("#payment_proof")
      .prop("disabled", !checked)
      .toggleClass("cursor-not-allowed", !checked)
      .toggleClass("cursor-pointer", checked);

    $("#submitBtn")
      .prop("disabled", !checked)
      .toggleClass("cursor-not-allowed opacity-50", !checked)
      .toggleClass("opacity-100 cursor-pointer", checked);
  });

  // ===== Check Availability =====
  $("#btnCheckAvailability").on("click", function () {
    const seats = $("#seats").val();
    const date = $("#date_schedule").val();
    const time = $("#time_schedule").val();

    if (!seats || !date || !time) {
      alert("Please fill in seats, date, and time before checking availability.");
      return;
    }

    // Example check - replace with AJAX to backend
    alert(`Checking availability for:\nSeats: ${seats}\nDate: ${date}\nTime: ${time}`);
  });

  // ===== Submit Form =====
  $("#frmRequestReservation").on("submit", function (e) {
    e.preventDefault();

    if ($('input[name="menus[]"]:checked').length === 0 &&
        $('input[name="promos[]"]:checked').length === 0 &&
        $('input[name="groups[]"]:checked').length === 0) {
      alert("Please select at least one menu, promo, or group deal.");
      return;
    }

    const formData = new FormData(this);

    $.ajax({
      url: "../controller/end-points/controller.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        alert(res);
        closeModal();
      }
    });
  });
});
