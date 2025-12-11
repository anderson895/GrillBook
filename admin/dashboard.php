<?php
include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";

$host = 'localhost';
$dbname = 'u777088444_grillbook';
$username = 'u777088444_grillbook';
$password = 'Grillbook123@';

$tableStatusMap = [];
$dashboardStats = [
    'total_reservations' => 0,
    'pending_reservations' => 0,
    'confirmed_reservations' => 0,
    'total_sales' => 0
];
$today = date('Y-m-d');

$businessHours = [];
$emergencyStatus = null;
$holidayStatus = null;
$emergencyActive = false;
$holidayActive = false;
$todayClosure = false;
$todayClosureReason = '';
$todayClosureType = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $holidaySql = "SELECT * FROM holiday_schedules 
                   WHERE holiday_date >= ? 
                   AND status = 'active'
                   ORDER BY holiday_date ASC LIMIT 1";
    $holidayStmt = $pdo->prepare($holidaySql);
    $holidayStmt->execute([$today]);
    $holidayStatus = $holidayStmt->fetch(PDO::FETCH_ASSOC);
    $holidayActive = ($holidayStatus !== false);

    $todayHolidaySql = "SELECT * FROM holiday_schedules 
                        WHERE holiday_date = ? 
                        AND status = 'active'
                        LIMIT 1";
    $todayHolidayStmt = $pdo->prepare($todayHolidaySql);
    $todayHolidayStmt->execute([$today]);
    $todayHoliday = $todayHolidayStmt->fetch(PDO::FETCH_ASSOC);

    $emergencySql = "SELECT * FROM emergency_closures 
                     WHERE closure_date >= ? 
                     AND status = 'active'
                     ORDER BY closure_date DESC LIMIT 1";
    $emergencyStmt = $pdo->prepare($emergencySql);
    $emergencyStmt->execute([$today]);
    $emergencyStatus = $emergencyStmt->fetch(PDO::FETCH_ASSOC);

    $todayEmergencySql = "SELECT * FROM emergency_closures 
                          WHERE closure_date = ? 
                          AND status = 'active'
                          LIMIT 1";
    $todayEmergencyStmt = $pdo->prepare($todayEmergencySql);
    $todayEmergencyStmt->execute([$today]);
    $todayEmergency = $todayEmergencyStmt->fetch(PDO::FETCH_ASSOC);

    $systemStatusSql = "SELECT * FROM system_status WHERE status_key = 'emergency_mode'";
    $systemStatusStmt = $pdo->query($systemStatusSql);
    $systemStatus = $systemStatusStmt->fetch(PDO::FETCH_ASSOC);

    $emergencyActive = false;
    if ($todayEmergency !== false || ($systemStatus && $systemStatus['status_value'] === 'true')) {
        $emergencyActive = true;
        if ($emergencyStatus !== false && $todayEmergency === false) {
            $emergencyDate = new DateTime($emergencyStatus['closure_date']);
            $todayDate = new DateTime($today);
            if ($emergencyDate <= $todayDate) {
                $emergencyActive = true;
                $emergencyStatus = $todayEmergency = $emergencyStatus;
            }
        }
    }

    $systemClosed = false;
    $closureReason = '';
    $closureType = '';

    if ($emergencyActive) {
        $systemClosed = true;
        $closureReason = isset($emergencyStatus['reason']) ? $emergencyStatus['reason'] : 'Emergency Closure';
        $closureType = 'emergency';
    } elseif ($holidayActive) {
        $systemClosed = true;
        $closureReason = isset($holidayStatus['holiday_name']) ? $holidayStatus['holiday_name'] : 'Holiday';
        $closureType = 'holiday';
    }

    $todayClosure = false;
    $todayClosureReason = '';
    $todayClosureType = '';

    if ($todayEmergency !== false) {
        $todayClosure = true;
        $todayClosureReason = $todayEmergency['reason'];
        $todayClosureType = 'emergency';
    } elseif ($todayHoliday !== false) {
        $todayClosure = true;
        $todayClosureReason = $todayHoliday['holiday_name'];
        $todayClosureType = 'holiday';
    }
    
    $sql = "SELECT table_code, status, customer_name, date_schedule, time_schedule, seats, grand_total, corkage_fee
            FROM reservations 
            WHERE date_schedule = ?
            AND status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule', 'walkin', 'completed')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$today]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($reservations as $reservation) {
        if (!empty($reservation['table_code'])) {
            $tableStatusMap[$reservation['table_code']] = $reservation['status'];
        }
    }
    
    $walkinSql = "SELECT walkin_table_code, walkin_status FROM walkin_tables WHERE DATE(walkin_created_at) = ?";
    $walkinStmt = $pdo->prepare($walkinSql);
    $walkinStmt->execute([$today]);
    $walkinTables = $walkinStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($walkinTables as $walkin) {
        if (!empty($walkin['walkin_table_code'])) {
            if (!isset($tableStatusMap[$walkin['walkin_table_code']])) {
                $tableStatusMap[$walkin['walkin_table_code']] = $walkin['walkin_status'];
            }
        }
    }
        
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_reservations,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reservations,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_reservations,
            COALESCE(SUM(CASE WHEN status IN ('confirmed', 'completed') THEN grand_total ELSE 0 END), 0) as total_sales
        FROM reservations 
        WHERE DATE(date_schedule) >= CURDATE()
    ");
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($stats) {
        $dashboardStats = [
            'total_reservations' => $stats['total_reservations'] ?? 0,
            'pending_reservations' => $stats['pending_reservations'] ?? 0,
            'confirmed_reservations' => $stats['confirmed_reservations'] ?? 0,
            'total_sales' => $stats['total_sales'] ?? 0
        ];
    }
    
    $hoursStmt = $pdo->query("SELECT * FROM business_hours ORDER BY id");
    $businessHours = $hoursStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $tableStatusMap = [];
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function getStatusClass($status) {
    switch($status) {
        case 'confirmed':
            return 'bg-success';
        case 'pending':
            return 'bg-warning';
        case 'cancelled':
            return 'bg-danger';
        case 'request_cancel':
            return 'bg-orange';
        case 'request_reschedule':
            return 'bg-info';
        case 'unavailable':
            return 'bg-gray';
        case 'facility':
            return 'facility-item';
        case 'walkin':
            return 'bg-purple';
        case 'completed':
            return 'bg-secondary';
        default:
            return 'bg-white';
    }
}

function isNonReservableFacility($tableName) {
    $nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];
    return in_array($tableName, $nonReservableFacilities);
}

