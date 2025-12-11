<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "grillbook";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create upload directory if it doesn't exist
$upload_dir = __DIR__ . "/../static/upload/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['requestType'])) {
    if ($_GET['requestType'] === 'fetch_all_promos') {
        $sql = "SELECT * FROM promo_deals ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        $promos = [];
        while($row = $result->fetch_assoc()) {
            $promos[] = $row;
        }
        
        echo json_encode([
            'status' => 200,
            'message' => 'Promo deals fetched successfully',
            'data' => $promos
        ]);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requestType'])) {
    $requestType = $_POST['requestType'];
    
    if ($requestType === 'AddPromo') {
        $promo_name = $_POST['entryName'] ?? '';
        $promo_description = $_POST['entryDescription'] ?? '';
        $end_date = $_POST['entryExpiration'] ?? '';
        $start_date = date('Y-m-d');
        
        $promo_image = '';
        if (isset($_FILES['entryImage']) && $_FILES['entryImage']['error'] === 0) {
            $target_dir = $upload_dir;
            $imageFileType = strtolower(pathinfo($_FILES["entryImage"]["name"], PATHINFO_EXTENSION));
            $promo_image = "promo_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $promo_image;
            
            if ($_FILES["entryImage"]["size"] > 5000000) {
                echo json_encode(['status' => 400, 'message' => 'File is too large. Maximum size is 5MB']);
                exit();
            }
            
            if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo json_encode(['status' => 400, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed']);
                exit();
            }
            
            if (!move_uploaded_file($_FILES["entryImage"]["tmp_name"], $target_file)) {
                echo json_encode(['status' => 400, 'message' => 'Error uploading file']);
                exit();
            }
        }
        
        $current_date = date('Y-m-d');
        $status = 'upcoming';
        if ($current_date >= $start_date && $current_date <= $end_date) {
            $status = 'active';
        } elseif ($current_date > $end_date) {
            $status = 'expired';
        }
        
        $stmt = $conn->prepare("INSERT INTO promo_deals (promo_name, promo_description, promo_image, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $promo_name, $promo_description, $promo_image, $start_date, $end_date, $status);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 200,
                'message' => 'Promo deal added successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Error adding promo deal: ' . $conn->error
            ]);
        }
        exit();
    }
    
    if ($requestType === 'UpdatePromo') {
        $promo_id = $_POST['entryId'] ?? 0;
        $promo_name = $_POST['entryName'] ?? '';
        $promo_description = $_POST['entryDescription'] ?? '';
        $end_date = $_POST['entryExpiration'] ?? '';
        
        $stmt = $conn->prepare("SELECT promo_image FROM promo_deals WHERE promo_id = ?");
        $stmt->bind_param("i", $promo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_promo = $result->fetch_assoc();
        $current_image = $current_promo['promo_image'] ?? '';
        
        $promo_image = $current_image;
        if (isset($_FILES['entryImage']) && $_FILES['entryImage']['error'] === 0) {
            $target_dir = $upload_dir;
            $imageFileType = strtolower(pathinfo($_FILES["entryImage"]["name"], PATHINFO_EXTENSION));
            $promo_image = "promo_" . time() . "." . $imageFileType;
            $target_file = $target_dir . $promo_image;
            
            if ($_FILES["entryImage"]["size"] > 5000000) {
                echo json_encode(['status' => 400, 'message' => 'File is too large. Maximum size is 5MB']);
                exit();
            }
            
            if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo json_encode(['status' => 400, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed']);
                exit();
            }
            
            if (!move_uploaded_file($_FILES["entryImage"]["tmp_name"], $target_file)) {
                echo json_encode(['status' => 400, 'message' => 'Error uploading file']);
                exit();
            }
            
            if ($current_image && $current_image !== $promo_image) {
                $old_file = $target_dir . $current_image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }
        
        $stmt = $conn->prepare("SELECT start_date FROM promo_deals WHERE promo_id = ?");
        $stmt->bind_param("i", $promo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $promo = $result->fetch_assoc();
        $start_date = $promo['start_date'] ?? date('Y-m-d');
        
        $current_date = date('Y-m-d');
        $status = 'upcoming';
        if ($current_date >= $start_date && $current_date <= $end_date) {
            $status = 'active';
        } elseif ($current_date > $end_date) {
            $status = 'expired';
        }
        
        $stmt = $conn->prepare("UPDATE promo_deals SET promo_name = ?, promo_description = ?, promo_image = ?, end_date = ?, status = ? WHERE promo_id = ?");
        $stmt->bind_param("sssssi", $promo_name, $promo_description, $promo_image, $end_date, $status, $promo_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 200,
                'message' => 'Promo deal updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Error updating promo deal: ' . $conn->error
            ]);
        }
        exit();
    }
    
    if ($requestType === 'removePromo') {
        $promo_id = $_POST['promo_id'] ?? 0;
        
        $stmt = $conn->prepare("SELECT promo_image FROM promo_deals WHERE promo_id = ?");
        $stmt->bind_param("i", $promo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $promo = $result->fetch_assoc();
        
        $stmt = $conn->prepare("DELETE FROM promo_deals WHERE promo_id = ?");
        $stmt->bind_param("i", $promo_id);
        
        if ($stmt->execute()) {
            if ($promo && isset($promo['promo_image']) && $promo['promo_image']) {
                $image_path = $upload_dir . $promo['promo_image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            echo json_encode([
                'status' => 200,
                'message' => 'Promo deal removed successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Error removing promo deal: ' . $conn->error
            ]);
        }
        exit();
    }
}

if (isset($_GET['export'])) {
    $export_type = $_GET['export'];
    
    $result = $conn->query("SELECT * FROM promo_deals ORDER BY promo_id");
    $promo_data = [];
    while($row = $result->fetch_assoc()) {
        $promo_data[] = $row;
    }
    
    if ($export_type === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=promo_deals_export_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Description', 'Image', 'Start Date', 'End Date', 'Status', 'Created At']);
        
        foreach ($promo_data as $promo) {
            fputcsv($output, [
                $promo['promo_id'],
                $promo['promo_name'],
                $promo['promo_description'],
                $promo['promo_image'],
                $promo['start_date'],
                $promo['end_date'],
                $promo['status'],
                $promo['created_at']
            ]);
        }
        
        fclose($output);
        exit();
    } elseif ($export_type === 'json') {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=promo_deals_export_' . date('Y-m-d') . '.json');
        
        echo json_encode($promo_data, JSON_PRETTY_PRINT);
        exit();
    }
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

.xl-text {
    font-size: 1.125rem;
    line-height: 1.6;
}

.xxl-text {
    font-size: 1.25rem;
    line-height: 1.5;
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 
        0 10px 25px -5px rgba(0, 0, 0, 0.1),
        0 4px 6px -2px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.table-container {
    max-height: calc(100vh - 400px);
    overflow-y: auto;
    overflow-x: hidden;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #d97706, #b45309);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #b45309, #92400e);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse-gold {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(217, 119, 6, 0);
    }
}

.pulse-dot {
    animation: pulse-gold 2s infinite;
}

.quick-action-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    border: 3px solid;
    border-color: #92400e;
    color: white;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
    padding: 12px 24px;
    font-size: 1rem;
    min-width: 180px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
}

.table-row-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.table-row-hover:hover {
    background: #fefce8;
    transform: translateX(8px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.interactive-modal {
    background: white;
    backdrop-filter: blur(20px);
    border: 2px solid #e2e8f0;
    animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

input:focus, textarea:focus, select:focus {
    transform: scale(1.02);
    box-shadow: 
        0 0 0 3px rgba(217, 119, 6, 0.2), 
        0 4px 20px rgba(0, 0, 0, 0.1);
    border-color: #d97706;
    outline: none;
}

.btn-primary {
    background: linear-gradient(135deg, #d97706, #b45309);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    border: 3px solid #92400e;
    color: white;
    font-weight: 700;
    padding: 12px 24px;
    font-size: 1rem;
    min-width: 140px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);
}

.viewDetailsBtn {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 10px 18px;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #1d4ed8;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    height: 44px;
    margin-right: 12px;
}

.viewDetailsBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, #2563EB, #1d4ed8);
    color: white !important;
}

.removeBtn {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-weight: 600;
    border: none;
    cursor: pointer;
    padding: 10px 18px;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    border: 2px solid #b91c1c;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    height: 44px;
    margin-left: 12px;
}

.removeBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    background: linear-gradient(135deg, #DC2626, #b91c1c);
}

.form-input-enhanced {
    background: white;
    border: 2px solid #cbd5e0;
    color: #2d3748;
    padding: 1rem 1.5rem 1rem 3.5rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    width: 100%;
    font-size: 1.125rem;
}

.form-input-enhanced:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2);
    outline: none;
}

.form-input-enhanced::placeholder {
    color: #718096;
}

.search-input-container {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-input-container .search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
    font-size: 1.1rem;
    z-index: 10;
    pointer-events: none;
}

.search-input-container input {
    padding-left: 3.5rem !important;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 9999px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.875rem;
    border-width: 2px;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
    border-color: #10b981;
}

.status-expired {
    background: #fee2e2;
    color: #991b1b;
    border-color: #ef4444;
}

.status-upcoming {
    background: #fef3c7;
    color: #92400e;
    border-color: #f59e0b;
}

.enhanced-table {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

.enhanced-table th {
    background: linear-gradient(135deg, #f8f4e5 0%, #f0e6c3 100%);
    color: #2d3748;
    font-size: 1.125rem;
    font-weight: 700;
    padding: 20px;
    border-bottom: 3px solid #e2e8f0;
}

.enhanced-table td {
    padding: 20px;
    font-size: 1.125rem;
    border-bottom: 2px solid #f7fafc;
    vertical-align: middle;
}

.enhanced-table tr:hover {
    background: #fefce8;
}

.enhanced-modal {
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 3px solid #e2e8f0;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    max-height: 90vh;
    overflow-y: auto;
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
    .xl-text {
        font-size: 1rem;
    }
    
    .xxl-text {
        font-size: 1.125rem;
    }
    
    .enhanced-table th,
    .enhanced-table td {
        padding: 16px;
        font-size: 1rem;
    }
    
    .viewDetailsBtn,
    .removeBtn {
        padding: 8px 12px;
        font-size: 0.75rem;
        min-width: 100px;
        height: 40px;
        margin-right: 8px;
        margin-left: 8px;
    }

    .enhanced-modal {
        width: 95%;
        margin: 0 auto;
    }
    
    .search-input-container {
        min-width: 100%;
    }
}

*:focus {
    outline: 3px solid #d97706;
    outline-offset: 2px;
}

::selection {
    background: #d97706;
    color: white;
}

.loading-spinner {
    display: inline-block;
    width: 24px;
    height: 24px;
    border: 3px solid rgba(217, 119, 6, 0.3);
    border-top: 3px solid #d97706;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.orange-icon {
    color: #d97706 !important;
}

.orange-icon-bg {
    background-color: #d97706 !important;
}

.top-bar-icon {
    color: #d97706 !important;
    font-size: 1.5rem;
}

.top-bar-icon-bg {
    background: #fef3c7 !important;
    border: 2px solid #fbbf24 !important;
}

.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 2px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(217, 119, 6, 0.1), transparent);
    transition: left 0.5s;
}

.stats-card:hover::before {
    left: 100%;
}

.stats-card:hover {
    transform: translateY(-8px);
    border-color: rgba(217, 119, 6, 0.3);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 30px rgba(217, 119, 6, 0.1);
}

.interactive-file-upload label {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(248, 250, 252, 0.8);
    border: 2px solid #e2e8f0;
}

.interactive-file-upload label.dragover {
    border-color: #d97706;
    background: rgba(217, 119, 6, 0.05);
    transform: scale(1.02);
}

.toast-progress {
    animation: toastProgress 5s linear forwards;
}

@keyframes toastProgress {
    from { width: 100%; }
    to { width: 0%; }
}

.price-currency {
    color: #d97706;
    font-weight: bold;
}

.pagination-btn {
    background: white;
    border: 2px solid #e2e8f0;
    color: #2d3748;
    transition: all 0.3s ease;
}

.pagination-btn:hover {
    background: #d97706;
    color: white;
    border-color: #b45309;
}

.pagination-btn.active {
    background: #d97706;
    color: white;
    border-color: #b45309;
}

.btn-consistent {
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 24px;
    padding: 8px 0;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 32px;
}

.cancel-btn {
    background: #6b7280;
    color: white;
    border: 2px solid #4b5563;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cancel-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.add-btn {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: white;
    border: 2px solid #92400e;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-btn:hover {
    background: linear-gradient(135deg, #b45309, #92400e);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
}

.analytics-modal {
    width: 90%;
    max-width: 800px;
    max-height: 85vh;
}

.export-modal {
    width: 90%;
    max-width: 500px;
    max-height: 70vh;
}

.chart-container {
    width: 100%;
    height: 300px;
    margin: 20px 0;
}

.export-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    margin: 10px 0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.export-option:hover {
    border-color: #d97706;
    background: #fef3c7;
}

.export-option i {
    font-size: 1.5rem;
    margin-right: 15px;
    color: #d97706;
}

.export-progress {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 10px;
}

.export-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #d97706, #b45309);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.stats-card-non-clickable {
    cursor: default !important;
}

.stats-card-non-clickable:hover {
    transform: none !important;
    border-color: #e2e8f0 !important;
    box-shadow: none !important;
}

.stats-card-non-clickable:hover::before {
    left: -100% !important;
}

.stats-card-non-clickable .group-hover\:scale-110 {
    transform: none !important;
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

.fas, .fa-utensils, .fa-sync-alt, .fa-chart-bar, .fa-download, .fa-history, 
.fa-search, .fa-filter, .fa-chevron-left, .fa-chevron-right, .fa-plus, 
.fa-plus-circle, .fa-edit, .fa-eye, .fa-trash, .fa-cloud-upload-alt, 
.fa-check-circle, .fa-save, .fa-times, .fa-ice-cream, .fa-carrot, 
.fa-drumstick-bite, .fa-glass-whiskey, .fa-layer-group, .fa-tags, 
.fa-file-csv, .fa-file-pdf, .fa-file-excel, .fa-code, .fa-exclamation-circle,
.fa-exclamation-triangle, .fa-info-circle, .fa-check {
    color: inherit !important;
    opacity: 1 !important;
    visibility: visible !important;
    font-style: normal;
    font-weight: 900;
}

.viewDetailsBtn i,
.removeBtn i,
.refresh-icon i,
.search-icon i {
    opacity: 1 !important;
    visibility: visible !important;
    color: inherit !important;
}

.filter-dropdown-container {
    position: relative;
    z-index: 100;
}

.filter-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 8px;
    width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    border: 2px solid #e2e8f0;
    z-index: 2000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.filter-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.filter-dropdown-content {
    padding: 20px;
}

.filter-dropdown-content h4 {
    color: #d97706;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 1rem;
}

.filter-option {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    margin-bottom: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-option:hover {
    background: #f8fafc;
}

.filter-option input[type="checkbox"] {
    margin-right: 12px;
    width: 18px;
    height: 18px;
    accent-color: #d97706;
}

.filter-option label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.95rem;
    color: #4a5568;
    flex: 1;
}

.filter-option i {
    margin-right: 8px;
    font-size: 1rem;
}

.filter-option.active {
    background: #fef3c7;
    border-left: 3px solid #d97706;
}
</style>

<!-- Enhanced Top Bar -->
<div class="glass-card mb-6">
    <div class="flex justify-between items-center p-6">
        <div class="flex items-center space-x-4">
            <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                <i class="fas fa-tags top-bar-icon"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-[#92400e] uppercase tracking-wide high-contrast-text">Promo Deals</h2>
                <p class="text-gray-600 xl-text">Manage your restaurant promotional deals and offers</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative group">
                <button class="p-4 glass-card hover:bg-[#d97706] hover:text-white transition-all duration-300 shadow-lg rounded-2xl" onclick="location.reload()">
                    <i class="fas fa-sync-alt top-bar-icon group-hover:text-white"></i>
                </button>
                <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full pulse-dot border-2 border-white"></div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Stats Cards with Analytics - Non-clickable version -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Total Promos</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="totalPromos">0</h3>
            </div>
            <div class="p-3 bg-[#d97706]/10 rounded-xl">
                <i class="fas fa-tags top-bar-icon text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-green-600 text-sm">
            <i class="fas fa-arrow-up mr-1"></i>
            <span>Live Count</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Active</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="activePromos">0</h3>
            </div>
            <div class="p-3 bg-green-500/10 rounded-xl">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-green-600 text-sm">
            <i class="fas fa-eye mr-1"></i>
            <span>All Active</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Expired</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="expiredPromos">0</h3>
            </div>
            <div class="p-3 bg-red-500/10 rounded-xl">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-red-600 text-sm">
            <i class="fas fa-clock mr-1"></i>
            <span>Past Deals</span>
        </div>
    </div>
    
    <div class="stats-card p-6 rounded-2xl stats-card-non-clickable">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-400 text-sm font-medium">Upcoming</p>
                <h3 class="text-3xl font-bold text-[#92400e] mt-2" id="upcomingPromos">0</h3>
            </div>
            <div class="p-3 bg-blue-500/10 rounded-xl">
                <i class="fas fa-calendar-alt text-blue-500 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-blue-600 text-sm">
            <i class="fas fa-clock mr-1"></i>
            <span>Future Deals</span>
        </div>
    </div>
</div>

<!-- Quick Actions Bar -->
<div class="glass-card p-6 rounded-2xl mb-6">
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex items-center space-x-4">
            <h3 class="text-xl font-semibold text-[#92400e] high-contrast-text">Quick Actions</h3>
            <div class="h-8 w-px bg-gray-300"></div>
            <button class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg" onclick="showAnalytics()">
                <i class="fas fa-chart-bar"></i>
                Analytics
            </button>
            <button class="quick-action-btn px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg" onclick="showExportOptions()">
                <i class="fas fa-download"></i>
                Export
            </button>
        </div>
        <div class="flex items-center space-x-3 xl-text text-gray-600">
            <i class="fas fa-history top-bar-icon"></i>
            <span>Last updated: <span id="lastUpdatedTime" class="font-semibold text-[#92400e]">Just now</span></span>
        </div>
    </div>
</div>

<!-- Enhanced Table Container -->
<div class="glass-card rounded-2xl p-6 text-gray-800">
    <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
            <div class="search-input-container">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
                <input
                    type="text"
                    id="searchInput"
                    class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-300 rounded-xl
                           text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#d97706] focus:border-transparent transition-all duration-300 form-input-enhanced xl-text"
                    placeholder="Search promos..."
                />
            </div>

            <div class="filter-dropdown-container">
                <button id="filterBtn" class="flex items-center gap-3 bg-white text-gray-800 px-6 py-4 rounded-xl hover:bg-gray-50 transition-all duration-300 border-2 border-gray-300 form-input-enhanced xl-text btn-consistent">
                    <i class="fas fa-filter top-bar-icon"></i>
                    <span>Filter</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>
                <div class="filter-dropdown" id="filterDropdown">
                    <div class="filter-dropdown-content">
                        <h4>Filter by Status</h4>
                        <div class="space-y-2">
                            <div class="filter-option" data-status="active">
                                <input type="checkbox" id="filter-active" class="mr-3 filter-checkbox" value="active">
                                <label for="filter-active" class="cursor-pointer flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Active
                                </label>
                            </div>
                            <div class="filter-option" data-status="expired">
                                <input type="checkbox" id="filter-expired" class="mr-3 filter-checkbox" value="expired">
                                <label for="filter-expired" class="cursor-pointer flex items-center">
                                    <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                    Expired
                                </label>
                            </div>
                            <div class="filter-option" data-status="upcoming">
                                <input type="checkbox" id="filter-upcoming" class="mr-3 filter-checkbox" value="upcoming">
                                <label for="filter-upcoming" class="cursor-pointer flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                    Upcoming
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button id="addBtn" class="w-full lg:w-auto quick-action-btn px-8 py-4 rounded-xl font-semibold transition-all duration-300 flex items-center gap-3 group text-lg btn-consistent">
            <i class="fas fa-plus group-hover:scale-110 transition-transform duration-200"></i>
            Add New Promo
        </button>
    </div>

    <div class="max-h-[600px] overflow-y-auto overflow-x-hidden rounded-xl border-2 border-gray-200 custom-scrollbar">
        <table class="w-full enhanced-table">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-left">Promo Name</th>
                    <th class="text-left">Description</th>
                    <th class="text-center">Banner</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Expiration</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="outputBody" class="divide-y divide-gray-100">
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-600">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                            <div>
                                <p class="xxl-text font-semibold high-contrast-text">Loading promos...</p>
                                <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 pt-4 border-t border-gray-300">
        <div class="text-sm text-gray-600 mb-4 sm:mb-0 xl-text">
            Showing <span class="text-[#92400e] font-semibold" id="showingFrom">0</span> to 
            <span class="text-[#92400e] font-semibold" id="showingTo">0</span> of 
            <span class="text-[#92400e] font-semibold" id="totalItems">0</span> results
        </div>
        <div class="flex items-center space-x-2">
            <button id="prevPage" class="p-3 pagination-btn rounded-lg hover:bg-[#d97706] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div id="paginationNumbers" class="flex space-x-1">
            </div>
            <button id="nextPage" class="p-3 pagination-btn rounded-lg hover:bg-[#d97706] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Spinner Overlay -->
<div id="spinner" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90 backdrop-blur-sm hidden">
    <div class="enhanced-modal p-10 flex flex-col items-center space-y-6">
        <div class="w-20 h-20 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
        <div class="text-center">
            <p class="xxl-text font-semibold high-contrast-text">Processing Request</p>
            <p class="xl-text text-gray-600 mt-2">Please wait while we process your request...</p>
        </div>
    </div>
</div>

<!-- Enhanced Toast Notification -->
<div id="toast" class="fixed top-6 right-6 glass-card text-gray-800 p-4 rounded-xl shadow-2xl z-50 transform translate-x-full transition-transform duration-500">
    <div class="flex items-center space-x-4">
        <div class="p-2 rounded-lg bg-green-500/20">
            <i id="toastIcon" class="fas fa-check-circle text-green-500 text-xl"></i>
        </div>
        <div class="flex-1">
            <p id="toastMessage" class="font-semibold">Action completed successfully!</p>
            <p class="text-xs text-gray-600 mt-1">Just now</p>
        </div>
        <button id="closeToast" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="w-full bg-gray-300 rounded-full h-1 mt-3">
        <div class="bg-green-500 h-1 rounded-full toast-progress"></div>
    </div>
</div>

<!-- Export Progress Modal -->
<div id="exportProgressModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal p-8 max-w-md">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                <h3 class="text-xl font-bold text-[#92400e]">Exporting Data</h3>
                <p class="text-gray-600 text-center">Preparing your export file...</p>
                <div class="w-full export-progress">
                    <div class="export-progress-bar" id="exportProgressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div id="analyticsModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal analytics-modal p-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 top-bar-icon-bg rounded-xl">
                        <i class="fas fa-chart-bar top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e]">Promo Analytics</h3>
                        <p class="text-gray-600">Detailed analysis of your promo deals</p>
                    </div>
                </div>
                <button onclick="closeAnalytics()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="glass-card p-6">
                    <h4 class="font-semibold text-[#92400e] mb-4">Status Distribution</h4>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <div class="glass-card p-6">
                    <h4 class="font-semibold text-[#92400e] mb-4">Monthly Trend</h4>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-6">
                <h4 class="font-semibold text-[#92400e] mb-4">Key Metrics</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-[#fef3c7] rounded-lg">
                        <p class="text-sm text-gray-600">Total Promos</p>
                        <p class="text-2xl font-bold text-[#92400e]" id="analyticsTotal">0</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-green-600" id="analyticsActive">0</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <p class="text-sm text-gray-600">Expired</p>
                        <p class="text-2xl font-bold text-red-600" id="analyticsExpired">0</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600">Upcoming</p>
                        <p class="text-2xl font-bold text-blue-600" id="analyticsUpcoming">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal export-modal p-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 top-bar-icon-bg rounded-xl">
                        <i class="fas fa-download top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e]">Export Promo Data</h3>
                        <p class="text-gray-600">Choose your preferred export format</p>
                    </div>
                </div>
                <button onclick="closeExport()" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="export-option" onclick="exportToCSV()">
                    <i class="fas fa-file-csv"></i>
                    <div>
                        <h4 class="font-semibold">CSV Format</h4>
                        <p class="text-sm text-gray-600">Compatible with Excel, Google Sheets</p>
                    </div>
                </div>
                
                <div class="export-option" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i>
                    <div>
                        <h4 class="font-semibold">PDF Format</h4>
                        <p class="text-sm text-gray-600">Printable document with formatting</p>
                    </div>
                </div>
                
                <div class="export-option" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i>
                    <div>
                        <h4 class="font-semibold">Excel Format</h4>
                        <p class="text-sm text-gray-600">Native Excel file with formatting</p>
                    </div>
                </div>
                
                <div class="export-option" onclick="exportToJSON()">
                    <i class="fas fa-code"></i>
                    <div>
                        <h4 class="font-semibold">JSON Format</h4>
                        <p class="text-sm text-gray-600">For developers and API integration</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Export will include all promo deals</span>
                    <button onclick="closeExport()" class="cancel-btn">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Create/Edit Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal w-full max-w-2xl mx-4 p-8 text-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-4 top-bar-icon-bg rounded-2xl shadow-lg">
                        <i class="fas fa-plus-circle top-bar-icon"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#92400e] high-contrast-text" id="modalTitle">Create Promo Deal</h3>
                        <p class="xl-text text-gray-600">Add a new promotional offer</p>
                    </div>
                </div>

                <hr class="border-gray-300">

                <form id="frmCreateEntry" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" id="entryId" name="entryId" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="entryName" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Promo Name</label>
                            <input type="text" id="entryName" name="entryName" class="w-full form-input-enhanced" placeholder="Enter promo name">
                            <p class="text-sm text-red-600 mt-2 hidden" id="nameError">Please enter a promo name</p>
                        </div>

                        <div>
                            <label for="entryExpiration" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Expiration Date</label>
                            <input type="date" id="entryExpiration" name="entryExpiration" class="w-full form-input-enhanced">
                            <p class="text-sm text-red-600 mt-2 hidden" id="expirationError">Please select a valid expiration date</p>
                        </div>
                    </div>

                    <div>
                        <label for="entryDescription" class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Description</label>
                        <textarea id="entryDescription" name="entryDescription" rows="4" class="w-full form-input-enhanced resize-none" placeholder="Enter promo description"></textarea>
                        <p class="text-sm text-red-600 mt-2 hidden" id="descriptionError">Please enter a description</p>
                    </div>

                    <div>
                        <label class="block mb-4 xl-text font-bold text-[#92400e] high-contrast-text">Promo Banner</label>
                        <div class="mt-1 flex items-center justify-center w-full interactive-file-upload">
                            <label for="entryImage" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-[#d97706] transition-all duration-300 group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3 group-hover:text-[#d97706] transition-colors duration-300"></i>
                                    <p class="xl-text text-gray-600 group-hover:text-[#92400e] transition-colors duration-300">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG (Max. 5MB)</p>
                                </div>
                                <input id="entryImage" name="entryImage" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <div id="imagePreview" class="mt-4 hidden">
                            <p class="text-sm text-gray-600 mb-2">Preview:</p>
                            <img id="previewImage" class="max-h-32 rounded-lg border-2 border-gray-300 shadow-md">
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="button" id="cancelBtn" class="cancel-btn">
                            Cancel
                        </button>
                        <button type="submit" class="add-btn">
                            <i class="fas fa-plus-circle mr-3"></i>
                            <span id="submitBtnText">Create Promo</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="modal-overlay">
        <div class="enhanced-modal w-full max-w-md mx-4 p-8 text-gray-800">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Delete Promo</h3>
                <p class="xl-text text-gray-600 mb-6">Are you sure you want to delete <span id="deletePromoName" class="font-semibold text-[#92400e]"></span>? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button id="cancelDelete" class="cancel-btn">
                        Cancel
                    </button>
                    <button id="confirmDelete" class="removeBtn">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../src/components/admin/footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    
    initEnhancedInteractions();
    initializePromoSystem();
});

let currentPromoId = null;
let promoData = [];
let filterDropdownOpen = false;

function initEnhancedInteractions() {
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterPromos(e.target.value);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    }

    const entryImage = document.getElementById('entryImage');
    if (entryImage) {
        entryImage.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const imagePreview = document.getElementById('imagePreview');
                const previewImage = document.getElementById('previewImage');
                const label = this.previousElementSibling;
                
                if (imagePreview && previewImage && label) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(this.files[0]);
                    
                    label.innerHTML = `
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-check-circle text-green-500 text-3xl mb-3"></i>
                            <p class="xl-text text-green-600 font-semibold">${this.files[0].name}</p>
                            <p class="text-sm text-gray-500 mt-2">Click to change image</p>
                        </div>
                    `;
                    label.classList.add('border-green-400', 'bg-green-50');
                }
            }
        });

        const label = entryImage.previousElementSibling;
        if (label) {
            label.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            label.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });
            
            label.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    entryImage.files = files;
                    entryImage.dispatchEvent(new Event('change'));
                }
            });
        }
    }

    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    
    if (filterBtn && filterDropdown) {
        filterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            filterDropdown.classList.toggle('show');
            filterDropdownOpen = !filterDropdownOpen;
            
            const chevron = this.querySelector('.fa-chevron-down');
            if (filterDropdownOpen) {
                chevron.style.transform = 'rotate(180deg)';
            } else {
                chevron.style.transform = 'rotate(0deg)';
            }
        });

        document.addEventListener('click', function(e) {
            if (filterDropdownOpen && !filterDropdown.contains(e.target) && !filterBtn.contains(e.target)) {
                filterDropdown.classList.remove('show');
                filterDropdownOpen = false;
                const chevron = filterBtn.querySelector('.fa-chevron-down');
                chevron.style.transform = 'rotate(0deg)';
            }
        });

        const filterOptions = document.querySelectorAll('.filter-option');
        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                
                if (checkbox.checked) {
                    this.classList.add('active');
                } else {
                    this.classList.remove('active');
                }
                
                applyFilters();
            });
        });
    }

    const cancelButtons = document.querySelectorAll('#cancelBtn, #cancelDelete');
    cancelButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.add('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('analyticsModal').classList.add('hidden');
            document.getElementById('exportModal').classList.add('hidden');
        }
    });
}

