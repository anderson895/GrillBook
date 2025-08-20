$(document).ready(function () {

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
                 <td class="p-3 font-semibold">${deal.deal_expiration}</td>
                <td class="p-3 text-center">
                  <a href="deal_menus?deal_id=${deal.deal_id}&&deal_name=${deal.deal_name}" 
                    class="inline-block bg-[#FFD700] hover:bg-yellow-500 text-black px-3 py-1 rounded text-xs font-semibold transition">
                    Menus
                  </a>
                  
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
