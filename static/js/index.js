$(document).ready(function () {
  $.ajax({
    url: "controller/end-points/controller.php",
    data: { requestType: "fetch_all_deals" },
    method: 'GET',
    dataType: 'json',
    success: function (response) {
      if (response.status === 200 && Array.isArray(response.data)) {
        const $menuContainer = $('#menuContainer');
        $menuContainer.empty(); // Clear existing content

        response.data.forEach(deal => {
          const dealIds = deal.deal_ids ? JSON.parse(deal.deal_ids) : [];

          const cardHtml = `
            <div class="bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition text-yellow-300">
              <img src="static/upload/${deal.deal_img_banner}" alt="${deal.deal_name}" class="rounded-lg mb-4 w-full h-48 object-cover">
              <h4 class="text-xl font-semibold">${deal.deal_name}</h4>
              <p class="mt-2">${deal.deal_description}</p>
            </div>
          `;

          $menuContainer.append(cardHtml);
        });
      } else {
        $('#menuContainer').html(`<p class="text-yellow-400">No deals found.</p>`);
      }
    },
    error: function (xhr, status, error) {
      console.error('API Error:', error);
      $('#menuContainer').html(`<p class="text-red-500">Failed to load deals.</p>`);
    }
  });
});