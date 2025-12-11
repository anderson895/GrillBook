<?php
ob_start();
session_start();

$userLoggedIn = false;
$userIsCustomer = false;

if (isset($_SESSION['user_id'])) {
    $userLoggedIn = true;
    
    if (isset($_SESSION['user_position']) && $_SESSION['user_position'] === 'customer') {
        $userIsCustomer = true;
    }
}

if (!$userLoggedIn || !$userIsCustomer) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php');
    exit();
}

include "../src/components/customer/header.php";
include "../src/components/customer/nav.php";



/**
 * 
 *  $username = "u777088444_grillbook";
    $password = "Grillbook123@";
    $database = "u777088444_grillbook";
 */

$host = 'localhost';
$dbname = 'u777088444_grillbook';
$username = 'u777088444_grillbook';
$password = 'Grillbook123@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $reservation_id = (int)$_POST['reservation_id'];
        $user_email = $_SESSION['email'];
        
        $checkStmt = $pdo->prepare("SELECT id, table_code, customer_name FROM reservations WHERE id = ? AND customer_email = ?");
        $checkStmt->execute([$reservation_id, $user_email]);
        
        if ($checkStmt->rowCount() > 0) {
            $reservation = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $table_code = $reservation['table_code'];
            $customer_name = $reservation['customer_name'];
            
            $action = $_POST['action'];
            
            if ($action === 'reschedule' || $action === 'cancel') {
                $reason = htmlspecialchars(strip_tags(trim($_POST['reason'])));
                $new_status = ($action === 'reschedule') ? 'request_reschedule' : 'request_cancel';
                
                $updateStmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
                $updateStmt->execute([$new_status, $reservation_id]);
                
                $notificationTitle = ($action === 'reschedule') ? 'Reschedule Request' : 'Cancellation Request';
                $notificationMessage = "Customer {$customer_name} requested to {$action} reservation #{$reservation_id} for table {$table_code}. Reason: {$reason}";
                
                $notificationStmt = $pdo->prepare("
                    INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) 
                    VALUES (?, ?, 'reservation', ?, ?, NOW())
                ");
                $notificationStmt->execute([$notificationTitle, $notificationMessage, $reservation_id, $table_code]);
                
                $success_action = $action;
                
            } elseif ($action === 'completed') {
                $updateStmt = $pdo->prepare("UPDATE reservations SET status = 'completed' WHERE id = ?");
                $updateStmt->execute([$reservation_id]);
                
                $tableStmt = $pdo->prepare("SELECT id FROM tables WHERE table_code = ?");
                $tableStmt->execute([$table_code]);
                $table = $tableStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($table) {
                    $updateTableStmt = $pdo->prepare("UPDATE tables SET status = 'available' WHERE id = ?");
                    $updateTableStmt->execute([$table['id']]);
                    
                    $notificationStmt = $pdo->prepare("
                        INSERT INTO notifications (title, message, type, reservation_id, table_code, created_at) 
                        VALUES ('Reservation Completed', ?, 'reservation', ?, ?, NOW())
                    ");
                    $notificationStmt->execute(["Reservation #{$reservation_id} has been marked as completed by customer. Table {$table_code} is now available.", $reservation_id, $table_code]);
                }
                
                $success_action = 'completed';
            }
            
            $redirectPage = isset($_GET['page']) ? $_GET['page'] : 1;
            header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $redirectPage . "&success=" . $success_action);
            exit();
        }
    }
}

$user_email = $_SESSION['email'];
$reservations = [];
$totalReservations = 0;
$totalPages = 0;

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM reservations WHERE customer_email = ?");
    $countStmt->execute([$user_email]);
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalReservations = $countResult['total'];
    $totalPages = ceil($totalReservations / $limit);

    $stmt = $pdo->prepare("
        SELECT * 
        FROM reservations 
        WHERE customer_email = ?
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $user_email, PDO::PARAM_STR);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error fetching reservations: " . $e->getMessage());
}

$menuNames = [];
try {
    $menuStmt = $pdo->prepare("SELECT menu_id, menu_name FROM menu");
    $menuStmt->execute();
    $menus = $menuStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($menus as $menu) {
        $menuNames[$menu['menu_id']] = $menu['menu_name'];
    }
} catch (PDOException $e) {
    error_log("Error fetching menu names: " . $e->getMessage());
}

$statusCounts = [
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'request_cancel' => 0,
    'request_reschedule' => 0
];

$totalSpent = 0;
foreach ($reservations as $res) {
    $status = $res['status'];
    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    }
    
    if (in_array($res['status'], ['confirmed', 'completed'])) {
        $totalSpent += $res['grand_total'];
    }
}

