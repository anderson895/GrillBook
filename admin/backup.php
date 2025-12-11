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
                case 'get_reservation_records':
                    $page = $_POST['page'] ?? 1;
                    $search = $_POST['search'] ?? '';
                    $status = $_POST['status'] ?? 'all';
                    
                    $limit = 10;
                    $offset = ($page - 1) * $limit;
                    
                    $whereConditions = [];
                    $params = [];
                    $types = '';
                    
                    if (!empty($search)) {
                        $whereConditions[] = "(customer_name LIKE ? OR customer_email LIKE ? OR table_code LIKE ? OR CONCAT(DATE_FORMAT(created_at, '%y'), '-', LPAD(id, 4, '0')) LIKE ?)";
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
                    
                    $query = "SELECT 
                        *,
                        CONCAT(
                            DATE_FORMAT(created_at, '%y'), 
                            '-', 
                            LPAD(id, 4, '0')
                        ) as reservation_code 
                        FROM reservations $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
                    
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
                    
                case 'export_reservation_records':
                    $search = $_POST['search'] ?? '';
                    $status = $_POST['status'] ?? 'all';
                    
                    $whereConditions = [];
                    $params = [];
                    $types = '';
                    
                    if (!empty($search)) {
                        $whereConditions[] = "(customer_name LIKE ? OR customer_email LIKE ? OR table_code LIKE ? OR CONCAT(DATE_FORMAT(created_at, '%y'), '-', LPAD(id, 4, '0')) LIKE ?)";
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
                    
                    $query = "SELECT 
                        *,
                        CONCAT(
                            DATE_FORMAT(created_at, '%y'), 
                            '-', 
                            LPAD(id, 4, '0')
                        ) as reservation_code 
                        FROM reservations $whereClause ORDER BY created_at DESC";
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
                        'data' => $reservations
                    ];
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

include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Records - GrillBook Admin</title>
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

        .archive-icon-container {
            padding: 12px;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .archive-icon-container:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(217, 119, 6, 0.4);
        }

        .archive-icon {
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

        /* STATUS COLORS MATCHING DASHBOARD.PHP */
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

        .export-btn {
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
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            min-width: 160px;
        }

        .export-btn:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
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
            
            .export-btn {
                min-width: 140px;
                padding: 8px 16px;
                font-size: 0.9rem;
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

        .reservation-code {
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

        .date-created {
            font-size: 0.8rem;
            color: #4a5568;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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

        .fas, .far, .fab, .svg-inline--fa, .fa-archive, .fa-sync-alt, .fa-database,
        .fa-search, .fa-history, .fa-download, .fa-clock, .fa-check, .fa-times, 
        .fa-exclamation-triangle, .fa-calendar-alt, .fa-file-invoice, .fa-user, 
        .fa-credit-card, .fa-calculator, .fa-utensils, .fa-history, .fa-receipt, 
        .fa-ban, .fa-eye, .fa-calendar-alt, .fa-check-circle {
            color: inherit !important;
            opacity: 1 !important;
            visibility: visible !important;
            font-style: normal;
            font-weight: 900;
        }
    </style>
</head>
<body>

    <div class="professional-header fade-in">
        <div class="flex items-center space-x-3">
            <div class="archive-icon-container">
                <i class="fas fa-archive archive-icon icon-wrapper"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Reservation Records</h1>
                <p class="text-gray-600 text-sm mt-0.5">Complete history of all client reservations</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <div class="stats-card">
                <div class="flex items-center gap-2">
                    <i class="fas fa-database text-[#d97706] icon-wrapper"></i>
                    <span class="text-gray-600 text-sm">Total Records:</span>
                    <span id="totalRecords" class="text-[#92400e] font-bold text-base">0</span>
                </div>
            </div>
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
                        <option value="all">All Status</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pending">Pending</option>
                        <option value="request_reschedule">Reschedule</option>
                        <option value="request_cancel">Cancel Request</option>
                    </select>
                </div>
                
                <button class="export-btn" onclick="exportReservationData()">
                    <i class="fas fa-download icon-wrapper"></i>
                    Export Data
                </button>
            </div>

            <div class="stats-info">
                <i class="fas fa-history text-[#d97706] text-base icon-wrapper"></i>
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
                        <th class="text-center">Date Created</th>
                        <th class="text-left">Customer</th>
                        <th class="text-center">Reservation Code</th>
                        <th class="text-center">Table</th>
                        <th class="text-center">Schedule Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Total Amount</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="outputTableBody" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="9" class="p-6 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 border-3 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                                <div>
                                    <p class="font-semibold high-contrast-text text-sm">Loading reservation data...</p>
                                    <p class="text-gray-600 text-xs mt-1">Please wait while we fetch the reservation records</p>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        
        const reservationRecordsManager = new ReservationRecordsManager();
        window.reservationRecordsManager = reservationRecordsManager;
    });

    class ReservationRecordsManager {
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
                formData.append('action', 'get_reservation_records');
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
                    this.updateReservationStats(data.totalReservations);
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
                        <td colspan="9" class="p-8 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                                <div>
                                    <p class="font-semibold high-contrast-text text-sm">No reservations found</p>
                                    <p class="text-gray-600 text-xs mt-2">Try adjusting your search or filter criteria</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = reservations.map((reservation, index) => {
                const statusBadge = this.getStatusBadge(reservation.status);
                
                return `
                    <tr class="table-hover-row fade-in">
                        <td class="text-center font-semibold text-sm">${(this.currentPage - 1) * 10 + index + 1}</td>
                        <td class="text-center date-created">${new Date(reservation.created_at).toLocaleDateString()}</td>
                        <td>
                            <div class="customer-info">
                                <span class="font-bold text-[#92400e] block">${reservation.customer_name}</span>
                                <span class="text-gray-600 text-xs">${reservation.customer_email}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="font-bold text-[#92400e] text-sm reservation-code">${reservation.reservation_code || 'N/A'}</span>
                        </td>
                        <td class="text-center font-bold text-sm">${reservation.table_code}</td>
                        <td class="text-center text-sm">${reservation.date_schedule}</td>
                        <td class="text-center time-schedule">${reservation.time_schedule}</td>
                        <td class="text-center amount-text">₱${parseFloat(reservation.grand_total || 0).toFixed(2)}</td>
                        <td class="text-center">
                            ${statusBadge}
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        getStatusBadge(status) {
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
                            onclick="reservationRecordsManager.goToPage(${currentPage - 1})">
                        <i class="fas fa-chevron-left icon-wrapper"></i>
                        <span>Previous</span>
                    </button>
            `;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    paginationHTML += `
                        <button class="page-btn ${currentPage === i ? 'active' : ''}" 
                                onclick="reservationRecordsManager.goToPage(${i})">
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
                            onclick="reservationRecordsManager.goToPage(${currentPage + 1})">
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
        
        updateReservationStats(totalCount) {
            const element = document.getElementById('totalRecords');
            if (element) element.textContent = totalCount;
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
        
        updateLastUpdated() {
            const element = document.getElementById('lastUpdated');
            if (element) {
                element.textContent = new Date().toLocaleTimeString();
            }
        }
    }

    async function exportReservationData() {
        const manager = window.reservationRecordsManager;
        if (!manager) {
            showExportError('Reservation manager not initialized');
            return;
        }
        
        try {
            manager.showSpinner();
            const formData = new FormData();
            formData.append('ajax_request', 'true');
            formData.append('action', 'export_reservation_records');
            formData.append('search', manager.searchTerm);
            formData.append('status', manager.filterStatus);
            
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
                let csvContent = "Reservation Records Export\n\n";
                csvContent += "Reservation Code,Customer Name,Email,Phone,Table,Seats,Schedule Date,Schedule Time,Menu Total,Promo Total,Group Total,Grand Total,Payment Method,Status,Created Date\n";
                
                data.data.forEach(reservation => {
                    csvContent += `"${reservation.reservation_code}","${reservation.customer_name}","${reservation.customer_email}","${reservation.customer_phone}","${reservation.table_code}","${reservation.seats}","${reservation.date_schedule}","${reservation.time_schedule}","₱${parseFloat(reservation.menu_total || 0).toFixed(2)}","₱${parseFloat(reservation.promo_total || 0).toFixed(2)}","₱${parseFloat(reservation.group_total || 0).toFixed(2)}","₱${parseFloat(reservation.grand_total || 0).toFixed(2)}","${reservation.payment_method}","${reservation.status}","${reservation.created_at}"\n`;
                });
                
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                const currentDate = new Date().toISOString().split('T')[0];
                link.setAttribute('href', url);
                link.setAttribute('download', `reservation_records_${currentDate}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Reservation data exported successfully!',
                    icon: 'success',
                    background: '#fff',
                    color: '#2d3748',
                    confirmButtonColor: '#d97706'
                });
            } else {
                showExportError('No data available for export');
            }
        } catch (error) {
            console.error('Export error:', error);
            showExportError('Failed to export data: ' + error.message);
        } finally {
            manager.hideSpinner();
        }
    }

    function showExportError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: message,
            background: '#fff',
            color: '#2d3748',
            confirmButtonColor: '#ef4444'
        });
    }

    setInterval(() => {
        if (window.reservationRecordsManager) {
            window.reservationRecordsManager.ensureIconVisibility();
        }
    }, 2000);
    </script>

<?php 
include "../src/components/admin/footer.php"; 
ob_end_flush();
?>
</body>
</html>