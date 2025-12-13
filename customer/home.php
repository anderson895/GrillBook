<?php
session_start();

// Enhanced session validation
$userLoggedIn = false;
$userIsCustomer = false;

// Check multiple possible session variables
if (isset($_SESSION['user_id'])) {
    $userLoggedIn = true;
    
    // Check for customer position in multiple possible session variables
    if (isset($_SESSION['user_position']) && $_SESSION['user_position'] === 'customer') {
        $userIsCustomer = true;
    } elseif (isset($_SESSION['position']) && $_SESSION['position'] === 'customer') {
        $userIsCustomer = true;
        $_SESSION['user_position'] = 'customer'; // Standardize
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'customer') {
        $userIsCustomer = true;
        $_SESSION['user_position'] = 'customer'; // Standardize
    }
    
    // If still not customer, try to fetch from database
    if (!$userIsCustomer) {
        try {
            $host = 'localhost';
            $dbname = 'u777088444_grillbook';
            $username = 'u777088444_grillbook';
            $password = 'Grillbook123@';
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Try different table/column combinations
            $queries = [
                "SELECT user_position FROM user WHERE user_id = ?",
                "SELECT position FROM users WHERE id = ?",
                "SELECT role FROM customers WHERE customer_id = ?",
                "SELECT user_type FROM user WHERE user_id = ?"
            ];
            
            $userId = $_SESSION['user_id'];
            
            foreach ($queries as $sql) {
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$userId])) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result) {
                        $position = $result['user_position'] ?? $result['position'] ?? $result['role'] ?? $result['user_type'] ?? '';
                        
                        if (strtolower($position) === 'customer') {
                            $_SESSION['user_position'] = 'customer';
                            $userIsCustomer = true;
                            break;
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Position check failed: " . $e->getMessage());
        }
    }
}

// Redirect if not logged in or not customer
if (!$userLoggedIn || !$userIsCustomer) {
    // Store current page for redirect back after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Debug logging
    error_log("Redirect triggered: User logged in: " . ($userLoggedIn ? 'Yes' : 'No'));
    error_log("User is customer: " . ($userIsCustomer ? 'Yes' : 'No'));
    error_log("Session position: " . ($_SESSION['user_position'] ?? 'Not set'));
    error_log("User ID: " . ($_SESSION['user_id'] ?? 'Not set'));
    
    header('Location: ../login.php');
    exit();
}

// Standardize session variables for the rest of the page
if (!isset($_SESSION['user_name']) && isset($_SESSION['user_fname'])) {
    $_SESSION['user_name'] = $_SESSION['user_fname'];
}

if (!isset($_SESSION['email']) && isset($_SESSION['user_email'])) {
    $_SESSION['email'] = $_SESSION['user_email'];
}

// Check for emergency closure at the beginning
$emergencyActive = false;
$emergencyStatus = null;
$host = 'localhost';
$dbname = 'grillbook';
$username = 'root';
$password = '';
$today = date('Y-m-d');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check for active emergency closures
    $emergencySql = "SELECT * FROM emergency_closures WHERE status = 'active' AND closure_date >= ? ORDER BY closure_date DESC LIMIT 1";
    $emergencyStmt = $pdo->prepare($emergencySql);
    $emergencyStmt->execute([$today]);
    $emergencyStatus = $emergencyStmt->fetch(PDO::FETCH_ASSOC);
    $emergencyActive = ($emergencyStatus !== false);
    
    // Also check if system is in emergency mode
    $systemStatusSql = "SELECT * FROM system_status WHERE status_key = 'emergency_mode'";
    $systemStatusStmt = $pdo->query($systemStatusSql);
    $systemStatus = $systemStatusStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($systemStatus && $systemStatus['status_value'] === 'true') {
        $emergencyActive = true;
    }
    
} catch (PDOException $e) {
    error_log("Emergency status check failed: " . $e->getMessage());
}

include "../src/components/customer/header.php"; 
include "../src/components/customer/nav.php"; 

$user_name = $_SESSION['user_name'] ?? $_SESSION['first_name'] ?? 'Guest User';
$user_email = $_SESSION['email'] ?? 'guest@example.com';
$user_id = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? null;
$user_phone = $_SESSION['phone'] ?? $_SESSION['customer_phone'] ?? $_SESSION['user_phone'] ?? '';

// DEBUG: Log session data
error_log("DEBUG - User ID from session: " . ($user_id ?? 'NOT FOUND'));
error_log("DEBUG - User Phone from session: " . ($user_phone ?? 'NOT FOUND'));

// FIX: Fetch phone number from database if not in session
if (empty($user_phone) && $user_id) {
    try {
        $pdo_temp = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check in user table first
        $stmt = $pdo_temp->prepare("SELECT user_phone FROM user WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $phone_row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($phone_row && !empty($phone_row['user_phone'])) {
            $user_phone = $phone_row['user_phone'];
            error_log("DEBUG - Phone found in user table: " . $user_phone);
        } else {
            // Check in customer table
            $stmt = $pdo_temp->prepare("SELECT customer_phone FROM customers WHERE customer_id = ?");
            $stmt->execute([$user_id]);
            $phone_row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($phone_row && !empty($phone_row['customer_phone'])) {
                $user_phone = $phone_row['customer_phone'];
                error_log("DEBUG - Phone found in customer table: " . $user_phone);
            }
        }
        
        // Store in session for future use
        if (!empty($user_phone)) {
            $_SESSION['phone'] = $user_phone;
            $_SESSION['customer_phone'] = $user_phone;
            error_log("DEBUG - Phone stored in session: " . $user_phone);
        }
        
    } catch (PDOException $e) {
        error_log("Error fetching phone: " . $e->getMessage());
        $user_phone = '';
    }
}

error_log("DEBUG - Final user_phone: " . ($user_phone ?: 'EMPTY'));

$tableStatusMap = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch all table statuses including walk-in tables
    $stmt = $pdo->query("SELECT walkin_table_code, walkin_status FROM walkin_tables WHERE DATE(walkin_created_at) = '$today' OR walkin_date = '$today'");
    $walkinTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($walkinTables as $table) {
        $tableStatusMap[$table['walkin_table_code']] = $table['walkin_status'];
    }
    
    // Fetch reservations for today
    $reservationStmt = $pdo->prepare("
        SELECT table_code, status 
        FROM reservations 
        WHERE date_schedule = ?
        AND status IN ('pending', 'confirmed', 'cancelled', 'request_cancel', 'request_reschedule')
        AND table_code IS NOT NULL
        AND table_code != ''
    ");
    $reservationStmt->execute([$today]);
    $reservations = $reservationStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($reservations as $reservation) {
        $tableCode = $reservation['table_code'];
        $reservationStatus = $reservation['status'];
        $tableStatusMap[$tableCode] = $reservationStatus;
    }
    
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $tableStatusMap = [];
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
        default:
            return 'bg-white';
    }
}

function isTableClickable($tableName, $status) {
    $nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];
    if (in_array($tableName, $nonReservableFacilities)) {
        return false;
    }
    
    return $status === 'available';
}

