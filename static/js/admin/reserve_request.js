$(document).ready(function () {
   

  // Fetch data
  let currentPage = 1;
  let limit = 10; 

  function loadTable(page = 1) {
    currentPage = page;

   $.ajax({
    url: "../controller/end-points/controller.php",
    method: "GET",
    data: { 
      requestType: "fetch_all_reserve_request",
      page: page,
      limit: limit
    },
    dataType: "json",
    beforeSend: function () {
      // Show loading spinner
      $('#outputTableBody').html(`
        <tr>
          <td colspan="11" class="p-6 text-center">
            <div class="flex items-center justify-center space-x-2">
              <div class="w-6 h-6 border-2 border-yellow-400 border-t-transparent rounded-full animate-spin"></div>
              <span class="text-yellow-400 font-medium">Loading...</span>
            </div>
          </td>
        </tr>
      `);
      $('#pagination').empty();
    },
    success: function (res) {
      let count = 1;
      $('#outputTableBody').empty();

      if (res.status === 200 && res.data.length > 0) {
        res.data.forEach(data => {

           const dateObj = new Date(data.created_at);

            // Para sa word format ng date
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const created_at = dateObj.toLocaleDateString('en-US', dateOptions);

            const timeString = data.time_schedule; // example: "14:30" or "14:30:00"
            const [hour, minute] = timeString.split(':');
            const timeDate = new Date();
            timeDate.setHours(parseInt(hour), parseInt(minute));

            const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };
            const time_schedule = timeDate.toLocaleTimeString('en-US', timeOptions);



          $('#outputTableBody').append(`
            <tr class="hover:bg-[#2B2B2B] transition-colors">
              <td class="p-3 text-center font-mono">${count++}</td>
              <td class="p-3 text-center font-mono">${created_at}</td>
              
              <td class="p-3 text-center font-semibold">${data.table_code}</td>
              <td class="p-3 text-center font-semibold">${data.date_schedule}</td>
              <td class="p-3 text-center font-semibold">${time_schedule}</td>
              <td class="p-3 text-center font-semibold">${data.grand_total}</td>
             
              <td class="p-3 text-center">
                  <button
                    class="viewDetailsBtn bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded text-xs font-semibold transition"
                    data-id='${data.id}'
                    data-table_code='${data.table_code}'
                    data-seats='${data.seats}'
                    data-date_schedule='${data.date_schedule}'
                    data-time_schedule='${data.time_schedule}'
                    data-grand_total='${data.grand_total}'
                    data-selected_menus='${encodeURIComponent(data.selected_menus)}'
                    data-selected_promos='${encodeURIComponent(data.selected_promos)}'
                    data-selected_groups='${encodeURIComponent(data.selected_groups)}'
                    data-menus_details='${encodeURIComponent(JSON.stringify(data.menus_details))}'
                    data-promos_details='${encodeURIComponent(JSON.stringify(data.promos_details))}'
                    data-groups_details='${encodeURIComponent(JSON.stringify(data.groups_details))}'
                    data-proof_of_payment='${data.proof_of_payment}'
                  >
                    Details
                  </button>

                <button class="removeBtn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition"
                  data-gt_id='${data.id}'
                  data-gt_name='${data.table_code}'>Remove</button>
              </td>
            </tr>
          `);
        });

        renderPagination(res.total, limit, currentPage);

      } else {
        $('#outputTableBody').append(`
          <tr>
            <td colspan="11" class="p-4 text-center text-gray-400 italic">No record found</td>
          </tr>
        `);
        $('#pagination').empty();
      }
    }
  });

  }

  function renderPagination(totalRows, limit, currentPage) {
    let totalPages = Math.ceil(totalRows / limit);
    let paginationHTML = '';

    if (totalPages > 1) {
      paginationHTML += `
        <button class="px-3 py-1 bg-gray-700 text-white rounded ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}">Prev</button>
      `;

      for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `
          <button class="px-3 py-1 mx-1 rounded ${i === currentPage ? 'bg-yellow-400 text-black' : 'bg-gray-700 text-white'}" data-page="${i}">${i}</button>
        `;
      }

      paginationHTML += `
        <button class="px-3 py-1 bg-gray-700 text-white rounded ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">Next</button>
      `;
    }

    $('#pagination').html(paginationHTML);
  }

  $(document).on('click', '#pagination button', function () {
    let page = $(this).data('page');
    if (page) loadTable(page);
  });

  // Initial load
  loadTable();



  // Search filter
  $('#searchInput').on('input', function () {
    const term = $(this).val().toLowerCase();
    $('#outputTableBody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(term));
    });
  });

    
});




