$(document).ready(function () {
  const template = document.getElementById('table-template');
  const cells = document.querySelectorAll('.grid > div');

  // Render template inside each grid cell
  cells.forEach((cell) => {
    const node = template.content.cloneNode(true);
    const name = cell.textContent.trim();
    node.querySelector('.table-name').textContent = name;
    node.querySelector('.table-orders').textContent = ``;
    cell.textContent = '';
    cell.appendChild(node);
  });

  // Status → color mapping
  const statusColors = {
    "pending": { bg: "var(--color-yellow-200)", border: "var(--color-yellow-500)" },
    "confirmed": { bg: "var(--color-green-200)", border: "var(--color-green-500)" },
    "cancelled": { bg: "var(--color-red-200)", border: "var(--color-red-500)" },
    "request cancel": { bg: "var(--color-orange-200)", border: "var(--color-orange-500)" },
    "request new schedule": { bg: "var(--color-purple-200)", border: "var(--color-purple-500)" }
  };

  // Fetch and update table statuses
  function fetchReservations() {
    $.ajax({
      url: "../controller/end-points/controller.php",
      method: "GET",
      data: { requestType: "fetch_all_customer_reservation_no_limit" },
      dataType: "json",
      success: function (response) {
        if (response.status === 200 && Array.isArray(response.data)) {
          response.data.forEach(res => {
            const cell = $(`.grid [data-value='${res.table_code}']`);
            const status = (res.status || "").toLowerCase().trim();

            if (cell.length) {
              const flexEl = cell.find(".table-name").closest(".flex");
              flexEl.css({ "background-color": statusColors[status]?.bg || "", 
                           "border-color": statusColors[status]?.border || "" });

              // Attach reservation data for modal
              cell.data({
                id: res.id,
                table_code: res.table_code,
                seats: res.seats,
                date_schedule: res.date_schedule,
                time_schedule: res.time_schedule,
                grand_total: res.grand_total,
                proof_of_payment: res.proof_of_payment,
                terms_signed: res.termsFileSigned,
                status: res.status,
                menus_details: encodeURIComponent(JSON.stringify(res.menus_details || [])),
                promos_details: encodeURIComponent(JSON.stringify(res.promos_details || [])),
                groups_details: encodeURIComponent(JSON.stringify(res.groups_details || []))
              });

              // Remove setSchedule class for reserved tables
              cell.removeClass('setSchedule');
            }
          });
        }
      }
    });
  }

  fetchReservations();
  setInterval(fetchReservations, 1000);
});

// Open modal on reserved table click
$(document).on('click', '.grid > div', function () {
  const data = $(this).data();
  if (!data || !data.id) return; // skip if no reservation

  openDetailsModal(data);
});

