<?php
ob_start();

require_once '../controller/class.php';

$db = new global_class();
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_request'])) {
    header('Content-Type: application/json');
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "grillbook";
    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $response = [];
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'get_reservations':
                    $page = $_POST['page'] ?? 1;
                    $search = $_POST['search'] ?? '';
                    $status = $_POST['status'] ?? 'all';
                    
                    $limit = 10;
                    $offset = ($page - 1) * $limit;
                    
                    $whereConditions = [];
                    $params = [];
                    $types = '';
                    
                    if (!empty($search)) {
                        $whereConditions[] = "(customer_name LIKE ? OR customer_email LIKE ? OR table_code LIKE ? OR reserve_unique_code LIKE ?)";
                        $searchTerm = "%{$search}%";
                        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
                        $types .= 'ssss';
                    }
                    
                    if ($status !== 'all') {
                        $whereConditions[] = "status = ?";
                        $params[] = $status;
                        $types .= 's';
                    }
                    
                    $whereClause = '';
                    if (!empty($whereConditions)) {
                        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
                    }
                    
                    $countQuery = "SELECT COUNT(*) as total FROM reservations $whereClause";
                    $countStmt = $conn->prepare($countQuery);
                    
                    if (!empty($params)) {
                        $countStmt->bind_param($types, ...$params);
                    }
                    
                    $countStmt->execute();
                    $totalResult = $countStmt->get_result()->fetch_assoc();
                    $totalReservations = $totalResult['total'];
                    $totalPages = ceil($totalReservations / $limit);
                    
                    $query = "SELECT * FROM reservations $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
                    
                    $params[] = $limit;
                    $params[] = $offset;
                    $types .= 'ii';
                    
                    $stmt = $conn->prepare($query);
                    if (!empty($params)) {
                        $stmt->bind_param($types, ...$params);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    $reservations = [];
                    while ($row = $result->fetch_assoc()) {
                        $reservations[] = $row;
                    }
                    
                    $response = [
                        'success' => true,
                        'reservations' => $reservations,
                        'totalPages' => $totalPages,
                        'currentPage' => $page,
                        'totalReservations' => $totalReservations
                    ];
                    break;
                    
                case 'get_details':
                    if (isset($_POST['id'])) {
                        $query = "SELECT * FROM reservations WHERE id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $_POST['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows === 0) {
                            $response = ['success' => false, 'message' => 'Reservation not found'];
                        } else {
                            $reservationData = $result->fetch_assoc();
                            
                            $menuIds = [];
                            $promoIds = [];
                            $groupIds = [];
                            
                            if (!empty($reservationData['selected_menus'])) {
                                $selectedMenus = json_decode($reservationData['selected_menus'], true);
                                if (is_array($selectedMenus)) {
                                    foreach ($selectedMenus as $menuId => $menuData) {
                                        if (is_numeric($menuId)) {
                                            $menuIds[] = (int)$menuId;
                                        }
                                    }
                                }
                            }
                            
                            if (!empty($reservationData['selected_promos'])) {
                                $selectedPromos = json_decode($reservationData['selected_promos'], true);
                                if (is_array($selectedPromos)) {
                                    foreach ($selectedPromos as $promoId => $promoData) {
                                        if (is_numeric($promoId)) {
                                            $promoIds[] = (int)$promoId;
                                        }
                                    }
                                }
                            }
                            
                            if (!empty($reservationData['selected_groups'])) {
                                $selectedGroups = json_decode($reservationData['selected_groups'], true);
                                if (is_array($selectedGroups)) {
                                    foreach ($selectedGroups as $groupId => $groupData) {
                                        if (is_numeric($groupId)) {
                                            $groupIds[] = (int)$groupId;
                                        }
                                    }
                                }
                            }
                            
                            $menuNames = [];
                            $promoNames = [];
                            $groupNames = [];
                            
                            if (!empty($menuIds)) {
                                $menuIdList = implode(',', $menuIds);
                                $menuQuery = "SELECT menu_id, menu_name FROM menu WHERE menu_id IN ($menuIdList)";
                                $menuResult = $conn->query($menuQuery);
                                while ($menuRow = $menuResult->fetch_assoc()) {
                                    $menuNames[$menuRow['menu_id']] = $menuRow['menu_name'];
                                }
                            }
                            
                            if (!empty($promoIds)) {
                                $promoIdList = implode(',', $promoIds);
                                $promoQuery = "SELECT menu_id, menu_name FROM menu WHERE menu_id IN ($promoIdList)";
                                $promoResult = $conn->query($promoQuery);
                                while ($promoRow = $promoResult->fetch_assoc()) {
                                    $promoNames[$promoRow['menu_id']] = $promoRow['menu_name'];
                                }
                            }
                            
                            if (!empty($groupIds)) {
                                $groupIdList = implode(',', $groupIds);
                                $groupQuery = "SELECT menu_id, menu_name FROM menu WHERE menu_id IN ($groupIdList)";
                                $groupResult = $conn->query($groupQuery);
                                while ($groupRow = $groupResult->fetch_assoc()) {
                                    $groupNames[$groupRow['menu_id']] = $groupRow['menu_name'];
                                }
                            }
                            
                            $response = [
                                'success' => true, 
                                'data' => $reservationData,
                                'menu_names' => $menuNames,
                                'promo_names' => $promoNames,
                                'group_names' => $groupNames
                            ];
                        }
                    } else {
                        $response = ['success' => false, 'message' => 'Reservation ID required'];
                    }
                    break;
                    
                case 'update_status':
                    if (isset($_POST['id']) && isset($_POST['status'])) {
                        $reservationId = $_POST['id'];
                        $newStatus = $_POST['status'];
                        
                        // Start transaction
                        $conn->begin_transaction();
                        
                        try {
                            // First get reservation details before updating
                            $reservationQuery = "SELECT * FROM reservations WHERE id = ? FOR UPDATE";
                            $reservationStmt = $conn->prepare($reservationQuery);
                            $reservationStmt->bind_param('i', $reservationId);
                            $reservationStmt->execute();
                            $reservationResult = $reservationStmt->get_result();
                            
                            if ($reservationResult->num_rows === 0) {
                                throw new Exception('Reservation not found');
                            }
                            
                            $reservationData = $reservationResult->fetch_assoc();
                            $oldStatus = $reservationData['status'];
                            $tableCode = $reservationData['table_code'];
                            
                            // Update reservation status
                            $query = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param('si', $newStatus, $reservationId);
                            
                            if (!$stmt->execute()) {
                                throw new Exception('Failed to update reservation status');
                            }
                            
                            // If status is being changed to 'completed', update walkin table availability
                            if ($newStatus === 'completed') {
                                // Check if table exists in walkin_tables table
                                $tableCheckQuery = "SELECT * FROM walkin_tables WHERE walkin_table_code = ?";
                                $tableCheckStmt = $conn->prepare($tableCheckQuery);
                                $tableCheckStmt->bind_param('s', $tableCode);
                                $tableCheckStmt->execute();
                                $tableResult = $tableCheckStmt->get_result();
                                
                                if ($tableResult->num_rows > 0) {
                                    // Table exists, update its status to available
                                    $updateTableQuery = "UPDATE walkin_tables SET walkin_status = 'available', walkin_updated_at = NOW() WHERE walkin_table_code = ?";
                                    $updateTableStmt = $conn->prepare($updateTableQuery);
                                    $updateTableStmt->bind_param('s', $tableCode);
                                    
                                    if (!$updateTableStmt->execute()) {
                                        throw new Exception('Failed to update table availability');
                                    }
                                } else {
                                    // Table doesn't exist in walkin_tables table, check regular tables
                                    $tableCheckQuery2 = "SELECT * FROM tables WHERE table_code = ?";
                                    $tableCheckStmt2 = $conn->prepare($tableCheckQuery2);
                                    $tableCheckStmt2->bind_param('s', $tableCode);
                                    $tableCheckStmt2->execute();
                                    $tableResult2 = $tableCheckStmt2->get_result();
                                    
                                    if ($tableResult2->num_rows > 0) {
                                        // Table exists in regular tables table
                                        $updateTableQuery2 = "UPDATE tables SET status = 'available', updated_at = NOW() WHERE table_code = ?";
                                        $updateTableStmt2 = $conn->prepare($updateTableQuery2);
                                        $updateTableStmt2->bind_param('s', $tableCode);
                                        
                                        if (!$updateTableStmt2->execute()) {
                                            throw new Exception('Failed to update table availability in regular tables');
                                        }
                                    } else {
                                        // Table doesn't exist in either table, log this for admin to see
                                        error_log("Table {$tableCode} not found in walkin_tables or tables tables while completing reservation {$reservationId}");
                                    }
                                }
                            }
                            
                            // If status is being changed from 'completed' to something else, set table back to unavailable
                            if ($oldStatus === 'completed' && $newStatus !== 'completed') {
                                // Check if table exists in walkin_tables table
                                $tableCheckQuery = "SELECT * FROM walkin_tables WHERE walkin_table_code = ?";
                                $tableCheckStmt = $conn->prepare($tableCheckQuery);
                                $tableCheckStmt->bind_param('s', $tableCode);
                                $tableCheckStmt->execute();
                                $tableResult = $tableCheckStmt->get_result();
                                
                                if ($tableResult->num_rows > 0) {
                                    // Table exists, update its status back to unavailable
                                    $updateTableQuery = "UPDATE walkin_tables SET walkin_status = 'unavailable', walkin_updated_at = NOW() WHERE walkin_table_code = ?";
                                    $updateTableStmt = $conn->prepare($updateTableQuery);
                                    $updateTableStmt->bind_param('s', $tableCode);
                                    
                                    if (!$updateTableStmt->execute()) {
                                        throw new Exception('Failed to update table availability');
                                    }
                                } else {
                                    // Check regular tables
                                    $tableCheckQuery2 = "SELECT * FROM tables WHERE table_code = ?";
                                    $tableCheckStmt2 = $conn->prepare($tableCheckQuery2);
                                    $tableCheckStmt2->bind_param('s', $tableCode);
                                    $tableCheckStmt2->execute();
                                    $tableResult2 = $tableCheckStmt2->get_result();
                                    
                                    if ($tableResult2->num_rows > 0) {
                                        // Table exists in regular tables
                                        $updateTableQuery2 = "UPDATE tables SET status = 'reserved', updated_at = NOW() WHERE table_code = ?";
                                        $updateTableStmt2 = $conn->prepare($updateTableQuery2);
                                        $updateTableStmt2->bind_param('s', $tableCode);
                                        
                                        if (!$updateTableStmt2->execute()) {
                                            throw new Exception('Failed to update table availability in regular tables');
                                        }
                                    }
                                }
                            }
                            
                            // Commit transaction
                            $conn->commit();
                            
                            // Send email notification
                            $emailSent = sendSimpleReservationEmail(
                                $reservationData['id'],
                                $newStatus,
                                $reservationData['customer_email'],
                                $reservationData['customer_name'],
                                $reservationData['reserve_unique_code'] ?? 'GB-' . str_pad($reservationData['id'], 6, '0', STR_PAD_LEFT),
                                $reservationData['date_schedule'],
                                $reservationData['time_schedule'],
                                $tableCode
                            );
                            
                            $response = [
                                'success' => true,
                                'message' => 'Status updated successfully' . ($emailSent ? ' and email sent' : ''),
                                'email_sent' => $emailSent,
                                'table_updated' => ($newStatus === 'completed' ? true : false)
                            ];
                            
                        } catch (Exception $e) {
                            // Rollback transaction on error
                            $conn->rollback();
                            throw $e;
                        }
                    } else {
                        $response = ['success' => false, 'message' => 'ID and status required'];
                    }
                    break;
                    
                default:
                    $response = ['success' => false, 'message' => 'Invalid action'];
            }
        } else {
            $response = ['success' => false, 'message' => 'No action specified'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
    
    ob_end_clean();
    echo json_encode($response);
    exit;
}

function sendSimpleReservationEmail($reservationId, $status, $email, $name, $orderCode, $date, $time, $table) {
    try {
        $formatted = '';
        if (!empty($date) && !empty($time)) {
            try {
                $dateTime = new DateTime("$date $time");
                $formatted = $dateTime->format('l, F j, Y - g:i A');
            } catch (Exception $e) {
                $formatted = "$date $time";
            }
        }
        
        require_once __DIR__ . '/../controller/class.php';
        $mailer = new Mailer();
        
        $result = $mailer->sendReservationStatusNotification($email, $name, $orderCode, $formatted, $status, $table);
        
        return $result;
        
    } catch (Exception $e) {
        error_log("❌ Email error for reservation $reservationId: " . $e->getMessage());
        return false;
    }
}

ob_end_clean();
ob_start();

include "../src/components/admin/header.php"; 
include "../src/components/admin/nav.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Management - GrillBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            width: 100%;
            position: relative;
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #2d3748;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .container, .wrapper, .max-w-7xl, .max-w-5xl {
            overflow: visible !important;
            max-height: none !important;
        }

        .mx-auto, .container, .main-content-wrapper {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        body > *:last-child {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        .high-contrast-text {
            color: #2d3748;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .glass-search-container {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .glass-search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            font-size: 1rem;
            color: #2d3748;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8),
                inset 0 -1px 0 rgba(0, 0, 0, 0.1);
        }

        .glass-search-input::placeholder {
            color: #718096;
            font-weight: 500;
        }

        .glass-search-input:focus {
            outline: none;
            border-color: rgba(217, 119, 6, 0.5);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 
                0 8px 24px rgba(217, 119, 6, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                inset 0 -1px 0 rgba(0, 0, 0, 0.05),
                0 0 0 3px rgba(217, 119, 6, 0.1);
            transform: translateY(-1px);
        }

        .glass-search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #d97706;
            font-size: 1.125rem;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .glass-search-input:focus + .glass-search-icon {
            color: #b45309;
            transform: translateY(-50%) scale(1.05);
        }

        .calendar-icon-container {
            padding: 12px;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .calendar-icon-container:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(217, 119, 6, 0.4);
        }

        .calendar-icon {
            color: white;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 
                0 8px 20px -5px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #d97706, #b45309);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #b45309, #92400e);
        }

        .fade-in {
          animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
          animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
          from { transform: translateX(100%); }
          to { transform: translateX(0); }
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            border-width: 1px;
            min-width: 100px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        /* UPDATED STATUS COLORS TO MATCH DASHBOARD.PHP */
        .status-pending {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white !important;
            border-color: #f59e0b !important;
        }

        .status-confirmed {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            color: white !important;
            border-color: #16a34a !important;
        }

        .status-completed {
            background: linear-gradient(135deg, #6b7280, #4b5563) !important;
            color: white !important;
            border-color: #6b7280 !important;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: white !important;
            border-color: #dc2626 !important;
        }

        .status-request-cancel {
            background: linear-gradient(135deg, #ea580c, #c2410c) !important;
            color: white !important;
            border-color: #ea580c !important;
        }

        .status-request-reschedule {
            background: linear-gradient(135deg, #9333ea, #7c3aed) !important;
            color: white !important;
            border-color: #9333ea !important;
        }

        .table-hover-row:hover {
          background: #fefce8;
          transform: translateX(2px);
          transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-backdrop {
          background: rgba(0, 0, 0, 0.5);
          backdrop-filter: blur(8px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 2px solid #92400e;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(217, 119, 6, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: 2px solid #b91c1c;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: 2px solid #b45309;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(245, 158, 11, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border: 2px solid #6d28d9;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(139, 92, 246, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: 2px solid #047857;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(16, 185, 129, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .enhanced-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.1);
        }

        .enhanced-table th {
            background: linear-gradient(135deg, #f8f4e5 0%, #f0e6c3 100%);
            color: #2d3748;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 14px 10px;
            border-bottom: 2px solid #e2e8f0;
            text-align: center;
        }

        .enhanced-table td {
            padding: 12px 10px;
            font-size: 0.85rem;
            border-bottom: 1px solid #f7fafc;
            vertical-align: middle;
            text-align: center;
        }

        .enhanced-table tr:hover {
            background: #fefce8;
        }

        .enhanced-modal {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25);
            border: 2px solid #e2e8f0;
        }

        .form-input-enhanced {
            background: white;
            border: 2px solid #cbd5e0;
            color: #2d3748;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }

        .form-input-enhanced:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2);
            outline: none;
        }

        .form-input-enhanced::placeholder {
            color: #718096;
        }

        :root {
            --primary-gold: #d97706;
            --dark-gold: #b45309;
            --light-gold: #fef3c7;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --border-light: #e2e8f0;
            --background-light: #f8f4e5;
        }

        @media (max-width: 768px) {
            .enhanced-table th,
            .enhanced-table td {
                padding: 10px 6px;
                font-size: 0.8rem;
            }

            .glass-search-container {
                max-width: 100%;
            }
        }

        *:focus {
            outline: 2px solid #d97706;
            outline-offset: 1px;
        }

        ::selection {
            background: #d97706;
            color: white;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(217, 119, 6, 0.3);
            border-top: 2px solid #d97706;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fas, .far, .fab, .svg-inline--fa {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
            width: 1em !important;
            height: 1em !important;
        }

        .action-btn {
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            border: 1px solid;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .view-btn {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-color: #1d4ed8;
        }

        .edit-btn {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border-color: #b45309;
        }

        .delete-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-color: #b91c1c;
        }

        .professional-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .search-filter-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .search-filter-row {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .search-filter-row {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 1.5rem;
            }
        }

        .search-controls {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            flex: 1;
        }

        @media (min-width: 640px) {
            .search-controls {
                flex-direction: row;
                align-items: center;
            }
        }

        .stats-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .reference-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #92400e;
            background: #fef3c7;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #f59e0b;
            font-size: 0.8rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding: 1rem;
        }

        .page-btn {
            padding: 8px 14px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 0.85rem;
        }

        .page-btn:hover {
            border-color: #d97706;
            background: #fef3c7;
        }

        .page-btn.active {
            background: #d97706;
            color: white;
            border-color: #b45309;
        }

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1em;
            height: 1em;
        }

        .table-actions {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .action-icon {
            width: 14px;
            height: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .amount-text {
            font-weight: 700;
            color: #065f46;
            font-size: 0.85rem;
        }

        .payment-method {
            font-weight: 600;
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .payment-cash {
            background: #d1fae5;
            color: #065f46;
        }

        .payment-online {
            background: #dbeafe;
            color: #1e40af;
        }

        .customer-info {
            font-size: 0.8rem;
            line-height: 1.3;
        }

        .time-schedule {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 0.85rem;
            color: #92400e;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 1rem;
            padding: 1rem;
        }

        .modal-action-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            min-width: 120px;
        }

        .modal-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .text-gray-600 {
            color: #2d3748 !important;
        }

        .text-gray-400 {
            color: #4a5568 !important;
        }

        .text-gray-700 {
            color: #1a202c !important;
        }

        .text-gray-800 {
            color: #000000 !important;
        }

        .fas, .far, .fab, .svg-inline--fa, .fa-calendar-check, .fa-sync-alt, .fa-chart-line,
        .fa-search, .fa-clock, .fa-check, .fa-times, .fa-exclamation-triangle, .fa-calendar-alt,
        .fa-file-invoice, .fa-user, .fa-credit-card, .fa-calculator, .fa-utensils, .fa-history,
        .fa-receipt, .fa-ban, .fa-eye, .fa-calendar-alt, .fa-check-circle {
            color: inherit !important;
            opacity: 1 !important;
            visibility: visible !important;
            font-style: normal;
            font-weight: 900;
        }

        .view-btn i,
        .edit-btn i,
        .delete-btn i,
        .action-btn i,
        .modal-action-btn i {
            opacity: 1 !important;
            visibility: visible !important;
            color: inherit !important;
        }
    </style>
</head>
<body>

    <div class="professional-header fade-in">
        <div class="flex items-center space-x-3">
            <div class="calendar-icon-container">
                <i class="fas fa-calendar-check calendar-icon icon-wrapper"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Reservation Management</h1>
                <p class="text-gray-600 text-sm mt-0.5">Review and manage restaurant reservation requests</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button class="p-2 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-md rounded-lg" 
                    onclick="location.reload()" 
                    title="Refresh Data">
                <i class="fas fa-sync-alt text-base icon-wrapper"></i>
            </button>
        </div>
    </div>

    <div class="search-filter-section fade-in">
        <div class="search-filter-row">
            <div class="search-controls">
                <div class="glass-search-container">
                    <input
                        type="text"
                        id="searchInput"
                        class="glass-search-input"
                        placeholder="Search reservations..."
                    />
                    <i class="fas fa-search glass-search-icon icon-wrapper"></i>
                </div>

                <div class="relative flex-1 sm:max-w-xs">
                    <select
                        name="filterStatus"
                        id="filterStatus"
                        class="w-full px-3 py-2 bg-white border-2 border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced text-sm"
                    >
                        <option value="all">All Reservations</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="request_reschedule">Reschedule</option>
                        <option value="request_cancel">Cancel Request</option>
                    </select>
                </div>
            </div>

            <div class="stats-info">
                <i class="fas fa-chart-line text-[#d97706] text-base icon-wrapper"></i>
                <span class="text-gray-600 text-sm">Updated: <span id="lastUpdated" class="font-semibold text-[#92400e]">Just now</span></span>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl p-4 fade-in">
        <div class="max-h-[500px] overflow-y-auto overflow-x-hidden rounded-lg border border-gray-200 custom-scrollbar">
            <table class="w-full enhanced-table">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Reservation Code</th>
                        <th class="text-center">Table</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Seats</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Payment</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="outputTableBody" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="11" class="p-6 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 border-3 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                                <div>
                                    <p class="font-semibold high-contrast-text text-sm">Loading reservation data...</p>
                                    <p class="text-gray-600 text-xs mt-1">Please wait while we fetch the latest reservations</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="pagination" class="pagination">
        </div>
    </div>

    <div id="spinnerOverlay" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop hidden">
        <div class="enhanced-modal p-6 flex flex-col items-center space-y-3">
            <div class="w-12 h-12 border-3 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
            <div class="text-center">
                <p class="font-semibold high-contrast-text text-sm">Processing Request</p>
                <p class="text-gray-600 text-xs mt-1">Please wait while we process your action...</p>
            </div>
        </div>
    </div>

    <div id="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop hidden">
        <div class="enhanced-modal w-full max-w-4xl mx-6 p-5 text-gray-800 relative max-h-[85vh] overflow-y-auto scrollbar-hidden slide-in">
            <button id="closeModal" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 text-xl font-bold transition focus:outline-none focus:ring-2 focus:ring-[#d97706] rounded-full p-1">
                <i class="fas fa-times icon-wrapper"></i>
            </button>

            <div class="flex items-center space-x-2 mb-4">
                <div class="p-2 bg-gradient-to-r from-[#d97706] to-[#b45309] rounded-lg">
                    <i class="fas fa-file-invoice text-white text-lg icon-wrapper"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#92400e] high-contrast-text">Reservation Details</h2>
                    <p class="text-gray-600 text-xs">Complete reservation information and management</p>
                </div>
            </div>

            <hr class="border-gray-300 mb-4">

            <div id="modalContent" class="space-y-4 text-sm">
            </div>

            <hr class="border-gray-300 my-4">

            <input type="hidden" id="reservation_id" name="reservation_id">
            
            <div class="modal-actions">
                <button type="button" id="btnReschedule" 
                        class="btn-info modal-action-btn"
                        data-action="request_reschedule">
                    <i class="fas fa-calendar-alt icon-wrapper action-icon"></i>
                    <span>Reschedule</span>
                </button>
                <button type="button" id="btnCancel" 
                        class="btn-warning modal-action-btn"
                        data-action="request_cancel">
                    <i class="fas fa-times icon-wrapper action-icon"></i>
                    <span>Request Cancel</span>
                </button>
                <button type="button" id="btnDecline" 
                        class="btn-danger modal-action-btn"
                        data-action="cancelled">
                    <i class="fas fa-ban icon-wrapper action-icon"></i>
                    <span>Decline</span>
                </button>
                <button type="submit" id="btnComplete" 
                        class="btn-success modal-action-btn"
                        data-action="completed">
                    <i class="fas fa-check-circle icon-wrapper action-icon"></i>
                    <span>Complete</span>
                </button>
                <button type="submit" id="btnApprove" 
                        class="btn-primary modal-action-btn"
                        data-action="confirmed">
                    <i class="fas fa-check icon-wrapper action-icon"></i>
                    <span>Approve</span>
                </button>
            </div>
        </div>
    </div>

    <div id="payment_img_modal" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop hidden">
        <div class="enhanced-modal w-full max-w-2xl mx-4 p-4 relative">
            <button id="close_modal" class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-xl font-bold transition focus:outline-none focus:ring-2 focus:ring-[#d97706] rounded-full p-1">
                <i class="fas fa-times icon-wrapper"></i>
            </button>
            <div class="text-center mb-3">
                <h3 class="text-base font-bold text-[#92400e] high-contrast-text">Payment Receipt</h3>
                <p class="text-gray-600 text-xs">Proof of payment submitted by customer</p>
            </div>
            <img id="modal_img" src="" alt="Payment Receipt" class="w-full h-auto rounded-md border border-gray-300 shadow-inner" />
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        
        const reservationManager = new ReservationManager();
        window.reservationManager = reservationManager;
    });

    class ReservationManager {
        constructor() {
            this.currentPage = 1;
            this.totalPages = 1;
            this.searchTerm = '';
            this.filterStatus = 'all';
            
            this.init();
        }
        
        init() {
            this.loadReservations();
            this.setupEventListeners();
            this.ensureIconVisibility();
        }
        
        setupEventListeners() {
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchTerm = e.target.value;
                    this.currentPage = 1;
                    this.loadReservations();
                }, 500);
            });
            
            document.getElementById('filterStatus').addEventListener('change', (e) => {
                this.filterStatus = e.target.value;
                this.currentPage = 1;
                this.loadReservations();
            });
            
            document.getElementById('closeModal').addEventListener('click', () => {
                this.hideModal('detailsModal');
            });
            
            document.getElementById('close_modal').addEventListener('click', () => {
                this.hideModal('payment_img_modal');
            });
            
            document.getElementById('btnApprove').addEventListener('click', () => {
                this.updateReservationStatus('confirmed');
            });
            
            document.getElementById('btnDecline').addEventListener('click', () => {
                this.updateReservationStatus('cancelled');
            });
            
            document.getElementById('btnCancel').addEventListener('click', () => {
                this.updateReservationStatus('request_cancel');
            });
            
            document.getElementById('btnReschedule').addEventListener('click', () => {
                this.updateReservationStatus('request_reschedule');
            });
            
            document.getElementById('btnComplete').addEventListener('click', () => {
                this.completeReservation();
            });
            
            document.addEventListener('click', (e) => {
                if (e.target.id === 'detailsModal') {
                    e.target.classList.add('hidden');
                }
                if (e.target.id === 'payment_img_modal') {
                    e.target.classList.add('hidden');
                }
            });
            
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.getElementById('detailsModal')?.classList.add('hidden');
                    document.getElementById('payment_img_modal')?.classList.add('hidden');
                }
            });
        }
        
        ensureIconVisibility() {
            const icons = document.querySelectorAll('i, .fas, .far, .fab, .svg-inline--fa');
            icons.forEach(icon => {
                icon.style.opacity = '1';
                icon.style.visibility = 'visible';
                icon.style.display = 'inline-block';
                icon.style.width = '1em';
                icon.style.height = '1em';
                
                const parent = icon.parentElement;
                if (parent) {
                    parent.style.display = 'flex';
                    parent.style.alignItems = 'center';
                    parent.style.justifyContent = 'center';
                }
            });
        }
        
        async loadReservations() {
            this.showSpinner();
            
            try {
                const formData = new FormData();
                formData.append('ajax_request', 'true');
                formData.append('action', 'get_reservations');
                formData.append('page', this.currentPage);
                formData.append('search', this.searchTerm);
                formData.append('status', this.filterStatus);
                
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text.substring(0, 200));
                    throw new Error('Server returned invalid JSON');
                }
                
                if (data.success && data.reservations) {
                    this.displayReservations(data.reservations);
                    this.setupPagination(data.totalPages, data.currentPage, data.totalReservations);
                } else {
                    this.showError(data.message || 'No reservation data received');
                }
            } catch (error) {
                console.error('Error loading reservations:', error);
                this.showError('Failed to load reservations: ' + error.message);
            } finally {
                this.hideSpinner();
                this.updateLastUpdated();
            }
        }
        
        displayReservations(reservations) {
            const tbody = document.getElementById('outputTableBody');
            
            if (reservations.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" class="p-6 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                                <div>
                                    <p class="font-semibold high-contrast-text text-sm">No reservations found</p>
                                    <p class="text-gray-600 text-xs mt-1">Try adjusting your search or filter criteria</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = reservations.map((reservation, index) => `
                <tr class="table-hover-row fade-in" data-id="${reservation.id}">
                    <td class="text-center font-semibold text-sm">${(this.currentPage - 1) * 10 + index + 1}</td>
                    <td class="text-center">
                        <span class="font-bold text-[#92400e] text-sm reference-number">${reservation.reserve_unique_code || 'GB-' + reservation.id.toString().padStart(6, '0')}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-bold text-[#92400e] text-sm">${reservation.table_code || 'N/A'}</span>
                    </td>
                    <td class="text-center">
                        <div class="customer-info">
                            <span class="font-semibold block">${reservation.customer_name || 'N/A'}</span>
                            <span class="text-gray-600 text-xs">${reservation.customer_email || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="text-center font-bold text-sm">${reservation.seats || 'N/A'}</td>
                    <td class="text-center text-sm">${reservation.date_schedule || 'N/A'}</td>
                    <td class="text-center time-schedule">${reservation.time_schedule || 'N/A'}</td>
                    <td class="text-center amount-text">₱${parseFloat(reservation.grand_total || 0).toFixed(2)}</td>
                    <td class="text-center">
                        <span class="payment-method ${reservation.payment_method === 'cash' ? 'payment-cash' : 'payment-online'}">
                            ${reservation.payment_method || 'N/A'}
                        </span>
                    </td>
                    <td class="text-center">
                        ${this.getStatusBadge(reservation.status)}
                    </td>
                    <td class="text-center">
                        <div class="table-actions">
                            <button class="action-btn view-btn" onclick="reservationManager.viewDetails(${reservation.id})" 
                                    title="View Details">
                                <i class="fas fa-eye action-icon"></i>
                                <span class="hidden sm:inline">View</span>
                            </button>
                            ${reservation.payment_proof ? `
                            <button class="action-btn edit-btn" onclick="reservationManager.viewPaymentProof('${reservation.payment_proof}')" 
                                    title="View Payment Proof">
                                <i class="fas fa-receipt action-icon"></i>
                                <span class="hidden sm:inline">Receipt</span>
                            </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        getStatusBadge(status) {
            // UPDATED TO MATCH DASHBOARD.PHP COLORS EXACTLY
            const statusConfig = {
                'pending': {
                    class: 'status-pending',
                    text: 'Pending',
                    icon: 'fa-clock'
                },
                'confirmed': {
                    class: 'status-confirmed',
                    text: 'Approved',
                    icon: 'fa-check'
                },
                'cancelled': {
                    class: 'status-cancelled',
                    text: 'Cancelled',
                    icon: 'fa-times'
                },
                'request_cancel': {
                    class: 'status-request-cancel',
                    text: 'Cancel Request',
                    icon: 'fa-exclamation-triangle'
                },
                'request_reschedule': {
                    class: 'status-request-reschedule',
                    text: 'Reschedule',
                    icon: 'fa-calendar-alt'
                },
                'completed': {
                    class: 'status-completed',
                    text: 'Completed',
                    icon: 'fa-check-circle'
                }
            };
            
            const config = statusConfig[status] || statusConfig.pending;
            
            return `
                <div class="status-badge ${config.class}">
                    <i class="fas ${config.icon} icon-wrapper"></i>
                    <span>${config.text}</span>
                </div>
            `;
        }
        
        setupPagination(totalPages, currentPage, totalReservations) {
            const pagination = document.getElementById('pagination');
            this.totalPages = totalPages;
            this.currentPage = currentPage;
            
            if (totalPages <= 1) {
                pagination.innerHTML = `
                    <div class="text-gray-600 text-sm">
                        Showing ${totalReservations} reservation${totalReservations !== 1 ? 's' : ''}
                    </div>
                `;
                return;
            }
            
            let paginationHTML = `
                <div class="flex items-center space-x-2">
                    <button class="page-btn ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}" 
                            ${currentPage === 1 ? 'disabled' : ''} 
                            onclick="reservationManager.goToPage(${currentPage - 1})">
                        <i class="fas fa-chevron-left icon-wrapper"></i>
                        <span>Previous</span>
                    </button>
            `;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    paginationHTML += `
                        <button class="page-btn ${currentPage === i ? 'active' : ''}" 
                                onclick="reservationManager.goToPage(${i})">
                            ${i}
                        </button>
                    `;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    paginationHTML += `<span class="px-2 text-gray-500">...</span>`;
                }
            }
            
            paginationHTML += `
                    <button class="page-btn ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}" 
                            ${currentPage === totalPages ? 'disabled' : ''} 
                            onclick="reservationManager.goToPage(${currentPage + 1})">
                        <span>Next</span>
                        <i class="fas fa-chevron-right icon-wrapper"></i>
                    </button>
                </div>
                <div class="text-gray-600 text-sm ml-3">
                    Page ${currentPage} of ${totalPages}
                </div>
            `;
            
            pagination.innerHTML = paginationHTML;
        }
        
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
                this.currentPage = page;
                this.loadReservations();
            }
        }
        
        async viewDetails(reservationId) {
            this.showSpinner();
            
            try {
                const formData = new FormData();
                formData.append('ajax_request', 'true');
                formData.append('action', 'get_details');
                formData.append('id', reservationId);
                
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text.substring(0, 200));
                    throw new Error('Server returned invalid JSON');
                }
                
                if (data.success && data.data) {
                    this.showReservationDetails(data.data, data.menu_names || {}, data.promo_names || {}, data.group_names || {});
                } else {
                    this.showError(data.message || 'Failed to load reservation details');
                }
            } catch (error) {
                console.error('Error loading reservation details:', error);
                this.showError('Failed to load reservation details: ' + error.message);
            } finally {
                this.hideSpinner();
            }
        }

        showReservationDetails(reservation, menuNames, promoNames, groupNames) {
            const modalContent = document.getElementById('modalContent');
            document.getElementById('reservation_id').value = reservation.id;
            
            let selectedMenus = {};
            let selectedPromos = {};
            let selectedGroups = {};
            
            try {
                selectedMenus = reservation.selected_menus ? JSON.parse(reservation.selected_menus) : {};
            } catch (e) {
                selectedMenus = {};
            }
            
            try {
                selectedPromos = reservation.selected_promos ? JSON.parse(reservation.selected_promos) : {};
            } catch (e) {
                selectedPromos = {};
            }
            
            try {
                selectedGroups = reservation.selected_groups ? JSON.parse(reservation.selected_groups) : {};
            } catch (e) {
                selectedGroups = {};
            }
            
            const menusList = Object.entries(selectedMenus).map(([id, menu]) => {
                const menuName = menuNames[id] || menu.name || 'Menu Item';
                return `<li>${menuName} - ₱${menu.price || 0} x ${menu.quantity || 1}</li>`;
            }).join('');
            
            const promosList = Object.entries(selectedPromos).map(([id, promo]) => {
                const promoName = promoNames[id] || promo.name || 'Promo Item';
                return `<li>${promoName} - ₱${promo.price || 0} x ${promo.quantity || 1}</li>`;
            }).join('');
            
            const groupsList = Object.entries(selectedGroups).map(([id, group]) => {
                const groupName = groupNames[id] || group.name || 'Group Item';
                return `<li>${groupName} - ₱${group.price || 0} x ${group.quantity || 1}</li>`;
            }).join('');
            
            modalContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-card p-4">
                        <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-base">
                            <i class="fas fa-user mr-2 icon-wrapper"></i>
                            Customer Information
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Name:</strong> ${reservation.customer_name || 'N/A'}</div>
                            <div><strong>Email:</strong> ${reservation.customer_email || 'N/A'}</div>
                            <div><strong>Phone:</strong> ${reservation.customer_phone || 'N/A'}</div>
                            <div><strong>Reservation Code:</strong> <span class="reference-number">${reservation.reserve_unique_code || 'GB-' + reservation.id.toString().padStart(6, '0')}</span></div>
                        </div>
                    </div>
                    
                    <div class="glass-card p-4">
                        <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-base">
                            <i class="fas fa-calendar-alt mr-2 icon-wrapper"></i>
                            Reservation Details
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Table:</strong> ${reservation.table_code || 'N/A'}</div>
                            <div><strong>Seats:</strong> ${reservation.seats || 'N/A'}</div>
                            <div><strong>Date:</strong> ${reservation.date_schedule || 'N/A'}</div>
                            <div><strong>Time:</strong> ${reservation.time_schedule || 'N/A'}</div>
                            <div><strong>Table Status:</strong> ${reservation.status === 'completed' ? '<span class="text-green-600 font-semibold">Available for new reservations</span>' : '<span class="text-orange-600 font-semibold">Currently reserved</span>'}</div>
                        </div>
                    </div>
                    
                    <div class="glass-card p-4">
                        <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-base">
                            <i class="fas fa-credit-card mr-2 icon-wrapper"></i>
                            Payment Information
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Method:</strong> ${reservation.payment_method || 'N/A'}</div>
                            <div><strong>Type:</strong> ${reservation.payment_type || 'N/A'}</div>
                            <div><strong>Amount:</strong> ₱${parseFloat(reservation.amount_to_pay || 0).toFixed(2)}</div>
                            ${reservation.payment_proof ? `
                            <div>
                                <strong>Proof:</strong> 
                                <button class="text-[#d97706] underline ml-1 text-sm" 
                                        onclick="reservationManager.viewPaymentProof('${reservation.payment_proof}')">
                                    View Receipt
                                </button>
                            </div>
                            ` : '<div><strong>Proof:</strong> None</div>'}
                        </div>
                    </div>
                    
                    <div class="glass-card p-4">
                        <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-base">
                            <i class="fas fa-calculator mr-2 icon-wrapper"></i>
                            Financial Breakdown
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div><strong>Menu Total:</strong> ₱${parseFloat(reservation.menu_total || 0).toFixed(2)}</div>
                            <div><strong>Promo Total:</strong> ₱${parseFloat(reservation.promo_total || 0).toFixed(2)}</div>
                            <div><strong>Group Total:</strong> ₱${parseFloat(reservation.group_total || 0).toFixed(2)}</div>
                            <div><strong>Corkage Fee:</strong> ₱${parseFloat(reservation.corkage_fee || 0).toFixed(2)}</div>
                            <div class="border-t pt-2 font-bold text-base">
                                <strong>Grand Total:</strong> ₱${parseFloat(reservation.grand_total || 0).toFixed(2)}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="glass-card p-4 mt-4">
                    <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-base">
                        <i class="fas fa-utensils mr-2 icon-wrapper"></i>
                        Selected Items
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        ${menusList ? `
                        <div>
                            <h4 class="font-semibold mb-2 text-sm">Menus</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                ${menusList}
                            </ul>
                        </div>
                        ` : '<div><h4 class="font-semibold mb-2 text-sm">Menus</h4><p class="text-gray-600 text-sm">No menus</p></div>'}
                        
                        ${promosList ? `
                        <div >
                            <h4 class="font-semibold mb-2 text-sm">Promos</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                ${promosList}
                            </ul>
                        </div>
                        ` : '<div hidden><h4 class="font-semibold mb-2 text-sm">Promos</h4><p class="text-gray-600 text-sm">No promos</p></div>'}
                        
                        ${groupsList ? `
                        <div>
                            <h4 class="font-semibold mb-2 text-sm">Groups</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                ${groupsList}
                            </ul>
                        </div>
                        ` : '<div hidden><h4 class="font-semibold mb-2 text-sm">Groups</h4><p class="text-gray-600 text-sm">No groups</p></div>'}
                    </div>
                </div>
                
                <div class="glass-card p-4 mt-4">
                    <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-base">
                        <i class="fas fa-history mr-2 icon-wrapper"></i>
                        Timeline
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div><strong>Created:</strong> ${new Date(reservation.created_at).toLocaleString()}</div>
                        <div><strong>Updated:</strong> ${new Date(reservation.updated_at).toLocaleString()}</div>
                    </div>
                </div>
            `;
            
            this.showModal('detailsModal');
            this.updateActionButtons(reservation.status);
        }
        
        updateActionButtons(currentStatus) {
            const approveBtn = document.getElementById('btnApprove');
            const declineBtn = document.getElementById('btnDecline');
            const cancelBtn = document.getElementById('btnCancel');
            const rescheduleBtn = document.getElementById('btnReschedule');
            const completeBtn = document.getElementById('btnComplete');
            
            approveBtn.style.display = 'none';
            declineBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            rescheduleBtn.style.display = 'none';
            completeBtn.style.display = 'none';
            
            switch(currentStatus) {
                case 'pending':
                    approveBtn.style.display = 'flex';
                    declineBtn.style.display = 'flex';
                    cancelBtn.style.display = 'flex';
                    rescheduleBtn.style.display = 'flex';
                    break;
                case 'request_reschedule':
                    approveBtn.style.display = 'flex';
                    declineBtn.style.display = 'flex';
                    break;
                case 'request_cancel':
                    approveBtn.style.display = 'flex';
                    declineBtn.style.display = 'flex';
                    break;
                case 'confirmed':
                    completeBtn.style.display = 'flex';
                    cancelBtn.style.display = 'flex';
                    rescheduleBtn.style.display = 'flex';
                    break;
                case 'completed':
                    // No actions for completed reservations
                    break;
                default:
                    break;
            }
        }
        
        async updateReservationStatus(newStatus) {
            const reservationId = document.getElementById('reservation_id').value;
            
            if (!reservationId) {
                this.showError('No reservation selected');
                return;
            }
            
            const actionText = newStatus === 'confirmed' ? 'approve' : 
                              newStatus === 'cancelled' ? 'decline' :
                              newStatus === 'request_cancel' ? 'request cancellation' :
                              newStatus === 'request_reschedule' ? 'request reschedule' : 'update';
            
            this.showProfessionalConfirm(
                `Are you sure you want to ${actionText} this reservation?`,
                async () => {
                    this.showSpinner();
                    
                    try {
                        const formData = new FormData();
                        formData.append('ajax_request', 'true');
                        formData.append('action', 'update_status');
                        formData.append('id', reservationId);
                        formData.append('status', newStatus);
                        
                        const response = await fetch('', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const text = await response.text();
                        let result;
                        try {
                            result = JSON.parse(text);
                        } catch (e) {
                            console.error('Invalid JSON response:', text.substring(0, 200));
                            throw new Error('Server returned invalid JSON');
                        }
                        
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `Reservation ${actionText}d successfully${result.email_sent ? ' and email sent' : ''}`,
                                background: '#fff',
                                color: '#2d3748',
                                confirmButtonColor: '#d97706'
                            });
                            this.hideModal('detailsModal');
                            this.loadReservations();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to update reservation',
                                background: '#fff',
                                color: '#2d3748',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    } catch (error) {
                        console.error('Error updating reservation:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update reservation: ' + error.message,
                            background: '#fff',
                            color: '#2d3748',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.hideSpinner();
                    }
                }
            );
        }
        
        async completeReservation() {
            const reservationId = document.getElementById('reservation_id').value;
            
            if (!reservationId) {
                this.showError('No reservation selected');
                return;
            }
            
            this.showProfessionalConfirm(
                'Are you sure you want to mark this reservation as completed?<br><br>' +
                '<small class="text-gray-600">This will make the table available for new reservations.</small>',
                async () => {
                    this.showSpinner();
                    
                    try {
                        const formData = new FormData();
                        formData.append('ajax_request', 'true');
                        formData.append('action', 'update_status');
                        formData.append('id', reservationId);
                        formData.append('status', 'completed');
                        
                        const response = await fetch('', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const text = await response.text();
                        let result;
                        try {
                            result = JSON.parse(text);
                        } catch (e) {
                            console.error('Invalid JSON response:', text.substring(0, 200));
                            throw new Error('Server returned invalid JSON');
                        }
                        
                        if (result.success) {
                            let successMessage = 'Reservation marked as completed successfully';
                            if (result.email_sent) {
                                successMessage += ' and email sent';
                            }
                            if (result.table_updated) {
                                successMessage += '<br><small class="text-green-600">✓ Table is now available for new reservations.</small>';
                            } else {
                                successMessage += '<br><small class="text-yellow-600">⚠ Table not found in walk-in tables.</small>';
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Completed!',
                                html: successMessage,
                                background: '#fff',
                                color: '#2d3748',
                                confirmButtonColor: '#10b981'
                            });
                            this.hideModal('detailsModal');
                            this.loadReservations();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to complete reservation',
                                background: '#fff',
                                color: '#2d3748',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    } catch (error) {
                        console.error('Error completing reservation:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to complete reservation: ' + error.message,
                            background: '#fff',
                            color: '#2d3748',
                            confirmButtonColor: '#ef4444'
                        });
                    } finally {
                        this.hideSpinner();
                    }
                }
            );
        }
        
        viewPaymentProof(imagePath) {
            const modalImg = document.getElementById('modal_img');
            modalImg.src = `../static/upload/payments/${imagePath}`;
            modalImg.alt = 'Payment Receipt';
            this.showModal('payment_img_modal');
        }
        
        showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        
        hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        
        showSpinner() {
            document.getElementById('spinnerOverlay').classList.remove('hidden');
        }
        
        hideSpinner() {
            document.getElementById('spinnerOverlay').classList.add('hidden');
        }
        
        showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                background: '#fff',
                color: '#2d3748',
                confirmButtonColor: '#ef4444'
            });
        }
        
        showProfessionalConfirm(message, confirmCallback) {
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'question',
                background: '#fff',
                color: '#2d3748',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmCallback();
                }
            });
        }
        
        updateLastUpdated() {
            const element = document.getElementById('lastUpdated');
            if (element) {
                element.textContent = new Date().toLocaleTimeString();
            }
        }
    }

    setInterval(() => {
        if (window.reservationManager) {
            window.reservationManager.ensureIconVisibility();
        }
    }, 2000);
    </script>

<?php 
include "../src/components/admin/footer.php"; 
ob_end_flush();
?>
</body>
</html>