function getStatusText($status) {
    switch($status) {
        case 'confirmed':
            return 'Confirmed';
        case 'pending':
            return 'Pending';
        case 'cancelled':
            return 'Cancelled';
        case 'request_cancel':
            return 'Cancel Request';
        case 'request_reschedule':
            return 'Reschedule Request';
        case 'unavailable':
            return 'Unavailable';
        case 'walkin':
            return 'Walk-in';
        case 'completed':
            return 'Completed';
        default:
            return 'Available';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GrillBook</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #D4AF37;
            --dark-gold: #B8860B;
            --light-gold: #F5E8C8;
            --dark-bg: #0A0A0A;
            --card-bg: #1A1A1A;
            --text-light: #E5E5E5;
            --text-muted: #A3A3A3;
            --grill-dark: #2A2A2A;
            --grill-light: #3A3A3A;
            --emergency-red: #DC2626;
            --emergency-dark: #B91C1C;
            --emergency-orange: #F59E0B;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1a1a 100%);
            min-height: 100vh;
            font-family: 'Arial', 'Helvetica', sans-serif;
            color: var(--text-light);
            margin: 0;
            padding: 0;
        }

        .scrollbar-hidden {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hidden::-webkit-scrollbar {
            display: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes glow {
            0% {
                box-shadow: 0 0 5px currentColor;
            }
            50% {
                box-shadow: 0 0 20px currentColor;
            }
            100% {
                box-shadow: 0 0 5px currentColor;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        @keyframes emergencyPulse {
            0%, 100% { 
                background: linear-gradient(135deg, #DC2626, #B91C1C);
                box-shadow: 0 0 10px rgba(220, 38, 38, 0.5);
            }
            50% { 
                background: linear-gradient(135deg, #F59E0B, #D97706);
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.7);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease-out forwards;
            opacity: 0;
        }

        .animate-pulse-custom {
            animation: pulse 2s infinite;
        }

        .animate-glow {
            animation: glow 2s infinite;
        }

        .animate-slideIn {
            animation: slideIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        .animate-emergency-pulse {
            animation: emergencyPulse 1.5s infinite;
        }

        .font-readable {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
        }

        .grill-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .grill-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(90deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px),
                linear-gradient(0deg, transparent 24px, var(--grill-dark) 25px, var(--grill-dark) 26px, transparent 27px, transparent 49px);
            background-size: 50px 50px;
            opacity: 0.1;
        }

        .grill-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(184, 134, 11, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(212, 175, 55, 0.08) 0%, transparent 50%);
        }

        .emergency-banner {
            background: linear-gradient(135deg, var(--emergency-red), var(--emergency-dark));
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            animation: slideIn 0.6s ease-out, emergencyPulse 1.5s infinite;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(220, 38, 38, 0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            z-index: 1000;
        }

        .emergency-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 25%, rgba(255, 255, 255, 0.1) 50%, transparent 75%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .emergency-banner-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            z-index: 1;
        }

        .emergency-banner h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .emergency-banner p {
            margin: 0.5rem 0 0 0;
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 500;
        }

        .emergency-banner .warning-icon {
            font-size: 2.5rem;
            animation: pulse 1s infinite;
        }

        .btn-restore {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 1;
            position: relative;
            overflow: hidden;
        }

        .btn-restore::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
        }

        .btn-restore:hover::before {
            left: 100%;
        }

        .btn-restore:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .emergency-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(220, 38, 38, 0.1);
            z-index: 9998;
            pointer-events: none;
            animation: emergencyPulse 3s infinite;
        }

        .emergency-status {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--emergency-red), var(--emergency-dark));
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.4);
            z-index: 10000;
            animation: emergencyPulse 2s infinite;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .emergency-status.active {
            display: flex;
        }

        .emergency-modal-warning {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, var(--emergency-red), #B91C1C);
            color: white;
            padding: 3rem;
            border-radius: 20px;
            z-index: 10001;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8);
            border: 4px solid rgba(255, 255, 255, 0.2);
            animation: emergencyPulse 1.5s infinite;
            max-width: 600px;
            width: 90%;
        }

        .emergency-modal-warning.active {
            display: block;
        }

        .emergency-modal-warning h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .emergency-modal-warning p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .emergency-modal-warning .btn-restore-modal {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            padding: 1rem 3rem;
            border-radius: 10px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.1rem;
        }

        .emergency-modal-warning .btn-restore-modal:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }

        .interactive-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            opacity: 0;
            transform: translateY(30px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--card-bg) 0%, var(--grill-dark) 100%);
        }
        
        .interactive-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
            transition: left 0.6s;
        }
        
        .interactive-card:hover::before {
            left: 100%;
        }
        
        .interactive-card.loaded {
            opacity: 1;
            transform: translateY(0);
        }
        
        .interactive-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 25px 50px rgba(212, 175, 55, 0.2),
                0 0 0 1px rgba(212, 175, 55, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .stats-card {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid rgba(212, 175, 55, 0.25);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            background-image: 
                linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%),
                linear-gradient(-45deg, transparent 49%, rgba(212, 175, 55, 0.03) 50%, transparent 51%);
            background-size: 20px 20px;
        }
        
        .stats-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(212, 175, 55, 0.05) 0%,
                transparent 30%,
                transparent 70%,
                rgba(212, 175, 55, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .stats-card:hover::after {
            opacity: 1;
        }
        
        .stats-card:hover {
            border-color: rgba(212, 175, 55, 0.5);
            box-shadow: 
                0 20px 40px rgba(212, 175, 55, 0.15),
                inset 0 1px 0 rgba(212, 175, 55, 0.1);
            transform: translateY(-5px);
        }

        .btn-grill {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            color: var(--dark-bg);
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 
                0 4px 15px rgba(212, 175, 55, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-grill::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.6s;
        }

        .btn-grill:hover::before {
            left: 100%;
        }

        .btn-grill:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 15px 30px rgba(212, 175, 55, 0.4),
                0 0 20px rgba(212, 175, 55, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            color: var(--dark-bg);
        }

        .btn-holiday {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(5, 150, 105, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 
                0 4px 15px rgba(5, 150, 105, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-disaster {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(220, 38, 38, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 
                0 4px 15px rgba(220, 38, 38, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Button group layout for modal forms */
        .booking-form-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        /* Cancel button style to match modal layout */
        .booking-cancel-btn {
            background: transparent;
            color: var(--text-light);
            padding: 0.9rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 2px solid rgba(255,255,255,0.06);
            cursor: pointer;
            font-size: 1.05rem;
            transition: all 0.18s ease;
        }

        .booking-cancel-btn:hover {
            background: rgba(255,255,255,0.03);
            transform: translateY(-2px);
        }

        .form-input-grill {
            background: rgba(26, 26, 26, 0.8);
            border: 2px solid rgba(212, 175, 55, 0.3);
            color: var(--text-light);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1.1rem;
            background-image: 
                linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.05) 50%, transparent 51%);
            background-size: 15px 15px;
        }

        .form-input-grill::placeholder {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-input-grill:focus {
            outline: none;
            border-color: var(--primary-gold);
            box-shadow: 
                0 0 0 3px rgba(212, 175, 55, 0.2),
                0 0 20px rgba(212, 175, 55, 0.1),
                inset 0 0 10px rgba(212, 175, 55, 0.05);
            transform: translateY(-2px);
            background: rgba(26, 26, 26, 0.95);
        }

        .form-input-grill:hover {
            border-color: rgba(212, 175, 55, 0.5);
            background: rgba(26, 26, 26, 0.9);
        }

        .table-grid-compact {
            background: var(--card-bg);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: repeat(11, 1fr);
            gap: 4px;
            min-width: 800px;
            min-height: 600px;
        }

        .table-item {
            background: white;
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            padding: 0.75rem 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 60px;
            color: #212529;
            position: relative;
            overflow: hidden;
        }
        
        .table-item:hover {
            transform: translateY(-3px);
            border-color: var(--primary-gold);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2), 0 0 12px rgba(212, 175, 55, 0.1);
        }

        .table-item.active {
            animation: glow 2s infinite !important;
        }

        .bg-success { 
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            color: white !important;
            border-color: #16a34a !important;
        }
        
        .bg-success.active {
            box-shadow: 0 0 15px #16a34a;
            border-color: #86efac !important;
        }
        
        .bg-warning { 
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white !important;
            border-color: #f59e0b !important;
        }
        
        .bg-warning.active {
            box-shadow: 0 0 15px #f59e0b;
            border-color: #fde68a !important;
        }
        
        .bg-danger { 
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: white !important;
            border-color: #dc2626 !important;
        }
        
        .bg-danger.active {
            box-shadow: 0 0 15px #dc2626;
            border-color: #fca5a5 !important;
        }
        
        .bg-info { 
            background: linear-gradient(135deg, #9333ea, #7c3aed) !important;
            color: white !important;
            border-color: #9333ea !important;
        }
        
        .bg-info.active {
            box-shadow: 0 0 15px #9333ea;
            border-color: #d8b4fe !important;
        }
        
        .bg-white { 
            background: white !important;
            color: #212529 !important;
            border-color: #d1d5db !important;
        }
        
        .bg-white.active {
            box-shadow: 0 0 15px #ffffff;
            border-color: #ffffff !important;
        }

        .bg-orange { 
            background: linear-gradient(135deg, #ea580c, #c2410c) !important;
            color: white !important;
            border-color: #ea580c !important;
        }
        
        .bg-orange.active {
            box-shadow: 0 0 15px #ea580c;
            border-color: #fdba74 !important;
        }

        .bg-gray { 
            background: linear-gradient(135deg, #6b7280, #4b5563) !important;
            color: white !important;
            border-color: #6b7280 !important;
        }
        
        .bg-gray.active {
            box-shadow: 0 0 15px #6b7280;
            border-color: #d1d5db !important;
        }

        .bg-purple { 
            background: linear-gradient(135deg, #8B5CF6, #7C3AED) !important;
            color: white !important;
            border-color: #8B5CF6 !important;
        }
        
        .bg-purple.active {
            box-shadow: 0 0 15px #8B5CF6;
            border-color: #C4B5FD !important;
        }

        .bg-secondary { 
            background: linear-gradient(135deg, #6B7280, #4B5563) !important;
            color: white !important;
            border-color: #6B7280 !important;
        }
        
        .bg-secondary.active {
            box-shadow: 0 0 15px #6B7280;
            border-color: #D1D5DB !important;
        }

        .text-white { color: white !important; }
        .text-dark { color: #212529 !important; }

        .table-item.facility-item {
            cursor: default !important;
            background: linear-gradient(135deg, #6B7280, #4B5563) !important;
            border-color: #9CA3AF !important;
            color: white !important;
        }

        .table-item.facility-item:hover {
            transform: none;
            box-shadow: none;
            border-color: #9CA3AF !important;
        }

        .col-start-1 { grid-column-start: 1; }
        .col-start-2 { grid-column-start: 2; }
        .col-start-3 { grid-column-start: 3; }
        .col-start-4 { grid-column-start: 4; }
        .col-start-5 { grid-column-start: 5; }
        .col-start-6 { grid-column-start: 6; }
        .col-start-7 { grid-column-start: 7; }
        .col-start-8 { grid-column-start: 8; }
        .col-start-9 { grid-column-start: 9; }
        .col-start-10 { grid-column-start: 10; }
        .col-start-11 { grid-column-start: 11; }
        .col-start-12 { grid-column-start: 12; }
        
        .row-start-1 { grid-row-start: 1; }
        .row-start-2 { grid-row-start: 2; }
        .row-start-3 { grid-row-start: 3; }
        .row-start-4 { grid-row-start: 4; }
        .row-start-5 { grid-row-start: 5; }
        .row-start-6 { grid-row-start: 6; }
        .row-start-7 { grid-row-start: 7; }
        .row-start-8 { grid-row-start: 8; }
        .row-start-9 { grid-row-start: 9; }
        .row-start-10 { grid-row-start: 10; }
        .row-start-11 { grid-row-start: 11; }
        
        .col-span-2 { grid-column-end: span 2; }
        .row-span-2 { grid-row-end: span 2; }
        .col-span-3 { grid-column-end: span 3; }
        .row-span-3 { grid-row-end: span 3; }

        .modal-grill {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 10000;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9);
            display: none;
        }

        .modal-grill.active {
            display: block;
        }

        .text-high-contrast {
            color: var(--text-light);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }

        .text-gold-contrast {
            color: var(--primary-gold);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .stats-label {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10000;
        }

        .notification-bell:hover {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #EF4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            animation: pulse 2s infinite;
            z-index: 10001;
            display: none;
        }

        .notification-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            background: var(--card-bg);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            width: 400px;
            max-height: 500px;
            overflow-y: auto;
            z-index: 10002;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
            display: none;
        }

        .notification-dropdown.active {
            display: block;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            background: rgba(212, 175, 55, 0.1);
        }

        .notification-header h3 {
            color: var(--primary-gold);
            font-weight: 600;
            margin: 0;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            cursor: default;
            transition: all 0.3s ease;
            position: relative;
        }

        .notification-item:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background: rgba(212, 175, 55, 0.05);
            border-left: 3px solid var(--primary-gold);
        }

        .notification-title {
            font-weight: 600;
            color: var(--primary-gold);
            margin-bottom: 0.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-message {
            color: var(--text-light);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .notification-time {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        .notification-type {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            margin-left: 0.5rem;
        }

        .notification-type.reservation {
            background: rgba(22, 163, 74, 0.2);
            color: #16a34a;
        }

        .notification-type.payment {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .notification-type.system {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
        }

        .notification-type.alert {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .success-notification {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            font-size: 1.1rem;
            font-weight: 600;
            z-index: 10004;
            position: fixed;
            top: 20px;
            right: 20px;
            display: none;
        }

        .success-notification.show {
            display: block;
        }

        .new-notification-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
            z-index: 10003;
            animation: fadeInUp 0.5s ease-out;
            border-left: 4px solid #fbbf24;
            display: none;
        }

        .new-notification-alert.show {
            display: block;
        }

        .email-modal {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            z-index: 10000;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9);
        }

        .email-modal.active {
            display: block;
        }

        .email-modal h3 {
            color: var(--primary-gold);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .email-modal .modal-body {
            padding: 1rem 0;
        }

        .hours-config-card {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .hours-table {
            width: 100%;
            border-collapse: collapse;
        }

        .hours-table th {
            background: rgba(212, 175, 55, 0.1);
            color: var(--primary-gold);
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .hours-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            color: var(--text-light);
        }

        .hours-table tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .cutoff-badge {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            display: none;
        }

        .modal-overlay.active {
            display: block;
        }

        .date-picker-container {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .legend-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(26, 26, 26, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .simple-table-modal {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            z-index: 10000;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9);
        }

        .simple-table-modal.active {
            display: block;
        }

        .simple-table-modal .modal-content {
            padding: 2rem;
        }

        .simple-table-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .simple-table-modal .modal-header h3 {
            color: var(--primary-gold);
            font-size: 1.5rem;
            margin: 0;
        }

        .simple-table-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .simple-table-btn {
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .simple-table-btn.available {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }

        .simple-table-btn.unavailable {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            color: white;
        }

        .simple-table-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .admin-footer {
            background: var(--dark-bg);
            color: var(--text-muted);
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            margin-top: 3rem;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-gold);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .footer-info {
            display: flex;
            gap: 2rem;
            font-size: 0.9rem;
        }

        .footer-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .footer-info {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            .stats-number {
                font-size: 2rem;
            }
            
            .stats-label {
                font-size: 1rem;
            }
            
            .table-grid-compact {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 2px;
                padding: 10px;
                min-width: auto;
                min-height: auto;
            }

            .table-item {
                min-height: 50px;
                padding: 8px 4px;
                font-size: 0.8rem;
            }

            .notification-dropdown {
                width: 320px;
                right: -80px;
            }

            .hours-table {
                font-size: 0.9rem;
            }

            .hours-table th,
            .hours-table td {
                padding: 0.75rem 0.5rem;
            }

            .modal-grill {
                max-width: 95%;
                margin: 1rem;
            }

            .emergency-banner {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding: 1rem;
            }

            .emergency-banner-content {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .btn-restore {
                width: 100%;
                justify-content: center;
            }

            .emergency-status {
                top: 10px;
                right: 10px;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .emergency-modal-warning {
                padding: 2rem 1rem;
                width: 95%;
            }

            .emergency-modal-warning h2 {
                font-size: 1.5rem;
            }

            .emergency-modal-warning p {
                font-size: 1rem;
            }
        }

        button:focus, 
        select:focus, 
        input:focus {
            outline: 3px solid var(--primary-gold);
            outline-offset: 2px;
        }

        ::selection {
            background: var(--primary-gold);
            color: var(--dark-bg);
        }

        .loading-spinner {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 3px solid rgba(212, 175, 55, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-gold);
            animation: spin 1s ease-in-out infinite;
            margin: 2rem auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .no-data {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .no-data .material-icons {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: rgba(212, 175, 55, 0.3);
        }

        .emergency-notice {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: emergencyPulse 2s infinite;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .emergency-notice .material-icons {
            font-size: 1.5rem;
            animation: pulse 1s infinite;
        }

        .holiday-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .holiday-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(26, 26, 26, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .holiday-option:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: var(--primary-gold);
        }

        .holiday-option.selected {
            background: rgba(212, 175, 55, 0.15);
            border-color: var(--primary-gold);
        }

        .holiday-option input[type="radio"] {
            accent-color: var(--primary-gold);
        }

        .holiday-option-label {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .holiday-option-title {
            font-weight: 600;
            color: var(--primary-gold);
        }

        .holiday-option-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .delayed-hours-container {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(26, 26, 26, 0.3);
            border-radius: 8px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            display: none;
        }

        .delayed-hours-container.active {
            display: block;
        }
        
        .table-status-badge {
            font-size: 0.7rem;
            margin-top: 0.25rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .status-confirmed-badge {
            background: rgba(22, 163, 74, 0.2);
            color: #16a34a;
            border: 1px solid rgba(22, 163, 74, 0.3);
        }
        
        .status-pending-badge {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .status-cancelled-badge {
            background: rgba(220, 38, 38, 0.2);
            color: #dc2626;
            border: 1px solid rgba(220, 38, 38, 0.3);
        }
        
        .status-request-cancel-badge {
            background: rgba(234, 88, 12, 0.2);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.3);
        }
        
        .status-request-reschedule-badge {
            background: rgba(147, 51, 234, 0.2);
            color: #9333ea;
            border: 1px solid rgba(147, 51, 234, 0.3);
        }
        
        .status-unavailable-badge {
            background: rgba(107, 114, 128, 0.2);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }
        
        .status-available-badge {
            background: rgba(229, 229, 229, 0.2);
            color: #212529;
            border: 1px solid rgba(209, 213, 219, 0.3);
        }
        
        .status-walkin-badge {
            background: rgba(139, 92, 246, 0.2);
            color: #8B5CF6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        
        .status-completed-badge {
            background: rgba(107, 114, 128, 0.2);
            color: #6B7280;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
<div class="grill-background">
    <div class="grill-pattern"></div>
    <div class="grill-overlay"></div>
</div>

<?php if ($emergencyActive): ?>
<div class="emergency-overlay"></div>
<?php endif; ?>

<div class="modal-overlay" id="modalOverlay"></div>

<div id="emergencyWarningModal" class="emergency-modal-warning">
    <div class="warning-icon">
        <span class="material-icons" style="font-size: 4rem; margin-bottom: 1rem;">warning</span>
    </div>
    <h2>🚨 EMERGENCY SYSTEM SHUTDOWN 🚨</h2>
    <p id="emergencyWarningMessage">
        The system is currently in emergency shutdown mode. 
        All reservations are suspended until further notice.
    </p>
    <p style="font-size: 1rem; margin-top: 1rem; opacity: 0.8;">
        Date: <?php echo date('F j, Y', strtotime($emergencyStatus['closure_date'] ?? date('Y-m-d'))); ?>
    </p>
    <div style="margin-top: 2rem;">
        <button class="btn-restore-modal" onclick="restoreSystemAccess()">
            <span class="material-icons">restore</span>
            RESTORE SYSTEM ACCESS
        </button>
    </div>
</div>

<div id="newNotificationAlert" class="new-notification-alert">
    <div class="flex items-center">
        <span class="material-icons mr-2">notifications_active</span>
        <div>
            <strong>New Reservation!</strong>
            <div id="alertMessage" class="text-sm"></div>
        </div>
    </div>
</div>

<?php if ($todayClosure): ?>
<div class="emergency-banner" style="background: linear-gradient(135deg, <?php echo $todayClosureType === 'emergency' ? 'var(--emergency-red), var(--emergency-dark)' : 'var(--primary-gold), var(--dark-gold)'; ?>);">
    <div class="emergency-banner-content">
        <span class="material-icons warning-icon"><?php echo $todayClosureType === 'emergency' ? 'warning' : 'beach_access'; ?></span>
        <div>
            <h3>🚨 <?php echo $todayClosureType === 'emergency' ? 'EMERGENCY SYSTEM SHUTDOWN' : 'HOLIDAY NOTICE'; ?> 🚨</h3>
            <p>
                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($today)); ?> | 
                <strong>Reason:</strong> <?php echo htmlspecialchars($todayClosureReason); ?> |
                <strong>Type:</strong> <?php echo ucfirst($todayClosureType); ?>
            </p>
            <p style="margin-top: 0.5rem; font-size: 0.95rem; opacity: 0.9;">
                ⚠️ All reservations are suspended. The system is in <?php echo $todayClosureType === 'emergency' ? 'emergency shutdown' : 'holiday'; ?> mode.
            </p>
        </div>
    </div>
    <?php if ($todayClosureType === 'emergency'): ?>
    <button class="btn-restore" onclick="showEmergencyWarning()">
        <span class="material-icons">restore</span>
        RESTORE SYSTEM ACCESS
    </button>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="flex justify-between items-center bg-[#0D0D0D] p-4 mb-6 rounded-md shadow-lg border-2 border-[#D4AF37]">
  <h2 class="text-xl font-bold text-[#FFD700] uppercase tracking-wide font-readable">Dashboard</h2>
  <div class="flex items-center space-x-4">
    <div class="notification-bell relative" id="notificationBell">
      <span class="material-icons text-[#FFD700] text-2xl">notifications</span>
      <div class="notification-badge" id="notificationBadge">0</div>
      <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
          <h3>Notifications</h3>
          <button id="markAllRead" class="text-sm text-[#FFD700] hover:text-yellow-400">Mark all as read</button>
        </div>
        <div id="notificationList">
          <div class="notification-item">
            <div class="notification-message">Loading notifications...</div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="flex space-x-2">
        <button class="btn-holiday" onclick="showHolidayModal()">
            <span class="material-icons">beach_access</span>
            Holiday Notice
        </button>
        <button class="btn-disaster" onclick="showDisasterModal()">
            <span class="material-icons">warning</span>
            Emergency Closure
        </button>
    </div>
    
    <div class="flex items-center space-x-2">
      <span class="text-gray-300 font-readable">Filter:</span>
      <select id="dateFilter" class="form-input-grill py-2">
        <option value="today">Today</option>
        <option value="week">This Week</option>
        <option value="month">This Month</option>
        <option value="year">This Year</option>
        <option value="all">All Time</option>
      </select>
    </div>
  </div>
</div>

<div class="p-6 bg-transparent min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-[#FFD700] flex items-center space-x-2 font-readable">
    <span class="material-icons text-[#FFD700] text-3xl">insert_chart</span>
    <span>Admin Dashboard</span>
    </h1>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stats-card interactive-card animate-fadeInUp" data-type="reservations">
      <div class="flex items-center space-x-4">
        <span class="material-icons text-[#FFD700] text-4xl">event</span>
        <div>
          <p class="text-gray-300 stats-label font-readable">Total Reservations</p>
          <h2 class="stats-number font-bold text-white text-high-contrast" id="totalReservations"><?php echo sanitizeInput($dashboardStats['total_reservations']); ?></h2>
          <p class="text-sm text-green-400 font-readable" id="reservationsChange">+0%</p>
        </div>
      </div>
    </div>
    
    <div class="stats-card interactive-card animate-fadeInUp" data-type="pending" style="animation-delay: 0.1s" onclick="window.location.href='reserve_request.php'">
      <div class="flex items-center space-x-4">
        <span class="material-icons text-[#FFD700] text-4xl">hourglass_empty</span>
        <div>
          <p class="text-gray-300 stats-label font-readable">Pending Reservations</p>
          <h2 class="stats-number font-bold text-yellow-400 text-high-contrast" id="pendingReservations"><?php echo sanitizeInput($dashboardStats['pending_reservations']); ?></h2>
          <p class="text-sm text-yellow-400 font-readable" id="pendingChange">+0%</p>
        </div>
      </div>
    </div>
    
    <div class="stats-card interactive-card animate-fadeInUp" data-type="confirmed" style="animation-delay: 0.2s">
      <div class="flex items-center space-x-4">
        <span class="material-icons text-[#FFD700] text-4xl">check_circle</span>
        <div>
          <p class="text-gray-300 stats-label font-readable">Confirmed Reservations</p>
          <h2 class="stats-number font-bold text-green-400 text-high-contrast" id="confirmedReservations"><?php echo sanitizeInput($dashboardStats['confirmed_reservations']); ?></h2>
          <p class="text-sm text-green-400 font-readable" id="confirmedChange">+0%</p>
        </div>
      </div>
    </div>
    
    <div class="stats-card interactive-card animate-fadeInUp" data-type="sales" style="animation-delay: 0.3s">
      <div class="flex items-center space-x-4">
        <span class="material-icons text-[#FFD700] text-4xl">₱</span>
        <div>
          <p class="text-gray-300 stats-label font-readable">Total Sales</p>
          <h2 class="stats-number font-bold text-green-400 text-high-contrast" id="totalSales">₱<?php echo number_format($dashboardStats['total_sales'], 2); ?></h2>
          <p class="text-sm text-green-400 font-readable" id="salesChange">+0%</p>
        </div>
      </div>
    </div>
  </div>

  <div class="hours-config-card interactive-card animate-fadeInUp">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold flex items-center space-x-2 text-white font-readable">
            <span class="material-icons text-[#FFD700]">schedule</span>
            <span>Operation Hours & Booking Rules</span>
        </h2>
        <div class="cutoff-badge">
            <span class="material-icons">access_time</span>
            Cutoff: 1h 30m before
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="hours-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Open Time</th>
                    <th>Close Time</th>
                    <th>Last Booking Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($businessHours as $hour): ?>
                    <?php
                    $openTime = new DateTime($hour['open_time']);
                    $closeTime = new DateTime($hour['close_time']);
                    $lastBookingTime = (new DateTime($hour['close_time']))->modify("-90 minutes");
                    ?>
                    <tr>
                        <td class="font-semibold"><?php echo sanitizeInput($hour['day_of_week']); ?></td>
                        <td><?php echo $openTime->format('g:i A'); ?></td>
                        <td><?php echo $closeTime->format('g:i A'); ?></td>
                        <td><?php echo $lastBookingTime->format('g:i A'); ?></td>
                        <td>
                            <?php if ($emergencyActive): ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white animate-pulse-custom">
                                EMERGENCY CLOSURE
                            </span>
                            <?php elseif ($holidayActive): ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white animate-pulse-custom">
                                HOLIDAY NOTICE
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                Active
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4 p-4 bg-yellow-900/20 border border-yellow-500 rounded-lg">
        <div class="flex items-start space-x-3">
            <span class="material-icons text-yellow-400 text-xl mt-0.5">info</span>
            <div>
                <h4 class="font-semibold text-yellow-400 mb-1">Booking Rules</h4>
                <p class="text-yellow-200 text-sm">• 30-minute booking intervals during operation hours</p>
                <p class="text-yellow-200 text-sm">• No reservations within 90 minutes before closing time</p>
                <p class="text-yellow-200 text-sm">• Same-day bookings must be made 90+ minutes in advance</p>
                <?php if ($emergencyActive): ?>
                <p class="text-red-200 text-sm font-bold mt-2">⚠️ EMERGENCY: System currently suspended due to emergency closure</p>
                <?php elseif ($holidayActive): ?>
                <p class="text-yellow-200 text-sm font-bold mt-2">⚠️ HOLIDAY: System currently suspended due to holiday notice</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
  </div>

  <div class="hours-config-card interactive-card animate-fadeInUp">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold flex items-center space-x-2 text-white font-readable">
            <span class="material-icons text-[#FFD700]">table_chart</span>
            <span>Table Status Dashboard</span>
        </h2>
        <div class="date-picker-container">
            <label for="datePicker" class="block text-[#FFD700] font-semibold mb-2">Select Date:</label>
            <input type="text" class="form-input-grill" id="datePicker" value="<?php echo $today; ?>">
        </div>
    </div>

    <div class="legend-container">
        <div class="legend-item">
            <div class="legend-color bg-success"></div>
            <span class="text-white text-sm">Confirmed</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-warning"></div>
            <span class="text-white text-sm">Pending</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-danger"></div>
            <span class="text-white text-sm">Cancelled</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-info"></div>
            <span class="text-white text-sm">Request Reschedule</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-white"></div>
            <span class="text-white text-sm">Available</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-orange"></div>
            <span class="text-white text-sm">Request Cancel</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-purple"></div>
            <span class="text-white text-sm">Walk-in</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-secondary"></div>
            <span class="text-white text-sm">Completed</span>
        </div>
        <div class="legend-item">
            <div class="legend-color bg-gray"></div>
            <span class="text-white text-sm">Unavailable</span>
        </div>
    </div>

    <div class="overflow-auto scrollbar-hidden">
        <div class="table-grid-compact" id="tableContainer">
            <div class="table-item facility-item bg-gray" style="grid-column: 6 / 7; grid-row: 1;">
                <span class="font-bold text-xs">🚪 ENTRANCE</span>
            </div>
            <div class="table-item facility-item bg-gray" style="grid-column: 7 / 8; grid-row: 1;">
                <span class="font-bold text-xs">🚶 EXIT</span>
            </div>
            
            <?php
            $row2Tables = [
                'G6' => ['col' => 1, 'row' => 2],
                'G5' => ['col' => 2, 'row' => 2],
                'Take out 1' => ['col' => 7, 'row' => 2],
                'Take out 2' => ['col' => 8, 'row' => 2],
                'F3' => ['col' => 11, 'row' => 2],
                'F4' => ['col' => 12, 'row' => 2],
            ];
            
            foreach ($row2Tables as $tableName => $pos) {
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                $statusClass = getStatusClass($status);
                $statusText = getStatusText($status);
                $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"
                         style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                echo htmlspecialchars($tableName);
                echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                echo "</div>";
            }
            ?>
            
            <?php
            $row3Tables = [
                'G4' => ['col' => 1, 'row' => 3],
                'G3' => ['col' => 2, 'row' => 3],
                'E4' => ['col' => 4, 'row' => 3],
                'E8' => ['col' => 5, 'row' => 3],
                'F1' => ['col' => 11, 'row' => 3],
                'F2' => ['col' => 12, 'row' => 3],
            ];
            
            foreach ($row3Tables as $tableName => $pos) {
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                $statusClass = getStatusClass($status);
                $statusText = getStatusText($status);
                $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"
                         style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                echo htmlspecialchars($tableName);
                echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                echo "</div>";
            }
            ?>
            
            <?php
            $row4Tables = [
                'G2' => ['col' => 1, 'row' => 4],
                'G1' => ['col' => 2, 'row' => 4],
                'E3' => ['col' => 4, 'row' => 4],
                'E7' => ['col' => 5, 'row' => 4],
                'C6' => ['col' => 7, 'row' => 4],
                'D6' => ['col' => 8, 'row' => 4],
                'PERFORMANCE' => ['col' => 11, 'row' => 4, 'colspan' => 2, 'rowspan' => 3, 'facility' => true],
            ];
            
            foreach ($row4Tables as $tableName => $pos) {
                $isFacility = isset($pos['facility']) && $pos['facility'];
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                
                if ($isFacility) {
                    $statusClass = 'facility-item bg-gray';
                    $onclick = '';
                    $statusText = 'Facility';
                } else {
                    $statusClass = getStatusClass($status);
                    $statusText = getStatusText($status);
                    $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                    $onclick = "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"";
                }
                
                $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         $onclick
                         style='$colspan $rowspan'>";
                echo htmlspecialchars($tableName);
                if (!$isFacility) {
                    echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                }
                echo "</div>";
            }
            ?>
            
            <?php
            $row5Tables = [
                'E2' => ['col' => 4, 'row' => 5],
                'E6' => ['col' => 5, 'row' => 5],
                'C5' => ['col' => 7, 'row' => 5],
                'D5' => ['col' => 8, 'row' => 5],
            ];
            
            foreach ($row5Tables as $tableName => $pos) {
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                $statusClass = getStatusClass($status);
                $statusText = getStatusText($status);
                $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"
                         style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                echo htmlspecialchars($tableName);
                echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                echo "</div>";
            }
            ?>
            
            <?php
            $row6Tables = [
                'A5' => ['col' => 1, 'row' => 6],
                'B6' => ['col' => 2, 'row' => 6],
                'E1' => ['col' => 4, 'row' => 6],
                'E5' => ['col' => 5, 'row' => 6],
                'C4' => ['col' => 7, 'row' => 6],
                'D4' => ['col' => 8, 'row' => 6],
            ];
            
            foreach ($row6Tables as $tableName => $pos) {
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                $statusClass = getStatusClass($status);
                $statusText = getStatusText($status);
                $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"
                         style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                echo htmlspecialchars($tableName);
                echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                echo "</div>";
            }
            ?>
            
            <?php
            $row7Tables = [
                'A4' => ['col' => 1, 'row' => 7],
                'B5' => ['col' => 2, 'row' => 7],
                'C3' => ['col' => 7, 'row' => 7],
                'D3' => ['col' => 8, 'row' => 7],
                'VIP 3' => ['col' => 11, 'row' => 7],
                'VIP 2' => ['col' => 12, 'row' => 7],
            ];
            
            foreach ($row7Tables as $tableName => $pos) {
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                $statusClass = getStatusClass($status);
                $statusText = getStatusText($status);
                $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"
                         style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                echo htmlspecialchars($tableName);
                echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                echo "</div>";
            }
            ?>
            
            <?php
            $row8Tables = [
                'A3' => ['col' => 1, 'row' => 8],
                'B4' => ['col' => 2, 'row' => 8],
                'RESERV.' => ['col' => 4, 'row' => 8, 'colspan' => 2],
                'C2' => ['col' => 7, 'row' => 8],
                'D2' => ['col' => 8, 'row' => 8],
                'BILLIARDS' => ['col' => 11, 'row' => 8, 'colspan' => 2, 'facility' => true],
            ];
            
            foreach ($row8Tables as $tableName => $pos) {
                $isFacility = isset($pos['facility']) && $pos['facility'];
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                
                if ($isFacility) {
                    $statusClass = 'facility-item bg-gray';
                    $onclick = '';
                    $statusText = 'Facility';
                } else {
                    $statusClass = getStatusClass($status);
                    $statusText = getStatusText($status);
                    $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                    $onclick = "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"";
                }
                
                $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         $onclick
                         style='$colspan $rowspan'>";
                echo htmlspecialchars($tableName);
                if (!$isFacility) {
                    echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                }
                echo "</div>";
            }
            ?>
            
            <?php
            $row9Tables = [
                'A2' => ['col' => 1, 'row' => 9],
                'B3' => ['col' => 2, 'row' => 9],
                'MEETING' => ['col' => 4, 'row' => 9, 'colspan' => 2],
                'C1' => ['col' => 7, 'row' => 9],
                'D1' => ['col' => 8, 'row' => 9],
                'VIP 1' => ['col' => 11, 'row' => 9, 'colspan' => 2],
            ];
            
            foreach ($row9Tables as $tableName => $pos) {
                $isFacility = isset($pos['facility']) && $pos['facility'];
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                
                if ($isFacility) {
                    $statusClass = 'facility-item bg-gray';
                    $onclick = '';
                    $statusText = 'Facility';
                } else {
                    $statusClass = getStatusClass($status);
                    $statusText = getStatusText($status);
                    $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                    $onclick = "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"";
                }
                
                $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         $onclick
                         style='$colspan $rowspan'>";
                echo htmlspecialchars($tableName);
                if (!$isFacility) {
                    echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                }
                echo "</div>";
            }
            ?>
            
            <?php
            $row10Tables = [
                'A1' => ['col' => 1, 'row' => 10],
                'B2' => ['col' => 2, 'row' => 10],
                'COMPLI' => ['col' => 4, 'row' => 10, 'colspan' => 2],
            ];
            
            foreach ($row10Tables as $tableName => $pos) {
                $isFacility = isset($pos['facility']) && $pos['facility'];
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                
                if ($isFacility) {
                    $statusClass = 'facility-item bg-gray';
                    $onclick = '';
                    $statusText = 'Facility';
                } else {
                    $statusClass = getStatusClass($status);
                    $statusText = getStatusText($status);
                    $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                    $onclick = "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"";
                }
                
                $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         $onclick
                         style='$colspan $rowspan'>";
                echo htmlspecialchars($tableName);
                if (!$isFacility) {
                    echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                }
                echo "</div>";
            }
            ?>
            
            <?php
            $row11Tables = [
                'B1' => ['col' => 2, 'row' => 11],
            ];
            
            foreach ($row11Tables as $tableName => $pos) {
                $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                $statusClass = getStatusClass($status);
                $statusText = getStatusText($status);
                $statusBadgeClass = 'status-' . str_replace('_', '-', $status) . '-badge';
                
                echo "<div class='table-item $statusClass' 
                         data-table='" . htmlspecialchars($tableName) . "' 
                         data-status='$status'
                         onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"
                         style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                echo htmlspecialchars($tableName);
                echo "<div class='table-status-badge $statusBadgeClass'>$statusText</div>";
                echo "</div>";
            }
            ?>
            
            <div class="table-item facility-item bg-gray" style="grid-column: 7 / 9; grid-row: 10;">
                <span class="font-bold text-xs">🔔 SERVICE COUNTER</span>
            </div>
            
            <div class="table-item facility-item bg-gray" style="grid-column: 7 / 9; grid-row: 11;">
                <span class="font-bold text-xs">👨‍🍳 KITCHEN AREA</span>
            </div>
        </div>
    </div>
  </div>
</div>

<div id="tableModal" class="simple-table-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="tableModalTitle">Table Actions</h3>
            <button onclick="closeTableModal()" class="text-gray-400 hover:text-white">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="modal-body">
            <p class="text-gray-300 mb-4" id="tableModalSubtitle">Select an action for this table</p>
            <div class="simple-table-buttons">
                <button class="simple-table-btn available" onclick="setTableStatus('available')">
                    <span class="material-icons">check_circle</span>
                    Mark as Available
                </button>
                <button class="simple-table-btn unavailable" onclick="setTableStatus('unavailable')">
                    <span class="material-icons">block</span>
                    Mark as Unavailable
                </button>
            </div>
        </div>
    </div>
</div>

<div id="holidayModal" class="email-modal">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3>Set Holiday Notice</h3>
            <button onclick="closeHolidayModal()" class="text-gray-400 hover:text-white">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="space-y-4">
                <div class="booking-form-group">
                    <label class="booking-form-label">Holiday Date *</label>
                    <input type="date" id="holidayDate" class="form-input-grill" required>
                </div>
                
                <div class="booking-form-group">
                    <label class="booking-form-label">Reason for Holiday *</label>
                    <textarea id="holidayReason" class="form-input-grill" placeholder="Reason for holiday..." rows="3" required></textarea>
                </div>
                
                <div class="holiday-options">
                    <div class="holiday-option" onclick="selectHolidayOption('total_close')">
                        <input type="radio" id="totalClose" name="holidayType" value="total_close">
                        <div class="holiday-option-label">
                            <span class="holiday-option-title">Total Closure</span>
                            <span class="holiday-option-desc">Completely closed for the entire day</span>
                        </div>
                    </div>
                    
                    <div class="holiday-option" onclick="selectHolidayOption('delayed_opening')">
                        <input type="radio" id="delayedOpening" name="holidayType" value="delayed_opening">
                        <div class="holiday-option-label">
                            <span class="holiday-option-title">Delayed Opening Hours</span>
                            <span class="holiday-option-desc">Open later than usual hours</span>
                        </div>
                    </div>
                </div>
                
                <div id="delayedHoursContainer" class="delayed-hours-container">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="booking-form-group">
                            <label class="booking-form-label">New Opening Time</label>
                            <input type="time" id="newOpenTime" class="form-input-grill" value="14:00">
                        </div>
                        <div class="booking-form-group">
                            <label class="booking-form-label">New Closing Time</label>
                            <input type="time" id="newCloseTime" class="form-input-grill" value="22:00">
                        </div>
                    </div>
                </div>
                
                <div class="booking-form-buttons">
                    <button onclick="sendHolidayNotice()" class="btn-holiday flex-1">
                        <span class="material-icons">beach_access</span>
                        Send Holiday Notice
                    </button>
                    <button onclick="closeHolidayModal()" class="booking-cancel-btn flex-1">
                        <span class="material-icons">close</span>
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="disasterModal" class="email-modal">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3>Emergency Closure Notice</h3>
            <button onclick="closeDisasterModal()" class="text-gray-400 hover:text-white">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="space-y-4">
                <div class="booking-form-group">
                    <label class="booking-form-label">Closure Date *</label>
                    <input type="date" id="disasterDate" class="form-input-grill" required>
                </div>
                
                <div class="booking-form-group">
                    <label class="booking-form-label">Reason for Emergency Closure *</label>
                    <textarea id="disasterReason" class="form-input-grill" placeholder="Reason for emergency closure..." rows="3" required></textarea>
                </div>
                
                <div class="booking-form-buttons">
                    <button onclick="sendDisasterNotice()" class="btn-disaster flex-1">
                        <span class="material-icons">warning</span>
                        Send Emergency Notice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="successNotification" class="success-notification">
    <div class="flex items-center space-x-3">
        <span class="material-icons text-2xl">check_circle</span>
        <div>
            <h4 class="font-bold">Success!</h4>
            <p id="successMessage">Operation completed successfully.</p>
        </div>
    </div>
</div>

<footer class="admin-footer">
    <div class="footer-content">
        <div class="footer-logo">
            <span class="material-icons">restaurant</span>
            <span>GrillBook Admin</span>
        </div>
        <div class="footer-info">
            <span>
                <span class="material-icons">event</span>
                <?php echo date('F j, Y'); ?>
            </span>
            <span>
                <span class="material-icons">schedule</span>
                <?php echo date('h:i A'); ?>
            </span>
            <span>
                <span class="material-icons">admin_panel_settings</span>
                Admin Dashboard
            </span>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
let currentTableName = '';
let currentTableStatus = '';
let selectedDate = '<?php echo $today; ?>';
let holidayType = 'total_close';
let emergencyActive = <?php echo $emergencyActive ? 'true' : 'false'; ?>;
let holidayActive = <?php echo $holidayActive ? 'true' : 'false'; ?>;
let activeHolidayInfo = <?php echo json_encode($holidayStatus ?: null); ?>;

function getBasePath() {
    const currentPath = window.location.pathname;
    const segments = currentPath.split('/');
    
    const cleanSegments = segments.filter(segment => segment !== '');
    
    const adminIndex = cleanSegments.indexOf('admin');
    
    if (adminIndex !== -1) {
        let relativePath = '';
        for (let i = adminIndex + 1; i < cleanSegments.length; i++) {
            relativePath += '../';
        }
        return relativePath + '../';
    } else if (currentPath.includes('/src/')) {
        return '../../';
    } else {
        return '../';
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'confirmed':
            return 'bg-success';
        case 'pending':
            return 'bg-warning';
        case 'cancelled':
            return 'bg-danger';
        case 'request_cancel':
            return 'bg-orange';
        case 'request_reschedule':
            return 'bg-info';
        case 'unavailable':
            return 'bg-gray';
        case 'facility':
            return 'facility-item';
        case 'walkin':
            return 'bg-purple';
        case 'completed':
            return 'bg-secondary';
        default:
            return 'bg-white';
    }
}

function isNonReservableFacility(tableName) {
    const nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];
    return nonReservableFacilities.includes(tableName);
}

function sendDisasterNotice() {
    const date = document.getElementById('disasterDate').value;
    const reason = document.getElementById('disasterReason').value;
    
    if (!date || !reason) {
        Swal.fire({
            title: 'Error',
            text: 'Please fill in all fields',
            icon: 'error',
            confirmButtonColor: '#D4AF37'
        });
        return;
    }

    const basePath = getBasePath();

    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            requestType: "send_emergency_closure",
            date: date,
            reason: reason,
            action: 'activate'
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                showSuccessNotification('Emergency closure notice sent to all users');
                closeDisasterModal();
                
                $.ajax({
                    url: basePath + "controller/end-points/controller.php",
                    method: "POST",
                    data: { 
                        requestType: "update_system_status",
                        status_key: 'emergency_mode',
                        status_value: 'true'
                    },
                    success: function() {
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'Failed to send emergency notice',
                    icon: 'error',
                    confirmButtonColor: '#D4AF37'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error',
                text: 'Failed to send emergency notice: ' + error,
                icon: 'error',
                confirmButtonColor: '#D4AF37'
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#datePicker", {
        dateFormat: "Y-m-d",
        defaultDate: selectedDate,
        onChange: function(selectedDates, dateStr, instance) {
            selectedDate = dateStr;
            loadTableReservations();
        }
    });

    flatpickr("#holidayDate", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    flatpickr("#disasterDate", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    loadTableReservations();
    
    setInterval(loadTableReservations, 10000);
    
    initializeNotificationBell();
    initializeModalHandlers();
    initializeAnimations();
    
    updateDashboardStats();
    updateNotifications();
    
    setInterval(updateDashboardStats, 30000);
    setInterval(updateNotifications, 30000);
    
    if (emergencyActive || holidayActive) {
        setTimeout(() => {
            if (emergencyActive) {
                showEmergencyWarning();
            }
        }, 1000);
    }
});

function handleTableClick(tableName, tableStatus) {
    const tableElement = document.querySelector(`.table-item[data-table="${tableName}"]`);
    if (!tableElement) return;
    
    const isFacility = tableElement.classList.contains('facility-item') || isNonReservableFacility(tableName);
    
    if (isFacility) return;
    
    if (emergencyActive || holidayActive) {
        if (emergencyActive) {
            showEmergencyWarning();
        }
        return;
    }
    
    currentTableName = tableName;
    currentTableStatus = tableStatus;
    
    document.querySelectorAll('.table-item').forEach(item => {
        item.classList.remove('active');
    });
    
    tableElement.classList.add('active');
    
    showTableModal(tableName, tableStatus);
}

function showTableModal(tableName, tableStatus) {
    document.getElementById('tableModalTitle').textContent = `Table: ${tableName}`;
    document.getElementById('tableModalSubtitle').textContent = `Current Status: ${tableStatus}`;
    document.getElementById('tableModal').classList.add('active');
    showModalOverlay();
}

function closeTableModal() {
    document.getElementById('tableModal').classList.remove('active');
    hideModalOverlay();
    
    document.querySelectorAll('.table-item').forEach(item => {
        item.classList.remove('active');
    });
}

function setTableStatus(newStatus) {
    if (emergencyActive || holidayActive) {
        if (emergencyActive) {
            showEmergencyWarning();
        }
        closeTableModal();
        return;
    }
    
    const confirmMessage = `Mark table ${currentTableName} as ${newStatus}?`;
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    const basePath = getBasePath();
    
    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            requestType: "update_table_status",
            table_code: currentTableName,
            status: newStatus,
            date: selectedDate
        },
        dataType: "json",
        success: function(response) {
            if (response.status === 'success' || response.success) {
                showSuccessNotification(`Table ${currentTableName} marked as ${newStatus}`);
                loadTableReservations();
                closeTableModal();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'Failed to update table status',
                    icon: 'error',
                    confirmButtonColor: '#D4AF37'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error',
                text: 'Failed to update table status: ' + error,
                icon: 'error',
                confirmButtonColor: '#D4AF37'
            });
        }
    });
}

function loadTableReservations() {
    const date = selectedDate;
    const basePath = getBasePath();
    
    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        type: 'POST',
        data: { 
            requestType: "fetch_reservations_for_tables",
            date: date 
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' || response.success) {
                updateTableColors(response.data);
                updateDashboardStats();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading table reservations:', error);
        }
    });
}

function updateTableColors(tableData) {
    document.querySelectorAll('.table-item:not(.facility-item)').forEach(table => {
        table.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-white', 'bg-orange', 'bg-gray', 'bg-purple', 'bg-secondary');
        table.classList.remove('text-white', 'text-dark');
        table.classList.remove('active');
        
        table.classList.add('bg-white', 'text-dark');
        table.setAttribute('data-status', 'available');
        
        const badge = table.querySelector('.table-status-badge');
        if (badge) {
            badge.textContent = 'Available';
            badge.className = 'table-status-badge status-available-badge';
        }
    });
    
    if (tableData && Array.isArray(tableData)) {
        tableData.forEach(function(item) {
            const tableElement = document.querySelector(`.table-item[data-table="${item.table_code}"]`);
            if (tableElement && !tableElement.classList.contains('facility-item') && !isNonReservableFacility(item.table_code)) {
                const status = item.status || 'available';
                
                tableElement.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-white', 'bg-orange', 'bg-gray', 'bg-purple', 'bg-secondary');
                tableElement.classList.remove('text-white', 'text-dark');
                
                const statusClass = getStatusClass(status);
                tableElement.classList.add(statusClass);
                
                if (status === 'available') {
                    tableElement.classList.add('text-dark');
                } else {
                    tableElement.classList.add('text-white');
                }
                
                tableElement.setAttribute('data-status', status);
                
                const badge = tableElement.querySelector('.table-status-badge');
                if (badge) {
                    let statusText = 'Available';
                    if (status === 'confirmed') statusText = 'Confirmed';
                    else if (status === 'pending') statusText = 'Pending';
                    else if (status === 'cancelled') statusText = 'Cancelled';
                    else if (status === 'request_cancel') statusText = 'Cancel Request';
                    else if (status === 'request_reschedule') statusText = 'Reschedule Request';
                    else if (status === 'unavailable') statusText = 'Unavailable';
                    else if (status === 'walkin') statusText = 'Walk-in';
                    else if (status === 'completed') statusText = 'Completed';
                    
                    badge.textContent = statusText;
                    badge.className = 'table-status-badge status-' + status.replace('_', '-') + '-badge';
                }
            }
        });
    }
}

function updateDashboardStats() {
    const basePath = getBasePath();
    
    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            requestType: "dashboard_analytics"
        },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                const data = response.data;
                
                const totalReservationsEl = document.getElementById('totalReservations');
                const pendingReservationsEl = document.getElementById('pendingReservations');
                const confirmedReservationsEl = document.getElementById('confirmedReservations');
                const totalSalesEl = document.getElementById('totalSales');
                
                if (totalReservationsEl) totalReservationsEl.textContent = data.total_reservations || 0;
                if (pendingReservationsEl) pendingReservationsEl.textContent = data.pending_reservations || 0;
                if (confirmedReservationsEl) confirmedReservationsEl.textContent = data.confirmed_reservations || 0;
                if (totalSalesEl) totalSalesEl.textContent = `₱${parseFloat(data.total_sales || 0).toFixed(2)}`;
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching dashboard stats:', error);
        }
    });
}

function showModalOverlay() {
    document.getElementById('modalOverlay').classList.add('active');
}

function hideModalOverlay() {
    document.getElementById('modalOverlay').classList.remove('active');
}

function showHolidayModal() {
    if (emergencyActive) {
        showEmergencyWarning();
        return;
    }

    // If there's already an active holiday, ask user to confirm before opening modal
    if (holidayActive && activeHolidayInfo) {
        const prettyDate = new Date(activeHolidayInfo.holiday_date).toLocaleDateString();
        Swal.fire({
            title: 'Active Holiday Exists',
            html: `<p>There is already an active holiday scheduled:<br><strong>${activeHolidayInfo.holiday_name}</strong> on <strong>${prettyDate}</strong>.</p><p>Do you want to create another holiday notice?</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Create Anyway',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#059669'
        }).then((result) => {
            if (result.isConfirmed) {
                openHolidayModalDefaults();
            }
        });
        return;
    }

    openHolidayModalDefaults();
}

function openHolidayModalDefaults() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('holidayDate').value = tomorrow.toISOString().split('T')[0];

    holidayType = 'total_close';
    document.getElementById('totalClose').checked = true;
    document.getElementById('delayedOpening').checked = false;
    document.getElementById('delayedHoursContainer').classList.remove('active');

    document.getElementById('holidayModal').classList.add('active');
    showModalOverlay();
}

function closeHolidayModal() {
    document.getElementById('holidayModal').classList.remove('active');
    hideModalOverlay();
}

function showDisasterModal() {
    const today = new Date();
    document.getElementById('disasterDate').value = today.toISOString().split('T')[0];
    
    document.getElementById('disasterModal').classList.add('active');
    showModalOverlay();
}

function closeDisasterModal() {
    const modal = document.getElementById('disasterModal');
    if (modal) modal.classList.remove('active');

    // Clear/reset input fields so modal opens clean next time
    const dateEl = document.getElementById('disasterDate');
    const reasonEl = document.getElementById('disasterReason');
    if (dateEl) dateEl.value = '';
    if (reasonEl) reasonEl.value = '';

    hideModalOverlay();
}

function showEmergencyWarning() {
    document.getElementById('emergencyWarningModal').classList.add('active');
    showModalOverlay();
}

function closeEmergencyWarning() {
    document.getElementById('emergencyWarningModal').classList.remove('active');
    hideModalOverlay();
}

function selectHolidayOption(option) {
    holidayType = option;
    
    if (option === 'total_close') {
        document.getElementById('totalClose').checked = true;
        document.getElementById('delayedOpening').checked = false;
        document.getElementById('delayedHoursContainer').classList.remove('active');
    } else {
        document.getElementById('totalClose').checked = false;
        document.getElementById('delayedOpening').checked = true;
        document.getElementById('delayedHoursContainer').classList.add('active');
    }
    
    document.querySelectorAll('.holiday-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
}

function sendHolidayNotice() {
    const date = document.getElementById('holidayDate').value;
    const reason = document.getElementById('holidayReason').value;
    
    if (!date || !reason) {
        Swal.fire({
            title: 'Error',
            text: 'Please fill in all fields',
            icon: 'error',
            confirmButtonColor: '#D4AF37'
        });
        return;
    }

    let newOpenTime = '';
    let newCloseTime = '';
    
    if (holidayType === 'delayed_opening') {
        newOpenTime = document.getElementById('newOpenTime').value;
        newCloseTime = document.getElementById('newCloseTime').value;
        
        if (!newOpenTime || !newCloseTime) {
            Swal.fire({
                title: 'Error',
                text: 'Please specify new opening and closing times',
                icon: 'error',
                confirmButtonColor: '#D4AF37'
            });
            return;
        }
    }

    const basePath = getBasePath();

    // Map frontend holiday types to backend expected values
    let backendHolidayType = 'closure';
    if (holidayType === 'total_close') backendHolidayType = 'closure';
    if (holidayType === 'delayed_opening') backendHolidayType = 'late_opening';

    // Send fields that the server-side isset handler expects so it calls the mailer
    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            send_holiday_notice: 1,
            holiday_date: date,
            holiday_reason: reason,
            holiday_type: backendHolidayType,
            late_opening_time: (backendHolidayType === 'late_opening' ? newOpenTime : ''),
            late_closing_time: (backendHolidayType === 'late_opening' ? newCloseTime : '')
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                showSuccessNotification('Holiday notice sent to all users');
                closeHolidayModal();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'Failed to send holiday notice',
                    icon: 'error',
                    confirmButtonColor: '#D4AF37'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error',
                text: 'Failed to send holiday notice: ' + error,
                icon: 'error',
                confirmButtonColor: '#D4AF37'
            });
        }
    });
}

function restoreSystemAccess() {
    if (!confirm('Are you sure you want to restore system access? This will allow customers to make reservations again.')) {
        return;
    }

    const basePath = getBasePath();

    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            requestType: "restore_system_access"
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                showSuccessNotification('System access restored successfully');
                closeEmergencyWarning();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'Failed to restore system access',
                    icon: 'error',
                    confirmButtonColor: '#D4AF37'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error',
                text: 'Failed to restore system access: ' + error,
                icon: 'error',
                confirmButtonColor: '#D4AF37'
            });
        }
    });
}

function showSuccessNotification(message) {
    const successNotif = document.getElementById('successNotification');
    successNotif.querySelector('#successMessage').textContent = message;
    successNotif.classList.add('show');
    
    setTimeout(() => {
        successNotif.classList.remove('show');
    }, 5000);
}

function initializeNotificationBell() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllRead = document.getElementById('markAllRead');
    
    if (notificationBell && notificationDropdown) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = notificationDropdown.classList.contains('active');
            if (isVisible) {
                notificationDropdown.classList.remove('active');
            } else {
                notificationDropdown.classList.add('active');
                updateNotifications();
            }
        });

        if (markAllRead) {
            markAllRead.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllNotificationsAsRead();
            });
        }

        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });
    }
}

function updateNotifications() {
    const basePath = getBasePath();
    
    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            requestType: "fetch_notifications"
        },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                const notifications = response.data;
                const notificationList = document.getElementById('notificationList');
                const notificationBadge = document.getElementById('notificationBadge');
                
                if (notifications && notifications.length > 0) {
                    notificationList.innerHTML = '';
                    let unreadCount = 0;
                    
                    notifications.forEach(notification => {
                        if (notification.is_read == 0) unreadCount++;
                        
                        const notificationItem = document.createElement('div');
                        notificationItem.className = `notification-item ${notification.is_read == 0 ? 'unread' : ''}`;
                        notificationItem.innerHTML = `
                            <div class="notification-title">
                                ${escapeHtml(notification.title || 'No Title')}
                                <span class="notification-type ${notification.type || 'system'}">${notification.type || 'system'}</span>
                            </div>
                            <div class="notification-message">${escapeHtml(notification.message || 'No message')}</div>
                            <div class="notification-time">${formatTimeAgo(notification.created_at)}</div>
                        `;
                        
                        notificationList.appendChild(notificationItem);
                    });
                    
                    if (unreadCount > 0) {
                        notificationBadge.textContent = unreadCount;
                        notificationBadge.style.display = 'flex';
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                } else {
                    notificationList.innerHTML = '<div class="notification-item"><div class="notification-message">No notifications</div></div>';
                    notificationBadge.style.display = 'none';
                }
            }
        },
        error: function(xhr, status, error) {
            const notificationList = document.getElementById('notificationList');
            if (notificationList) {
                notificationList.innerHTML = '<div class="notification-item"><div class="notification-message">Error loading notifications</div></div>';
            }
        }
    });
}

function formatTimeAgo(timestamp) {
    if (!timestamp) return 'Unknown time';
    
    const now = new Date();
    const notificationTime = new Date(timestamp);
    
    if (isNaN(notificationTime.getTime())) {
        return 'Invalid time';
    }
    
    const diffInSeconds = Math.floor((now - notificationTime) / 1000);
    
    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

function markAllNotificationsAsRead() {
    const basePath = getBasePath();

    $.ajax({
        url: basePath + "controller/end-points/controller.php",
        method: "POST",
        data: { 
            requestType: "mark_notifications_read"
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                updateNotifications();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error marking all notifications as read:', error);
        }
    });
}

function initializeModalHandlers() {
    const modalOverlay = document.getElementById('modalOverlay');
    const emergencyModal = document.getElementById('emergencyWarningModal');
    const tableModal = document.getElementById('tableModal');
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeAllModals();
                hideModalOverlay();
            }
        });
    }
    
    if (emergencyModal) {
        emergencyModal.addEventListener('click', function(e) {
            if (e.target === emergencyModal) {
                closeEmergencyWarning();
            }
        });
    }
    
    if (tableModal) {
        tableModal.addEventListener('click', function(e) {
            if (e.target === tableModal) {
                closeTableModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
            hideModalOverlay();
        }
    });
}

function closeAllModals() {
    document.getElementById('tableModal').classList.remove('active');
    document.getElementById('holidayModal').classList.remove('active');
    document.getElementById('disasterModal').classList.remove('active');
    document.getElementById('emergencyWarningModal').classList.remove('active');
    document.getElementById('notificationDropdown').classList.remove('active');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function initializeAnimations() {
    const cards = document.querySelectorAll('.interactive-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('loaded');
        }, index * 100);
    });
}
</script>
</body>
</html>
<?php
include "../src/components/admin/footer.php";
?>