$(document).ready(function () {

  $("#frmLogin").submit(function (e) {
    e.preventDefault();

    $('#spinner').show();
    $('#btnLogin').prop('disabled', true);

    var formData = $(this).serializeArray();
    formData.push({ name: 'requestType', value: 'Login' });
    var serializedData = $.param(formData);

    $.ajax({
      type: "POST",
      url: "controller/end-points/controller.php",
      data: serializedData,
      dataType: 'json',
      success: function (response) {
        console.log(response.status);

        if (response.status === "success") {
          // ✅ Get status message and color
          const { message: statusMessage, color } = getRestaurantStatus();

          const position = response.user_position;
          const routes = {
            admin: "admin/dashboard",
            headstaff: "headstaff/dashboard",
            customer: "customer/home"
          };

          // ✅ Show Swal with colored text
          Swal.fire({
            title: 'Login Successful 🎉',
            html: `<span style="color:${color}; font-weight:600;">${statusMessage}</span>`,
            icon: 'success',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed && routes[position]) {
              window.location.href = routes[position];
            }
          });

        } else {
          $('#spinner').hide();
          $('#btnLogin').prop('disabled', false);
          Swal.fire({
            title: 'Login Failed ❌',
            text: response.message,
            icon: 'error',
            confirmButtonText: 'Try Again'
          });
        }
      },
      error: function () {
        $('#spinner').hide();
        $('#btnLogin').prop('disabled', false);
        Swal.fire({
          title: 'Error',
          text: 'An error occurred. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    });
  });

  // ✅ Helper Function to Check Restaurant Schedule
  function getRestaurantStatus() {
    const schedules = {
      0: { start: "17:00", end: "03:00" }, // Sunday
      1: { start: "17:00", end: "02:00" }, // Monday
      2: { start: "17:00", end: "02:00" }, // Tuesday
      3: { start: "17:00", end: "02:00" }, // Wednesday
      4: { start: "17:00", end: "02:00" }, // Thursday
      5: { start: "19:00", end: "04:00" }, // Friday
      6: { start: "19:00", end: "04:00" }  // Saturday
    };

    const now = new Date();
    const currentDay = now.getDay();
    const currentTime = now.getHours() * 60 + now.getMinutes();

    const schedule = schedules[currentDay];
    if (!schedule) return { message: "Closed today.", color: "red" };

    const [startH, startM] = schedule.start.split(":").map(Number);
    const [endH, endM] = schedule.end.split(":").map(Number);

    const start = startH * 60 + startM;
    let end = endH * 60 + endM;
    if (end <= start) end += 24 * 60; // handles past midnight

    const nowMinutes = currentTime < start ? currentTime + 24 * 60 : currentTime;
    const isOpen = nowMinutes >= start && nowMinutes <= end;

    const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    // ✅ Convert times to AM/PM format
    const formattedStart = formatTime(schedule.start);
    const formattedEnd = formatTime(schedule.end);

    const message = isOpen
      ? `Welcome! The restaurant is OPEN now (${days[currentDay]}: ${formattedStart} - ${formattedEnd}).`
      : `The restaurant is CLOSED now. Today's schedule is ${days[currentDay]}: ${formattedStart} - ${formattedEnd}.`;

    return { message, color: isOpen ? "green" : "red" };
  }

  // ✅ Helper: Convert 24-hour time → 12-hour with AM/PM
  function formatTime(timeStr) {
    let [hours, minutes] = timeStr.split(":").map(Number);
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12; // convert 0 → 12, 13 → 1, etc.
    return `${hours}:${minutes.toString().padStart(2, "0")} ${ampm}`;
  }

});