$(document).on('click', '.viewDetailsBtn', function() {
  const id = $(this).data('id');
  $("#reservation_id").val(id);

  const table_code = $(this).data('table_code');
  const seats = $(this).data('seats');
  const date_schedule = $(this).data('date_schedule');
  const time_schedule = $(this).data('time_schedule');
  const grand_total = $(this).data('grand_total');
  const proof_of_payment = $(this).data('proof_of_payment');

  function safeParse(data) {
    try { return JSON.parse(decodeURIComponent(data)); }
    catch { return []; }
  }

  const menus_details = safeParse($(this).attr('data-menus_details'));
  const promos_details = safeParse($(this).attr('data-promos_details'));
  const groups_details = safeParse($(this).attr('data-groups_details'));

  const formattedDate = new Date(date_schedule + 'T' + time_schedule).toLocaleString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
    hour12: false
  });

  function buildList(items, title) {
  if (!items.length) return '';
  return `
    <section class="mb-6 max-w-full sm:max-w-lg mx-auto px-4 sm:px-0">
      <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1">${title}</h3>
      <ul class="space-y-3 max-h-52 overflow-y-auto pr-2 scrollbar-hidden">
        ${items.map(item => `
          <li class="flex items-center space-x-4 bg-[#222222] rounded-md p-3 shadow-md hover:shadow-yellow-500 transition cursor-default">
            <img src="../static/upload/${item.details?.menu_image_banner || item.details?.deal_img_banner || ''}" alt="${item.name}" class="w-10 h-10 rounded object-cover flex-shrink-0" />
            <div class="truncate">
              <p class="text-[#CCCCCC] text-sm font-semibold leading-tight truncate">${item.name}</p>
              <p class="text-yellow-400 text-xs mt-1">₱${item.price}</p>
            </div>
          </li>
        `).join('')}
      </ul>
    </section>
  `;
}


  const menusHtml = buildList(menus_details, 'Menus');
  const promosHtml = buildList(promos_details, 'Promos');
  const groupsHtml = buildList(groups_details, 'Groups');

  const proofHtml = proof_of_payment
    ? `<section class="mb-6 max-w-full sm:max-w-md mx-auto px-4 sm:px-0">
        <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1 select-none">
          Proof of Payment
        </h3>
        <a href="../static/upload/${proof_of_payment}" download="${proof_of_payment}" title="Download Proof of Payment" 
          class="relative inline-block rounded-md overflow-hidden shadow-xl group cursor-pointer">
          <img src="../static/upload/${proof_of_payment}" alt="Proof of Payment" 
              class="max-w-full max-h-56 rounded-md object-cover transition duration-300 group-hover:opacity-80" />
           <span class="material-icons absolute inset-0 flex items-center justify-center text-yellow-400 text-6xl opacity-0 group-hover:opacity-100 transition pointer-events-none bg-black bg-opacity-40">
            download
          </span>
        </a>
      </section>`
    : '';

  $('#modalContent').html(`
    <section class="mb-10 max-w-full sm:max-w-md mx-auto px-4 sm:px-0 text-center">
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 text-[#CCCCCC] text-sm leading-relaxed">
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Table Code</dt>
          <dd>${table_code}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Seats</dt>
          <dd>${seats}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Date & Time</dt>
          <dd>${formattedDate}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Grand Total</dt>
          <dd class="text-yellow-400 font-semibold">₱${grand_total}</dd>
        </div>
      </dl>
    </section>

    ${menusHtml}
    ${promosHtml}
    ${groupsHtml}
    ${proofHtml}
  `);

  $('#detailsModal').removeClass('hidden');
});

// Close modal handlers
$('#closeModal').on('click', () => {
  $('#detailsModal').addClass('hidden');
});
$('#detailsModal').on('click', function(e) {
  if (e.target === this) {
    $(this).addClass('hidden');
  }
});





$(document).on("click", "#btnApprove, #btnCancel", function () {
  const actionStatus = $(this).data("action"); // "confirmed" or "cancelled"
  const reservationId = $("#reservation_id").val();

  const formData = new FormData();
  formData.append("requestType", "UpdateReservationStatus");
  formData.append("status", actionStatus);
  formData.append("reservation_id", reservationId);

  $.ajax({
    url: "../controller/end-points/controller.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {  // <-- change here
        Swal.fire('Success!', response.message || `Reservation ${actionStatus}.`, 'success').then(() => {
          location.reload();
        });
      } else {
        alertify.error(response.message || "Error updating info.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Update error:", error);
      alertify.error("Failed to update info. Please try again.");
    }
  });
});