window.showEnhancedToast = function(message, type = 'success', duration = 5000) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    if (toast && toastMessage && toastIcon) {
        toast.style.animation = 'none';
        void toast.offsetHeight;
        
        toastMessage.textContent = message;
        
        const toastConfig = {
            success: { icon: 'fa-check-circle', color: 'green' },
            error: { icon: 'fa-exclamation-circle', color: 'red' },
            warning: { icon: 'fa-exclamation-triangle', color: 'yellow' },
            info: { icon: 'fa-info-circle', color: 'blue' }
        };
        
        const config = toastConfig[type] || toastConfig.success;
        toastIcon.className = `fas ${config.icon} text-${config.color}-500 text-xl`;
        toastIcon.parentElement.className = `p-2 rounded-lg bg-${config.color}-500/20`;
        
        toast.style.transform = 'translateX(0)';
        
        setTimeout(() => {
            hideEnhancedToast();
        }, duration);
    }
};

window.hideEnhancedToast = function() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.style.transform = 'translateX(100%)';
    }
};

const closeToast = document.getElementById('closeToast');
if (closeToast) {
    closeToast.addEventListener('click', hideEnhancedToast);
}

function showExportProgress() {
    document.getElementById('exportProgressModal').classList.remove('hidden');
    const progressBar = document.getElementById('exportProgressBar');
    let width = 0;
    const interval = setInterval(() => {
        if (width >= 100) {
            clearInterval(interval);
        } else {
            width += 10;
            progressBar.style.width = width + '%';
        }
    }, 50);
}