// Details modal function
function openDetailsModal(data) {
  const formattedDate = new Date(data.date_schedule + 'T' + data.time_schedule).toLocaleString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
    hour12: false
  });

  function safeParse(json) {
    try { return JSON.parse(decodeURIComponent(json)); }
    catch { return []; }
  }

  function buildList(items, title) {
    if (!items.length) return '';
    return `
      <section class="mb-6 max-w-full sm:max-w-lg mx-auto px-4 sm:px-0">
        <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1">${title}</h3>
        <ul class="space-y-3 max-h-52 overflow-y-auto pr-2 scrollbar-hidden">
          ${items.map(item => `
            <li class="flex items-center space-x-4 bg-[#222222] rounded-md p-3 shadow-md hover:shadow-yellow-500 transition cursor-default">
              <img src="../static/upload/${item.details?.menu_image_banner || item.details?.deal_img_banner || ''}" 
                   alt="${item.name}" class="w-10 h-10 rounded object-cover flex-shrink-0" />
              <div class="truncate">
                <p class="text-[#CCCCCC] text-sm font-semibold leading-tight truncate">${item.name}</p>
                <p class="text-yellow-400 text-xs mt-1">₱${item.price}</p>
              </div>
            </li>
          `).join('')}
        </ul>
      </section>`;
  }

  const menusHtml = buildList(safeParse(data.menus_details), 'Menus');
  const promosHtml = buildList(safeParse(data.promos_details), 'Promos');
  const groupsHtml = buildList(safeParse(data.groups_details), 'Groups');

  const statusColorsText = {
    "pending": "text-yellow-400",
    "confirmed": "text-green-400",
    "cancelled": "text-red-400",
    "request cancel": "text-orange-400",
    "request new schedule": "text-purple-400"
  };
  const statusClass = statusColorsText[(data.status || "").toLowerCase()] || "text-gray-400";

  let termsPreviewHtml = '';
  if (data.terms_signed) {
    const ext = data.terms_signed.split('.').pop().toLowerCase();
    if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
      termsPreviewHtml = `<img src="../static/upload/${data.terms_signed}" alt="Signed Terms" class="w-full max-h-56 object-cover rounded-md transition duration-300 group-hover:opacity-80" />`;
    } else {
      termsPreviewHtml = `<div class="flex flex-col items-center justify-center h-56 w-[20rem] max-w-full text-gray-300 bg-[#222222] rounded-md">
        <span class="material-icons text-[8rem] leading-none">description</span>
        <p class="text-base mt-3">${ext.toUpperCase()} File</p>
      </div>`;
    }
  }

  const proofHtml = data.proof_of_payment ? `<div class="flex flex-col sm:flex-row gap-6 max-w-full mx-auto px-4 sm:px-0">
    <section class="flex-1">
      <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1 select-none">Proof of Payment</h3>
      <a href="../static/upload/${data.proof_of_payment}" download="${data.proof_of_payment}" class="relative inline-block rounded-md overflow-hidden shadow-xl group cursor-pointer">
        <img src="../static/upload/${data.proof_of_payment}" alt="Proof of Payment" class="w-full max-h-56 rounded-md object-cover transition duration-300 group-hover:opacity-80" />
        <span class="material-icons absolute inset-0 flex items-center justify-center text-yellow-400 text-8xl opacity-0 group-hover:opacity-100 transition pointer-events-none">download</span>
      </a>
    </section>
    <section class="flex-1">
      <h3 class="text-base font-semibold text-[#FFD700] mb-3 tracking-wide border-b border-gray-600 pb-1 select-none">Signed Terms</h3>
      <a href="../static/upload/${data.terms_signed}" download="${data.terms_signed}" class="relative inline-block rounded-md overflow-hidden shadow-xl group cursor-pointer">
        ${termsPreviewHtml}
        <span class="material-icons absolute inset-0 flex items-center justify-center text-yellow-400 text-8xl opacity-0 group-hover:opacity-100 transition pointer-events-none">download</span>
      </a>
    </section>
  </div>` : '';

  $('#modalContent').html(`
    <section class="mb-10 max-w-full sm:max-w-md mx-auto px-4 sm:px-0 text-center">
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 text-[#CCCCCC] text-sm leading-relaxed">
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Table Code</dt>
          <dd>${data.table_code}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Seats</dt>
          <dd>${data.seats}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Date & Time</dt>
          <dd>${formattedDate}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Grand Total</dt>
          <dd class="text-yellow-400 font-semibold">₱${data.grand_total}</dd>
        </div>
        <div class="flex flex-col items-center">
          <dt class="font-semibold mb-1">Status</dt>
          <dd class="${statusClass} font-semibold capitalize">${data.status}</dd>
        </div>
      </dl>
    </section>
    ${menusHtml}${promosHtml}${groupsHtml}${proofHtml}
  `);

  $('#detailsModal').removeClass('hidden');
}

// Close modal
$('#closeModal').on('click', () => $('#detailsModal').addClass('hidden'));
$('#detailsModal').on('click', e => { if(e.target===e.currentTarget) $('#detailsModal').addClass('hidden'); });
