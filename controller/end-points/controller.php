<?php
include('../class.php');

$db = new global_class();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['requestType'])) {
         if ($_POST['requestType'] == 'Login') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $loginResult = $db->Login($email, $password);

            if ($loginResult['success']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $loginResult['message'],
                    'user_position' => $loginResult['data']['user_position']
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => $loginResult['message']
                ]);
            }
        }else if ($_POST['requestType'] == 'reschedule') {
                   $reservationId = $_POST['reservationId'] ?? null;
                    $reason = $_POST['reason'] ?? null;
                    $seats = $_POST['seats'] ?? null;
                    $newDate = $_POST['newDate'] ?? null;
                    $newTime = $_POST['newTime'] ?? null;


                    $result = $db->reschedule($reservationId, $reason,$seats, $newDate,$newTime);

                    if (!empty($result['success']) && $result['success']) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => $result['message']   
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $result['message'] ?? 'Something went wrong.'
                        ]);
                    }


        }else if ($_POST['requestType'] == 'cancel_reservation') {
                    $reservationId = $_POST['reservation_id'];
                    $result = $db->cancel_reservation($reservationId);

                    if ($result['success']) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => $result['message']   
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $result['message']  
                        ]);
                    }

        }else if ($_POST['requestType'] == 'RegisterCustomer') {
                $first_name = $_POST['first_name'];
                $last_name  = $_POST['last_name'];
                $email      = $_POST['email'];
                $password   = $_POST['password'];

                $result = $db->RegisterCustomer($first_name, $last_name, $email, $password);

                if ($result['success']) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message'],
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }

        }else if ($_POST['requestType'] == 'UpdateAccount') {

                $user_id = $_POST['user_id'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $password = $_POST['password'];
                $email = $_POST['email'];

                // Update user information in the database
                $updateSuccess = $db->UpdateAccount($user_id, $first_name, $last_name, $email, $password);

                if ($updateSuccess) {
                    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
                }


        }else if ($_POST['requestType'] == 'updateArchived') {

                $column = $_POST['column'];
                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                

                $result = $db->updateArchived($reservation_id, $status,$column);

                if ($result['success']) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message'],
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }


               

        }else if ($_POST['requestType'] == 'ApproveReschedule') {

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                

                $result = $db->ApproveReschedule($reservation_id, $status);

                if ($result['success']) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message'],
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }

        }else if ($_POST['requestType'] == 'UpdateReservationStatus') {

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                

                $result = $db->UpdateReservationStatus($reservation_id, $status);

                if ($result['success']) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message'],
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }

        }else if ($_POST['requestType'] == 'ApproveReservationStatus') {

                $reservation_id = $_POST['reservation_id'];
                $status = $_POST['status'];
                

                $result = $db->ApproveReservationStatus($reservation_id, $status);

                if ($result['success']) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message'],
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }



        }else if ($_POST['requestType'] == 'AddMenu') {

                $menuName  = $_POST['menuName'];
                $menuCategory  = $_POST['menuCategory'];
                $menuDescription = $_POST['menuDescription'];
                $menuPrice  = $_POST['menuPrice'];
                // FILES
                $menuImage = $_FILES['menuImage'];
                $uploadDir = '../../static/upload/';
                $menuImageFileName = ''; 
                if (isset($menuImage) && $menuImage['error'] === UPLOAD_ERR_OK) {
                    $bannerExtension = pathinfo($menuImage['name'], PATHINFO_EXTENSION);
                    $menuImageFileName = uniqid('menu_', true) . '.' . $bannerExtension;
                    $bannerPath = $uploadDir . $menuImageFileName;

                    $bannerUploaded = move_uploaded_file($menuImage['tmp_name'], $bannerPath);

                    if (!$bannerUploaded) {
                        echo json_encode([
                            'status' => 500,
                            'message' => 'Error uploading menuImage image.'
                        ]);
                        exit;
                    }
                } elseif ($menuImage['error'] !== UPLOAD_ERR_NO_FILE && $menuImage['error'] !== 0) {
                    echo json_encode([
                        'status' => 400,
                        'message' => 'Invalid image upload.'
                    ]);
                    exit;
                }
                $result = $db->AddMenu(
                    $menuName,
                    $menuCategory,
                    $menuDescription,
                    $menuPrice,
                    $menuImageFileName 
                );

                if ($result) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Posted Successfully.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Error saving data.'
                    ]);
                }

               

        }else if ($_POST['requestType'] == 'UpdatMenu') {

            $menu_id = $_POST['menu_id'];
            $menu_name = $_POST['menu_name'];
            $menu_category = $_POST['menuCategory'];
            $menu_description = $_POST['menu_description'];
            $menu_price = $_POST['menu_price'];

            // Handle Banner Image Upload
            $uniqueBannerFileName = null;
            if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../static/upload/';
                $fileExtension = pathinfo($_FILES['menu_image']['name'], PATHINFO_EXTENSION);
                $uniqueBannerFileName = uniqid('menu_', true) . '.' . $fileExtension;

                move_uploaded_file($_FILES['menu_image']['tmp_name'], $uploadDir . $uniqueBannerFileName);
            }

            // Update
            $result = $db->UpdateMenu(
                $menu_id,
                $menu_name,
                $menu_category,
                $menu_description,
                $menu_price,
                $uniqueBannerFileName 
            );

            if ($result['status']) {
                echo json_encode([
                    'status' => 200,
                    'message' => $result['message']
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => $result['message']
                ]);
            }

            exit;


        }else if ($_POST['requestType'] == 'AddMenuDeals') {

            $menu_id=$_POST['menu'];
            $deal_id=$_POST['deal_id'];

            
            $result = $db->AddMenuDeals($menu_id,$deal_id);
            if ($result) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'successfully Added.'
                    ]);
            } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'No changes made or error updating data.'
                    ]);
            }

        }else if ($_POST['requestType'] == 'removeMenu') {

            $menu_id=$_POST['menu_id'];
            $result = $db->removeMenu($menu_id);
            if ($result) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Remove successfully.'
                    ]);
            } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'No changes made or error updating data.'
                    ]);
            }
        }else if ($_POST['requestType'] == 'removeUser') {

            $user_id=$_POST['user_id'];
            $result = $db->removeUser($user_id);
            if ($result) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Remove successfully.'
                    ]);
            } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'No changes made or error updating data.'
                    ]);
            }
        }else if ($_POST['requestType'] == 'remove_deal_ids') {

            $deal_id =$_POST['dealId'];
            $menu_id=$_POST['menu_id'];
            $result = $db->remove_deal_ids($menu_id,$deal_id);
            if ($result) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Remove successfully.'
                    ]);
            } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'No changes made or error updating data.'
                    ]);
            }
        }else if ($_POST['requestType'] == 'removeDeals') {

            $deal_id=$_POST['deal_id'];
            $result = $db->removeDeals($deal_id);
            if ($result) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Remove successfully.'
                    ]);
            } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'No changes made or error updating data.'
                    ]);
            }
        }else if ($_POST['requestType'] == 'CreatDeals') {

            $entryName = $_POST['entryName'];
            $entryDescription = $_POST['entryDescription'];
            $deal_type = $_POST['deal_type'];
            $entryExpiration = $_POST['entryExpiration'] ?? null;

            $entryImage = $_FILES['entryImage'];
            $uploadDir = '../../static/upload/';
            $entryImageFileName = '';

            if (isset($entryImage) && $entryImage['error'] === UPLOAD_ERR_OK) {
                $bannerExtension = pathinfo($entryImage['name'], PATHINFO_EXTENSION);
                $entryImageFileName = uniqid('deals_', true) . '.' . $bannerExtension;
                $bannerPath = $uploadDir . $entryImageFileName;

                $bannerUploaded = move_uploaded_file($entryImage['tmp_name'], $bannerPath);

                if (!$bannerUploaded) {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Error uploading image.'
                    ]);
                    exit;
                }
            } elseif ($entryImage['error'] !== UPLOAD_ERR_NO_FILE && $entryImage['error'] !== 0) {
                echo json_encode([
                    'status' => 400,
                    'message' => 'Invalid image upload.'
                ]);
                exit;
            }

            // Generic function call (update to match actual method name in your DB class)
            $result = $db->createDeals($entryName, $entryDescription, $deal_type, $entryImageFileName,$entryExpiration);

            if ($result) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'Added successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'No changes made or error updating data.'
                ]);
            }


        }else if ($_POST['requestType'] == 'RequestReservation') {

            session_start();
            $user_id=$_SESSION['user_id'];
           // Extract all POST variables
            $table_code = $_POST['table_code'];
            $seats = $_POST['seats'];
            $date_schedule = $_POST['date_schedule'];
            $time_schedule = $_POST['time_schedule'];
            $menu_select = isset($_POST['menu_select']) ? $_POST['menu_select'] : [];
            $promo_select = isset($_POST['promo_select']) ? $_POST['promo_select'] : [];
            $group_select = isset($_POST['group_select']) ? $_POST['group_select'] : [];
            $selected_menus = $_POST['selected_menus'];
            $selected_promos = $_POST['selected_promos'];
            $selected_groups = $_POST['selected_groups'];
            $menu_total = $_POST['menu_total'];
            $promo_total = $_POST['promo_total'];
            $group_total = $_POST['group_total'];
            $grand_total = $_POST['grand_total'];


            
            $entryImage = $_FILES['payment_proof'];
            $termsFileSigned = $_FILES['termsFileSigned'];
            $uploadDir = '../../static/upload/';

            $entryImageFileName = '';
            $termsFileSignedFileName = '';

            // Upload payment proof
            if (isset($entryImage) && $entryImage['error'] === UPLOAD_ERR_OK) {
                $bannerExtension = pathinfo($entryImage['name'], PATHINFO_EXTENSION);
                $entryImageFileName = uniqid('proof_', true) . '.' . $bannerExtension;
                $bannerPath = $uploadDir . $entryImageFileName;

                if (!move_uploaded_file($entryImage['tmp_name'], $bannerPath)) {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Error uploading payment proof.'
                    ]);
                    exit;
                }
            } elseif ($entryImage['error'] !== UPLOAD_ERR_NO_FILE && $entryImage['error'] !== 0) {
                echo json_encode([
                    'status' => 400,
                    'message' => 'Invalid payment proof upload.'
                ]);
                exit;
            }

            // Upload signed terms file
            if (isset($termsFileSigned) && $termsFileSigned['error'] === UPLOAD_ERR_OK) {
                $termsExtension = pathinfo($termsFileSigned['name'], PATHINFO_EXTENSION);
                $termsFileSignedFileName = uniqid('terms_', true) . '.' . $termsExtension;
                $termsPath = $uploadDir . $termsFileSignedFileName;

                if (!move_uploaded_file($termsFileSigned['tmp_name'], $termsPath)) {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Error uploading signed terms file.'
                    ]);
                    exit;
                }
            } elseif ($termsFileSigned['error'] !== UPLOAD_ERR_NO_FILE && $termsFileSigned['error'] !== 0) {
                echo json_encode([
                    'status' => 400,
                    'message' => 'Invalid signed terms file upload.'
                ]);
                exit;
            }

            

            // Call the database method with all parameters
            $result = $db->RequestReservation(
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
            );

            if ($result) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'Added successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'No changes made or error updating data.'
                ]);
            }


        }else {
            echo '404';
        }
    } else {
        echo 'Access Denied! No Request Type.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

   if (isset($_GET['requestType']))
    {
        if ($_GET['requestType'] == 'fetch_all_menu') {
            $result = $db->fetch_all_menu();
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);
        }else if ($_GET['requestType'] == 'fetch_all_users') {
            $result = $db->fetch_all_users();
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);
        }else if ($_GET['requestType'] == 'fetch_all_deals') {
            $deal_type = isset($_GET["deal_type"]) && $_GET["deal_type"] !== '' ? $_GET["deal_type"] : null;
            $result = $db->fetch_all_deals($deal_type);
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);

        }else if ($_GET['requestType'] == 'fetch_all_deals_and_menu') {
            $deal_type = isset($_GET["deal_type"]) && $_GET["deal_type"] !== '' ? $_GET["deal_type"] : null;
            $result = $db->fetch_all_deals_and_menu($deal_type);
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);

        }else if ($_GET['requestType'] == 'GetAllDealsWithMenus_byId') {
            $dealId=$_GET['deal_id'];


            $result = $db->GetAllDealsWithMenus_byId($dealId);
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);
        }else if ($_GET['requestType'] == 'checkAvailability') {
            $table_code = $_GET['table_code'];
            $date_schedule = $_GET['date_schedule'];
            $time_schedule = $_GET['time_schedule'];

           $availability = $db->checkAvailability($table_code, $date_schedule,$time_schedule);

            echo json_encode([
                'status' => 200,
                'availability' => $availability
            ]);

        }else if ($_GET['requestType'] == 'fetch_all_reserve_request') {
            $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $data  = $db->fetch_all_reserve_request($limit, $offset);
            $total = $db->count_all_reserve_request();

            echo json_encode([
                'status' => 200,
                'total'  => $total,
                'data'   => $data
            ]);
            exit;

        }else if ($_GET['requestType'] == 'fetch_all_reserved') {
            $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $data  = $db->fetch_all_reserved($limit, $offset);
            $total = $db->count_all_reserved();

            echo json_encode([
                'status' => 200,
                'total'  => $total,
                'data'   => $data
            ]);
            exit;

        }else if ($_GET['requestType'] == 'fetch_all_reserved_archived') {
            $collumn=$_GET['collumn'];
            $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $data  = $db->fetch_all_reserved_archived($limit, $offset,$collumn);
            $total = $db->count_all_reserved_archived();

            echo json_encode([
                'status' => 200,
                'total'  => $total,
                'data'   => $data
            ]);
            exit;

        }else if ($_GET['requestType'] == 'fetch_all_customer_reservation') {

            session_start();
            $user_id=$_SESSION['user_id'];

            $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $data  = $db->fetch_all_customer_reservation($limit, $offset,$user_id);
            $total = $db->count_all_customer_reservation($user_id);

            echo json_encode([
                'status' => 200,
                'total'  => $total,
                'data'   => $data
            ]);
            exit;
            

        }else if ($_GET['requestType'] == 'dashboard_analytics') {
         
                $data = $db->getDataAnalytics();

                if ($data) {
                    echo json_encode([
                        'success' => true,
                        'data' => $data
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to retrieve analytics'
                    ]);
                }

        }else if ($_GET['requestType'] == 'fetch_all_customer_reservation_no_limit') {
           
                session_start();
                $user_id=$_SESSION['user_id'];
                $data  = $db->fetch_all_customer_reservation_no_limit($user_id);

                echo json_encode([
                    'status' => 200,
                    'data'   => $data
                ]);
                exit;

        }else if ($_GET['requestType'] === 'fetch_all_table_availability_today') {
                // Hardcoded table list (same as in HTML)
                $allTables = [
                    'G6','G5','Take out 1','Take out 2','F3','F4',
                    'G4','G3','E4','E8','F1','F2',
                    'G2','G1','E3','E7','C6','D6','DJ',
                    'E2','E6','C5','D5','SOUNDECT',
                    'A5','B6','E1','E5','C4','D4','ACOUSTIC',
                    'A4','B5','C3','D3','VIP 3','VIP 2',
                    'A3','B4','RESERV.','C2','D2','BILLIARDS',
                    'A2','B3','MEETING','C1','D1','VIP 1',
                    'A1','B2','COMPLI','B1'
                ];

                // Fetch reserved tables for today
                $reservedTables = $db->fetch_all_reservations_today();

                // Build availability list
                $availability = [];
                foreach ($allTables as $table) {
                    $availability[] = [
                        'table_code' => $table,
                        'status' => in_array($table, $reservedTables) ? 'unavailable' : 'available'
                    ];
                }

                echo json_encode([
                    'status' => 200,
                    'data' => $availability
                ]);
                exit;
        }else{
            echo "404";
        }
    }else {
        echo 'No GET REQUEST';
    }

}
?>