
<?php


include ('config.php');

date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

    public function fetch_all_menu() {
        $query = $this->conn->prepare("SELECT * FROM menu ORDER BY menu_id DESC");

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





    
    public function Login($email, $password)
{
    $query = $this->conn->prepare("SELECT * FROM `user` WHERE `user_email` = ?");
    $query->bind_param("s", $email);

    if ($query->execute()) {
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['user_password'])) {
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
        // Step 1: Get the banner filename from the database
        $selectQuery = "SELECT menu_image_banner FROM menu WHERE menu_id = ?";
        $stmt = $this->conn->prepare($selectQuery);
        if (!$stmt) {
            return 'Prepare failed (select): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $menu_id);
        $stmt->execute();
        $stmt->bind_result($bannerFile);
        $stmt->fetch();
        $stmt->close();

        // Step 2: Delete the record from the database
        $deleteQuery = "DELETE FROM menu WHERE menu_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (delete): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $menu_id);
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






}
