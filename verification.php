<?php
session_start();

if (!isset($_SESSION['register_data'])) {
    header('Location: register.php');
    exit;
}

$user = $_SESSION['register_data'];


?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Account</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
</head>
<body>
    <h2>Enter Verification Code</h2>

    <form id="frmVerify">
        <input type="text" name="verification_code" placeholder="Enter your code" required />
        <button type="submit" id="btnVerify">Verify</button>
    </form>

  
</body>
</html>



<script src="static/js/verification.js"></script>