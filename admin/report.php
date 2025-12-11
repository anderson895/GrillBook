<?php
ob_start();

$isAjax = isset($_GET['requestType']);

if (!$isAjax) {
    include "../src/components/admin/header.php";
    // Don't include nav.php since we're building sidebar directly in this file
}

$host = "localhost";
 $username = "u777088444_grillbook";
    $password = "Grillbook123@";
    $database = "u777088444_grillbook";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['requestType'])) {
    ob_clean();
    header('Content-Type: application/json');
    
    $filter = $_GET['filter'] ?? 'all';
    $startDate = $_GET['startDate'] ?? null;
    $endDate = $_GET['endDate'] ?? null;
    
    $query = "SELECT * FROM reservations WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($filter !== 'all') {
        switch($filter) {
            case 'daily':
                $query .= " AND DATE(date_schedule) = CURDATE()";
                break;
            case 'weekly':
                $query .= " AND YEARWEEK(date_schedule) = YEARWEEK(CURDATE())";
                break;
            case 'monthly':
                $query .= " AND MONTH(date_schedule) = MONTH(CURDATE()) AND YEAR(date_schedule) = YEAR(CURDATE())";
                break;
            case 'yearly':
                $query .= " AND YEAR(date_schedule) = YEAR(CURDATE())";
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query .= " AND DATE(date_schedule) BETWEEN ? AND ?";
                    $params[] = $startDate;
                    $params[] = $endDate;
                    $types .= "ss";
                }
                break;
        }
    }
    
    $query .= " ORDER BY date_schedule DESC, time_schedule DESC";
    
    try {
        $stmt = $conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode($reservations);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    
    exit;
}

