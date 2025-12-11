<?php
include('controller/config.php');

if (isset($_GET['token']) && isset($_GET['id'])) {
    $token = $_GET['token'];
    $user_id = $_GET['id'];
    
    $globalClass = new global_class();
    $result = $globalClass->verifyEmail($user_id, $token);
    
    $message = $result['message'];
    $success = $result['success'];
} else {
    $message = "Invalid verification link.";
    $success = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Verification - Ultimate Liempo Haus</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .success { 
            color: #28a745; 
            font-size: 18px;
            margin: 20px 0;
        }
        .error { 
            color: #dc3545; 
            font-size: 18px;
            margin: 20px 0;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #D4AF37;
            margin-bottom: 20px;
        }
        .btn {
            background: #D4AF37;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #B8860B;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Ultimate Liempo Haus</div>
        <h1>Email Verification</h1>
        <p class="<?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>
        <?php if ($success): ?>
            <a href="login.php" class="btn">Proceed to Login</a>
        <?php else: ?>
            <a href="register.php" class="btn">Back to Registration</a>
        <?php endif; ?>
    </div>
</body>
</html>