function hideExportProgress() {
    document.getElementById('exportProgressModal').classList.add('hidden');
    document.getElementById('exportProgressBar').style.width = '0%';
}

function showExportOptions() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExport() {
    document.getElementById('exportModal').classList.add('hidden');
}

function exportToCSV() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        window.location.href = window.location.pathname + '?export=csv';
        hideExportProgress();
        showEnhancedToast('CSV file downloaded successfully!', 'success');
    }, 1000);
}

function exportToPDF() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');
        
        doc.setFontSize(20);
        doc.setTextColor(146, 64, 14);
        doc.text('Promo Deals Report', 14, 20);
        
        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        doc.text(`Generated on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}`, 14, 28);
        
        const table = document.querySelector('.enhanced-table');
        const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());
        
        const rows = Array.from(table.querySelectorAll('tbody tr')).map(row => {
            return Array.from(row.querySelectorAll('td')).map((cell, index) => {
                if (index === 3) {
                    const img = cell.querySelector('img');
                    return img ? '✓ Has Image' : 'No Image';
                } else if (index === 6) {
                    return 'Available';
                }
                return cell.textContent.trim();
            });
        });
        
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 35,
            theme: 'grid',
            styles: {
                fontSize: 9,
                cellPadding: 3,
                overflow: 'linebreak',
                cellWidth: 'wrap'
            },
            headStyles: {
                fillColor: [217, 119, 6],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [248, 244, 229]
            },
            margin: { top: 35 }
        });
        
        doc.save(`promo_export_${new Date().toISOString().split('T')[0]}.pdf`);
        
        hideExportProgress();
        showEnhancedToast('PDF file downloaded successfully!', 'success');
    }, 1000);
}

