<?php
// test_mailer.php
require __DIR__ . '/class.php';

echo "Testing Mailer class...\n";

try {
    // Create Mailer instance
    $mailer = new Mailer();
    
    // Test data
    $email = "rodriguezryan325@gmail.com";
    $name = "Test User";
    $type = "closure";
    $date = date('Y-m-d');
    $reason = "Testing email system";
    
    echo "Sending test email to: $email\n";
    
    // Send test email
    $result = $mailer->sendBroadcastNotification($email, $name, $type, $date, $reason);
    
    if ($result) {
        echo "✅ Email sent successfully!\n";
        echo "Check your inbox (and spam folder).\n";
    } else {
        echo "❌ Email failed to send\n";
        echo "Check PHP error logs for details.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