$successMessage = '';
if (isset($_GET['success'])) {
    $action = $_GET['success'];
    $messages = [
        'reschedule' => 'Reschedule request submitted successfully! Our team will review your request.',
        'cancel' => 'Cancellation request submitted successfully! Our team will review your request.',
        'completed' => 'Reservation marked as completed! Thank you for dining with us. The table is now available for other customers.'
    ];
    $successMessage = $messages[$action] ?? 'Action completed successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - GrillBook</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0a0a0a;
            --primary-gold: #d4af37;
            --gold-light: #f5e8c8;
            --gold-dark: #b8860b;
            --dark-bg: #1a1a1a;
            --card-bg: #242424;
            --light-card: #2d2d2d;
            --text-light: #f5f5f5;
            --text-muted: #a3a3a3;
            --border-dark: #333333;
            --border-gold: rgba(212, 175, 55, 0.3);
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --purple-color: #8b5cf6;
            --teal-color: #14b8a6;
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1a1a1a 100%);
            font-family: 'Poppins', 'Arial', 'Helvetica', sans-serif;
            color: var(--text-light);
            line-height: 1.6;
            font-size: 16px;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-weight: 400;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .page-header {
            padding: 60px 0 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(10, 10, 10, 0.95) 0%, rgba(26, 26, 26, 0.95) 100%);
            border-bottom: 1px solid var(--border-gold);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(184, 134, 11, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--primary-gold);
            letter-spacing: 0.5px;
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-gold), transparent);
        }

        .page-subtitle {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            max-width: 800px;
            line-height: 1.7;
            font-weight: 400;
        }

        .user-email-display {
            display: inline-flex;
            align-items: center;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            padding: 12px 20px;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 0.95rem;
            color: var(--gold-light);
            margin-top: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            font-weight: 500;
        }

        .user-email-display .material-icons {
            font-size: 18px;
            margin-right: 10px;
            color: var(--primary-gold);
        }

        .content-section {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-dark);
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .content-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary-gold);
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border-gold);
            letter-spacing: 0.5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: linear-gradient(145deg, var(--card-bg), var(--dark-bg));
            border-radius: 12px;
            padding: 28px;
            border: 1px solid var(--border-dark);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-gold);
            box-shadow: 
                0 12px 32px rgba(212, 175, 55, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-gold), transparent);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 28px;
            background: rgba(212, 175, 55, 0.1);
            color: var(--primary-gold);
            border: 1px solid var(--border-gold);
        }

        .stat-number {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-light);
            font-feature-settings: "tnum";
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-section {
            display: flex;
            gap: 16px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 14px 20px;
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            color: var(--text-light);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-weight: 400;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-muted);
            font-weight: 300;
        }

        .filter-select {
            padding: 14px 20px;
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            color: var(--text-light);
            min-width: 200px;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            font-weight: 400;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .reservations-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 32px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-dark);
        }

        .reservations-table th {
            background: linear-gradient(135deg, var(--dark-bg), #242424);
            color: var(--primary-gold);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 20px 16px;
            text-align: left;
            border-bottom: 2px solid var(--border-gold);
        }

        .reservations-table td {
            padding: 20px 16px;
            border-bottom: 1px solid var(--border-dark);
            vertical-align: middle;
            color: var(--text-light);
            background: var(--light-card);
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
        }

        .reservations-table tr:last-child td {
            border-bottom: none;
        }

        .reservations-table tr:hover td {
            background: rgba(212, 175, 55, 0.05);
        }

        .reservation-date {
            font-weight: 600;
            color: var(--text-light);
            font-size: 1rem;
        }

        .reservation-day {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .table-code {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--primary-gold);
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid transparent;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .status-confirmed {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .status-completed {
            background: rgba(20, 184, 166, 0.15);
            color: var(--teal-color);
            border-color: rgba(20, 184, 166, 0.3);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger-color);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .status-request_cancel {
            background: rgba(249, 115, 22, 0.15);
            color: #f97316;
            border-color: rgba(249, 115, 22, 0.3);
        }

        .status-request_reschedule {
            background: rgba(168, 85, 247, 0.15);
            color: var(--purple-color);
            border-color: rgba(168, 85, 247, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            white-space: nowrap;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.5px;
            white-space: nowrap;
            min-width: fit-content;
        }

        .btn-view {
            background: linear-gradient(135deg, var(--primary-gold), var(--gold-dark));
            color: var(--primary-dark);
            border: 1px solid var(--primary-gold);
        }

        .btn-view:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--primary-gold));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.3);
        }

        .btn-receipt {
            background: linear-gradient(135deg, var(--info-color), #2563eb);
            color: white;
            border: 1px solid var(--info-color);
        }

        .btn-receipt:hover {
            background: linear-gradient(135deg, #2563eb, var(--info-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-reschedule {
            background: linear-gradient(135deg, var(--purple-color), #7c3aed);
            color: white;
            border: 1px solid var(--purple-color);
        }

        .btn-reschedule:hover {
            background: linear-gradient(135deg, #7c3aed, var(--purple-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
        }

        .btn-cancel {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            border: 1px solid var(--danger-color);
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #dc2626, var(--danger-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        .btn-complete {
            background: linear-gradient(135deg, var(--teal-color), #0d9488);
            color: white;
            border: 1px solid var(--teal-color);
        }

        .btn-complete:hover {
            background: linear-gradient(135deg, #0d9488, var(--teal-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.3);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid var(--border-dark);
        }

        .page-link {
            padding: 12px 18px;
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            color: var(--text-light);
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 44px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .page-link:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: var(--primary-gold);
            color: var(--primary-gold);
        }

        .page-link.active {
            background: linear-gradient(135deg, var(--primary-gold), var(--gold-dark));
            border-color: var(--primary-gold);
            color: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .results-info {
            text-align: center;
            color: var(--text-muted);
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
            margin-top: 20px;
            padding: 12px;
            background: rgba(26, 26, 26, 0.5);
            border-radius: 8px;
            border: 1px solid var(--border-dark);
            font-weight: 400;
        }

        .empty-state {
            text-align: center;
            padding: 80px 32px;
            border: 2px dashed var(--border-gold);
            border-radius: 12px;
            background: rgba(26, 26, 26, 0.5);
            margin: 32px 0;
        }

        .empty-icon {
            font-size: 4.5rem;
            color: var(--border-gold);
            margin-bottom: 28px;
            opacity: 0.6;
        }

        .empty-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            color: var(--primary-gold);
            margin-bottom: 16px;
            font-weight: 600;
        }

        .empty-description {
            color: var(--text-muted);
            font-family: 'Poppins', sans-serif;
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            font-weight: 400;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-gold), var(--gold-dark));
            color: var(--primary-dark);
            padding: 14px 28px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 1px solid var(--primary-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--primary-gold));
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(212, 175, 55, 0.4);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: linear-gradient(145deg, var(--card-bg), var(--dark-bg));
            border-radius: 16px;
            max-width: 900px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-gold);
            position: relative;
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
        }

        .modal-header {
            padding: 28px 36px;
            border-bottom: 1px solid var(--border-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.9), rgba(36, 36, 36, 0.9));
        }

        .modal-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-gold);
            margin: 0;
            letter-spacing: 0.5px;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            cursor: pointer;
            padding: 4px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--border-gold);
            font-weight: 300;
        }

        .modal-close:hover {
            background: rgba(212, 175, 55, 0.2);
            color: var(--primary-gold);
            transform: rotate(90deg);
        }

        .modal-content {
            padding: 36px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .form-textarea {
            width: 100%;
            padding: 16px;
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 120px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-submit {
            background: linear-gradient(135deg, var(--primary-gold), var(--gold-dark));
            color: var(--primary-dark);
            padding: 14px 28px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 16px;
        }

        .form-submit:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--primary-gold));
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.4);
        }

        .form-submit.btn-reschedule-modal {
            background: linear-gradient(135deg, var(--purple-color), #7c3aed);
            color: white;
        }

        .form-submit.btn-reschedule-modal:hover {
            background: linear-gradient(135deg, #7c3aed, var(--purple-color));
        }

        .form-submit.btn-cancel-modal {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        .form-submit.btn-cancel-modal:hover {
            background: linear-gradient(135deg, #dc2626, var(--danger-color));
        }

        .form-submit.btn-complete-modal {
            background: linear-gradient(135deg, var(--teal-color), #0d9488);
            color: white;
        }

        .form-submit.btn-complete-modal:hover {
            background: linear-gradient(135deg, #0d9488, var(--teal-color));
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(150%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 400px;
        }

        .notification.active {
            transform: translateX(0);
        }

        .notification.error {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .notification.warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .notification.info {
            background: linear-gradient(135deg, var(--info-color), #2563eb);
        }

        .reservation-info {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .reservation-info p {
            margin: 8px 0;
            color: var(--gold-light);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }

        .reservation-info strong {
            color: var(--primary-gold);
        }

        .info-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-style: italic;
            margin-top: 8px;
            font-weight: 400;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }

        .detail-section {
            background: rgba(26, 26, 26, 0.5);
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            padding: 24px;
        }

        .detail-section-title {
            color: var(--primary-gold);
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-gold);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--text-muted);
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 400;
            text-align: right;
            max-width: 60%;
        }

        .items-container {
            margin-bottom: 24px;
        }

        .items-list {
            margin-bottom: 20px;
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            overflow: hidden;
            background: rgba(26, 26, 26, 0.3);
        }

        .items-list h4 {
            background: linear-gradient(135deg, rgba(36, 36, 36, 0.9), rgba(45, 45, 45, 0.9));
            padding: 16px 24px;
            margin: 0;
            border-bottom: 1px solid var(--border-gold);
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-dark);
            background: rgba(26, 26, 26, 0.5);
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-row:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .item-name {
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .item-quantity {
            color: var(--text-muted);
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
        }

        .item-price {
            color: var(--primary-gold);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
        }

        .summary-section {
            background: linear-gradient(145deg, rgba(26, 26, 26, 0.8), rgba(36, 36, 36, 0.8));
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            padding: 24px;
            margin-top: 24px;
        }

        .summary-title {
            color: var(--primary-gold);
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .summary-value {
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .summary-row.grand-total {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid var(--border-gold);
        }

        .summary-row.grand-total .summary-label,
        .summary-row.grand-total .summary-value {
            color: var(--primary-gold);
            font-family: 'Poppins', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .action-buttons-modal {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border-dark);
        }

        @media (max-width: 1024px) {
            .container {
                padding: 0 20px;
            }
            
            .page-title {
                font-size: 2.5rem;
            }
            
            .content-section {
                padding: 28px;
            }
            
            .filter-section {
                flex-direction: column;
            }
            
            .search-input,
            .filter-select {
                min-width: 100%;
            }

            .action-buttons {
                flex-wrap: wrap;
                justify-content: flex-start;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 0.8125rem;
            }
            
            .modal {
                max-width: 95%;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 40px 0 32px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .reservations-table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: flex-start;
            }
            
            .modal-header {
                padding: 20px 24px;
            }
            
            .modal-content {
                padding: 24px;
            }

            .btn {
                padding: 6px 10px;
                font-size: 0.75rem;
                gap: 4px;
            }
            
            .btn .material-icons {
                font-size: 16px;
                margin-right: 4px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gold);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-dark);
        }

        .font-light { font-weight: 300; }
        .font-normal { font-weight: 400; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
    </style>
</head>
<body>
<?php if (!empty($successMessage)): ?>
    <div class="notification active" id="successNotification">
        <span class="material-icons">check_circle</span>
        <span><?php echo htmlspecialchars($successMessage); ?></span>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('successNotification').classList.remove('active');
        }, 5000);
    </script>
<?php endif; ?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title font-semibold">My Reservations</h1>
        <p class="page-subtitle font-normal">View and manage all your restaurant reservations. Track your booking history, check status updates, and access important details for each reservation.</p>
        <div class="user-email-display font-medium">
            <span class="material-icons">mail_outline</span>
            <?php echo htmlspecialchars($user_email); ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">event_seat</span>
            </div>
            <div class="stat-number"><?php echo htmlspecialchars($totalReservations); ?></div>
            <div class="stat-label font-medium">Total Reservations</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">pending_actions</span>
            </div>
            <div class="stat-number"><?php echo $statusCounts['pending']; ?></div>
            <div class="stat-label font-medium">Pending Review</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">verified</span>
            </div>
            <div class="stat-number"><?php echo $statusCounts['confirmed']; ?></div>
            <div class="stat-label font-medium">Confirmed</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">done_all</span>
            </div>
            <div class="stat-number"><?php echo $statusCounts['completed']; ?></div>
            <div class="stat-label font-medium">Completed</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="material-icons">account_balance_wallet</span>
            </div>
            <div class="stat-number">₱<?php echo number_format($totalSpent, 2); ?></div>
            <div class="stat-label font-medium">Total Amount Spent</div>
        </div>
    </div>

    <div class="content-section">
        <h2 class="section-title font-semibold">Reservation History</h2>
        
        <div class="filter-section">
            <input type="text" 
                   id="searchInput" 
                   class="search-input font-normal" 
                   placeholder="Search by table number, reservation date, or amount..."
                   aria-label="Search reservations">
            
            <select id="statusFilter" class="filter-select font-normal">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="request_cancel">Cancel Requested</option>
                <option value="request_reschedule">Reschedule Requested</option>
            </select>
        </div>

        <?php if (empty($reservations)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <span class="material-icons">event_busy</span>
                </div>
                <h3 class="empty-title font-semibold">No Reservations Found</h3>
                <p class="empty-description font-normal">You haven't made any reservations with us yet. Experience our premium dining by making your first reservation today.</p>
                <a href="home.php" class="btn-primary font-bold">
                    <span class="material-icons">add_circle</span>
                    Book Your First Table
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="reservations-table">
                    <thead>
                        <tr>
                            <th class="font-semibold">#</th>
                            <th class="font-semibold">Reservation Date</th>
                            <th class="font-semibold">Table</th>
                            <th class="font-semibold">Time Slot</th>
                            <th class="font-semibold">Guests</th>
                            <th class="font-semibold">Total Amount</th>
                            <th class="font-semibold">Status</th>
                            <th class="font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reservationsTableBody">
                        <?php foreach ($reservations as $index => $reservation): ?>
                            <?php
                            $statusClass = 'status-' . $reservation['status'];
                            $statusText = ucwords(str_replace('_', ' ', $reservation['status']));
                            
                            $rowNumber = $offset + $index + 1;
                            
                            $canRequestReschedule = in_array($reservation['status'], ['pending', 'confirmed']);
                            $canRequestCancel = in_array($reservation['status'], ['pending', 'confirmed']);
                            ?>
                            <tr class="reservation-row font-normal" 
                                data-id="<?php echo $reservation['id']; ?>"
                                data-table="<?php echo htmlspecialchars($reservation['table_code']); ?>"
                                data-date="<?php echo $reservation['date_schedule']; ?>"
                                data-status="<?php echo $reservation['status']; ?>"
                                data-total="<?php echo $reservation['grand_total']; ?>">
                                
                                <td style="font-weight: 600; color: var(--text-muted);"><?php echo $rowNumber; ?></td>
                                
                                <td>
                                    <div class="reservation-date font-semibold">
                                        <?php echo date('F j, Y', strtotime($reservation['date_schedule'])); ?>
                                    </div>
                                    <div class="reservation-day font-normal">
                                        <?php echo date('l', strtotime($reservation['date_schedule'])); ?>
                                    </div>
                                </td>
                                
                                <td class="table-code font-semibold"><?php echo htmlspecialchars($reservation['table_code']); ?></td>
                                
                                <td>
                                    <div class="reservation-date font-semibold">
                                        <?php echo date('g:i A', strtotime($reservation['time_schedule'])); ?>
                                    </div>
                                    <div class="reservation-day font-normal">
                                        <?php echo date('g:i A', strtotime($reservation['time_schedule'] . ' +2 hours')); ?>
                                    </div>
                                </td>
                                
                                <td style="text-align: center;">
                                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-gold);">
                                        <?php echo $reservation['seats']; ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">
                                        persons
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--success-color);">
                                        ₱<?php echo number_format($reservation['grand_total'], 2); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">
                                        incl. service charge
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="status-badge <?php echo $statusClass; ?> font-semibold">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" onclick="showReservationDetails(<?php echo $reservation['id']; ?>)" 
                                                class="btn btn-view font-semibold">
                                            <span class="material-icons" style="font-size: 18px;">visibility</span>
                                            Details
                                        </button>
                                        
                                        <?php if (!empty($reservation['payment_proof'])): ?>
                                            <button type="button" onclick="viewPaymentProof('<?php echo htmlspecialchars($reservation['payment_proof']); ?>')" 
                                                    class="btn btn-receipt font-semibold">
                                                <span class="material-icons" style="font-size: 18px;">receipt_long</span>
                                                Receipt
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($canRequestReschedule): ?>
                                            <button type="button" onclick="showRescheduleForm(<?php echo $reservation['id']; ?>, '<?php echo $reservation['date_schedule']; ?>', '<?php echo $reservation['time_schedule']; ?>', '<?php echo $reservation['table_code']; ?>')" 
                                                    class="btn btn-reschedule font-semibold">
                                                <span class="material-icons" style="font-size: 18px;">schedule</span>
                                                Reschedule
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($canRequestCancel): ?>
                                            <button type="button" onclick="showCancelForm(<?php echo $reservation['id']; ?>, '<?php echo $reservation['date_schedule']; ?>', '<?php echo $reservation['time_schedule']; ?>', '<?php echo $reservation['table_code']; ?>')" 
                                                    class="btn btn-cancel font-semibold">
                                                <span class="material-icons" style="font-size: 18px;">cancel</span>
                                                Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link font-semibold">
                            <span class="material-icons" style="font-size: 20px;">chevron_left</span>
                            Previous
                        </a>
                    <?php else: ?>
                        <span class="page-link disabled font-semibold">
                            <span class="material-icons" style="font-size: 20px;">chevron_left</span>
                            Previous
                        </span>
                    <?php endif; ?>

                    <div style="display: flex; gap: 8px;">
                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        $startPage = max(1, $endPage - 4);
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?> font-semibold">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link font-semibold">
                            Next
                            <span class="material-icons" style="font-size: 20px;">chevron_right</span>
                        </a>
                    <?php else: ?>
                        <span class="page-link disabled font-semibold">
                            Next
                            <span class="material-icons" style="font-size: 20px;">chevron_right</span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="results-info font-normal">
                Displaying <?php echo min($limit, count($reservations)); ?> of <?php echo $totalReservations; ?> reservations • Page <?php echo $page; ?> of <?php echo $totalPages; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal-overlay" id="detailsModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title font-semibold">Reservation Details</h2>
            <button type="button" onclick="closeModal()" class="modal-close" aria-label="Close modal">
                &times;
            </button>
        </div>
        <div class="modal-content" id="modalContent">
        </div>
    </div>
</div>

<div class="modal-overlay" id="paymentReceiptModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title font-semibold">Payment Receipt</h2>
            <button type="button" onclick="closePaymentModal()" class="modal-close" aria-label="Close modal">
                &times;
            </button>
        </div>
        <div class="modal-content">
            <div style="text-align: center;">
                <img id="receiptImage" src="" alt="Payment Receipt" 
                     style="max-width: 100%; height: auto; border-radius: 12px; border: 1px solid var(--border-gold); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);">
                <div style="margin-top: 32px; color: var(--text-light);">
                    <p style="font-size: 1.125rem; font-weight: 500; font-family: 'Poppins', sans-serif;">Payment proof for your reservation</p>
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 8px; font-family: 'Poppins', sans-serif; font-weight: 400;">This receipt has been uploaded as proof of payment</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="rescheduleModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title font-semibold">Request Reschedule</h2>
            <button type="button" onclick="closeRescheduleModal()" class="modal-close" aria-label="Close modal">
                &times;
            </button>
        </div>
        <div class="modal-content">
            <form id="rescheduleForm" method="POST" action="">
                <input type="hidden" name="action" value="reschedule">
                <input type="hidden" name="reservation_id" id="rescheduleReservationId">
                
                <div class="reservation-info">
                    <p><strong>Current Reservation:</strong> <span id="rescheduleDate"></span> at <span id="rescheduleTime"></span></p>
                    <p><strong>Table:</strong> <span id="rescheduleTable"></span></p>
                </div>
                
                <div class="form-group">
                    <label for="rescheduleReason" class="form-label">Why do you want to reschedule? *</label>
                    <textarea id="rescheduleReason" name="reason" class="form-textarea" 
                              placeholder="Please provide the reason for rescheduling your reservation..." 
                              required minlength="20" maxlength="500"></textarea>
                </div>
                
                <button type="submit" class="form-submit btn-reschedule-modal">
                    <span class="material-icons" style="font-size: 20px; margin-right: 8px;">schedule</span>
                    Submit Reschedule Request
                </button>
            </form>
        </div>
    </div>
</div>

<div class="modal-overlay" id="cancelModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title font-semibold">Request Cancellation</h2>
            <button type="button" onclick="closeCancelModal()" class="modal-close" aria-label="Close modal">
                &times;
            </button>
        </div>
        <div class="modal-content">
            <form id="cancelForm" method="POST" action="">
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="reservation_id" id="cancelReservationId">
                
                <div class="reservation-info">
                    <p><strong>Reservation to Cancel:</strong> <span id="cancelDate"></span> at <span id="cancelTime"></span></p>
                    <p><strong>Table:</strong> <span id="cancelTable"></span></p>
                </div>
                
                <div class="form-group">
                    <label for="cancelReason" class="form-label">Why do you want to cancel? *</label>
                    <textarea id="cancelReason" name="reason" class="form-textarea" 
                              placeholder="Please provide the reason for cancelling your reservation..." 
                              required minlength="20" maxlength="500"></textarea>
                </div>
                
                <button type="submit" class="form-submit btn-cancel-modal">
                    <span class="material-icons" style="font-size: 20px; margin-right: 8px;">cancel</span>
                    Submit Cancellation Request
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const menuNames = <?php echo json_encode($menuNames); ?>;
let allReservations = <?php echo json_encode($reservations); ?>;
let currentReservationId = null;

function showReservationDetails(reservationId) {
    currentReservationId = reservationId;
    showModal('detailsModal');
    const content = document.getElementById('modalContent');
    
    const reservation = allReservations.find(r => r.id == reservationId);
    if (!reservation) {
        content.innerHTML = '<div style="text-align: center; padding: 40px; color: var(--danger-color); font-family: \'Poppins\', sans-serif;">Reservation not found</div>';
        return;
    }

    let menuItems = {}, promoDeals = {}, groupDeals = {};
    try {
        menuItems = reservation.selected_menus ? JSON.parse(reservation.selected_menus) : {};
        promoDeals = reservation.selected_promos ? JSON.parse(reservation.selected_promos) : {};
        groupDeals = reservation.selected_groups ? JSON.parse(reservation.selected_groups) : {};
    } catch (e) {
        console.error('Error parsing JSON data:', e);
    }

    let statusClass = 'status-pending';
    let statusText = 'Pending';
    
    switch(reservation.status) {
        case 'pending':
            statusClass = 'status-pending';
            statusText = 'Pending';
            break;
        case 'confirmed':
            statusClass = 'status-confirmed';
            statusText = 'Confirmed';
            break;
        case 'completed':
            statusClass = 'status-completed';
            statusText = 'Completed';
            break;
        case 'cancelled':
            statusClass = 'status-cancelled';
            statusText = 'Cancelled';
            break;
        case 'request_cancel':
            statusClass = 'status-request_cancel';
            statusText = 'Cancel Requested';
            break;
        case 'request_reschedule':
            statusClass = 'status-request_reschedule';
            statusText = 'Reschedule Requested';
            break;
    }

    const menu_total = parseFloat(reservation.menu_total || 0);
    const promo_total = parseFloat(reservation.promo_total || 0);
    const group_total = parseFloat(reservation.group_total || 0);
    const corkage_fee = parseFloat(reservation.corkage_fee || 0);
    const subtotal = menu_total + promo_total + group_total;
    const service_charge = subtotal * 0.10;
    const grand_total = subtotal + service_charge + corkage_fee;

    let itemsHTML = '';
    
    if (Object.keys(menuItems).length > 0) {
        itemsHTML += '<div class="items-list">';
        itemsHTML += '<h4 class="detail-section-title">Menu Items</h4>';
        Object.values(menuItems).forEach(item => {
            if (item.quantity > 0) {
                const itemName = menuNames[item.id] || item.name || 'Menu Item ' + item.id;
                itemsHTML += `
                    <div class="item-row">
                        <div>
                            <div class="item-name">${escapeHtml(itemName)}</div>
                            <div class="item-quantity">${item.quantity} × ₱${item.price.toFixed(2)}</div>
                        </div>
                        <div class="item-price">₱${(item.price * item.quantity).toFixed(2)}</div>
                    </div>`;
            }
        });
        itemsHTML += '</div>';
    }
    
    if (Object.keys(promoDeals).length > 0) {
        itemsHTML += '<div class="items-list">';
        itemsHTML += '<h4 class="detail-section-title">Promotional Deals</h4>';
        Object.values(promoDeals).forEach(deal => {
            if (deal.quantity > 0) {
                const dealName = deal.name || 'Promo Deal ' + deal.id;
                itemsHTML += `
                    <div class="item-row">
                        <div>
                            <div class="item-name">${escapeHtml(dealName)}</div>
                            <div class="item-quantity">${deal.quantity} × ₱${deal.price.toFixed(2)}</div>
                        </div>
                        <div class="item-price">₱${(deal.price * deal.quantity).toFixed(2)}</div>
                    </div>`;
            }
        });
        itemsHTML += '</div>';
    }
    
    if (Object.keys(groupDeals).length > 0) {
        itemsHTML += '<div class="items-list">';
        itemsHTML += '<h4 class="detail-section-title">Group Deals</h4>';
        Object.values(groupDeals).forEach(deal => {
            if (deal.quantity > 0) {
                const dealName = deal.name || 'Group Deal ' + deal.id;
                itemsHTML += `
                    <div class="item-row">
                        <div>
                            <div class="item-name">${escapeHtml(dealName)}</div>
                            <div class="item-quantity">${deal.quantity} × ₱${deal.price.toFixed(2)}</div>
                        </div>
                        <div class="item-price">₱${(deal.price * deal.quantity).toFixed(2)}</div>
                    </div>`;
            }
        });
        itemsHTML += '</div>';
    }

    if (itemsHTML === '') {
        itemsHTML = '<div style="text-align: center; padding: 40px; color: var(--text-muted); font-family: \'Poppins\', sans-serif;">No items were ordered with this reservation</div>';
    }

    const canMarkComplete = reservation.status === 'confirmed';
    const canRequestReschedule = reservation.status === 'confirmed' || reservation.status === 'pending';
    const canRequestCancel = reservation.status === 'confirmed' || reservation.status === 'pending';
    
    let actionButtonsHTML = '';
    if (canMarkComplete || canRequestReschedule || canRequestCancel) {
        actionButtonsHTML = '<div class="action-buttons-modal">';
        
        if (canMarkComplete) {
            actionButtonsHTML += `
                <button type="button" onclick="markAsCompleted(${reservation.id})" 
                        class="btn btn-complete font-semibold" style="flex: 1;">
                    <span class="material-icons" style="font-size: 18px;">done_all</span>
                    Mark as Completed
                </button>
            `;
        }
        
        if (canRequestReschedule) {
            actionButtonsHTML += `
                <button type="button" onclick="showRescheduleForm(${reservation.id}, '${reservation.date_schedule}', '${reservation.time_schedule}', '${reservation.table_code}')" 
                        class="btn btn-reschedule font-semibold" style="flex: 1;">
                    <span class="material-icons" style="font-size: 18px;">schedule</span>
                    Request Reschedule
                </button>
            `;
        }
        
        if (canRequestCancel) {
            actionButtonsHTML += `
                <button type="button" onclick="showCancelForm(${reservation.id}, '${reservation.date_schedule}', '${reservation.time_schedule}', '${reservation.table_code}')" 
                        class="btn btn-cancel font-semibold" style="flex: 1;">
                    <span class="material-icons" style="font-size: 18px;">cancel</span>
                    Request Cancel
                </button>
            `;
        }
        
        actionButtonsHTML += '</div>';
    }

    const detailsHTML = `
        <div class="details-grid">
            <div class="detail-section">
                <h3 class="detail-section-title">Reservation Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Reference Number</span>
                    <span class="detail-value" style="color: var(--primary-gold); font-weight: 600;">GB-${String(reservation.id).padStart(6, '0')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Table Number</span>
                    <span class="detail-value" style="color: var(--primary-gold); font-weight: 600;">${reservation.table_code || 'Not assigned'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reservation Date</span>
                    <span class="detail-value">${formatDate(reservation.date_schedule)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time Slot</span>
                    <span class="detail-value">${formatTime(reservation.time_schedule)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Number of Guests</span>
                    <span class="detail-value">${reservation.seats || 'N/A'} persons</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Corkage Fee</span>
                    <span class="detail-value" style="color: var(--warning-color); font-weight: 600;">₱${corkage_fee.toFixed(2)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Created On</span>
                    <span class="detail-value">${reservation.created_at ? new Date(reservation.created_at).toLocaleString() : 'N/A'}</span>
                </div>
            </div>

            <div class="detail-section">
                <h3 class="detail-section-title">Customer Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-value">${escapeHtml(reservation.customer_name || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email Address</span>
                    <span class="detail-value">${escapeHtml(reservation.customer_email || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone Number</span>
                    <span class="detail-value">${escapeHtml(reservation.customer_phone || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">${reservation.payment_method || 'Not specified'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reservation Status</span>
                    <span class="detail-value">
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="items-container">
            <h3 class="detail-section-title">Order Summary</h3>
            ${itemsHTML}
        </div>

        <div class="summary-section">
            <h3 class="summary-title">Payment Summary</h3>
            <div class="summary-row">
                <span class="summary-label">Menu Items Total</span>
                <span class="summary-value">₱${menu_total.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Promotional Deals Total</span>
                <span class="summary-value">₱${promo_total.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Group Deals Total</span>
                <span class="summary-value">₱${group_total.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">₱${subtotal.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Service Charge (10%)</span>
                <span class="summary-value" style="color: var(--primary-gold);">₱${service_charge.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Corkage Fee</span>
                <span class="summary-value" style="color: var(--warning-color); font-weight: 600;">₱${corkage_fee.toFixed(2)}</span>
            </div>
            <div class="summary-row grand-total">
                <span class="summary-label">Grand Total</span>
                <span class="summary-value">₱${grand_total.toFixed(2)}</span>
            </div>
        </div>

        ${actionButtonsHTML}
    `;

    content.innerHTML = detailsHTML;
}

function markAsCompleted(reservationId) {
    if (confirm(`Are you sure you want to mark this reservation as completed?\n\nThis will:\n1. Change the reservation status to "Completed"\n2. Make the table available for new reservations\n3. Send a notification to our admin team\n\nThis action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'completed';
        
        const reservationInput = document.createElement('input');
        reservationInput.type = 'hidden';
        reservationInput.name = 'reservation_id';
        reservationInput.value = reservationId;
        
        form.appendChild(actionInput);
        form.appendChild(reservationInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function viewPaymentProof(paymentProofPath) {
    showModal('paymentReceiptModal');
    const image = document.getElementById('receiptImage');
    
    image.src = `../static/upload/payments/${paymentProofPath}`;

    console.log(paymentProofPath);

    image.onerror = function() {
        this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjMWExYTFhIi8+CiAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAyMDApIj4KICAgIDxwYXRoIGQ9Ik0wLC00MCBBNDAsNDAgMCAxLDEgMCw0MCBBNDAsNDAgMCAxLDEgMCwtNDAgWiIgZmlsbD0iIzI0MjQyNCIvPgogICAgPHBhdGggZD0iTS0yMCwtMTAgTDAsMTAgTDIwLC0xMCBaIiBmaWxsPSIjZDRhZjM3Ii8+CiAgICA8dGV4dCB0ZXh0LWFuY2hvcj0ibWlkZGxlIiB5PSI1MCIgZm9udC1mYW1pbHk9IlBvcHBpbnMiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNhM2EzYTMiPlJlY2VpcHQgbm90IGF2YWlsYWJsZTwvdGV4dD4KICA8L2c+Cjwvc3ZnPg==';
    };
}

function showRescheduleForm(reservationId, date, time, table) {
    closeModal();
    setTimeout(() => {
        document.getElementById('rescheduleReservationId').value = reservationId;
        document.getElementById('rescheduleDate').textContent = formatDate(date);
        document.getElementById('rescheduleTime').textContent = formatTime(time);
        document.getElementById('rescheduleTable').textContent = table;
        document.getElementById('rescheduleReason').value = '';
        
        showModal('rescheduleModal');
    }, 300);
}

function showCancelForm(reservationId, date, time, table) {
    closeModal();
    setTimeout(() => {
        document.getElementById('cancelReservationId').value = reservationId;
        document.getElementById('cancelDate').textContent = formatDate(date);
        document.getElementById('cancelTime').textContent = formatTime(time);
        document.getElementById('cancelTable').textContent = table;
        document.getElementById('cancelReason').value = '';
        
        showModal('cancelModal');
    }, 300);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function formatTime(timeString) {
    const time = new Date(`1970-01-01T${timeString}`);
    return time.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('detailsModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function closePaymentModal() {
    document.getElementById('paymentReceiptModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.reservation-row');

    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        rows.forEach(row => {
            const table = row.getAttribute('data-table').toLowerCase();
            const date = row.getAttribute('data-date');
            const status = row.getAttribute('data-status');
            const total = row.getAttribute('data-total');
            
            const matchesSearch = table.includes(searchTerm) || 
                                 date.includes(searchTerm) || 
                                 total.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterRows);
    statusFilter.addEventListener('change', filterRows);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closePaymentModal();
        closeRescheduleModal();
        closeCancelModal();
    }
});

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
            closePaymentModal();
            closeRescheduleModal();
            closeCancelModal();
        }
    });
});

document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
    const reason = document.getElementById('rescheduleReason').value.trim();
    if (reason.length < 20) {
        e.preventDefault();
        showNotification('Please provide a more detailed reason (at least 20 characters).', 'warning');
        return false;
    }
});

document.getElementById('cancelForm').addEventListener('submit', function(e) {
    const reason = document.getElementById('cancelReason').value.trim();
    if (reason.length < 20) {
        e.preventDefault();
        showNotification('Please provide a more detailed reason (at least 20 characters).', 'warning');
        return false;
    }
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span class="material-icons">${type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'info'}</span>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('active');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('active');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 400);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        setTimeout(() => {
            searchInput.focus();
        }, 300);
    }
});
</script>

<?php include "../src/components/customer/footer.php"; ?>

<?php
ob_end_flush();
?>