function exportToExcel() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        const wb = XLSX.utils.book_new();
        wb.Props = {
            Title: "Promo Deals Export",
            Subject: "Promo Data",
            Author: "Restaurant Management System",
            CreatedDate: new Date()
        };
        
        const table = document.querySelector('.enhanced-table');
        const ws_data = [];
        
        const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());
        ws_data.push(headers);
        
        table.querySelectorAll('tbody tr').forEach(row => {
            const rowData = Array.from(row.querySelectorAll('td')).map((cell, index) => {
                if (index === 3) {
                    const img = cell.querySelector('img');
                    return img ? '✓ Has Image' : 'No Image';
                } else if (index === 6) {
                    return 'Available';
                }
                return cell.textContent.trim();
            });
            ws_data.push(rowData);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        
        const wscols = [
            {wch: 5},
            {wch: 25},
            {wch: 40},
            {wch: 15},
            {wch: 15},
            {wch: 15},
            {wch: 20}
        ];
        ws['!cols'] = wscols;
        
        XLSX.utils.book_append_sheet(wb, ws, "Promo Deals");
        
        XLSX.writeFile(wb, `promo_export_${new Date().toISOString().split('T')[0]}.xlsx`);
        
        hideExportProgress();
        showEnhancedToast('Excel file downloaded successfully!', 'success');
    }, 1000);
}

