
<?php


include ('config.php');

date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }



    public function getDataAnalytics()
{
    $query = "
        SELECT 
            (SELECT COUNT(*) FROM user WHERE user_status = 1) AS totalUsers,
            (SELECT COUNT(*) FROM reservations) AS totalReservations,
            (SELECT COUNT(*) FROM reservations WHERE status = 'pending') AS pendingReservations,
            (SELECT COUNT(*) FROM reservations WHERE status = 'confirmed') AS confirmedReservations,
            (SELECT COUNT(*) FROM reservations WHERE status = 'completed') AS completedReservations,
            (SELECT COUNT(*) FROM menu WHERE menu_status = 1) AS activeMenuItems,
            (SELECT COUNT(*) FROM deals WHERE deal_type = 'promo_deals') AS totalPromos,
            (SELECT COUNT(*) FROM deals WHERE deal_type = 'group_deals') AS totalGroupDeals,
            (SELECT COALESCE(SUM(grand_total), 0) FROM reservations WHERE status = 'completed') AS totalSales
    ";

    $result = $this->conn->query($query);

    if ($result) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}






public function UpdateAccount($user_id, $first_name, $last_name, $email, $password) {

    $queryStr = "UPDATE `user` SET `user_fname` = ?, `user_lname` = ?, `user_email` = ?";

    if (!empty($password)) {
        $queryStr .= ", `user_password` = ?";
        // Hash the password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    }

    $queryStr .= " WHERE `user_id` = ?";

    // Prepare the query
    $query = $this->conn->prepare($queryStr);

    if (!$query) {
        // Optional: log the error
        error_log("Prepare failed: " . $this->conn->error);
        return false;
    }

    // Bind parameters based on whether password is provided
    if (!empty($password)) {
        $query->bind_param("ssssi", $first_name, $last_name, $email, $hashedPassword, $user_id);
    } else {
        $query->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    }

    // Execute the query and return the result
    return $query->execute();
}










    public function updateArchived($reservation_id, $status,$column){
            $stmt = $this->conn->prepare("UPDATE `reservations` SET $column = ? WHERE `id` = ?");
            $stmt->bind_param("ii", $status, $reservation_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Reservation status updated successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $stmt->error
                ];
            }
        }




        public function UpdateReservationStatus($reservation_id, $status){
            $stmt = $this->conn->prepare("UPDATE `reservations` SET `status` = ? WHERE `id` = ?");
            $stmt->bind_param("si", $status, $reservation_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Reservation status updated successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $stmt->error
                ];
            }
        }




         public function ApproveReschedule($reservation_id) {
    // Fetch the request_details JSON
    $stmt = $this->conn->prepare("SELECT request_details FROM reservations WHERE id = ?");
    if (!$stmt) {
        return [
            'success' => false,
            'message' => 'Prepare failed: ' . $this->conn->error
        ];
    }

    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        return [
            'success' => false,
            'message' => 'Reservation not found.'
        ];
    }

    $request_details = json_decode($result['request_details'], true);

    if (isset($request_details['newDate']) && isset($request_details['newTime'])&& isset($request_details['seats'])) {
        $newDate = $request_details['newDate'];
        $newTime = $request_details['newTime'];
        $newSeats = $request_details['seats'];

        // Update status to 'pending', date_schedule, time_schedule, and clear request_details
        $stmt = $this->conn->prepare(
            "UPDATE reservations 
             SET status = 'pending', date_schedule = ?, time_schedule = ?,seats = ?, request_details = NULL 
             WHERE id = ?"
        );

        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Prepare failed: ' . $this->conn->error
            ];
        }

        $stmt->bind_param("ssii", $newDate, $newTime,$newSeats, $reservation_id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Reservation updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Update failed: ' . $stmt->error
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => 'No new schedule found in request_details.'
        ];
    }
}





        public function ApproveReservationStatus($reservation_id, $status){
            $stmt = $this->conn->prepare("UPDATE `reservations` SET `status` = ? WHERE `id` = ?");
            $stmt->bind_param("si", $status, $reservation_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Reservation status updated successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $stmt->error
                ];
            }
        }





       public function ApproveReservationStatus_with_validation($reservation_id, $status) {
    // First, get the reservation details
    $stmt = $this->conn->prepare("SELECT table_code, date_schedule, time_schedule, status FROM `reservations` WHERE id = ?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'success' => false,
            'message' => 'Reservation not found.'
        ];
    }

    $reservation = $result->fetch_assoc();

    // Only check if we're trying to approve to "confirmed"
    if ($status === 'confirmed') {
        $table_code = $reservation['table_code'];
        $date_schedule = $reservation['date_schedule'];
        $time_schedule = $reservation['time_schedule'];

        // Check if another reservation is already confirmed for the same table and schedule
        $checkStmt = $this->conn->prepare("
            SELECT COUNT(*) as cnt 
            FROM `reservations` 
            WHERE table_code = ? AND date_schedule = ? AND time_schedule = ? AND status = 'confirmed' AND id != ?
        ");
        $checkStmt->bind_param("sssi", $table_code, $date_schedule, $time_schedule, $reservation_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result()->fetch_assoc();

        if ($checkResult['cnt'] > 0) {
            return [
                'success' => false,
                'message' => 'Cannot approve. Another reservation is already confirmed for the same table and schedule.'
            ];
        }
    }

    // Proceed with the update
    $updateStmt = $this->conn->prepare("UPDATE `reservations` SET `status` = ? WHERE `id` = ?");
    $updateStmt->bind_param("si", $status, $reservation_id);

    if ($updateStmt->execute()) {
        return [
            'success' => true,
            'message' => 'Reservation status updated successfully.'
        ];
    } else {
        return [
            'success' => false,
            'message' => $updateStmt->error
        ];
    }
}





    public function fetch_all_menu() {
        $query = $this->conn->prepare("SELECT * FROM menu
        where menu_status='1'
        ORDER BY menu_id DESC");

        if ($query->execute()) {
            $result = $query->get_result();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return $data;
        }
        return []; 
    }







    public function fetch_all_users() {
        $query = $this->conn->prepare("SELECT * FROM user
        where user_status='1'
        ORDER BY user_id DESC");

        if ($query->execute()) {
            $result = $query->get_result();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return $data;
        }
        return []; 
    }



    public function AddMenu($menuName,$menuCategory,$menuDescription,$menuPrice,$menuImageFileName ) {
            $query = "INSERT INTO `menu` (`menu_name`,`menu_category`, `menu_description`, `menu_price`, `menu_image_banner`) 
                    VALUES (?,?,?,?,?)";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("sssss", $menuName,$menuCategory,$menuDescription,$menuPrice,$menuImageFileName);

            $result = $stmt->execute();

            if (!$result) {
                $stmt->close();
                return false;
            }

            $inserted_id = $this->conn->insert_id; 
            $stmt->close();

            return $inserted_id; 
        }










    public function createDeals($groupName,$groupDescription,$deal_type,$groupImageFileName,$entryExpiration) {
            $query = "INSERT INTO `deals` (`deal_name`,`deal_description`,`deal_img_banner`,`deal_type`,`deal_expiration`) 
                    VALUES (?,?,?,?,?)";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("sssss", $groupName,$groupDescription,$groupImageFileName,$deal_type,$entryExpiration);

            $result = $stmt->execute();

            if (!$result) {
                $stmt->close();
                return false;
            }

            $inserted_id = $this->conn->insert_id; 
            $stmt->close();

            return $inserted_id; 
        }





    
    public function Login($email, $password)
{
    $query = $this->conn->prepare("SELECT * FROM `user` WHERE `user_email` = ?");
    $query->bind_param("s", $email);

    if ($query->execute()) {
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['user_password'])) {
                // 🔍 Check if inactive
                if ($user['user_status'] == 0) {
                    $query->close();
                    return [
                        'success' => false,
                        'message' => 'Your account is not active.'
                    ];
                }

                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_fname'] = $user['user_fname'];
                $_SESSION['user_position'] = $user['user_position']; 

                $query->close();
                return [
                    'success' => true,
                    'message' => 'Login successful.',
                    'data' => [
                        'user_id' => $user['user_id'],
                        'user_fname' => $user['user_fname'],
                        'user_position' => $user['user_position'], 
                    ]
                ];
            } else {
                $query->close();
                return ['success' => false, 'message' => 'Incorrect password.'];
            }
        } else {
            $query->close();
            return ['success' => false, 'message' => 'User not found.'];
        }
    } else {
        $query->close();
        return ['success' => false, 'message' => 'Database error during execution.'];
    }
}




