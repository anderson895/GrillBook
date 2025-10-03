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

  // 🔹 Define status → color mapping using Tailwind OKLCH variables
  const statusColors = {
    "pending": { bg: "var(--color-yellow-200)", border: "var(--color-yellow-500)" },
    "confirmed": { bg: "var(--color-green-200)", border: "var(--color-green-500)" },
    "cancelled": { bg: "var(--color-red-200)", border: "var(--color-red-500)" },
    "request cancel": { bg: "var(--color-orange-200)", border: "var(--color-orange-500)" },
    "request new schedule": { bg: "var(--color-purple-200)", border: "var(--color-purple-500)" },
  };

  // 🔹 Function to fetch and update table statuses
  function fetchReservations() {
    $.ajax({
      url: "../controller/end-points/controller.php",
      method: "GET",
      data: { requestType: "fetch_all_customer_reservation_no_limit" },
      dataType: "json",
      success: function (response) {
        if (response.status === 200 && Array.isArray(response.data)) {
          response.data.forEach(res => {
            const el = $(`.grid [data-value='${res.table_code}'] .table-name`).closest(".flex");

            const status = (res.status || "").toLowerCase().trim();

            if (el.length) {
              // Reset previous inline styles
              el.css({ "background-color": "", "border-color": "" });
              el.removeClass("ring-4 ring-black");

              // Apply colors from mapping
              if (statusColors[status]) {
                el.css({
                  "background-color": statusColors[status].bg,
                  "border-color": statusColors[status].border
                });
              }
            }
          });
        }
      }
    });
  }

  // 🔹 Initial fetch
  fetchReservations();

  // 🔹 Poll every 5 seconds (5000ms)
  setInterval(fetchReservations, 1000);
});