function exportToJSON() {
    showExportProgress();
    closeExport();
    
    setTimeout(() => {
        window.location.href = window.location.pathname + '?export=json';
        hideExportProgress();
        showEnhancedToast('JSON file downloaded successfully!', 'success');
    }, 1000);
}

function showAnalytics() {
    document.getElementById('analyticsModal').classList.remove('hidden');
    updateAnalyticsCharts();
}

function closeAnalytics() {
    document.getElementById('analyticsModal').classList.add('hidden');
}

function updateAnalyticsCharts() {
    if (promoData.length === 0) return;
    
    document.getElementById('analyticsTotal').textContent = promoData.length;
    
    const activeCount = promoData.filter(item => item.status === 'active').length;
    const expiredCount = promoData.filter(item => item.status === 'expired').length;
    const upcomingCount = promoData.filter(item => item.status === 'upcoming').length;
    
    document.getElementById('analyticsActive').textContent = activeCount;
    document.getElementById('analyticsExpired').textContent = expiredCount;
    document.getElementById('analyticsUpcoming').textContent = upcomingCount;
    
    const ctx1 = document.getElementById('statusChart');
    if (ctx1) {
        if (window.statusChartInstance) {
            window.statusChartInstance.destroy();
        }
        
        window.statusChartInstance = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Active', 'Expired', 'Upcoming'],
                datasets: [{
                    data: [activeCount, expiredCount, upcomingCount],
                    backgroundColor: ['#10b981', '#ef4444', '#3b82f6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    const ctx2 = document.getElementById('trendChart');
    if (ctx2) {
        if (window.trendChartInstance) {
            window.trendChartInstance.destroy();
        }
        
        window.trendChartInstance = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Promos Created',
                    data: [5, 8, 12, 6, 9, 15],
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
}

function applyFilters() {
    const activeFilter = document.getElementById('filter-active').checked;
    const expiredFilter = document.getElementById('filter-expired').checked;
    const upcomingFilter = document.getElementById('filter-upcoming').checked;
    
    const rows = document.querySelectorAll('#outputBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.cells.length > 1) {
            const statusElement = row.cells[4];
            const status = statusElement.textContent.toLowerCase().trim();
            
            let shouldShow = false;
            
            if (!activeFilter && !expiredFilter && !upcomingFilter) {
                shouldShow = true;
            } else {
                if (activeFilter && status === 'active') shouldShow = true;
                if (expiredFilter && status === 'expired') shouldShow = true;
                if (upcomingFilter && status === 'upcoming') shouldShow = true;
            }
            
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        }
    });
    
    updatePaginationDisplay(visibleCount);
}

function filterPromosByStatus(status) {
    document.getElementById('filter-active').checked = status === 'active';
    document.getElementById('filter-expired').checked = status === 'expired';
    document.getElementById('filter-upcoming').checked = status === 'upcoming';
    
    document.querySelectorAll('.filter-option').forEach(option => {
        const checkbox = option.querySelector('input[type="checkbox"]');
        if (checkbox.checked) {
            option.classList.add('active');
        } else {
            option.classList.remove('active');
        }
    });
    
    applyFilters();
    showEnhancedToast(`Filtering ${status} promos...`, 'info');
}

function filterActivePromos() {
    filterPromosByStatus('active');
}

function filterExpiredPromos() {
    filterPromosByStatus('expired');
}

function filterUpcomingPromos() {
    filterPromosByStatus('upcoming');
}

function filterPromos(searchTerm) {
    const rows = document.querySelectorAll('#outputBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.cells.length > 1) {
            const text = row.textContent.toLowerCase();
            const shouldShow = text.includes(searchTerm.toLowerCase());
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        }
    });
    
    updatePaginationDisplay(visibleCount);
}

function updatePaginationDisplay(visibleCount) {
    document.getElementById('showingFrom').textContent = 1;
    document.getElementById('showingTo').textContent = visibleCount;
    document.getElementById('totalItems').textContent = visibleCount;
}

function initializePromoSystem() {
    document.getElementById('addBtn').addEventListener('click', function(e) { 
        e.preventDefault();
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = "Create Promo Deal";
        document.getElementById('submitBtnText').textContent = "Create Promo";
        document.getElementById('entryId').value = "";
        document.getElementById('frmCreateEntry').reset();
        document.getElementById('imagePreview').classList.add('hidden');
        
        const label = document.querySelector('label[for="entryImage"]');
        if (label) {
            label.innerHTML = `
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3 group-hover:text-[#d97706] transition-colors duration-300"></i>
                    <p class="xl-text text-gray-600 group-hover:text-[#92400e] transition-colors duration-300">Click to upload or drag and drop</p>
                    <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG (Max. 5MB)</p>
                </div>
            `;
            label.classList.remove('border-green-400', 'bg-green-50');
        }
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('entryExpiration').min = today;
    });

    document.getElementById('frmCreateEntry').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const promoName = document.getElementById('entryName').value.trim();
        const description = document.getElementById('entryDescription').value.trim();
        const expiration = document.getElementById('entryExpiration').value;
        const promoId = document.getElementById('entryId').value;
        
        document.getElementById('nameError').classList.add('hidden');
        document.getElementById('descriptionError').classList.add('hidden');
        document.getElementById('expirationError').classList.add('hidden');
        
        let isValid = true;
        
        if (!promoName) {
            document.getElementById('nameError').classList.remove('hidden');
            isValid = false;
        }
        
        if (!description) {
            document.getElementById('descriptionError').classList.remove('hidden');
            isValid = false;
        }
        
        if (!expiration) {
            document.getElementById('expirationError').classList.remove('hidden');
            isValid = false;
        }
        
        if (!isValid) {
            showEnhancedToast('Please fill all required fields', 'error');
            return;
        }
        
        document.getElementById('spinner').classList.remove('hidden');
        
        const formData = new FormData(this);
        formData.append('requestType', promoId ? 'UpdatePromo' : 'AddPromo');
        
        fetch(window.location.pathname, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('spinner').classList.add('hidden');
            
            if (data.status === 200) {
                showEnhancedToast(data.message || 'Promo saved successfully!', 'success');
                document.getElementById('addModal').classList.add('hidden');
                loadPromos();
            } else {
                showEnhancedToast(data.message || 'Error saving promo', 'error');
            }
        })
        .catch(error => {
            document.getElementById('spinner').classList.add('hidden');
            console.error('Error:', error);
            showEnhancedToast('Network error. Please try again.', 'error');
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!currentPromoId) return;
        
        document.getElementById('spinner').classList.remove('hidden');
        
        fetch(window.location.pathname, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `promo_id=${currentPromoId}&requestType=removePromo`
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('spinner').classList.add('hidden');
            
            if (data.status === 200) {
                showEnhancedToast(data.message || 'Promo deleted successfully!', 'success');
                document.getElementById('deleteModal').classList.add('hidden');
                loadPromos();
            } else {
                showEnhancedToast(data.message || 'Error deleting promo', 'error');
            }
        })
        .catch(error => {
            document.getElementById('spinner').classList.add('hidden');
            console.error('Error:', error);
            showEnhancedToast('Network error. Please try again.', 'error');
        });
    });

    loadPromos();
}