function isNonReservableFacility($tableName) {
    $nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];
    return in_array($tableName, $nonReservableFacilities);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - GrillBook</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        /* Emergency Styles */
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

        .emergency-modal-warning {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #DC2626, #B91C1C);
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
            display: none;
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

        .interactive-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
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
        
        .interactive-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 25px 50px rgba(212, 175, 55, 0.2),
                0 0 0 1px rgba(212, 175, 55, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .btn-grill {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            color: var(--dark-bg);
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 
                0 4px 15px rgba(212, 175, 55, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            min-height: 48px;
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

        .form-input-grill {
            background: rgba(26, 26, 26, 0.8);
            border: 2px solid rgba(212, 175, 55, 0.3);
            color: var(--text-light);
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
            background-image: 
                linear-gradient(45deg, transparent 49%, rgba(212, 175, 55, 0.05) 50%, transparent 51%);
            background-size: 15px 15px;
        }

        .form-input-grill::placeholder {
            color: var(--text-muted);
            font-size: 0.95rem;
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
            font-size: 0.85rem;
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
        
        .table-item.clickable:hover {
            transform: translateY(-3px);
            border-color: var(--primary-gold);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2), 0 0 12px rgba(212, 175, 55, 0.1);
        }

        .table-item.non-clickable {
            cursor: not-allowed !important;
            opacity: 0.7;
        }
        
        .table-item.non-clickable:hover {
            transform: none !important;
            box-shadow: none !important;
            border-color: inherit !important;
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

        /* Facility Color Classes */
        .table-item.facility-item.bg-entrance {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }

        .table-item.facility-item.bg-entrance:hover {
            transform: none;
            box-shadow: none;
            border-color: #3b82f6 !important;
        }

        .table-item.facility-item.bg-exit {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            border-color: #ef4444 !important;
        }

        .table-item.facility-item.bg-exit:hover {
            transform: none;
            box-shadow: none;
            border-color: #ef4444 !important;
        }

        .table-item.facility-item.bg-performance {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9) !important;
            color: white !important;
            border-color: #8b5cf6 !important;
        }

        .table-item.facility-item.bg-performance:hover {
            transform: none;
            box-shadow: none;
            border-color: #8b5cf6 !important;
        }

        .table-item.facility-item.bg-billiards {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: white !important;
            border-color: #10b981 !important;
        }

        .table-item.facility-item.bg-billiards:hover {
            transform: none;
            box-shadow: none;
            border-color: #10b981 !important;
        }

        .table-item.facility-item.bg-service-counter {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white !important;
            border-color: #f59e0b !important;
        }

        .table-item.facility-item.bg-service-counter:hover {
            transform: none;
            box-shadow: none;
            border-color: #f59e0b !important;
        }

        .table-item.facility-item.bg-kitchen-area {
            background: linear-gradient(135deg, #ec4899, #be185d) !important;
            color: white !important;
            border-color: #ec4899 !important;
        }

        .table-item.facility-item.bg-kitchen-area:hover {
            transform: none;
            box-shadow: none;
            border-color: #ec4899 !important;
        }

        .bg-entrance {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }

        .bg-entrance.active {
            box-shadow: 0 0 15px #3b82f6;
            border-color: #93c5fd !important;
        }

        .bg-exit {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            border-color: #ef4444 !important;
        }

        .bg-exit.active {
            box-shadow: 0 0 15px #ef4444;
            border-color: #fca5a5 !important;
        }

        .bg-performance {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9) !important;
            color: white !important;
            border-color: #8b5cf6 !important;
        }

        .bg-performance.active {
            box-shadow: 0 0 15px #8b5cf6;
            border-color: #d8b4fe !important;
        }

        .bg-billiards {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: white !important;
            border-color: #10b981 !important;
        }

        .bg-billiards.active {
            box-shadow: 0 0 15px #10b981;
            border-color: #6ee7b7 !important;
        }

        .bg-service-counter {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white !important;
            border-color: #f59e0b !important;
        }

        .bg-service-counter.active {
            box-shadow: 0 0 15px #f59e0b;
            border-color: #fcd34d !important;
        }

        .bg-kitchen-area {
            background: linear-gradient(135deg, #ec4899, #be185d) !important;
            color: white !important;
            border-color: #ec4899 !important;
        }

        .bg-kitchen-area.active {
            box-shadow: 0 0 15px #ec4899;
            border-color: #f472b6 !important;
        }

        /* Accessibility: facility label contrast helpers (preserve font-size)
           Remove dark outline and heavy shadows so white text appears bright */
        .facility-label,
        .table-item.facility-item {
            color: #ffffff !important; /* pure white */
            font-weight: 700 !important; /* keep bold for clarity */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            -webkit-text-stroke: 0 !important; /* remove dark stroke */
            text-shadow: 0 1px 1px rgba(0,0,0,0.32) !important; /* subtle shadow only */
            filter: none !important; /* remove drop-shadow which darkened text */
            font-size: 0.85rem !important; /* original table size */
        }

        /* Ensure facility-label is block-level and readable */
        .facility-label {
            display: block;
            line-height: 1.1;
            letter-spacing: 0.2px !important;
            color: #ffffff !important; /* reinforce pure white */
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

        .legend-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(26, 26, 26, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            justify-content: center;
            text-align: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 2px solid rgba(255, 255, 255, 0.2);
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

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }

        .text-high-contrast {
            color: var(--text-light);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }

        .text-gold-contrast {
            color: var(--primary-gold);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }

        .table-filter-container {
            background: linear-gradient(135deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-select {
            background: rgba(26, 26, 26, 0.8);
            border: 2px solid rgba(212, 175, 55, 0.3);
            color: var(--text-light);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .compact-hours-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .compact-hour-item {
            background: var(--card-bg);
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            padding: 0.8rem;
            text-align: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .compact-hour-item.current {
            border-color: var(--primary-gold);
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(230, 197, 80, 0.1));
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        .compact-hour-day {
            font-weight: 600;
            color: var(--primary-gold);
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .compact-hour-time {
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.8rem;
            background: rgba(212, 175, 55, 0.1);
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            display: inline-block;
        }

        .live-clock-container {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            border-radius: 12px;
            padding: 1.5rem;
            color: var(--dark-bg);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
            border: 2px solid rgba(212, 175, 55, 0.2);
        }

        #liveClock {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .status-indicator {
            animation: pulse 2s infinite;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
        }

        .status-indicator.open {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            box-shadow: 0 3px 10px rgba(22, 163, 74, 0.3);
        }

        .status-indicator.closed {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 3px 10px rgba(220, 38, 38, 0.3);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .creative-modal {
            background: linear-gradient(145deg, var(--card-bg), var(--grill-dark));
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9);
            width: 100%;
            max-width: 950px;
            margin: 1rem auto;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            color: var(--dark-bg);
            border-radius: 14px 14px 0 0;
            padding: 1.5rem;
        }

        .menu-category-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .menu-category-tab {
            background: var(--card-bg);
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            padding: 1rem 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: var(--text-light);
            text-align: center;
            min-width: 150px;
        }

        .menu-category-tab.active {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            color: var(--dark-bg);
            border-color: var(--dark-gold);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .menu-category-tab:hover:not(.active) {
            border-color: var(--primary-gold);
            transform: translateY(-2px);
        }

        .menu-category-content {
            display: none;
        }

        .menu-category-content.active {
            display: block;
        }

        .menu-item-description {
            font-size: 0.85rem;
            color: #bbb;
            margin-top: 0.5rem;
            line-height: 1.4;
            height: auto;
            min-height: 40px;
            overflow: visible;
            text-overflow: unset;
            display: block;
            white-space: normal;
            padding: 0.3rem;
        }

        .billing-summary-formal {
            background: var(--card-bg);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .billing-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            text-align: center;
        }

        .billing-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .billing-item:last-child {
            border-bottom: none;
        }

        .billing-label {
            font-weight: 600;
            color: var(--text-light);
            font-size: 1rem;
        }

        .billing-value {
            font-weight: 700;
            color: var(--primary-gold);
            font-size: 1rem;
        }

        .billing-total {
            background: rgba(212, 175, 55, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.5rem;
            border: 2px solid var(--primary-gold);
        }

        .billing-total .billing-label {
            font-size: 1.1rem;
            color: var(--primary-gold);
        }

        .billing-total .billing-value {
            font-size: 1.2rem;
            color: var(--primary-gold);
        }

        .corkage-section {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border-left: 4px solid var(--primary-gold);
        }

        .corkage-input-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .corkage-input-label {
            font-size: 0.9rem;
            color: var(--text-light);
            min-width: 200px;
        }

        .corkage-input {
            width: 80px;
            padding: 0.5rem;
            background: var(--card-bg);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 4px;
            color: var(--text-light);
            text-align: center;
        }

        .payment-type-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        .payment-type-option {
            background: var(--card-bg);
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-type-option:hover {
            border-color: var(--primary-gold);
            transform: translateY(-2px);
        }

        .payment-type-option.selected {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(230, 197, 80, 0.1));
            border-color: var(--primary-gold);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }

        .payment-type-amount {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-top: 0.5rem;
        }

        .payment-qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 1rem;
        }

        .payment-qr-image {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .payment-qr-image:hover {
            transform: scale(1.05);
        }

        .time-select-container {
            position: relative;
            width: 100%;
        }

        .time-select-container select {
            background: var(--card-bg);
            color: var(--text-light);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            min-height: 55px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23d4af37' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 16px 12px;
            cursor: pointer;
        }

        .time-select-container select:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
            outline: none;
            transform: scale(1.01);
        }

        @media (max-width: 768px) {
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

            .compact-hours-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .payment-type-container {
                grid-template-columns: 1fr;
            }

            .menu-category-tab {
                min-width: 120px;
                padding: 0.75rem 1rem;
            }

            .legend-container {
                gap: 0.5rem;
            }

            .legend-item {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .live-clock-container {
                padding: 1rem;
            }

            #liveClock {
                font-size: 1.5rem;
            }
            
            .emergency-modal-warning {
                padding: 1.5rem;
                width: 95%;
            }
            
            .emergency-status {
                top: 10px;
                right: 10px;
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
            }
            
            .emergency-notice {
                bottom: 10px;
                right: 10px;
                padding: 0.75rem 1rem;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(26, 26, 26, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(212, 175, 55, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(212, 175, 55, 0.7);
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

        .chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .chatbot-button {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: none;
            transition: all 0.3s ease;
        }

        .chatbot-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }

        .chatbot-button-icon {
            font-size: 1.5rem;
            color: var(--dark-bg);
        }

        .chatbot-modal {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: var(--card-bg);
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
            display: none;
            flex-direction: column;
            z-index: 1001;
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            padding: 1rem;
            border-radius: 13px 13px 0 0;
            color: var(--dark-bg);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--dark-bg);
        }

        .chatbot-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .chatbot-message {
            padding: 0.8rem;
            border-radius: 10px;
            max-width: 80%;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .chatbot-message.bot {
            background: var(--card-bg);
            align-self: flex-start;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .chatbot-message.user {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold));
            color: var(--dark-bg);
            align-self: flex-end;
        }

        .chatbot-input {
            display: flex;
            padding: 1rem;
            border-top: 1px solid rgba(212, 175, 55, 0.3);
            gap: 0.5rem;
        }

        .chatbot-input-field {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .chatbot-send {
            background: var(--primary-gold);
            border: none;
            border-radius: 8px;
            padding: 0.8rem;
            cursor: pointer;
            color: var(--dark-bg);
            font-weight: 600;
        }

        .swal2-container .swal2-popup {
            background: var(--card-bg) !important;
            color: var(--text-light) !important;
            border: 2px solid rgba(212, 175, 55, 0.3);
        }

        .swal2-title, .swal2-html-container {
            color: var(--text-light) !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold)) !important;
            color: var(--dark-bg) !important;
            border: none !important;
        }

        .swal2-cancel {
            background: transparent !important;
            color: var(--text-light) !important;
            border: 1px solid rgba(212, 175, 55, 0.3) !important;
        }
    </style>
</head>
<body>
<?php if ($emergencyActive): ?>
<!-- Emergency Overlay for entire system -->
<div class="emergency-overlay"></div>

<!-- Emergency Modal Warning -->
<div id="emergencyWarningModal" class="emergency-modal-warning">
    <div class="warning-icon">
        <span class="material-icons" style="font-size: 4rem; margin-bottom: 1rem;">warning</span>
    </div>
    <h2>🚨 EMERGENCY SYSTEM SHUTDOWN 🚨</h2>
    <p id="emergencyWarningMessage">
        <?php 
        if ($emergencyStatus) {
            echo "The restaurant is temporarily closed on " . date('F j, Y', strtotime($emergencyStatus['closure_date'])) . " due to: " . htmlspecialchars($emergencyStatus['reason']);
        } else {
            echo "The system is currently in emergency shutdown mode. All reservations are suspended until further notice.";
        }
        ?>
    </p>
    <p style="font-size: 1rem; margin-top: 1rem; opacity: 0.8;">
        We apologize for the inconvenience. Please check back later or contact us for updates.
    </p>
</div>

<!-- Emergency Notice at bottom -->
<div class="emergency-notice">
    <span class="material-icons">error</span>
    <div>
        <strong>EMERGENCY CLOSURE</strong>
        <div style="font-size: 0.9rem; opacity: 0.9;">
            <?php echo htmlspecialchars($emergencyStatus['reason'] ?? 'Temporary closure'); ?>
        </div>
    </div>
</div>

<!-- Emergency Status Indicator -->
<div class="emergency-status animate-emergency-pulse">
    <span class="material-icons">warning</span>
    <span>SYSTEM TEMPORARILY CLOSED</span>
</div>
<?php endif; ?>

<div class="grill-background">
    <div class="grill-pattern"></div>
    <div class="grill-overlay"></div>
</div>

<div class="modal-overlay" id="modalOverlay"></div>

<div id="newNotificationAlert" class="new-notification-alert">
    <div class="flex items-center">
        <span class="material-icons mr-2">notifications_active</span>
        <div>
            <strong>New Reservation!</strong>
            <div id="alertMessage" class="text-sm"></div>
        </div>
    </div>
</div>

<div class="flex flex-col items-center justify-start min-h-screen pt-24">
    <div class="w-full max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center mt-4 mb-8">
            <div class="interactive-card p-6 w-full max-w-5xl animate-fadeInUp">
                <div class="flex items-center gap-6 flex-wrap justify-between">
                    <div class="flex-shrink-0 p-3 bg-yellow-400/20 rounded-xl">
                        <span class="material-icons text-[#FFD700] text-3xl">schedule</span>
                    </div>
                    <div class="text-white leading-relaxed flex-1 min-w-0">
                        <div class="flex items-center gap-4 mb-3 flex-wrap">
                            <h2 class="font-bold text-[#FFD700] text-2xl">📅 Operating Hours</h2>
                            <span class="status-indicator closed">
                                CLOSED
                            </span>
                        </div>
                        <div class="compact-hours-grid">
                            <div class="compact-hour-item current">
                                <div class="compact-hour-day text-[#FFD700]">Sunday</div>
                                <div class="compact-hour-time">5:00 PM - 3:00 AM</div>
                            </div>
                            <div class="compact-hour-item">
                                <div class="compact-hour-day text-white">Monday</div>
                                <div class="compact-hour-time">5:00 PM - 2:00 AM</div>
                            </div>
                            <div class="compact-hour-item">
                                <div class="compact-hour-day text-white">Tuesday</div>
                                <div class="compact-hour-time">5:00 PM - 2:00 AM</div>
                            </div>
                            <div class="compact-hour-item">
                                <div class="compact-hour-day text-white">Wednesday</div>
                                <div class="compact-hour-time">5:00 PM - 2:00 AM</div>
                            </div>
                            <div class="compact-hour-item">
                                <div class="compact-hour-day text-white">Thursday</div>
                                <div class="compact-hour-time">5:00 PM - 2:00 AM</div>
                            </div>
                            <div class="compact-hour-item">
                                <div class="compact-hour-day text-white">Friday</div>
                                <div class="compact-hour-time">7:00 PM - 4:00 AM</div>
                            </div>
                            <div class="compact-hour-item">
                                <div class="compact-hour-day text-white">Saturday</div>
                                <div class="compact-hour-time">7:00 PM - 4:00 AM</div>
                            </div>
                        </div>
                    </div>
                    <div class="live-clock-container">
                        <div class="text-center">
                            <div class="flex items-center justify-center gap-1 mb-1">
                                <span class="material-icons text-gray-900">access_time</span>
                                <span class="text-gray-900 font-bold text-xs">CURRENT TIME</span>
                            </div>
                            <div id="liveClock" class="mb-1"></div>
                            <div class="text-gray-900 text-sm font-semibold" id="currentDate"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="legend-container animate-fadeInUp" style="animation-delay: 0.1s">
            <!-- Reservation Status Separator -->
            <div style="width: 100%; margin: 16px 0; padding: 12px 0; border-top: 2px solid rgba(212, 175, 55, 0.4); border-bottom: 2px solid rgba(212, 175, 55, 0.4); text-align: center;">
                <div style="font-size: 0.8rem; color: rgba(212, 175, 55, 0.8); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">━━ RESERVATION STATUS ━━</div>
            </div>
            
            <div class="legend-item">
                <div class="legend-color bg-white"></div>
                <span class="text-white text-sm">Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-warning"></div>
                <span class="text-white text-sm">Pending</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-success"></div>
                <span class="text-white text-sm">Confirmed</span>
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
                <div class="legend-color bg-orange"></div>
                <span class="text-white text-sm">Request Cancel</span>
            </div>
            <!-- Facility Areas Separator -->
            <div style="width: 100%; margin: 16px 0; padding: 12px 0; border-top: 2px solid rgba(212, 175, 55, 0.4); border-bottom: 2px solid rgba(212, 175, 55, 0.4); text-align: center;">
                <div style="font-size: 0.8rem; color: rgba(212, 175, 55, 0.8); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">━━ FACILITY AREAS ━━</div>
            </div>
            
            <div class="legend-item">
                <div class="legend-color bg-entrance"></div>
                <span class="text-white text-sm">Entrance</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-exit"></div>
                <span class="text-white text-sm">Exit</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-performance"></div>
                <span class="text-white text-sm">Performance</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-billiards"></div>
                <span class="text-white text-sm">Billiards</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-service-counter"></div>
                <span class="text-white text-sm">Service Counter</span>
            </div>
            <div class="legend-item">
                <div class="legend-color bg-kitchen-area"></div>
                <span class="text-white text-sm">Kitchen Area</span>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 mb-6 justify-center animate-fadeInUp" style="animation-delay: 0.2s">
            <button class="btn-grill flex items-center gap-2" onclick="handleQuickReservation()">
                <span class="material-icons">event</span>
                Quick Reservation
            </button>
            <button class="btn-grill flex items-center gap-2" onclick="handleViewBookings()">
                <span class="material-icons">list_alt</span>
                View My Bookings
            </button>
            <button class="btn-grill flex items-center gap-2" onclick="handleMenuDeals()">
                <span class="material-icons">restaurant_menu</span>
                Menu & Special Offers
            </button>
        </div>

        <div class="interactive-card p-4 mb-6 animate-fadeInUp" style="animation-delay: 0.3s">
            <div class="table-filter-container mb-4">
                <div class="flex flex-wrap justify-between items-center">
                    <h3 class="text-lg font-bold text-[#FFD700]">🎯 Select Your Table</h3>
                    <div class="flex space-x-3">
                        <select id="tableStatusFilter" class="filter-select">
                            <option value="all">All Tables</option>
                            <option value="available">Available Only</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="request_cancel">Request Cancel</option>
                            <option value="request_reschedule">Request Reschedule</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-auto scrollbar-hidden">
                <div class="table-grid-compact" id="tableContainer">
                    <!-- Row 1 -->
                    <?php
                    $tableName = 'ENTRANCE';
                    $isNonReservable = isNonReservableFacility($tableName);
                    $statusClass = $isNonReservable ? 'facility-item bg-entrance' : getStatusClass('available');
                    $isClickable = isTableClickable($tableName, 'available');
                    $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                    $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', 'available')\"" : '';
                    $cursorClass = $isClickable ? 'cursor-pointer' : ($isNonReservable ? 'cursor-default' : 'cursor-not-allowed');
                    ?>
                    <div class="table-item table-item-filterable <?php echo $statusClass . ' ' . $clickClass . ' ' . $cursorClass; ?>" 
                         data-table="<?php echo htmlspecialchars($tableName); ?>" 
                         data-status="available"
                         data-clickable="<?php echo $isClickable ? 'true' : 'false'; ?>"
                         <?php echo $onclick; ?>
                         style="grid-column: 6 / 7; grid-row: 1;">
                        <span class="font-bold text-xs">🚪 ENTRANCE</span>
                    </div>
                    
                    <?php
                    $tableName = 'EXIT';
                    $isNonReservable = isNonReservableFacility($tableName);
                    $statusClass = $isNonReservable ? 'facility-item bg-exit' : getStatusClass('available');
                    $isClickable = isTableClickable($tableName, 'available');
                    $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                    $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', 'available')\"" : '';
                    $cursorClass = $isClickable ? 'cursor-pointer' : ($isNonReservable ? 'cursor-default' : 'cursor-not-allowed');
                    ?>
                    <div class="table-item table-item-filterable <?php echo $statusClass . ' ' . $clickClass . ' ' . $cursorClass; ?>" 
                         data-table="<?php echo htmlspecialchars($tableName); ?>" 
                         data-status="available"
                         data-clickable="<?php echo $isClickable ? 'true' : 'false'; ?>"
                         <?php echo $onclick; ?>
                         style="grid-column: 7 / 8; grid-row: 1;">
                        <span class="font-bold text-xs">🚶 EXIT</span>
                    </div>
                    
                    <!-- Row 2 -->
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
                        $isNonReservable = isNonReservableFacility($tableName);
                        $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                        $statusClass = $isNonReservable ? 'facility-item bg-gray' : getStatusClass($status);
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $filterableClass = 'table-item-filterable';
                        $cursorClass = $isClickable ? 'cursor-pointer' : ($isNonReservable ? 'cursor-default' : 'cursor-not-allowed');
                        
                        echo "<div class='table-item $statusClass $filterableClass $clickClass $cursorClass' 
                                data-table='" . htmlspecialchars($tableName) . "' 
                                data-status='$status'
                                data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                $onclick
                                style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable && !$isNonReservable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 3 -->
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
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 4 -->
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
                        $isNonReservable = isNonReservableFacility($tableName);
                        $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                        
                        // Determine specific color class for facilities
                        if ($isFacility || $isNonReservable) {
                            if ($tableName === 'PERFORMANCE') {
                                $statusClass = 'facility-item bg-performance';
                            } else {
                                $statusClass = 'facility-item bg-gray';
                            }
                        } else {
                            $statusClass = getStatusClass($status);
                        }
                        
                        $isClickable = ($isFacility || $isNonReservable) ? false : isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                        $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $filterableClass = 'table-item-filterable';
                        $cursorClass = $isClickable ? 'cursor-pointer' : (($isFacility || $isNonReservable) ? 'cursor-default' : 'cursor-not-allowed');
                        
                        echo "<div class='table-item $statusClass $filterableClass $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='$colspan $rowspan'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable && !$isFacility && !$isNonReservable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 5 -->
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
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 6 -->
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
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 7 -->
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
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 8 -->
                    <?php
                    $row8Tables = [
                        'A3' => ['col' => 1, 'row' => 8],
                        'B4' => ['col' => 2, 'row' => 8],
                        'C2' => ['col' => 7, 'row' => 8],
                        'D2' => ['col' => 8, 'row' => 8],
                        'RESERV.' => ['col' => 4, 'row' => 8, 'colspan' => 2],
                        'BILLIARDS' => ['col' => 11, 'row' => 8, 'colspan' => 2, 'facility' => true],
                    ];
                    
                    foreach ($row8Tables as $tableName => $pos) {
                        $isFacility = isset($pos['facility']) && $pos['facility'];
                        $isNonReservable = isNonReservableFacility($tableName);
                        $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                        
                        // Determine specific color class for facilities
                        if ($isFacility || $isNonReservable) {
                            if ($tableName === 'BILLIARDS') {
                                $statusClass = 'facility-item bg-billiards';
                            } else {
                                $statusClass = 'facility-item bg-gray';
                            }
                        } else {
                            $statusClass = getStatusClass($status);
                        }
                        
                        $isClickable = ($isFacility || $isNonReservable) ? false : isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                        $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $filterableClass = 'table-item-filterable';
                        $cursorClass = $isClickable ? 'cursor-pointer' : (($isFacility || $isNonReservable) ? 'cursor-default' : 'cursor-not-allowed');
                        
                        echo "<div class='table-item $statusClass $filterableClass $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='$colspan $rowspan'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable && !$isFacility && !$isNonReservable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 9 -->
                    <?php
                    $row9Tables = [
                        'A2' => ['col' => 1, 'row' => 9],
                        'B3' => ['col' => 2, 'row' => 9],
                        'C1' => ['col' => 7, 'row' => 9],
                        'D1' => ['col' => 8, 'row' => 9],
                        'MEETING' => ['col' => 4, 'row' => 9, 'colspan' => 2],
                        'VIP 1' => ['col' => 11, 'row' => 9, 'colspan' => 2],
                    ];
                    
                    foreach ($row9Tables as $tableName => $pos) {
                        $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                        $statusClass = getStatusClass($status);
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                        $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='$colspan $rowspan'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 10 -->
                    <?php
                    $row10Tables = [
                        'A1' => ['col' => 1, 'row' => 10],
                        'B2' => ['col' => 2, 'row' => 10],
                        'COMPLI' => ['col' => 4, 'row' => 10, 'colspan' => 2],
                    ];
                    
                    foreach ($row10Tables as $tableName => $pos) {
                        $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                        $statusClass = getStatusClass($status);
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $colspan = isset($pos['colspan']) ? "grid-column: {$pos['col']} / " . ($pos['col'] + $pos['colspan']) . ";" : "grid-column: {$pos['col']} / " . ($pos['col'] + 1) . ";";
                        $rowspan = isset($pos['rowspan']) ? "grid-row: {$pos['row']} / " . ($pos['row'] + $pos['rowspan']) . ";" : "grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";";
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='$colspan $rowspan'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- Row 11 -->
                    <?php
                    $row11Tables = [
                        'B1' => ['col' => 2, 'row' => 11],
                    ];
                    
                    foreach ($row11Tables as $tableName => $pos) {
                        $status = isset($tableStatusMap[$tableName]) ? $tableStatusMap[$tableName] : 'available';
                        $statusClass = getStatusClass($status);
                        $isClickable = isTableClickable($tableName, $status);
                        $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                        $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', '$status')\"" : '';
                        $cursorClass = $isClickable ? 'cursor-pointer' : 'cursor-not-allowed';
                        
                        echo "<div class='table-item $statusClass table-item-filterable $clickClass $cursorClass' 
                                 data-table='" . htmlspecialchars($tableName) . "' 
                                 data-status='$status'
                                 data-clickable='" . ($isClickable ? 'true' : 'false') . "'
                                 $onclick
                                 style='grid-column: {$pos['col']} / " . ($pos['col'] + 1) . "; grid-row: {$pos['row']} / " . ($pos['row'] + 1) . ";'>";
                        echo htmlspecialchars($tableName);
                        if (!$isClickable) {
                            echo "<div class='text-xs mt-1 font-normal'>" . ucfirst($status) . "</div>";
                        }
                        echo "</div>";
                    }
                    ?>
                    
                    <!-- SERVICE COUNTER -->
                    <?php
                    $tableName = 'SERVICE COUNTER';
                    $isNonReservable = isNonReservableFacility($tableName);
                    $statusClass = $isNonReservable ? 'facility-item bg-service-counter' : getStatusClass('available');
                    $isClickable = isTableClickable($tableName, 'available');
                    $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                    $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', 'available')\"" : '';
                    $cursorClass = $isClickable ? 'cursor-pointer' : ($isNonReservable ? 'cursor-default' : 'cursor-not-allowed');
                    ?>
                    <div class="table-item table-item-filterable <?php echo $statusClass . ' ' . $clickClass . ' ' . $cursorClass; ?>" 
                         data-table="<?php echo htmlspecialchars($tableName); ?>" 
                         data-status="available"
                         data-clickable="<?php echo $isClickable ? 'true' : 'false'; ?>"
                         <?php echo $onclick; ?>
                         style="grid-column: 7 / 9; grid-row: 10;">
                        <span class="font-bold text-xs">🔔 SERVICE COUNTER</span>
                    </div>

                    <!-- KITCHEN AREA -->
                    <?php
                    $tableName = 'KITCHEN AREA';
                    $isNonReservable = isNonReservableFacility($tableName);
                    $statusClass = $isNonReservable ? 'facility-item bg-kitchen-area' : getStatusClass('available');
                    $isClickable = isTableClickable($tableName, 'available');
                    $clickClass = $isClickable ? 'clickable' : 'non-clickable';
                    $onclick = $isClickable ? "onclick=\"handleTableClick('" . htmlspecialchars($tableName) . "', 'available')\"" : '';
                    $cursorClass = $isClickable ? 'cursor-pointer' : ($isNonReservable ? 'cursor-default' : 'cursor-not-allowed');
                    ?>
                    <div class="table-item table-item-filterable <?php echo $statusClass . ' ' . $clickClass . ' ' . $cursorClass; ?>" 
                         data-table="<?php echo htmlspecialchars($tableName); ?>" 
                         data-status="available"
                         data-clickable="<?php echo $isClickable ? 'true' : 'false'; ?>"
                         <?php echo $onclick; ?>
                         style="grid-column: 7 / 9; grid-row: 11;">
                        <span class="font-bold text-xs">👨‍🍳 KITCHEN AREA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="interactive-card py-4 border-t border-gray-700 w-full mt-6 animate-fadeInUp" style="animation-delay: 0.4s">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 text-center sm:text-left">
                <div class="flex items-center gap-3 bg-yellow-400/10 px-4 py-3 rounded-xl shadow-sm border border-yellow-400/20">
                    <div class="text-2xl text-yellow-400">ℹ️</div>
                    <div>
                        <p class="text-white text-sm leading-relaxed font-medium">
                            <span class="font-bold text-[#FFD700]">Note:</span> For groups larger than 6, please contact us directly. View our <a href="../static/resources/terms_and_condition.pdf" target="_blank" class="text-[#FFD700] font-bold hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFD700] rounded-lg px-1">Reservation Guidelines</a> and <a href="../static/resources/terms_and_condition.pdf" target="_blank" class="text-[#FFD700] font-bold hover:underline focus:outline-none focus:ring-2 focus:ring-[#FFD700] rounded-lg px-1">Policy Details</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" tabindex="-1" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="creative-modal w-full max-w-5xl mx-4 p-0 text-white relative max-h-[90vh] overflow-hidden">
        <div class="modal-header rounded-t-[12px] p-4 sticky top-0 z-10">
            <div class="flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-900">
                    🍽️ Reservation for Table: <span id="table_code_label" class="text-gray-900"></span>
                </h2>
                <button id="closeAddModal" class="text-gray-900 hover:text-gray-700 text-2xl font-bold transition cursor-pointer">
                    &times;
                </button>
            </div>
        </div>

        <div class="modal-scrollable scrollbar-hidden" style="max-height: 70vh; overflow-y: auto;">
            <form id="frmRequestReservation" class="space-y-6" enctype="multipart/form-data" autocomplete="off" novalidate>
                <input type="hidden" id="table_code" name="table_code" />
                <input type="hidden" id="menu_total" name="menu_total" value="0" />
                <input type="hidden" id="drink_total" name="drink_total" value="0" />
                <input type="hidden" id="promo_total" name="promo_total" value="0" />
                <input type="hidden" id="group_total" name="group_total" value="0" />
                <input type="hidden" id="grand_total" name="grand_total" value="0" />
                <input type="hidden" id="selected_menus" name="selected_menus" value="[]" />
                <input type="hidden" id="selected_drinks" name="selected_drinks" value="[]" />
                <input type="hidden" id="selected_promos" name="selected_promos" value="[]" />
                <input type="hidden" id="selected_groups" name="selected_groups" value="[]" />
                <input type="hidden" id="payment_type" name="payment_type" value="full" />
                <input type="hidden" id="amount_to_pay" name="amount_to_pay" value="0" />
                <input type="hidden" id="food_corkage_fee" name="food_corkage_fee" value="0" />
                <input type="hidden" id="drink_corkage_fee" name="drink_corkage_fee" value="0" />
                <input type="hidden" id="service_charge_amount" name="service_charge_amount" value="0" />
                <input type="hidden" id="corkage_fee" name="corkage_fee" value="0" />
                
                <div class="form-section p-4 interactive-card m-3">
                    <h3 class="text-lg font-bold text-[#FFD700] mb-3">👤 Your Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Full Name</label>
                            <div class="form-input-grill bg-gray-700 text-gray-300 border-gray-600">
                                <?php echo htmlspecialchars($user_name ?: 'Guest User'); ?>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Email Address</label>
                            <div class="form-input-grill bg-gray-700 text-gray-300 border-gray-600">
                                <?php echo htmlspecialchars($user_email ?: 'guest@example.com'); ?>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label for="customer_phone_input" class="form-label">Phone Number *</label>
                            <input type="tel" 
                                   id="customer_phone_input" 
                                   name="customer_phone" 
                                   class="form-input-grill w-full" 
                                   placeholder="Enter your phone number (e.g., 09171234567)"
                                   value="<?php echo htmlspecialchars($user_phone ?: ''); ?>"
                                   required>
                            <p class="text-xs text-gray-400 mt-1">This phone number will be used for reservation confirmation</p>
                        </div>
                    </div>
                </div>

                <div class="form-section p-4 interactive-card m-3">
                    <h3 class="text-lg font-bold text-[#FFD700] mb-3">📅 Schedule & Date</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="date_schedule" class="form-label">Reservation Date *</label>
                            <input type="text" id="date_schedule" name="date_schedule" required class="form-input-grill w-full datepicker" placeholder="Select date" />
                        </div>
                        
                        <div class="time-select-container">
                            <label for="time_schedule" class="form-label">Reservation Time *</label>
                            <select id="time_schedule" name="time_schedule" required class="w-full">
                                <option value="">Select Time</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="seats" class="form-label">Number of Guests *</label>
                        <input type="number" id="seats" name="seats" min="1" max="6" placeholder="Enter number of guests (1-6)" required class="form-input-grill w-full" />
                        <p class="text-red-400 text-xs mt-1">Maximum capacity: 6 guests per table</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="button" id="btnCheckAvailability" class="btn-grill">
                            Check Availability
                        </button>
                    </div>
                </div>

                <section class="py-8 px-4 bg-[#1A1A1A] relative" id="menu_section">
                    <div class="max-w-5xl mx-auto">
                        <h2 class="text-2xl text-center font-bold text-[#FFD700] uppercase mb-6">Selection of Food and Beverages</h2>
                        
                        <div class="menu-category-tabs">
                            <div class="menu-category-tab active" data-category="food">🍽️ Food Menu</div>
                            <div class="menu-category-tab" data-category="alcoholic">🍺 Alcoholic Drinks</div>
                        </div>

                        <div class="menu-category-content active" id="food-content">
                            <div class="swiper menuSwiper relative">
                                <div class="swiper-wrapper" id="menuContainer">
                                </div>
                                <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                                <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                            </div>
                        </div>

                        <div class="menu-category-content" id="alcoholic-content">
                            <div class="swiper alcoholicSwiper relative">
                                <div class="swiper-wrapper" id="alcoholicContainer">
                                </div>
                                <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                                <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                            </div>
                        </div>

                        <div class="menu-category-content" id="promo-content">
                            <div class="swiper promoSwiper relative">
                                <div class="swiper-wrapper" id="promoContainer">
                                </div>
                                <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                                <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                            </div>
                        </div>

                        <div class="menu-category-content" id="group-content">
                            <div class="swiper groupSwiper relative">
                                <div class="swiper-wrapper" id="groupContainer">
                                </div>
                                <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                                <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="form-section p-4 interactive-card m-3">
                    <h3 class="text-lg font-bold text-[#FFD700] mb-3">💰 Billing Summary</h3>
                    <div class="billing-summary-formal">
                        <div class="billing-header">Order Summary</div>
                        
                        <div class="billing-item">
                            <span class="billing-label">Menu Items Total:</span>
                            <span class="billing-value" id="menu-total">₱0.00</span>
                        </div>
                        
                        <div class="billing-item">
                            <span class="billing-label">Alcoholic Drinks Total:</span>
                            <span class="billing-value" id="drink-total">₱0.00</span>
                        </div>
                        
                        <div class="billing-item">
                            <span class="billing-label">Promo Deals Total:</span>
                            <span class="billing-value" id="promo-total">₱0.00</span>
                        </div>
                        
                        <div class="billing-item">
                            <span class="billing-label">Group Deals Total:</span>
                            <span class="billing-value" id="group-total">₱0.00</span>
                        </div>
                        
                        <div class="billing-item">
                            <span class="billing-label">Subtotal:</span>
                            <span class="billing-value" id="subtotal">₱0.00</span>
                        </div>
                        
                        <div class="corkage-section">
                            <div class="billing-item">
                                <span class="billing-label">Outside Food Fee:</span>
                                <span class="billing-value" id="food-corkage-fee">₱0.00</span>
                            </div>
                            <div class="corkage-input-group">
                                <span class="corkage-input-label">Number of outside food items:</span>
                                <input type="number" id="foodCorkageQuantity" name="foodCorkageQuantity" min="0" value="0" class="corkage-input">
                                <span class="corkage-rate">(₱100 per item)</span>
                            </div>
                            
                            <div class="billing-item mt-3">
                                <span class="billing-label">Outside Drink Fee:</span>
                                <span class="billing-value" id="drink-corkage-fee">₱0.00</span>
                            </div>
                            <div class="corkage-input-group">
                                <span class="corkage-input-label">Number of 100ml outside drinks:</span>
                                <input type="number" id="drinkCorkageQuantity" name="drinkCorkageQuantity" min="0" value="0" class="corkage-input">
                                <span class="corkage-rate">(₱100 per 100ml)</span>
                            </div>
                            <p class="corkage-info mt-2 text-sm text-gray-400">* Corkage fee: ₱100 per outside food item or per 100ml of outside drink</p>
                        </div>
                        
                        <div class="service-charge-section">
                            <div class="billing-item">
                                <span class="billing-label">Service Charge (10%):</span>
                                <span class="billing-value" id="service-charge">₱0.00</span>
                            </div>
                        </div>
                        
                        <div class="billing-item billing-total">
                            <span class="billing-label">Grand Total:</span>
                            <span class="billing-value" id="grand-total">₱0.00</span>
                        </div>
                    </div>
                </div>

                <div class="form-section p-4 interactive-card m-3">
                    <h3 class="text-lg font-bold text-[#FFD700] mb-3">💳 Payment Type</h3>
                    <div class="payment-type-container">
                        <div class="payment-type-option selected" data-type="full">
                            <h4 class="font-bold text-[#FFD700]">Full Payment</h4>
                            <p class="text-sm text-gray-300">Pay the full amount now</p>
                            <div class="payment-type-amount" id="full-payment-amount">₱0.00</div>
                        </div>
                        <div class="payment-type-option" data-type="half">
                            <h4 class="font-bold text-[#FFD700]">50% Downpayment</h4>
                            <p class="text-sm text-gray-300">Pay half now, half upon arrival</p>
                            <div class="payment-type-amount" id="half-payment-amount">₱0.00</div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                        <p class="text-sm text-blue-300 text-center">
                            <span id="selected-payment-type">Full Payment</span> Selected: 
                            <span class="font-bold" id="amount-due">₱0.00</span>
                        </p>
                    </div>
                </div>

                <div class="form-section p-4 interactive-card m-3" id="payment_details">
                    <h3 class="text-lg font-bold text-[#FFD700] mb-3">💳 Payment Details</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <select id="payment_method" name="payment_method" class="form-input-grill" required>
                                <option value="">-- Select Payment Method --</option>
                                <option value="cash">Cash (Pay at Restaurant)</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                <option value="bpi">BPI Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                        
                        <div id="online_payment_details" class="space-y-3 hidden">
                            <div id="payment_text" class="payment-instructions"></div>
                            <div class="payment-qr-container">
                                <img id="payment_qr" src="" alt="QR Code" class="payment-qr-image">
                                <a id="download_qr_btn" href="#" download class="btn-grill text-sm py-2 px-4 mt-3">
                                    <span class="material-icons mr-2">download</span>
                                    Download QR Code
                                </a>
                            </div>
                            <div>
                                <label for="payment_proof" class="form-label">Upload Payment Proof *</label>
                                <input type="file" id="payment_proof" name="payment_proof" accept="image/*,.pdf" class="form-input-grill" />
                                <p id="fileNamePreview" class="text-sm text-gray-400 mt-1 text-center"></p>
                            </div>
                        </div>

                        <div id="cash_payment_note" class="p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg hidden">
                            <p class="text-sm text-yellow-300 text-center">
                                💡 Please bring the exact amount for payment when you visit the restaurant.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-section p-4 interactive-card m-3">
                    <h3 class="text-lg font-bold text-[#FFD700] mb-3">📝 Terms & Conditions</h3>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3 p-3 bg-[#1A1A1A] rounded-lg border border-gray-600">
                            <input type="checkbox" id="terms" name="terms" required class="mt-1 rounded text-[#FFD700] focus:ring-[#FFD700] w-4 h-4" />
                            <label for="terms" class="text-xs select-none text-gray-300">
                                I agree to the <a href="../static/resources/terms_and_condition.pdf" target="_blank" class="underline text-[#FFD700] hover:text-yellow-300"> Terms and Conditions </a>
                                including the cancellation policy and payment terms.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center space-y-3 m-3">
                    <p id="availabilityInstruction" class="flex items-center justify-center text-xs text-white bg-red-500 px-3 py-2 rounded-lg">
                        <span class="material-icons mr-2 text-sm">event_busy</span>
                        Please check the availability of your date schedule before submitting.
                    </p>
                    <button type="submit" id="submitBtn" disabled class="btn-grill cursor-not-allowed opacity-50">
                        Submit Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Menu Only Modal -->
<div id="menuOnlyModal" role="dialog" aria-modal="true" aria-labelledby="menuModalTitle" tabindex="-1" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="creative-modal menu-only-modal w-full max-w-5xl mx-4 p-0 text-white relative max-h-[90vh] overflow-hidden">
        <div class="modal-header rounded-t-[12px] p-4 sticky top-0 z-10">
            <div class="flex justify-between items-center">
                <h2 id="menuModalTitle" class="text-xl font-bold text-white">
                    🍽️ Our Menu & Special Offers
                </h2>
                <button id="closeMenuModal" class="text-white hover:text-gray-300 text-2xl font-bold transition cursor-pointer">
                    &times;
                </button>
            </div>
        </div>

        <div class="modal-scrollable scrollbar-hidden" style="max-height: 70vh; overflow-y: auto;">
            <section class="py-8 px-4 bg-[#1A1A1A] relative" id="menu_only_section">
                <div class="max-w-5xl mx-auto">
                    <h2 class="text-2xl text-center font-bold text-[#FFD700] uppercase mb-6">Menu & Deals</h2>
                    
                    <div class="menu-category-tabs">
                        <div class="menu-category-tab active" data-category="food">🍽️ Food Menu</div>
                        <div class="menu-category-tab" data-category="alcoholic">🍺 Alcoholic Drinks</div>
                    </div>

                    <div class="menu-category-content active" id="menu-only-food-content">
                        <div class="swiper menuOnlySwiper relative">
                            <div class="swiper-wrapper" id="menuOnlyContainer">
                            </div>
                            <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                            <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                        </div>
                    </div>

                    <div class="menu-category-content" id="menu-only-alcoholic-content">
                        <div class="swiper alcoholicOnlySwiper relative">
                            <div class="swiper-wrapper" id="alcoholicOnlyContainer">
                            </div>
                            <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                            <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block"></button>
                        </div>
                    </div>

                    <div class="menu-category-content" id="menu-only-promo-content">
                        <div class="swiper promoOnlySwiper relative">
                            <div class="swiper-wrapper" id="promoOnlyContainer">
                            </div>
                            <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                            <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                        </div>
                    </div>

                    <div class="menu-category-content" id="menu-only-group-content">
                        <div class="swiper groupOnlySwiper relative">
                            <div class="swiper-wrapper" id="groupOnlyContainer">
                            </div>
                            <button class="swiper-button-prev absolute top-1/2 left-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                            <button class="swiper-button-next absolute top-1/2 right-2 transform -translate-y-1/2 z-10 p-2 rounded-full shadow-lg !block opacity-100"></button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Chatbot -->
<div class="chatbot-container">
    <div class="chatbot-modal" id="chatbotModal">
        <div class="chatbot-header">
            <span>Reservation Assistant</span>
            <button class="chatbot-close" id="chatbotClose">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                Hello! I'm here to help you with your reservation. How can I assist you today?
            </div>
            <div class="chatbot-message bot">
                Here are the steps to make a reservation:
                <br>1. Select an available table (white color)
                <br>2. Choose your date and time
                <br>3. Browse and select from Food Menu, Alcoholic Drinks, Promo Deals, or Group Deals
                <br>4. Choose payment method
                <br>5. Submit your reservation
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" class="chatbot-input-field" id="chatbotInput" placeholder="Type your question...">
            <button class="chatbot-send" id="chatbotSend">Send</button>
        </div>
    </div>
    <button class="chatbot-button" id="chatbotButton">
        <span class="chatbot-button-icon">💬</span>
    </button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const CONTROLLER_URL = "../../controller/end-points/controller.php";
    console.log("Controller URL:", CONTROLLER_URL);
    
    // JavaScript version of getStatusClass()
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
            default:
                return 'bg-white';
        }
    }
    
    function isTableClickable(tableName, status) {
        const nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];
        if (nonReservableFacilities.includes(tableName)) {
            return false;
        }
        
        return status === 'available';
    }

    function isNonReservableFacility(tableName) {
        const nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];
        return nonReservableFacilities.includes(tableName);
    }
        
    const operationHours = {
        0: { open: "17:00", close: "03:00", next_day: true },
        1: { open: "17:00", close: "02:00", next_day: true },
        2: { open: "17:00", close: "02:00", next_day: true },
        3: { open: "17:00", close: "02:00", next_day: true },
        4: { open: "17:00", close: "02:00", next_day: true },
        5: { open: "19:00", close: "04:00", next_day: true },
        6: { open: "19:00", close: "04:00", next_day: true }
    };
    
    let selectedTable = null;
    let isTableAvailable = false;
    let isTermsAccepted = false;
    let currentPaymentType = 'full';
    let orderSummary = {
        menuItems: {},
        drinkItems: {},
        promoDeals: {},
        groupDeals: {},
        foodCorkageFee: 0,
        drinkCorkageFee: 0,
        serviceChargeRate: 0.10,
        foodCorkageRate: 100,
        drinkCorkageRate: 100
    };

    let autoUpdateInterval = null;
    let initialTableData = {};
    
    // Add emergency active variable
    let emergencyActive = <?php echo $emergencyActive ? 'true' : 'false'; ?>;

    function validatePhoneNumber(phone) {
        const cleaned = phone.replace(/\D/g, '');
        
        if (cleaned.length < 10 || cleaned.length > 12) {
            return false;
        }
        
        if (!cleaned.startsWith('09') && !cleaned.startsWith('639')) {
            return false;
        }
        
        return true;
    }

    function fetchAlcoholicDrinks(modalType = 'main') {
        const containerId = modalType === 'menu-only' ? 'alcoholicOnlyContainer' : 'alcoholicContainer';
        
        $.ajax({
            url: CONTROLLER_URL,
            method: "GET",
            data: { 
                requestType: "fetch_all_menu", 
                category: "alcoholic" 
            },
            dataType: "json",
            success: function (response) {
                console.log("Alcoholic drinks response:", response); // Debug log
                
                if (response.status === 200 && response.data && response.data.length > 0) {
                    const container = $(`#${containerId}`).empty(); 

                    response.data.forEach(drink => {
                        const description = drink.menu_description || drink.description || 'Premium alcoholic beverage.';
                        const imgSrc = `../static/upload/menu/${drink.menu_image_banner || drink.drink_image_banner || drink.image || 'default_drink.jpg'}`;
                        const imgAlt = drink.menu_name || drink.drink_name;
                        const price = drink.menu_price || drink.drink_price || 0;
                        const id = drink.menu_id || drink.drink_id;
                        const name = drink.menu_name || drink.drink_name;
                        const category = drink.menu_category || drink.drink_category || 'Alcoholic';
                        const volume = drink.volume || 'Standard';
                        
                        container.append(`
                            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] shadow-lg relative text-center w-72">
                                ${modalType === 'main' ? `
                                <div class="absolute top-4 left-4">
                                    <input type="checkbox" class="w-5 h-5 accent-[#FFD700] cursor-pointer drink-checkbox"
                                        value="${id}" name="drink_select[]" data-type="drink" data-id="${id}" data-price="${price}" data-name="${name}" />
                                </div>
                                ` : ''}

                                <button type="button" class="w-full text-left focus:outline-none" data-id="${id}">
                                    <img src="${imgSrc}" 
                                        alt="${imgAlt}" 
                                        class="w-full h-40 object-cover rounded-lg mb-4"
                                        onerror="this.src='../static/upload/default_drink.jpg'; this.onerror=null;" />
                                    <h3 class="text-xl font-bold text-[#FFD700] mb-1">${name}</h3>
                                    <p class="text-[#CCCCCC] text-sm mb-2">${category} | ${volume}</p>
                                    <p class="text-[#CCCCCC] text-sm mb-2">Price: ₱${price}</p>
                                    <div class="menu-description-container">
                                        <p class="menu-item-description">${description}</p>
                                    </div>
                                </button>

                                ${modalType === 'main' ? `
                                <div class="flex items-center justify-center mt-2 space-x-2">
                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition"
                                        data-target="drink-${id}" 
                                        data-change="-1"
                                        data-type="drink"
                                        data-id="${id}"
                                        data-price="${price}"
                                        data-name="${name}"
                                    >−</button>

                                    <input 
                                        type="number" 
                                        min="0" 
                                        max="10"
                                        value="0" 
                                        id="drink-${id}"
                                        name="drink_quantity[${id}]" 
                                        class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1 quantity-input" 
                                        data-type="drink"
                                        data-id="${id}"
                                        data-price="${price}"
                                        data-name="${name}"
                                    />

                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition"
                                        data-target="drink-${id}" 
                                        data-change="1"
                                        data-type="drink"
                                        data-id="${id}"
                                        data-price="${price}"
                                        data-name="${name}"
                                    >+</button>
                                </div>
                                ` : ''}
                            </div>
                        `);
                    });

                    const swiperClass = modalType === 'menu-only' ? '.alcoholicOnlySwiper' : '.alcoholicSwiper';
                    initSwiper(swiperClass);
                    
                    if (modalType === 'main') {
                        attachQtyHandlers();
                    }
                } else {
                    console.error("No alcoholic drinks data received:", response);
                    $(`#${containerId}`).html('<div class="text-center text-gray-400 py-8">No alcoholic drinks available at the moment.</div>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading alcoholic drinks:", error);
                $(`#${containerId}`).html('<div class="text-center text-red-400 py-8">Error loading alcoholic drinks.</div>');
            }
        });
    }

    function initializeChatBot() {
        const chatbotButton = document.getElementById('chatbotButton');
        const chatbotModal = document.getElementById('chatbotModal');
        const chatbotClose = document.getElementById('chatbotClose');
        const chatbotSend = document.getElementById('chatbotSend');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotMessages = document.getElementById('chatbotMessages');

        chatbotButton.addEventListener('click', () => {
            chatbotModal.style.display = 'flex';
        });

        chatbotClose.addEventListener('click', () => {
            chatbotModal.style.display = 'none';
        });

        chatbotSend.addEventListener('click', sendMessage);
        chatbotInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const message = chatbotInput.value.trim();
            if (message) {
                addMessage(message, 'user');
                chatbotInput.value = '';

                setTimeout(() => {
                    const response = getBotResponse(message);
                    addMessage(response, 'bot');
                }, 1000);
            }
        }

        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${sender}`;
            messageDiv.textContent = text;
            chatbotMessages.appendChild(messageDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function getBotResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            if (lowerMessage.includes('step') || lowerMessage.includes('how') || lowerMessage.includes('reserve')) {
                return "Here are the reservation steps:\n1. Select an available table (white color)\n2. Choose your date and time\n3. Browse and select from Food Menu, Alcoholic Drinks, Promo Deals, or Group Deals\n4. Choose your payment method\n5. Submit your reservation\n6. Wait for confirmation email";
            } else if (lowerMessage.includes('table') || lowerMessage.includes('available')) {
                return "Available tables are shown in white color. Click on any white table to start your reservation. Tables in other colors are reserved or unavailable.";
            } else if (lowerMessage.includes('menu') || lowerMessage.includes('food') || lowerMessage.includes('drink')) {
                return "We have 4 menu categories:\n🍽️ Food Menu - Main dishes and appetizers\n🍺 Alcoholic Drinks - Beer, cocktails, and spirits\n🎉 Promo Deals - Special combo offers\n👥 Group Deals - Perfect for parties and groups";
            } else if (lowerMessage.includes('payment') || lowerMessage.includes('pay')) {
                return "We accept:\n- Cash (pay at restaurant)\n- GCash\n- Maya\n- BPI Bank Transfer\n- PayPal\nYou can choose full payment or 50% downpayment.";
            } else if (lowerMessage.includes('time') || lowerMessage.includes('hour')) {
                return "Our operating hours:\nSun: 5PM-3AM\nMon-Thu: 5PM-2AM (next day)\nFri-Sat: 7PM-4AM (next day)\nCurrent status is shown at the top of the page.";
            } else if (lowerMessage.includes('cancel') || lowerMessage.includes('refund')) {
                return "Cancellation policy:\n- Free cancellation 24 hours before reservation\n- 50% charge for late cancellations\n- No refund for no-shows\nView full terms in the reservation modal.";
            } else {
                return "I'm here to help with reservations! You can ask about:\n- Reservation steps\n- Table availability\n- Menu options\n- Payment methods\n- Operating hours\n- Cancellation policy";
            }
        }
    }

    function initializeMenuCategoryTabs() {
        const tabs = document.querySelectorAll('#scheduleModal .menu-category-tab');
        const contents = document.querySelectorAll('#scheduleModal .menu-category-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const category = tab.getAttribute('data-category');
                
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                
                tab.classList.add('active');
                document.getElementById(`${category}-content`).classList.add('active');
                
                loadMenuCategory(category, 'main');
            });
        });

        const menuOnlyTabs = document.querySelectorAll('#menuOnlyModal .menu-category-tab');
        const menuOnlyContents = document.querySelectorAll('#menuOnlyModal .menu-category-content');

        menuOnlyTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const category = tab.getAttribute('data-category');
                
                menuOnlyTabs.forEach(t => t.classList.remove('active'));
                menuOnlyContents.forEach(c => c.classList.remove('active'));
                
                tab.classList.add('active');
                document.getElementById(`menu-only-${category}-content`).classList.add('active');
                
                loadMenuCategory(category, 'menu-only');
            });
        });
    }

    function loadMenuCategory(category, modalType = 'main') {
        const containerSuffix = modalType === 'menu-only' ? 'OnlyContainer' : 'Container';
        const containerId = `${category}${containerSuffix}`;
        
        switch(category) {
            case 'food':
                if (document.getElementById(containerId).children.length === 0) {
                    fetchFoodMenu(modalType);
                }
                break;
            case 'alcoholic':
                if (document.getElementById(containerId).children.length === 0) {
                    fetchAlcoholicDrinks(modalType);
                }
                break;
            case 'promo':
                if (document.getElementById(containerId).children.length === 0) {
                    fetchPromos(modalType);
                }
                break;
            case 'group':
                if (document.getElementById(containerId).children.length === 0) {
                    fetchGroups(modalType);
                }
                break;
        }
    }

    function initializeTableStatuses() {
        const tableItems = document.querySelectorAll('.table-item[data-table]');
        tableItems.forEach(table => {
            const tableName = table.getAttribute('data-table');
            const isFacility = table.classList.contains('facility-item');
            const currentStatus = table.getAttribute('data-status') || 'available';
            
            initialTableData[tableName] = {
                element: table,
                isFacility: isFacility,
                defaultStatus: currentStatus
            };
            
            const phpStatus = <?php echo json_encode($tableStatusMap); ?>;
            if (phpStatus[tableName] && !isFacility) {
                const status = phpStatus[tableName];
                table.setAttribute('data-status', status);
                table.className = `table-item ${getStatusClass(status)} ${isTableClickable(tableName, status) ? 'clickable' : 'non-clickable'}`;
            } else if (!isFacility) {
                table.setAttribute('data-status', 'available');
                table.className = 'table-item bg-white clickable';
            }
        });
    }

    function updateTableStatuses() {
        if (!CONTROLLER_URL) {
            console.error("CONTROLLER_URL is not defined");
            return;
        }
        
        const today = new Date().toISOString().split('T')[0];
        
        $.ajax({
            url: CONTROLLER_URL,
            method: "GET",
            data: { 
                requestType: "get_realtime_table_status",
                date: today
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    updateTableColors(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating table statuses:', error);
            }
        });
    }

    function updateTableColors(tableStatusMap) {
        if (!tableStatusMap || typeof tableStatusMap !== 'object') {
            return;
        }
        
        Object.keys(initialTableData).forEach(tableName => {
            const tableInfo = initialTableData[tableName];
            if (!tableInfo.isFacility) {
                const tableElement = tableInfo.element;
                const status = tableStatusMap[tableName] || 'available';
                const isClickable = isTableClickable(tableName, status);
                
                tableElement.className = `table-item ${getStatusClass(status)} ${isClickable ? 'clickable' : 'non-clickable'}`;
                tableElement.setAttribute('data-status', status);
                tableElement.setAttribute('data-clickable', isClickable);
                
                const statusText = status.charAt(0).toUpperCase() + status.slice(1);
                tableElement.title = `Table ${tableName}: ${statusText}`;
                
                // Update onclick handler
                if (isClickable) {
                    tableElement.onclick = function() {
                        handleTableClick(tableName, status);
                    };
                    tableElement.style.cursor = 'pointer';
                } else {
                    tableElement.onclick = null;
                    tableElement.style.cursor = 'not-allowed';
                    
                    // Update status text if it exists
                    const statusDiv = tableElement.querySelector('.text-xs');
                    if (statusDiv) {
                        statusDiv.textContent = ucfirst(status);
                    }
                }
            }
        });
    }

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function updateOperatingStatus() {
        const now = new Date();
        const currentDay = now.getDay();
        const currentTime = now.toTimeString().slice(0, 5);
        const currentHours = operationHours[currentDay];
        
        if (!currentHours) {
            const statusIndicator = document.querySelector('.status-indicator');
            statusIndicator.textContent = 'CLOSED';
            statusIndicator.className = 'status-indicator closed';
            return;
        }
        
        const openTime = currentHours.open;
        const closeTime = currentHours.close;
        const isNextDay = currentHours.next_day;
        let isOpen = false;
        
        const [currentHour, currentMinute] = currentTime.split(':').map(Number);
        const [openHour, openMinute] = openTime.split(':').map(Number);
        const [closeHour, closeMinute] = closeTime.split(':').map(Number);
        
        const currentTotalMinutes = currentHour * 60 + currentMinute;
        const openTotalMinutes = openHour * 60 + openMinute;
        const closeTotalMinutes = closeHour * 60 + closeMinute;
        
        if (isNextDay) {
            if (currentTotalMinutes >= openTotalMinutes) {
                isOpen = true;
            } else if (currentTotalMinutes <= closeTotalMinutes) {
                isOpen = true;
            }
        } else {
            isOpen = (currentTotalMinutes >= openTotalMinutes && currentTotalMinutes <= closeTotalMinutes);
        }
        
        const statusIndicator = document.querySelector('.status-indicator');
        statusIndicator.textContent = isOpen ? 'OPEN NOW' : 'CLOSED';
        statusIndicator.className = `status-indicator ${isOpen ? 'open' : 'closed'}`;
        
        document.querySelectorAll('.compact-hour-item').forEach((item, index) => {
            if (index === currentDay) {
                item.classList.add('current');
            } else {
                item.classList.remove('current');
            }
        });
    }

    function initializePaymentTypeSelection() {
        const paymentOptions = document.querySelectorAll('.payment-type-option');
        
        paymentOptions.forEach(option => {
            option.addEventListener('click', function() {
                paymentOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                currentPaymentType = this.getAttribute('data-type');
                document.getElementById('payment_type').value = currentPaymentType;
                
                const paymentTypeDisplay = currentPaymentType === 'full' ? 'Full Payment' : '50% Downpayment';
                document.getElementById('selected-payment-type').textContent = paymentTypeDisplay;
                
                updatePaymentAmounts();
            });
        });
    }

    function updatePaymentAmounts() {
        const grandTotal = parseFloat(document.getElementById('grand_total').value) || 0;
        let amountDue = grandTotal;
        
        if (currentPaymentType === 'half') {
            amountDue = grandTotal * 0.5;
        }
        
        document.getElementById('full-payment-amount').textContent = `₱${grandTotal.toFixed(2)}`;
        document.getElementById('half-payment-amount').textContent = `₱${(grandTotal * 0.5).toFixed(2)}`;
        document.getElementById('amount-due').textContent = `₱${amountDue.toFixed(2)}`;
        document.getElementById('amount_to_pay').value = amountDue.toFixed(2);
    }

    function initializeCalendar() {
        const dateInput = document.getElementById('date_schedule');
        
        if (typeof flatpickr !== "undefined" && dateInput) {
            flatpickr(dateInput, {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: [
                    function(date) {
                        const day = date.getDay();
                        return !operationHours[day];
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    updateTimeSlots(dateStr);
                },
                onOpen: function(selectedDates, dateStr, instance) {
                    document.getElementById('scheduleModal').style.zIndex = '10001';
                },
                onClose: function(selectedDates, dateStr, instance) {
                    document.getElementById('scheduleModal').style.zIndex = '50';
                }
            });
        } else {
            dateInput.type = 'date';
            dateInput.min = new Date().toISOString().split('T')[0];
            dateInput.addEventListener('change', function() {
                updateTimeSlots(this.value);
            });
        }
    }

    function formatTime24to12(time24) {
        if (!time24) return '';
        const [hour, minute] = time24.split(':');
        let h = parseInt(hour, 10);
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return `${h}:${minute} ${ampm}`;
    }

        function generateTimeSlots(start, end, isNextDay) {
        const slots = [];
        const [startHour, startMinute] = start.split(':').map(Number);
        const [endHour, endMinute] = end.split(':').map(Number);
        
        // Convert times to minutes for easier comparison
        const startTotalMinutes = startHour * 60 + startMinute;
        let endTotalMinutes = endHour * 60 + endMinute;
        
        // If it's next day closure, add 24 hours to end time
        if (isNextDay) {
            endTotalMinutes += 24 * 60;
        }
        
        // Calculate cutoff time (90 minutes before closing)
        const cutoffTotalMinutes = endTotalMinutes - 90;
        
        // Start from opening time
        let currentTotalMinutes = startTotalMinutes;
        
        // Generate 30-minute slots until we reach cutoff
        while (currentTotalMinutes < cutoffTotalMinutes) {
            // Calculate current hour and minute (handle day rollover)
            let displayHour = Math.floor(currentTotalMinutes / 60) % 24;
            const displayMinute = currentTotalMinutes % 60;
            
            // Format the time
            const timeString = `${String(displayHour).padStart(2, '0')}:${String(displayMinute).padStart(2, '0')}`;
            slots.push(timeString);
            
            // Add 30 minutes for next slot
            currentTotalMinutes += 30;
            
            // Safety break to prevent infinite loops
            if (slots.length > 48) break;
        }
        
        return slots;
    }

    function updateTimeSlots(selectedDate) {
        const selectedDay = new Date(selectedDate).getDay();
        const currentHours = operationHours[selectedDay];
        const timeSelect = document.getElementById('time_schedule');
        
        if (!currentHours) {
            timeSelect.innerHTML = '<option value="">No operating hours for selected date</option>';
            return;
        }
        
        const openTime = currentHours.open;
        const closeTime = currentHours.close;
        const isNextDay = currentHours.next_day;
        
        // Get time slots with 90-minute cutoff
        const timeSlots = generateTimeSlots(openTime, closeTime, isNextDay);
        
        // Clear existing options
        timeSelect.innerHTML = '<option value="">Select Time</option>';
        
        // Use Set to prevent duplicates
        const uniqueSlots = new Set();
        
        timeSlots.forEach(slot => {
            if (!uniqueSlots.has(slot)) {
                uniqueSlots.add(slot);
                const option = document.createElement('option');
                option.value = slot;
                option.textContent = formatTime24to12(slot);
                timeSelect.appendChild(option);
            }
        });
        
        // Show cutoff info
        showCutoffInfo(selectedDate);
    }

        function showCutoffInfo(selectedDate) {
        const timeSelect = document.getElementById('time_schedule');
        if (!timeSelect || !selectedDate) return;
        
        // Remove existing cutoff info
        const existingInfo = document.getElementById('cutoff-info');
        if (existingInfo) {
            existingInfo.remove();
        }
        
        const selectedDay = new Date(selectedDate).getDay();
        const currentHours = operationHours[selectedDay];
        
        if (!currentHours) return;
        
        const openTime = currentHours.open;
        const closeTime = currentHours.close;
        const isNextDay = currentHours.next_day;
        
        const [openHour, openMinute] = openTime.split(':').map(Number);
        const [closeHour, closeMinute] = closeTime.split(':').map(Number);
        
        // Calculate cutoff time (90 minutes before closing)
        let cutoffHour = closeHour;
        let cutoffMinute = closeMinute - 90;
        
        while (cutoffMinute < 0) {
            cutoffHour -= 1;
            cutoffMinute += 60;
        }
        
        if (cutoffHour < 0) {
            cutoffHour += 24;
        }
        
        const cutoffTimeStr = `${String(cutoffHour).padStart(2, '0')}:${String(cutoffMinute).padStart(2, '0')}`;
        const formattedCutoff = formatTime24to12(cutoffTimeStr);
        const formattedOpen = formatTime24to12(openTime);
        const formattedClose = formatTime24to12(closeTime);
        
        // Create cutoff info element
        const cutoffInfo = document.createElement('div');
        cutoffInfo.id = 'cutoff-info';
        cutoffInfo.className = 'mt-2 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg text-xs';
        cutoffInfo.innerHTML = `
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-yellow-400" style="font-size: 16px;">schedule</span>
                    <span class="text-yellow-300 font-semibold">Operating Hours:</span>
                    <span class="text-white">${formattedOpen} - ${formattedClose} ${isNextDay ? '(next day)' : ''}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-icons text-yellow-400" style="font-size: 16px;">access_time</span>
                    <span class="text-yellow-300 font-semibold">Last Reservation:</span>
                    <span class="text-white">${formattedCutoff}</span>
                    <span class="text-gray-400 text-xs ml-auto">(90 mins before closing)</span>
                </div>
            </div>
        `;
        
        // Insert after the time select container
        timeSelect.parentNode.appendChild(cutoffInfo);
    }

    function initializeAvailabilityChecker() {
        const checkAvailabilityBtn = document.getElementById('btnCheckAvailability');
        const submitBtn = document.getElementById('submitBtn');
        const availabilityInstruction = document.getElementById('availabilityInstruction');
        
        if (checkAvailabilityBtn) {
            checkAvailabilityBtn.addEventListener('click', function() {
                const date = document.getElementById('date_schedule').value;
                const time = document.getElementById('time_schedule').value;
                const tableCode = document.getElementById('table_code').value;
                
                if (!date || !time || !tableCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please select a date, time, and table first.',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        color: '#e5e5e5'
                    });
                    return;
                }
                
                $.ajax({
                    url: CONTROLLER_URL,
                    method: "GET",
                    data: { 
                        requestType: "checkAvailability",
                        table_code: tableCode,
                        date_schedule: date,
                        time_schedule: time
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.availability) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Table Available!',
                                text: `Table ${tableCode} is available on ${date} at ${formatTime24to12(time)}. You can now proceed with your reservation.`,
                                confirmButtonColor: '#d4af37',
                                background: '#1a1a1a',
                                color: '#e5e5e5'
                            });
                            
                            isTableAvailable = true;
                            updateFormState();
                            
                            availabilityInstruction.innerHTML = '<span class="material-icons mr-2 text-sm">event_available</span> Table is available! You can now submit your reservation.';
                            availabilityInstruction.className = 'flex items-center justify-center text-xs text-white bg-green-500 px-3 py-2 rounded-lg';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Table Not Available',
                                text: `Table ${tableCode} is not available on ${date} at ${formatTime24to12(time)}. Please choose a different date, time, or table.`,
                                confirmButtonColor: '#d4af37',
                                background: '#1a1a1a',
                                color: '#e5e5e5'
                            });
                            
                            isTableAvailable = false;
                            updateFormState();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to check availability. Please try again.',
                            confirmButtonColor: '#d4af37',
                            background: '#1a1a1a',
                            color: '#e5e5e5'
                        });
                    }
                });
            });
        }
    }

    function updateBillingSummary() {
        let menuTotal = 0;
        Object.values(orderSummary.menuItems).forEach(item => {
            menuTotal += item.price * item.quantity;
        });
        
        let drinkTotal = 0;
        Object.values(orderSummary.drinkItems).forEach(item => {
            drinkTotal += item.price * item.quantity;
        });
        
        let promoTotal = 0;
        Object.values(orderSummary.promoDeals).forEach(deal => {
            promoTotal += deal.price * deal.quantity;
        });
        
        let groupTotal = 0;
        Object.values(orderSummary.groupDeals).forEach(deal => {
            groupTotal += deal.price * deal.quantity;
        });
        
        const subtotal = menuTotal + drinkTotal + promoTotal + groupTotal;
        const serviceCharge = subtotal * orderSummary.serviceChargeRate;
        const totalCorkage = orderSummary.foodCorkageFee + orderSummary.drinkCorkageFee;
        const grandTotal = subtotal + serviceCharge + totalCorkage;
        
        // Update display
        document.getElementById('menu-total').textContent = `₱${menuTotal.toFixed(2)}`;
        document.getElementById('drink-total').textContent = `₱${drinkTotal.toFixed(2)}`;
        document.getElementById('promo-total').textContent = `₱${promoTotal.toFixed(2)}`;
        document.getElementById('group-total').textContent = `₱${groupTotal.toFixed(2)}`;
        document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
        document.getElementById('service-charge').textContent = `₱${serviceCharge.toFixed(2)}`;
        document.getElementById('grand-total').textContent = `₱${grandTotal.toFixed(2)}`;
        document.getElementById('food-corkage-fee').textContent = `₱${orderSummary.foodCorkageFee.toFixed(2)}`;
        document.getElementById('drink-corkage-fee').textContent = `₱${orderSummary.drinkCorkageFee.toFixed(2)}`;
        
        // Update hidden fields
        document.getElementById('menu_total').value = menuTotal;
        document.getElementById('drink_total').value = drinkTotal;
        document.getElementById('promo_total').value = promoTotal;
        document.getElementById('group_total').value = groupTotal;
        document.getElementById('grand_total').value = grandTotal;
        document.getElementById('food_corkage_fee').value = orderSummary.foodCorkageFee;
        document.getElementById('drink_corkage_fee').value = orderSummary.drinkCorkageFee;
        document.getElementById('corkage_fee').value = totalCorkage;
        document.getElementById('service_charge_amount').value = serviceCharge;
        
        document.getElementById('selected_menus').value = JSON.stringify(orderSummary.menuItems);
        document.getElementById('selected_drinks').value = JSON.stringify(orderSummary.drinkItems);
        document.getElementById('selected_promos').value = JSON.stringify(orderSummary.promoDeals);
        document.getElementById('selected_groups').value = JSON.stringify(orderSummary.groupDeals);
        
        updatePaymentAmounts();
    }

    function initializeCorkageHandler() {
        const foodCorkageQuantity = document.getElementById('foodCorkageQuantity');
        const drinkCorkageQuantity = document.getElementById('drinkCorkageQuantity');
        const foodCorkageFeeElement = document.getElementById('food-corkage-fee');
        const drinkCorkageFeeElement = document.getElementById('drink-corkage-fee');
        
        if (foodCorkageQuantity) {
            foodCorkageQuantity.addEventListener('input', function() {
                const quantity = parseInt(this.value) || 0;
                orderSummary.foodCorkageFee = quantity * orderSummary.foodCorkageRate;
                foodCorkageFeeElement.textContent = `₱${orderSummary.foodCorkageFee.toFixed(2)}`;
                updateBillingSummary();
            });
        }
        
        if (drinkCorkageQuantity) {
            drinkCorkageQuantity.addEventListener('input', function() {
                const quantity = parseInt(this.value) || 0;
                orderSummary.drinkCorkageFee = quantity * orderSummary.drinkCorkageRate;
                drinkCorkageFeeElement.textContent = `₱${orderSummary.drinkCorkageFee.toFixed(2)}`;
                updateBillingSummary();
            });
        }
    }

    function fetchFoodMenu(modalType = 'main') {
        const containerId = modalType === 'menu-only' ? 'menuOnlyContainer' : 'menuContainer';
        
        $.ajax({
            url: CONTROLLER_URL,
            method: "GET",
            data: { requestType: "fetch_all_menu", category: "food" },
            dataType: "json",
            success: function (response) {
                if (response.status === 200 && response.data.length > 0) {
                    const container = $(`#${containerId}`).empty(); 

                    response.data.forEach(menu => {
                        const description = menu.menu_description || 'Delicious dish prepared with fresh ingredients.';
                        const imgSrc = `../static/upload/menu/${menu.menu_image_banner}`;
                        const imgAlt = menu.menu_name;
                        
                        container.append(`
                            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] shadow-lg relative text-center w-72">
                                ${modalType === 'main' ? `
                                <div class="absolute top-4 left-4">
                                    <input type="checkbox" class="w-5 h-5 accent-[#FFD700] cursor-pointer menu-checkbox"
                                        value="${menu.menu_id}" name="menu_select[]" data-type="menu" data-id="${menu.menu_id}" data-price="${menu.menu_price}" data-name="${menu.menu_name}" />
                                </div>
                                ` : ''}

                                <button type="button" class="w-full text-left focus:outline-none" data-id="${menu.menu_id}">
                                    <img src="${imgSrc}" 
                                        alt="${imgAlt}" 
                                        class="w-full h-40 object-cover rounded-lg mb-4"
                                        onerror="this.src='../static/upload/default_menu.jpg'; this.onerror=null;" />
                                    <h3 class="text-xl font-bold text-[#FFD700] mb-1">${menu.menu_name}</h3>
                                    <p class="text-[#CCCCCC] text-sm mb-2">Price: ₱${menu.menu_price}</p>
                                    <div class="menu-description-container">
                                        <p class="menu-item-description">${description}</p>
                                    </div>
                                </button>

                                ${modalType === 'main' ? `
                                <div class="flex items-center justify-center mt-2 space-x-2">
                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition"
                                        data-target="menu-${menu.menu_id}" 
                                        data-change="-1"
                                        data-type="menu"
                                        data-id="${menu.menu_id}"
                                        data-price="${menu.menu_price}"
                                        data-name="${menu.menu_name}"
                                    >−</button>

                                    <input 
                                        type="number" 
                                        min="0" 
                                        max="10"
                                        value="0" 
                                        id="menu-${menu.menu_id}"
                                        name="menu_quantity[${menu.menu_id}]" 
                                        class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1 quantity-input" 
                                        data-type="menu"
                                        data-id="${menu.menu_id}"
                                        data-price="${menu.menu_price}"
                                        data-name="${menu.menu_name}"
                                    />

                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition"
                                        data-target="menu-${menu.menu_id}" 
                                        data-change="1"
                                        data-type="menu"
                                        data-id="${menu.menu_id}"
                                        data-price="${menu.menu_price}"
                                        data-name="${menu.menu_name}"
                                    >+</button>
                                </div>
                                ` : ''}
                            </div>
                        `);
                    });

                    const swiperClass = modalType === 'menu-only' ? '.menuOnlySwiper' : '.menuSwiper';
                    initSwiper(swiperClass);
                    
                    if (modalType === 'main') {
                        attachQtyHandlers();
                    }
                } else {
                    $(`#${containerId}`).html('<div class="text-center text-gray-400 py-8">No food items available at the moment.</div>');
                }
            },
            error: function (xhr, status, error) {
                $(`#${containerId}`).html('<div class="text-center text-red-400 py-8">Error loading food menu.</div>');
            }
        });
    }

    function fetchPromos(modalType = 'main') {
        const containerId = modalType === 'menu-only' ? 'promoOnlyContainer' : 'promoContainer';
        
        $.ajax({
            url: CONTROLLER_URL,
            method: "GET",
            data: { requestType: "fetch_all_deals_and_menu", deal_type: "promo_deals" },
            dataType: "json",
            success: function (res) {
                const container = $(`#${containerId}`).empty();

                if (res.status === 200 && res.data.length > 0) {
                    res.data.forEach(deal => {
                        let menusHTML = '';
                        if (deal.menus && deal.menus.length > 0) {
                            menusHTML = `
                                <div class="mt-3 space-y-2 hidden menu-list" id="menuList-${deal.deal_id}">
                                    ${deal.menus.map(menu => `
                                        <div class="flex items-center bg-[#1E1E1E] p-2 rounded-lg">
                                            <img src="../static/upload/menu/${menu.menu_image_banner}" 
                                                 alt="${menu.menu_name}" 
                                                 class="w-12 h-12 object-cover rounded-md mr-3"
                                                 onerror="this.src='../static/upload/menu/default_menu.jpg'; this.onerror=null;" />
                                            <div class="flex-1">
                                                <p class="text-[#FFD700] font-semibold">${menu.menu_name}</p>
                                                <p class="text-[#CCCCCC] text-sm">₱${parseFloat(menu.menu_price).toFixed(2)}</p>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                        }

                        const description = deal.deal_description || 'Special combo offer with great value!';
                        const imgSrc = `../static/upload/deals/${deal.deal_img_banner}`;
                        const imgAlt = deal.deal_name;
                        
                        container.append(`
                            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] w-72 shadow-lg relative text-center">
                                ${modalType === 'main' ? `
                                <input type="checkbox" 
                                    class="absolute top-3 left-3 w-5 h-5 accent-[#FFD700] cursor-pointer promo-checkbox" 
                                    value="${deal.deal_id}" 
                                    name="promo_select[]" 
                                    data-type="promo"
                                    data-id="${deal.deal_id}"
                                    data-price="${deal.total_price}"
                                    data-name="${deal.deal_name}" />
                                ` : ''}

                                <button type="button" class="w-full text-left focus:outline-none" data-id="${deal.deal_id}">
                                    <img src="${imgSrc}" 
                                        alt="${imgAlt}" 
                                        class="w-full h-40 object-cover rounded-lg mb-4"
                                        onerror="this.src='../static/upload/default_deal.jpg'; this.onerror=null;" />
                                    <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                                    <p class="text-[#CCCCCC] text-sm mb-2">Total Price: ₱${parseFloat(deal.total_price).toFixed(2)}</p>
                                    <div class="menu-description-container">
                                        <p class="menu-item-description">${description}</p>
                                    </div>
                                </button>

                                ${modalType === 'main' ? `
                                <div class="flex items-center justify-center mt-2 space-x-2">
                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                                        data-target="promo-${deal.deal_id}" 
                                        data-change="-1"
                                        data-type="promo"
                                        data-id="${deal.deal_id}"
                                        data-price="${deal.total_price}"
                                        data-name="${deal.deal_name}"
                                    >−</button>

                                    <input 
                                        type="number" 
                                        min="0" 
                                        max="10"
                                        value="0" 
                                        id="promo-${deal.deal_id}"
                                        name="promo_quantity[${deal.deal_id}]" 
                                        class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1 quantity-input" 
                                        data-type="promo"
                                        data-id="${deal.deal_id}"
                                        data-price="${deal.total_price}"
                                        data-name="${deal.deal_name}"
                                    />

                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                                        data-target="promo-${deal.deal_id}" 
                                        data-change="1"
                                        data-type="promo"
                                        data-id="${deal.deal_id}"
                                        data-price="${deal.total_price}"
                                        data-name="${deal.deal_name}"
                                    >+</button>
                                </div>
                                ` : ''}

                                <button 
                                    type="button" 
                                    class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                                    data-id="${deal.deal_id}"
                                >
                                    Show Menu
                                </button>

                                ${menusHTML}
                            </div>
                        `);
                    });

                    const swiperClass = modalType === 'menu-only' ? '.promoOnlySwiper' : '.promoSwiper';
                    initSwiper(swiperClass);
                    
                    if (modalType === 'main') {
                        attachQtyHandlers();
                    }

                    $(`#${containerId} .toggle-menu-btn`).off("click").on("click", function () {
                        const id = $(this).data("id");
                        const menuList = $(`#menuList-${id}`);
                        const isHidden = menuList.hasClass("hidden");
                        $(this).text(isHidden ? "Hide Menu" : "Show Menu");
                        menuList.toggleClass("hidden");
                    });

                } else {
                    $(`#${containerId}`).html('<div class="text-center text-gray-400 py-8">No promo deals available at the moment.</div>');
                }
            },
            error: function(xhr, status, error) {
                $(`#${containerId}`).html('<div class="text-center text-red-400 py-8">Error loading promo deals.</div>');
            }
        });
    }

    function fetchGroups(modalType = 'main') {
        const containerId = modalType === 'menu-only' ? 'groupOnlyContainer' : 'groupContainer';
        
        $.ajax({
            url: CONTROLLER_URL,
            method: "GET",
            data: { requestType: "fetch_group_deals" },
            dataType: "json",
            success: function (res) {
                const container = $(`#${containerId}`).empty();

                if (res.status === 'success' && res.data && res.data.length > 0) {
                    res.data.forEach(deal => {
                        let menusHTML = '';
                        if (deal.menus && deal.menus.length > 0) {
                            menusHTML = `
                                <div class="mt-3 space-y-2 hidden menu-list" id="menuList-${deal.deal_id}">
                                    ${deal.menus.map(menu => `
                                        <div class="flex items-center bg-[#1E1E1E] p-2 rounded-lg">
                                            <img src="../static/upload/${menu.menu_image_banner}" 
                                                 alt="${menu.menu_name}" 
                                                 class="w-12 h-12 object-cover rounded-md mr-3"
                                                 onerror="this.src='../static/upload/default_menu.jpg'; this.onerror=null;" />
                                            <div class="flex-1">
                                                <p class="text-[#FFD700] font-semibold">${menu.menu_name}</p>
                                                <p class="text-[#CCCCCC] text-sm">₱${parseFloat(menu.menu_price).toFixed(2)}</p>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                        }

                        const description = deal.deal_description || 'Perfect for group gatherings and celebrations!';
                        const totalPrice = 0;
                        const imgSrc = `../static/upload/${deal.deal_img_banner || 'default_deal.jpg'}`;
                        const imgAlt = deal.deal_name;
                        
                        container.append(`
                            <div class="swiper-slide bg-[#2B2B2B] p-6 rounded-xl border border-[#333] w-72 shadow-lg relative text-center">
                                ${modalType === 'main' ? `
                                <input type="checkbox" 
                                    class="absolute top-3 left-3 w-5 h-5 accent-[#FFD700] cursor-pointer group-checkbox" 
                                    value="${deal.deal_id}" 
                                    name="group_select[]" 
                                    data-type="group"
                                    data-id="${deal.deal_id}"
                                    data-price="0"
                                    data-name="${deal.deal_name}" />
                                ` : ''}

                                <button type="button" class="w-full text-left focus:outline-none" data-id="${deal.deal_id}">
                                    <img src="${imgSrc}" 
                                        alt="${imgAlt}" 
                                        class="w-full h-40 object-cover rounded-lg mb-4"
                                        onerror="this.src='../static/upload/default_deal.jpg'; this.onerror=null;" />
                                    <h3 class="text-xl font-bold text-[#FFD700] mb-1">${deal.deal_name}</h3>
                                    <p class="text-[#CCCCCC] text-sm mb-2">Group Deal Package</p>
                                    <div class="menu-description-container">
                                        <p class="menu-item-description">${description}</p>
                                    </div>
                                </button>

                                ${modalType === 'main' ? `
                                <div class="flex items-center justify-center mt-2 space-x-2">
                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                                        data-target="group-${deal.deal_id}" 
                                        data-change="-1"
                                        data-type="group"
                                        data-id="${deal.deal_id}"
                                        data-price="0"
                                        data-name="${deal.deal_name}"
                                    >−</button>

                                    <input 
                                        type="number" 
                                        min="0" 
                                        max="10"
                                        value="0" 
                                        id="group-${deal.deal_id}"
                                        name="group_quantity[${deal.deal_id}]" 
                                        class="w-16 text-center rounded-md bg-[#1E1E1E] border border-[#555] text-white p-1 quantity-input" 
                                        data-type="group"
                                        data-id="${deal.deal_id}"
                                        data-price="0"
                                        data-name="${deal.deal_name}"
                                    />

                                    <button 
                                        type="button" 
                                        class="qty-btn bg-[#FFD700] text-black px-2 rounded-md text-lg font-bold cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                                        data-target="group-${deal.deal_id}" 
                                        data-change="1"
                                        data-type="group"
                                        data-id="${deal.deal_id}"
                                        data-price="0"
                                        data-name="${deal.deal_name}"
                                    >+</button>
                                </div>
                                ` : ''}

                                ${deal.menus && deal.menus.length > 0 ? `
                                <button 
                                    type="button" 
                                    class="toggle-menu-btn mt-3 px-3 py-1 bg-[#FFD700] text-black rounded-lg text-sm cursor-pointer hover:bg-yellow-400 transition hover:scale-105"
                                    data-id="${deal.deal_id}"
                                >
                                    Show Menu
                                </button>
                                ` : ''}

                                ${menusHTML}
                            </div>
                        `);
                    });

                    const swiperClass = modalType === 'menu-only' ? '.groupOnlySwiper' : '.groupSwiper';
                    initSwiper(swiperClass);
                    
                    if (modalType === 'main') {
                        attachQtyHandlers();
                    }

                    $(`#${containerId} .toggle-menu-btn`).off("click").on("click", function () {
                        const id = $(this).data("id");
                        const menuList = $(`#menuList-${id}`);
                        const isHidden = menuList.hasClass("hidden");
                        $(this).text(isHidden ? "Hide Menu" : "Show Menu");
                        menuList.toggleClass("hidden");
                    });

                } else {
                    $(`#${containerId}`).html('<div class="text-center text-gray-400 py-8">No group deals available at the moment.</div>');
                }
            },
            error: function(xhr, status, error) {
                $(`#${containerId}`).html('<div class="text-center text-red-400 py-8">Error loading group deals.</div>');
            }
        });
    }

    function attachQtyHandlers() {
        $(".qty-btn").off("click").on("click", function () {
            const targetId = $(this).data("target");
            const change = parseInt($(this).data("change"));
            const type = $(this).data("type");
            const id = $(this).data("id");
            const price = parseFloat($(this).data("price")) || 0;
            const name = $(this).data("name");
            const input = $("#" + targetId);
            let currentValue = parseInt(input.val()) || 0;

            let newValue = currentValue + change;

            if (newValue < 0) {
                newValue = 0;
            } else if (newValue > 10) {
                newValue = 10;
            }

            input.val(newValue);
            
            updateOrderSummary(type, id, newValue, price, name);
            
            const checkbox = $(`.${type}-checkbox[data-id="${id}"]`);
            if (newValue > 0) {
                checkbox.prop('checked', true);
            } else {
                checkbox.prop('checked', false);
            }
        });

        $(".quantity-input").off("input").on("input", function () {
            let val = $(this).val().trim();
            const type = $(this).data("type");
            const id = $(this).data("id");
            const price = parseFloat($(this).data("price")) || 0;
            const name = $(this).data("name");

            if (val === "") {
                $(this).val(0);
                val = 0;
            }

            if (isNaN(val)) {
                $(this).val(0);
                return;
            }

            val = parseInt(val, 10);

            if (val < 0) {
                val = 0;
            } else if (val > 10) {
                val = 10;
            }

            $(this).val(val);
            
            updateOrderSummary(type, id, val, price, name);
            
            const checkbox = $(`.${type}-checkbox[data-id="${id}"]`);
            if (val > 0) {
                checkbox.prop('checked', true);
            } else {
                checkbox.prop('checked', false);
            }
        });

        $(".menu-checkbox, .drink-checkbox, .promo-checkbox, .group-checkbox").on("change", function() {
            const type = $(this).data("type");
            const id = $(this).data("id");
            const price = parseFloat($(this).data("price")) || 0;
            const name = $(this).data("name");
            const input = $(`#${type}-${id}`);
            
            if ($(this).is(":checked")) {
                if (input.val() == 0) {
                    input.val(1);
                    updateOrderSummary(type, id, 1, price, name);
                }
            } else {
                input.val(0);
                updateOrderSummary(type, id, 0, price, name);
            }
        });
    }

    function updateOrderSummary(type, id, quantity, price, name) {
        const item = {
            id: id,
            name: name || `${type} item ${id}`,
            price: price,
            quantity: quantity,
            total: price * quantity
        };

        switch(type) {
            case 'menu':
                if (quantity > 0) {
                    orderSummary.menuItems[id] = item;
                } else {
                    delete orderSummary.menuItems[id];
                }
                break;
            case 'drink':
                if (quantity > 0) {
                    orderSummary.drinkItems[id] = item;
                } else {
                    delete orderSummary.drinkItems[id];
                }
                break;
            case 'promo':
                if (quantity > 0) {
                    orderSummary.promoDeals[id] = item;
                } else {
                    delete orderSummary.promoDeals[id];
                }
                break;
            case 'group':
                if (quantity > 0) {
                    orderSummary.groupDeals[id] = item;
                } else {
                    delete orderSummary.groupDeals[id];
                }
                break;
        }

        updateBillingSummary();
    }

    function initSwiper(selector) {
        if (typeof Swiper !== 'undefined') {
            new Swiper(selector, {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                },
            });
        }
    }

    function initializePaymentHandler() {
        $("#payment_method").on("change", function () {
            const selected = $(this).val();
            const onlinePaymentDetails = $("#online_payment_details");
            const cashPaymentNote = $("#cash_payment_note");
            
            onlinePaymentDetails.addClass("hidden");
            cashPaymentNote.addClass("hidden");
            
            if (selected === "cash") {
                cashPaymentNote.removeClass("hidden");
                $("#payment_proof").val("");
                $("#fileNamePreview").text("");
            } else if (selected && selected !== "") {
                onlinePaymentDetails.removeClass("hidden");
                
                let qrSrc = "";
                let text = "";

                if (selected === "gcash") {
                    text = "Send payment to GCash number: 0917-123-4567 (Juan Dela Cruz)";
                    qrSrc = "../static/qr/grillbook_gcash_qr.jpg";
                } 
                else if (selected === "bpi") {
                    text = "Transfer to BPI Account: 1234-5678-90 (Juan Dela Cruz)";
                    qrSrc = "../static/qr/bpi.webp";
                } 
                else if (selected === "maya") {
                    text = "Send payment to Maya account: 0917-987-6543 (Juan Dela Cruz)";
                    qrSrc = "../static/qr/maya.jpg";
                } 
                else if (selected === "paypal") {
                    text = "Pay via PayPal: paypal.me/juanpay";
                    qrSrc = "../static/qr/paypal.webp";
                }

                if (qrSrc) {
                    $("#payment_text").text(text);
                    $("#payment_qr").attr("src", qrSrc);
                    $("#download_qr_btn").attr("href", qrSrc);
                    $("#qrFullPicture").attr("src", qrSrc);

                }
            }
        });
    }

    function handleTableClick(tableCode, status) {
        // Check if emergency closure is active
        if (emergencyActive) {
            Swal.fire({
                icon: 'error',
                title: 'Emergency Closure',
                html: `🚨 <strong>EMERGENCY CLOSURE NOTICE</strong> 🚨<br><br>
                      The restaurant is temporarily closed due to:<br>
                      <strong><?php echo htmlspecialchars($emergencyStatus['reason'] ?? 'Emergency situation'); ?></strong><br><br>
                      Date: <?php echo date('F j, Y', strtotime($emergencyStatus['closure_date'] ?? date('Y-m-d'))); ?><br><br>
                      <em>All reservations are suspended until further notice.</em>`,
                confirmButtonText: 'Understood',
                confirmButtonColor: '#DC2626',
                background: '#1a1a1a',
                color: '#e5e5e5',
                width: '500px'
            });
            return;
        }
        
        // Check if table is clickable
        if (!isTableClickable(tableCode, status)) {
            let message = '';
            let icon = 'info';
            
            const nonReservableFacilities = ['ENTRANCE', 'EXIT', 'PERFORMANCE', 'BILLIARDS', 'SERVICE COUNTER', 'KITCHEN AREA'];

            if (nonReservableFacilities.includes(tableCode)) {
                message = 'This is a facility area and cannot be reserved.';
                icon = 'info';
            }
            
            if (status === 'pending') {
                message = 'This table has a pending reservation. Please select another table.';
                icon = 'warning';
            } else if (status === 'confirmed') {
                message = 'This table is confirmed for reservation. Please select another table.';
                icon = 'info';
            } else if (status === 'cancelled') {
                message = 'This table was cancelled. Please select another table.';
                icon = 'info';
            } else if (status === 'request_cancel') {
                message = 'This table has a cancellation request. Please select another table.';
                icon = 'warning';
            } else if (status === 'request_reschedule') {
                message = 'This table has a reschedule request. Please select another table.';
                icon = 'warning';
            } else if (status === 'unavailable') {
                message = 'This table is currently unavailable. Please select another table.';
                icon = 'error';
            } else {
                message = 'This table is not available for reservation.';
                icon = 'error';
            }
            
            Swal.fire({
                icon: icon,
                title: `Table ${status ? ucfirst(status) : 'Not Available'}`,
                text: message,
                confirmButtonColor: '#d4af37',
                background: '#1a1a1a',
                color: '#e5e5e5'
            });
            return;
        }
        
        // Original table click logic for available tables
        if (status === 'available') {
            if (['RESERV.', 'MEETING', 'COMPLI', 'VIP 1', 'VIP 2', 'VIP 3'].includes(tableCode)) {
                Swal.fire({
                    icon: 'info',
                    title: 'Special Table',
                    html: `Table <strong>${tableCode}</strong> is available for reservation.<br><br>
                          ${getSpecialTableDescription(tableCode)}`,
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#e5e5e5'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedWithTableSelection(tableCode);
                    }
                });
            } else {
                proceedWithTableSelection(tableCode);
            }
        } else {
            // This should not happen since we already checked isTableClickable
            Swal.fire({
                icon: 'error',
                title: 'Table Not Available',
                text: 'This table is not available for reservation.',
                confirmButtonColor: '#d4af37',
                background: '#1a1a1a',
                color: '#e5e5e5'
            });
        }
    }

    function getSpecialTableDescription(tableCode) {
        const descriptions = {
            'RESERV.': 'This table is designated for reservations but can be booked like regular tables.',
            'MEETING': 'Perfect for business meetings or group discussions.',
            'COMPLI': 'Compliance area table, suitable for various occasions.',
            'VIP 1': 'Premium VIP table with exclusive features.',
            'VIP 2': 'Premium VIP table with exclusive features.',
            'VIP 3': 'Premium VIP table with exclusive features.'
        };
        return descriptions[tableCode] || 'Special table available for reservation.';
    }

    function proceedWithTableSelection(tableCode) {
        const tableElement = document.querySelector(`[data-table="${tableCode}"]`);
        if (tableElement) {
            tableElement.style.animation = 'pulse 0.3s ease-in-out';
            setTimeout(() => {
                tableElement.style.animation = '';
            }, 300);
        }
        
        selectedTable = tableCode;
        document.getElementById('table_code').value = tableCode;
        document.getElementById('table_code_label').textContent = tableCode;
        
        document.getElementById('scheduleModal').classList.remove('hidden');
        
        setTimeout(() => {
            initializeCalendar();
            
            let today = new Date();
            today.setDate(today.getDate() + 1);
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let tomorrow = `${yyyy}-${mm}-${dd}`;
            document.getElementById('date_schedule').value = tomorrow;
            
            updateTimeSlots(tomorrow);
        }, 100);
        
        isTableAvailable = false;
        isTermsAccepted = false;
        updateFormState();
        
        fetchFoodMenu();
        
        setTimeout(() => {
            document.querySelector('.modal-scrollable').scrollTop = 0;
        }, 200);
    }

    function updateFormState() {
        const shouldEnable = isTableAvailable && isTermsAccepted;

        $("#submitBtn")
            .prop("disabled", !shouldEnable)
            .toggleClass("cursor-not-allowed opacity-50", !shouldEnable)
            .toggleClass("opacity-100 cursor-pointer", shouldEnable);

        if (shouldEnable) {
            $("#availabilityInstruction")
                .removeClass("bg-red-500")
                .addClass("bg-green-600")
                .html(`
                    <span class="material-icons mr-2">check_circle</span>
                    Table is available and terms accepted. You may now submit your reservation.
                `);
        } else {
            $("#availabilityInstruction")
                .removeClass("bg-green-600")
                .addClass("bg-red-500")
                .html(`
                    <span class="material-icons mr-2">event_busy</span>
                    Please check the availability of your date schedule before submitting.
                `);
        }
    }

    window.handleQuickReservation = function() {
        if (emergencyActive) {
            Swal.fire({
                icon: 'error',
                title: 'Emergency Closure',
                html: `🚨 <strong>RESERVATIONS SUSPENDED</strong> 🚨<br><br>
                      Due to an emergency closure, we are not accepting reservations at this time.<br><br>
                      <strong>Reason:</strong> <?php echo htmlspecialchars($emergencyStatus['reason'] ?? 'Emergency situation'); ?><br>
                      <strong>Date:</strong> <?php echo date('F j, Y', strtotime($emergencyStatus['closure_date'] ?? date('Y-m-d'))); ?><br><br>
                      Please check back later or contact us for updates.`,
                confirmButtonText: 'Understood',
                confirmButtonColor: '#DC2626',
                background: '#1a1a1a',
                color: '#e5e5e5'
            });
            return;
        }
        
        const availableTables = document.querySelectorAll('.table-item[data-status="available"]');
        if (availableTables.length > 0) {
            document.getElementById('tableContainer').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            Swal.fire({
                title: 'Quick Reservation',
                text: 'Please select an available table (white color) to start your reservation.',
                icon: 'info',
                confirmButtonText: 'Got it!',
                confirmButtonColor: '#d4af37',
                background: '#1a1a1a',
                color: '#e5e5e5'
            });
        } else {
            Swal.fire({
                title: 'No Available Tables',
                text: 'All tables are currently reserved. Please check back later.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d4af37',
                background: '#1a1a1a',
                color: '#e5e5e5'
            });
        }
    };
    
    window.handleViewBookings = function() {
        window.location.href = 'reservation.php';
    };
    
    window.handleMenuDeals = function() {
        document.getElementById('menuOnlyModal').classList.remove('hidden');
        
        if (document.getElementById('menuOnlyContainer').children.length === 0) {
            fetchFoodMenu('menu-only');
        }
        
        document.querySelector('#menuOnlyModal .menu-category-tab[data-category="food"]').classList.add('active');
        document.getElementById('menu-only-food-content').classList.add('active');
    };

    function initializeClocks() {
        function updateClock(clockElement) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: true, 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
            
            if (clockElement) {
                clockElement.textContent = timeString;
            }
            
            const currentDateElement = document.getElementById('currentDate');
            if (currentDateElement) {
                const dateString = now.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                currentDateElement.textContent = dateString;
            }
        }
        
        const liveClock = document.getElementById('liveClock');
        if (liveClock) updateClock(liveClock);
        setInterval(() => {
            if (liveClock) updateClock(liveClock);
        }, 1000);
    }

    function initializeReservationModal() {
        const closeModalBtn = document.getElementById('closeAddModal');
        const scheduleModal = document.getElementById('scheduleModal');
        const closeMenuModalBtn = document.getElementById('closeMenuModal');
        const menuOnlyModal = document.getElementById('menuOnlyModal');
        const termsCheckbox = document.getElementById('terms');
        
        if (closeModalBtn && scheduleModal) {
            closeModalBtn.addEventListener('click', function() {
                scheduleModal.classList.add('hidden');
                resetOrderSummary();
            });
            
            scheduleModal.addEventListener('click', function(e) {
                if (e.target === scheduleModal) {
                    scheduleModal.classList.add('hidden');
                    resetOrderSummary();
                }
            });
        }

        if (closeMenuModalBtn && menuOnlyModal) {
            closeMenuModalBtn.addEventListener('click', function() {
                menuOnlyModal.classList.add('hidden');
            });
            
            menuOnlyModal.addEventListener('click', function(e) {
                if (e.target === menuOnlyModal) {
                    menuOnlyModal.classList.add('hidden');
                }
            });
        }

        if (termsCheckbox) {
            termsCheckbox.addEventListener('change', function() {
                isTermsAccepted = this.checked;
                updateFormState();
            });
        }
    }
    
    function resetOrderSummary() {
        orderSummary = {
            menuItems: {},
            drinkItems: {},
            promoDeals: {},
            groupDeals: {},
            foodCorkageFee: 0,
            drinkCorkageFee: 0,
            serviceChargeRate: 0.10,
            foodCorkageRate: 100,
            drinkCorkageRate: 100
        };
        updateBillingSummary();
        
        const foodCorkageQuantity = document.getElementById('foodCorkageQuantity');
        const drinkCorkageQuantity = document.getElementById('drinkCorkageQuantity');
        if (foodCorkageQuantity) {
            foodCorkageQuantity.value = 0;
        }
        if (drinkCorkageQuantity) {
            drinkCorkageQuantity.value = 0;
        }
        
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.value = 0;
        });
        document.querySelectorAll('.menu-checkbox, .drink-checkbox, .promo-checkbox, .group-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        currentPaymentType = 'full';
        document.getElementById('payment_type').value = 'full';
        document.querySelectorAll('.payment-type-option').forEach(option => {
            option.classList.remove('selected');
        });
        document.querySelector('.payment-type-option[data-type="full"]').classList.add('selected');
        updatePaymentAmounts();
    }

    function initializeFormSubmission() {
        const form = document.getElementById('frmRequestReservation');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const seats = document.getElementById('seats').value;
                const date = document.getElementById('date_schedule').value;
                const time = document.getElementById('time_schedule').value;
                const terms = document.getElementById('terms').checked;
                const paymentMethod = document.getElementById('payment_method').value;
                const paymentProof = document.getElementById('payment_proof').files[0];
                const phone = document.getElementById('customer_phone_input').value;
                
                if (!phone || phone.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Phone Number Required',
                        text: 'Please enter your phone number.',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        color: '#e5e5e5'
                    });
                    return;
                }
                
                if (!validatePhoneNumber(phone)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Phone Number',
                        text: 'Please enter a valid Philippine phone number (e.g., 09171234567).',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        color: '#e5e5e5'
                    });
                    return;
                }
                
                if (!seats || !date || !time || !terms || !paymentMethod) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Form',
                        text: 'Please fill in all required fields and agree to the terms and conditions.',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        color: '#e5e5e5'
                    });
                    return;
                }

                if (paymentMethod !== 'cash') {
                    if (!paymentProof) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Proof Required',
                            text: 'Please upload payment proof for online payment methods.',
                            confirmButtonColor: '#d4af37',
                            background: '#1a1a1a',
                            color: '#e5e5e5'
                        });
                        return;
                    }
                    
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
                    if (!allowedTypes.includes(paymentProof.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Type',
                            text: 'Please upload JPG, PNG, GIF, or PDF files only.',
                            confirmButtonColor: '#d4af37',
                            background: '#1a1a1a',
                            color: '#e5e5e5'
                        });
                        return;
                    }
                }
                
                const totalItems = Object.keys(orderSummary.menuItems).length + 
                                Object.keys(orderSummary.drinkItems).length +
                                Object.keys(orderSummary.promoDeals).length + 
                                Object.keys(orderSummary.groupDeals).length;
                
                if (totalItems === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Items Selected',
                        text: 'Please select at least one menu item, drink, promo, or group deal.',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        color: '#e5e5e5'
                    });
                    return;
                }

                const grandTotal = parseFloat(document.getElementById('grand_total').value) || 0;
                const amountToPay = parseFloat(document.getElementById('amount_to_pay').value) || 0;
                const paymentType = currentPaymentType === 'full' ? 'Full Payment' : '50% Downpayment';
                const remainingAmount = currentPaymentType === 'half' ? (grandTotal - amountToPay) : 0;
                const foodCorkageQuantity = parseInt(document.getElementById('foodCorkageQuantity').value) || 0;
                const drinkCorkageQuantity = parseInt(document.getElementById('drinkCorkageQuantity').value) || 0;
                const foodCorkageFee = orderSummary.foodCorkageFee || 0;
                const drinkCorkageFee = orderSummary.drinkCorkageFee || 0;

                let menuTotal = 0;
                Object.values(orderSummary.menuItems).forEach(item => {
                    menuTotal += item.total;
                });
                
                let drinkTotal = 0;
                Object.values(orderSummary.drinkItems).forEach(item => {
                    drinkTotal += item.total;
                });
                
                let promoTotal = 0;
                Object.values(orderSummary.promoDeals).forEach(deal => {
                    promoTotal += deal.total;
                });
                
                let groupTotal = 0;
                Object.values(orderSummary.groupDeals).forEach(deal => {
                    groupTotal += deal.total;
                });
                
                const subtotal = menuTotal + drinkTotal + promoTotal + groupTotal;
                const serviceCharge = subtotal * orderSummary.serviceChargeRate;

                let summaryHTML = `<div style="text-align:left; color: white;">`;
                summaryHTML += `<h3 style="color: #FFD700; border-bottom: 1px solid #444; padding-bottom: 10px;">Reservation Summary</h3>`;
                
                summaryHTML += `<p><strong>Table:</strong> ${selectedTable}</p>`;
                summaryHTML += `<p><strong>Guests:</strong> ${seats}</p>`;
                summaryHTML += `<p><strong>Date:</strong> ${date}</p>`;
                summaryHTML += `<p><strong>Time:</strong> ${formatTime24to12(time)}</p>`;
                summaryHTML += `<p><strong>Contact Phone:</strong> ${phone}</p>`;
                summaryHTML += `<p><strong>Payment Method:</strong> ${paymentMethod}</p>`;
                summaryHTML += `<p><strong>Payment Type:</strong> ${paymentType}</p>`;
                
                if (Object.keys(orderSummary.menuItems).length > 0) {
                    summaryHTML += `<h4 style="color: #FFD700; margin-top: 15px;">Menu Items:</h4>`;
                    Object.values(orderSummary.menuItems).forEach(item => {
                        if (item.quantity > 0) {
                            summaryHTML += `<p>• ${item.quantity}x ${item.name} - ₱${item.total.toFixed(2)}</p>`;
                        }
                    });
                }
                
                if (Object.keys(orderSummary.drinkItems).length > 0) {
                    summaryHTML += `<h4 style="color: #FFD700; margin-top: 15px;">Alcoholic Drinks:</h4>`;
                    Object.values(orderSummary.drinkItems).forEach(item => {
                        if (item.quantity > 0) {
                            summaryHTML += `<p>• ${item.quantity}x ${item.name} - ₱${item.total.toFixed(2)}</p>`;
                        }
                    });
                }
                
                if (Object.keys(orderSummary.promoDeals).length > 0) {
                    summaryHTML += `<h4 style="color: #FFD700; margin-top: 15px;">Promo Deals:</h4>`;
                    Object.values(orderSummary.promoDeals).forEach(deal => {
                        if (deal.quantity > 0) {
                            summaryHTML += `<p>• ${deal.quantity}x ${deal.name} - ₱${deal.total.toFixed(2)}</p>`;
                        }
                    });
                }
                
                if (Object.keys(orderSummary.groupDeals).length > 0) {
                    summaryHTML += `<h4 style="color: #FFD700; margin-top: 15px;">Group Deals:</h4>`;
                    Object.values(orderSummary.groupDeals).forEach(deal => {
                        if (deal.quantity > 0) {
                            summaryHTML += `<p>• ${deal.quantity}x ${deal.name} - ₱${deal.total.toFixed(2)}</p>`;
                        }
                    });
                }
                
                if (foodCorkageQuantity > 0 || drinkCorkageQuantity > 0) {
                    summaryHTML += `<h4 style="color: #FFD700; margin-top: 15px;">Corkage Fees:</h4>`;
                    if (foodCorkageQuantity > 0) {
                        summaryHTML += `<p>• ${foodCorkageQuantity}x Outside Food Items - ₱${foodCorkageFee.toFixed(2)} (₱100 per item)</p>`;
                    }
                    if (drinkCorkageQuantity > 0) {
                        summaryHTML += `<p>• ${drinkCorkageQuantity}x Outside Drinks (100ml) - ₱${drinkCorkageFee.toFixed(2)} (₱100 per 100ml)</p>`;
                    }
                }
                
                summaryHTML += `<h4 style="color: #FFD700; margin-top: 15px; border-top: 1px solid #444; padding-top: 10px;">Payment Summary:</h4>`;
                
                summaryHTML += `<div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px; margin-bottom: 15px;">`;
                
                if (menuTotal > 0) {
                    summaryHTML += `<p style="margin: 5px 0;"><strong>Menu Total:</strong> ₱${menuTotal.toFixed(2)}</p>`;
                }
                
                if (drinkTotal > 0) {
                    summaryHTML += `<p style="margin: 5px 0;"><strong>Alcoholic Drinks Total:</strong> ₱${drinkTotal.toFixed(2)}</p>`;
                }
                
                if (promoTotal > 0) {
                    summaryHTML += `<p style="margin: 5px 0;"><strong>Promo Deals Total:</strong> ₱${promoTotal.toFixed(2)}</p>`;
                }
                
                if (groupTotal > 0) {
                    summaryHTML += `<p style="margin: 5px 0;"><strong>Group Deals Total:</strong> ₱${groupTotal.toFixed(2)}</p>`;
                }
                
                summaryHTML += `<p style="margin: 10px 0; border-top: 1px dashed #555; padding-top: 8px;"><strong>Subtotal:</strong> ₱${subtotal.toFixed(2)}</p>`;
                
                if (foodCorkageFee > 0) {
                    summaryHTML += `<p style="margin: 5px 0;"><strong>Outside Food Fee:</strong> ₱${foodCorkageFee.toFixed(2)}</p>`;
                }
                if (drinkCorkageFee > 0) {
                    summaryHTML += `<p style="margin: 5px 0;"><strong>Outside Drink Fee:</strong> ₱${drinkCorkageFee.toFixed(2)}</p>`;
                }
                
                summaryHTML += `<div style="margin: 8px 0; padding-left: 10px; border-left: 2px solid #FFD700;">`;
                summaryHTML += `<p style="margin: 3px 0;"><strong>Service Charge (10%):</strong> ₱${serviceCharge.toFixed(2)}</p>`;
                summaryHTML += `<p style="margin: 3px 0; font-size: 0.9em; color: #aaa;">Calculation: ₱${subtotal.toFixed(2)} × 10% = ₱${serviceCharge.toFixed(2)}</p>`;
                summaryHTML += `</div>`;
                
                summaryHTML += `<p style="margin: 10px 0; border-top: 1px dashed #555; padding-top: 8px; font-size: 1.1em; color: #FFD700;"><strong>Grand Total:</strong> ₱${grandTotal.toFixed(2)}</p>`;
                summaryHTML += `</div>`;
                
                summaryHTML += `<div style="background: rgba(212, 175, 55, 0.1); padding: 12px; border-radius: 8px; border: 1px solid #FFD700; margin-top: 10px;">`;
                summaryHTML += `<p style="font-size: 1.2em; margin: 5px 0; color: #FFD700;"><strong>Amount to Pay Now:</strong> ₱${amountToPay.toFixed(2)}</p>`;
                
                if (currentPaymentType === 'half') {
                    summaryHTML += `<p style="font-size: 1.1em; margin: 5px 0; color: #FFA500;"><strong>Remaining Balance:</strong> ₱${remainingAmount.toFixed(2)} (Pay upon arrival)</p>`;
                }
                summaryHTML += `</div>`;
                
                summaryHTML += `</div>`;

                Swal.fire({
                    title: 'Confirm Reservation',
                    html: summaryHTML,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm Reservation',
                    cancelButtonText: 'Review Details',
                    confirmButtonColor: '#d4af37',
                    cancelButtonColor: '#6b7280',
                    background: '#1a1a1a',
                    width: '650px',
                    customClass: {
                        popup: 'swal2-popup-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitReservation();
                    }
                });
            });
        }
    }

    function submitReservation() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

        const formData = new FormData();
        
        const customerPhone = document.getElementById('customer_phone_input').value;
        
        formData.append("requestType", "RequestReservation");
        formData.append("table_code", document.getElementById('table_code').value);
        formData.append("customer_name", "<?php echo htmlspecialchars($user_name ?: 'Guest User'); ?>");
        formData.append("customer_email", "<?php echo htmlspecialchars($user_email ?: 'guest@example.com'); ?>");
        formData.append("customer_phone", customerPhone);
        formData.append("seats", document.getElementById('seats').value);
        formData.append("date_schedule", document.getElementById('date_schedule').value);
        formData.append("time_schedule", document.getElementById('time_schedule').value);
        formData.append("menu_total", document.getElementById('menu_total').value);
        formData.append("drink_total", document.getElementById('drink_total').value);
        formData.append("promo_total", document.getElementById('promo_total').value);
        formData.append("group_total", document.getElementById('group_total').value);
        formData.append("grand_total", document.getElementById('grand_total').value);
        formData.append("selected_menus", document.getElementById('selected_menus').value);
        formData.append("selected_drinks", document.getElementById('selected_drinks').value);
        formData.append("selected_promos", document.getElementById('selected_promos').value);
        formData.append("selected_groups", document.getElementById('selected_groups').value);
        formData.append("payment_type", document.getElementById('payment_type').value);
        formData.append("amount_to_pay", document.getElementById('amount_to_pay').value);
        formData.append("payment_method", document.getElementById('payment_method').value);
        formData.append("foodCorkageQuantity", document.getElementById('foodCorkageQuantity').value || 0);
        formData.append("drinkCorkageQuantity", document.getElementById('drinkCorkageQuantity').value || 0);
        formData.append("food_corkage_fee", document.getElementById('food_corkage_fee').value || 0);
        formData.append("drink_corkage_fee", document.getElementById('drink_corkage_fee').value || 0);
        formData.append("corkage_fee", document.getElementById('corkage_fee').value || 0);
        formData.append("service_charge", document.getElementById('service_charge_amount').value || 0);
        formData.append("service_charge_amount", document.getElementById('service_charge_amount').value || 0);

        const paymentProof = document.getElementById('payment_proof').files[0];
        if (paymentProof) {
            formData.append("payment_proof", paymentProof);
        }

        $.ajax({
            url: CONTROLLER_URL,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const paymentType = currentPaymentType === 'full' ? 'Full Payment' : '50% Downpayment';
                    const amountPaid = document.getElementById('amount_to_pay').value;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Reservation Submitted!',
                        html: `
                            <div style="text-align: left; color: white;">
                                <p>Your reservation has been submitted successfully and is pending approval.</p>
                                <p><strong>Reference ID:</strong> ${response.reference_id || 'N/A'}</p>
                                <p><strong>Payment Type:</strong> ${paymentType}</p>
                                <p><strong>Amount Paid:</strong> ₱${amountPaid}</p>
                                <p>You will receive a confirmation email shortly.</p>
                            </div>
                        `,
                        confirmButtonText: 'Great!',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        width: '500px'
                    }).then(() => {
                        document.getElementById('scheduleModal').classList.add('hidden');
                        document.getElementById('frmRequestReservation').reset();
                        resetOrderSummary();
                        
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Submit Reservation';
                        updateFormState();
                        
                        updateTableStatuses();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: response.message || 'Error submitting reservation. Please try again.',
                        confirmButtonColor: '#d4af37',
                        background: '#1a1a1a',
                        color: '#e5e5e5'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error submitting reservation. Please try again.';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: errorMessage,
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#e5e5e5'
                });
            },
            complete: function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Reservation';
            }
        });
    }

    function initializeSeatsHandler() {
        const seatsInput = document.getElementById('seats');
        const seatsWarning = document.createElement('p');
        seatsWarning.className = 'text-red-400 text-xs mt-1';
        seatsWarning.id = 'seats-warning';
        seatsWarning.textContent = '';
        seatsInput.parentNode.appendChild(seatsWarning);

        seatsInput.addEventListener('input', function() {
            let val = this.value.trim();
            seatsWarning.style.display = 'block';

            if (val === "") {
                seatsWarning.textContent = "Please enter number of seats.";
                return;
            }

            if (isNaN(val)) {
                seatsWarning.textContent = "Please enter a valid number.";
                return;
            }

            val = parseInt(val, 10);

            if (val > 6) {
                seatsWarning.textContent = "Maximum number of seats is 6.";
                val = 6;
            } else if (val < 1) {
                seatsWarning.textContent = "Minimum number of seats is 1.";
                val = 1;
            }

            this.value = val;
            seatsWarning.style.display = 'none';
        });

        if (seatsInput.value.trim() === "") {
            seatsWarning.style.display = 'block';
        } else {
            seatsWarning.style.display = 'none';
        }
    }

    function startAutoUpdate() {
        if (autoUpdateInterval) {
            clearInterval(autoUpdateInterval);
        }
        
        updateTableStatuses();
        
        autoUpdateInterval = setInterval(updateTableStatuses, 5000);
    }

    function stopAutoUpdate() {
        if (autoUpdateInterval) {
            clearInterval(autoUpdateInterval);
            autoUpdateInterval = null;
        }
    }

            function initializeTableFilter() {
                const tableFilter = document.getElementById('tableStatusFilter');
                
                if (tableFilter) {
                    tableFilter.addEventListener('change', function() {
                        const filterValue = this.value;
                        const allTables = document.querySelectorAll('.table-item[data-table]');
                        
                        allTables.forEach(table => {
                            const tableStatus = table.getAttribute('data-status');
                            const tableName = table.getAttribute('data-table');
                            const isNonReservable = isNonReservableFacility(tableName);
                            
                            if (isNonReservable) {
                                table.style.display = (filterValue === 'all') ? 'flex' : 'none';
                                return; 
                            }
                            
                            if (filterValue === 'all') {
                                table.style.display = 'flex';
                            } else if (filterValue === 'available') {
                                table.style.display = tableStatus === 'available' ? 'flex' : 'none';
                            } else {
                                table.style.display = tableStatus === filterValue ? 'flex' : 'none';
                            }
                        });
                    });
                }
            }

    document.addEventListener('DOMContentLoaded', function() {
        // Debug Script - Add this to see session info in console
        console.log("Session Debug Info:");
        console.log("User ID: ", "<?php echo $_SESSION['user_id'] ?? 'Not set'; ?>");
        console.log("User Position: ", "<?php echo $_SESSION['user_position'] ?? 'Not set'; ?>");
        console.log("User Name: ", "<?php echo $_SESSION['user_name'] ?? 'Not set'; ?>");
        
        // Add click handler for reservation link in nav
        const reservationLink = document.querySelector('a[href="reservation.php"]');
        if (reservationLink) {
            reservationLink.addEventListener('click', function(e) {
                console.log("Reservation link clicked");
                console.log("Current session user_id: ", "<?php echo $_SESSION['user_id'] ?? 'Not set'; ?>");
                console.log("Current session position: ", "<?php echo $_SESSION['user_position'] ?? 'Not set'; ?>");
            });
        }
        
        // Auto-show emergency modal on page load
        const emergencyModal = document.getElementById('emergencyWarningModal');
        if (emergencyActive && emergencyModal) {
            // Show modal after a short delay
            setTimeout(() => {
                emergencyModal.style.display = 'block';
            }, 1000);
            
            // Close modal on click
            emergencyModal.addEventListener('click', function(e) {
                if (e.target === emergencyModal) {
                    emergencyModal.style.display = 'none';
                }
            });
            
            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && emergencyModal.style.display === 'block') {
                    emergencyModal.style.display = 'none';
                }
            });
        }
        
        initializeClocks();
        initializeChatBot();
        initializeMenuCategoryTabs();
        initializeTableStatuses();
        initializeTableFilter();
        initializeCorkageHandler();
        initializePaymentTypeSelection();
        initializePaymentHandler();
        initializeAvailabilityChecker();
        initializeReservationModal();
        initializeFormSubmission();
        initializeSeatsHandler();
        
        document.querySelectorAll('.table-item[data-table]').forEach(table => {
            const isClickable = table.getAttribute('data-clickable') === 'true';
            if (isClickable) {
                table.addEventListener('click', function() {
                    const tableCode = this.getAttribute('data-table');
                    const status = this.getAttribute('data-status');
                    handleTableClick(tableCode, status);
                });
            }
        });
        
        updateOperatingStatus();
        setInterval(updateOperatingStatus, 60000);
        
        startAutoUpdate();
        
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoUpdate();
            } else {
                startAutoUpdate();
            }
        });
    });
