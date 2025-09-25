
<?php


include ('config.php');

date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
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





  public function RegisterCustomer($first_name, $last_name, $email, $password) {
    // Check if the email already exists
    $checkQuery = "SELECT user_id FROM `user` WHERE `user_email` = ?";
    $checkStmt = $this->conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
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






        
    public function checkTableAvailability($table_code, $date_schedule) {
        $query = $this->conn->prepare("
            SELECT * FROM reservations 
            WHERE table_code = ? 
            AND date_schedule = ? 
            AND status = 'confirmed'
        ");
    $query->bind_param("ss", $table_code, $date_schedule);

    if ($query->execute()) {
        $result = $query->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // If there are confirmed reservations, table is NOT available
        if (count($data) > 0) {
            return false; // NOT available
        } else {
            return true;  // available
        }
    }
    return false;
}



public function fetch_all_reserve_request($limit, $offset) {
    // Step 1: Get reservations only
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        WHERE status = 'pending'
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


public function fetch_all_reserved($limit, $offset) {
    // Step 1: Get reservations only
    $query = $this->conn->prepare("
        SELECT * FROM reservations
        WHERE status = 'confirmed' || status = 'completed'
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
                FROM reservations where status = 'confirmed' || status = 'complete'
            ");
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        } 













    public function fetch_all_reserved_archived($limit, $offset) {
            // Step 1: Get reservations only
            $query = $this->conn->prepare("
                SELECT * FROM reservations
                WHERE status = 'archived'
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




public function count_all_customer_reservation($user_id) {
    $result = $this->conn->query("
        SELECT COUNT(*) as total 
        FROM reservations WHERE reserve_user_id = $user_id
    ");
    $row = $result->fetch_assoc();
    return (int)$row['total'];
} 

      








}