if (!$isAjax) {
    $user_id = $_SESSION['user_id'] ?? 1;
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $On_Session = $result->fetch_assoc();

    // Updated query to include corkage and service charge
    $query = "SELECT 
                DATE(date_schedule) as date,
                COUNT(*) as reservation_count,
                SUM(grand_total) as daily_revenue,
                SUM(menu_total) as menu_revenue,
                SUM(promo_total) as promo_revenue,
                SUM(group_total) as group_revenue,
                SUM(corkage_fee) as corkage_revenue,
                SUM(service_charge) as service_revenue,
                payment_method,
                status
              FROM reservations 
              WHERE date_schedule >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
              GROUP BY DATE(date_schedule), payment_method, status
              ORDER BY date DESC";
    $result = $conn->query($query);
    $chartData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    $paymentQuery = "SELECT 
                      payment_method,
                      COUNT(*) as count,
                      SUM(grand_total) as total
                    FROM reservations 
                    GROUP BY payment_method
                    ORDER BY count DESC";
    $paymentResult = $conn->query($paymentQuery);
    $paymentData = $paymentResult ? $paymentResult->fetch_all(MYSQLI_ASSOC) : [];

    $monthlyQuery = "SELECT 
                      DATE_FORMAT(date_schedule, '%Y-%m') as month,
                      COUNT(*) as reservation_count,
                      SUM(grand_total) as monthly_revenue,
                      SUM(corkage_fee) as monthly_corkage,
                      SUM(service_charge) as monthly_service
                    FROM reservations 
                    WHERE date_schedule >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(date_schedule, '%Y-%m')
                    ORDER BY month DESC";
    $monthlyResult = $conn->query($monthlyQuery);
    $monthlyData = $monthlyResult ? $monthlyResult->fetch_all(MYSQLI_ASSOC) : [];

    // Updated stats query to include corkage and service charge
    $statsQuery = "SELECT 
                    COUNT(*) as total_reservations,
                    SUM(grand_total) as total_revenue,
                    AVG(grand_total) as avg_revenue,
                    SUM(menu_total) as total_menu,
                    SUM(promo_total) as total_promo,
                    SUM(group_total) as total_group,
                    SUM(corkage_fee) as total_corkage,
                    SUM(service_charge) as total_service
                  FROM reservations";
    $statsResult = $conn->query($statsQuery);
    $stats = $statsResult ? $statsResult->fetch_assoc() : [];
    
    ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - GrillBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* CLEAN WHITE BACKGROUND */
        body {
            background: #f8fafc;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            width: 100%;
            position: relative;
            font-family: 'Arial', sans-serif;
            color: #1a202c;
        }

        /* Remove all scrollbars from body */
        body::-webkit-scrollbar {
            display: none;
        }

        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Ensure full height layout */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.08),
                0 4px 12px rgba(0, 0, 0, 0.03),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.12),
                0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .quick-action-btn {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 2px solid #92400e;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.25);
        }

        .quick-action-btn:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(217, 119, 6, 0.35);
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #e2e8f0;
            color: #2d3748;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 1);
            border-color: #d1d5db;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #d97706, #b45309);
            color: white;
            border-color: #92400e;
        }

        .form-input-enhanced {
            background: white;
            border: 2px solid #e2e8f0;
            color: #1a202c;
            padding: 10px;
            width: 100%;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-input-enhanced:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
            outline: none;
            background: #fffdf6;
        }

        .simple-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .simple-table th {
            background: linear-gradient(135deg, #f8f4e5 0%, #f0e6c3 100%);
            color: #2d3748;
            padding: 12px;
            text-align: left;
            border-bottom: 3px solid #e2e8f0;
            font-weight: 700;
        }

        .simple-table td {
            padding: 12px;
            border-bottom: 2px solid #f7fafc;
            color: #1a202c;
        }

        .simple-table tr:hover {
            background: #fefce8;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-card:hover {
            transform: translateY(-8px);
            border-color: rgba(217, 119, 6, 0.3);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 0 30px rgba(217, 119, 6, 0.1);
        }

        .chart-container {
            background: white;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .bar-chart-container, .pie-chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media print {
            @page {
                size: landscape;
                margin: 0.5cm;
            }
            
            body * {
                visibility: hidden;
            }

            #printArea, #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 10px;
                background: white;
                color: black;
                font-size: 10pt;
            }

            #reportTable {
                width: 100%;
                border-collapse: collapse;
                color: black;
                font-size: 9pt;
            }

            #reportTable th,
            #reportTable td {
                border: 1px solid #000;
                padding: 6px 4px;
                color: black;
                font-size: 9pt;
            }

            #reportTable th {
                background-color: #f0f0f0;
                font-weight: bold;
                text-align: center;
            }

            .print-header {
                text-align: center;
                margin-bottom: 15px;
                color: black;
            }

            .print-header h1 {
                font-size: 20px;
                font-weight: bold;
                margin: 0 0 5px 0;
            }

            .print-header h2 {
                font-size: 16px;
                margin: 0 0 10px 0;
            }

            .print-header p {
                font-size: 11px;
                margin: 2px 0;
            }

            .print-footer {
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #000;
                font-size: 9px;
                color: black;
            }

            .no-print {
                display: none !important;
            }

            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
            }
            
            * {
                color: black !important;
                background-color: white !important;
            }
        }

        *:focus {
            outline: 3px solid rgba(217, 119, 6, 0.3);
            outline-offset: 2px;
        }

        ::selection {
            background: #d97706;
            color: white;
            text-shadow: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

        /* Material icons color fix */
        .material-icons {
            color: inherit !important;
            font-size: 1.25rem !important;
            margin-right: 0.75rem !important;
        }

        /* Ensure all icons are same size */
        .fas, .fa-chart-bar, .fa-sync-alt, .fa-history, .fa-download, 
        .fa-print, .fa-filter, .fa-calendar-day, .fa-calendar-week, 
        .fa-calendar-alt, .fa-calendar, .fa-layer-group, .fa-calendar-check,
        .fa-peso-sign, .fa-chart-pie, .fa-credit-card, .fa-exclamation-circle,
        .fa-inbox, .fa-wine-bottle, .fa-percent {
            color: inherit !important;
            opacity: 1 !important;
            visibility: visible !important;
            font-style: normal;
            font-weight: 900;
        }

        /* Sidebar styling - DARK THEME */
        #sidebar a {
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 0.375rem !important;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            color: #CCCCCC;
        }

        #sidebar a:hover {
            color: #FFD700;
            background: rgba(255, 255, 255, 0.1);
        }

        #sidebar a.active {
            color: #FFD700;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(217, 119, 6, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(217, 119, 6, 0.7);
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex flex-col lg:flex-row">
  <!-- Sidebar - DARK THEME -->
  <aside id="sidebar" class="bg-[#0D0D0D] shadow-lg w-64 lg:w-1/5 xl:w-1/6 p-6 space-y-6 lg:static fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-wrap justify-center items-center space-x-4 p-4 bg-[#1A1A1A] rounded-lg shadow-inner hover:shadow-xl transition-shadow duration-300 max-w-full">
      <img src="../static/logo.jpg" alt="GrillBook" class="w-20 h-20 rounded-full border-2 border-gray-700 shadow-sm transform transition-transform duration-300 hover:scale-105">
      <h1 class="text-base sm:text-lg md:text-xl font-bold text-[#FFD700] tracking-tight text-center">
        Administrator
      </h1>
    </div>

    <nav class="space-y-4 text-left text-[#CCCCCC] overflow-y-auto h-[calc(100vh-120px)]">
      <a href="dashboard.php" class="nav-link flex items-center space-x-3 hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">dashboard</span>
        <span>Dashboard</span>
      </a>

      <button id="toggleDeals" class="w-full flex cursor-pointer items-center justify-between text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <div class="flex items-center space-x-3">
          <span class="material-icons">sell</span>
          <span>Deals</span>
        </div>
        <span id="deals_dropdownIcon" class="material-icons transition-transform duration-300">expand_more</span>
      </button>
      <div id="dealsDropdown" class="ml-8 space-y-2 hidden">
        <a href="group_deals.php" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Group Deals</a>
        <a href="promo_deals.php" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Promo Deals</a>
      </div>

      <button id="toggleReservation" class="w-full cursor-pointer flex items-center justify-between text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <div class="flex items-center space-x-3">
          <span class="material-icons">event</span>
          <span>Reservation</span>
        </div>
        <span id="reserve_dropdownIcon" class="material-icons transition-transform duration-300">expand_more</span>
      </button>
      <div id="reserveDropdown" class="ml-8 space-y-2 hidden">
        <a href="reserve_request.php" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">Reservation Request</a>
        <a href="all_reserved.php" class="nav-link block text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">All</a>
      </div>

      <a href="menu.php" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">local_dining</span>
        <span>Menu</span>
      </a>

      <a href="manage_users.php" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">group</span>
        <span>Users</span>
      </a>

      <a href="backup.php" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">backup</span>
        <span>Backup</span>
      </a>

      <a href="sales_report.php" class="nav-link flex items-center space-x-3 text-[#FFD700] bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">assignment</span>
        <span>Sales Report</span>
      </a>

      <a href="settings.php" class="nav-link flex items-center space-x-3 text-[#CCCCCC] hover:text-[#FFD700] hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">settings</span>
        <span>Settings</span>
      </a>

      <!-- LOGOUT BUTTON - PERFECTLY MATCHED -->
      <a href="../src/logout.php" class="flex items-center space-x-3 text-[#CCCCCC] hover:text-red-500 hover:bg-white/10 px-4 py-2 rounded-md transition-all duration-300">
        <span class="material-icons">logout</span>
        <span>Logout</span>
      </a>
    </nav>
  </aside>

  <div id="overlay" class="fixed inset-0 bg-black opacity-50 hidden lg:hidden z-40"></div>

  <!-- Main Content - WHITE THEME -->
  <main class="flex-1 bg-gray-50 p-8 lg:p-12 overflow-auto h-screen">
    <button id="menuButton" class="lg:hidden text-[#FFD700] bg-white/10 hover:bg-white/20 p-2 rounded-md transition duration-300 mb-4">
      <span class="material-icons">menu</span> 
    </button>

    <!-- Sales Report Content -->
    <div class="min-h-screen">
        <div class="glass-card mb-6 no-print">
            <div class="flex justify-between items-center p-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-br from-[#d97706] to-[#b45309] rounded-lg">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-[#92400e]">SALES REPORT</h2>
                        <p class="text-gray-600">Complete restaurant reservation analysis with corkage & service charge</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="p-3 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300" onclick="location.reload()">
                        <i class="fas fa-sync-alt text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 no-print">
            <div class="stats-card">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Total Reservations</p>
                        <h3 class="text-2xl font-bold text-[#92400e] mt-2" id="totalReservations"><?php echo $stats['total_reservations'] ?? 0; ?></h3>
                    </div>
                    <div class="p-2 bg-[#d97706]/20 rounded">
                        <i class="fas fa-calendar-check text-[#d97706] text-lg"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm">Total Revenue</p>
                        <h3 class="text-2xl font-bold text-[#92400e] mt-2" id="totalRevenue">₱<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="p-2 bg-green-500/20 rounded">
                        <i class="fas fa-peso-sign text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm">Average per Reservation</p>
                        <h3 class="text-2xl font-bold text-[#92400e] mt-2" id="averageRevenue">₱<?php echo number_format($stats['avg_revenue'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="p-2 bg-blue-500/20 rounded">
                        <i class="fas fa-chart-pie text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm">Corkage Revenue</p>
                        <h3 class="text-xl font-bold text-[#92400e] mt-2" id="totalCorkage">₱<?php echo number_format($stats['total_corkage'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="p-2 bg-purple-500/20 rounded">
                        <i class="fas fa-wine-bottle text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 no-print">
            <div class="stats-card">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Service Charge (10%)</p>
                        <h3 class="text-2xl font-bold text-[#92400e] mt-2" id="totalService">₱<?php echo number_format($stats['total_service'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="p-2 bg-red-500/20 rounded">
                        <i class="fas fa-percent text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm">Menu Revenue</p>
                        <h3 class="text-xl font-bold text-[#92400e] mt-2" id="totalMenuRevenue">₱<?php echo number_format($stats['total_menu'] ?? 0, 2); ?></h3>
                    </div>
                    <div class="p-2 bg-yellow-500/20 rounded">
                        <i class="fas fa-utensils text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-600 text-sm">Promo & Group Revenue</p>
                        <h3 class="text-xl font-bold text-[#92400e] mt-2" id="totalPromoGroup">₱<?php echo number_format(($stats['total_promo'] ?? 0) + ($stats['total_group'] ?? 0), 2); ?></h3>
                    </div>
                    <div class="p-2 bg-indigo-500/20 rounded">
                        <i class="fas fa-tags text-indigo-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 mb-6 no-print">
            <div class="flex flex-wrap gap-4 justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h3 class="text-lg font-semibold text-[#92400e]">Time Period</h3>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <div class="flex flex-wrap gap-2">
                        <button class="filter-btn active" data-filter="all">
                            <i class="fas fa-layer-group mr-1"></i> All Time
                        </button>
                        <button class="filter-btn" data-filter="daily">
                            <i class="fas fa-calendar-day mr-1"></i> Today
                        </button>
                        <button class="filter-btn" data-filter="weekly">
                            <i class="fas fa-calendar-week mr-1"></i> This Week
                        </button>
                        <button class="filter-btn" data-filter="monthly">
                            <i class="fas fa-calendar-alt mr-1"></i> This Month
                        </button>
                        <button class="filter-btn" data-filter="yearly">
                            <i class="fas fa-calendar mr-1"></i> This Year
                        </button>
                    </div>
                </div>
                <div class="flex items-center space-x-3 text-gray-600">
                    <i class="fas fa-history text-sm"></i>
                    <span class="text-sm">Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 mb-6 no-print">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <h3 class="text-lg font-semibold text-[#92400e]">Custom Date Range</h3>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex items-center gap-2">
                            <input type="date" id="startDate" class="px-3 py-2 form-input-enhanced" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                            <span class="text-gray-700">to</span>
                            <input type="date" id="endDate" class="px-3 py-2 form-input-enhanced" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <button id="applyDateRange" class="quick-action-btn px-4 py-2 flex items-center gap-2">
                            <i class="fas fa-filter text-xs"></i>
                            Apply Filter
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="refreshData" class="quick-action-btn px-4 py-2 flex items-center gap-2">
                        <i class="fas fa-sync-alt text-xs"></i>
                        Refresh
                    </button>
                    <button id="exportData" class="quick-action-btn px-4 py-2 flex items-center gap-2">
                        <i class="fas fa-download text-xs"></i>
                        Export CSV
                    </button>
                    <button id="printReport" class="quick-action-btn px-4 py-2 flex items-center gap-2">
                        <i class="fas fa-print text-xs"></i>
                        Print Report
                    </button>
                </div>
            </div>
        </div>

        <div id="printArea" class="bg-white p-6 border border-gray-300">
            <div class="hidden print:block print-header">
                <h1 class="text-2xl font-extrabold text-center">GRILLBOOK</h1>
                <h2 class="text-xl text-center mb-2">SALES REPORT</h2>
                <p class="text-center text-sm" id="printDateRange">Date: <?php echo date('F j, Y'); ?></p>
                <p class="text-center text-sm"><strong>Generated by:</strong> <?php echo strtoupper($On_Session['user_fname'] . ' ' . $On_Session['user_lname']); ?></p>
                <hr class="my-4 border-gray-600">
            </div>

            <div class="text-center mb-6 print:hidden">
                <h1 class="text-2xl font-extrabold text-[#92400e]">GRILLBOOK</h1>
                <p class="text-base text-gray-700">SALES REPORT</p>
                <p class="text-base text-gray-700" id="screenDateRange">Date: <?php echo date('F j, Y'); ?></p>
                <hr class="my-4 border-gray-300">
            </div>

            <div class="overflow-x-auto">
                <table class="simple-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>Reservation Code</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Table</th>
                            <th>Seats</th>
                            <th>Date & Time</th>
                            <th>Menu Total</th>
                            <th>Promo Total</th>
                            <th>Group Total</th>
                            <th>Corkage Fee</th>
                            <th>Service Charge (10%)</th>
                            <th>Grand Total</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody id="reportBody">
                        <tr>
                            <td colspan="13" class="p-8 text-center text-gray-600">
                              <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-[#d97706] mr-2"></div>
                              <span>Loading reservation data...</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-[#d97706]/10">
                        <tr>
                            <td colspan="6" class="p-4 text-right"><strong>Overall Sales Total:</strong></td>
                            <td id="totalMenu" class="p-4"><strong>₱0.00</strong></td>
                            <td id="totalPromo" class="p-4"><strong>₱0.00</strong></td>
                            <td id="totalGroup" class="p-4"><strong>₱0.00</strong></td>
                            <td id="totalCorkageTable" class="p-4"><strong>₱0.00</strong></td>
                            <td id="totalServiceTable" class="p-4"><strong>₱0.00</strong></td>
                            <td id="totalGrand" class="p-4"><strong>₱0.00</strong></td>
                            <td class="p-4">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="hidden print:block print-footer mt-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p><strong>Total Reservations:</strong> <span id="printTotalCount">0</span></p>
                        <p><strong>Total Revenue:</strong> <span id="printTotalRevenue">₱0.00</span></p>
                        <p><strong>Corkage Revenue:</strong> <span id="printCorkageRevenue">₱0.00</span></p>
                        <p><strong>Service Charge:</strong> <span id="printServiceRevenue">₱0.00</span></p>
                    </div>
                    <div class="text-right">
                        <p><strong>Report Generated:</strong> <?php echo date('F j, Y, g:i a'); ?></p>
                        <p>GrillBook Restaurant Management System</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>

<script>
// Add this JavaScript to remove any extra scroll
document.addEventListener('DOMContentLoaded', function() {
    // Remove body scroll
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    
    // Initialize the report system
    initializeReportSystem();
    loadInitialData();
    
    // Initialize sidebar functionality
    initializeSidebar();
});

let currentFilter = 'all';

function initializeSidebar() {
    // Toggle dropdowns
    $("#toggleDeals").click(function () {
        $("#dealsDropdown").slideToggle(300);
        const icon = $("#deals_dropdownIcon");
        icon.text(icon.text() === "expand_more" ? "expand_less" : "expand_more");
    });

    $("#toggleReservation").click(function () {
        $("#reserveDropdown").slideToggle(300);
        const icon = $("#reserve_dropdownIcon");
        icon.text(icon.text() === "expand_more" ? "expand_less" : "expand_more");
    });

    const menuButton = document.getElementById('menuButton');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    menuButton.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Mark active link
    const links = document.querySelectorAll('.nav-link');
    const currentPath = window.location.pathname;

    links.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (currentPath.includes(linkHref)) {
            link.classList.add('text-[#FFD700]', 'bg-white/10');

            if (link.closest('#dealsDropdown')) {
                document.getElementById('dealsDropdown').style.display = 'block';
                document.getElementById('deals_dropdownIcon').textContent = 'expand_less';
            }

            if (link.closest('#reserveDropdown')) {
                document.getElementById('reserveDropdown').style.display = 'block';
                document.getElementById('reserve_dropdownIcon').textContent = 'expand_less';
            }
        }
    });
}

function initializeReportSystem() {
    $('.filter-btn').on('click', function() {
        currentFilter = $(this).data('filter');
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        updateDateRangeDisplay(currentFilter);
        loadReports(currentFilter);
    });
    
    $('#applyDateRange').on('click', function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be after end date');
            return;
        }
        
        currentFilter = 'custom';
        $('.filter-btn').removeClass('active');
        updateDateRangeDisplay('custom', startDate, endDate);
        loadReports('custom', startDate, endDate);
    });
    
    $('#refreshData').on('click', function() {
        loadReports(currentFilter);
    });
    
    $('#printReport').on('click', function() {
        updatePrintSummary();
        window.print();
    });
    
    $('#exportData').on('click', exportToCSV);
}

function loadInitialData() {
    loadReports('all');
}

function loadReports(filter = 'all', startDate = null, endDate = null) {
    $('#reportBody').html(`
        <tr>
            <td colspan="13" class="p-8 text-center text-gray-600">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-[#d97706] mr-2"></div>
                <span>Loading reservation data...</span>
            </td>
        </tr>
    `);
    
    const requestData = {
        requestType: "fetch_report",
        filter: filter
    };
    
    if (filter === 'custom' && startDate && endDate) {
        requestData.startDate = startDate;
        requestData.endDate = endDate;
    }
    
    $.ajax({
        url: window.location.href,
        method: "GET",
        data: requestData,
        dataType: "json",
        success: function(data) {
            if (data.error) {
                showErrorMessage(data.error);
                return;
            }
            
            renderTable(data);
            updateStatistics(data);
            
            document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
        },
        error: function(xhr, status, error) {
            console.error("Error loading data:", error);
            $('#reportBody').html(`
                <tr>
                    <td colspan="13" class="p-8 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-3xl mb-4"></i>
                        <span>Error loading report data. Please try again.</span>
                        <button onclick="loadReports('all')" class="quick-action-btn mt-4">Retry</button>
                    </td>
                </tr>
            `);
        }
    });
}

function renderTable(data) {
    let html = '';
    let totalMenu = 0, totalPromo = 0, totalGroup = 0, totalGrand = 0;
    let totalCorkage = 0, totalService = 0;
    let totalCount = data.length;

    if(totalCount > 0){
        data.forEach(res => {
            const date = new Date(res.date_schedule);
            const formattedDate = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
            
            const reserveCode = 'RES' + String(res.id).padStart(4, '0');
            const menuTotal = parseFloat(res.menu_total) || 0;
            const promoTotal = parseFloat(res.promo_total) || 0;
            const groupTotal = parseFloat(res.group_total) || 0;
            const corkageFee = parseFloat(res.corkage_fee) || 0;
            const serviceCharge = parseFloat(res.service_charge) || 0;
            const grandTotal = parseFloat(res.grand_total) || 0;
            
            totalMenu += menuTotal;
            totalPromo += promoTotal;
            totalGroup += groupTotal;
            totalCorkage += corkageFee;
            totalService += serviceCharge;
            totalGrand += grandTotal;
            
            html += `<tr>
                <td class="p-4 font-mono text-gray-800">${reserveCode}</td>
                <td class="p-4 text-gray-800">${res.customer_name || 'Guest User'}</td>
                <td class="p-4 text-gray-800">${res.customer_email || 'N/A'}</td>
                <td class="p-4 text-gray-800 text-center">${res.table_code || 'N/A'}</td>
                <td class="p-4 text-gray-800 text-center">${res.seats || '0'}</td>
                <td class="p-4 text-gray-800">${formattedDate} ${res.time_schedule || '00:00:00'}</td>
                <td class="p-4 text-right text-gray-800">₱${menuTotal.toFixed(2)}</td>
                <td class="p-4 text-right text-gray-800">₱${promoTotal.toFixed(2)}</td>
                <td class="p-4 text-right text-gray-800">₱${groupTotal.toFixed(2)}</td>
                <td class="p-4 text-right text-gray-800">₱${corkageFee.toFixed(2)}</td>
                <td class="p-4 text-right text-gray-800">₱${serviceCharge.toFixed(2)}</td>
                <td class="p-4 font-bold text-[#92400e] text-right">₱${grandTotal.toFixed(2)}</td>
                <td class="p-4 text-center text-gray-800">${(res.payment_method || 'cash').toUpperCase()}</td>
            </tr>`;
        });
    } else {
        html = `<tr>
            <td colspan="13" class="p-8 text-center text-gray-600">
                <i class="fas fa-inbox text-4xl mb-4 text-gray-400"></i>
                <p>No reservation data found for the selected period.</p>
            </td>
        </tr>`;
    }

    $('#reportBody').html(html);
    $('#totalMenu').html(`<strong>₱${totalMenu.toFixed(2)}</strong>`);
    $('#totalPromo').html(`<strong>₱${totalPromo.toFixed(2)}</strong>`);
    $('#totalGroup').html(`<strong>₱${totalGroup.toFixed(2)}</strong>`);
    $('#totalCorkageTable').html(`<strong>₱${totalCorkage.toFixed(2)}</strong>`);
    $('#totalServiceTable').html(`<strong>₱${totalService.toFixed(2)}</strong>`);
    $('#totalGrand').html(`<strong>₱${totalGrand.toFixed(2)}</strong>`);
    
    // Update print summary
    $('#printTotalCount').text(totalCount);
    $('#printTotalRevenue').text(`₱${totalGrand.toFixed(2)}`);
    $('#printCorkageRevenue').text(`₱${totalCorkage.toFixed(2)}`);
    $('#printServiceRevenue').text(`₱${totalService.toFixed(2)}`);
}

function updateStatistics(data) {
    const totalCount = data.length;
    let totalRevenue = 0;
    let totalCorkage = 0;
    let totalService = 0;
    let totalMenu = 0;
    let totalPromo = 0;
    let totalGroup = 0;
    const paymentMethods = {};
    
    data.forEach(res => {
        totalRevenue += parseFloat(res.grand_total) || 0;
        totalCorkage += parseFloat(res.corkage_fee) || 0;
        totalService += parseFloat(res.service_charge) || 0;
        totalMenu += parseFloat(res.menu_total) || 0;
        totalPromo += parseFloat(res.promo_total) || 0;
        totalGroup += parseFloat(res.group_total) || 0;
        
        const paymentMethod = res.payment_method || 'cash';
        paymentMethods[paymentMethod] = (paymentMethods[paymentMethod] || 0) + 1;
    });
    
    const averageRevenue = totalCount > 0 ? totalRevenue / totalCount : 0;
    let topPaymentMethod = '-';
    let maxCount = 0;
    
    for (const [method, count] of Object.entries(paymentMethods)) {
        if (count > maxCount) {
            maxCount = count;
            topPaymentMethod = method.toUpperCase();
        }
    }
    
    $('#totalReservations').text(totalCount);
    $('#totalRevenue').html(`₱${totalRevenue.toFixed(2)}`);
    $('#averageRevenue').html(`₱${averageRevenue.toFixed(2)}`);
    $('#totalCorkage').html(`₱${totalCorkage.toFixed(2)}`);
    $('#totalService').html(`₱${totalService.toFixed(2)}`);
    $('#totalMenuRevenue').html(`₱${totalMenu.toFixed(2)}`);
    $('#totalPromoGroup').html(`₱${(totalPromo + totalGroup).toFixed(2)}`);
    $('#topPaymentMethod').text(topPaymentMethod);
}

function updateDateRangeDisplay(filter, startDate = null, endDate = null) {
    let displayText = '';
    
    switch(filter) {
        case 'daily': displayText = 'Today'; break;
        case 'weekly': displayText = 'This Week'; break;
        case 'monthly': displayText = 'This Month'; break;
        case 'yearly': displayText = 'This Year'; break;
        case 'custom':
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                displayText = `${start.toLocaleDateString()} - ${end.toLocaleDateString()}`;
            } else {
                displayText = 'Custom Range';
            }
            break;
        default: displayText = 'All Time';
    }
    
    $('#printDateRange').text(`Date Range: ${displayText}`);
    $('#screenDateRange').text(`Date Range: ${displayText}`);
}

function updatePrintSummary() {
    const totalReservations = $('#totalReservations').text();
    const totalRevenue = $('#totalRevenue').text();
    const totalCorkage = $('#totalCorkage').text();
    const totalService = $('#totalService').text();
    
    $('#printTotalCount').text(totalReservations);
    $('#printTotalRevenue').text(totalRevenue);
    $('#printCorkageRevenue').text(totalCorkage);
    $('#printServiceRevenue').text(totalService);
}

function showErrorMessage(error) {
    $('#reportBody').html(`
        <tr>
            <td colspan="13" class="p-8 text-center text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-4"></i>
                <span>Error: ${error}</span>
                <button onclick="loadReports('all')" class="quick-action-btn mt-4">Retry</button>
            </td>
        </tr>
    `);
}

function exportToCSV() {
    const table = document.getElementById('reportTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s)/gm, " ");
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(","));        
    }

    const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `sales_report_with_fees_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert('CSV file downloaded successfully!');
}
</script>
</body>
</html>
<?php
}
?>