</script>
</body>
<!-- QR Code Full Picture Modal -->
<div class="qr-full-picture-modal" id="qrFullPictureModal">
    <div class="qr-close-btn" id="qrCloseBtn">&times;</div>
    <img id="qrFullPicture" src="" alt="QR Code" class="qr-full-picture">
</div>

<script>
// QR Code Click Functionality
function initializeQrClick() {
    // Click QR code to show full picture
    $("#payment_qr").on("click", function() {
        const qrSrc = $(this).attr("src");
        if (qrSrc) {
            $("#qrFullPicture").attr("src", qrSrc);
            $("#qrFullPictureModal").addClass("active");
            $("body").css("overflow", "hidden");
        }
    });
    
    // Close modal when clicking X or overlay
    $("#qrCloseBtn, #qrFullPictureModal").on("click", function(e) {
        if (e.target === this || $(e.target).hasClass("qr-close-btn")) {
            $("#qrFullPictureModal").removeClass("active");
            $("body").css("overflow", "auto");
        }
    });
    
    // Close on ESC key
    $(document).on("keydown", function(e) {
        if (e.key === "Escape" && $("#qrFullPictureModal").hasClass("active")) {
            $("#qrFullPictureModal").removeClass("active");
            $("body").css("overflow", "auto");
        }
    });
}

// Call this function in your existing document ready
$(document).ready(function() {
    initializeQrClick();
});
</script>

</html>

<?php include "../src/components/customer/footer.php"; ?>