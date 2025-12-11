<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_request'])) {
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
                        $whereConditions[] = "(customer_name LIKE ? OR customer_email LIKE ? OR table_code LIKE ? OR id LIKE ?)";
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
                        'reservations' => $reservations,
                        'totalPages' => $totalPages,
                        'currentPage' => $page,
                        'totalReservations' => $totalReservations
                    ];
                    break;
                    
                case 'get_dashboard_stats':
                    $today_start = date('Y-m-d 00:00:00');
                    $today_end = date('Y-m-d 23:59:59');
                    
                    $week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
                    $week_end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                    
                    $month_start = date('Y-m-01 00:00:00');
                    $month_end = date('Y-m-t 23:59:59');
                    
                    $year_start = date('Y-01-01 00:00:00');
                    $year_end = date('Y-12-31 23:59:59');
                    
                    $total_query = "SELECT COUNT(*) as total FROM reservations";
                    $total_result = $conn->query($total_query)->fetch_assoc();
                    
                    $today_query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                        COALESCE(SUM(CASE WHEN status = 'confirmed' THEN grand_total ELSE 0 END), 0) as revenue
                    FROM reservations 
                    WHERE created_at BETWEEN ? AND ?";
                    
                    $stmt = $conn->prepare($today_query);
                    $stmt->bind_param('ss', $today_start, $today_end);
                    $stmt->execute();
                    $today_stats = $stmt->get_result()->fetch_assoc();
                    
                    $stmt = $conn->prepare($today_query);
                    $stmt->bind_param('ss', $week_start, $week_end);
                    $stmt->execute();
                    $weekly_stats = $stmt->get_result()->fetch_assoc();
                    
                    $stmt = $conn->prepare($today_query);
                    $stmt->bind_param('ss', $month_start, $month_end);
                    $stmt->execute();
                    $monthly_stats = $stmt->get_result()->fetch_assoc();
                    
                    $stmt = $conn->prepare($today_query);
                    $stmt->bind_param('ss', $year_start, $year_end);
                    $stmt->execute();
                    $yearly_stats = $stmt->get_result()->fetch_assoc();
                    
                    $response = [
                        'success' => true,
                        'stats' => [
                            'total' => $total_result['total'] ?? 0,
                            'today' => $today_stats ?? ['total' => 0, 'revenue' => 0],
                            'weekly' => $weekly_stats ?? ['total' => 0, 'revenue' => 0],
                            'monthly' => $monthly_stats ?? ['total' => 0, 'revenue' => 0],
                            'yearly' => $yearly_stats ?? ['total' => 0, 'revenue' => 0]
                        ]
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
                            $response = ['success' => true, 'data' => $result->fetch_assoc()];
                        }
                    } else {
                        $response = ['success' => false, 'message' => 'Reservation ID required'];
                    }
                    break;
                    
                case 'update_status':
                    if (isset($_POST['id']) && isset($_POST['status'])) {
                        $query = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('si', $_POST['status'], $_POST['id']);
                        
                        if ($stmt->execute()) {
                            $response = [
                                'success' => true,
                                'message' => 'Status updated'
                            ];
                        } else {
                            $response = [
                                'success' => false,
                                'message' => 'Failed to update status'
                            ];
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
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

include "../src/components/admin/header.php";
include "../src/components/admin/nav.php";
?>

<style>
body {
    background: #ffffff;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    width: 100%;
    position: relative;
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

.enhanced-table {
    min-height: auto;
}

.main-content {
    width: calc(100% - 250px);
    margin-left: 250px;
    min-height: 100vh;
    padding: 20px;
    box-sizing: border-box;
    position: relative;
}

body {
    font-family: 'Arial', 'Helvetica', sans-serif;
    line-height: 1.6;
    color: #000000;
}

.high-contrast-text {
    color: #000000;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-item {
    background: rgba(255, 255, 255, 0.95);
    padding: 1.5rem;
    border-radius: 16px;
    border: 2px solid #e2e8f0;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
}

.stat-number {
    font-size: 2.2rem;
    font-weight: bold;
    color: #d97706;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    color: #000000;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

.stat-subtext {
    font-size: 0.9rem;
    color: #000000;
    margin-top: 0.5rem;
    font-weight: 500;
}

.revenue-text {
    color: #000000;
    font-weight: 700;
    font-size: 1rem;
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
    font-size: 1.1rem;
    color: #000000;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8),
        inset 0 -1px 0 rgba(0, 0, 0, 0.1);
}

.glass-search-input::placeholder {
    color: #000000;
    font-weight: 500;
    opacity: 0.7;
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
    font-size: 1.6rem;
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
    font-size: 0.9rem;
    border-width: 1px;
    min-width: 100px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

.status-pending {
    background: #fef3c7;
    color: #000000;
    border-color: #f59e0b;
}

.status-confirmed {
    background: #d1fae5;
    color: #000000;
    border-color: #10b981;
}

.status-cancelled {
    background: #fee2e2;
    color: #000000;
    border-color: #ef4444;
}

.status-request-cancel {
    background: #fed7aa;
    color: #000000;
    border-color: #ea580c;
}

.status-request-reschedule {
    background: #e9d5ff;
    color: #000000;
    border-color: #9333ea;
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

.enhanced-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.1);
    width: 100%;
}

.enhanced-table th {
    background: linear-gradient(135deg, #f8f4e5 0%, #f0e6c3 100%);
    color: #000000;
    font-size: 1rem;
    font-weight: 700;
    padding: 16px 12px;
    border-bottom: 2px solid #e2e8f0;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.enhanced-table td {
    padding: 14px 12px;
    font-size: 0.95rem;
    border-bottom: 1px solid #f7fafc;
    vertical-align: middle;
    text-align: center;
    color: #000000;
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
    color: #000000;
    padding: 14px 18px;
    border-radius: 10px;
    transition: all 0.3s ease;
    width: 100%;
    font-size: 1.1rem;
}

.form-input-enhanced:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2);
    outline: none;
}

.form-input-enhanced::placeholder {
    color: #000000;
    opacity: 0.7;
}

:root {
    --primary-gold: #d97706;
    --dark-gold: #b45309;
    --light-gold: #fef3c7;
    --text-dark: #000000;
    --text-muted: #000000;
    --border-light: #e2e8f0;
    --background-light: #f8f4e5;
}

@media (max-width: 768px) {
    .enhanced-table th,
    .enhanced-table td {
        padding: 12px 8px;
        font-size: 0.9rem;
    }

    .glass-search-container {
        max-width: 100%;
    }
    
    .stats-container {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .stat-number {
        font-size: 1.8rem;
    }
    
    .stat-label {
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

.action-btn {
    padding: 9px 15px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px solid;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none;
    cursor: pointer;
    color: white;
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
    color: #000000;
    background: #fef3c7;
    padding: 4px 9px;
    border-radius: 4px;
    border: 1px solid #f59e0b;
    font-size: 0.9rem;
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
    padding: 9px 15px;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 0.95rem;
    color: #000000;
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
    color: #000000;
    font-size: 0.95rem;
}

.payment-method {
    font-weight: 600;
    font-size: 0.9rem;
    padding: 5px 11px;
    border-radius: 6px;
}

.payment-cash {
    background: #d1fae5;
    color: #000000;
}

.payment-online {
    background: #dbeafe;
    color: #000000;
}

.customer-info {
    font-size: 0.9rem;
    line-height: 1.3;
}

.time-schedule {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    font-size: 0.95rem;
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
    padding: 11px 22px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
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

.table-container {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
}

.responsive-table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 12px;
}

.responsive-table-wrapper::-webkit-scrollbar {
    height: 8px;
}

.responsive-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 4px;
}

.responsive-table-wrapper::-webkit-scrollbar-thumb {
    background: linear-gradient(to right, #d97706, #b45309);
    border-radius: 4px;
}

.responsive-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to right, #b45309, #92400e);
}
</style>

<div class="professional-header fade-in">
    <div class="flex items-center space-x-3">
        <div class="calendar-icon-container">
            <i class="fas fa-calendar-check calendar-icon icon-wrapper"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Reservation Management</h1>
            <p class="text-black text-base mt-0.5">Review and manage restaurant reservation requests</p>
        </div>
    </div>
    <div class="flex items-center space-x-3">
        <button class="p-3 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-md rounded-lg" 
                onclick="location.reload()" 
                title="Refresh Data">
            <i class="fas fa-sync-alt text-lg icon-wrapper"></i>
        </button>
    </div>
</div>

<div class="stats-container fade-in">
    <div class="stat-item">
        <div class="stat-number" id="totalReservations">0</div>
        <div class="stat-label">Total Reservations</div>
        <div class="stat-subtext">All Time</div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="todayReservations">0</div>
        <div class="stat-label">Today</div>
        <div class="stat-subtext" id="todayRevenue">Revenue: ₱0.00</div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="weeklyReservations">0</div>
        <div class="stat-label">This Week</div>
        <div class="stat-subtext" id="weeklyRevenue">Revenue: ₱0.00</div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="monthlyReservations">0</div>
        <div class="stat-label">This Month</div>
        <div class="stat-subtext" id="monthlyRevenue">Revenue: ₱0.00</div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="yearlyReservations">0</div>
        <div class="stat-label">This Year</div>
        <div class="stat-subtext" id="yearlyRevenue">Revenue: ₱0.00</div>
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
                    class="w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced text-base"
                >
                    <option value="all">All Reservations</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Approved</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="request_reschedule">Reschedule</option>
                </select>
            </div>
        </div>

        <div class="stats-info">
            <i class="fas fa-chart-line text-[#d97706] text-lg icon-wrapper"></i>
            <span class="text-black text-base">Updated: <span id="lastUpdated" class="font-semibold text-[#92400e]">Just now</span></span>
        </div>
    </div>
</div>

<div class="glass-card rounded-xl p-4 fade-in">
    <div class="responsive-table-wrapper">
        <table class="w-full enhanced-table">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Table</th>
                    <th class="text-center">Customer</th>
                    <th class="text-center">Seats</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Time</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Payment</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody id="outputTableBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="9" class="p-8 text-center text-black">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <div class="w-14 h-14 border-3 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                            <div>
                                <p class="font-semibold high-contrast-text text-base">Loading reservation data...</p>
                                <p class="text-black text-sm mt-1">Please wait while we fetch the latest reservations</p>
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
        <div class="w-14 h-14 border-3 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
        <div class="text-center">
            <p class="font-semibold high-contrast-text text-base">Processing Request</p>
            <p class="text-black text-sm mt-1">Please wait while we process your action...</p>
        </div>
    </div>
</div>

<div id="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop hidden">
    <div class="enhanced-modal w-full max-w-4xl mx-6 p-6 text-black relative max-h-[85vh] overflow-y-auto slide-in">
        <button id="closeModal" class="absolute top-4 right-4 text-black hover:text-gray-800 text-xl font-bold transition focus:outline-none focus:ring-2 focus:ring-[#d97706] rounded-full p-1">
            <i class="fas fa-times icon-wrapper"></i>
        </button>

        <div class="flex items-center space-x-2 mb-4">
            <div class="p-2 bg-gradient-to-r from-[#d97706] to-[#b45309] rounded-lg">
                <i class="fas fa-file-invoice text-white text-lg icon-wrapper"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-[#92400e] high-contrast-text">Reservation Details</h2>
                <p class="text-black text-sm">Complete reservation information and management</p>
            </div>
        </div>

        <hr class="border-gray-300 mb-4">

        <div id="modalContent" class="space-y-4 text-base">
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
        <button id="close_modal" class="absolute top-3 right-3 text-black hover:text-gray-800 text-xl font-bold transition focus:outline-none focus:ring-2 focus:ring-[#d97706] rounded-full p-1">
            <i class="fas fa-times icon-wrapper"></i>
        </button>
        <div class="text-center mb-3">
            <h3 class="text-lg font-bold text-[#92400e] high-contrast-text">Payment Receipt</h3>
            <p class="text-black text-sm">Proof of payment submitted by customer</p>
        </div>
        <img id="modal_img" src="" alt="Payment Receipt" class="w-full h-auto rounded-md border border-gray-300 shadow-inner" />
    </div>
</div>

<?php
include "../src/components/admin/footer.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        this.loadDashboardStats();
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
    
    async loadDashboardStats() {
        try {
            const formData = new FormData();
            formData.append('ajax_request', 'true');
            formData.append('action', 'get_dashboard_stats');
            
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success && data.stats) {
                this.updateDashboardStats(data.stats);
            } else {
                console.error('Failed to load dashboard stats:', data);
                this.updateDashboardStats({
                    total: 0,
                    today: {total: 0, revenue: 0},
                    weekly: {total: 0, revenue: 0},
                    monthly: {total: 0, revenue: 0},
                    yearly: {total: 0, revenue: 0}
                });
            }
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
            this.updateDashboardStats({
                total: 0,
                today: {total: 0, revenue: 0},
                weekly: {total: 0, revenue: 0},
                monthly: {total: 0, revenue: 0},
                yearly: {total: 0, revenue: 0}
            });
        }
    }
    
    updateDashboardStats(stats) {
        document.getElementById('totalReservations').textContent = stats.total || 0;
        
        const today = stats.today || {total: 0, revenue: 0};
        document.getElementById('todayReservations').textContent = today.total || 0;
        document.getElementById('todayRevenue').textContent = `Revenue: ₱${parseFloat(today.revenue || 0).toFixed(2)}`;
        
        const weekly = stats.weekly || {total: 0, revenue: 0};
        document.getElementById('weeklyReservations').textContent = weekly.total || 0;
        document.getElementById('weeklyRevenue').textContent = `Revenue: ₱${parseFloat(weekly.revenue || 0).toFixed(2)}`;
        
        const monthly = stats.monthly || {total: 0, revenue: 0};
        document.getElementById('monthlyReservations').textContent = monthly.total || 0;
        document.getElementById('monthlyRevenue').textContent = `Revenue: ₱${parseFloat(monthly.revenue || 0).toFixed(2)}`;
        
        const yearly = stats.yearly || {total: 0, revenue: 0};
        document.getElementById('yearlyReservations').textContent = yearly.total || 0;
        document.getElementById('yearlyRevenue').textContent = `Revenue: ₱${parseFloat(yearly.revenue || 0).toFixed(2)}`;
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
            
            const data = await response.json();
            
            if (data.reservations) {
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
                    <td colspan="9" class="p-8 text-center text-black">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                            <div>
                                <p class="font-semibold high-contrast-text text-base">No reservations found</p>
                                <p class="text-black text-sm mt-1">Try adjusting your search or filter criteria</p>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = reservations.map((reservation, index) => `
            <tr class="table-hover-row fade-in" data-id="${reservation.id}">
                <td class="text-center font-semibold text-base">${(this.currentPage - 1) * 10 + index + 1}</td>
                <td class="text-center">
                    <span class="font-bold text-[#92400e] text-base">${reservation.table_code}</span>
                </td>
                <td class="text-center">
                    <div class="customer-info">
                        <span class="font-semibold block">${reservation.customer_name}</span>
                        <span class="text-black text-sm">${reservation.customer_email}</span>
                    </div>
                </td>
                <td class="text-center font-bold text-base">${reservation.seats}</td>
                <td class="text-center text-base">${reservation.date_schedule}</td>
                <td class="text-center time-schedule">${reservation.time_schedule}</td>
                <td class="text-center amount-text">₱${parseFloat(reservation.grand_total || 0).toFixed(2)}</td>
                <td class="text-center">
                    <span class="payment-method ${reservation.payment_method === 'cash' ? 'payment-cash' : 'payment-online'}">
                        ${reservation.payment_method || 'N/A'}
                    </span>
                </td>
                <td class="text-center">
                    ${this.getStatusBadge(reservation.status)}
                </td>
            </tr>
        `).join('');
    }
    
    getStatusBadge(status) {
        const statusConfig = {
            'pending': { class: 'status-pending', text: 'Pending', icon: 'fa-clock' },
            'confirmed': { class: 'status-confirmed', text: 'Approved', icon: 'fa-check' },
            'cancelled': { class: 'status-cancelled', text: 'Cancelled', icon: 'fa-times' },
            'request_cancel': { class: 'status-request-cancel', text: 'Cancel Req', icon: 'fa-exclamation-triangle' },
            'request_reschedule': { class: 'status-request-reschedule', text: 'Reschedule', icon: 'fa-calendar-alt' }
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
                <div class="text-black text-base">
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
            <div class="text-black text-base ml-3">
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
            
            const data = await response.json();
            
            if (data.success && data.data) {
                this.showReservationDetails(data.data);
            } else {
                this.showError(data.message || 'Failed to load reservation details');
            }
        } catch (error) {
            console.error('Error loading reservation details:', error);
            this.showError('Failed to load reservation details');
        } finally {
            this.hideSpinner();
        }
    }
    
    showReservationDetails(reservation) {
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
        
        modalContent.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="glass-card p-4">
                    <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-lg">
                        <i class="fas fa-user mr-2 icon-wrapper"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-2 text-base">
                        <div><strong>Name:</strong> ${reservation.customer_name}</div>
                        <div><strong>Email:</strong> ${reservation.customer_email}</div>
                        <div><strong>Phone:</strong> ${reservation.customer_phone}</div>
                    </div>
                </div>
                
                <div class="glass-card p-4">
                    <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-lg">
                        <i class="fas fa-calendar-alt mr-2 icon-wrapper"></i>
                        Reservation Details
                    </h3>
                    <div class="space-y-2 text-base">
                        <div><strong>Reservation ID:</strong> <span class="reference-number">${reservation.id}</span></div>
                        <div><strong>Table:</strong> ${reservation.table_code}</div>
                        <div><strong>Seats:</strong> ${reservation.seats}</div>
                        <div><strong>Date:</strong> ${reservation.date_schedule}</div>
                        <div><strong>Time:</strong> ${reservation.time_schedule}</div>
                    </div>
                </div>
                
                <div class="glass-card p-4">
                    <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-lg">
                        <i class="fas fa-credit-card mr-2 icon-wrapper"></i>
                        Payment Information
                    </h3>
                    <div class="space-y-2 text-base">
                        <div><strong>Method:</strong> ${reservation.payment_method}</div>
                        <div><strong>Type:</strong> ${reservation.payment_type}</div>
                        <div><strong>Amount:</strong> ₱${parseFloat(reservation.amount_to_pay || 0).toFixed(2)}</div>
                        ${reservation.payment_proof ? `
                        <div>
                            <strong>Proof:</strong> 
                            <button class="text-[#d97706] underline ml-1 text-base" 
                                    onclick="reservationManager.viewPaymentProof('${reservation.payment_proof}')">
                                View Receipt
                            </button>
                        </div>
                        ` : '<div><strong>Proof:</strong> None</div>'}
                    </div>
                </div>
                
                <div class="glass-card p-4">
                    <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-lg">
                        <i class="fas fa-calculator mr-2 icon-wrapper"></i>
                        Financial Breakdown
                    </h3>
                    <div class="space-y-2 text-base">
                        <div><strong>Menu Total:</strong> ₱${parseFloat(reservation.menu_total || 0).toFixed(2)}</div>
                        <div><strong>Promo Total:</strong> ₱${parseFloat(reservation.promo_total || 0).toFixed(2)}</div>
                        <div><strong>Group Total:</strong> ₱${parseFloat(reservation.group_total || 0).toFixed(2)}</div>
                        <div><strong>Corkage Fee:</strong> ₱${parseFloat(reservation.corkage_fee || 0).toFixed(2)}</div>
                        <div class="border-t pt-2 font-bold text-lg">
                            <strong>Grand Total:</strong> ₱${parseFloat(reservation.grand_total || 0).toFixed(2)}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-4 mt-4">
                <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-lg">
                    <i class="fas fa-utensils mr-2 icon-wrapper"></i>
                    Selected Items
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    ${Object.keys(selectedMenus).length > 0 ? `
                    <div>
                        <h4 class="font-semibold mb-2 text-base">Menus</h4>
                        <ul class="list-disc list-inside space-y-1 text-base">
                            ${Object.values(selectedMenus).map(menu => 
                                `<li>${menu.name || menu.id || 'Menu Item'} - ₱${menu.price || 0} x ${menu.quantity || 1}</li>`
                            ).join('')}
                        </ul>
                    </div>
                    ` : '<div><h4 class="font-semibold mb-2 text-base">Menus</h4><p class="text-black text-base">No menus</p></div>'}
                    
                    ${Object.keys(selectedPromos).length > 0 ? `
                    <div>
                        <h4 class="font-semibold mb-2 text-base">Promos</h4>
                        <ul class="list-disc list-inside space-y-1 text-base">
                            ${Object.values(selectedPromos).map(promo => 
                                `<li>${promo.name || promo.id || 'Promo Item'} - ₱${promo.price || 0} x ${promo.quantity || 1}</li>`
                            ).join('')}
                        </ul>
                    </div>
                    ` : '<div><h4 class="font-semibold mb-2 text-base">Promos</h4><p class="text-black text-base">No promos</p></div>'}
                    
                    ${Object.keys(selectedGroups).length > 0 ? `
                    <div>
                        <h4 class="font-semibold mb-2 text-base">Groups</h4>
                        <ul class="list-disc list-inside space-y-1 text-base">
                            ${Object.values(selectedGroups).map(group => 
                                `<li>${group.name || group.id || 'Group Item'} - ₱${group.price || 0} x ${group.quantity || 1}</li>`
                            ).join('')}
                        </ul>
                    </div>
                    ` : '<div><h4 class="font-semibold mb-2 text-base">Groups</h4><p class="text-black text-base">No groups</p></div>'}
                </div>
            </div>
            
            <div class="glass-card p-4 mt-4">
                <h3 class="font-bold text-[#92400e] mb-3 flex items-center text-lg">
                    <i class="fas fa-history mr-2 icon-wrapper"></i>
                    Timeline
                </h3>
                <div class="space-y-2 text-base">
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
        
        approveBtn.style.display = 'none';
        declineBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        rescheduleBtn.style.display = 'none';
        
        if (currentStatus === 'pending') {
            approveBtn.style.display = 'flex';
            declineBtn.style.display = 'flex';
            cancelBtn.style.display = 'flex';
            rescheduleBtn.style.display = 'flex';
        } else if (currentStatus === 'request_reschedule') {
            approveBtn.style.display = 'flex';
            declineBtn.style.display = 'flex';
        } else if (currentStatus === 'request_cancel') {
            approveBtn.style.display = 'flex';
            declineBtn.style.display = 'flex';
        } else if (currentStatus === 'confirmed') {
            cancelBtn.style.display = 'flex';
            rescheduleBtn.style.display = 'flex';
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
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.hideModal('detailsModal');
                        this.loadReservations();
                        this.loadDashboardStats();
                    } else {
                        this.showError(result.message);
                    }
                } catch (error) {
                    console.error('Error updating reservation:', error);
                    this.showError('Failed to update reservation');
                } finally {
                    this.hideSpinner();
                }
            }
        );
    }
    
    viewPaymentProof(imagePath) {
        const modalImg = document.getElementById('modal_img');
        modalImg.src = `../${imagePath}`;
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
        alert(message);
    }
    
    showProfessionalConfirm(message, confirmCallback) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'question',
            background: '#fff',
            color: '#000000',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#6b7280',
            customClass: {
                popup: 'enhanced-modal'
            }
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

const reservationManager = new ReservationManager();
window.reservationManager = reservationManager;

setInterval(() => {
    reservationManager.loadDashboardStats();
}, 30000);

setInterval(() => {
    reservationManager.loadReservations();
}, 60000);

setInterval(() => {
    reservationManager.ensureIconVisibility();
}, 2000);
</script>