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
                <button class="viewDetailsBtn bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded text-xs font-semibold transition"
                  data-gt_id='${data.id}'
                  data-gt_name='${data.table_code}'>Details</button>
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