function loadPromos() {
    document.getElementById('outputBody').innerHTML = `
        <tr>
            <td colspan="7" class="p-8 text-center text-gray-600">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="w-16 h-16 border-4 border-[#d97706] border-t-transparent rounded-full animate-spin"></div>
                    <div>
                        <p class="xxl-text font-semibold high-contrast-text">Loading promos...</p>
                        <p class="xl-text text-gray-600 mt-2">Please wait while we fetch your data</p>
                    </div>
                </div>
            </td>
        </tr>
    `;
    
    fetch(window.location.pathname + '?requestType=fetch_all_promos')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(res => {
            if (res.status === 200) {
                promoData = res.data || [];
                let count = 1;
                let activeCount = 0;
                let expiredCount = 0;
                let upcomingCount = 0;

                const outputBody = document.getElementById('outputBody');
                outputBody.innerHTML = '';

                if (promoData.length > 0) {
                    promoData.forEach(promo => {
                        const currentDate = new Date();
                        const endDate = new Date(promo.end_date);
                        const startDate = new Date(promo.start_date);
                        let status = 'upcoming';
                        let statusClass = 'status-upcoming';
                        
                        if (currentDate > endDate) {
                            status = 'expired';
                            statusClass = 'status-expired';
                            expiredCount++;
                        } else if (currentDate >= startDate && currentDate <= endDate) {
                            status = 'active';
                            statusClass = 'status-active';
                            activeCount++;
                        } else {
                            upcomingCount++;
                        }

                        const row = document.createElement('tr');
                        row.className = 'table-row-hover';
                        row.innerHTML = `
                            <td class="text-center font-semibold xl-text">${count++}</td>
                            <td class="font-bold text-[#92400e] xl-text">${promo.promo_name}</td>
                            <td class="xl-text">
                                ${promo.promo_description.length > 60 ? promo.promo_description.substring(0, 60) + '...' : promo.promo_description}
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center items-center">
                                    ${promo.promo_image ? 
                                        `<img src="/Grillbook/static/upload/${promo.promo_image}" alt="Banner" class="w-20 h-16 object-cover rounded-lg shadow-md" />`
                                        : 
                                        '<span class="text-gray-500 xl-text">No image</span>'
                                    }
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge ${statusClass}">${status}</span>
                            </td>
                            <td class="text-center font-semibold xl-text">${new Date(promo.end_date).toLocaleDateString()}</td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <button class="viewDetailsBtn cursor-pointer font-semibold transition"
                                            data-promo_id="${promo.promo_id}"
                                            data-promo_name="${promo.promo_name}"
                                            data-promo_description="${promo.promo_description}"
                                            data-end_date="${promo.end_date}"
                                            data-promo_image="${promo.promo_image || ''}">
                                        <i class="fas fa-edit mr-2"></i>
                                        Edit
                                    </button>
                                    <button class="removeBtn cursor-pointer font-semibold transition"
                                            data-promo_id="${promo.promo_id}"
                                            data-promo_name="${promo.promo_name}">
                                        <i class="fas fa-trash mr-2"></i>
                                        Remove
                                    </button>
                                </div>
                            </td>
                        `;
                        outputBody.appendChild(row);
                    });
                    
                    updateStatsCards(promoData.length, activeCount, expiredCount, upcomingCount);
                    
                    document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
                    
                    attachEventListeners();
                    
                    applyFilters();
                } else {
                    outputBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-600">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <i class="fas fa-tags text-6xl text-gray-400"></i>
                                    <div>
                                        <p class="xxl-text font-semibold high-contrast-text">No Promo Deals Found</p>
                                        <p class="xl-text text-gray-600 mt-2">Click "Add New Promo" to get started</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } else {
                throw new Error(res.message || 'Failed to load promos');
            }
        })
        .catch(error => {
            console.error('Error loading promos:', error);
            document.getElementById('outputBody').innerHTML = `
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-600">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <i class="fas fa-exclamation-triangle text-6xl text-red-400"></i>
                            <div>
                                <p class="xxl-text font-semibold high-contrast-text">Error Loading Data</p>
                                <p class="xl-text text-gray-600 mt-2">${error.message || 'Please check your connection and try again'}</p>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
}

function attachEventListeners() {
    document.querySelectorAll('.viewDetailsBtn').forEach(button => {
        button.addEventListener('click', function() {
            const promoId = this.getAttribute('data-promo_id');
            const promoName = this.getAttribute('data-promo_name');
            const description = this.getAttribute('data-promo_description');
            const endDate = this.getAttribute('data-end_date');
            const promoImage = this.getAttribute('data-promo_image');
            
            document.getElementById('modalTitle').textContent = "Edit Promo Deal";
            document.getElementById('submitBtnText').textContent = "Update Promo";
            document.getElementById('entryId').value = promoId;
            document.getElementById('entryName').value = promoName;
            document.getElementById('entryDescription').value = description;
            document.getElementById('entryExpiration').value = endDate.split(' ')[0];
            
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            const label = document.querySelector('label[for="entryImage"]');
            
            if (promoImage) {
                previewImage.src = `/Grillbook/static/upload/${promoImage}`;
                imagePreview.classList.remove('hidden');
                
                if (label) {
                    label.innerHTML = `
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-check-circle text-green-500 text-3xl mb-3"></i>
                            <p class="xl-text text-green-600 font-semibold">Current image: ${promoImage}</p>
                            <p class="text-sm text-gray-500 mt-2">Click to change image</p>
                        </div>
                    `;
                    label.classList.add('border-green-400', 'bg-green-50');
                }
            } else {
                imagePreview.classList.add('hidden');
                if (label) {
                    label.innerHTML = `
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3 group-hover:text-[#d97706] transition-colors duration-300"></i>
                            <p class="xl-text text-gray-600 group-hover:text-[#92400e] transition-colors duration-300">Click to upload or drag and drop</p>
                            <p class="text-sm text-gray-500 mt-2">PNG, JPG, JPEG (Max. 5MB)</p>
                        </div>
                    `;
                    label.classList.remove('border-green-400', 'bg-green-50');
                }
            }
            
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('entryExpiration').min = today;
            
            document.getElementById('addModal').classList.remove('hidden');
        });
    });

    document.querySelectorAll('.removeBtn').forEach(button => {
        button.addEventListener('click', function() {
            currentPromoId = this.getAttribute('data-promo_id');
            const promoName = this.getAttribute('data-promo_name');
            
            document.getElementById('deletePromoName').textContent = promoName;
            document.getElementById('deleteModal').classList.remove('hidden');
        });
    });
}

function updateStatsCards(total, active, expired, upcoming) {
    document.getElementById('totalPromos').textContent = total;
    document.getElementById('activePromos').textContent = active;
    document.getElementById('expiredPromos').textContent = expired;
    document.getElementById('upcomingPromos').textContent = upcoming;
    
    document.getElementById('lastUpdatedTime').textContent = new Date().toLocaleTimeString();
}
</script>