<?php
// Simple test to check if PHPMailer loads
echo "Testing PHPMailer autoload...\n";

require __DIR__ . '/../vendor/autoload.php';

// Check if class exists
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer class loaded successfully\n";
    
    // Try to create instance
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "✅ PHPMailer instance created\n";
    } catch (Exception $e) {
        echo "❌ Failed to create PHPMailer instance: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ PHPMailer class NOT found\n";
    
    // Check what's in vendor
    echo "Checking vendor/autoload.php...\n";
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        echo "autoload.php exists\n";
        $content = file_get_contents(__DIR__ . '/../vendor/autoload.php');
        if (strpos($content, 'PHPMailer') !== false) {
            echo "PHPMailer mentioned in autoload.php\n";
        } else {
            echo "PHPMailer NOT mentioned in autoload.php\n";
        }
    } else {
        echo "autoload.php NOT found\n";
    }
}
