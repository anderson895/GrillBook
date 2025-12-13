<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../class.php');
$db = new global_class();

$pdo_host = 'localhost';
$pdo_dbname = 'u777088444_grillbook';
$pdo_username = 'u777088444_grillbook';
$pdo_password = 'Grillbook123@';

try {
    $pdo = new PDO("mysql:host=$pdo_host;dbname=$pdo_dbname", $pdo_username, $pdo_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("PDO Connection failed: " . $e->getMessage());
    $pdo = null;
}

function createSystemTables($pdo) {
    $tables = [
        'system_status' => "
            CREATE TABLE IF NOT EXISTS system_status (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status_key VARCHAR(100) NOT NULL UNIQUE,
                status_value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_status_key (status_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",
        'emergency_closures' => "
            CREATE TABLE IF NOT EXISTS emergency_closures (
                id INT AUTO_INCREMENT PRIMARY KEY,
                closure_date DATE NOT NULL,
                reason TEXT NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                restored_at DATETIME NULL,
                INDEX idx_closure_date (closure_date),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        "
    ];
    
    foreach ($tables as $tableName => $createSQL) {
        try {
            $checkTable = $pdo->query("SHOW TABLES LIKE '$tableName'");
            if ($checkTable->rowCount() === 0) {
                $pdo->exec($createSQL);
                error_log("Created table: $tableName");
            }
        } catch (PDOException $e) {
            error_log("Error creating table $tableName: " . $e->getMessage());
        }
    }
}

function getMenuImagePath($imageName) {
    if (empty($imageName)) {
        return null;
    }
    
    $possiblePaths = [
        __DIR__ . '/../../static/upload/menu/' . $imageName,
        __DIR__ . '/../static/upload/menu/' . $imageName,
        __DIR__ . '/static/upload/menu/' . $imageName,
        'static/upload/menu/' . $imageName,
        '../static/upload/menu/' . $imageName,
        '../../static/upload/menu/' . $imageName
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            if (strpos($path, __DIR__) === 0) {
                $relativePath = str_replace(__DIR__ . '/../', '../', $path);
                return $relativePath;
            }
            return $path;
        }
    }
    
    return null;
}

function validateRequiredFields($fields, $data) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    return $missing;
}

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

function sendSuccess($data = []) {
    echo json_encode(array_merge(['status' => 'success'], $data));
    exit;
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function checkTableAvailability($db, $table_code, $date_schedule, $time_schedule) {
    try {
        $sql = "SELECT COUNT(*) as count FROM reservations 
                WHERE table_code = ? 
                AND date_schedule = ? 
                AND time_schedule = ? 
                AND status IN ('pending', 'confirmed', 'request_reschedule')";
        
        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("sss", $table_code, $date_schedule, $time_schedule);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] == 0;
    } catch (Exception $e) {
        error_log("Availability check error: " . $e->getMessage());
        return false;
    }
}

function sendNotification($pdo, $title, $message, $type = 'system') {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$title, $message, $type]);
        return true;
    } catch (PDOException $e) {
        error_log("Failed to send notification: " . $e->getMessage());
        return false;
    }
}

function isEmergencyModeActive($pdo) {
    try {
        $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM emergency_closures WHERE status = 'active' AND closure_date >= CURDATE()");
        $stmt1->execute();
        $hasActiveClosure = $stmt1->fetchColumn() > 0;
        
        $stmt2 = $pdo->prepare("SELECT status_value FROM system_status WHERE status_key = 'emergency_mode'");
        $stmt2->execute();
        $systemStatus = $stmt2->fetch(PDO::FETCH_ASSOC);
        $isSystemEmergency = $systemStatus && $systemStatus['status_value'] === 'true';
        
        return $hasActiveClosure || $isSystemEmergency;
    } catch (PDOException $e) {
        error_log("Failed to check emergency mode: " . $e->getMessage());
        return false;
    }
}

function getUnreadNotificationCount($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        error_log("Failed to get unread count: " . $e->getMessage());
        return 0;
    }
}

