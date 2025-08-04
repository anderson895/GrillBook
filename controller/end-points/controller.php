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

               

        }else {
            echo '404';
        }
    } else {
        echo 'Access Denied! No Request Type.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

   if (isset($_GET['requestType'])) {
        if ($_GET['requestType'] == 'fetch_all_registered_dogs') {
            $result = $db->fetch_all_registered_dogs();
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