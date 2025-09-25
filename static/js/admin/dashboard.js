let reservationsChart, salesChart;

const initCharts = () => {
  // Reservation Status (Pie)
  reservationsChart = new ApexCharts(document.querySelector("#reservationsChart"), {
    chart: {
      type: "donut",
      height: 300,
    },
    labels: ["Pending", "Confirmed", "Completed"],
    series: [0, 0, 0],
    colors: ["#facc15", "#22c55e", "#3b82f6"],
    legend: {
      position: "bottom",
    },
  });
  reservationsChart.render();

  // Sales Overview (Bar)
  salesChart = new ApexCharts(document.querySelector("#salesChart"), {
    chart: {
      type: "bar",
      height: 300,
    },
    series: [
      {
        name: "Sales",
        data: [0],
      },
    ],
    xaxis: {
      categories: ["Total Sales"],
    },
    colors: ["#6366f1"],
  });
  salesChart.render();
};

const getDataAnalytics = () => {
  $.ajax({
    url: "../controller/end-points/controller.php",
    method: "GET",
    data: { requestType: "dashboard_analytics" },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        const data = response.data;

        // Update stats
        $("#totalUsers").text(data.totalUsers);
        $("#totalReservations").text(data.totalReservations);
        $("#pendingReservations").text(data.pendingReservations);
        $("#confirmedReservations").text(data.confirmedReservations);
        $("#completedReservations").text(data.completedReservations);
        $("#activeMenuItems").text(data.activeMenuItems);
        $("#totalPromos").text(data.totalPromos);
        $("#totalGroupDeals").text(data.totalGroupDeals);
        $("#totalSales").text("₱" + data.totalSales);

        // Update charts
        reservationsChart.updateSeries([
          parseInt(data.pendingReservations),
          parseInt(data.confirmedReservations),
          parseInt(data.completedReservations),
        ]);

        salesChart.updateSeries([
          {
            name: "Sales",
            data: [parseFloat(data.totalSales)],
          },
        ]);
      } else {
        console.error("Analytics fetch failed:", response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", status, error);
    },
  });
};

// Initialize
initCharts();
getDataAnalytics();
setInterval(getDataAnalytics, 5000);
