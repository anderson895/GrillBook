<?php
session_start();
require '../../vendor/autoload.php';
include('../class.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

class RegisterUser {
    private $db;
    private $firstName;
    private $lastName;
    private $email;
    private $password;
    private $verificationCode;
    private $codeExpiry = 300; // 5 minutes
    private $resendCooldown = 300; // 5 minutes

    public function __construct($db, $postData = []) {
        $this->db = $db;
        $this->firstName = $postData['first_name'] ?? '';
        $this->lastName  = $postData['last_name'] ?? '';
        $this->email     = $postData['email'] ?? '';
        $this->password  = $postData['password'] ?? '';
    }

    public function register() {
        if ($this->isEmailExist()) {
            return ['status' => 'error', 'message' => 'Email already registered.'];
        }

        $this->generateVerificationCode();
        $this->storeSession();

        if ($this->sendVerificationEmail()) {
            return ['status' => 'success', 'message' => 'Verification code sent!'];
        }
        return ['status' => 'error', 'message' => 'Failed to send verification email.'];
    }

    public function resendVerification() {
        if (!isset($_SESSION['register_data'])) {
            return ['status' => 'error', 'message' => 'No registration session found.'];
        }

        $user = &$_SESSION['register_data'];
        $current_time = time();

        // Initialize last_resend_time if not set
        if (!isset($user['last_resend_time'])) {
            $user['last_resend_time'] = 0;
        }

        // Check cooldown
        $remainingCooldown = $this->resendCooldown - ($current_time - $user['last_resend_time']);
        if ($remainingCooldown > 0) {
            return ['status' => 'error', 'message' => "Please wait {$remainingCooldown} seconds before resending."];
        }

        // Regenerate verification code
        $user['verification_code'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user['code_generated_time'] = $current_time;
        $user['last_resend_time'] = $current_time;

        $this->firstName = $user['first_name'];
        $this->lastName  = $user['last_name'];
        $this->email     = $user['email'];
        $this->verificationCode = $user['verification_code'];

        if ($this->sendVerificationEmail()) {
            return ['status' => 'success', 'message' => 'Verification code resent!'];
        }
        return ['status' => 'error', 'message' => 'Failed to resend verification email.'];
    }

    private function isEmailExist() {
        return $this->db->isEmailExist($this->email);
    }

    private function generateVerificationCode() {
        $this->verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function storeSession() {
        $_SESSION['register_data'] = [
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'email'      => $this->email,
            'password'   => $this->password,
            'verification_code' => $this->verificationCode,
            'code_generated_time' => time(),
            'last_resend_time' => 0
        ];
    }

    private function sendVerificationEmail() {
        $fullname = $this->firstName . ' ' . $this->lastName;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'GRILLBOOK');
            $mail->addAddress($this->email, $fullname);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            $mail->isHTML(true);
            $mail->Subject = 'GRILLBOOK Verification Code';
            $mail->Body = "
                <h2>Hello $fullname!</h2>
                <p>Your verification code is: <b>{$this->verificationCode}</b></p>
                <p>This code will expire in 5 minutes.</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Verification email error: " . $e->getMessage());
            return false;
        }
    }
}

// --- Handle requests ---
$db = new global_class();
$requestType = $_POST['requestType'] ?? '';

if ($requestType === 'RegisterCustomer') {
    $register = new RegisterUser($db, $_POST);
    echo json_encode($register->register());
    exit;
}

if ($requestType === 'ResendVerification') {
    $register = new RegisterUser($db);
    echo json_encode($register->resendVerification());
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request type.']);
