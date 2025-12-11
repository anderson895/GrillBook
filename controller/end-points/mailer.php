<?php
// Use the correct path to class.php
$classPath = __DIR__ . '/../class.php';
if (file_exists($classPath)) {
    include($classPath);
} else {
    die("Error: class.php not found at $classPath");
}

$db = new global_class();
date_default_timezone_set('Asia/Manila');

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['requestType']) && $_POST['requestType'] === 'send_broadcast_email') {
        $type = $_POST['type'] ?? '';
        $date = $_POST['date'] ?? '';
        $reason = $_POST['reason'] ?? '';
        
        if (!$type || !$date || !$reason) {
            echo json_encode(['status' => 400, 'message' => 'Missing required fields']);
            exit;
        }
        
        // Use the Mailer class from class.php
        $mailer = new Mailer();
        $result = $mailer->sendBroadcastNotificationToAll($type, $date, $reason);
        echo json_encode($result);
        exit;
    }

    if (isset($_POST['requestType']) && $_POST['requestType'] === 'reservation_status_update') {
        $reservation_id = $_POST['reservation_id'] ?? '';
        $actionStatus = $_POST['status'] ?? '';
        
        if (!$reservation_id || !$actionStatus) {
            echo json_encode(['status' => 400, 'message' => 'Missing required fields']);
            exit;
        }
        
        $fetch_reservation = $db->fetch_reservation($reservation_id);
        if (!$fetch_reservation) {
            echo json_encode(['status' => 404, 'message' => 'Reservation not found']);
            exit;
        }
        
        $data = mysqli_fetch_assoc($fetch_reservation);
        
        $date_schedule = $data['date_schedule'] ?? '';
        $time_schedule = $data['time_schedule'] ?? '';
        $table_code = $data['table_code'] ?? '';
        $Email = $data["customer_email"] ?? '';
        $Fullname = $data["customer_name"] ?? '';
        $order_code = $data["reserve_unique_code"] ?? 'GB-' . str_pad($reservation_id, 6, '0', STR_PAD_LEFT);
        
        if (!empty($date_schedule) && !empty($time_schedule)) {
            try {
                $date_scheduleTime = new DateTime("$date_schedule $time_schedule");
                $formatted = $date_scheduleTime->format('l, F j, Y - g:i A');
            } catch (Exception $e) {
                $formatted = "$date_schedule $time_schedule";
            }
        } else {
            $formatted = "Not specified";
        }
        
        // Use the sendReservationStatusNotification function from Mailer class
        $mailer = new Mailer();
        $result = $mailer->sendReservationStatusNotification($Email, $Fullname, $order_code, $formatted, $actionStatus, $table_code);
        
        echo json_encode(['status' => 200, 'success' => $result, 'message' => $result ? 'Email sent' : 'Email failed']);
        exit;
    }

    if (isset($_POST['reservations_id']) && isset($_POST['actionStatus'])) {
        $reservations_id = $_POST['reservations_id'];
        $actionStatus = $_POST['actionStatus'];

        $fetch_reservation = $db->fetch_reservation($reservations_id);
        $data = mysqli_fetch_assoc($fetch_reservation);

        $date_schedule = $data['date_schedule'];
        $time_schedule = $data['time_schedule'];
        $table_code = $data['table_code'];
        $Email = $data["customer_email"];
        $Fullname = $data["customer_name"];
        $order_code = $data["reserve_unique_code"] ?? 'GB-' . str_pad($reservations_id, 6, '0', STR_PAD_LEFT);

        $date_scheduleTime = new DateTime("$date_schedule $time_schedule");
        $formatted = $date_scheduleTime->format('l, F j, Y - g:i A');

        // Use the sendReservationStatusNotification function
        $mailer = new Mailer();
        $result = $mailer->sendReservationStatusNotification($Email, $Fullname, $order_code, $formatted, $actionStatus, $table_code);
        
        if ($result) {
            echo "Email sent successfully";
        } else {
            echo "Email sending failed";
        }
        exit;
    }
}

// Add this to handle broadcast notifications via the Mailer class
function sendBroadcastNotificationToAll($type, $date, $reason) {
    // This function is already in your Mailer class in class.php
    // Just call it from there
    $mailer = new Mailer();
    return $mailer->sendBroadcastNotificationToAll($type, $date, $reason);
}
?>