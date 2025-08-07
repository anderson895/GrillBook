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
        }else if ($_POST['requestType'] == 'CreatDeals') {

            $entryName = $_POST['entryName'];
            $entryDescription = $_POST['entryDescription'];
            $deal_type = $_POST['deal_type'];

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
            $result = $db->createDeals($entryName, $entryDescription, $deal_type, $entryImageFileName);

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

   if (isset($_GET['requestType'])) {
        if ($_GET['requestType'] == 'fetch_all_menu') {
            $result = $db->fetch_all_menu();
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);
        }else if ($_GET['requestType'] == 'fetch_all_deals') {
            $deal_type=$_GET["deal_type"];
            $result = $db->fetch_all_deals($deal_type);
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
        }else{
            echo "404";
        }
    }else {
        echo 'No GET REQUEST';
    }

}
?>