function checkEmergencyStatus($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM emergency_closures WHERE status = 'active' AND closure_date >= CURDATE() LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkSystemStatus($pdo) {
    $stmt = $pdo->prepare("SELECT status_value FROM system_status WHERE status_key = 'emergency_mode'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['status_value'] === 'true';
}

$statusCounts = [
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'request_cancel' => 0,
    'request_reschedule' => 0
];

if (isset($_GET['requestType']) && $_GET['requestType'] === "checkAvailability") {
    $table_code = $_GET['table_code'];
    $date_schedule = $_GET['date_schedule'];
    $time_schedule = $_GET['time_schedule'];
    
    $checkStmt = $pdo->prepare("SELECT * FROM reservations WHERE table_code = ? AND date_schedule = ? AND time_schedule = ? AND status IN ('pending', 'confirmed')");
    $checkStmt->execute([$table_code, $date_schedule, $time_schedule]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['availability' => false]);
    } else {
        echo json_encode(['availability' => true]);
    }
    exit();
}

if (isset($_GET['requestType']) && $_GET['requestType'] === "get_realtime_table_status") {
    $date = $_GET['date'] ?? date('Y-m-d');
    $tableStatusMap = [];
    
    $walkinStmt = $pdo->prepare("SELECT walkin_table_code, walkin_status FROM walkin_tables WHERE DATE(walkin_created_at) = ?");
    $walkinStmt->execute([$date]);
    $walkinTables = $walkinStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($walkinTables as $table) {
        $tableStatusMap[$table['walkin_table_code']] = $table['walkin_status'];
    }
    
    $reservationStmt = $pdo->prepare("
        SELECT table_code, status 
        FROM reservations 
        WHERE date_schedule = ?
        AND status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule', 'walkin', 'completed', 'unavailable')
        AND table_code IS NOT NULL
        AND table_code != ''
    ");
    $reservationStmt->execute([$date]);
    $reservations = $reservationStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($reservations as $reservation) {
        $tableCode = $reservation['table_code'];
        $reservationStatus = $reservation['status'];
        $tableStatusMap[$tableCode] = $reservationStatus;
    }
    
    echo json_encode(['status' => 'success', 'data' => $tableStatusMap]);
    exit();
}

if (isset($_POST['send_holiday_notice'])) {
    // Ensure we return strict JSON even if libraries emit warnings/HTML
    header('Content-Type: application/json; charset=utf-8');
    ob_start();
    $date = $_POST['holiday_date'] ?? '';
    $reason = $_POST['holiday_reason'] ?? '';
    $holidayType = $_POST['holiday_type'] ?? '';
    $lateOpeningTime = isset($_POST['late_opening_time']) ? $_POST['late_opening_time'] : null;
    $lateClosingTime = isset($_POST['late_closing_time']) ? $_POST['late_closing_time'] : null;

    try {
        $result = $db->sendHolidayNotice($date, $reason, $holidayType, $lateOpeningTime, $lateClosingTime);
        // discard any accidental output (warnings, HTML) generated by libraries
        ob_end_clean();
        echo json_encode($result);
    } catch (Throwable $e) {
        // collect and discard buffer, then return a safe error JSON
        ob_end_clean();
        $errorMsg = $e->getMessage();
        error_log('send_holiday_notice error: ' . $errorMsg . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
        error_log('Trace: ' . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $errorMsg]);
    }
    exit;
}

if (isset($_POST['send_broadcast_email'])) {
    $type = $_POST['broadcast_type'];
    $date = $_POST['broadcast_date'];
    $reason = $_POST['broadcast_reason'];
    
    $result = $db->sendBroadcastEmail($type, $date, $reason);
    echo json_encode($result);
    exit;
}

if (isset($_POST['update_reservation_status'])) {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['status'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;
    
    $result = $db->UpdateReservationStatus($reservation_id, $status, $reason);
    echo json_encode($result);
    exit;
}

if (isset($_POST['approve_reschedule'])) {
    $reservation_id = $_POST['reservation_id'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;
    
    $result = $db->ApproveReschedule($reservation_id, $reason);
    echo json_encode($result);
    exit;
}

if (isset($_POST['cancel_reservation_client'])) {
    $reservation_id = $_POST['reservation_id'];
    $reason = $_POST['reason'];
    
    $result = $db->cancel_reservation($reservation_id, $reason);
    echo json_encode($result);
    exit;
}

if (isset($_POST['reschedule_reservation'])) {
    $reservation_id = $_POST['reservation_id'];
    $reason = $_POST['reason'];
    $newDate = $_POST['new_date'];
    $newTime = $_POST['new_time'];
    $seats = $_POST['seats'];
    
    $result = $db->reschedule($reservation_id, $reason, $seats, $newDate, $newTime);
    echo json_encode($result);
    exit;
}

if (isset($_GET['get_holiday_closures'])) {
    $holidays = $db->getActiveHolidays();
    $closures = $db->getActiveClosures();
    
    echo json_encode([
        'holidays' => $holidays,
        'closures' => $closures
    ]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['requestType'])) {
            sendError('No request type specified.');
        }

        $requestType = $_POST['requestType'];

        switch ($requestType) {
            case "get_notifications":
                try {
                    $checkTable = $pdo->query("SHOW TABLES LIKE 'notifications'");
                    if ($checkTable->rowCount() > 0) {
                        $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
                        $stmt->execute();
                        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        echo json_encode([
                            'success' => true,
                            'data' => $notifications
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'data' => []
                        ]);
                    }
                } catch (PDOException $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to fetch notifications: ' . $e->getMessage()
                    ]);
                }
                break;

            case "mark_notifications_read":
                try {
                    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
                    $stmt->execute();
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'All notifications marked as read'
                    ]);
                } catch (PDOException $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
                    ]);
                }
                break;

            case "restore_system_access":
                try {
                    $pdo->beginTransaction();
                    
                    $stmt1 = $pdo->prepare("UPDATE system_status SET status_value = 'false', updated_at = NOW() WHERE status_key = 'emergency_mode'");
                    $stmt1->execute();
                    
                    if ($stmt1->rowCount() === 0) {
                        $insertStmt = $pdo->prepare("INSERT INTO system_status (status_key, status_value) VALUES ('emergency_mode', 'false')");
                        $insertStmt->execute();
                    }
                    
                    $stmt2 = $pdo->prepare("UPDATE emergency_closures SET status = 'inactive', restored_at = NOW() WHERE status = 'active'");
                    $stmt2->execute();
                    
                    $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read) VALUES (?, ?, ?, 0)");
                    $notificationStmt->execute([
                        '✅ SYSTEM RESTORED ✅',
                        'Emergency system shutdown has been lifted. The system is now active and ready for reservations.',
                        'system'
                    ]);
                    
                    $pdo->commit();
                    
                    echo json_encode([
                        'success' => true,
                        'message' => '✅ System access restored successfully! The system is now active and ready for reservations.',
                        'redirect' => 'dashboard.php?restored=true&timestamp=' . time()
                    ]);
                    
                } catch (PDOException $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    error_log("Failed to restore system access: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to restore system access: Database error occurred'
                    ]);
                } catch (Exception $e) {
                    error_log("Restore system access error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to restore system access: System error occurred'
                    ]);
                }
                break;

            case "dashboard_analytics":
                try {
                    $data = [];
                    
                    $stmt1 = $pdo->prepare("SELECT COUNT(*) as total FROM reservations WHERE DATE(date_schedule) = CURDATE()");
                    $stmt1->execute();
                    $data['total_reservations'] = $stmt1->fetchColumn() ?: 0;
                    
                    $stmt2 = $pdo->prepare("SELECT COUNT(*) as total FROM reservations WHERE status = 'pending' AND DATE(date_schedule) >= CURDATE()");
                    $stmt2->execute();
                    $data['pending_reservations'] = $stmt2->fetchColumn() ?: 0;
                    
                    $stmt3 = $pdo->prepare("SELECT COUNT(*) as total FROM reservations WHERE status = 'confirmed' AND DATE(date_schedule) >= CURDATE()");
                    $stmt3->execute();
                    $data['confirmed_reservations'] = $stmt3->fetchColumn() ?: 0;
                    
                    $stmt4 = $pdo->prepare("SELECT COUNT(*) as total FROM reservations WHERE status = 'completed' AND DATE(date_schedule) >= CURDATE()");
                    $stmt4->execute();
                    $data['completed_reservations'] = $stmt4->fetchColumn() ?: 0;
                    
                    $stmt5 = $pdo->prepare("SELECT COALESCE(SUM(grand_total), 0) as total FROM reservations WHERE status IN ('confirmed', 'completed') AND DATE(date_schedule) >= CURDATE()");
                    $stmt5->execute();
                    $data['total_sales'] = $stmt5->fetchColumn() ?: 0;
                    
                    $stmt6 = $pdo->prepare("SELECT COUNT(*) as total FROM emergency_closures WHERE status = 'active' AND closure_date >= CURDATE()");
                    $stmt6->execute();
                    $data['emergency_active'] = $stmt6->fetchColumn() > 0;
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $data
                    ]);
                } catch (PDOException $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to fetch dashboard analytics: ' . $e->getMessage()
                    ]);
                }
                break;

            case "update_table_status":
                try {
                    $table_code = $_POST['table_code'] ?? '';
                    $status = $_POST['status'] ?? '';
                    $date = $_POST['date'] ?? date('Y-m-d');
                    
                    if (empty($table_code)) {
                        echo json_encode(['success' => false, 'message' => 'Table code is required']);
                        exit;
                    }
                    
                    $responseMessage = '';
                    
                    if ($status === 'walkin') {
                        $checkSql = "SELECT id FROM reservations 
                                    WHERE table_code = :table_code 
                                    AND date_schedule = :date 
                                    AND status IN ('pending', 'confirmed', 'walkin')";
                        
                        $checkStmt = $pdo->prepare($checkSql);
                        $checkStmt->execute([':table_code' => $table_code, ':date' => $date]);
                        $existingReservation = $checkStmt->fetch();
                        
                        if ($existingReservation) {
                            echo json_encode(['success' => false, 'message' => 'Table already has a reservation for this date']);
                            exit;
                        }
                        
                        $checkWalkinSql = "SELECT walkin_id FROM walkin_tables 
                                          WHERE walkin_table_code = :table_code 
                                          AND DATE(walkin_created_at) = :date 
                                          AND walkin_status = 'active'";
                        
                        $checkWalkinStmt = $pdo->prepare($checkWalkinSql);
                        $checkWalkinStmt->execute([':table_code' => $table_code, ':date' => $date]);
                        $existingWalkin = $checkWalkinStmt->fetch();
                        
                        if ($existingWalkin) {
                            echo json_encode(['success' => false, 'message' => 'Walk-in already exists for this table']);
                            exit;
                        }
                        
                        $insertSql = "INSERT INTO reservations 
                                     (table_code, customer_name, customer_email, customer_phone, seats, 
                                      date_schedule, time_schedule, grand_total, status, created_at) 
                                     VALUES (:table_code, 'Walk-in Customer', 'walkin@example.com', 
                                             '0000000000', 2, :date, CURTIME(), 0, 'walkin', NOW())";
                        
                        $insertStmt = $pdo->prepare($insertSql);
                        $insertStmt->execute([
                            ':table_code' => $table_code,
                            ':date' => $date
                        ]);
                        
                        $responseMessage = 'Walk-in added successfully';
                        
                    } elseif ($status === 'available') {
                        $updateSql = "UPDATE reservations 
                                     SET status = 'cancelled' 
                                     WHERE table_code = :table_code 
                                     AND date_schedule = :date 
                                     AND status IN ('pending', 'confirmed', 'walkin')";
                        
                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->execute([':table_code' => $table_code, ':date' => $date]);
                        
                        $updateWalkinSql = "UPDATE walkin_tables 
                                           SET walkin_status = 'completed' 
                                           WHERE walkin_table_code = :table_code 
                                           AND DATE(walkin_created_at) = :date";
                        
                        $updateWalkinStmt = $pdo->prepare($updateWalkinSql);
                        $updateWalkinStmt->execute([':table_code' => $table_code, ':date' => $date]);
                        
                        $responseMessage = 'Table marked as available';
                        
                    } elseif ($status === 'unavailable') {
                        $checkSql = "SELECT id FROM reservations 
                                    WHERE table_code = :table_code 
                                    AND date_schedule = :date 
                                    AND status IN ('pending', 'confirmed', 'walkin')";
                        
                        $checkStmt = $pdo->prepare($checkSql);
                        $checkStmt->execute([':table_code' => $table_code, ':date' => $date]);
                        $existingReservation = $checkStmt->fetch();
                        
                        if ($existingReservation) {
                            echo json_encode(['success' => false, 'message' => 'Cannot mark as unavailable - table has existing reservation']);
                            exit;
                        }
                        
                        $insertSql = "INSERT INTO reservations 
                                     (table_code, customer_name, customer_email, customer_phone, seats, 
                                      date_schedule, time_schedule, grand_total, status, created_at) 
                                     VALUES (:table_code, 'Unavailable', 'unavailable@example.com', 
                                             '0000000000', 0, :date, CURTIME(), 0, 'unavailable', NOW())
                                     ON DUPLICATE KEY UPDATE status = 'unavailable'";
                        
                        $insertStmt = $pdo->prepare($insertSql);
                        $insertStmt->execute([
                            ':table_code' => $table_code,
                            ':date' => $date
                        ]);
                        
                        $responseMessage = 'Table marked as unavailable';
                        
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Invalid status']);
                        exit;
                    }
                    
                    echo json_encode(['success' => true, 'message' => $responseMessage, 'status' => 'success']);
                    
                } catch (PDOException $e) {
                    error_log("Update table status error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Failed to update table status', 
                        'error' => $e->getMessage()
                    ]);
                }
                break;

            case "send_holiday_notice":
            try {
                $date = $_POST['date'] ?? '';
                $reason = $_POST['reason'] ?? '';
                $holiday_type = $_POST['holiday_type'] ?? 'closure';
                $late_opening_time = $_POST['late_opening_time'] ?? null;
                
                if (empty($date) || empty($reason)) {
                    echo json_encode(['success' => false, 'message' => 'Date and reason are required']);
                    break;
                }
                
                // FIXED LINE - Changed 'holidays' to 'holiday_schedules'
                $stmt = $pdo->prepare("INSERT INTO holiday_schedules (holiday_date, holiday_name, holiday_type, late_opening_time, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'active', NOW(), NOW())");
                $stmt->execute([$date, $reason, $holiday_type, $late_opening_time]);
                
                $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read) VALUES (?, ?, ?, 0)");
                $notificationStmt->execute([
                    'Holiday Notice',
                    "Holiday on {$date}: {$reason}",
                    'alert'
                ]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Holiday notice sent successfully'
                ]);
            } catch (PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send holiday notice: ' . $e->getMessage()
                ]);
            }
            break;

            case "send_emergency_closure":
                try {
                    $date = $_POST['date'] ?? '';
                    $reason = $_POST['reason'] ?? '';
                    $action = $_POST['action'] ?? 'activate';
                    
                    if (empty($date) || empty($reason)) {
                        echo json_encode(['success' => false, 'message' => 'Date and reason are required']);
                        break;
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO emergency_closures (closure_date, reason, status, created_at) 
                                           VALUES (?, ?, 'active', NOW()) 
                                           ON DUPLICATE KEY UPDATE 
                                           reason = VALUES(reason), 
                                           status = VALUES(status),
                                           updated_at = NOW()");
                    $stmt->execute([$date, $reason]);
                    
                    $stmt = $pdo->prepare("INSERT INTO system_status (status_key, status_value, updated_at) 
                                           VALUES ('emergency_mode', 'true', NOW()) 
                                           ON DUPLICATE KEY UPDATE 
                                           status_value = VALUES(status_value),
                                           updated_at = NOW()");
                    $stmt->execute();
                    
                    $pdo->beginTransaction();
                    
                    $cancelStmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled', cancellation_reason = 'Emergency Closure: {$reason}' WHERE date_schedule = ? AND status IN ('pending', 'confirmed')");
                    $cancelStmt->execute([$date]);
                    
                    $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read) VALUES (?, ?, ?, 0)");
                    $notificationStmt->execute([
                        'Emergency Closure',
                        "Emergency closure on {$date}: {$reason}. All reservations cancelled.",
                        'alert'
                    ]);
                    
                    $pdo->commit();

                    // Send emergency email notifications to all users
                    try {
                        $sendResult = $db->sendEmergencyNotice($date, $reason);
                        if (isset($sendResult['success']) && $sendResult['success']) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Emergency closure activated successfully. ' . ($sendResult['message'] ?? '')
                            ]);
                        } else {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Emergency closure activated, but failed to send emails: ' . ($sendResult['message'] ?? 'Unknown error')
                            ]);
                        }
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Emergency closure activated, but error sending emails: ' . $e->getMessage()
                        ]);
                    }
                } catch (PDOException $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to send emergency closure: ' . $e->getMessage()
                    ]);
                }
                break;

            case "fetch_reservations_for_tables":
                $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
                
                try {
                    $reservationSql = "
                        SELECT table_code, status 
                        FROM reservations 
                        WHERE date_schedule = ?
                        AND status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule', 'walkin', 'completed', 'unavailable')
                        AND table_code IS NOT NULL
                        AND table_code != ''
                    ";
                    
                    $reservationStmt = $pdo->prepare($reservationSql);
                    $reservationStmt->execute([$date]);
                    $reservations = $reservationStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $walkinSql = "
                        SELECT walkin_table_code, 'walkin' as status 
                        FROM walkin_tables 
                        WHERE walkin_date = ? OR DATE(walkin_created_at) = ?
                        AND walkin_status = 'active'
                    ";
                    
                    $walkinStmt = $pdo->prepare($walkinSql);
                    $walkinStmt->execute([$date, $date]);
                    $walkins = $walkinStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $allTables = array_merge($reservations, $walkins);
                    
                    $tableStatusMap = [];
                    foreach ($allTables as $item) {
                        if (!empty($item['table_code'])) {
                            $tableStatusMap[$item['table_code']] = $item['status'];
                        }
                    }
                    
                    $response = [];
                    foreach ($tableStatusMap as $tableCode => $status) {
                        $response[] = [
                            'table_code' => $tableCode,
                            'status' => $status
                        ];
                    }
                    
                    echo json_encode([
                        'status' => 'success',
                        'success' => true,
                        'data' => $response
                    ]);
                    
                } catch (PDOException $e) {
                    error_log("Error in fetch_reservations_for_tables: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                }
                break;

            case "fetch_alcoholic_drinks":
                try {
                    $stmt = $pdo->prepare("SELECT * FROM alcoholic_drinks WHERE is_available = 1 ORDER BY drink_name");
                    $stmt->execute();
                    $drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo json_encode([
                        'status' => 'success',
                        'data' => $drinks
                    ]);
                } catch (PDOException $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error fetching drinks: ' . $e->getMessage()
                    ]);
                }
                break;

            case "add_walkin_customer":
                $required = ['table_code', 'customer_name', 'customer_phone', 'seats'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }
                
                $table_code = $_POST['table_code'];
                $customer_name = $_POST['customer_name'];
                $customer_phone = $_POST['customer_phone'];
                $seats = $_POST['seats'];
                $special_requests = $_POST['special_requests'] ?? '';
                $date = $_POST['date'] ?? date('Y-m-d');
                
                try {
                    $checkTableSQL = "SHOW TABLES LIKE 'walkin_tables'";
                    $tableCheck = $db->conn->query($checkTableSQL);
                    
                    if ($tableCheck->num_rows === 0) {
                        $createTableSQL = "
                            CREATE TABLE walkin_tables (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                walkin_table_code VARCHAR(50) NOT NULL,
                                walkin_status VARCHAR(50) NOT NULL,
                                walkin_customer_name VARCHAR(255),
                                walkin_customer_phone VARCHAR(20),
                                walkin_seats INT DEFAULT 1,
                                walkin_notes TEXT,
                                walkin_date DATE,
                                walkin_created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                walkin_updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                UNIQUE KEY unique_table_date (walkin_table_code, walkin_date),
                                INDEX idx_table_code (walkin_table_code),
                                INDEX idx_status (walkin_status),
                                INDEX idx_date (walkin_date)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ";
                        $db->conn->query($createTableSQL);
                    }
                    
                    $sql = "
                        INSERT INTO walkin_tables (
                            walkin_table_code, 
                            walkin_status, 
                            walkin_customer_name, 
                            walkin_customer_phone, 
                            walkin_seats, 
                            walkin_notes, 
                            walkin_date,
                            walkin_created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                            walkin_status = VALUES(walkin_status),
                            walkin_customer_name = VALUES(walkin_customer_name),
                            walkin_customer_phone = VALUES(walkin_customer_phone),
                            walkin_seats = VALUES(walkin_seats),
                            walkin_notes = VALUES(walkin_notes),
                            walkin_updated_at = NOW()
                    ";
                    
                    $stmt = $db->conn->prepare($sql);
                    $status = 'walkin';
                    $stmt->bind_param(
                        "ssssiss", 
                        $table_code, 
                        $status, 
                        $customer_name, 
                        $customer_phone, 
                        $seats, 
                        $special_requests,
                        $date
                    );
                    
                    if ($stmt->execute()) {
                        if ($pdo) {
                            $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, table_code, created_at) VALUES (?, ?, ?, ?, NOW())");
                            $notificationStmt->execute([
                                "Walk-in Customer Added",
                                "Customer {$customer_name} ({$customer_phone}) with {$seats} seat(s) added to table {$table_code}",
                                "system",
                                $table_code
                            ]);
                        }
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Walk-in customer added successfully',
                            'status' => 'success'
                        ]);
                    } else {
                        sendError('Failed to save walk-in customer: ' . $db->conn->error);
                    }
                    
                } catch (Exception $e) {
                    error_log("Add walkin customer error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage());
                }
                break;

            case "create_reservation":
                $required = ['table_code', 'customer_name', 'customer_email', 'customer_phone', 
                             'seats', 'date_schedule', 'time_schedule', 'payment_method', 'payment_type'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }
                
                $table_code = $_POST['table_code'];
                $customer_name = $_POST['customer_name'];
                $customer_email = $_POST['customer_email'];
                $customer_phone = $_POST['customer_phone'];
                $seats = $_POST['seats'];
                $date_schedule = $_POST['date_schedule'];
                $time_schedule = $_POST['time_schedule'];
                $payment_method = $_POST['payment_method'];
                $payment_type = $_POST['payment_type'];
                $special_requests = $_POST['special_requests'] ?? '';
                
                $reserve_unique_code = 'GB-' . strtoupper(uniqid());
                
                try {
                    $sql = "INSERT INTO reservations (
                                table_code, 
                                customer_name, 
                                customer_email, 
                                customer_phone, 
                                seats, 
                                date_schedule, 
                                time_schedule, 
                                payment_method,
                                payment_type,
                                special_requests,
                                reserve_unique_code,
                                status,
                                created_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW())";
                    
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param(
                        "sssssssssss",
                        $table_code,
                        $customer_name,
                        $customer_email,
                        $customer_phone,
                        $seats,
                        $date_schedule,
                        $time_schedule,
                        $payment_method,
                        $payment_type,
                        $special_requests,
                        $reserve_unique_code
                    );
                    
                    if ($stmt->execute()) {
                        $reservation_id = $db->conn->insert_id;
                        
                        require_once '../../vendor/autoload.php';
                        $mailer = new Mailer();
                        $date_scheduleTime = new DateTime("$date_schedule $time_schedule");
                        $formatted = $date_scheduleTime->format('l, F j, Y - g:i A');
                        $mailer->sendAccountNotification($customer_email, $customer_name, $reserve_unique_code, $formatted, 'confirmed', $table_code);
                        
                        if ($pdo) {
                            $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                            $notificationStmt->execute([
                                "New Reservation Created",
                                "Reservation #{$reservation_id} ({$reserve_unique_code}) for {$customer_name} at table {$table_code} on {$formatted}",
                                "reservation",
                                $reservation_id,
                                $table_code
                            ]);
                        }
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Reservation created successfully',
                            'reservation_id' => $reservation_id,
                            'reference_code' => $reserve_unique_code
                        ]);
                    } else {
                        sendError('Failed to create reservation: ' . $db->conn->error);
                    }
                    
                } catch (Exception $e) {
                    error_log("Create reservation error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage());
                }
                break;

            case "send_reservation_status_email":
                $required = ['reservation_id', 'status'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                
                $reservation = $db->getReservationById($reservation_id);
                
                if (!$reservation) {
                    sendError('Reservation not found');
                }
                
                require_once '../../vendor/autoload.php';
                $mailer = new Mailer();
                
                $date_schedule = $reservation['date_schedule'] ?? '';
                $time_schedule = $reservation['time_schedule'] ?? '';
                $table_code = $reservation['table_code'] ?? '';
                $Email = $reservation["customer_email"] ?? '';
                $Fullname = $reservation["customer_name"] ?? '';
                $order_code = $reservation["reserve_unique_code"] ?? 'GB-' . str_pad($reservation_id, 6, '0', STR_PAD_LEFT);
                
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
                
                $result = $mailer->sendReservationStatusNotification($Email, $Fullname, $order_code, $formatted, $status, $table_code);
                
                if ($result) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "Reservation Status Updated",
                            "Reservation #{$reservation_id} ({$order_code}) status changed to {$status}. Email sent to customer.",
                            "reservation",
                            $reservation_id,
                            $table_code
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Email notification sent successfully']);
                } else {
                    sendError('Failed to send email notification');
                }
                break;

            case "check_system_status":
                try {
                    $checkTableSQL = "SHOW TABLES LIKE 'system_status'";
                    $tableCheck = $pdo->query($checkTableSQL);
                    
                    $emergency_mode = false;
                    
                    if ($tableCheck->rowCount() > 0) {
                        $statusStmt = $pdo->prepare("SELECT status_value FROM system_status WHERE status_key = 'emergency_mode'");
                        $statusStmt->execute();
                        $status = $statusStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($status && $status['status_value'] === 'true') {
                            $emergency_mode = true;
                        }
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'emergency_mode' => $emergency_mode
                    ]);
                    
                } catch (Exception $e) {
                    error_log("Error checking system status: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'emergency_mode' => false,
                        'message' => 'Error checking system status'
                    ]);
                }
                break;

            case "fetch_table_reservation_details":
                $table_code = sanitizeInput($_POST['table_code']);
                $date = sanitizeInput($_POST['date']);
                
                try {
                    $stmt = $pdo->prepare("
                        SELECT * FROM reservations 
                        WHERE table_code = ?
                        AND date_schedule = ?
                        AND status IN ('pending', 'confirmed', 'request_reschedule', 'request_cancel', 'completed')
                        ORDER BY created_at DESC
                        LIMIT 1
                    ");
                    $stmt->execute([$table_code, $date]);
                    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($reservation) {
                        $response = [
                            'success' => true,
                            'data' => $reservation
                        ];
                    } else {
                        $walkinStmt = $pdo->prepare("
                            SELECT * FROM walkin_tables 
                            WHERE walkin_table_code = ? 
                            AND DATE(walkin_created_at) = ?
                            LIMIT 1
                        ");
                        $walkinStmt->execute([$table_code, $date]);
                        $walkin = $walkinStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($walkin) {
                            $response = [
                                'success' => true,
                                'data' => [
                                    'table_code' => $walkin['walkin_table_code'],
                                    'customer_name' => $walkin['walkin_customer_name'],
                                    'customer_email' => $walkin['walkin_customer_phone'] ? $walkin['walkin_customer_phone'] . ' (Walk-in)' : 'Walk-in Customer',
                                    'seats' => $walkin['walkin_seats'],
                                    'status' => 'walkin',
                                    'created_at' => $walkin['walkin_created_at'],
                                    'special_requests' => $walkin['walkin_special_requests']
                                ]
                            ];
                        } else {
                            $response = [
                                'success' => true,
                                'data' => null
                            ];
                        }
                    }
                    
                    echo json_encode($response);
                } catch (PDOException $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Database error: ' . $e->getMessage()
                    ];
                    echo json_encode($response);
                }
                break;

            case "create_notification":
                if (!$pdo) {
                    sendError('Database connection failed');
                }
                
                $title = $_POST['title'] ?? '';
                $message = $_POST['message'] ?? '';
                $type = $_POST['type'] ?? 'system';
                $reservation_id = $_POST['reservation_id'] ?? null;
                $table_code = $_POST['table_code'] ?? null;
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$title, $message, $type, $reservation_id, $table_code]);
                    
                    echo json_encode(['success' => true, 'message' => 'Notification created']);
                } catch (Exception $e) {
                    error_log("Create notification error: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Error creating notification']);
                }
                break;

            case "fetch_notifications":
                if (!$pdo) {
                    echo json_encode(['success' => false, 'data' => [], 'message' => 'Database connection failed']);
                    break;
                }
                
                try {
                    $tableCheck = $pdo->query("SHOW TABLES LIKE 'notifications'");
                    if ($tableCheck->rowCount() === 0) {
                        $createTableSQL = "
                            CREATE TABLE notifications (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                title VARCHAR(255) NOT NULL,
                                message TEXT NOT NULL,
                                type ENUM('reservation', 'payment', 'system', 'alert') DEFAULT 'system',
                                reservation_id INT NULL,
                                table_code VARCHAR(50) NULL,
                                is_read TINYINT(1) DEFAULT 0,
                                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                INDEX idx_reservation (reservation_id),
                                INDEX idx_table (table_code),
                                INDEX idx_read (is_read),
                                INDEX idx_created (created_at)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                        ";
                        $pdo->exec($createTableSQL);
                        
                        echo json_encode(['success' => true, 'data' => []]);
                        break;
                    }
                    
                    $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 50");
                    $stmt->execute();
                    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $notifications
                    ]);
                } catch (Exception $e) {
                    error_log("Notifications error: " . $e->getMessage());
                    echo json_encode(['success' => false, 'data' => [], 'message' => 'Error fetching notifications']);
                }
                break;

            case "mark_notification_read":
                if (!$pdo) {
                    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
                    break;
                }
                
                $notificationId = $_POST['notification_id'] ?? null;
                
                if (!$notificationId) {
                    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
                    break;
                }
                
                try {
                    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$notificationId]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Notification marked as read'
                    ]);
                } catch (Exception $e) {
                    error_log("Mark notification read error: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Error marking notification as read']);
                }
                break;

            case "send_broadcast_email":
                require_once '../../vendor/autoload.php';
                require_once('../class.php');
                $db = new global_class();
                
                $type = $_POST['type'] ?? '';
                $date = $_POST['date'] ?? '';
                $reason = $_POST['reason'] ?? '';
                
                if (!$type || !$date || !$reason) {
                    sendError('Missing required fields');
                }
                
                $sql = "SELECT user_id, user_fname, user_lname, user_email FROM user WHERE user_status = '1'";
                $stmt = $db->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $users = [];
                while ($row = $result->fetch_assoc()) {
                    $users[] = [
                        'email' => $row['user_email'],
                        'user_fname' => $row['user_fname'],
                        'user_lname' => $row['user_lname']
                    ];
                }
                
                $mailer = new Mailer();
                $successCount = 0;
                $failCount = 0;
                $failedEmails = [];
                
                foreach ($users as $user) {
                    $fullName = trim($user['user_fname'] . ' ' . $user['user_lname']);
                    $result = $mailer->sendBroadcastNotification($user['email'], $fullName, $type, $date, $reason);
                    if ($result) {
                        $successCount++;
                    } else {
                        $failCount++;
                        $failedEmails[] = $user['email'];
                    }
                }
                
                if ($pdo) {
                    $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                    $notificationStmt->execute([
                        "📢 Broadcast Email Sent",
                        "Broadcast email sent to {$successCount} users. Type: {$type}, Date: {$date}. {$failCount} failed deliveries.",
                        "system"
                    ]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => "Broadcast email sent. {$successCount} successful, {$failCount} failed",
                    'success_count' => $successCount,
                    'fail_count' => $failCount,
                    'failed_emails' => $failedEmails
                ]);
                break;

            case "reservation_status_email":
                require_once '../../vendor/autoload.php';
                require_once('../class.php');
                
                $reservation_id = $_POST['reservation_id'] ?? '';
                $status = $_POST['status'] ?? '';
                
                if (!$reservation_id || !$status) {
                    sendError('Missing required fields');
                }
                
                $db = new global_class();
                $fetch_reservation = $db->fetch_reservation($reservation_id);
                
                if (!$fetch_reservation) {
                    sendError('Reservation not found');
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
                
                $mailer = new Mailer();
                $result = $mailer->sendAccountNotification($Email, $Fullname, $order_code, $formatted, $status, $table_code);
                
                if ($result) {
                    sendSuccess(['message' => 'Email notification sent successfully']);
                } else {
                    sendError('Failed to send email notification');
                }
                break;

            case 'SendVerificationCode':
                $email = $_POST['email'] ?? '';
                
                if (empty(trim($email))) {
                    sendError('Email is required');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    sendError('Please enter a valid email address');
                }

                $checkEmail = $db->checkEmailExists($email);
                if ($checkEmail) {
                    sendError('This email is already registered');
                }

                $result = $db->sendVerificationCode($email);
                
                if ($result['success']) {
                    sendSuccess([
                        'message' => 'Verification code sent to your email',
                        'verification_code' => $result['verification_code']
                    ]);
                } else {
                    sendError(!empty($result['message']) ? $result['message'] : 'Failed to send verification code');
                }
                break;

            case 'RegisterWithVerification':
                $required = ['email', 'password', 'verification_code'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $email = $_POST['email'];
                $password = $_POST['password'];
                $verification_code = $_POST['verification_code'];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    sendError('Please enter a valid email address');
                }

                if (strlen($password) < 6) {
                    sendError('Password must be at least 6 characters long');
                }

                $checkEmail = $db->checkEmailExists($email);
                if ($checkEmail) {
                    sendError('This email is already registered');
                }

                $result = $db->RegisterWithVerification($email, $password, $verification_code);
                
                if ($result['success']) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "👤 New User Registration",
                            "New user registered with email: {$email}",
                            "system"
                        ]);
                    }
                    
                    sendSuccess([
                        'message' => 'Registration successful! Redirecting to login...',
                        'redirect' => 'login.php'
                    ]);
                } else {
                    sendError($result['message']);
                }
                break;

            case 'Login':
                $required = ['email', 'password'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $email = $_POST['email'];
                $password = $_POST['password'];
                $loginResult = $db->Login($email, $password);

                if ($loginResult['success']) {
                    $_SESSION['user_id'] = $loginResult['data']['user_id'];
                    $_SESSION['user_position'] = $loginResult['data']['user_position'];
                    $_SESSION['email'] = $email;
                    $_SESSION['user_fname'] = $loginResult['data']['user_fname'] ?? '';
                    $_SESSION['user_lname'] = $loginResult['data']['user_lname'] ?? '';
                    $_SESSION['phone'] = $loginResult['data']['user_phone'] ?? '';
                    
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "🔐 User Login",
                            "User {$email} ({$loginResult['data']['user_position']}) logged into the system",
                            "system"
                        ]);
                    }
                    
                    sendSuccess([
                        'message' => $loginResult['message'],
                        'user_position' => $loginResult['data']['user_position']
                    ]);
                } else {
                    sendError($loginResult['message']);
                }
                break;

            case 'Register':
                $required = ['email', 'password'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $email = $_POST['email'];
                $password = $_POST['password'];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    sendError('Please enter a valid email address');
                }

                if (strlen($password) < 6) {
                    sendError('Password must be at least 6 characters long');
                }

                $checkEmail = $db->checkEmailExists($email);
                if ($checkEmail) {
                    sendError('This email is already registered');
                }

                $result = $db->RegisterWithVerification($email, $password, '');
                
                if ($result['success']) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "👤 New User Registration",
                            "New user registered with email: {$email}",
                            "system"
                        ]);
                    }
                    
                    sendSuccess([
                        'message' => 'Registration successful! Redirecting to login...',
                        'redirect' => 'login.php'
                    ]);
                } else {
                    sendError($result['message']);
                }
                break;

            case 'VerifyEmail':
                $required = ['user_id', 'token'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $user_id = $_POST['user_id'];
                $token = $_POST['token'];
                $result = $db->verifyEmail($user_id, $token);
                echo json_encode($result);
                break;

            case 'reschedule':
                $required = ['reservationId', 'reason', 'seats', 'newDate', 'newTime'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $reservationId = $_POST['reservationId'];
                $reason = $_POST['reason'];
                $seats = $_POST['seats'];
                $newDate = $_POST['newDate'];
                $newTime = $_POST['newTime'];

                if (!is_numeric($seats) || $seats < 1 || $seats > 6) {
                    sendError('Number of seats must be between 1 and 6');
                }

                $table_code = $_POST['table_code'] ?? '';
                if ($table_code && !checkTableAvailability($db, $table_code, $newDate, $newTime)) {
                    sendError('This table is already reserved for the selected date and time. Please choose a different time slot.');
                }

                try {
                    $sql = "UPDATE reservations 
                            SET status = 'request_reschedule', 
                                seats = ?,
                                date_schedule = ?,
                                time_schedule = ?,
                                updated_at = NOW()
                            WHERE id = ?";
                    
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("issi", $seats, $newDate, $newTime, $reservationId);
                    
                    if ($stmt->execute()) {
                        require_once '../../vendor/autoload.php';
                        $mailer = new Mailer();
                        
                        $reservation = $db->getReservationById($reservationId);
                        if ($reservation) {
                            $date_schedule = $newDate;
                            $time_schedule = $newTime;
                            $table_code = $reservation['table_code'];
                            $Email = $reservation["customer_email"];
                            $Fullname = $reservation["customer_name"];
                            $order_code = $reservation["reserve_unique_code"] ?? 'GB-' . str_pad($reservationId, 6, '0', STR_PAD_LEFT);
                            
                            $date_scheduleTime = new DateTime("$date_schedule $time_schedule");
                            $formatted = $date_scheduleTime->format('l, F j, Y - g:i A');
                            
                            $mailer->sendAccountNotification($Email, $Fullname, $order_code, $formatted, 'request_reschedule', $table_code);
                        }
                        
                        if ($pdo) {
                            $notificationMessage = "Reservation #{$reservationId} requested reschedule to {$newDate} {$newTime}. Reason: {$reason}";
                            $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read, reservation_id, table_code, created_at, updated_at) VALUES ('Reschedule Request', ?, 'reservation', 0, ?, ?, NOW(), NOW())");
                            $notificationStmt->execute([$notificationMessage, $reservationId, $reservation['table_code']]);
                        }
                        
                        sendSuccess(['message' => 'Reschedule request submitted successfully']);
                    } else {
                        sendError('Failed to submit reschedule request');
                    }
                } catch (Exception $e) {
                    error_log("Reschedule error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage());
                }
                break;

            case 'cancel_reservation':
                if (!isset($_POST['reservation_id'])) {
                    sendError('Reservation ID is required');
                }

                $reservationId = $_POST['reservation_id'];
                
                try {
                    $sql = "UPDATE reservations SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("i", $reservationId);
                    
                    if ($stmt->execute()) {
                        require_once '../../vendor/autoload.php';
                        $mailer = new Mailer();
                        
                        $reservation = $db->getReservationById($reservationId);
                        if ($reservation) {
                            $date_schedule = $reservation['date_schedule'];
                            $time_schedule = $reservation['time_schedule'];
                            $table_code = $reservation['table_code'];
                            $Email = $reservation["customer_email"];
                            $Fullname = $reservation["customer_name"];
                            $order_code = $reservation["reserve_unique_code"] ?? 'GB-' . str_pad($reservationId, 6, '0', STR_PAD_LEFT);
                            
                            $date_scheduleTime = new DateTime("$date_schedule $time_schedule");
                            $formatted = $date_scheduleTime->format('l, F j, Y - g:i A');
                            
                            $mailer->sendAccountNotification($Email, $Fullname, $order_code, $formatted, 'cancelled', $table_code);
                        }
                        
                        if ($pdo) {
                            $reservation = $db->getReservationById($reservationId);
                            if ($reservation) {
                                $notificationMessage = "Reservation #{$reservationId} has been cancelled. Reference: {$order_code}";
                                $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read, reservation_id, table_code, created_at, updated_at) VALUES ('Reservation Cancelled', ?, 'reservation', 0, ?, ?, NOW(), NOW())");
                                $notificationStmt->execute([$notificationMessage, $reservationId, $reservation['table_code']]);
                            }
                        }
                        
                        sendSuccess(['message' => 'Reservation cancelled successfully']);
                    } else {
                        sendError('Failed to cancel reservation');
                    }
                } catch (Exception $e) {
                    error_log("Cancel reservation error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage());
                }
                break;

            case 'completed':
                if (!isset($_POST['reservation_id'])) {
                    sendError('Reservation ID is required');
                }

                $reservation_id = $_POST['reservation_id'];
                
                try {
                    $updateStmt = $pdo->prepare("UPDATE reservations SET status = 'completed' WHERE id = ?");
                    $updateStmt->execute([$reservation_id]);
                    
                    $reservation = $db->getReservationById($reservation_id);
                    if ($reservation && isset($reservation['table_code'])) {
                        $table_code = $reservation['table_code'];
                        
                        $notificationStmt = $pdo->prepare("
                            INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) 
                            VALUES ('Reservation Completed', ?, 'reservation', ?, ?, NOW())
                        ");
                        $notificationStmt->execute([
                            "Reservation #{$reservation_id} has been marked as completed. Table {$table_code} is now available.",
                            $reservation_id,
                            $table_code
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Reservation marked as completed successfully']);
                } catch (Exception $e) {
                    error_log("Complete reservation error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage());
                }
                break;

            case 'RegisterCustomer':
                $required = ['first_name', 'last_name', 'email', 'password'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $email = $_POST['email'];
                $password = $_POST['password'];

                $result = $db->RegisterCustomer($first_name, $last_name, $email, $password);

                if ($result['success']) {
                    if (isset($result['data'])) {
                        $_SESSION['user_id'] = $result['data']['user_id'] ?? '';
                        $_SESSION['user_position'] = $result['data']['user_position'] ?? 'customer';
                        $_SESSION['email'] = $email;
                        $_SESSION['user_fname'] = $first_name;
                        $_SESSION['user_lname'] = $last_name;
                        $_SESSION['phone'] = $result['data']['user_phone'] ?? '';
                    }
                    
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "👤 New Customer Registration",
                            "New customer registered: {$first_name} {$last_name} ({$email})",
                            "system"
                        ]);
                    }
                    
                    sendSuccess([
                        'message' => 'Registration successful! Redirecting to login...',
                        'redirect' => 'login.php'
                    ]);
                } else {
                    sendError($result['message']);
                }
                break;

            case 'set_table_unavailable_walking':
                if (!isset($_POST['table_code'])) {
                    sendError('Table code is required');
                }

                $table_code = $_POST['table_code'];
                $updateSuccess = $db->set_table_unavailable_walking($table_code);

                if ($updateSuccess) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, table_code, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "Table Status Changed",
                            "Table {$table_code} has been set to Not Available",
                            "system",
                            $table_code
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Table successfully set to Not Available']);
                } else {
                    sendError('Failed to update table status');
                }
                break;

            case 'set_table_available_from_walkin':
                if (!isset($_POST['table_code'])) {
                    sendError('Table code is required');
                }

                $table_code = $_POST['table_code'];
                $updateSuccess = $db->set_table_available_from_walkin($table_code);

                if ($updateSuccess) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, table_code, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "Table Status Changed",
                            "Table {$table_code} has been set to Available from walk-in",
                            "system",
                            $table_code
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Table successfully set to Available']);
                } else {
                    sendError('Failed to update table status');
                }
                break;

            case 'updateArchived':
                $required = ['column', 'reservation_id', 'status'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $column = $_POST['column'];
                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                
                $result = $db->updateArchived($reservation_id, $status, $column);

                if ($result['success']) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "Reservation Archived",
                            "Reservation #{$reservation_id} has been {$status} in {$column}",
                            "system",
                            $reservation_id
                        ]);
                    }
                    
                    sendSuccess(['message' => $result['message']]);
                } else {
                    sendError($result['message']);
                }
                break;

            case 'ApproveReschedule':
                $required = ['reservation_id', 'status'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                
                $result = $db->ApproveReschedule($reservation_id, $status);

                if ($result['success']) {
                    if ($pdo) {
                        $reservation = $db->getReservationById($reservation_id);
                        if ($reservation) {
                            $notificationMessage = "Reschedule request for Reservation #{$reservation_id} ({$reservation['reserve_unique_code']}) has been approved. New status: {$status}";
                            $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) VALUES ('Reschedule Approved', ?, 'reservation', ?, ?, NOW())");
                            $notificationStmt->execute([$notificationMessage, $reservation_id, $reservation['table_code']]);
                        }
                    }
                    
                    sendSuccess(['message' => $result['message']]);
                } else {
                    sendError($result['message']);
                }
                break;

            case 'UpdateReservationStatus':
                $required = ['reservation_id', 'status'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                
                $result = $db->UpdateReservationStatus($reservation_id, $status);

                if ($result['success']) {
                    if ($pdo) {
                        $reservation = $db->getReservationById($reservation_id);
                        if ($reservation) {
                            $notificationMessage = "Reservation #{$reservation_id} ({$reservation['reserve_unique_code']}) status updated to {$status}";
                            $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) VALUES ('Status Updated', ?, 'reservation', ?, ?, NOW())");
                            $notificationStmt->execute([$notificationMessage, $reservation_id, $reservation['table_code']]);
                        }
                    }
                    
                    sendSuccess(['message' => $result['message']]);
                } else {
                    sendError($result['message']);
                }
                break;

            case 'ApproveReservationStatus':
                $required = ['reservation_id', 'status'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];

                if ($status === "confirmed") {
                    $result = $db->ApproveReservationStatus_with_validation($reservation_id, $status);
                } else {
                    $result = $db->ApproveReservationStatus($reservation_id, $status);
                }

                if ($result['success']) {
                    require_once '../../vendor/autoload.php';
                    
                    try {
                        $reservation = $db->getReservationById($reservation_id);
                        
                        if ($reservation) {
                            $date_schedule = $reservation['date_schedule'] ?? '';
                            $time_schedule = $reservation['time_schedule'] ?? '';
                            $table_code = $reservation['table_code'] ?? '';
                            $Email = $reservation["customer_email"] ?? '';
                            $Fullname = $reservation["customer_name"] ?? '';
                            $order_code = $reservation["reserve_unique_code"] ?? 'GB-' . str_pad($reservation_id, 6, '0', STR_PAD_LEFT);
                            
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
                            
                            $mailer = new Mailer();
                            $emailResult = $mailer->sendAccountNotification($Email, $Fullname, $order_code, $formatted, $status, $table_code);
                            
                            if ($emailResult) {
                                $mailer = new Mailer();
                                $mailer->sendAccountNotification($Email, $Fullname, $order_code, $formatted, $status, $table_code);
                                
                                if ($pdo) {
                                    $notificationMessage = "Reservation #{$reservation_id} ({$order_code}) has been {$status}. Email sent to customer.";
                                    $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) VALUES ('Reservation Approved', ?, 'reservation', ?, ?, NOW())");
                                    $notificationStmt->execute([$notificationMessage, $reservation_id, $table_code]);
                                }
                                
                                error_log("Email sent successfully for reservation #{$reservation_id} to {$Email}");
                                
                                sendSuccess(['message' => $result['message'] . ' and email notification sent successfully']);
                            } else {
                                sendSuccess(['message' => $result['message'] . ' but email failed to send']);
                            }
                        } else {
                            sendSuccess(['message' => $result['message'] . ' (reservation details not found for email)']);
                        }
                    } catch (Exception $e) {
                        error_log("Email error for reservation #{$reservation_id}: " . $e->getMessage());
                        sendSuccess(['message' => $result['message'] . ' (email system error)']);
                    }
                } else {
                    sendError($result['message']);
                }
                break;

            case 'AddMenu':
                $required = ['menuName', 'menuCategory', 'menuDescription', 'menuPrice'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $menuName = $_POST['menuName'];
                $menuCategory = $_POST['menuCategory'];
                $menuDescription = $_POST['menuDescription'];
                $menuPrice = $_POST['menuPrice'];
                $menuImage = $_FILES['menuImage'] ?? null;
                $uploadDir = '../../static/upload/menu/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $menuImageFileName = '';
                
                if ($menuImage && $menuImage['error'] === UPLOAD_ERR_OK) {
                    $bannerExtension = pathinfo($menuImage['name'], PATHINFO_EXTENSION);
                    $menuImageFileName = uniqid('menu_', true) . '.' . $bannerExtension;
                    $bannerPath = $uploadDir . $menuImageFileName;

                    $bannerUploaded = move_uploaded_file($menuImage['tmp_name'], $bannerPath);

                    if (!$bannerUploaded) {
                        sendError('Error uploading menu image.', 500);
                    }
                } elseif ($menuImage && $menuImage['error'] !== UPLOAD_ERR_NO_FILE && $menuImage['error'] !== 0) {
                    sendError('Invalid image upload.', 400);
                }
                
                $result = $db->AddMenu($menuName, $menuCategory, $menuDescription, $menuPrice, $menuImageFileName);

                if ($result) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "🍽️ New Menu Added",
                            "New menu item added: {$menuName} ({$menuCategory}) - ₱{$menuPrice}",
                            "system"
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Menu added successfully.']);
                } else {
                    sendError('Error saving menu data.', 500);
                }
                break;

            case 'UpdatMenu':
                $required = ['menu_id', 'menu_name', 'menuCategory', 'menu_description', 'menu_price'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $menu_id = intval($_POST['menu_id']);
                $menu_name = sanitizeInput($_POST['menu_name']);
                $menu_category = sanitizeInput($_POST['menuCategory']);
                $menu_description = sanitizeInput($_POST['menu_description']);
                $menu_price = floatval($_POST['menu_price']);

                $uniqueBannerFileName = null;
                $oldImageFile = null;
                
                $oldMenu = $db->getMenuById($menu_id);
                if ($oldMenu && !empty($oldMenu['menu_image_banner'])) {
                    $oldImageFile = $oldMenu['menu_image_banner'];
                }
                
                if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../../static/upload/menu/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    $fileType = mime_content_type($_FILES['menu_image']['tmp_name']);
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        sendError('Invalid image type. Please upload JPEG, PNG, or GIF images only.');
                    }
                    
                    $fileExtension = pathinfo($_FILES['menu_image']['name'], PATHINFO_EXTENSION);
                    $uniqueBannerFileName = uniqid('menu_', true) . '.' . strtolower($fileExtension);
                    
                    if (!move_uploaded_file($_FILES['menu_image']['tmp_name'], $uploadDir . $uniqueBannerFileName)) {
                        sendError('Failed to upload new image.');
                    }
                    
                    if ($oldImageFile) {
                        $oldPath = $uploadDir . $oldImageFile;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                }

                try {
                    $result = $db->UpdateMenu($menu_id, $menu_name, $menu_category, $menu_description, $menu_price, $uniqueBannerFileName);

                    if ($result['status']) {
                        echo json_encode([
                            'status' => 200,
                            'success' => true,
                            'message' => 'Menu item updated successfully!'
                        ]);
                    } else {
                        if ($uniqueBannerFileName && file_exists($uploadDir . $uniqueBannerFileName)) {
                            unlink($uploadDir . $uniqueBannerFileName);
                        }
                        echo json_encode([
                            'status' => 500,
                            'success' => false,
                            'message' => $result['message']
                        ]);
                    }
                } catch (Exception $e) {
                    if ($uniqueBannerFileName && file_exists($uploadDir . $uniqueBannerFileName)) {
                        unlink($uploadDir . $uniqueBannerFileName);
                    }
                    error_log("Update menu error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage(), 500);
                }
                break;

            case 'AddMenuDeals':
                $required = ['menu', 'deal_id'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $menu_id = $_POST['menu'];
                $deal_id = $_POST['deal_id'];
                
                $result = $db->AddMenuDeals($menu_id, $deal_id);
                if ($result) {
                    sendSuccess(['message' => 'Successfully Added.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case 'removeMenu':
                if (!isset($_POST['menu_id'])) {
                    sendError('Menu ID is required');
                }

                $menu_id = intval($_POST['menu_id']);
                
                try {
                    $menuItem = $db->getMenuById($menu_id);
                    $imageFile = null;
                    
                    if ($menuItem && !empty($menuItem['menu_image_banner'])) {
                        $imageFile = $menuItem['menu_image_banner'];
                    }
                    
                    $result = $db->removeMenu($menu_id);
                    
                    if ($result) {
                        if ($imageFile) {
                            $imagePath = getMenuImagePath($imageFile);
                            if ($imagePath && file_exists($imagePath)) {
                                unlink($imagePath);
                            }
                            
                            $uploadDir = '../../static/upload/menu/';
                            $fullPath = $uploadDir . $imageFile;
                            if (file_exists($fullPath)) {
                                unlink($fullPath);
                            }
                        }
                        
                        if ($pdo) {
                            $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                            $notificationStmt->execute([
                                "🗑️ Menu Item Removed",
                                "Menu item #{$menu_id} has been removed from the system",
                                "system"
                            ]);
                        }
                        
                        echo json_encode([
                            'status' => 200,
                            'success' => true,
                            'message' => 'Menu item removed successfully!'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 500,
                            'success' => false,
                            'message' => 'Failed to remove menu item. It may not exist.'
                        ]);
                    }
                } catch (Exception $e) {
                    error_log("Remove menu error: " . $e->getMessage());
                    sendError('Database error: ' . $e->getMessage(), 500);
                }
                break;

            case 'removeUser':
                if (!isset($_POST['user_id'])) {
                    sendError('User ID is required');
                }

                $user_id = $_POST['user_id'];
                $result = $db->removeUser($user_id);
                if ($result) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "👤 User Removed",
                            "User with ID {$user_id} has been removed from the system",
                            "system"
                        ]);
                    }
                    
                    sendSuccess(['message' => 'User removed successfully.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case 'remove_deal_ids':
                $required = ['dealId', 'menu_id'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $deal_id = $_POST['dealId'];
                $menu_id = $_POST['menu_id'];
                $result = $db->remove_deal_ids($menu_id, $deal_id);
                if ($result) {
                    sendSuccess(['message' => 'Deal item removed successfully.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case 'removeDeals':
                if (!isset($_POST['deal_id'])) {
                    sendError('Deal ID is required');
                }

                $deal_id = $_POST['deal_id'];
                $result = $db->removeDeals($deal_id);
                if ($result) {
                    sendSuccess(['message' => 'Deal removed successfully.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case 'CreatDeals':
                $required = ['entryName', 'entryDescription', 'deal_type'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $entryName = $_POST['entryName'];
                $entryDescription = $_POST['entryDescription'];
                $deal_type = $_POST['deal_type'];
                $entryExpiration = $_POST['entryExpiration'] ?? null;

                $entryImage = $_FILES['entryImage'] ?? null;
                $uploadDir = '../../static/uploads/deals/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $entryImageFileName = '';

                if ($entryImage && $entryImage['error'] === UPLOAD_ERR_OK) {
                    $bannerExtension = pathinfo($entryImage['name'], PATHINFO_EXTENSION);
                    $entryImageFileName = uniqid('deals_', true) . '.' . $bannerExtension;
                    $bannerPath = $uploadDir . $entryImageFileName;

                    $bannerUploaded = move_uploaded_file($entryImage['tmp_name'], $bannerPath);

                    if (!$bannerUploaded) {
                        sendError('Error uploading image.', 500);
                    }
                } elseif ($entryImage && $entryImage['error'] !== UPLOAD_ERR_NO_FILE && $entryImage['error'] !== 0) {
                    sendError('Invalid image upload.', 400);
                }

                $result = $db->createDeals($entryName, $entryDescription, $deal_type, $entryImageFileName, $entryExpiration);

                if ($result) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "🎉 New Deal Created",
                            "New deal created: {$entryName} ({$deal_type})",
                            "system"
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Deal created successfully.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case 'create_group_deal':
                $required = ['entryName', 'entryDescription', 'dealPrice', 'selected_menus'];
                $missing = validateRequiredFields($required, $_POST);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $entryName = $_POST['entryName'];
                $entryDescription = $_POST['entryDescription'];
                $dealPrice = $_POST['dealPrice'];
                $selectedMenus = json_decode($_POST['selected_menus'], true);

                if (empty($selectedMenus)) {
                    sendError('Please select at least one menu item for the group deal');
                }

                $entryImage = $_FILES['entryImage'] ?? null;
                $uploadDir = '../../static/uploads/deals/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $entryImageFileName = '';

                if ($entryImage && $entryImage['error'] === UPLOAD_ERR_OK) {
                    $bannerExtension = pathinfo($entryImage['name'], PATHINFO_EXTENSION);
                    $entryImageFileName = uniqid('deals_', true) . '.' . $bannerExtension;
                    $bannerPath = $uploadDir . $entryImageFileName;

                    $bannerUploaded = move_uploaded_file($entryImage['tmp_name'], $bannerPath);

                    if (!$bannerUploaded) {
                        sendError('Error uploading image.', 500);
                    }
                } elseif ($entryImage && $entryImage['error'] !== UPLOAD_ERR_NO_FILE && $entryImage['error'] !== 0) {
                    sendError('Invalid image upload.', 400);
                }

                $menuIds = array_column($selectedMenus, 'menu_id');
                
                $result = $db->createGroupDeal($entryName, $entryDescription, $dealPrice, $entryImageFileName, $menuIds);

                if ($result) {
                    if ($pdo) {
                        $notificationStmt = $pdo->prepare("INSERT INTO notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
                        $notificationStmt->execute([
                            "👥 New Group Deal Created",
                            "New group deal created: {$entryName} - ₱{$dealPrice} (includes " . count($menuIds) . " menu items)",
                            "system"
                        ]);
                    }
                    
                    sendSuccess(['message' => 'Group deal created successfully.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case 'remove_group_deal':
                if (!isset($_POST['group_id'])) {
                    sendError('Group ID is required');
                }

                $group_id = $_POST['group_id'];
                $result = $db->removeGroupDeal($group_id);
                if ($result) {
                    sendSuccess(['message' => 'Group deal removed successfully.']);
                } else {
                    sendError('No changes made or error updating data.', 500);
                }
                break;

            case "RequestReservation":
                $table_code = $_POST['table_code'] ?? '';
                $customer_name = $_POST['customer_name'] ?? '';
                $customer_email = $_POST['customer_email'] ?? '';
                $customer_phone = $_POST['customer_phone'] ?? '';
                $seats = $_POST['seats'] ?? 0;
                $date_schedule = $_POST['date_schedule'] ?? '';
                $time_schedule = $_POST['time_schedule'] ?? '';
                
                $menu_total = floatval($_POST['menu_total'] ?? 0);
                $drink_total = floatval($_POST['drink_total'] ?? 0);
                $promo_total = floatval($_POST['promo_total'] ?? 0);
                $group_total = floatval($_POST['group_total'] ?? 0);
                $grand_total = floatval($_POST['grand_total'] ?? 0);
                
                $selected_menus = $_POST['selected_menus'] ?? '[]';
                $selected_drinks = $_POST['selected_drinks'] ?? '[]';
                $selected_promos = $_POST['selected_promos'] ?? '[]';
                $selected_groups = $_POST['selected_groups'] ?? '[]';
                
                $payment_type = $_POST['payment_type'] ?? 'full';
                $amount_to_pay = floatval($_POST['amount_to_pay'] ?? 0);
                $payment_method = $_POST['payment_method'] ?? '';
                
                $food_corkage_fee = floatval($_POST['food_corkage_fee'] ?? 0);
                $drink_corkage_fee = floatval($_POST['drink_corkage_fee'] ?? 0);
                $corkage_fee = floatval($_POST['corkage_fee'] ?? 0);
                $service_charge = floatval($_POST['service_charge'] ?? 0);
                
                $payment_proof_filename = '';
                if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
                    $payment_proof = $_FILES['payment_proof'];
                    $payment_proof_filename = time() . '_' . basename($payment_proof['name']);
                    $upload_path = '../../static/upload/payments/' . $payment_proof_filename;
                    move_uploaded_file($payment_proof['tmp_name'], $upload_path);
                }
                
                $selected_menus_json = json_encode(json_decode($selected_menus, true));
                $selected_drinks_json = json_encode(json_decode($selected_drinks, true));
                $selected_promos_json = json_encode(json_decode($selected_promos, true));
                $selected_groups_json = json_encode(json_decode($selected_groups, true));
                
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations (
                            table_code, customer_name, customer_email, customer_phone, seats, 
                            date_schedule, time_schedule, menu_total, drink_total, promo_total, group_total, 
                            grand_total, selected_menus, selected_drinks, selected_promos, selected_groups, 
                            payment_type, amount_to_pay, payment_method, payment_proof, 
                            status, corkage_fee, service_charge, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    
                    $stmt->execute([
                        $table_code, $customer_name, $customer_email, $customer_phone, $seats,
                        $date_schedule, $time_schedule, $menu_total, $drink_total, $promo_total, $group_total,
                        $grand_total, $selected_menus_json, $selected_drinks_json, $selected_promos_json, $selected_groups_json,
                        $payment_type, $amount_to_pay, $payment_method, $payment_proof_filename,
                        'pending', $corkage_fee, $service_charge
                    ]);
                    
                    $reservation_id = $pdo->lastInsertId();
                    
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Reservation submitted successfully',
                        'reference_id' => $reservation_id
                    ]);
                    
                } catch (PDOException $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error saving reservation: ' . $e->getMessage()
                    ]);
                }
                exit();

            default:
                sendError('Invalid POST request type.', 404);
                break;
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['requestType'])) {
            sendError('No GET request type specified.');
        }

        $requestType = $_GET['requestType'];

        switch ($requestType) {
            case 'get_realtime_table_status':
                try {
                    $date = $_GET['date'] ?? date('Y-m-d');
                    
                    $sql = "
                        SELECT 
                            r.table_code,
                            r.status
                        FROM reservations r
                        WHERE r.date_schedule = ? 
                        AND r.status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule', 'completed')
                        
                        UNION ALL
                        
                        SELECT 
                            wt.walkin_table_code as table_code,
                            'walkin' as status
                        FROM walkin_tables wt 
                        WHERE (wt.walkin_date = ? OR wt.walkin_date IS NULL)
                        AND wt.walkin_status IS NOT NULL
                    ";
                    
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("ss", $date, $date);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $tableStatus = [];
                    while ($row = $result->fetch_assoc()) {
                        $tableStatus[$row['table_code']] = $row['status'];
                    }
                    
                    echo json_encode([
                        'status' => 'success',
                        'data' => $tableStatus
                    ]);
                    
                } catch (Exception $e) {
                    error_log("Error getting realtime table status: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                }
                break;

            case 'fetch_walkin_tables':
                try {
                    $date = $_GET['date'] ?? date('Y-m-d');
                    
                    $checkTableSQL = "SHOW TABLES LIKE 'walkin_tables'";
                    $tableCheck = $db->conn->query($checkTableSQL);
                    
                    if ($tableCheck->num_rows === 0) {
                        echo json_encode([
                            'status' => 'success',
                            'data' => [],
                            'message' => 'No walk-in tables found'
                        ]);
                        break;
                    }
                    
                    $sql = "SELECT * FROM walkin_tables WHERE walkin_date = ? OR walkin_date IS NULL";
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("s", $date);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $walkinTables = [];
                    while ($row = $result->fetch_assoc()) {
                        $walkinTables[] = [
                            'walkin_table_code' => $row['walkin_table_code'],
                            'walkin_status' => $row['walkin_status'],
                            'walkin_customer_name' => $row['walkin_customer_name'],
                            'walkin_seats' => $row['walkin_seats'],
                            'walkin_date' => $row['walkin_date']
                        ];
                    }
                    
                    echo json_encode([
                        'status' => 'success',
                        'data' => $walkinTables,
                        'count' => count($walkinTables)
                    ]);
                    
                } catch (Exception $e) {
                    error_log("Error fetching walkin tables: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                }
                break;

            case 'fetch_group_deals':
                try {
                    $deal_type = 'group_deals';
                    
                    $sql = "SELECT * FROM deals WHERE deal_type = ?";
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("s", $deal_type);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $deals = [];
                    while ($row = $result->fetch_assoc()) {
                        if (!empty($row['deal_ids'])) {
                            $deal_ids = json_decode($row['deal_ids'], true);
                            if ($deal_ids && is_array($deal_ids)) {
                                $menus = [];
                                foreach ($deal_ids as $menu_id) {
                                    $menuSql = "SELECT * FROM menus WHERE menu_id = ?";
                                    $menuStmt = $db->conn->prepare($menuSql);
                                    $menuStmt->bind_param("i", $menu_id);
                                    $menuStmt->execute();
                                    $menuResult = $menuStmt->get_result();
                                    if ($menuRow = $menuResult->fetch_assoc()) {
                                        $menus[] = $menuRow;
                                    }
                                }
                                $row['menus'] = $menus;
                            }
                        }
                        $deals[] = $row;
                    }
                    
                    echo json_encode([
                        'status' => 'success',
                        'data' => $deals
                    ]);
                    
                } catch (Exception $e) {
                    error_log("Error fetching group deals: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                }
                break;

            case 'fetch_all_menu':
                try {
                    $menuData = $db->fetch_all_menu();
                    
                    if ($menuData && is_array($menuData) && count($menuData) > 0) {
                        $processedData = [];
                        foreach ($menuData as $menu) {
                            $processedItem = [
                                'menu_id' => $menu['menu_id'] ?? 0,
                                'menu_name' => $menu['menu_name'] ?? 'Unknown Item',
                                'menu_category' => $menu['menu_category'] ?? 'uncategorized',
                                'menu_description' => $menu['menu_description'] ?? '',
                                'menu_price' => $menu['menu_price'] ?? 0,
                                'menu_image_banner' => $menu['menu_image_banner'] ?? '',
                                'has_image' => !empty($menu['menu_image_banner'])
                            ];
                            
                            if (!empty($menu['menu_image_banner'])) {
                                $imagePath = getMenuImagePath($menu['menu_image_banner']);
                                $processedItem['image_url'] = $imagePath ? $imagePath : '../static/images/no-image.jpg';
                            } else {
                                $processedItem['image_url'] = '../static/images/no-image.jpg';
                            }
                            
                            $processedData[] = $processedItem;
                        }
                        
                        echo json_encode([
                            'status' => 200,
                            'success' => true,
                            'data' => $processedData,
                            'count' => count($processedData)
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 404,
                            'success' => false,
                            'message' => 'No menu items found',
                            'data' => [],
                            'count' => 0
                        ]);
                    }
                } catch (Exception $e) {
                    error_log("Error fetching menu: " . $e->getMessage());
                    echo json_encode([
                        'status' => 500,
                        'success' => false,
                        'message' => 'Error fetching menu items',
                        'data' => [],
                        'count' => 0
                    ]);
                }
                break;

            case 'fetch_all_users':
                $result = $db->fetch_all_users();
                echo json_encode(['status' => 200, 'data' => $result]);
                break;

            case 'fetch_all_deals':
                $deal_type = isset($_GET["deal_type"]) && $_GET["deal_type"] !== '' ? $_GET["deal_type"] : null;
                $result = $db->fetch_all_deals($deal_type);
                echo json_encode(['status' => 200, 'data' => $result]);
                break;

            case 'fetch_all_deals_and_menu':
                $deal_type = isset($_GET["deal_type"]) && $_GET["deal_type"] !== '' ? $_GET["deal_type"] : null;
                $result = $db->fetch_all_deals_and_menu($deal_type);
                echo json_encode(['status' => 200, 'data' => $result]);
                break;

            case 'GetAllDealsWithMenus_byId':
                if (!isset($_GET['deal_id'])) {
                    sendError('Deal ID is required');
                }
                $dealId = $_GET['deal_id'];
                $result = $db->GetAllDealsWithMenus_byId($dealId);
                echo json_encode(['status' => 200, 'data' => $result]);
                break;

            case 'checkAvailability':
                $required = ['table_code', 'date_schedule', 'time_schedule'];
                $missing = validateRequiredFields($required, $_GET);
                if (!empty($missing)) {
                    sendError('Missing required fields: ' . implode(', ', $missing));
                }

                $table_code = $_GET['table_code'];
                $date_schedule = $_GET['date_schedule'];
                $time_schedule = $_GET['time_schedule'];

                $availability = checkTableAvailability($db, $table_code, $date_schedule, $time_schedule);
                echo json_encode(['status' => 200, 'availability' => $availability]);
                break;

            case 'fetch_all_reserve_request':
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                $data = $db->fetch_all_reserve_request($limit, $offset);
                $total = $db->count_all_reserve_request();

                echo json_encode(['status' => 200, 'total' => $total, 'data' => $data]);
                break;

            case 'fetch_all_reserved':
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                $data = $db->fetch_all_reserved($limit, $offset);
                $total = $db->count_all_reserved();

                echo json_encode(['status' => 200, 'total' => $total, 'data' => $data]);
                break;

            case 'fetch_all_reserved_archived':
                if (!isset($_GET['collumn'])) {
                    sendError('Column parameter is required');
                }
                $collumn = $_GET['collumn'];
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                $data = $db->fetch_all_reserved_archived($limit, $offset, $collumn);
                $total = $db->count_all_reserved_archived();

                echo json_encode(['status' => 200, 'total' => $total, 'data' => $data]);
                break;

            case 'fetch_all_customer_reservation':
                $customer_email = $_GET['customer_email'] ?? '';
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                if (empty($customer_email)) {
                    sendError('Customer email is required');
                }

                try {
                    $count_sql = "SELECT COUNT(*) as total FROM reservations WHERE customer_email = ?";
                    $count_stmt = $db->conn->prepare($count_sql);
                    $count_stmt->bind_param("s", $customer_email);
                    $count_stmt->execute();
                    $count_result = $count_stmt->get_result();
                    $total_count = $count_result->fetch_assoc()['total'];
                    
                    $sql = "SELECT * FROM reservations 
                            WHERE customer_email = ? 
                            ORDER BY created_at DESC 
                            LIMIT ? OFFSET ?";
                    
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("sii", $customer_email, $limit, $offset);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $reservations = [];
                    while ($row = $result->fetch_assoc()) {
                        if (!empty($row['selected_menus'])) {
                            $row['selected_menus'] = json_decode($row['selected_menus'], true);
                        }
                        if (!empty($row['selected_promos'])) {
                            $row['selected_promos'] = json_decode($row['selected_promos'], true);
                        }
                        if (!empty($row['selected_groups'])) {
                            $row['selected_groups'] = json_decode($row['selected_groups'], true);
                        }
                        
                        $reservations[] = $row;
                    }
                    
                    echo json_encode([
                        'status' => 200,
                        'data' => $reservations,
                        'total' => $total_count,
                        'page' => $page,
                        'limit' => $limit,
                        'total_pages' => ceil($total_count / $limit)
                    ]);
                    
                } catch (Exception $e) {
                    error_log("Error fetching customer reservations: " . $e->getMessage());
                    echo json_encode([
                        'status' => 500, 
                        'message' => 'Error fetching reservations',
                        'data' => [],
                        'total' => 0
                    ]);
                }
                break;

            case 'dashboard_analytics':
                $data = $db->getDataAnalytics();
                
                $pendingStmt = $db->conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'");
                $pendingStmt->execute();
                $pendingResult = $pendingStmt->get_result();
                $pendingCount = $pendingResult->fetch_assoc()['count'];
                
                $completedStmt = $db->conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = 'completed'");
                $completedStmt->execute();
                $completedResult = $completedStmt->get_result();
                $completedCount = $completedResult->fetch_assoc()['count'];
                
                $unreadNotifications = 0;
                if ($pdo) {
                    try {
                        $tableCheck = $pdo->query("SHOW TABLES LIKE 'notifications'");
                        if ($tableCheck->rowCount() > 0) {
                            $notifStmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
                            $notifStmt->execute();
                            $notifResult = $notifStmt->fetch(PDO::FETCH_ASSOC);
                            $unreadNotifications = $notifResult['count'];
                        }
                    } catch (Exception $e) {
                        error_log("Error counting notifications: " . $e->getMessage());
                    }
                }
                
                if ($data) {
                    $data['pending_reservations'] = $pendingCount;
                    $data['completed_reservations'] = $completedCount;
                    $data['unread_notifications'] = $unreadNotifications;
                    echo json_encode(['success' => true, 'data' => $data]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to retrieve analytics']);
                }
                break;

            case 'fetch_all_customer_reservation_no_limit':
                if (!isset($_SESSION['user_id'])) {
                    sendError('User not logged in');
                }
                $user_id = $_SESSION['user_id'];
                $data = $db->fetch_all_customer_reservation_no_limit($user_id);

                echo json_encode(['status' => 200, 'data' => $data]);
                break;

            case 'fetch_all_admin_reservation_no_limit':
                $data = $db->fetch_all_admin_reservation_no_limit();
                echo json_encode(['status' => 200, 'data' => $data]);
                break;

            case 'fetch_report':
                $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
                $completedReservations = $db->getCompletedReservations($filter);
                echo json_encode($completedReservations);
                break;

            case 'fetch_all_table_availability_today':
                $allTables = [
                    'G6','G5','Take out 1','Take out 2','F3','F4',
                    'G4','G3','E4','E8','F1','F2',
                    'G2','G1','E3','E7','C6','D6','DJ',
                    'E2','E6','C5','D5','SOUNDECT',
                    'A5','B6','E1','E5','C4','D4','ACOUSTIC',
                    'A4','B5','C3','D3','VIP 3','VIP 2',
                    'A3','B4','RESERV.','C2','D2','BILLIARDS',
                    'A2','B3','MEETING','C2','D1','VIP 1',
                    'A1','B2','COMPLI','B1'
                ];

                $reservedTables = $db->fetch_all_reservations_today();

                $availability = [];
                foreach ($allTables as $table) {
                    $availability[] = [
                        'table_code' => $table,
                        'status' => in_array($table, $reservedTables) ? 'unavailable' : 'available'
                    ];
                }

                echo json_encode(['status' => 200, 'data' => $availability]);
                break;

            case 'fetch_reservations_for_tables':
                $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
                
                try {
                    $sql = "SELECT 
                                r.table_code, 
                                COALESCE(w.walkin_status, r.status) as final_status,
                                CASE 
                                    WHEN w.walkin_table_code IS NOT NULL THEN 'walkin_table'
                                    ELSE 'reservation'
                                END as source_type
                            FROM reservations r
                            LEFT JOIN walkin_tables w ON r.table_code = w.walkin_table_code 
                                AND DATE(w.walkin_created_at) = r.date_schedule
                                AND w.walkin_status IS NOT NULL
                            WHERE (r.date_schedule = ? OR r.date_schedule = DATE_ADD(?, INTERVAL 1 DAY))
                            AND r.status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule', 'walkin', 'completed')
                            
                            UNION ALL
                            
                            SELECT 
                                wt.walkin_table_code as table_code,
                                wt.walkin_status as final_status,
                                'walkin_only' as source_type
                            FROM walkin_tables wt
                            LEFT JOIN reservations r ON wt.walkin_table_code = r.table_code 
                                AND DATE(wt.walkin_created_at) = r.date_schedule
                            WHERE DATE(wt.walkin_created_at) = ?
                            AND r.table_code IS NULL";
                    
                    $stmt = $db->conn->prepare($sql);
                    $stmt->bind_param("sss", $date, $date, $date);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $reservations = [];
                    
                    while ($row = $result->fetch_assoc()) {
                        $reservations[] = [
                            'table_code' => $row['table_code'],
                            'status' => $row['final_status']
                        ];
                    }
                    
                    echo json_encode([
                        'status' => 'success',
                        'data' => $reservations
                    ]);
                    
                } catch (Exception $e) {
                    error_log("Error in fetch_reservations_for_tables: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                }
                break;

            case 'get_realtime_stats':
                $stats = [];
                
                $totalStmt = $db->conn->prepare("SELECT COUNT(*) as count FROM reservations");
                $totalStmt->execute();
                $totalResult = $totalStmt->get_result();
                $stats['total_reservations'] = $totalResult->fetch_assoc()['count'];
                
                $pendingStmt = $db->conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'");
                $pendingStmt->execute();
                $pendingResult = $pendingStmt->get_result();
                $stats['pending_reservations'] = $pendingResult->fetch_assoc()['count'];
                
                $confirmedStmt = $db->conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = 'confirmed'");
                $confirmedStmt->execute();
                $confirmedResult = $confirmedStmt->get_result();
                $stats['confirmed_reservations'] = $confirmedResult->fetch_assoc()['count'];
                
                $completedStmt = $db->conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE status = 'completed'");
                $completedStmt->execute();
                $completedResult = $completedStmt->get_result();
                $stats['completed_reservations'] = $completedResult->fetch_assoc()['count'];
                
                $salesStmt = $db->conn->prepare("SELECT SUM(grand_total) as total FROM reservations WHERE status = 'confirmed'");
                $salesStmt->execute();
                $salesResult = $salesStmt->get_result();
                $stats['total_sales'] = $salesResult->fetch_assoc()['total'] ?? 0;
                
                $unreadNotifications = 0;
                if ($pdo) {
                    try {
                        $tableCheck = $pdo->query("SHOW TABLES LIKE 'notifications'");
                        if ($tableCheck->rowCount() > 0) {
                            $notifStmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
                            $notifStmt->execute();
                            $notifResult = $notifStmt->fetch(PDO::FETCH_ASSOC);
                            $unreadNotifications = $notifResult['count'];
                        }
                    } catch (Exception $e) {
                        error_log("Error counting notifications: " . $e->getMessage());
                    }
                }
                
                $stats['unread_notifications'] = $unreadNotifications;
                
                echo json_encode([
                    'success' => true,
                    'data' => $stats
                ]);
                break;

            case "checkAvailability":
                $table_code = $_GET['table_code'];
                $date_schedule = $_GET['date_schedule'];
                $time_schedule = $_GET['time_schedule'];
                
                $checkStmt = $pdo->prepare("SELECT * FROM reservations WHERE table_code = ? AND date_schedule = ? AND time_schedule = ? AND status IN ('pending', 'confirmed')");
                $checkStmt->execute([$table_code, $date_schedule, $time_schedule]);
                
                if ($checkStmt->rowCount() > 0) {
                    echo json_encode(['availability' => false]);
                } else {
                    echo json_encode(['availability' => true]);
                }
                break;

            case "get_realtime_table_status":
                $date = $_GET['date'] ?? date('Y-m-d');
                $tableStatusMap = [];
                
                $walkinStmt = $pdo->prepare("SELECT walkin_table_code, walkin_status FROM walkin_tables WHERE DATE(walkin_created_at) = ? OR walkin_date = ?");
                $walkinStmt->execute([$date]);
                $walkinTables = $walkinStmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($walkinTables as $table) {
                    $tableStatusMap[$table['walkin_table_code']] = $table['walkin_status'];
                }
                
                $reservationStmt = $pdo->prepare("
                    SELECT table_code, status 
                    FROM reservations 
                    WHERE date_schedule = ?
                    AND status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule', 'completed')
                    AND table_code IS NOT NULL
                    AND table_code != ''
                ");
                $reservationStmt->execute([$date]);
                $reservations = $reservationStmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($reservations as $reservation) {
                    $tableCode = $reservation['table_code'];
                    $reservationStatus = $reservation['status'];
                    $tableStatusMap[$tableCode] = $reservationStatus;
                }
                
                echo json_encode(['status' => 'success', 'data' => $tableStatusMap]);
                break;

            case "fetch_alcoholic_drinks":
                try {
                    $stmt = $pdo->prepare("SELECT * FROM alcoholic_drinks WHERE is_available = 1 ORDER BY drink_name");
                    $stmt->execute();
                    $drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo json_encode([
                        'status' => 'success',
                        'data' => $drinks
                    ]);
                } catch (PDOException $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error fetching drinks: ' . $e->getMessage()
                    ]);
                }
                break;

            default:
                sendError('Invalid GET request type.', 404);
                break;
        }

    } else {
        sendError('Method not allowed.', 405);
    }

} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendError('Internal server error', 500);
}
?>