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
      requestType: "fetch_all_customer_reservation",
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
    const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
    const created_at = dateObj.toLocaleDateString('en-US', dateOptions);

    const timeString = data.time_schedule;
    const [hour, minute] = timeString.split(':');
    const timeDate = new Date();
    timeDate.setHours(parseInt(hour), parseInt(minute));
    const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };
    const time_schedule = timeDate.toLocaleTimeString('en-US', timeOptions);

    const isPending = data.status.toLowerCase() === 'pending';

    // Cancel button: enabled if pending
    const cancelButton = `<button class="cancelBtn cursor-pointer bg-gray-400 text-white px-3 py-1 rounded text-xs font-semibold transition ${isPending ? '' : 'cursor-not-allowed opacity-50'}"
        data-reservation_id='${data.id}'
        ${isPending ? '' : 'disabled'}
      >
        Cancel
      </button>`;

    // Archive button: disabled if pending
    const archiveButton = `<button class="btnArchived bg-red-500 cursor-pointer hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition ${isPending ? 'cursor-not-allowed opacity-50' : ''}"
        data-reservation_id='${data.id}'
        data-action='archived'
        ${isPending ? 'disabled' : ''}
      >
        Archive
      </button>`;


    const isConfirmed = data.status.toLowerCase() === 'confirmed';

      // Reschedule button: enabled if confirmed
      const rescheduleButton = `<button class="rescheduleBtn cursor-pointer bg-green-500 hover:bg-green-600 text-black px-3 py-1 rounded text-xs font-semibold transition ${isConfirmed ? '' : 'cursor-not-allowed opacity-50'}"
          data-reservation_id='${data.id}'
          ${isConfirmed ? '' : 'disabled'}
        >
          Re-schedule
        </button>`;



    $('#outputTableBody').append(`
      <tr class="hover:bg-[#2B2B2B] transition-colors">
        <td class="p-3 text-center font-mono">${count++}</td>
        <td class="p-3 text-center font-mono">${created_at}</td>
        <td class="p-3 text-center font-semibold">${data.table_code}</td>
        <td class="p-3 text-center font-semibold">${data.date_schedule}</td>
        <td class="p-3 text-center font-semibold">${time_schedule}</td>
        <td class="p-3 text-center font-semibold">${data.grand_total}</td>
        <td class="p-3 text-center font-semibold capitalize">${data.status}</td>
        <td class="p-3 text-center">
          <button
            class="viewDetailsBtn cursor-pointer bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded text-xs font-semibold transition"
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
            data-terms_signed='${data.termsFileSigned}'
          >
            Details
          </button>

          ${archiveButton}
          ${cancelButton}
          ${rescheduleButton} <!-- added here -->
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
  const terms_signed = $(this).data('terms_signed');
  

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
              <img src="../static/upload/${item.details?.menu_image_banner || item.details?.deal_img_banner || ''}" 
                   alt="${item.name}" 
                   class="w-10 h-10 rounded object-cover flex-shrink-0" />
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

  // Build Signed Terms preview
  let termsPreviewHtml = '';
  if (terms_signed) {
    const fileExt = terms_signed.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
      // Show image preview
      termsPreviewHtml = `<img src="../static/upload/${terms_signed}" alt="Signed Terms" class="w-full max-h-56 object-cover rounded-md transition duration-300 group-hover:opacity-80" />`;
    } else {
      // Show icon for non-image
      termsPreviewHtml = `
        <div class="flex flex-col items-center justify-center h-56 w-[20rem] max-w-full text-gray-300 bg-[#222222] rounded-md">
          <span class="material-icons text-[8rem] leading-none">description</span>
          <p class="text-base mt-3">${fileExt.toUpperCase()} File</p>
        </div>

      `;

    }
  }

  const proofHtml = proof_of_payment
    ? `
    <div class="flex flex-col sm:flex-row gap-6 max-w-full mx-auto px-4 sm:px-0">
      <!-- Proof of Payment -->
      <section class="flex-1">
        <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1 select-none">
          Proof of Payment
        </h3>
        <a href="../static/upload/${proof_of_payment}" download="${proof_of_payment}" title="Download Proof of Payment" 
          class="relative inline-block rounded-md overflow-hidden shadow-xl group cursor-pointer">
          <img src="../static/upload/${proof_of_payment}" alt="Proof of Payment" 
            class="w-full max-h-56 rounded-md object-cover transition duration-300 group-hover:opacity-80" />
        <span class="material-icons absolute inset-0 flex items-center justify-center text-yellow-400 text-8xl opacity-0 group-hover:opacity-100 transition pointer-events-none">
          download
        </span>

        </a>
      </section>

      <!-- Signed Terms -->
      <section class="flex-1">
        <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1 select-none">
          Signed Terms
        </h3>
        <a href="../static/upload/${terms_signed}" download="${terms_signed}" title="Download Signed Terms" 
          class="relative inline-block rounded-md overflow-hidden shadow-xl group cursor-pointer">
          ${termsPreviewHtml}
          <span class="material-icons absolute inset-0 flex items-center justify-center text-yellow-400 text-8xl opacity-0 group-hover:opacity-100 transition pointer-events-none">
            download
          </span>
        </a>
      </section>
    </div>
    `
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






// Show confirmation modal
function confirmAction() {
    return Swal.fire({
        title: 'Are you sure?',
        text: `Archived this record.`,
        icon: 'warning',
        showCancelButton: true,
        background: '#1f2937',
        color: '#f3f4f6',
        iconColor: '#f87171',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'No, cancel'
    }).then(result => result.isConfirmed);
}





$(document).on("click", ".btnArchived", function () {
    let action = $(this).data("action")
    let actionStatus = $(this).data("action"); 
    const reservationId = $(this).data("reservation_id");

    if (actionStatus === "archived") {
        actionStatus=1
    } else if (actionStatus === "restore") {
        actionStatus = 0; 
    }

    console.log(actionStatus);

    confirmAction(action).then((confirmed) => {
        if (confirmed) {
            updateReservationStatus(reservationId, actionStatus);
        }
    });
});


function updateReservationStatus(reservationId, actionStatus) {
    const formData = new FormData();
    formData.append("requestType", "updateArchived");
    formData.append("status", actionStatus);
    formData.append("reservation_id", reservationId);
    formData.append("column", "archived_by_customer");

    // Show spinner right away
    $('#spinnerOverlay').removeClass('hidden');

    $.ajax({
        url: "../controller/end-points/controller.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                  // Show spinner right away
                location.reload();
            } else {
                $('#spinnerOverlay').addClass('hidden'); // hide spinner on error
                alertify.error(response.message || "Error updating info.");
            }
        },
        error: function () {
            $('#spinnerOverlay').addClass('hidden'); // hide spinner on error
            alertify.error("Failed to update info. Please try again.");
        }
    });
}







// Event delegation para sa dynamically generated buttons
$(document).on('click', '.cancelBtn', function() {
    const reservationId = $(this).data('reservation_id');
    const button = $(this);

    // Kung disabled, huwag gawin kahit click
    if (button.prop('disabled')) return;

    // Swal confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to cancel this reservation?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX request to cancel
            $.ajax({
                url: '../controller/end-points/controller.php',
                method: 'POST',
                data: {
                    requestType: 'cancel_reservation',
                    reservation_id: reservationId
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === "success") {
                        Swal.fire(
                            'Cancelled!',
                            'The reservation has been cancelled.',
                            'success'
                        );
                        // Optional: refresh table
                         setTimeout(() => {
                            location.reload();
                        }, 1000);


                    } else {
                        Swal.fire(
                            'Error!',
                            res.message || 'Failed to cancel reservation.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again.',
                        'error'
                    );
                }
            });
        }
    });
});














// Open modal when Reschedule button is clicked
$(document).on('click', '.rescheduleBtn', function() {
  const reservationId = $(this).data('reservation_id');
  $('#rescheduleReservationId').val(reservationId); // set hidden input
  $('#rescheduleReason').val(''); // clear previous reason
  $('#rescheduleDate').val(''); // clear previous date
  $('#rescheduleModal').fadeIn(200).css('display', 'flex'); // fade in with flex display
});

// Close modal
$('#closeRescheduleModal').click(function() {
  $('#rescheduleModal').fadeOut(200);
});

// Close modal when clicking outside the content
$('#rescheduleModal').on('click', function(e) {
  if (e.target === this) {
    $(this).fadeOut(200);
  }
});


$("#rescheduleForm").submit(function (e) {
    e.preventDefault();

    $('#spinner').show();
    $('#submitReschedule').prop('disabled', true);

    var formData = $(this).serializeArray(); 
    formData.push({ name: 'requestType', value: 'reschedule' });
    var serializedData = $.param(formData);

    $.ajax({
        type: "POST",
        url: "../controller/end-points/controller.php",
        data: serializedData,
        dataType: 'json',
        success: function (response) {
            if (response.status === "success") {
                alertify.success('Request Sent Successfully');
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                $('#spinner').hide();
                $('#submitReschedule').prop('disabled', false);
                console.log(response); 
                alertify.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            $('#spinner').hide();
            $('#submitReschedule').prop('disabled', false);
            alertify.error('An error occurred. Please try again.');
        }
    });
});
