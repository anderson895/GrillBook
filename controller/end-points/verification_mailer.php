<?php
session_start();
require '../../vendor/autoload.php';
include('../class.php');
$db = new global_class();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get POST data
$first_name = $_POST['first_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$fullname   = $first_name . ' ' . $last_name;

// Check if email already exists
if ($db->isEmailExist($email)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email already registered.'
    ]);
    exit; // stop execution if email exists
}

// Generate verification code
$verification_code = rand(100000, 999999);

// Store user info and code in session
$_SESSION['register_data'] = [
    'first_name' => $first_name,
    'last_name'  => $last_name,
    'email'      => $email,
    'password'   => $password,
    'verification_code' => $verification_code
];

// PHPMailer setup
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rodriguezryan325@gmail.com';
    $mail->Password = 'ofvf yxut wpcc iecx'; // use .env in production
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('rodriguezryan325@gmail.com', 'GRILLBOOK');
    $mail->addAddress($email, $fullname);
    $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

    $mail->isHTML(true);
    $mail->Subject = 'GRILLBOOK Verification Code';
    $mail->Body = "
        <h2>Hello $fullname!</h2>
        <p>Your verification code is: <b>$verification_code</b></p>
        <p>Use this code to verify your account.</p>
    ";

    $mail->send();

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
}
?>