// Check if an email is already registered
public function isEmailExist($email) {
    $query = "SELECT user_id FROM `user` WHERE `user_email` = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    return $stmt->num_rows > 0; 
}


// Register a new customer
public function RegisterCustomer($first_name, $last_name, $email, $password) {
    // Use the separate function to check email
    if ($this->isEmailExist($email)) {
        return [
            'success' => false,
            'message' => 'Email already registered.'
        ];
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user
    $query = "INSERT INTO `user`(`user_fname`, `user_lname`, `user_email`, `user_password`, `user_position`) 
              VALUES (?, ?, ?, ?, 'customer')";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashedPassword);

    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Registration successful.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ];
    }
}













public function UpdateMenu(
                $menu_id,
                $menu_name,
                $menu_category,
                $menu_description,
                $menu_price,
                $menu_image_banner = null) {
    // Convert empty description to null
    $menu_description = trim($menu_description) === '' ? null : $menu_description;

    // Delete old image if new one is provided
    if ($menu_image_banner) {
        $stmt = $this->conn->prepare("SELECT menu_image_banner FROM menu WHERE menu_id = ?");
        $stmt->bind_param("s", $menu_id);
        $stmt->execute();
        $stmt->bind_result($oldBanner);
        $stmt->fetch();
        $stmt->close();

        if (!empty($oldBanner)) {
            $oldPath = "../../static/upload/" . $oldBanner;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
    }

    // Build query
    $query = "UPDATE menu SET menu_name = ?,menu_category=?, menu_description = ?, menu_price = ?";
    $types = "ssss";
    $params = [$menu_name,$menu_category, $menu_description, $menu_price];

    if ($menu_image_banner) {
        $query .= ", menu_image_banner = ?";
        $types .= "s";
        $params[] = $menu_image_banner;
    }

    $query .= " WHERE menu_id = ?";
    $types .= "s";
    $params[] = $menu_id;

    // Prepare and execute
    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        return ['status' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();

    return ['status' => true, 'message' => 'Menu updated successfully.'];
}













    public function removeMenu($menu_id) {
       
        $deleteQuery = "UPDATE menu SET menu_status = 0 WHERE menu_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (update): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $menu_id);
        $result = $stmt->execute();
        $stmt->close();

        return $result ? 'success' : 'Error updating menu';
    }





    public function removeUser($user_id) {
       
        $deleteQuery = "UPDATE user SET user_status = 0 WHERE user_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (update): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $stmt->close();

        return $result ? 'success' : 'Error updating menu';
    }







    public function fetch_all_deals($deal_type) {
        // Base query
        $queryStr = "SELECT * FROM deals";

        // Check if deal_type is not null, then add WHERE clause
        if (!is_null($deal_type)) {
            $queryStr .= " WHERE deal_type = ?";
        }

        // Always add ORDER BY
        $queryStr .= " ORDER BY deal_id DESC";

        $query = $this->conn->prepare($queryStr);

        if (!is_null($deal_type)) {
            $query->bind_param("s", $deal_type);
        }

        if ($query->execute()) {
            $result = $query->get_result();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return $data;
        }

        return [];
    }








public function fetch_all_deals_and_menu($deal_type) {
    $queryStr = "SELECT * FROM deals";
    $params = [];
    $types = "";

    if (!is_null($deal_type)) {
        if ($deal_type === 'promo_deals') {
            // Only promo_deals that are not expired
            $queryStr .= " WHERE deal_type = ? AND deal_expiration >= NOW()";
            $types = "s";
            $params[] = $deal_type;
        } else {
            // Other deal types no expiration filter
            $queryStr .= " WHERE deal_type = ?";
            $types = "s";
            $params[] = $deal_type;
        }
    }
    $queryStr .= " ORDER BY deal_id DESC";

    $query = $this->conn->prepare($queryStr);
    if ($types !== "") {
        $query->bind_param($types, ...$params);
    }

    if ($query->execute()) {
        $result = $query->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $dealIds = json_decode($row['deal_ids'], true);

            // Skip deals na walang laman o walang array
            if (empty($dealIds) || !is_array($dealIds)) {
                continue;
            }

            $totalPrice = 0;
            $menus = [];

            // Prepare IN clause for menu ids
            $placeholders = implode(',', array_fill(0, count($dealIds), '?'));
            $typesMenu = str_repeat('i', count($dealIds));

            $sqlMenu = "SELECT * FROM menu WHERE menu_id IN ($placeholders)";
            $stmtMenu = $this->conn->prepare($sqlMenu);

            // Mysqli doesn't support unpacking directly in bind_param, need workaround:
            $bindParams = [];
            $bindParams[] = &$typesMenu;
            foreach ($dealIds as $key => $id) {
                $bindParams[] = &$dealIds[$key];
            }
            call_user_func_array([$stmtMenu, 'bind_param'], $bindParams);

            $stmtMenu->execute();
            $resMenu = $stmtMenu->get_result();

            while ($menu = $resMenu->fetch_assoc()) {
                $menus[] = $menu;
                $totalPrice += $menu['menu_price'];
            }

            $row['menus'] = $menus;
            $row['total_price'] = $totalPrice;

            $data[] = $row;
        }

        return $data;
    }

    return [];
}











     public function AddMenuDeals($menu_id, $deal_id) {
            // Step 1: Kunin ang existing deal_ids
            $query = "SELECT deal_ids FROM deals WHERE deal_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $deal_id);
            $stmt->execute();
            $stmt->bind_result($deal_ids_json);
            $stmt->fetch();
            $stmt->close();

            // Step 2: Convert JSON to array
            $deal_ids = json_decode($deal_ids_json, true);

            // If null or not an array, initialize as array
            if (!is_array($deal_ids)) {
                $deal_ids = [];
            }

            // Step 3: Append menu_id if not yet in the array
            if (!in_array($menu_id, $deal_ids)) {
                $deal_ids[] = $menu_id;
            }

            // Step 4: Encode back to JSON
            $new_deal_ids_json = json_encode($deal_ids);

            // Step 5: Update query
            $updateQuery = "UPDATE deals SET deal_ids = ? WHERE deal_id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $new_deal_ids_json, $deal_id);

            $result = $updateStmt->execute();
            $updateStmt->close();

            return $result;
        }


   public function remove_deal_ids($menu_id, $deal_id) {
        // Step 1: Kunin ang existing deal_ids
        $query = "SELECT deal_ids FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $deal_id);
        $stmt->execute();
        $stmt->bind_result($deal_ids_json);
        $stmt->fetch();
        $stmt->close();

        // Step 2: Convert JSON to array
        $deal_ids = json_decode($deal_ids_json, true);

        // If null or not an array, initialize as array
        if (!is_array($deal_ids)) {
            $deal_ids = [];
        }

        // Step 3: Remove menu_id from array if present
        if (in_array($menu_id, $deal_ids)) {
            $deal_ids = array_filter($deal_ids, function($id) use ($menu_id) {
                return $id != $menu_id;
            });

            // Re-index array to maintain proper JSON format
            $deal_ids = array_values($deal_ids);
        }

        // Step 4: Encode back to JSON
        $new_deal_ids_json = json_encode($deal_ids);

        // Step 5: Update query
        $updateQuery = "UPDATE deals SET deal_ids = ? WHERE deal_id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $new_deal_ids_json, $deal_id);

        $result = $updateStmt->execute();
        $updateStmt->close();

        return $result;
    }



  public function GetAllDealsWithMenus_byId($dealId) {
    $query = "SELECT deal_id, deal_name, deal_ids FROM deals WHERE deal_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $dealId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $deal_ids = json_decode($row['deal_ids'], true);
        if (!is_array($deal_ids)) {
            $deal_ids = [];
        }

        $deal = [
            'deal_id' => $row['deal_id'],
            'deal_name' => $row['deal_name'],
            'deal_ids' => $deal_ids,
            'menus' => []
        ];

        if (count($deal_ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($deal_ids), '?'));
            $menuQuery = "SELECT * FROM menu WHERE menu_id IN ($placeholders)";
            $menuStmt = $this->conn->prepare($menuQuery);

            $types = str_repeat('i', count($deal_ids)); 
            $menuStmt->bind_param($types, ...$deal_ids);

            $menuStmt->execute();
            $menuResult = $menuStmt->get_result();

            $menus = [];
            while ($menuRow = $menuResult->fetch_assoc()) {
                $menus[$menuRow['menu_id']] = $menuRow;
            }

            foreach ($deal_ids as $id) {
                if (isset($menus[$id])) {
                    $deal['menus'][] = $menus[$id];
                }
            }

            $menuStmt->close();
        }

        $stmt->close();
        return $deal; 
    }

    $stmt->close();
    return null; 
}










 public function removeDeals($deal_id) {
        // Step 1: Get the banner filename from the database
        $selectQuery = "SELECT deal_img_banner FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($selectQuery);
        if (!$stmt) {
            return 'Prepare failed (select): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $deal_id);
        $stmt->execute();
        $stmt->bind_result($bannerFile);
        $stmt->fetch();
        $stmt->close();

        // Step 2: Delete the record from the database
        $deleteQuery = "DELETE FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (delete): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $deal_id);
        $result = $stmt->execute();
        $stmt->close();

        // Step 3: Delete the file from the filesystem
        if ($result && $bannerFile) {
            $filePath = __DIR__ . "../../static/upload/" . $bannerFile;
            if (file_exists($filePath)) {
                unlink($filePath); // deletes the image file
            }
        }

        return $result ? 'success' : 'Error deleting event';
    }







    public function RequestReservation(
        $table_code,
        $seats,
        $date_schedule,
        $time_schedule,
        $menu_select,
        $promo_select,
        $group_select,
        $selected_menus,
        $selected_promos,
        $selected_groups,
        $menu_total,
        $promo_total,
        $group_total,
        $grand_total,
        $entryImageFileName,
        $user_id,
        $termsFileSignedFileName
    ) {
        // Generate unique code
        $uniqueCode = $this->generateUniqueCode();

        $sql = "INSERT INTO reservations (
            table_code,
            seats,
            date_schedule,
            time_schedule,
            selected_menus,
            selected_promos,
            selected_groups,
            menu_total,
            promo_total,
            group_total,
            grand_total,
            proof_of_payment,
            reserve_user_id,
            reserve_unique_code,
            termsFileSigned
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param(
            "sissssssdddssss",
            $table_code,
            $seats,
            $date_schedule,
            $time_schedule,
            $selected_menus,
            $selected_promos,
            $selected_groups,
            $menu_total,
            $promo_total,
            $group_total,
            $grand_total,
            $entryImageFileName,
            $user_id,
            $uniqueCode,
            $termsFileSignedFileName
        );

        $result = $stmt->execute();

        if (!$result) {
            die("Execute failed: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }



    private function generateUniqueCode($length = 8) {
        do {
            // Gumawa ng random alphanumeric code
            $code = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length));
            
            // Check kung existing na sa database
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE reserve_unique_code = ?");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        } while ($count > 0); 

        return $code;
    }




    
public function checkAvailability($table_code, $date_schedule, $time_schedule) {
    // 1. Kunin day of week
    $dayOfWeek = date('l', strtotime($date_schedule)); // e.g. "Sunday"

    // 2. Kunin open/close time sa business_hours
    $scheduleQuery = $this->conn->prepare("
        SELECT open_time, close_time 
        FROM business_hours 
        WHERE day_of_week = ?
    ");
    $scheduleQuery->bind_param("s", $dayOfWeek);
    $scheduleQuery->execute();
    $scheduleResult = $scheduleQuery->get_result();
    $hours = $scheduleResult->fetch_assoc();

    if (!$hours) {
        return [
            "status" => 200,
            "available" => false,
            "dayOfWeek" => $dayOfWeek,
            "reason" => "no_schedule"
        ];
    }

    $openTime  = $hours['open_time'];   // e.g., "17:00:00"
    $closeTime = $hours['close_time'];  // e.g., "03:00:00"

    // 3. Convert times to DateTime for reliable comparison
    $time       = DateTime::createFromFormat('H:i', $time_schedule);
    $open       = DateTime::createFromFormat('H:i:s', $openTime);
    $close      = DateTime::createFromFormat('H:i:s', $closeTime);

    // Handle overnight schedules (close time < open time)
    if ($close <= $open) {
        $close->modify('+1 day');  // close is next day
        if ($time < $open) {
            $time->modify('+1 day'); // adjust time if before open (after midnight)
        }
    }

    $isAvailableTime = ($time >= $open && $time <= $close);

    if (!$isAvailableTime) {
        return [
            "status" => 200,
            "available" => false,
            "dayOfWeek" => $dayOfWeek,
            "open_time" => $openTime,
            "close_time" => $closeTime,
            "reason" => "outside_hours"
        ];
    }

    // 4. Check existing confirmed reservations (same table, same date)
    $query = $this->conn->prepare("
        SELECT * FROM reservations 
        WHERE table_code = ? 
        AND date_schedule = ? 
        AND status = 'confirmed'
    ");
    $query->bind_param("ss", $table_code, $date_schedule);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $conflicts = [];
        while ($row = $result->fetch_assoc()) {
            $conflicts[] = $row;
        }

        return [
            "status" => 200,
            "available" => false,
            "dayOfWeek" => $dayOfWeek,
            "open_time" => $openTime,
            "close_time" => $closeTime,
            "reason" => "conflict",
            "conflicts" => $conflicts
        ];
    }

    // 5. Available
    return [
        "status" => 200,
        "available" => true,
        "dayOfWeek" => $dayOfWeek,
        "open_time" => $openTime,
        "close_time" => $closeTime
    ];
}








public function fetch_all_reserve_request($limit, $offset) {
    // Step 1: Get reservations only
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        LEFT JOIN user
        ON reservations.reserve_user_id = user.user_id 
        WHERE status = 'pending' OR status = 'request cancel'
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ");
    $query->bind_param("ii", $limit, $offset);
    $query->execute();
    $result = $query->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC);

    if (!$reservations) return [];

    // Step 2: Collect all unique menu, promo, and group IDs
    $menu_ids = [];
    $promo_ids = [];
    $group_ids = [];

    foreach ($reservations as $res) {
        $menus = json_decode($res['selected_menus'], true) ?: [];
        $promos = json_decode($res['selected_promos'], true) ?: [];
        $groups = json_decode($res['selected_groups'], true) ?: [];

        foreach ($menus as $m) {
            $id = (int)$m['id'];
            if (!in_array($id, $menu_ids, true)) $menu_ids[] = $id;
        }
        foreach ($promos as $p) {
            $id = (int)$p['id'];
            if (!in_array($id, $promo_ids, true)) $promo_ids[] = $id;
        }
        foreach ($groups as $g) {
            $id = (int)$g['id'];
            if (!in_array($id, $group_ids, true)) $group_ids[] = $id;
        }
    }

    // Step 3: Fetch menus
    $menus = [];
    if ($menu_ids) {
        $placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
        $types = str_repeat('i', count($menu_ids));
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($placeholders)");
        $stmt->bind_param($types, ...$menu_ids);
        $stmt->execute();
        $resMenus = $stmt->get_result();
        while ($row = $resMenus->fetch_assoc()) {
            $menus[$row['menu_id']] = $row;
        }
    }

    // Step 4: Fetch promos
    $promos = [];
    if ($promo_ids) {
        $placeholders = implode(',', array_fill(0, count($promo_ids), '?'));
        $types = str_repeat('i', count($promo_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
        $stmt->bind_param($types, ...$promo_ids);
        $stmt->execute();
        $resPromos = $stmt->get_result();
        while ($row = $resPromos->fetch_assoc()) {
            $promos[$row['deal_id']] = $row;
        }
    }

    // Step 5: Fetch groups (assuming groups are stored in `deals` table like promos)
    $groups = [];
    if ($group_ids) {
        $placeholders = implode(',', array_fill(0, count($group_ids), '?'));
        $types = str_repeat('i', count($group_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
        $stmt->bind_param($types, ...$group_ids);
        $stmt->execute();
        $resGroups = $stmt->get_result();
        while ($row = $resGroups->fetch_assoc()) {
            $groups[$row['deal_id']] = $row;
        }
    }

    // Step 6: Attach full menu, promo, and group details to reservations
    foreach ($reservations as &$res) {
        $resMenus = json_decode($res['selected_menus'], true) ?: [];
        foreach ($resMenus as &$menuItem) {
            $menuId = (int)$menuItem['id'];
            if (isset($menus[$menuId])) {
                $menuItem['details'] = $menus[$menuId];
            }
        }
        $res['menus_details'] = $resMenus;

        $resPromos = json_decode($res['selected_promos'], true) ?: [];
        foreach ($resPromos as &$promoItem) {
            $promoId = (int)$promoItem['id'];
            if (isset($promos[$promoId])) {
                $promoItem['details'] = $promos[$promoId];
            }
        }
        $res['promos_details'] = $resPromos;

        $resGroups = json_decode($res['selected_groups'], true) ?: [];
        foreach ($resGroups as &$groupItem) {
            $groupId = (int)$groupItem['id'];
            if (isset($groups[$groupId])) {
                $groupItem['details'] = $groups[$groupId];
            }
        }
        $res['groups_details'] = $resGroups;
    }

    return $reservations;
}


public function count_all_reserve_request() {
    $result = $this->conn->query("
        SELECT COUNT(*) as total 
        FROM reservations where status='pending'
    ");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
} 



// ALL RESERVED


public function getCompletedReservations() {
    $sql = "SELECT r.*, u.user_fname, u.user_lname, u.user_email
            FROM reservations r
            JOIN user u ON r.reserve_user_id = u.user_id
            WHERE r.status = 'completed'
            ORDER BY r.date_schedule DESC, r.time_schedule DESC";
    $result = $this->conn->query($sql);

    $data = [];
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){

            // Helper function to decode JSON and return array of objects
            $decodeItems = function($json) {
                $items = json_decode($json, true);
                if(!$items || !is_array($items)) return [];
                
                // Keep only relevant fields
                return array_map(function($item) {
                    return [
                        'name' => $item['name'],
                        'price'=> $item['price'],
                        'qty'  => $item['qty']
                    ];
                }, $items);
            };

            $row['selected_menus']  = $decodeItems($row['selected_menus']);
            $row['selected_promos'] = $decodeItems($row['selected_promos']);
            $row['selected_groups'] = $decodeItems($row['selected_groups']);

            $data[] = $row;
        }
    }
    return $data;
}







public function fetch_all_reserved($limit, $offset) {
    // Step 1: Get reservations only
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        LEFT JOIN user
        ON reservations.reserve_user_id = user.user_id 
        WHERE archived_by_admin='0' AND (status = 'confirmed' || status = 'completed'|| status = 'request new schedule'|| status = 'cancelled')
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ");
    $query->bind_param("ii", $limit, $offset);
    $query->execute();
    $result = $query->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC);

    if (!$reservations) return [];

    // Step 2: Collect all unique menu, promo, and group IDs
    $menu_ids = [];
    $promo_ids = [];
    $group_ids = [];

    foreach ($reservations as $res) {
        $menus = json_decode($res['selected_menus'], true) ?: [];
        $promos = json_decode($res['selected_promos'], true) ?: [];
        $groups = json_decode($res['selected_groups'], true) ?: [];

        foreach ($menus as $m) {
            $id = (int)$m['id'];
            if (!in_array($id, $menu_ids, true)) $menu_ids[] = $id;
        }
        foreach ($promos as $p) {
            $id = (int)$p['id'];
            if (!in_array($id, $promo_ids, true)) $promo_ids[] = $id;
        }
        foreach ($groups as $g) {
            $id = (int)$g['id'];
            if (!in_array($id, $group_ids, true)) $group_ids[] = $id;
        }
    }

    // Step 3: Fetch menus
    $menus = [];
    if ($menu_ids) {
        $placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
        $types = str_repeat('i', count($menu_ids));
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($placeholders)");
        $stmt->bind_param($types, ...$menu_ids);
        $stmt->execute();
        $resMenus = $stmt->get_result();
        while ($row = $resMenus->fetch_assoc()) {
            $menus[$row['menu_id']] = $row;
        }
    }

    // Step 4: Fetch promos
    $promos = [];
    if ($promo_ids) {
        $placeholders = implode(',', array_fill(0, count($promo_ids), '?'));
        $types = str_repeat('i', count($promo_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
        $stmt->bind_param($types, ...$promo_ids);
        $stmt->execute();
        $resPromos = $stmt->get_result();
        while ($row = $resPromos->fetch_assoc()) {
            $promos[$row['deal_id']] = $row;
        }
    }

    // Step 5: Fetch groups (assuming groups are stored in `deals` table like promos)
    $groups = [];
    if ($group_ids) {
        $placeholders = implode(',', array_fill(0, count($group_ids), '?'));
        $types = str_repeat('i', count($group_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
        $stmt->bind_param($types, ...$group_ids);
        $stmt->execute();
        $resGroups = $stmt->get_result();
        while ($row = $resGroups->fetch_assoc()) {
            $groups[$row['deal_id']] = $row;
        }
    }

    // Step 6: Attach full menu, promo, and group details to reservations
    foreach ($reservations as &$res) {
        $resMenus = json_decode($res['selected_menus'], true) ?: [];
        foreach ($resMenus as &$menuItem) {
            $menuId = (int)$menuItem['id'];
            if (isset($menus[$menuId])) {
                $menuItem['details'] = $menus[$menuId];
            }
        }
        $res['menus_details'] = $resMenus;

        $resPromos = json_decode($res['selected_promos'], true) ?: [];
        foreach ($resPromos as &$promoItem) {
            $promoId = (int)$promoItem['id'];
            if (isset($promos[$promoId])) {
                $promoItem['details'] = $promos[$promoId];
            }
        }
        $res['promos_details'] = $resPromos;

        $resGroups = json_decode($res['selected_groups'], true) ?: [];
        foreach ($resGroups as &$groupItem) {
            $groupId = (int)$groupItem['id'];
            if (isset($groups[$groupId])) {
                $groupItem['details'] = $groups[$groupId];
            }
        }
        $res['groups_details'] = $resGroups;
    }

    return $reservations;
}




        public function count_all_reserved() {
            $result = $this->conn->query("
                SELECT COUNT(*) as total 
                FROM reservations where archived_by_admin='0' AND (status = 'confirmed' || status = 'completed'|| status = 'request new schedule'|| status = 'cancelled')
            ");
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        } 













    public function fetch_all_reserved_archived($limit, $offset,$collumn) {
            // Step 1: Get reservations only
            $query = $this->conn->prepare("
                SELECT * FROM reservations
                WHERE $collumn = '1'
                ORDER BY id DESC
                LIMIT ? OFFSET ?
            ");
            $query->bind_param("ii", $limit, $offset);
            $query->execute();
            $result = $query->get_result();
            $reservations = $result->fetch_all(MYSQLI_ASSOC);

            if (!$reservations) return [];

            // Step 2: Collect all unique menu, promo, and group IDs
            $menu_ids = [];
            $promo_ids = [];
            $group_ids = [];

            foreach ($reservations as $res) {
                $menus = json_decode($res['selected_menus'], true) ?: [];
                $promos = json_decode($res['selected_promos'], true) ?: [];
                $groups = json_decode($res['selected_groups'], true) ?: [];

                foreach ($menus as $m) {
                    $id = (int)$m['id'];
                    if (!in_array($id, $menu_ids, true)) $menu_ids[] = $id;
                }
                foreach ($promos as $p) {
                    $id = (int)$p['id'];
                    if (!in_array($id, $promo_ids, true)) $promo_ids[] = $id;
                }
                foreach ($groups as $g) {
                    $id = (int)$g['id'];
                    if (!in_array($id, $group_ids, true)) $group_ids[] = $id;
                }
            }

            // Step 3: Fetch menus
            $menus = [];
            if ($menu_ids) {
                $placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
                $types = str_repeat('i', count($menu_ids));
                $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($placeholders)");
                $stmt->bind_param($types, ...$menu_ids);
                $stmt->execute();
                $resMenus = $stmt->get_result();
                while ($row = $resMenus->fetch_assoc()) {
                    $menus[$row['menu_id']] = $row;
                }
            }

            // Step 4: Fetch promos
            $promos = [];
            if ($promo_ids) {
                $placeholders = implode(',', array_fill(0, count($promo_ids), '?'));
                $types = str_repeat('i', count($promo_ids));
                $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
                $stmt->bind_param($types, ...$promo_ids);
                $stmt->execute();
                $resPromos = $stmt->get_result();
                while ($row = $resPromos->fetch_assoc()) {
                    $promos[$row['deal_id']] = $row;
                }
            }

            // Step 5: Fetch groups (assuming groups are stored in `deals` table like promos)
            $groups = [];
            if ($group_ids) {
                $placeholders = implode(',', array_fill(0, count($group_ids), '?'));
                $types = str_repeat('i', count($group_ids));
                $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
                $stmt->bind_param($types, ...$group_ids);
                $stmt->execute();
                $resGroups = $stmt->get_result();
                while ($row = $resGroups->fetch_assoc()) {
                    $groups[$row['deal_id']] = $row;
                }
            }

            // Step 6: Attach full menu, promo, and group details to reservations
            foreach ($reservations as &$res) {
                $resMenus = json_decode($res['selected_menus'], true) ?: [];
                foreach ($resMenus as &$menuItem) {
                    $menuId = (int)$menuItem['id'];
                    if (isset($menus[$menuId])) {
                        $menuItem['details'] = $menus[$menuId];
                    }
                }
                $res['menus_details'] = $resMenus;

                $resPromos = json_decode($res['selected_promos'], true) ?: [];
                foreach ($resPromos as &$promoItem) {
                    $promoId = (int)$promoItem['id'];
                    if (isset($promos[$promoId])) {
                        $promoItem['details'] = $promos[$promoId];
                    }
                }
                $res['promos_details'] = $resPromos;

                $resGroups = json_decode($res['selected_groups'], true) ?: [];
                foreach ($resGroups as &$groupItem) {
                    $groupId = (int)$groupItem['id'];
                    if (isset($groups[$groupId])) {
                        $groupItem['details'] = $groups[$groupId];
                    }
                }
                $res['groups_details'] = $resGroups;
            }

            return $reservations;
        }




        public function count_all_reserved_archived() {
            $result = $this->conn->query("
                SELECT COUNT(*) as total 
                FROM reservations where status = 'archived'
            ");
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        } 








      public function fetch_reservation($reservations_id) {
        $query = $this->conn->prepare(" SELECT *
        FROM reservations
        LEFT JOIN user
        ON user.user_id  = reservations.reserve_user_id
        where id = $reservations_id");
        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }


// Customer Reserved


public function fetch_all_customer_reservation($limit, $offset,$user_id) {
    // Step 1: Get reservations only
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        WHERE reserve_user_id = $user_id
        AND archived_by_customer=0
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ");
    $query->bind_param("ii", $limit, $offset);
    $query->execute();
    $result = $query->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC);

    if (!$reservations) return [];

    // Step 2: Collect all unique menu, promo, and group IDs
    $menu_ids = [];
    $promo_ids = [];
    $group_ids = [];

    foreach ($reservations as $res) {
        $menus = json_decode($res['selected_menus'], true) ?: [];
        $promos = json_decode($res['selected_promos'], true) ?: [];
        $groups = json_decode($res['selected_groups'], true) ?: [];

        foreach ($menus as $m) {
            $id = (int)$m['id'];
            if (!in_array($id, $menu_ids, true)) $menu_ids[] = $id;
        }
        foreach ($promos as $p) {
            $id = (int)$p['id'];
            if (!in_array($id, $promo_ids, true)) $promo_ids[] = $id;
        }
        foreach ($groups as $g) {
            $id = (int)$g['id'];
            if (!in_array($id, $group_ids, true)) $group_ids[] = $id;
        }
    }

    // Step 3: Fetch menus
    $menus = [];
    if ($menu_ids) {
        $placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
        $types = str_repeat('i', count($menu_ids));
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($placeholders)");
        $stmt->bind_param($types, ...$menu_ids);
        $stmt->execute();
        $resMenus = $stmt->get_result();
        while ($row = $resMenus->fetch_assoc()) {
            $menus[$row['menu_id']] = $row;
        }
    }

    // Step 4: Fetch promos
    $promos = [];
    if ($promo_ids) {
        $placeholders = implode(',', array_fill(0, count($promo_ids), '?'));
        $types = str_repeat('i', count($promo_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
        $stmt->bind_param($types, ...$promo_ids);
        $stmt->execute();
        $resPromos = $stmt->get_result();
        while ($row = $resPromos->fetch_assoc()) {
            $promos[$row['deal_id']] = $row;
        }
    }

    // Step 5: Fetch groups (assuming groups are stored in `deals` table like promos)
    $groups = [];
    if ($group_ids) {
        $placeholders = implode(',', array_fill(0, count($group_ids), '?'));
        $types = str_repeat('i', count($group_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
        $stmt->bind_param($types, ...$group_ids);
        $stmt->execute();
        $resGroups = $stmt->get_result();
        while ($row = $resGroups->fetch_assoc()) {
            $groups[$row['deal_id']] = $row;
        }
    }

    // Step 6: Attach full menu, promo, and group details to reservations
    foreach ($reservations as &$res) {
        $resMenus = json_decode($res['selected_menus'], true) ?: [];
        foreach ($resMenus as &$menuItem) {
            $menuId = (int)$menuItem['id'];
            if (isset($menus[$menuId])) {
                $menuItem['details'] = $menus[$menuId];
            }
        }
        $res['menus_details'] = $resMenus;

        $resPromos = json_decode($res['selected_promos'], true) ?: [];
        foreach ($resPromos as &$promoItem) {
            $promoId = (int)$promoItem['id'];
            if (isset($promos[$promoId])) {
                $promoItem['details'] = $promos[$promoId];
            }
        }
        $res['promos_details'] = $resPromos;

        $resGroups = json_decode($res['selected_groups'], true) ?: [];
        foreach ($resGroups as &$groupItem) {
            $groupId = (int)$groupItem['id'];
            if (isset($groups[$groupId])) {
                $groupItem['details'] = $groups[$groupId];
            }
        }
        $res['groups_details'] = $resGroups;
    }

    return $reservations;
}









public function fetch_all_reservations_today() {
    $query = "SELECT table_code FROM reservations 
              WHERE DATE(date_schedule) = CURDATE() AND STATUS ='confirmed' 
              AND status NOT IN ('cancelled')";
    $result = $this->conn->query($query);

    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row['table_code'];
        }
    }
    return $data; // returns array of table codes reserved today
}









public function count_all_customer_reservation($user_id) {
    $result = $this->conn->query("
        SELECT COUNT(*) as total 
        FROM reservations WHERE reserve_user_id = $user_id
    ");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
} 

      










        public function cancel_reservation($reservationId) {
            // Prepare statement
            $stmt = $this->conn->prepare("UPDATE reservations SET status = 'request cancel' WHERE id = ?");
            
            if ($stmt) {
                // Bind parameter
                $stmt->bind_param("i", $reservationId); 
                
                // Execute statement
                if ($stmt->execute()) {
                    $stmt->close();
                    return [
                        'success' => true,
                        'message' => 'Request canceled successfully.'
                    ];
                } else {
                    $stmt->close();
                    return [
                        'success' => false,
                        'message' => 'Failed to cancel appointment. Please try again.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare the statement.'
                ];
            }
        }








        public function reschedule($reservationId, $reason,$seats, $newDate,$newTime) {
            // Create JSON from reason and new date
            $requestDetails = json_encode([
                'reason' => $reason,
                'newDate' => $newDate,
                'newTime' => $newTime,
                'seats' => $seats
            ]);

            // Prepare statement
            $stmt = $this->conn->prepare(
                "UPDATE reservations 
                SET status = 'request new schedule', request_details = ? 
                WHERE id = ?"
            );

            if ($stmt) {
                // Bind parameters: s = string, i = integer
                $stmt->bind_param("si", $requestDetails, $reservationId);

                // Execute statement
                if ($stmt->execute()) {
                    $stmt->close();
                    return [
                        'success' => true,
                        'message' => 'Request sent successfully.'
                    ];
                } else {
                    $stmt->close();
                    return [
                        'success' => false,
                        'message' => 'Failed to send request. Please try again.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare the statement.'
                ];
            }
        }










//FOR ADMIN

public function fetch_all_admin_reservation_no_limit() {
    // Step 1: Get only today's reservations (not archived)
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        WHERE archived_by_admin = 0
        AND DATE(date_schedule) = CURDATE()
        ORDER BY id DESC
    ");
    $query->execute();
    $result = $query->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC) ?: [];

    // Step 2: Collect unique menu, promo, and group IDs
    $menu_ids = $promo_ids = $group_ids = [];
    foreach ($reservations as &$res) {
        $menus = json_decode($res['selected_menus'], true) ?: [];
        $promos = json_decode($res['selected_promos'], true) ?: [];
        $groups = json_decode($res['selected_groups'], true) ?: [];

        foreach ($menus as $m) if (!in_array((int)$m['id'], $menu_ids, true)) $menu_ids[] = (int)$m['id'];
        foreach ($promos as $p) if (!in_array((int)$p['id'], $promo_ids, true)) $promo_ids[] = (int)$p['id'];
        foreach ($groups as $g) if (!in_array((int)$g['id'], $group_ids, true)) $group_ids[] = (int)$g['id'];

        $res['is_walkin'] = false;
        $res['source'] = 'reservation';
    }

    // Step 3–5: Fetch related details
    $menus = $promos = $groups = [];

    if ($menu_ids) {
        $in = implode(',', array_fill(0, count($menu_ids), '?'));
        $types = str_repeat('i', count($menu_ids));
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($in)");
        $stmt->bind_param($types, ...$menu_ids);
        $stmt->execute();
        $resMenus = $stmt->get_result();
        while ($row = $resMenus->fetch_assoc()) $menus[$row['menu_id']] = $row;
    }

    if ($promo_ids) {
        $in = implode(',', array_fill(0, count($promo_ids), '?'));
        $types = str_repeat('i', count($promo_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
        $stmt->bind_param($types, ...$promo_ids);
        $stmt->execute();
        $resPromos = $stmt->get_result();
        while ($row = $resPromos->fetch_assoc()) $promos[$row['deal_id']] = $row;
    }

    if ($group_ids) {
        $in = implode(',', array_fill(0, count($group_ids), '?'));
        $types = str_repeat('i', count($group_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
        $stmt->bind_param($types, ...$group_ids);
        $stmt->execute();
        $resGroups = $stmt->get_result();
        while ($row = $resGroups->fetch_assoc()) $groups[$row['deal_id']] = $row;
    }

    // Step 6: Attach details
    foreach ($reservations as &$res) {
        $resMenus = json_decode($res['selected_menus'], true) ?: [];
        foreach ($resMenus as &$menuItem) $menuItem['details'] = $menus[(int)$menuItem['id']] ?? [];
        $res['menus_details'] = $resMenus;

        $resPromos = json_decode($res['selected_promos'], true) ?: [];
        foreach ($resPromos as &$promoItem) $promoItem['details'] = $promos[(int)$promoItem['id']] ?? [];
        $res['promos_details'] = $resPromos;

        $resGroups = json_decode($res['selected_groups'], true) ?: [];
        foreach ($resGroups as &$groupItem) $groupItem['details'] = $groups[(int)$groupItem['id']] ?? [];
        $res['groups_details'] = $resGroups;
    }

    // Step 7: Fetch walk-in tables that are unavailable today
    $walkins = [];
    $result = $this->conn->query("
        SELECT * FROM walkin_tables 
        WHERE walkin_status = 'unavailable' 
        AND DATE(walkin_created_at) = CURDATE()
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $walkins[] = [
                'id' => $row['walkin_id'],
                'table_code' => $row['walkin_table_code'],
                'status' => $row['walkin_status'],
                'is_walkin' => true,
                'source' => 'walkin'
            ];
        }
    }

    // Step 8: Merge results
    return array_merge($reservations, $walkins);
}





// FOR CUSTOMER
public function fetch_all_customer_reservation_no_limit($user_id) {
    // Step 1: Get only today's reservations for this user
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        WHERE reserve_user_id = ?
        AND archived_by_customer = 0
        AND DATE(date_schedule) = CURDATE()
        ORDER BY id DESC
    ");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC) ?: [];

    // Step 2: Collect unique IDs
    $menu_ids = $promo_ids = $group_ids = [];
    foreach ($reservations as &$res) {
        $menus = json_decode($res['selected_menus'], true) ?: [];
        $promos = json_decode($res['selected_promos'], true) ?: [];
        $groups = json_decode($res['selected_groups'], true) ?: [];

        foreach ($menus as $m) if (!in_array((int)$m['id'], $menu_ids, true)) $menu_ids[] = (int)$m['id'];
        foreach ($promos as $p) if (!in_array((int)$p['id'], $promo_ids, true)) $promo_ids[] = (int)$p['id'];
        foreach ($groups as $g) if (!in_array((int)$g['id'], $group_ids, true)) $group_ids[] = (int)$g['id'];

        $res['is_walkin'] = false;
        $res['source'] = 'reservation';
    }

    // Step 3–5: Fetch menu, promo, group details
    $menus = $promos = $groups = [];

    if ($menu_ids) {
        $in = implode(',', array_fill(0, count($menu_ids), '?'));
        $types = str_repeat('i', count($menu_ids));
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($in)");
        $stmt->bind_param($types, ...$menu_ids);
        $stmt->execute();
        $resMenus = $stmt->get_result();
        while ($row = $resMenus->fetch_assoc()) $menus[$row['menu_id']] = $row;
    }

    if ($promo_ids) {
        $in = implode(',', array_fill(0, count($promo_ids), '?'));
        $types = str_repeat('i', count($promo_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
        $stmt->bind_param($types, ...$promo_ids);
        $stmt->execute();
        $resPromos = $stmt->get_result();
        while ($row = $resPromos->fetch_assoc()) $promos[$row['deal_id']] = $row;
    }

    if ($group_ids) {
        $in = implode(',', array_fill(0, count($group_ids), '?'));
        $types = str_repeat('i', count($group_ids));
        $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
        $stmt->bind_param($types, ...$group_ids);
        $stmt->execute();
        $resGroups = $stmt->get_result();
        while ($row = $resGroups->fetch_assoc()) $groups[$row['deal_id']] = $row;
    }

    // Step 6: Attach details
    foreach ($reservations as &$res) {
        $resMenus = json_decode($res['selected_menus'], true) ?: [];
        foreach ($resMenus as &$menuItem) $menuItem['details'] = $menus[(int)$menuItem['id']] ?? [];
        $res['menus_details'] = $resMenus;

        $resPromos = json_decode($res['selected_promos'], true) ?: [];
        foreach ($resPromos as &$promoItem) $promoItem['details'] = $promos[(int)$promoItem['id']] ?? [];
        $res['promos_details'] = $resPromos;

        $resGroups = json_decode($res['selected_groups'], true) ?: [];
        foreach ($resGroups as &$groupItem) $groupItem['details'] = $groups[(int)$groupItem['id']] ?? [];
        $res['groups_details'] = $resGroups;
    }

    // Step 7: Fetch today's walk-ins that are unavailable
    $walkins = [];
    $result = $this->conn->query("
        SELECT * FROM walkin_tables 
        WHERE walkin_status = 'unavailable' 
        AND DATE(walkin_created_at) = CURDATE()
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $walkins[] = [
                'id' => $row['walkin_id'],
                'table_code' => $row['walkin_table_code'],
                'status' => $row['walkin_status'],
                'is_walkin' => true,
                'source' => 'walkin'
            ];
        }
    }

    // Step 8: Merge both sets
    return array_merge($reservations, $walkins);
}








// Set table as unavailable for walk-ins (delete old if exists, insert new)
public function set_table_unavailable_walking($table_code) {
    // Step 1: Delete existing record with the same table code (if any)
    $delete_sql = "DELETE FROM walkin_tables WHERE walkin_table_code = ?";
    $stmt_delete = $this->conn->prepare($delete_sql);
    $stmt_delete->bind_param("s", $table_code);
    $stmt_delete->execute();

    // Step 2: Insert a new unavailable record
    $insert_sql = "INSERT INTO walkin_tables (walkin_table_code, walkin_status)
                   VALUES (?, 'unavailable')";
    $stmt_insert = $this->conn->prepare($insert_sql);
    $stmt_insert->bind_param("s", $table_code);

    return $stmt_insert->execute();
}

// Set table as available (delete the unavailable entry if exists)
public function set_table_available_from_walkin($table_code) {
    // Delete the record entirely when it becomes available
    $delete_sql = "DELETE FROM walkin_tables WHERE walkin_table_code = ?";
    $stmt = $this->conn->prepare($delete_sql);
    $stmt->bind_param("s", $table_code);

    return $stmt->execute();
}







}
