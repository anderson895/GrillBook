$(document).ready(function () {
  $("#addBtn").click(function (e) {
    e.preventDefault();
    $("#addModal").fadeIn();
  });

  $("#closeAddModal").click(function (e) {
    e.preventDefault();
    $("#addModal").fadeOut();
  });

  // REMOVE ENTRY
  $(document).on('click', '.removeBtn', function (e) {
    e.preventDefault();
    var entry_id = $(this).data('deal_id');
    var entry_name = $(this).data('deal_name');

    Swal.fire({
      title: `Are you sure to Remove <span style="color:red;">${entry_name}</span> ?`,
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
          data: { deal_id: entry_id, requestType: 'removeEntry' },
          dataType: 'json',
          success: function (response) {
            if (response.status === 200) {
              Swal.fire('Removed!', response.message, 'success').then(() => {
                location.reload();
              });
            } else {
              Swal.fire('Error!', response.message, 'error');
            }
          },
          error: function () {
            Swal.fire('Error!', 'There was a problem with the request.', 'error');
          }
        });
      }
    });
  });

  // SUBMIT CREATE ENTRY
  $('#frmCreateEntry').on('submit', function (e) {
    e.preventDefault();

    var entryName = $('#entryName').val();
    if (entryName === "") {
      alertify.error("Please enter a name.");
      return;
    }

    var entryDescription = $('#entryDescription').val();
    if (entryDescription === "") {
      alertify.error("Please enter a description.");
      return;
    }

    var entryImage = $('#entryImage').val();
    if (entryImage === "") {
      alertify.error("Please upload an image.");
      return;
    }

    $('.spinner').show();
    $('#frmCreateEntry').prop('disabled', true);

    var formData = new FormData(this);
    formData.append('requestType', 'CreatDeals');
    formData.append('deal_type', 'promo_deals');

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
            window.location.href = 'promo_deals';
          });
        } else {
          Swal.fire('Error', response.message || 'Something went wrong.', 'error');
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", xhr.responseText);
        Swal.fire('Error', 'An unexpected error occurred.', 'error');
      }
    });
  });

  // FETCH ALL ENTRIES
  $.ajax({
    url: "../controller/end-points/controller.php",
    method: "GET",
    data: {
      requestType: "fetch_all_deals",
      deal_type: "promo_deals"
    },
    dataType: "json",
    success: function (res) {
      if (res.status === 200) {
        let count = 1;
        $('#outputBody').empty();

        if (res.data.length > 0) {
          res.data.forEach(deal => {
            $('#outputBody').append(`
              <tr class="hover:bg-[#2B2B2B] transition-colors">
                <td class="p-3 font-mono">${count++}</td>
                <td class="p-3 font-mono">${deal.deal_name}</td>
                <td class="p-3 font-semibold">${deal.deal_description}</td>
                <td class="p-3">
                  <img src="../static/upload/${deal.deal_img_banner}" alt="${deal.deal_img_banner}" class="w-12 h-12 rounded object-cover border border-gray-600">
                </td>
                <td class="p-3 text-center">
                  <a href="deal_menus?deal_id=${deal.deal_id}&&deal_name=${deal.deal_name}" 
                    class="inline-block bg-[#FFD700] hover:bg-yellow-500 text-black px-3 py-1 rounded text-xs font-semibold transition">
                    Menus
                  </a>
                  <button class="removeBtn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition"
                    data-deal_id='${deal.deal_id}'
                    data-deal_name='${deal.deal_name}'>Remove</button>
                </td>
              </tr>
            `);
          });
        } else {
          $('#outputBody').append(`
            <tr>
              <td colspan="4" class="p-4 text-center text-gray-400 italic">
                No record found
              </td>
            </tr>
          `);
        }
      }
    }
  });

  // SEARCH
  $('#searchInput').on('input', function () {
    const term = $(this).val().toLowerCase();
    $('#outputBody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().includes(term));
    });
  });
});
