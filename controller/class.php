<?php
/**
 * Grillbook System - Controller Class
 * Ultimate Liempo Haus Reservation Management System
 * 
 * @package Grillbook System
 * @category Controller
 * @version 1.0.0
 * @author System Administrator
 * @copyright 2024 Ultimate Liempo Haus
 */

include('config.php');
date_default_timezone_set('Asia/Manila');

require __DIR__ . '/../vendor/autoload.php';

/**
 * Mailer Class
 * 
 * Handles all email notification functionalities including
 * broadcast notifications, emergency closures, holiday notices,
 * and reservation status updates. Utilizes PHPMailer for
 * reliable SMTP email delivery with HTML templating.
 */
class Mailer {
    private $conn;
    
    /**
     * Constructor for Mailer class
     * 
     * @param mysqli|null $connection Database connection (optional)
     */
    public function __construct($connection = null) {
        $this->conn = $connection;
    }
    
    /**
     * Send broadcast notification to customers
     * 
     * @param string $email Recipient email address
     * @param string $name Recipient full name
     * @param string $type Notification type (closure, maintenance, event)
     * @param string $date Date of the announcement
     * @param string $reason Detailed explanation for the notification
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendBroadcastNotification($email, $name, $type, $date, $reason) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'Ultimate Liempo Haus');
            $mail->addAddress($email);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            $mail->isHTML(true);
            
            $subject = '';
            $message = '';
            $notificationType = '';
            
            switch($type) {
                case 'closure':
                    $subject = 'Important Notice: Restaurant Closure Announcement';
                    $notificationType = 'Restaurant Closure';
                    $message = "
                        <div style='padding: 30px; background: white;'>
                            <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We hope this message finds you well. We are writing to formally inform you that Ultimate Liempo Haus will be temporarily closed on <strong style='color: #c0392b;'>{$date}</strong>.
                            </p>
                            
                            <div style='background: #f8f9fa; border-left: 5px solid #e74c3c; padding: 20px; margin: 25px 0; border-radius: 0 5px 5px 0;'>
                                <h3 style='color: #2c3e50; margin-top: 0;'>Closure Details:</h3>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Date of Closure:</strong> {$date}</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Type:</strong> Temporary Restaurant Closure</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Reason:</strong> {$reason}</p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We understand that this closure may affect your dining plans, and we sincerely apologize for any inconvenience this may cause. The decision was made after careful consideration to ensure the highest standards of service and safety for our valued patrons.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                During this period, our team will be working diligently to address the necessary matters that prompted this temporary closure. We are committed to returning with enhanced services and an even better dining experience for you.
                            </p>
                            
                            <div style='background: #e8f4f8; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2980b9; margin-top: 0;'>Important Information:</h4>
                                <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                    <li>All reservations for {$date} have been automatically cancelled</li>
                                    <li>No new reservations will be accepted for this date</li>
                                    <li>You will receive a separate notification regarding any refunds or rescheduling options</li>
                                    <li>Our online reservation system will reflect this closure date as unavailable</li>
                                </ul>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We truly value your patronage and understanding during this time. Your satisfaction remains our top priority, and we appreciate your continued support.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                For any questions or concerns regarding this closure, please do not hesitate to contact our customer service team. We are here to assist you and provide any necessary information.
                            </p>
                            
                            <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 25px 0; text-align: center;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>Contact Information:</h4>
                                <p style='color: #7f8c8d; margin: 5px 0;'><strong>📞 Customer Service:</strong> (123) 456-7890</p>
                                <p style='color: #7f8c8d; margin: 5px 0;'><strong>📧 Email:</strong> support@ultimateliempo.com</p>
                                <p style='color: #7f8c8d; margin: 5px 0;'><strong>📍 Address:</strong> 123 Gourmet Street, Manila, Philippines</p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                We look forward to welcoming you back soon and providing you with the exceptional dining experience that you have come to expect from Ultimate Liempo Haus.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8;'>
                                With warmest regards,<br>
                                <strong>The Management Team</strong><br>
                                Ultimate Liempo Haus<br>
                                <em>Excellence in Every Bite</em>
                            </p>
                        </div>
                    ";
                    break;
                    
                case 'maintenance':
                    $subject = 'Notice: Scheduled Maintenance Period';
                    $notificationType = 'Maintenance Announcement';
                    $message = "
                        <div style='padding: 30px; background: white;'>
                            <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We hope this message finds you in good health and spirits. We are writing to inform you about an important scheduled maintenance period at Ultimate Liempo Haus.
                            </p>
                            
                            <div style='background: #f8f9fa; border-left: 5px solid #3498db; padding: 20px; margin: 25px 0; border-radius: 0 5px 5px 0;'>
                                <h3 style='color: #2c3e50; margin-top: 0;'>Maintenance Schedule:</h3>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Maintenance Date:</strong> {$date}</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Type:</strong> Scheduled Facility Maintenance</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Purpose:</strong> {$reason}</p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                This maintenance period has been carefully planned to enhance our facilities and ensure we continue to provide you with the highest quality dining experience. Our commitment to excellence drives us to continuously improve our restaurant environment.
                            </p>
                            
                            <div style='background: #e8f4f8; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2980b9; margin-top: 0;'>What to Expect:</h4>
                                <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                    <li>Temporary closure on the specified maintenance date</li>
                                    <li>Enhanced facilities and improved dining environment upon reopening</li>
                                    <li>Potential for extended maintenance hours based on project requirements</li>
                                    <li>Regular updates available on our website and social media channels</li>
                                </ul>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We understand that this may affect your dining plans, and we sincerely apologize for any inconvenience. However, we believe these improvements will significantly enhance your future dining experiences with us.
                            </p>
                            
                            <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>Customer Support During Maintenance:</h4>
                                <p style='color: #7f8c8d; margin: 10px 0;'>
                                    Our customer service team remains available to assist you with any inquiries or concerns during this period. You may reach us through the following channels:
                                </p>
                                <p style='color: #7f8c8d; margin: 10px 0;'>
                                    <strong>Phone:</strong> (123) 456-7890<br>
                                    <strong>Email:</strong> support@ultimateliempo.com<br>
                                    <strong>Hours:</strong> Monday to Sunday, 8:00 AM to 10:00 PM
                                </p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We appreciate your understanding and patience as we work to improve our facilities. Your satisfaction is our priority, and we are excited to share the enhanced dining experience with you soon.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                For the latest updates on our reopening and special promotions following the maintenance period, please follow our social media pages and visit our website regularly.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8;'>
                                Thank you for your continued patronage and support.<br><br>
                                With warm regards,<br>
                                <strong>The Management Team</strong><br>
                                Ultimate Liempo Haus<br>
                                <em>Dedicated to Culinary Excellence</em>
                            </p>
                        </div>
                    ";
                    break;
                    
                case 'event':
                    $subject = 'Announcement: Special Event at Ultimate Liempo Haus';
                    $notificationType = 'Special Event';
                    $message = "
                        <div style='padding: 30px; background: white;'>
                            <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We are delighted to share some exciting news with you! Ultimate Liempo Haus is thrilled to announce a special event that we believe will enhance your dining experience and create unforgettable memories.
                            </p>
                            
                            <div style='background: #f8f9fa; border-left: 5px solid #9b59b6; padding: 20px; margin: 25px 0; border-radius: 0 5px 5px 0;'>
                                <h3 style='color: #2c3e50; margin-top: 0;'>Event Details:</h3>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Event Date:</strong> {$date}</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Event Type:</strong> Special Dining Experience</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Event Description:</strong> {$reason}</p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                This exclusive event has been carefully curated by our culinary team to provide you with a unique and memorable dining experience. We have incorporated special menus, entertainment, and ambiance that we believe will exceed your expectations.
                            </p>
                            
                            <div style='background: #f5e8ff; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #8e44ad; margin-top: 0;'>Event Highlights:</h4>
                                <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                    <li>Specially curated menu featuring seasonal ingredients</li>
                                    <li>Live entertainment and music performances</li>
                                    <li>Complimentary welcome drinks for all attendees</li>
                                    <li>Interactive culinary demonstrations by our chefs</li>
                                    <li>Special discounts on select menu items</li>
                                </ul>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                Due to the exclusive nature of this event and limited seating capacity, we highly recommend making reservations in advance to secure your preferred time slot. Our reservation system will reflect special event pricing and availability.
                            </p>
                            
                            <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>Reservation Information:</h4>
                                <p style='color: #7f8c8d; margin: 10px 0;'>
                                    <strong>Reservation Period:</strong> Now open until {$date}<br>
                                    <strong>Special Event Hours:</strong> 5:00 PM to 11:00 PM<br>
                                    <strong>Dress Code:</strong> Smart Casual<br>
                                    <strong>Event Capacity:</strong> Limited seating available
                                </p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We cordially invite you and your guests to join us for this special occasion. It is our pleasure to share these memorable moments with our valued customers who have supported us throughout our journey.
                            </p>
                            
                            <div style='background: #e8f4f8; padding: 20px; border-radius: 5px; margin: 25px 0; text-align: center;'>
                                <h4 style='color: #2980b9; margin-top: 0;'>For Reservations and Inquiries:</h4>
                                <p style='color: #7f8c8d; margin: 5px 0;'><strong>📞 Phone:</strong> (123) 456-7890</p>
                                <p style='color: #7f8c8d; margin: 5px 0;'><strong>🌐 Website:</strong> www.ultimateliempo.com/events</p>
                                <p style='color: #7f8c8d; margin: 5px 0;'><strong>📱 Social Media:</strong> @UltimateLiempoHaus</p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                Should you have any special requests or dietary requirements for the event, please inform our team when making your reservation. We are committed to accommodating your needs to ensure a perfect dining experience.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8;'>
                                We look forward to welcoming you to this special event and creating wonderful memories together.<br><br>
                                Warmest regards,<br>
                                <strong>The Management Team</strong><br>
                                Ultimate Liempo Haus<br>
                                <em>Creating Memorable Dining Experiences</em>
                            </p>
                        </div>
                    ";
                    break;
                    
                default:
                    $subject = 'Important Announcement from Ultimate Liempo Haus';
                    $notificationType = 'General Announcement';
                    $message = "
                        <div style='padding: 30px; background: white;'>
                            <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We hope this message finds you well. We are writing to share an important announcement regarding Ultimate Liempo Haus and our ongoing commitment to providing exceptional dining experiences for our valued customers.
                            </p>
                            
                            <div style='background: #f8f9fa; border-left: 5px solid #f39c12; padding: 20px; margin: 25px 0; border-radius: 0 5px 5px 0;'>
                                <h3 style='color: #2c3e50; margin-top: 0;'>Announcement Details:</h3>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Date:</strong> {$date}</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Nature of Announcement:</strong> General Update</p>
                                <p style='color: #7f8c8d; margin: 10px 0;'><strong>Details:</strong> {$reason}</p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                At Ultimate Liempo Haus, we are constantly striving to enhance our services and facilities to better serve our customers. This announcement reflects our dedication to maintaining transparency and keeping you informed about developments that may affect your dining experience with us.
                            </p>
                            
                            <div style='background: #fef9e7; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #d35400; margin-top: 0;'>Key Information:</h4>
                                <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                    <li>This announcement may affect reservation availability and restaurant operations</li>
                                    <li>Normal business operations are expected unless otherwise specified</li>
                                    <li>Any changes to our services will be communicated promptly</li>
                                    <li>Your existing reservations remain valid unless specifically notified</li>
                                </ul>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We understand the importance of clear communication with our customers, and we want to ensure that you have all the necessary information to plan your visits accordingly. Your satisfaction and trust are of utmost importance to us.
                            </p>
                            
                            <div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>Customer Support:</h4>
                                <p style='color: #7f8c8d; margin: 10px 0;'>
                                    If you have any questions or require clarification regarding this announcement, our customer service team is ready to assist you. You may contact us through any of the following channels:
                                </p>
                                <p style='color: #7f8c8d; margin: 10px 0; text-align: center;'>
                                    <strong>Customer Service Hotline:</strong> (123) 456-7890<br>
                                    <strong>Email Support:</strong> announcements@ultimateliempo.com<br>
                                    <strong>Website:</strong> www.ultimateliempo.com/announcements
                                </p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                We appreciate your continued support and trust in Ultimate Liempo Haus. We remain committed to providing you with exceptional dining experiences and look forward to serving you soon.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8;'>
                                Thank you for being a valued member of our dining community.<br><br>
                                Sincerely,<br>
                                <strong>The Management Team</strong><br>
                                Ultimate Liempo Haus<br>
                                <em>Committed to Excellence Since 2024</em>
                            </p>
                        </div>
                    ";
            }

            $mail->Subject = $subject;
            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>$subject</title>
                </head>
                <body>
                    <div style='max-width:700px;margin:auto;padding:20px;background:#f9f9f9;border-radius:10px;font-family:Arial,sans-serif;'>
                        <div style='background: linear-gradient(135deg, #D4AF37, #B8860B); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; color: white;'>
                            <h1 style='margin:0;font-size:28px;'>Ultimate Liempo Haus</h1>
                            <p style='margin:10px 0 0;font-size:16px;opacity:0.9;'>$notificationType</p>
                        </div>
                        $message
                        <div style='text-align: center; padding: 25px; color: #7f8c8d; font-size: 12px; border-top: 1px solid #e0e0e0; margin-top: 30px;'>
                            <p style='margin:5px 0;'>&copy; 2024 Ultimate Liempo Haus. All rights reserved.</p>
                            <p style='margin:5px 0;'>123 Gourmet Street, Manila, Philippines</p>
                            <p style='margin:5px 0;'>This is an automated notification. Please do not reply to this email.</p>
                            <p style='margin:5px 0;'>To unsubscribe from these notifications, please contact our customer service team.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $altBody = "ULTIMATE LIEMPO HAUS - {$notificationType}\n\n";
            $altBody .= "Dear {$name},\n\n";
            $altBody .= "We are writing to inform you about an important announcement regarding Ultimate Liempo Haus.\n\n";
            $altBody .= "Date: {$date}\n";
            $altBody .= "Details: {$reason}\n\n";
            $altBody .= "This notification is sent to keep you informed about developments that may affect your dining experience with us.\n\n";
            $altBody .= "For more information or if you have any questions, please contact our customer service team at (123) 456-7890 or email support@ultimateliempo.com.\n\n";
            $altBody .= "Thank you for your continued support.\n\n";
            $altBody .= "Sincerely,\n";
            $altBody .= "The Management Team\n";
            $altBody .= "Ultimate Liempo Haus\n";

            $mail->AltBody = $altBody;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Broadcast email error to $email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send emergency closure notification to all customers
     * 
     * @param string $email Recipient email address
     * @param string $name Recipient full name
     * @param string $date Date of emergency closure
     * @param string $reason Reason for emergency closure
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendEmergencyClosureNotification($email, $name, $date, $reason) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'Ultimate Liempo Haus');
            $mail->addAddress($email);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            $mail->isHTML(true);
            $mail->Subject = 'URGENT NOTICE: Emergency Closure - Ultimate Liempo Haus';
            
            $formattedDate = date('F j, Y', strtotime($date));
            
            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Emergency Closure Notice</title>
                </head>
                <body>
                    <div style='max-width:700px;margin:auto;padding:20px;background:#f9f9f9;border-radius:10px;font-family:Arial,sans-serif;'>
                        <div style='background: linear-gradient(135deg, #DC2626, #B91C1C); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; color: white;'>
                            <h1 style='margin:0;font-size:28px;'>🚨 URGENT NOTICE: Emergency Closure</h1>
                            <p style='margin:10px 0 0;font-size:16px;opacity:0.9;'>Ultimate Liempo Haus</p>
                        </div>
                        <div style='padding: 30px; background: white;'>
                            <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We are writing to you with urgent and important information regarding an unforeseen circumstance that requires immediate attention. It is with sincere regret that we must inform you that Ultimate Liempo Haus will be temporarily closed due to an emergency situation.
                            </p>
                            
                            <div style='background: #ffe6e6; border: 2px solid #dc2626; padding: 25px; margin: 25px 0; border-radius: 5px;'>
                                <h3 style='color: #b91c1c; margin-top: 0;'>EMERGENCY CLOSURE DETAILS</h3>
                                <table style='width:100%;border-collapse:collapse;color:#7f8c8d;'>
                                    <tr>
                                        <td style='padding:10px;border-bottom:1px solid #ffcccc;'><strong>Closure Date:</strong></td>
                                        <td style='padding:10px;border-bottom:1px solid #ffcccc;color:#b91c1c;font-weight:bold;'>$formattedDate</td>
                                    </tr>
                                    <tr>
                                        <td style='padding:10px;border-bottom:1px solid #ffcccc;'><strong>Closure Type:</strong></td>
                                        <td style='padding:10px;border-bottom:1px solid #ffcccc;'>Emergency Situation</td>
                                    </tr>
                                    <tr>
                                        <td style='padding:10px;'><strong>Reason for Closure:</strong></td>
                                        <td style='padding:10px;'>$reason</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                The decision to close our establishment was not made lightly. After careful assessment of the situation and in consideration of the safety and well-being of our customers and staff, we have determined that temporary closure is the most responsible course of action.
                            </p>
                            
                            <div style='background: #f8f9fa; padding: 25px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>IMMEDIATE ACTIONS TAKEN:</h4>
                                <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                    <li>All reservations for $formattedDate have been automatically cancelled</li>
                                    <li>Our reservation system has been updated to reflect the closure</li>
                                    <li>All staff have been notified and instructed accordingly</li>
                                    <li>Alternative arrangements are being explored where possible</li>
                                    <li>Regular updates will be provided as the situation develops</li>
                                </ul>
                            </div>
                            
                            <div style='background: #e8f4f8; padding: 25px; border-radius: 5px; margin: 25px 0;'>
                                <h4 style='color: #2980b9; margin-top: 0;'>RESERVATION IMPACT & NEXT STEPS:</h4>
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 10px;'>
                                    <strong>For Existing Reservations:</strong><br>
                                    • All reservations for the affected date have been cancelled automatically<br>
                                    • You will receive a separate email regarding refunds or rescheduling options<br>
                                    • Our team will contact you within 48 hours to discuss alternatives<br>
                                    • Priority will be given to rescheduling affected reservations
                                </p>
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 10px;'>
                                    <strong>For Future Reservations:</strong><br>
                                    • The online booking system will not accept reservations for the affected date<br>
                                    • Reservations for other dates remain unaffected<br>
                                    • We recommend checking our website for updates before making new reservations
                                </p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We understand that this emergency closure may cause significant inconvenience and disappointment, particularly if you had special plans or celebrations scheduled. Please accept our deepest apologies for any disruption to your plans. The safety and satisfaction of our customers remain our highest priority.
                            </p>
                            
                            <div style='background: #f8f9fa; padding: 25px; border-radius: 5px; margin: 25px 0; text-align: center;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>CONTACT INFORMATION & SUPPORT:</h4>
                                <p style='color: #7f8c8d; margin: 10px 0;'>
                                    <strong>Emergency Contact Line:</strong> (123) 456-7890<br>
                                    <strong>Customer Service Email:</strong> emergency@ultimateliempo.com<br>
                                    <strong>Website Updates:</strong> www.ultimateliempo.com/status<br>
                                    <strong>Operating Hours:</strong> 8:00 AM - 10:00 PM (During this period)
                                </p>
                                <p style='color: #7f8c8d; margin: 10px 0; font-size: 14px;'>
                                    Our customer service team has been reinforced to handle inquiries related to this emergency closure. Please allow 24-48 hours for response due to high inquiry volumes.
                                </p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                We are actively monitoring the situation and will provide updates as soon as more information becomes available. Our team is working diligently to resolve the emergency and restore normal operations as quickly as possible.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                For the latest information regarding our reopening and any compensation or rescheduling policies, please visit our website or follow our social media channels. We will communicate promptly once we have a clearer timeline for resuming operations.
                            </p>
                            
                            <p style='color: #34495e; line-height: 1.8;'>
                                Thank you for your understanding and patience during this challenging time. We value your continued support and look forward to welcoming you back to Ultimate Liempo Haus as soon as circumstances permit.<br><br>
                                
                                With sincere apologies,<br>
                                <strong>The Emergency Response Team</strong><br>
                                Ultimate Liempo Haus<br>
                                <em>Your Safety is Our Priority</em>
                            </p>
                        </div>
                        <div style='text-align: center; padding: 25px; color: #7f8c8d; font-size: 12px; border-top: 1px solid #e0e0e0; margin-top: 30px;'>
                            <p style='margin:5px 0;'>&copy; 2024 Ultimate Liempo Haus. All rights reserved.</p>
                            <p style='margin:5px 0;'>This is an urgent automated notification. For emergency inquiries, please contact our dedicated hotline.</p>
                            <p style='margin:5px 0;'>Last Updated: " . date('F j, Y \a\t g:i A') . "</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $altBody = "URGENT: EMERGENCY CLOSURE NOTICE - ULTIMATE LIEMPO HAUS\n\n";
            $altBody .= "Dear $name,\n\n";
            $altBody .= "EMERGENCY CLOSURE ANNOUNCEMENT\n\n";
            $altBody .= "We regret to inform you that due to unforeseen circumstances, Ultimate Liempo Haus will be CLOSED on $formattedDate.\n\n";
            $altBody .= "REASON FOR CLOSURE: $reason\n\n";
            $altBody .= "IMMEDIATE IMPACT:\n";
            $altBody .= "• All reservations for $formattedDate have been automatically cancelled\n";
            $altBody .= "• No new reservations will be accepted for this date\n";
            $altBody .= "• The online booking system has been updated accordingly\n\n";
            $altBody .= "NEXT STEPS:\n";
            $altBody .= "1. You will receive a separate email regarding refunds or rescheduling\n";
            $altBody .= "2. Our team will contact affected customers within 48 hours\n";
            $altBody .= "3. Priority rescheduling will be offered to affected reservations\n\n";
            $altBody .= "CONTACT INFORMATION:\n";
            $altBody .= "Emergency Hotline: (123) 456-7890\n";
            $altBody .= "Email: emergency@ultimateliempo.com\n";
            $altBody .= "Website: www.ultimateliempo.com/status\n\n";
            $altBody .= "We sincerely apologize for any inconvenience caused and appreciate your understanding during this emergency situation.\n\n";
            $altBody .= "Sincerely,\n";
            $altBody .= "The Emergency Response Team\n";
            $altBody .= "Ultimate Liempo Haus\n";
            
            $mail->AltBody = $altBody;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Emergency closure email error to $email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send holiday notification to customers
     * 
     * @param string $email Recipient email address
     * @param string $name Recipient full name
     * @param string $date Holiday date
     * @param string $reason Holiday name or description
     * @param string $holidayType Type of holiday (closure or late_opening)
     * @param string|null $lateOpeningTime New opening time for late opening holidays
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendHolidayNotification($email, $name, $date, $reason, $holidayType = 'closure', $lateOpeningTime = null, $lateClosingTime = null) {
        try {
            error_log("sendHolidayNotification called with: email=$email, name=$name, date=$date, reason=$reason, type=$holidayType, lateOpen=$lateOpeningTime, lateClose=$lateClosingTime");
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'Ultimate Liempo Haus');
            $mail->addAddress($email);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            $mail->isHTML(true);
            
            $formattedDate = date('F j, Y', strtotime($date));
            
            if ($holidayType === 'closure') {
                $subject = 'Holiday Closure Notice - Ultimate Liempo Haus';
                $titleColor = 'linear-gradient(135deg, #059669, #047857)';
                $borderColor = '#059669';
                $bgColor = '#D1FAE5';
                $textColor = '#047857';
                
                // Format late opening time to 12-hour (standard) format for email readability
                $lateOpeningTimeDisplay = '';
                if (!empty($lateOpeningTime)) {
                    // attempt to parse and format time like '14:00' -> '2:00 PM'
                    $lateOpeningTimeDisplay = date('g:i A', strtotime($lateOpeningTime));
                }

                $mail->Body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Holiday Closure Notice</title>
                    </head>
                    <body>
                        <div style='max-width:700px;margin:auto;padding:20px;background:#f9f9f9;border-radius:10px;font-family:Arial,sans-serif;'>
                            <div style='background: $titleColor; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; color: white;'>
                                <h1 style='margin:0;font-size:28px;'>🏖️ Holiday Closure Notice</h1>
                                <p style='margin:10px 0 0;font-size:16px;opacity:0.9;'>Ultimate Liempo Haus</p>
                            </div>
                            <div style='padding: 30px; background: white;'>
                                <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                                
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                    We hope this message finds you well. As we approach the upcoming holiday season, we would like to formally inform you about our operational schedule during this special time of year.
                                </p>
                                
                                <div style='background: $bgColor; border: 2px solid $borderColor; padding: 25px; margin: 25px 0; border-radius: 5px;'>
                                    <h3 style='color: $textColor; margin-top: 0;'>HOLIDAY CLOSURE DETAILS</h3>
                                    <table style='width:100%;border-collapse:collapse;color:#047857;'>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #a7f3d0;'><strong>Holiday Name:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #a7f3d0;font-weight:bold;'>$reason</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #a7f3d0;'><strong>Closure Date:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #a7f3d0;font-weight:bold;'>$formattedDate</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;'><strong>Closure Type:</strong></td>
                                            <td style='padding:10px;'>Full Day Closure</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                    In observance of $reason, Ultimate Liempo Haus will be closed to allow our dedicated staff members to celebrate this important occasion with their families and loved ones. We believe that taking this time to honor traditions and spend quality time with family is essential for maintaining the warm, family-oriented atmosphere that defines our restaurant.
                                </p>
                                
                                <div style='background: #f8f9fa; padding: 25px; border-radius: 5px; margin: 25px 0;'>
                                    <h4 style='color: #2c3e50; margin-top: 0;'>RESERVATION IMPACT & ARRANGEMENTS:</h4>
                                    <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                        <li>All existing reservations for $formattedDate have been automatically cancelled</li>
                                        <li>Our online reservation system reflects this closure date as unavailable</li>
                                        <li>No new reservations will be accepted for the holiday date</li>
                                        <li>Normal business operations will resume the following day</li>
                                        <li>You may still make reservations for dates before and after the holiday</li>
                                    </ul>
                                </div>
                                
                                <div style='background: #e8f4f8; padding: 25px; border-radius: 5px; margin: 25px 0;'>
                                    <h4 style='color: #2980b9; margin-top: 0;'>FOR AFFECTED RESERVATIONS:</h4>
                                    <p style='color: #34495e; line-height: 1.8; margin-bottom: 10px;'>
                                        If you had a reservation scheduled for $formattedDate:
                                    </p>
                                    <ol style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                        <li>Your reservation has been automatically cancelled</li>
                                        <li>You will receive priority booking for alternative dates</li>
                                        <li>Our team will contact you to discuss rescheduling options</li>
                                        <li>Any deposits will be fully refunded or transferred to your new reservation</li>
                                    </ol>
                                    <p style='color: #34495e; line-height: 1.8; margin-top: 15px;'>
                                        We sincerely apologize for any inconvenience this may cause to your dining plans. We appreciate your understanding and hope to welcome you on another date.
                                    </p>
                                </div>
                                
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                    During this holiday period, we encourage you to plan your visits accordingly. Our team looks forward to serving you when we resume normal operations and continuing to provide you with the exceptional dining experience that you have come to expect from Ultimate Liempo Haus.
                                </p>
                                
                                <div style='background: #f8f9fa; padding: 25px; border-radius: 5px; margin: 25px 0; text-align: center;'>
                                    <h4 style='color: #2c3e50; margin-top: 0;'>CONTACT INFORMATION:</h4>
                                    <p style='color: #7f8c8d; margin: 10px 0;'>
                                        <strong>Customer Service:</strong> (123) 456-7890<br>
                                        <strong>Email:</strong> holidays@ultimateliempo.com<br>
                                        <strong>Website:</strong> www.ultimateliempo.com/hours<br>
                                        <strong>Response Time:</strong> Within 24 hours (excluding holiday date)
                                    </p>
                                </div>
                                
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                    On behalf of the entire Ultimate Liempo Haus team, we wish you and your family a wonderful and joyous $reason. May this holiday bring you happiness, peace, and cherished moments with your loved ones.
                                </p>
                                
                                <p style='color: #34495e; line-height: 1.8;'>
                                    Thank you for your continued patronage and understanding.<br><br>
                                    Warm holiday wishes,<br>
                                    <strong>The Management Team</strong><br>
                                    Ultimate Liempo Haus<br>
                                    <em>Celebrating Traditions, Creating Memories</em>
                                </p>
                            </div>
                            <div style='text-align: center; padding: 25px; color: #7f8c8d; font-size: 12px; border-top: 1px solid #e0e0e0; margin-top: 30px;'>
                                <p style='margin:5px 0;'>&copy; 2024 Ultimate Liempo Haus. All rights reserved.</p>
                                <p style='margin:5px 0;'>This is an automated holiday notification. Please do not reply to this email.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";

                $altBody = "HOLIDAY CLOSURE NOTICE - ULTIMATE LIEMPO HAUS\n\n";
                $altBody .= "Dear $name,\n\n";
                $altBody .= "HOLIDAY CLOSURE ANNOUNCEMENT\n\n";
                $altBody .= "In observance of $reason, Ultimate Liempo Haus will be closed on $formattedDate.\n\n";
                $altBody .= "IMPORTANT INFORMATION:\n";
                $altBody .= "• All reservations for $formattedDate have been cancelled\n";
                $altBody .= "• No new reservations accepted for this date\n";
                $altBody .= "• Normal operations resume the following day\n";
                $altBody .= "• Reservations for other dates remain available\n\n";
                $altBody .= "FOR AFFECTED CUSTOMERS:\n";
                $altBody .= "1. Your reservation has been automatically cancelled\n";
                $altBody .= "2. Priority rescheduling will be offered\n";
                $altBody .= "3. Our team will contact you regarding alternatives\n";
                $altBody .= "4. Full refunds or reservation transfers available\n\n";
                $altBody .= "CONTACT INFORMATION:\n";
                $altBody .= "Phone: (123) 456-7890\n";
                $altBody .= "Email: holidays@ultimateliempo.com\n\n";
                $altBody .= "We apologize for any inconvenience and appreciate your understanding.\n\n";
                $altBody .= "Wishing you a joyful $reason!\n\n";
                $altBody .= "Sincerely,\n";
                $altBody .= "The Management Team\n";
                $altBody .= "Ultimate Liempo Haus\n";
                
                $mail->Subject = $subject;
                $mail->AltBody = $altBody;
                
            } else {
                $subject = 'Notice: Modified Hours for Holiday - Ultimate Liempo Haus';
                $titleColor = 'linear-gradient(135deg, #D97706, #B45309)';
                $borderColor = '#D97706';
                $bgColor = '#FEF3C7';
                $textColor = '#B45309';
                
                // Format late opening time to 12-hour (standard) format for email readability
                $lateOpeningTimeDisplay = '';
                if (!empty($lateOpeningTime)) {
                    $lateOpeningTimeDisplay = date('g:i A', strtotime($lateOpeningTime));
                }
                
                // Get the closing time - use provided late_closing_time if available, otherwise query business_hours
                $regularClosingTimeDisplay = 'As scheduled';
                if (!empty($lateClosingTime)) {
                    // Use the admin-provided closing time
                    $regularClosingTimeDisplay = date('g:i A', strtotime($lateClosingTime));
                } else {
                    // Fall back to querying the regular closing time for this day
                    try {
                        // Query for closing time matching the holiday date's day of week
                        if (empty($date)) {
                            throw new Exception('Holiday date is empty');
                        }
                        
                        $dateObj = new DateTime($date);
                        $dayOfWeek = $dateObj->format('l'); // e.g., 'Monday', 'Tuesday', etc.
                        
                        $closingTimeStmt = $this->conn->prepare("SELECT close_time FROM business_hours WHERE day_of_week = ? LIMIT 1");
                        if ($closingTimeStmt === false) {
                            throw new Exception('Prepare failed: ' . $this->conn->error);
                        }
                        
                        $closingTimeStmt->bind_param("s", $dayOfWeek);
                        if (!$closingTimeStmt->execute()) {
                            throw new Exception('Execute failed: ' . $closingTimeStmt->error);
                        }
                        
                        $closingResult = $closingTimeStmt->get_result();
                        
                        if ($closingRow = $closingResult->fetch_assoc()) {
                            if (!empty($closingRow['close_time'])) {
                                $regularClosingTimeDisplay = date('g:i A', strtotime($closingRow['close_time']));
                            }
                        }
                        $closingTimeStmt->close();
                    } catch (Exception $e) {
                        // If query fails, just use default message and log it
                        error_log("Failed to fetch closing time: " . $e->getMessage());
                        $regularClosingTimeDisplay = 'As scheduled';
                    }
                }
                
                $mail->Body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Modified Holiday Hours</title>
                    </head>
                    <body>
                        <div style='max-width:700px;margin:auto;padding:20px;background:#f9f9f9;border-radius:10px;font-family:Arial,sans-serif;'>
                            <div style='background: $titleColor; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; color: white;'>
                                <h1 style='margin:0;font-size:28px;'>⏰ Modified Holiday Hours</h1>
                                <p style='margin:10px 0 0;font-size:16px;opacity:0.9;'>Ultimate Liempo Haus</p>
                            </div>
                            <div style='padding: 30px; background: white;'>
                                <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Valued Customer, $name,</h2>
                                
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                    We hope this message finds you well. In celebration of $reason, we would like to inform you about modified operating hours at Ultimate Liempo Haus. This adjustment allows our staff to participate in holiday festivities while continuing to serve our valued customers.
                                </p>
                                
                                <div style='background: $bgColor; border: 2px solid $borderColor; padding: 25px; margin: 25px 0; border-radius: 5px;'>
                                    <h3 style='color: $textColor; margin-top: 0;'>MODIFIED HOURS DETAILS</h3>
                                    <table style='width:100%;border-collapse:collapse;color:#b45309;'>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #fbbf24;'><strong>Holiday:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #fbbf24;font-weight:bold;'>$reason</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #fbbf24;'><strong>Date:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #fbbf24;font-weight:bold;'>$formattedDate</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #fbbf24;'><strong>New Opening Time:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #fbbf24;font-weight:bold;color:#d97706;'>{$lateOpeningTimeDisplay}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;'><strong>Regular Closing Time:</strong></td>
                                            <td style='padding:10px;'>{$regularClosingTimeDisplay}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                    <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                    Please be advised that on $formattedDate, Ultimate Liempo Haus will open at {$lateOpeningTimeDisplay} instead of our regular opening time. This modified schedule allows our team to celebrate $reason while ensuring we can continue providing exceptional service to our customers later in the day.
                                </p>
                                
                                <div style='background: #f8f9fa; padding: 25px; border-radius: 5px; margin: 25px 0;'>
                                    <h4 style='color: #2c3e50; margin-top: 0;'>IMPACT ON EXISTING RESERVATIONS:</h4>
                                    <p style='color: #34495e; line-height: 1.8; margin-bottom: 10px;'>
                                        Reservations scheduled before {$lateOpeningTimeDisplay} on $formattedDate:
                                    </p>
                                    <ul style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                        <li>Have been automatically rescheduled to the nearest available time after {$lateOpeningTimeDisplay}</li>
                                        <li>You will receive a separate notification with your new reservation time</li>
                                        <li>If the new time is unsuitable, you may contact us to choose an alternative</li>
                                        <li>Priority will be given to affected customers for preferred time slots</li>
                                    </ul>
                                    <p style='color: #34495e; line-height: 1.8; margin-top: 15px;'>
                                        We sincerely apologize for any inconvenience this schedule adjustment may cause. Our team will make every effort to accommodate your preferred dining time.
                                    </p>
                                </div>
                                
                                <div style='background: #e8f4f8; padding: 25px; border-radius: 5px; margin: 25px 0;'>
                                    <h4 style='color: #2980b9; margin-top: 0;'>OPERATIONAL DETAILS:</h4>
                                    <table style='width:100%;border-collapse:collapse;color:#7f8c8d;'>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #e0e0e0;'><strong>Regular Opening Time:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #e0e0e0;'>Varies by day</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #e0e0e0;'><strong>Holiday Opening Time:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #e0e0e0;font-weight:bold;color:#d97706;'>{$lateOpeningTimeDisplay}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;border-bottom:1px solid #e0e0e0;'><strong>Closing Time:</strong></td>
                                            <td style='padding:10px;border-bottom:1px solid #e0e0e0;'>{$regularClosingTimeDisplay}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding:10px;'><strong>Service Availability:</strong></td>
                                            <td style='padding:10px;'>Full menu and services available from opening time</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                        <p style='color: #34495e; line-height: 1.8; margin-bottom: 15px;'>
                                    For reservations on $formattedDate, please note that our earliest available booking time will be {$lateOpeningTimeDisplay}. We recommend booking in advance to secure your preferred time slot, as we anticipate higher demand due to the holiday.
                                </p>
                                
                                <div style='background: #f8f9fa; padding: 25px; border-radius: 5px; margin: 25px 0; text-align: center;'>
                                    <h4 style='color: #2c3e50; margin-top: 0;'>CONTACT & RESCHEDULING:</h4>
                                    <p style='color: #7f8c8d; margin: 10px 0;'>
                                        <strong>Reservation Changes:</strong> (123) 456-7890<br>
                                        <strong>Email Support:</strong> schedule@ultimateliempo.com<br>
                                        <strong>Online Portal:</strong> www.ultimateliempo.com/reschedule<br>
                                        <strong>Response Time:</strong> Within 12 hours
                                    </p>
                                    <p style='color: #7f8c8d; margin: 10px 0; font-size: 14px;'>
                                        If you need to adjust your reservation time or have questions about the modified hours, our team is ready to assist you.
                                    </p>
                                </div>
                                
                                <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                    We appreciate your understanding as we celebrate $reason with our team. We look forward to serving you and hope you enjoy the holiday festivities as well.
                                </p>
                                
                                <p style='color: #34495e; line-height: 1.8;'>
                                    Thank you for your continued patronage and flexibility.<br><br>
                                    With warm regards,<br>
                                    <strong>The Management Team</strong><br>
                                    Ultimate Liempo Haus<br>
                                    <em>Adapting to Celebrate, Committed to Serve</em>
                                </p>
                            </div>
                            <div style='text-align: center; padding: 25px; color: #7f8c8d; font-size: 12px; border-top: 1px solid #e0e0e0; margin-top: 30px;'>
                                <p style='margin:5px 0;'>&copy; 2024 Ultimate Liempo Haus. All rights reserved.</p>
                                <p style='margin:5px 0;'>This is an automated notification regarding modified holiday hours.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";

                $altBody = "MODIFIED HOLIDAY HOURS NOTICE - ULTIMATE LIEMPO HAUS\n\n";
                $altBody .= "Dear $name,\n\n";
                $altBody .= "MODIFIED OPERATING HOURS ANNOUNCEMENT\n\n";
                $altBody .= "In celebration of $reason, Ultimate Liempo Haus will have modified hours on $formattedDate.\n\n";
                $altBody .= "NEW SCHEDULE:\n";
                $altBody .= "• Opening Time: {$lateOpeningTimeDisplay} (instead of regular opening time)\n";
                $altBody .= "• Closing Time: {$regularClosingTimeDisplay}\n";
                $altBody .= "• Holiday: $reason\n\n";
                $altBody .= "IMPACT ON RESERVATIONS:\n";
                $altBody .= "• Reservations before {$lateOpeningTimeDisplay} have been rescheduled\n";
                $altBody .= "• New reservation times will be communicated separately\n";
                $altBody .= "• Alternative times available upon request\n";
                $altBody .= "• Priority given to affected customers\n\n";
                $altBody .= "CONTACT INFORMATION:\n";
                $altBody .= "Phone: (123) 456-7890\n";
                $altBody .= "Email: schedule@ultimateliempo.com\n\n";
                $altBody .= "We appreciate your understanding and wish you a happy $reason!\n\n";
                $altBody .= "Sincerely,\n";
                $altBody .= "The Management Team\n";
                $altBody .= "Ultimate Liempo Haus\n";
                
                $mail->Subject = $subject;
                $mail->AltBody = $altBody;
            }
            
            error_log("Attempting to send email to $email - Subject: $subject");
            $mail->send();
            error_log("Email successfully sent to $email");
            return true;
        } catch (Exception $e) {
            error_log("Holiday email error to $email: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Send reservation status notification to customer
     * 
     * @param string $email Customer email address
     * @param string $name Customer full name
     * @param string $orderCode Reservation unique code
     * @param string $formatted Formatted date and time
     * @param string $status New reservation status
     * @param string $table Table number
     * @param string|null $reason Reason for status change
     * @param string|null $rescheduleDate New date if rescheduled
     * @param string|null $rescheduleTime New time if rescheduled
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendReservationStatusNotification($email, $name, $orderCode, $formatted, $status, $table, $reason = null, $rescheduleDate = null, $rescheduleTime = null) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'Ultimate Liempo Haus Reservation System');
            $mail->addAddress($email, $name);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            $mail->isHTML(true);
            
            $statusText = strtoupper($status);
            $subject = '';
            $statusColor = '#d97706';
            $icon = '📋';
            $statusDescription = '';
            $additionalInfo = '';
            $actionRequired = '';
            
            switch($status) {
                case 'confirmed':
                    $subject = "Reservation Confirmed - Reference #$orderCode";
                    $statusColor = '#10b981';
                    $icon = '✅';
                    $statusDescription = 'Your reservation has been officially confirmed and is now active.';
                    $actionRequired = 'No action required. Please arrive 15 minutes before your scheduled time.';
                    break;
                    
                case 'cancelled':
                    $subject = "Reservation Cancelled - Reference #$orderCode";
                    $statusColor = '#ef4444';
                    $icon = '❌';
                    $statusDescription = 'Your reservation has been cancelled as per your request or due to unavoidable circumstances.';
                    $actionRequired = 'If this cancellation was unexpected, please contact our customer service team.';
                    if ($reason) {
                        $additionalInfo = "<div style='background: #FEE2E2; border-left: 5px solid #DC2626; padding: 20px; margin: 20px 0; border-radius: 0 5px 5px 0;'>
                                            <h4 style='color: #B91C1C; margin-top: 0;'>CANCELLATION DETAILS</h4>
                                            <p style='color: #7f8c8d; margin: 10px 0;'><strong>Reason Provided:</strong></p>
                                            <p style='color: #444; line-height: 1.6; padding: 15px; background: white; border-radius: 5px;'>$reason</p>
                                          </div>";
                    }
                    break;
                    
                case 'request_reschedule':
                    $subject = "Reschedule Request Received - Reference #$orderCode";
                    $statusColor = '#8b5cf6';
                    $icon = '📅';
                    $statusDescription = 'Your request to reschedule the reservation has been received and is pending administrative review.';
                    $actionRequired = 'Our team will review your request and respond within 24-48 hours.';
                    if ($reason) {
                        $additionalInfo = "<div style='background: #EDE9FE; border-left: 5px solid #8B5CF6; padding: 20px; margin: 20px 0; border-radius: 0 5px 5px 0;'>
                                            <h4 style='color: #7C3AED; margin-top: 0;'>RESCHEDULE REQUEST DETAILS</h4>
                                            <p style='color: #7f8c8d; margin: 10px 0;'><strong>Reason for Reschedule:</strong></p>
                                            <p style='color: #444; line-height: 1.6; padding: 15px; background: white; border-radius: 5px;'>$reason</p>
                                          </div>";
                    }
                    if ($rescheduleDate && $rescheduleTime) {
                        $additionalInfo .= "<div style='background: #F0F9FF; border: 2px solid #0EA5E9; padding: 20px; margin: 20px 0; border-radius: 5px;'>
                                            <h4 style='color: #0369A1; margin-top: 0;'>REQUESTED NEW SCHEDULE</h4>
                                            <table style='width:100%;border-collapse:collapse;color:#0C4A6E;'>
                                                <tr>
                                                    <td style='padding:10px;border-bottom:1px solid #BAE6FD;'><strong>Proposed Date:</strong></td>
                                                    <td style='padding:10px;border-bottom:1px solid #BAE6FD;font-weight:bold;'>$rescheduleDate</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding:10px;border-bottom:1px solid #BAE6FD;'><strong>Proposed Time:</strong></td>
                                                    <td style='padding:10px;border-bottom:1px solid #BAE6FD;font-weight:bold;'>$rescheduleTime</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding:10px;'><strong>Status:</strong></td>
                                                    <td style='padding:10px;font-weight:bold;color:#8B5CF6;'>Pending Administrative Approval</td>
                                                </tr>
                                            </table>
                                          </div>";
                    }
                    break;
                    
                case 'request_cancel':
                    $subject = "Cancellation Request Received - Reference #$orderCode";
                    $statusColor = '#f59e0b';
                    $icon = '⚠️';
                    $statusDescription = 'Your cancellation request has been received and is currently under administrative review.';
                    $actionRequired = 'Our team will process your request and confirm cancellation within 24 hours.';
                    if ($reason) {
                        $additionalInfo = "<div style='background: #FEF3C7; border-left: 5px solid #F59E0B; padding: 20px; margin: 20px 0; border-radius: 0 5px 5px 0;'>
                                            <h4 style='color: #D97706; margin-top: 0;'>CANCELLATION REQUEST DETAILS</h4>
                                            <p style='color: #7f8c8d; margin: 10px 0;'><strong>Reason Provided:</strong></p>
                                            <p style='color: #444; line-height: 1.6; padding: 15px; background: white; border-radius: 5px;'>$reason</p>
                                          </div>";
                    }
                    break;
                    
                case 'rescheduled':
                    $subject = "Reservation Rescheduled - Reference #$orderCode";
                    $statusColor = '#8b5cf6';
                    $icon = '🔄';
                    $statusDescription = 'Your reservation has been successfully rescheduled to a new date and time.';
                    $actionRequired = 'Please note your new reservation details below and update your calendar accordingly.';
                    if ($rescheduleDate && $rescheduleTime) {
                        $additionalInfo = "<div style='background: #F0F9FF; border: 2px solid #0EA5E9; padding: 25px; margin: 25px 0; border-radius: 5px;'>
                                            <h4 style='color: #0369A1; margin-top: 0;'>NEW RESERVATION SCHEDULE</h4>
                                            <table style='width:100%;border-collapse:collapse;color:#0C4A6E;'>
                                                <tr>
                                                    <td style='padding:12px;border-bottom:2px solid #BAE6FD;background:#E0F2FE;'><strong>Rescheduled Date:</strong></td>
                                                    <td style='padding:12px;border-bottom:2px solid #BAE6FD;background:#E0F2FE;font-weight:bold;font-size:16px;color:#0369A1;'>$rescheduleDate</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding:12px;border-bottom:2px solid #BAE6FD;'><strong>Rescheduled Time:</strong></td>
                                                    <td style='padding:12px;border-bottom:2px solid #BAE6FD;font-weight:bold;font-size:16px;color:#0369A1;'>$rescheduleTime</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding:12px;'><strong>Confirmation Status:</strong></td>
                                                    <td style='padding:12px;font-weight:bold;color:#10B981;'>Confirmed and Active</td>
                                                </tr>
                                            </table>
                                            <p style='color: #0C4A6E; margin-top: 15px; font-size: 14px;'>
                                                <strong>Note:</strong> Your reservation is now confirmed for the new date and time. Please update your calendar accordingly.
                                            </p>
                                          </div>";
                    }
                    if ($reason) {
                        $additionalInfo .= "<div style='background: #EDE9FE; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                                            <h4 style='color: #7C3AED; margin-top: 0;'>RESCHEDULE REASON</h4>
                                            <p style='color: #444; line-height: 1.6;'>$reason</p>
                                          </div>";
                    }
                    break;
                    
                default:
                    $subject = "Reservation Status Update - Reference #$orderCode";
                    $statusDescription = 'Your reservation status has been updated.';
                    $actionRequired = 'Please review the details below and contact us if you have any questions.';
            }

            $mail->Subject = $subject;
            
            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f8f9fa; }
                        .container { max-width: 700px; margin: 0 auto; background: #ffffff; border-radius: 10px; border: 1px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0,0,0,0.08); overflow: hidden; }
                        .header { background: linear-gradient(135deg, #D4AF37, #B8860B); padding: 35px; border-radius: 10px 10px 0 0; color: white; text-align: center; }
                        .content { padding: 35px; background: #ffffff; }
                        .footer { text-align: center; padding: 25px; font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; background: #f9fafb; }
                        .status-badge { display: inline-block; padding: 12px 30px; border-radius: 30px; font-weight: bold; margin: 20px 0; background: $statusColor; color: white; font-size: 16px; letter-spacing: 0.5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                        .info-box { background: #f8fafc; border: 2px solid #e5e7eb; padding: 25px; margin: 30px 0; border-radius: 8px; }
                        .icon { font-size: 28px; margin-right: 12px; vertical-align: middle; }
                        .details { color: #4b5563; margin: 8px 0; line-height: 1.7; }
                        .contact-info { background: linear-gradient(135deg, #f0f9ff, #e0f2fe); padding: 25px; border-radius: 8px; margin: 30px 0; text-align: center; border-left: 5px solid #0ea5e9; }
                        .action-box { background: #fef3c7; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 5px solid #f59e0b; }
                        .reservation-details { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 25px 0; }
                        .detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
                        .detail-row:last-child { border-bottom: none; }
                        .detail-label { font-weight: 600; color: #6b7280; }
                        .detail-value { font-weight: 500; color: #111827; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1 style='margin:0;font-size:32px;letter-spacing:0.5px;'>Ultimate Liempo Haus</h1>
                            <p style='margin:10px 0 0;font-size:16px;opacity:0.9;'>Reservation Status Update</p>
                        </div>
                        <div class='content'>
                            <h2 style='color: #2c3e50; margin-bottom: 10px;'>Dear $name,</h2>
                            <p style='color: #4b5563; line-height: 1.7; margin-bottom: 20px;'>
                                Thank you for choosing Ultimate Liempo Haus for your dining experience. We are writing to inform you about an important update regarding your reservation.
                            </p>
                            
                            <div style='text-align: center; margin: 25px 0;'>
                                <div class='status-badge'>
                                    <span class='icon'>$icon</span> $statusText
                                </div>
                                <p style='color: #6b7280; margin-top: 10px; font-size: 16px;'>$statusDescription</p>
                            </div>
                            
                            <div class='reservation-details'>
                                <h3 style='color: #374151; margin-top: 0; margin-bottom: 20px;'>RESERVATION DETAILS</h3>
                                <div class='detail-row'>
                                    <span class='detail-label'>Reservation Reference:</span>
                                    <span class='detail-value' style='font-family: monospace; font-size: 18px; color: #059669;'>$orderCode</span>
                                </div>
                                <div class='detail-row'>
                                    <span class='detail-label'>Table Assignment:</span>
                                    <span class='detail-value'>Table $table</span>
                                </div>
                                <div class='detail-row'>
                                    <span class='detail-label'>Original Schedule:</span>
                                    <span class='detail-value'>$formatted</span>
                                </div>
                                <div class='detail-row'>
                                    <span class='detail-label'>Update Date:</span>
                                    <span class='detail-value'>" . date('F j, Y \a\t g:i A') . "</span>
                                </div>
                            </div>
                            
                            $additionalInfo
                            
                            <div class='action-box'>
                                <h4 style='color: #d97706; margin-top: 0;'>ACTION REQUIRED / NEXT STEPS</h4>
                                <p style='color: #92400e; margin: 10px 0; line-height: 1.6;'>$actionRequired</p>
                                <p style='color: #92400e; margin: 10px 0; font-size: 14px;'>
                                    Please review all details carefully and contact our customer service team if you have any questions or require further assistance.
                                </p>
                            </div>
                            
                            <p style='color: #4b5563; line-height: 1.7; margin: 25px 0;'>
                                At Ultimate Liempo Haus, we are committed to providing you with exceptional service and ensuring your dining experience meets the highest standards. We appreciate your understanding and cooperation as we manage reservation updates.
                            </p>
                            
                            <div class='contact-info'>
                                <h4 style='color: #0369a1; margin-top: 0;'>CUSTOMER SERVICE CONTACT</h4>
                                <p style='color: #0c4a6e; margin: 12px 0;'>
                                    <strong>📞 Telephone:</strong> (123) 456-7890<br>
                                    <strong>📧 Email:</strong> reservations@ultimateliempo.com<br>
                                    <strong>🌐 Website:</strong> www.ultimateliempo.com/reservations<br>
                                    <strong>⏰ Service Hours:</strong> Monday to Sunday, 8:00 AM to 10:00 PM
                                </p>
                                <p style='color: #0c4a6e; margin: 12px 0; font-size: 14px;'>
                                    Our dedicated customer service team is available to assist you with any questions or concerns regarding your reservation.
                                </p>
                            </div>
                            
                            <p style='color: #4b5563; line-height: 1.7; margin: 25px 0;'>
                                We value your patronage and look forward to providing you with an unforgettable dining experience at Ultimate Liempo Haus. Should you require any special arrangements or have specific requests for your visit, please do not hesitate to inform our team.
                            </p>
                            
                            <p style='color: #4b5563; line-height: 1.7;'>
                                Thank you for choosing Ultimate Liempo Haus. We appreciate your business and the opportunity to serve you.<br><br>
                                
                                With warm regards,<br>
                                <strong style='color: #111827;'>The Reservation Management Team</strong><br>
                                Ultimate Liempo Haus<br>
                                <em style='color: #6b7280;'>Where Every Meal is a Celebration</em>
                            </p>
                        </div>
                        <div class='footer'>
                            <p style='margin: 5px 0;'>&copy; " . date('Y') . " Ultimate Liempo Haus. All rights reserved.</p>
                            <p style='margin: 5px 0;'>123 Gourmet Street, Manila, Philippines</p>
                            <p style='margin: 5px 0;'>This is an automated message. Please do not reply directly to this email.</p>
                            <p style='margin: 5px 0; font-size: 12px; color: #9ca3af;'>
                                If you believe you received this email in error, please contact our customer service team immediately.
                            </p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $altBody = "ULTIMATE LIEMPO HAUS - RESERVATION STATUS UPDATE\n";
            $altBody .= "=============================================\n\n";
            $altBody .= "Dear $name,\n\n";
            $altBody .= "Thank you for choosing Ultimate Liempo Haus for your dining experience.\n";
            $altBody .= "We are writing to inform you about an important update regarding your reservation.\n\n";
            $altBody .= "RESERVATION STATUS: $statusText\n";
            $altBody .= "Status Description: $statusDescription\n\n";
            $altBody .= "RESERVATION DETAILS:\n";
            $altBody .= "• Reservation Reference: $orderCode\n";
            $altBody .= "• Table Assignment: Table $table\n";
            $altBody .= "• Original Schedule: $formatted\n";
            $altBody .= "• Update Date: " . date('F j, Y \a\t g:i A') . "\n\n";
            
            if ($reason) {
                $altBody .= "REASON FOR STATUS CHANGE:\n";
                $altBody .= "$reason\n\n";
            }
            
            if ($rescheduleDate && $rescheduleTime) {
                $altBody .= "NEW SCHEDULE DETAILS:\n";
                $altBody .= "• Rescheduled Date: $rescheduleDate\n";
                $altBody .= "• Rescheduled Time: $rescheduleTime\n";
                $altBody .= "• Status: Confirmed and Active\n\n";
            }
            
            $altBody .= "ACTION REQUIRED / NEXT STEPS:\n";
            $altBody .= "$actionRequired\n\n";
            
            $altBody .= "CUSTOMER SERVICE CONTACT INFORMATION:\n";
            $altBody .= "• Telephone: (123) 456-7890\n";
            $altBody .= "• Email: reservations@ultimateliempo.com\n";
            $altBody .= "• Website: www.ultimateliempo.com/reservations\n";
            $altBody .= "• Service Hours: Monday to Sunday, 8:00 AM to 10:00 PM\n\n";
            
            $altBody .= "We value your patronage and look forward to providing you with an unforgettable dining experience at Ultimate Liempo Haus.\n\n";
            $altBody .= "Thank you for choosing Ultimate Liempo Haus.\n\n";
            $altBody .= "Sincerely,\n";
            $altBody .= "The Reservation Management Team\n";
            $altBody .= "Ultimate Liempo Haus\n";
            $altBody .= "Where Every Meal is a Celebration\n\n";
            $altBody .= "---\n";
            $altBody .= "© " . date('Y') . " Ultimate Liempo Haus. All rights reserved.\n";
            $altBody .= "123 Gourmet Street, Manila, Philippines\n";
            $altBody .= "This is an automated message. Please do not reply directly to this email.\n";
            
            $mail->AltBody = $altBody;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Reservation status email error to $email: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Global Class
 * 
 * Main controller class handling all system operations including
 * user management, reservation processing, menu management,
 * and system-wide functionalities. Extends database connection
 * for seamless data operations.
 */
class global_class extends db_connect
{
    /**
     * Constructor method
     * 
     * Initializes the database connection by calling parent constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Set emergency closure for the restaurant
     * 
     * @param string $date Date of emergency closure
     * @param string $reason Reason for emergency closure
     * @return array Operation result with success status and message
     */
    public function sendEmergencyClosure($date, $reason) {
        try {
            $checkTableSQL = "SHOW TABLES LIKE 'emergency_closures'";
            $tableCheck = $this->conn->query($checkTableSQL);
            
            if ($tableCheck->num_rows === 0) {
                $createTableSQL = "
                    CREATE TABLE emergency_closures (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        closure_date DATE NOT NULL,
                        reason TEXT NOT NULL,
                        closure_type ENUM('emergency', 'holiday', 'maintenance') DEFAULT 'emergency',
                        status ENUM('active', 'inactive') DEFAULT 'active',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_date (closure_date),
                        INDEX idx_status (status)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                $this->conn->query($createTableSQL);
            }
            
            $deactivateSQL = "UPDATE emergency_closures SET status = 'inactive' WHERE status = 'active' AND closure_type = 'emergency'";
            $this->conn->query($deactivateSQL);
            
            $stmt = $this->conn->prepare("INSERT INTO emergency_closures (closure_date, reason, closure_type, status) VALUES (?, ?, 'emergency', 'active')");
            $stmt->bind_param("ss", $date, $reason);
            
            if ($stmt->execute()) {
                $mailer = new Mailer();
                
                $users = $this->getAllUsers();
                $successCount = 0;
                
                foreach ($users as $user) {
                    $result = $mailer->sendEmergencyClosureNotification(
                        $user['user_email'],
                        $user['user_fname'] . ' ' . $user['user_lname'],
                        $date,
                        $reason
                    );
                    
                    if ($result) $successCount++;
                }
                
                $this->cancelReservationsForDate($date, 'Emergency closure: ' . $reason);
                
                return [
                    'success' => true,
                    'message' => 'Emergency closure set successfully. Notifications have been dispatched to ' . $successCount . ' registered users.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to establish emergency closure. Please verify system parameters and try again.'
                ];
            }
        } catch (Exception $e) {
            error_log("Emergency closure error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'System error encountered: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restore system access by deactivating emergency closures
     * 
     * @return array Operation result with success status and message
     */
    public function restoreSystemAccess() {
        try {
            $checkTableSQL = "SHOW TABLES LIKE 'emergency_closures'";
            $tableCheck = $this->conn->query($checkTableSQL);
            
            if ($tableCheck->num_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'Emergency closures table not found in database structure.'
                ];
            }
            
            $stmt = $this->conn->prepare("UPDATE emergency_closures SET status = 'inactive' WHERE status = 'active'");
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'System access has been successfully restored. Normal operations may now resume.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to restore system access. Please contact system administrator.'
                ];
            }
        } catch (Exception $e) {
            error_log("Restore system access error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error encountered during restoration: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve all active users from the database
     * 
     * @return array List of active users with their details
     */
    public function getAllUsers() {
        $users = [];
        try {
            $stmt = $this->conn->prepare("SELECT user_id, user_fname, user_lname, user_email FROM user WHERE user_status = 1");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        } catch (Exception $e) {
            error_log("Get all users error: " . $e->getMessage());
        }
        
        return $users;
    }

    /**
     * Send holiday notice to all registered users
     * 
     * @param string $date Holiday date
     * @param string $reason Holiday name or description
     * @param string $holidayType Type of holiday (closure or late_opening)
     * @param string|null $lateOpeningTime New opening time for late opening holidays
     * @param string|null $lateClosingTime New closing time for late opening holidays
     * @return array Operation result with success status and message
     */
    public function sendHolidayNotice($date, $reason, $holidayType = 'closure', $lateOpeningTime = null, $lateClosingTime = null) {
        try {
            $mailer = new Mailer($this->conn);
            
            $users = $this->getAllUsers();
            $successCount = 0;
            
            foreach ($users as $user) {
                $result = $mailer->sendHolidayNotification(
                    $user['user_email'],
                    $user['user_fname'] . ' ' . $user['user_lname'],
                    $date,
                    $reason,
                    $holidayType,
                    $lateOpeningTime,
                    $lateClosingTime
                );
                
                if ($result) $successCount++;
            }
            
            $this->recordHoliday($date, $reason, $holidayType, $lateOpeningTime, $lateClosingTime);
            
            if ($holidayType === 'closure') {
                $this->cancelReservationsForDate($date, 'Holiday closure: ' . $reason);
            } elseif ($holidayType === 'late_opening' && $lateOpeningTime) {
                $this->rescheduleMorningReservations($date, $lateOpeningTime);
            }
            
            return [
                'success' => true,
                'message' => 'Holiday notification dispatched to ' . $successCount . ' registered users.'
            ];
        } catch (Exception $e) {
            error_log("Holiday notice error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error encountered during holiday notification: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send emergency notification to all registered users
     *
     * @param string $date Closure date
     * @param string $reason Reason for closure
     * @return array result with success and message
     */
    public function sendEmergencyNotice($date, $reason) {
        try {
            $mailer = new Mailer($this->conn);
            $users = $this->getAllUsers();
            $successCount = 0;

            foreach ($users as $user) {
                $ok = $mailer->sendEmergencyClosureNotification(
                    $user['user_email'],
                    $user['user_fname'] . ' ' . $user['user_lname'],
                    $date,
                    $reason
                );
                if ($ok) $successCount++;
            }

            return [
                'success' => true,
                'message' => 'Emergency notification dispatched to ' . $successCount . ' registered users.'
            ];
        } catch (Exception $e) {
            error_log('Emergency notice error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error encountered during emergency notification: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Record holiday schedule in the database
     * 
     * @param string $date Holiday date
     * @param string $reason Holiday name or description
     * @param string $holidayType Type of holiday
     * @param string|null $lateOpeningTime New opening time
     */
    private function recordHoliday($date, $reason, $holidayType, $lateOpeningTime = null, $lateClosingTime = null) {
        try {
            $checkTableSQL = "SHOW TABLES LIKE 'holiday_schedules'";
            $tableCheck = $this->conn->query($checkTableSQL);
            
            if ($tableCheck->num_rows === 0) {
                $createTableSQL = "
                    CREATE TABLE holiday_schedules (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        holiday_date DATE NOT NULL,
                        holiday_name VARCHAR(255) NOT NULL,
                        holiday_type ENUM('closure', 'late_opening') DEFAULT 'closure',
                        late_opening_time TIME NULL,
                        late_closing_time TIME NULL,
                        status ENUM('active', 'inactive') DEFAULT 'active',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_date (holiday_date),
                        INDEX idx_status (status)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                $this->conn->query($createTableSQL);
            }
            
            $deactivateSQL = "UPDATE holiday_schedules SET status = 'inactive' WHERE holiday_date = ?";
            $stmt = $this->conn->prepare($deactivateSQL);
            $stmt->bind_param("s", $date);
            $stmt->execute();
            
            $stmt = $this->conn->prepare("INSERT INTO holiday_schedules (holiday_date, holiday_name, holiday_type, late_opening_time, late_closing_time, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("sssss", $date, $reason, $holidayType, $lateOpeningTime, $lateClosingTime);
            $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Record holiday error: " . $e->getMessage());
        }
    }

    /**
     * Cancel all reservations for a specific date
     * 
     * @param string $date Date for which to cancel reservations
     * @param string $reason Reason for cancellation
     */
    private function cancelReservationsForDate($date, $reason) {
        try {
            $stmt = $this->conn->prepare("SELECT id, reserve_user_id, reserve_unique_code FROM reservations WHERE date_schedule = ? AND status IN ('pending', 'confirmed')");
            $stmt->bind_param("s", $date);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $mailer = new Mailer();
            
            while ($row = $result->fetch_assoc()) {
                $updateStmt = $this->conn->prepare("UPDATE reservations SET status = 'cancelled', cancellation_reason = ? WHERE id = ?");
                $updateStmt->bind_param("si", $reason, $row['id']);
                $updateStmt->execute();
                
                $userStmt = $this->conn->prepare("SELECT user_fname, user_lname, user_email FROM user WHERE user_id = ?");
                $userStmt->bind_param("i", $row['reserve_user_id']);
                $userStmt->execute();
                $userResult = $userStmt->get_result();
                
                if ($userData = $userResult->fetch_assoc()) {
                    $mailer->sendReservationStatusNotification(
                        $userData['user_email'],
                        $userData['user_fname'] . ' ' . $userData['user_lname'],
                        $row['reserve_unique_code'],
                        $date,
                        'cancelled',
                        'N/A',
                        $reason
                    );
                }
            }
            
        } catch (Exception $e) {
            error_log("Cancel reservations error: " . $e->getMessage());
        }
    }

    /**
     * Reschedule morning reservations for late opening holidays
     * 
     * @param string $date Holiday date
     * @param string $lateOpeningTime New opening time
     */
    private function rescheduleMorningReservations($date, $lateOpeningTime) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.id, r.reserve_user_id, r.reserve_unique_code, r.time_schedule, 
                       u.user_fname, u.user_lname, u.user_email 
                FROM reservations r
                JOIN user u ON r.reserve_user_id = u.user_id
                WHERE r.date_schedule = ? 
                AND r.status IN ('pending', 'confirmed')
                AND TIME(r.time_schedule) < ?
            ");
            $stmt->bind_param("ss", $date, $lateOpeningTime);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $mailer = new Mailer();
            
            while ($row = $result->fetch_assoc()) {
                $newTime = date('H:i', strtotime($lateOpeningTime . ' +1 hour'));
                
                $updateStmt = $this->conn->prepare("
                    UPDATE reservations 
                    SET time_schedule = ?, 
                        status = 'pending', 
                        reschedule_reason = 'Holiday late opening' 
                    WHERE id = ?
                ");
                $updateStmt->bind_param("si", $newTime, $row['id']);
                $updateStmt->execute();
                
                $mailer->sendReservationStatusNotification(
                    $row['user_email'],
                    $row['user_fname'] . ' ' . $row['user_lname'],
                    $row['reserve_unique_code'],
                    $date . ' ' . $row['time_schedule'],
                    'rescheduled',
                    'N/A',
                    'Holiday late opening - Restaurant opens at ' . $lateOpeningTime,
                    $date,
                    $newTime
                );
            }
            
        } catch (Exception $e) {
            error_log("Reschedule morning reservations error: " . $e->getMessage());
        }
    }

    /**
     * Send verification code to user email for registration
     * 
     * @param string $email User email address
     * @return array Operation result with success status and verification code
     */
    public function sendVerificationCode($email) {
        try {
            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['verification_codes'][$email] = [
                'code' => $verification_code,
                'timestamp' => time(),
                'attempts' => 0
            ];
            
            $email_sent = $this->sendVerificationCodeEmail($email, $verification_code);
            
            if ($email_sent) {
                return [
                    'success' => true,
                    'verification_code' => $verification_code,
                    'message' => 'Verification code has been dispatched to your registered email address.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to dispatch verification email. Please verify your email address and try again.'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send verification code: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send verification code email to user
     * 
     * @param string $email User email address
     * @param string $verification_code 6-digit verification code
     * @return bool True if email sent successfully, false otherwise
     */
    private function sendVerificationCodeEmail($email, $verification_code) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'Ultimate Liempo Haus');
            $mail->addAddress($email);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification - Ultimate Liempo Haus';

            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Email Verification</title>
                </head>
                <body>
                    <div style='max-width:600px;margin:auto;padding:20px;background:#f9f9f9;border-radius:10px;font-family:Arial,sans-serif;'>
                        <div style='background: linear-gradient(135deg, #D4AF37, #B8860B); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; color: white;'>
                            <h1 style='margin:0;font-size:28px;'>Ultimate Liempo Haus</h1>
                            <p style='margin:10px 0 0;font-size:16px;opacity:0.9;'>Email Verification Code</p>
                        </div>
                        <div style='padding: 30px; background: white;'>
                            <h2 style='color: #2c3e50; margin-bottom: 20px;'>Dear Customer,</h2>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                Thank you for initiating the registration process with Ultimate Liempo Haus. To complete your account setup and ensure the security of your information, we require email verification.
                            </p>
                            
                            <div style='text-align: center; margin: 30px 0;'>
                                <div style='font-size: 42px; font-weight: bold; letter-spacing: 15px; color: #D4AF37; margin: 20px 0; padding: 30px; background: #FEF3C7; border-radius: 10px; border: 3px dashed #D4AF37;'>
                                    {$verification_code}
                                </div>
                                <p style='color: #7f8c8d; font-size: 18px; font-weight: bold;'>Your Verification Code</p>
                            </div>
                            
                            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0;'>
                                <h4 style='color: #2c3e50; margin-top: 0;'>Instructions:</h4>
                                <ol style='color: #34495e; line-height: 1.8; padding-left: 20px;'>
                                    <li>Copy the 6-digit verification code displayed above</li>
                                    <li>Return to the registration page on our website</li>
                                    <li>Paste or enter the code in the verification field</li>
                                    <li>Complete the remaining registration steps</li>
                                </ol>
                            </div>
                            
                            <div style='background: #fee2e2; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 5px solid #dc2626;'>
                                <h4 style='color: #b91c1c; margin-top: 0;'>Important Security Notice:</h4>
                                <ul style='color: #7f8c8d; line-height: 1.8; padding-left: 20px;'>
                                    <li>This verification code will expire in <strong>10 minutes</strong></li>
                                    <li>Do not share this code with anyone</li>
                                    <li>Ultimate Liempo Haus will never ask for this code via phone call</li>
                                    <li>If you did not initiate this registration, please disregard this email</li>
                                </ul>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8; margin-bottom: 20px;'>
                                Once your email is verified, you will have full access to our reservation system, special promotions, and exclusive member benefits at Ultimate Liempo Haus.
                            </p>
                            
                            <div style='background: #e8f4f8; padding: 20px; border-radius: 8px; margin: 25px 0; text-align: center;'>
                                <h4 style='color: #2980b9; margin-top: 0;'>Need Assistance?</h4>
                                <p style='color: #7f8c8d; margin: 10px 0;'>
                                    <strong>Customer Support:</strong> (123) 456-7890<br>
                                    <strong>Email Support:</strong> verification@ultimateliempo.com<br>
                                    <strong>Support Hours:</strong> 8:00 AM - 10:00 PM Daily
                                </p>
                            </div>
                            
                            <p style='color: #34495e; line-height: 1.8;'>
                                We look forward to welcoming you to the Ultimate Liempo Haus family!<br><br>
                                Sincerely,<br>
                                <strong>The Ultimate Liempo Haus Team</strong><br>
                                <em>Your Trusted Dining Destination</em>
                            </p>
                        </div>
                        <div style='text-align: center; padding: 25px; color: #7f8c8d; font-size: 12px; border-top: 1px solid #e0e0e0; margin-top: 30px;'>
                            <p style='margin:5px 0;'>&copy; 2024 Ultimate Liempo Haus. All rights reserved.</p>
                            <p style='margin:5px 0;'>This is an automated verification email. Please do not reply to this message.</p>
                            <p style='margin:5px 0;'>For security purposes, this email was generated in response to a registration request.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $altBody = "ULTIMATE LIEMPO HAUS - EMAIL VERIFICATION CODE\n\n";
            $altBody .= "Dear Customer,\n\n";
            $altBody .= "Thank you for registering with Ultimate Liempo Haus.\n\n";
            $altBody .= "YOUR VERIFICATION CODE: {$verification_code}\n\n";
            $altBody .= "IMPORTANT INFORMATION:\n";
            $altBody .= "• This code expires in 10 minutes\n";
            $altBody .= "• Enter this code on the registration page to complete your account setup\n";
            $altBody .= "• Do not share this code with anyone\n";
            $altBody .= "• If you did not request this code, please ignore this email\n\n";
            $altBody .= "Need help? Contact our support team at (123) 456-7890\n\n";
            $altBody .= "Welcome to Ultimate Liempo Haus!\n\n";
            $altBody .= "Sincerely,\n";
            $altBody .= "The Ultimate Liempo Haus Team\n";

            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Verification email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register new user with optional email verification
     * 
     * @param string $email User email address
     * @param string $password User password
     * @param string $verification_code Optional verification code
     * @return array Registration result with success status and message
     */
    public function RegisterWithVerification($email, $password, $verification_code = '') {
        try {
            if (!empty($verification_code)) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                $stored_code = $_SESSION['verification_codes'][$email] ?? null;
                
                if (!$stored_code || $stored_code['code'] !== $verification_code) {
                    return [
                        'success' => false,
                        'message' => 'Invalid verification code provided.'
                    ];
                }
                
                if (time() - $stored_code['timestamp'] > 600) {
                    unset($_SESSION['verification_codes'][$email]);
                    return [
                        'success' => false,
                        'message' => 'Verification code has expired. Please request a new code.'
                    ];
                }
                
                unset($_SESSION['verification_codes'][$email]);
                
                if ($this->checkEmailExists($email)) {
                    return ['success' => false, 'message' => 'This email address is already registered in our system.'];
                }

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $email_prefix = explode('@', $email)[0];
                $first_name = ucfirst($email_prefix);
                $last_name = 'Customer';
                
                $stmt = $this->conn->prepare("INSERT INTO user (user_fname, user_lname, user_email, user_password, user_position, user_status, is_verified) VALUES (?, ?, ?, ?, 'customer', 1, 1)");
                $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Registration successfully completed! You may now proceed to log into your account.'];
                } else {
                    return ['success' => false, 'message' => 'Registration process failed. Please attempt the registration again.'];
                }
            } else {
                if ($this->checkEmailExists($email)) {
                    return ['success' => false, 'message' => 'This email address is already registered in our system.'];
                }

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $email_prefix = explode('@', $email)[0];
                $first_name = ucfirst($email_prefix);
                $last_name = 'Customer';
                
                $verification_token = bin2hex(random_bytes(50));
                $token_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                $stmt = $this->conn->prepare("INSERT INTO user (user_fname, user_lname, user_email, user_password, user_position, user_status, verification_token, is_verified, token_expires) VALUES (?, ?, ?, ?, 'customer', 1, ?, 0, ?)");
                $stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $verification_token, $token_expires);
                
                if ($stmt->execute()) {
                    $user_id = $this->conn->insert_id;
                    
                    $email_sent = $this->sendVerificationEmail($email, $verification_token, $user_id);
                    
                    if ($email_sent) {
                        return [
                            'success' => true,
                            'message' => 'Registration successfully completed! Please check your email to verify your account.'
                        ];
                    } else {
                        $this->conn->query("DELETE FROM user WHERE user_id = $user_id");
                        return [
                            'success' => false,
                            'message' => 'Failed to send verification email. Please attempt the registration process again.'
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => 'Registration process failed. Please attempt the registration again.'
                    ];
                }
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error encountered during registration: ' . $e->getMessage()];
        }
    }

    /**
     * Retrieve reservation details by reservation ID
     * 
     * @param int $reservation_id Reservation identifier
     * @return array|null Reservation details or null if not found
     */
    public function getReservationById($reservation_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            return null;
        } catch (Exception $e) {
            error_log("Error getting reservation by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve comprehensive system analytics data
     * 
     * @return array|bool Analytics data or false on failure
     */
    public function getDataAnalytics()
    {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM user WHERE user_status = 1) AS totalUsers,
                (SELECT COUNT(*) FROM reservations) AS totalReservations,
                (SELECT COUNT(*) FROM reservations WHERE status = 'pending') AS pendingReservations,
                (SELECT COUNT(*) FROM reservations WHERE status = 'confirmed') AS confirmedReservations,
                (SELECT COUNT(*) FROM reservations WHERE status = 'completed') AS completedReservations,
                (SELECT COUNT(*) FROM menu WHERE menu_status = 1) AS activeMenuItems,
                (SELECT COUNT(*) FROM deals WHERE deal_type = 'promo_deals') AS totalPromos,
                (SELECT COUNT(*) FROM deals WHERE deal_type = 'group_deals') AS totalGroupDeals,
                (SELECT COALESCE(SUM(grand_total), 0) FROM reservations WHERE status = 'completed') AS totalSales,
                (SELECT COUNT(*) FROM emergency_closures WHERE status = 'active') AS activeClosures,
                (SELECT COUNT(*) FROM holiday_schedules WHERE status = 'active') AS activeHolidays
        ";

        $result = $this->conn->query($query);

        if ($result) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    /**
     * Update user account information
     * 
     * @param int $user_id User identifier
     * @param string $first_name Updated first name
     * @param string $last_name Updated last name
     * @param string $email Updated email address
     * @param string $password Updated password (optional)
     * @return bool True if update successful, false otherwise
     */
    public function UpdateAccount($user_id, $first_name, $last_name, $email, $password) {
        $queryStr = "UPDATE `user` SET `user_fname` = ?, `user_lname` = ?, `user_email` = ?";

        if (!empty($password)) {
            $queryStr .= ", `user_password` = ?";
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        }

        $queryStr .= " WHERE `user_id` = ?";
        $query = $this->conn->prepare($queryStr);

        if (!$query) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        if (!empty($password)) {
            $query->bind_param("ssssi", $first_name, $last_name, $email, $hashedPassword, $user_id);
        } else {
            $query->bind_param("sssi", $first_name, $last_name, $email, $user_id);
        }

        return $query->execute();
    }

    /**
     * Update archive status of reservation
     * 
     * @param int $reservation_id Reservation identifier
     * @param int $status Archive status (0 or 1)
     * @param string $column Database column to update
     * @return array Operation result with success status and message
     */
    public function updateArchived($reservation_id, $status,$column){
        $stmt = $this->conn->prepare("UPDATE `reservations` SET $column = ? WHERE `id` = ?");
        $stmt->bind_param("ii", $status, $reservation_id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Reservation archive status updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => $stmt->error
            ];
        }
    }

    /**
     * Update reservation status with notification
     * 
     * @param int $reservation_id Reservation identifier
     * @param string $status New reservation status
     * @param string|null $reason Reason for status change
     * @return array Operation result with success status and message
     */
    public function UpdateReservationStatus($reservation_id, $status, $reason = null){
        try {
            $stmt = $this->conn->prepare("UPDATE `reservations` SET `status` = ?, `cancellation_reason` = ? WHERE `id` = ?");
            $stmt->bind_param("ssi", $status, $reason, $reservation_id);

            if ($stmt->execute()) {
                $reservation = $this->getReservationById($reservation_id);
                if ($reservation) {
                    $user = $this->getUserById($reservation['reserve_user_id']);
                    if ($user) {
                        $mailer = new Mailer();
                        $mailer->sendReservationStatusNotification(
                            $user['user_email'],
                            $user['user_fname'] . ' ' . $user['user_lname'],
                            $reservation['reserve_unique_code'],
                            $reservation['date_schedule'] . ' ' . $reservation['time_schedule'],
                            $status,
                            $reservation['table_code'],
                            $reason
                        );
                    }
                }

                return [
                    'success' => true,
                    'message' => 'Reservation status updated successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error encountered during status update: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Approve reschedule request for a reservation
     * 
     * @param int $reservation_id Reservation identifier
     * @param string|null $reason Additional reason from admin
     * @return array Operation result with success status and message
     */
    public function ApproveReschedule($reservation_id, $reason = null) {
        $stmt = $this->conn->prepare("SELECT request_details, reserve_user_id, reserve_unique_code FROM reservations WHERE id = ?");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Prepare failed: ' . $this->conn->error
            ];
        }

        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Reservation not found in database records.'
            ];
        }

        $request_details = json_decode($result['request_details'], true);

        if (isset($request_details['newDate']) && isset($request_details['newTime'])&& isset($request_details['seats'])) {
            $newDate = $request_details['newDate'];
            $newTime = $request_details['newTime'];
            $newSeats = $request_details['seats'];
            $clientReason = $request_details['reason'] ?? '';

            $stmt = $this->conn->prepare(
                "UPDATE reservations 
                 SET status = 'confirmed', 
                     date_schedule = ?, 
                     time_schedule = ?,
                     seats = ?, 
                     request_details = NULL,
                     reschedule_reason = ?
                 WHERE id = ?"
            );

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Prepare failed: ' . $this->conn->error
                ];
            }

            $combinedReason = $clientReason . ($reason ? ' | Admin note: ' . $reason : '');
            $stmt->bind_param("ssisi", $newDate, $newTime, $newSeats, $combinedReason, $reservation_id);

            if ($stmt->execute()) {
                $user = $this->getUserById($result['reserve_user_id']);
                if ($user) {
                    $mailer = new Mailer();
                    $mailer->sendReservationStatusNotification(
                        $user['user_email'],
                        $user['user_fname'] . ' ' . $user['user_lname'],
                        $result['reserve_unique_code'],
                        $newDate . ' ' . $newTime,
                        'rescheduled',
                        'N/A',
                        $combinedReason,
                        $newDate,
                        $newTime
                    );
                }

                return [
                    'success' => true,
                    'message' => 'Reservation schedule updated successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Update failed: ' . $stmt->error
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'No new schedule information found in request details.'
            ];
        }
    }

    /**
     * Approve reservation status with notification
     * 
     * @param int $reservation_id Reservation identifier
     * @param string $status New reservation status
     * @param string|null $reason Reason for status change
     * @return array Operation result with success status and message
     */
    public function ApproveReservationStatus($reservation_id, $status, $reason = null){
        $stmt = $this->conn->prepare("UPDATE `reservations` SET `status` = ?, `approval_reason` = ? WHERE `id` = ?");
        $stmt->bind_param("ssi", $status, $reason, $reservation_id);

        if ($stmt->execute()) {
            $reservation = $this->getReservationById($reservation_id);
            if ($reservation) {
                $user = $this->getUserById($reservation['reserve_user_id']);
                if ($user) {
                    $mailer = new Mailer();
                    $mailer->sendReservationStatusNotification(
                        $user['user_email'],
                        $user['user_fname'] . ' ' . $user['user_lname'],
                        $reservation['reserve_unique_code'],
                        $reservation['date_schedule'] . ' ' . $reservation['time_schedule'],
                        $status,
                        $reservation['table_code'],
                        $reason
                    );
                }
            }

            return [
                'success' => true,
                'message' => 'Reservation status updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => $stmt->error
            ];
        }
    }

    /**
     * Approve reservation status with table availability validation
     * 
     * @param int $reservation_id Reservation identifier
     * @param string $status New reservation status
     * @param string|null $reason Reason for status change
     * @return array Operation result with success status and message
     */
    public function ApproveReservationStatus_with_validation($reservation_id, $status, $reason = null) {
        $stmt = $this->conn->prepare("SELECT table_code, date_schedule, time_schedule, status, reserve_user_id, reserve_unique_code FROM `reservations` WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Reservation not found in database records.'
            ];
        }

        $reservation = $result->fetch_assoc();

        if ($status === 'confirmed') {
            $table_code = $reservation['table_code'];
            $date_schedule = $reservation['date_schedule'];
            $time_schedule = $reservation['time_schedule'];

            $checkStmt = $this->conn->prepare("
                SELECT COUNT(*) as cnt 
                FROM `reservations` 
                WHERE table_code = ? AND date_schedule = ? AND time_schedule = ? AND status = 'confirmed' AND id != ?
            ");
            $checkStmt->bind_param("sssi", $table_code, $date_schedule, $time_schedule, $reservation_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result()->fetch_assoc();

            if ($checkResult['cnt'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot approve reservation. Another reservation is already confirmed for the same table and schedule.'
                ];
            }
        }

        $updateStmt = $this->conn->prepare("UPDATE `reservations` SET `status` = ?, `approval_reason` = ? WHERE `id` = ?");
        $updateStmt->bind_param("ssi", $status, $reason, $reservation_id);

        if ($updateStmt->execute()) {
            $user = $this->getUserById($reservation['reserve_user_id']);
            if ($user) {
                $mailer = new Mailer();
                $mailer->sendReservationStatusNotification(
                    $user['user_email'],
                    $user['user_fname'] . ' ' . $user['user_lname'],
                    $reservation['reserve_unique_code'],
                    $reservation['date_schedule'] . ' ' . $reservation['time_schedule'],
                    $status,
                    $reservation['table_code'],
                    $reason
                );
            }

            return [
                'success' => true,
                'message' => 'Reservation status updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => $updateStmt->error
            ];
        }
    }

    /**
     * Retrieve all active menu items
     * 
     * @return array List of active menu items
     */
    public function fetch_all_menu() {
        $query = $this->conn->prepare("SELECT * FROM menu WHERE menu_status = '1' ORDER BY menu_id DESC");

        if ($query->execute()) {
            $result = $query->get_result();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'menu_id' => $row['menu_id'] ?? null,
                    'menu_name' => $row['menu_name'] ?? '',
                    'menu_category' => $row['menu_category'] ?? '',
                    'menu_description' => $row['menu_description'] ?? '',
                    'menu_price' => $row['menu_price'] ?? 0,
                    'menu_image_banner' => $row['menu_image_banner'] ?? '',
                    'menu_status' => $row['menu_status'] ?? '1'
                ];
            }

            return $data;
        }
        return []; 
    }

    /**
     * Retrieve menu item by ID
     * 
     * @param int $menu_id Menu identifier
     * @return array|null Menu details or null if not found
     */
    public function getMenuById($menu_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id = ?");
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            return null;
        } catch (Exception $e) {
            error_log("Error getting menu by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all active users
     * 
     * @return array List of active users
     */
    public function fetch_all_users() {
        $query = $this->conn->prepare("SELECT * FROM user WHERE user_status='1' ORDER BY user_id DESC");

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

    /**
     * Add new menu item to the system
     * 
     * @param string $menuName Menu item name
     * @param string $menuCategory Menu category
     * @param string $menuDescription Menu description
     * @param float $menuPrice Menu price
     * @param string $menuImageFileName Menu image filename
     * @return int|bool Inserted menu ID or false on failure
     */
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

    /**
     * Create new deals or promotions
     * 
     * @param string $groupName Deal name
     * @param string $groupDescription Deal description
     * @param string $deal_type Type of deal (promo_deals or group_deals)
     * @param string $groupImageFileName Deal image filename
     * @param string $entryExpiration Deal expiration date
     * @return int|bool Inserted deal ID or false on failure
     */
    public function createDeals($groupName,$groupDescription,$deal_type,$groupImageFileName,$entryExpiration) {
        $query = "INSERT INTO `deals` (`deal_name`,`deal_description`,`deal_img_banner`,`deal_type`,`deal_expiration`) 
                VALUES (?,?,?,?,?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("sssss", $groupName,$groupDescription,$groupImageFileName,$deal_type,$entryExpiration);
        $result = $stmt->execute();

        if (!$result) {
            $stmt->close();
            return false;
        }

        $inserted_id = $this->conn->insert_id; 
        $stmt->close();
        return $inserted_id; 
    }

    /**
     * Authenticate user login credentials
     * 
     * @param string $email User email address
     * @param string $password User password
     * @return array Login result with success status and user data
     */
    public function Login($email, $password)
    {
        $query = $this->conn->prepare("SELECT * FROM `user` WHERE `user_email` = ? AND `is_verified` = 1");
        $query->bind_param("s", $email);

        if ($query->execute()) {
            $result = $query->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['user_password'])) {
                    if ($user['user_status'] == 0) {
                        $query->close();
                        return [
                            'success' => false,
                            'message' => 'Your account is currently inactive. Please contact administrator.'
                        ];
                    }

                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_fname'] = $user['user_fname'];
                    $_SESSION['user_position'] = $user['user_position']; 

                    $query->close();
                    return [
                        'success' => true,
                        'message' => 'Login successful. Welcome to Ultimate Liempo Haus.',
                        'data' => [
                            'user_id' => $user['user_id'],
                            'user_fname' => $user['user_fname'],
                            'user_position' => $user['user_position'], 
                        ]
                    ];
                } else {
                    $query->close();
                    return ['success' => false, 'message' => 'Incorrect password provided.'];
                }
            } else {
                $query->close();
                return ['success' => false, 'message' => 'Account not found or email not verified. Please check your email for verification link.'];
            }
        } else {
            $query->close();
            return ['success' => false, 'message' => 'Database error encountered during login process.'];
        }
    }

    /**
     * Check if email already exists in the system
     * 
     * @param string $email Email address to check
     * @return bool True if email exists, false otherwise
     */
    public function isEmailExist($email) {
        $query = "SELECT user_id FROM `user` WHERE `user_email` = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0; 
    }

    /**
     * Register new customer account
     * 
     * @param string $first_name Customer first name
     * @param string $last_name Customer last name
     * @param string $email Customer email address
     * @param string $password Customer password
     * @return array Registration result with success status and message
     */
    public function RegisterCustomer($first_name, $last_name, $email, $password) {
        if ($this->isEmailExist($email)) {
            return [
                'success' => false,
                'message' => 'This email address is already registered in our system.'
            ];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO `user`(`user_fname`, `user_lname`, `user_email`, `user_password`, `user_position`) 
                  VALUES (?, ?, ?, ?, 'customer')";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashedPassword);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Registration completed successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registration process failed. Please attempt the registration again.'
            ];
        }
    }

    /**
     * Check email existence in database
     * 
     * @param string $email Email address to verify
     * @return bool True if email exists, false otherwise
     */
    public function checkEmailExists($email) {
        try {
            $stmt = $this->conn->prepare("SELECT user_id FROM user WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0;
        } catch (Exception $e) {
            error_log("Database error in checkEmailExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send verification email to new user
     * 
     * @param string $email User email address
     * @param string $token Verification token
     * @param int $user_id User identifier
     * @return bool True if email sent successfully, false otherwise
     */
    private function sendVerificationEmail($email, $token, $user_id) {
        $verification_link = "http://localhost/Grillbook%20System/verify.php?token=" . $token . "&id=" . $user_id;
        
        $subject = "Verify Your Email Address - Ultimate Liempo Haus";
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #D4AF37, #B8860B); padding: 20px; text-align: center; border-radius: 10px 10px 0 0; color: white; }
                .content { padding: 20px; }
                .verification-btn { background: #D4AF37; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Ultimate Liempo Haus</h2>
                    <p>Email Verification Required</p>
                </div>
                <div class='content'>
                    <h3>Dear Customer,</h3>
                    <p>Thank you for registering with Ultimate Liempo Haus. To complete your registration, please verify your email address by clicking the button below:</p>
                    <a href='{$verification_link}' class='verification-btn'>Verify Email Address</a>
                    <p>Alternatively, you may copy and paste this link into your browser:<br>{$verification_link}</p>
                    <p><strong>Important:</strong> This verification link will expire in 24 hours.</p>
                    <p>If you did not request this registration, please disregard this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 Ultimate Liempo Haus. All rights reserved.</p>
                    <p>This is an automated email. Please do not reply to this message.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Ultimate Liempo Haus <noreply@ultimateliempo.com>" . "\r\n";
        $headers .= "Reply-To: noreply@ultimateliempo.com" . "\r\n";
        
        file_put_contents('verification_emails.html', 
            "To: $email<br>Link: $verification_link<br>Time: " . date('Y-m-d H:i:s') . "<br><hr>", 
            FILE_APPEND
        );
        
        return true;
    }

    /**
     * Verify user email with token
     * 
     * @param int $user_id User identifier
     * @param string $token Verification token
     * @return array Verification result with success status and message
     */
    public function verifyEmail($user_id, $token) {
        $stmt = $this->conn->prepare("SELECT user_id, token_expires FROM user WHERE user_id = ? AND verification_token = ? AND is_verified = 0");
        $stmt->bind_param("is", $user_id, $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (strtotime($user['token_expires']) < time()) {
                return [
                    'success' => false,
                    'message' => 'Verification link has expired. Please initiate the registration process again.'
                ];
            } else {
                $update_stmt = $this->conn->prepare("UPDATE user SET is_verified = 1, verification_token = NULL, token_expires = NULL WHERE user_id = ?");
                $update_stmt->bind_param("i", $user_id);
                
                if ($update_stmt->execute()) {
                    return [
                        'success' => true,
                        'message' => 'Email verification completed successfully! You may now log into your account.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Verification process failed. Please attempt the verification again.'
                    ];
                }
            }
        } else {
            return [
                'success' => false,
                'message' => 'Invalid verification link provided or account already verified.'
            ];
        }
    }

    /**
     * Update menu item information
     * 
     * @param int $menu_id Menu identifier
     * @param string $menu_name Updated menu name
     * @param string $menu_category Updated menu category
     * @param string $menu_description Updated menu description
     * @param float $menu_price Updated menu price
     * @param string|null $menu_image_banner Updated menu image filename
     * @return array Update result with success status and message
     */
    public function UpdateMenu(
        $menu_id,
        $menu_name,
        $menu_category,
        $menu_description,
        $menu_price,
        $menu_image_banner = null) {
        
        $menu_description = trim($menu_description) === '' ? null : $menu_description;

        if ($menu_image_banner) {
            $stmt = $this->conn->prepare("SELECT menu_image_banner FROM menu WHERE menu_id = ?");
            $stmt->bind_param("s", $menu_id);
            $stmt->execute();
            $stmt->bind_result($oldBanner);
            $stmt->fetch();
            $stmt->close();

            if (!empty($oldBanner)) {
                $oldPath = "../../static/uploads/menu/" . $oldBanner;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        }

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

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['status' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();

        return ['status' => true, 'message' => 'Menu information updated successfully.'];
    }

    /**
     * Remove menu item by deactivating it
     * 
     * @param int $menu_id Menu identifier
     * @return string Operation result message
     */
    public function removeMenu($menu_id) {
        $deleteQuery = "UPDATE menu SET menu_status = 0 WHERE menu_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (update): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $menu_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 'success' : 'Error updating menu';
    }

    /**
     * Remove user by deactivating account
     * 
     * @param int $user_id User identifier
     * @return string Operation result message
     */
    public function removeUser($user_id) {
        $deleteQuery = "UPDATE user SET user_status = 0 WHERE user_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (update): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 'success' : 'Error updating menu';
    }

    /**
     * Retrieve all deals with optional filtering
     * 
     * @param string|null $deal_type Type of deal to filter
     * @return array List of deals
     */
    public function fetch_all_deals($deal_type) {
        $queryStr = "SELECT * FROM deals";
        if (!is_null($deal_type)) {
            $queryStr .= " WHERE deal_type = ?";
        }
        $queryStr .= " ORDER BY deal_id DESC";

        $query = $this->conn->prepare($queryStr);
        if (!is_null($deal_type)) {
            $query->bind_param("s", $deal_type);
        }

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

    /**
     * Retrieve all deals with associated menu details
     * 
     * @param string|null $deal_type Type of deal to filter
     * @return array List of deals with menu information
     */
    public function fetch_all_deals_and_menu($deal_type) {
        $queryStr = "SELECT * FROM deals";
        $params = [];
        $types = "";

        if (!is_null($deal_type)) {
            if ($deal_type === 'promo_deals') {
                $queryStr .= " WHERE deal_type = ? AND deal_expiration >= NOW()";
                $types = "s";
                $params[] = $deal_type;
            } else {
                $queryStr .= " WHERE deal_type = ?";
                $types = "s";
                $params[] = $deal_type;
            }
        }
        $queryStr .= " ORDER BY deal_id DESC";

        $query = $this->conn->prepare($queryStr);
        if ($types !== "") {
            $query->bind_param($types, ...$params);
        }

        if ($query->execute()) {
            $result = $query->get_result();
            $data = [];

            while ($row = $result->fetch_assoc()) {
                $dealIds = json_decode($row['deal_ids'], true);
                if (empty($dealIds) || !is_array($dealIds)) {
                    continue;
                }

                $totalPrice = 0;
                $menus = [];
                $placeholders = implode(',', array_fill(0, count($dealIds), '?'));
                $typesMenu = str_repeat('i', count($dealIds));

                $sqlMenu = "SELECT * FROM menu WHERE menu_id IN ($placeholders)";
                $stmtMenu = $this->conn->prepare($sqlMenu);
                $bindParams = [];
                $bindParams[] = &$typesMenu;
                foreach ($dealIds as $key => $id) {
                    $bindParams[] = &$dealIds[$key];
                }
                call_user_func_array([$stmtMenu, 'bind_param'], $bindParams);

                $stmtMenu->execute();
                $resMenu = $stmtMenu->get_result();

                while ($menu = $resMenu->fetch_assoc()) {
                    $menus[] = $menu;
                    $totalPrice += $menu['menu_price'];
                }

                $row['menus'] = $menus;
                $row['total_price'] = $totalPrice;
                $data[] = $row;
            }
            return $data;
        }
        return [];
    }

    /**
     * Add menu to existing deal
     * 
     * @param int $menu_id Menu identifier
     * @param int $deal_id Deal identifier
     * @return bool True if operation successful, false otherwise
     */
    public function AddMenuDeals($menu_id, $deal_id) {
        $query = "SELECT deal_ids FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $deal_id);
        $stmt->execute();
        $stmt->bind_result($deal_ids_json);
        $stmt->fetch();
        $stmt->close();

        $deal_ids = json_decode($deal_ids_json, true);
        if (!is_array($deal_ids)) {
            $deal_ids = [];
        }

        if (!in_array($menu_id, $deal_ids)) {
            $deal_ids[] = $menu_id;
        }

        $new_deal_ids_json = json_encode($deal_ids);
        $updateQuery = "UPDATE deals SET deal_ids = ? WHERE deal_id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $new_deal_ids_json, $deal_id);
        $result = $updateStmt->execute();
        $updateStmt->close();
        return $result;
    }

    /**
     * Remove menu from deal
     * 
     * @param int $menu_id Menu identifier
     * @param int $deal_id Deal identifier
     * @return bool True if operation successful, false otherwise
     */
    public function remove_deal_ids($menu_id, $deal_id) {
        $query = "SELECT deal_ids FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $deal_id);
        $stmt->execute();
        $stmt->bind_result($deal_ids_json);
        $stmt->fetch();
        $stmt->close();

        $deal_ids = json_decode($deal_ids_json, true);
        if (!is_array($deal_ids)) {
            $deal_ids = [];
        }

        if (in_array($menu_id, $deal_ids)) {
            $deal_ids = array_filter($deal_ids, function($id) use ($menu_id) {
                return $id != $menu_id;
            });
            $deal_ids = array_values($deal_ids);
        }

        $new_deal_ids_json = json_encode($deal_ids);
        $updateQuery = "UPDATE deals SET deal_ids = ? WHERE deal_id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $new_deal_ids_json, $deal_id);
        $result = $updateStmt->execute();
        $updateStmt->close();
        return $result;
    }

    /**
     * Retrieve specific deal with all associated menu details
     * 
     * @param int $dealId Deal identifier
     * @return array|null Deal information with menus or null if not found
     */
    public function GetAllDealsWithMenus_byId($dealId) {
        $query = "SELECT deal_id, deal_name, deal_ids FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $dealId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $deal_ids = json_decode($row['deal_ids'], true);
            if (!is_array($deal_ids)) {
                $deal_ids = [];
            }

            $deal = [
                'deal_id' => $row['deal_id'],
                'deal_name' => $row['deal_name'],
                'deal_ids' => $deal_ids,
                'menus' => []
            ];

            if (count($deal_ids) > 0) {
                $placeholders = implode(',', array_fill(0, count($deal_ids), '?'));
                $menuQuery = "SELECT * FROM menu WHERE menu_id IN ($placeholders)";
                $menuStmt = $this->conn->prepare($menuQuery);
                $types = str_repeat('i', count($deal_ids)); 
                $menuStmt->bind_param($types, ...$deal_ids);
                $menuStmt->execute();
                $menuResult = $menuStmt->get_result();

                $menus = [];
                while ($row = $menuResult->fetch_assoc()) {
                    $menus[$row['menu_id']] = $row;
                }

                foreach ($deal_ids as $id) {
                    if (isset($menus[$id])) {
                        $deal['menus'][] = $menus[$id];
                    }
                }
                $menuStmt->close();
            }

            $stmt->close();
            return $deal; 
        }

        $stmt->close();
        return null; 
    }

    /**
     * Remove deal from the system
     * 
     * @param int $deal_id Deal identifier
     * @return string Operation result message
     */
    public function removeDeals($deal_id) {
        $selectQuery = "SELECT deal_img_banner FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($selectQuery);
        if (!$stmt) {
            return 'Prepare failed (select): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $deal_id);
        $stmt->execute();
        $stmt->bind_result($bannerFile);
        $stmt->fetch();
        $stmt->close();

        $deleteQuery = "DELETE FROM deals WHERE deal_id = ?";
        $stmt = $this->conn->prepare($deleteQuery);
        if (!$stmt) {
            return 'Prepare failed (delete): ' . $this->conn->error;
        }

        $stmt->bind_param("i", $deal_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result && $bannerFile) {
            $filePath = __DIR__ . "/../../static/uploads/deals/" . $bannerFile;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $result ? 'success' : 'Error deleting event';
    }

    /**
     * Process reservation request from customer
     * 
     * @param string $table_code Table number
     * @param int $seats Number of seats
     * @param string $date_schedule Reservation date
     * @param string $time_schedule Reservation time
     * @param mixed $menu_selected Selected menu items
     * @param mixed $promo_selected Selected promotions
     * @param mixed $group_selected Selected group deals
     * @param string $selected_menus JSON encoded selected menus
     * @param string $selected_promos JSON encoded selected promos
     * @param string $selected_groups JSON encoded selected groups
     * @param float $menu_total Menu items total price
     * @param float $promo_total Promotions total price
     * @param float $group_total Group deals total price
     * @param float $grand_total Grand total price
     * @param string $entryImageFileName Payment proof image filename
     * @param int $user_id Customer identifier
     * @param string $termsFileSignedFileName Signed terms filename
     * @param int $wine_bottles Number of wine bottles
     * @param int $beer_bottles Number of beer bottles
     * @return bool True if reservation created successfully, false otherwise
     */
    public function RequestReservation(
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
        $termsFileSignedFileName,
        $wine_bottles = 0,
        $beer_bottles = 0
    ) {
        $uniqueCode = $this->generateUniqueCode();
        $sql = "INSERT INTO reservations (
            table_code,
            seats,
            date_schedule,
            time_schedule,
            selected_menus,
            selected_promos,
            selected_groups,
            menu_total,
            promo_total,
            group_total,
            grand_total,
            proof_of_payment,
            reserve_user_id,
            reserve_unique_code,
            termsFileSigned,
            wine_bottles,
            beer_bottles
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param(
            "sissssssdddssssii",
            $table_code,
            $seats,
            $date_schedule,
            $time_schedule,
            $selected_menus,
            $selected_promos,
            $selected_groups,
            $menu_total,
            $promo_total,
            $group_total,
            $grand_total,
            $entryImageFileName,
            $user_id,
            $uniqueCode,
            $termsFileSignedFileName,
            $wine_bottles,
            $beer_bottles
        );

        $result = $stmt->execute();
        if (!$result) {
            die("Execute failed: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }

    /**
     * Generate unique reservation code
     * 
     * @param int $length Length of code (default 8)
     * @return string Unique reservation code
     */
    private function generateUniqueCode($length = 8) {
        do {
            $code = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length));
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reservations WHERE reserve_unique_code = ?");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        } while ($count > 0); 
        return $code;
    }

    /**
     * Check table availability for requested schedule
     * 
     * @param string $table_code Table number
     * @param string $date_schedule Requested date
     * @param string $time_schedule Requested time
     * @return array Availability check result with detailed information
     */
    public function checkAvailability($table_code, $date_schedule, $time_schedule) {
        $dayOfWeek = date('l', strtotime($date_schedule));
        $scheduleQuery = $this->conn->prepare("
            SELECT open_time, close_time 
            FROM business_hours 
            WHERE day_of_week = ?
        ");
        $scheduleQuery->bind_param("s", $dayOfWeek);
        $scheduleQuery->execute();
        $scheduleResult = $scheduleQuery->get_result();
        $hours = $scheduleResult->fetch_assoc();

        if (!$hours) {
            return [
                "status" => 200,
                "available" => false,
                "dayOfWeek" => $dayOfWeek,
                "reason" => "no_schedule"
            ];
        }

        $openTime  = $hours['open_time'];
        $closeTime = $hours['close_time'];
        $time       = DateTime::createFromFormat('H:i', $time_schedule);
        $open       = DateTime::createFromFormat('H:i:s', $openTime);
        $close      = DateTime::createFromFormat('H:i:s', $closeTime);

        if ($close <= $open) {
            $close->modify('+1 day');
            if ($time < $open) {
                $time->modify('+1 day');
            }
        }

        $isAvailableTime = ($time >= $open && $time <= $close);

        if (!$isAvailableTime) {
            return [
                "status" => 200,
                "available" => false,
                "dayOfWeek" => $dayOfWeek,
                "open_time" => $openTime,
                "close_time" => $closeTime,
                "reason" => "outside_hours"
            ];
        }

        $query = $this->conn->prepare("
            SELECT * FROM reservations 
            WHERE table_code = ? 
            AND date_schedule = ? 
            AND status = 'confirmed'
        ");
        $query->bind_param("ss", $table_code, $date_schedule);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $conflicts = [];
            while ($row = $result->fetch_assoc()) {
                $conflicts[] = $row;
            }

            return [
                "status" => 200,
                "available" => false,
                "dayOfWeek" => $dayOfWeek,
                "open_time" => $openTime,
                "close_time" => $closeTime,
                "reason" => "conflict",
                "conflicts" => $conflicts
            ];
        }

        return [
            "status" => 200,
            "available" => true,
            "dayOfWeek" => $dayOfWeek,
            "open_time" => $openTime,
            "close_time" => $closeTime
        ];
    }

    /**
     * Attach detailed menu, promo, and group information to reservations
     * 
     * @param array $reservations List of reservations
     * @return array Reservations with attached details
     */
    private function attachMenuPromoGroupDetails($reservations) {
        if (!$reservations) return [];

        $menu_ids = [];
        $promo_ids = [];
        $group_ids = [];

        foreach ($reservations as $res) {
            $menus = json_decode($res['selected_menus'], true) ?: [];
            $promos = json_decode($res['selected_promos'], true) ?: [];
            $groups = json_decode($res['selected_groups'], true) ?: [];

            foreach ($menus as $m) {
                $id = (int)$m['id'];
                if (!in_array($id, $menu_ids, true)) $menu_ids[] = $id;
            }
            foreach ($promos as $p) {
                $id = (int)$p['id'];
                if (!in_array($id, $promo_ids, true)) $promo_ids[] = $id;
            }
            foreach ($groups as $g) {
                $id = (int)$g['id'];
                if (!in_array($id, $group_ids, true)) $group_ids[] = $id;
            }
        }

        $menus = [];
        if ($menu_ids) {
            $placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
            $typesMenu = str_repeat('i', count($menu_ids));
            $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($placeholders)");
            $stmt->bind_param($typesMenu, ...$menu_ids);
            $stmt->execute();
            $resMenus = $stmt->get_result();
            while ($row = $resMenus->fetch_assoc()) {
                $menus[$row['menu_id']] = $row;
            }
        }

        $promos = [];
        if ($promo_ids) {
            $placeholders = implode(',', array_fill(0, count($promo_ids), '?'));
            $types = str_repeat('i', count($promo_ids));
            $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
            $stmt->bind_param($types, ...$promo_ids);
            $stmt->execute();
            $resPromos = $stmt->get_result();
            while ($row = $resPromos->fetch_assoc()) {
                $promos[$row['deal_id']] = $row;
            }
        }

        $groups = [];
        if ($group_ids) {
            $placeholders = implode(',', array_fill(0, count($group_ids), '?'));
            $types = str_repeat('i', count($group_ids));
            $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($placeholders)");
            $stmt->bind_param($types, ...$group_ids);
            $stmt->execute();
            $resGroups = $stmt->get_result();
            while ($row = $resGroups->fetch_assoc()) {
                $groups[$row['deal_id']] = $row;
            }
        }

        foreach ($reservations as &$res) {
            $resMenus = json_decode($res['selected_menus'], true) ?: [];
            foreach ($resMenus as &$menuItem) {
                $menuId = (int)$menuItem['id'];
                if (isset($menus[$menuId])) {
                    $menuItem['details'] = $menus[$menuId];
                }
            }
            $res['menus_details'] = $resMenus;

            $resPromos = json_decode($res['selected_promos'], true) ?: [];
            foreach ($resPromos as &$promoItem) {
                $promoId = (int)$promoItem['id'];
                if (isset($promos[$promoId])) {
                    $promoItem['details'] = $promos[$promoId];
                }
            }
            $res['promos_details'] = $resPromos;

            $resGroups = json_decode($res['selected_groups'], true) ?: [];
            foreach ($resGroups as &$groupItem) {
                $groupId = (int)$groupItem['id'];
                if (isset($groups[$groupId])) {
                    $groupItem['details'] = $groups[$groupId];
                }
            }
            $res['groups_details'] = $resGroups;
        }

        return $reservations;
    }

    /**
     * Retrieve all pending reservation requests with pagination
     * 
     * @param int $limit Number of records per page
     * @param int $offset Pagination offset
     * @return array List of pending reservation requests
     */
    public function fetch_all_reserve_request($limit, $offset) {
        $query = $this->conn->prepare("
            SELECT * FROM reservations
            LEFT JOIN user
            ON reservations.reserve_user_id = user.user_id 
            WHERE status = 'pending' OR status = 'request cancel'
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $query->bind_param("ii", $limit, $offset);
        $query->execute();
        $result = $query->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);

        if (!$reservations) return [];
        return $this->attachMenuPromoGroupDetails($reservations);
    }

    /**
     * Count total pending reservation requests
     * 
     * @return int Total count of pending requests
     */
    public function count_all_reserve_request() {
        $result = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM reservations where status='pending'
        ");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } 

    /**
     * Retrieve completed reservations with optional filtering
     * 
     * @param string $filter Time filter (daily, weekly, monthly, yearly, all)
     * @return array List of completed reservations
     */
    public function getCompletedReservations($filter = 'all') {
        $dateCondition = "";
        switch ($filter) {
            case 'daily':
                $dateCondition = "AND DATE(r.date_schedule) = CURDATE()";
                break;
            case 'weekly':
                $dateCondition = "AND YEARWEEK(r.date_schedule, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'monthly':
                $dateCondition = "AND MONTH(r.date_schedule) = MONTH(CURDATE()) AND YEAR(r.date_schedule) = YEAR(CURDATE())";
                break;
            case 'yearly':
                $dateCondition = "AND YEAR(r.date_schedule) = YEAR(CURDATE())";
                break;
            default:
                $dateCondition = "";
        }

        $sql = "SELECT r.*, u.user_fname, u.user_lname, u.user_email
                FROM reservations r
                JOIN user u ON r.reserve_user_id = u.user_id
                WHERE r.status = 'completed' $dateCondition
                ORDER BY r.date_schedule DESC, r.time_schedule DESC";

        $result = $this->conn->query($sql);
        $data = [];
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $decodeItems = function($json) {
                    $items = json_decode($json, true);
                    if(!$items || !is_array($items)) return [];
                    return array_map(function($item) {
                        return [
                            'name' => $item['name'],
                            'price'=> $item['price'],
                            'qty'  => $item['qty']
                        ];
                    }, $items);
                };

                $row['selected_menus']  = $decodeItems($row['selected_menus']);
                $row['selected_promos'] = $decodeItems($row['selected_promos']);
                $row['selected_groups'] = $decodeItems($row['selected_groups']);
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Retrieve all reservations with pagination
     * 
     * @param int $limit Number of records per page
     * @param int $offset Pagination offset
     * @return array List of reservations
     */
    public function fetch_all_reserved($limit, $offset) {
        $query = $this->conn->prepare("
            SELECT * FROM reservations
            LEFT JOIN user
            ON reservations.reserve_user_id = user.user_id 
            WHERE archived_by_admin='0' AND (status = 'confirmed' || status = 'completed'|| status = 'request new schedule'|| status = 'cancelled')
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $query->bind_param("ii", $limit, $offset);
        $query->execute();
        $result = $query->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);

        if (!$reservations) return [];
        return $this->attachMenuPromoGroupDetails($reservations);
    }

    /**
     * Count total active reservations
     * 
     * @return int Total count of active reservations
     */
    public function count_all_reserved() {
        $result = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM reservations where archived_by_admin='0' AND (status = 'confirmed' || status = 'completed'|| status = 'request new schedule'|| status = 'cancelled')
        ");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } 

    /**
     * Retrieve archived reservations with pagination
     * 
     * @param int $limit Number of records per page
     * @param int $offset Pagination offset
     * @param string $column Archive status column
     * @return array List of archived reservations
     */
    public function fetch_all_reserved_archived($limit, $offset,$collumn) {
        $query = $this->conn->prepare("
            SELECT * FROM reservations
            WHERE $collumn = '1'
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $query->bind_param("ii", $limit, $offset);
        $query->execute();
        $result = $query->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);

        if (!$reservations) return [];
        return $this->attachMenuPromoGroupDetails($reservations);
    }

    /**
     * Count total archived reservations
     * 
     * @return int Total count of archived reservations
     */
    public function count_all_reserved_archived() {
        $result = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM reservations where status = 'archived'
        ");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } 

    /**
     * Retrieve specific reservation details
     * 
     * @param int $reservations_id Reservation identifier
     * @return mixed Reservation details result set
     */
    public function fetch_reservation($reservations_id) {
        $query = $this->conn->prepare(" SELECT *
        FROM reservations
        LEFT JOIN user
        ON user.user_id  = reservations.reserve_user_id
        where id = $reservations_id");
        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }

    /**
     * Retrieve customer reservations with pagination
     * 
     * @param int $limit Number of records per page
     * @param int $offset Pagination offset
     * @param int $user_id Customer identifier
     * @return array List of customer reservations
     */
    public function fetch_all_customer_reservation($limit, $offset,$user_id) {
        $query = $this->conn->prepare("
            SELECT * FROM reservations
            WHERE reserve_user_id = $user_id
            AND archived_by_customer=0
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $query->bind_param("ii", $limit, $offset);
        $query->execute();
        $result = $query->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);

        if (!$reservations) return [];
        return $this->attachMenuPromoGroupDetails($reservations);
    }

    /**
     * Retrieve reservations scheduled for today
     * 
     * @return array List of today's confirmed table codes
     */
    public function fetch_all_reservations_today() {
        $query = "SELECT table_code FROM reservations 
                  WHERE DATE(date_schedule) = CURDATE() AND STATUS ='confirmed' 
                  AND status NOT IN ('cancelled')";
        $result = $this->conn->query($query);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row['table_code'];
            }
        }
        return $data;
    }

    /**
     * Count total customer reservations
     * 
     * @param int $user_id Customer identifier
     * @return int Total count of customer reservations
     */
    public function count_all_customer_reservation($user_id) {
        $result = $this->conn->query("
            SELECT COUNT(*) as total 
            FROM reservations WHERE reserve_user_id = $user_id
        ");
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } 

    /**
     * Process cancellation request for reservation
     * 
     * @param int $reservationId Reservation identifier
     * @param string $reason Cancellation reason
     * @return array Operation result with success status and message
     */
    public function cancel_reservation($reservationId, $reason) {
        try {
            $stmt = $this->conn->prepare("UPDATE reservations SET status = 'request cancel', cancellation_reason = ? WHERE id = ?");
            
            if ($stmt) {
                $stmt->bind_param("si", $reason, $reservationId); 
                
                if ($stmt->execute()) {
                    $reservation = $this->getReservationById($reservationId);
                    if ($reservation) {
                        $user = $this->getUserById($reservation['reserve_user_id']);
                        if ($user) {
                            $mailer = new Mailer();
                            $mailer->sendReservationStatusNotification(
                                $user['user_email'],
                                $user['user_fname'] . ' ' . $user['user_lname'],
                                $reservation['reserve_unique_code'],
                                $reservation['date_schedule'] . ' ' . $reservation['time_schedule'],
                                'request_cancel',
                                $reservation['table_code'],
                                $reason
                            );
                        }
                    }
                    
                    $stmt->close();
                    return [
                        'success' => true,
                        'message' => 'Cancellation request submitted successfully. Awaiting administrator approval.'
                    ];
                } else {
                    $stmt->close();
                    return [
                        'success' => false,
                        'message' => 'Failed to submit cancellation request. Please attempt the process again.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare the database statement.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error encountered during cancellation request: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process reschedule request for reservation
     * 
     * @param int $reservationId Reservation identifier
     * @param string $reason Reschedule reason
     * @param int $seats Number of seats
     * @param string $newDate New requested date
     * @param string $newTime New requested time
     * @return array Operation result with success status and message
     */
    public function reschedule($reservationId, $reason, $seats, $newDate, $newTime) {
        try {
            $requestDetails = json_encode([
                'reason' => $reason,
                'newDate' => $newDate,
                'newTime' => $newTime,
                'seats' => $seats
            ]);

            $stmt = $this->conn->prepare(
                "UPDATE reservations 
                SET status = 'request new schedule', request_details = ? 
                WHERE id = ?"
            );

            if ($stmt) {
                $stmt->bind_param("si", $requestDetails, $reservationId);

                if ($stmt->execute()) {
                    $reservation = $this->getReservationById($reservationId);
                    if ($reservation) {
                        $user = $this->getUserById($reservation['reserve_user_id']);
                        if ($user) {
                            $mailer = new Mailer();
                            $mailer->sendReservationStatusNotification(
                                $user['user_email'],
                                $user['user_fname'] . ' ' . $user['user_lname'],
                                $reservation['reserve_unique_code'],
                                $reservation['date_schedule'] . ' ' . $reservation['time_schedule'],
                                'request_reschedule',
                                $reservation['table_code'],
                                $reason,
                                $newDate,
                                $newTime
                            );
                        }
                    }
                    
                    $stmt->close();
                    return [
                        'success' => true,
                        'message' => 'Reschedule request submitted successfully. Awaiting administrator approval.'
                    ];
                } else {
                    $stmt->close();
                    return [
                        'success' => false,
                        'message' => 'Failed to submit reschedule request. Please attempt the process again.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare the database statement.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error encountered during reschedule request: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve all admin reservations for today without pagination
     * 
     * @return array List of today's reservations and walk-ins
     */
    public function fetch_all_admin_reservation_no_limit() {
        $query = $this->conn->prepare("
            SELECT * FROM reservations
            WHERE archived_by_admin = 0
            AND DATE(date_schedule) = CURDATE()
            ORDER BY id DESC
        ");
        $query->execute();
        $result = $query->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC) ?: [];

        $menu_ids = $promo_ids = $group_ids = [];
        foreach ($reservations as &$res) {
            $menus = json_decode($res['selected_menus'], true) ?: [];
            $promos = json_decode($res['selected_promos'], true) ?: [];
            $groups = json_decode($res['selected_groups'], true) ?: [];

            foreach ($menus as $m) if (!in_array((int)$m['id'], $menu_ids, true)) $menu_ids[] = (int)$m['id'];
            foreach ($promos as $p) if (!in_array((int)$p['id'], $promo_ids, true)) $promo_ids[] = (int)$p['id'];
            foreach ($groups as $g) if (!in_array((int)$g['id'], $group_ids, true)) $group_ids[] = (int)$g['id'];

            $res['is_walkin'] = false;
            $res['source'] = 'reservation';
        }

        $menus = $promos = $groups = [];

        if ($menu_ids) {
            $in = implode(',', array_fill(0, count($menu_ids), '?'));
            $types = str_repeat('i', count($menu_ids));
            $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($in)");
            $stmt->bind_param($types, ...$menu_ids);
            $stmt->execute();
            $resMenus = $stmt->get_result();
            while ($row = $resMenus->fetch_assoc()) $menus[$row['menu_id']] = $row;
        }

        if ($promo_ids) {
            $in = implode(',', array_fill(0, count($promo_ids), '?'));
            $types = str_repeat('i', count($promo_ids));
            $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
            $stmt->bind_param($types, ...$promo_ids);
            $stmt->execute();
            $resPromos = $stmt->get_result();
            while ($row = $resPromos->fetch_assoc()) $promos[$row['deal_id']] = $row;
        }

        if ($group_ids) {
            $in = implode(',', array_fill(0, count($group_ids), '?'));
            $types = str_repeat('i', count($group_ids));
            $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
            $stmt->bind_param($types, ...$group_ids);
            $stmt->execute();
            $resGroups = $stmt->get_result();
            while ($row = $resGroups->fetch_assoc()) $groups[$row['deal_id']] = $row;
        }

        foreach ($reservations as &$res) {
            $resMenus = json_decode($res['selected_menus'], true) ?: [];
            foreach ($resMenus as &$menuItem) $menuItem['details'] = $menus[(int)$menuItem['id']] ?? [];
            $res['menus_details'] = $resMenus;

            $resPromos = json_decode($res['selected_promos'], true) ?: [];
            foreach ($resPromos as &$promoItem) $promoItem['details'] = $promos[(int)$promoItem['id']] ?? [];
            $res['promos_details'] = $resPromos;

            $resGroups = json_decode($res['selected_groups'], true) ?: [];
            foreach ($resGroups as &$groupItem) $groupItem['details'] = $groups[(int)$groupItem['id']] ?? [];
            $res['groups_details'] = $resGroups;
        }

        $walkins = [];
        $result = $this->conn->query("
            SELECT * FROM walkin_tables 
            WHERE walkin_status = 'unavailable' 
            AND DATE(walkin_created_at) = CURDATE()
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $walkins[] = [
                    'id' => $row['walkin_id'],
                    'table_code' => $row['walkin_table_code'],
                    'status' => $row['walkin_status'],
                    'is_walkin' => true,
                    'source' => 'walkin'
                ];
            }
        }

        return array_merge($reservations, $walkins);
    }

    /**
     * Retrieve customer reservations for today without pagination
     * 
     * @param int $user_id Customer identifier
     * @return array List of today's customer reservations and walk-ins
     */
    public function fetch_all_customer_reservation_no_limit($user_id) {
        $query = $this->conn->prepare("
            SELECT * FROM reservations
            WHERE reserve_user_id = ?
            AND archived_by_customer = 0
            AND DATE(date_schedule) = CURDATE()
            ORDER BY id DESC
        ");
        $query->bind_param("i", $user_id);
        $query->execute();
        $result = $query->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC) ?: [];

        $menu_ids = $promo_ids = $group_ids = [];
        foreach ($reservations as &$res) {
            $menus = json_decode($res['selected_menus'], true) ?: [];
            $promos = json_decode($res['selected_promos'], true) ?: [];
            $groups = json_decode($res['selected_groups'], true) ?: [];

            foreach ($menus as $m) if (!in_array((int)$m['id'], $menu_ids, true)) $menu_ids[] = (int)$m['id'];
            foreach ($promos as $p) if (!in_array((int)$p['id'], $promo_ids, true)) $promo_ids[] = (int)$p['id'];
            foreach ($groups as $g) if (!in_array((int)$g['id'], $group_ids, true)) $group_ids[] = (int)$g['id'];

            $res['is_walkin'] = false;
            $res['source'] = 'reservation';
        }

        $menus = $promos = $groups = [];

        if ($menu_ids) {
            $in = implode(',', array_fill(0, count($menu_ids), '?'));
            $types = str_repeat('i', count($menu_ids));
            $stmt = $this->conn->prepare("SELECT * FROM menu WHERE menu_id IN ($in)");
            $stmt->bind_param($types, ...$menu_ids);
            $stmt->execute();
            $resMenus = $stmt->get_result();
            while ($row = $resMenus->fetch_assoc()) $menus[$row['menu_id']] = $row;
        }

        if ($promo_ids) {
            $in = implode(',', array_fill(0, count($promo_ids), '?'));
            $types = str_repeat('i', count($promo_ids));
            $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
            $stmt->bind_param($types, ...$promo_ids);
            $stmt->execute();
            $resPromos = $stmt->get_result();
            while ($row = $resPromos->fetch_assoc()) $promos[$row['deal_id']] = $row;
        }

        if ($group_ids) {
            $in = implode(',', array_fill(0, count($group_ids), '?'));
            $types = str_repeat('i', count($group_ids));
            $stmt = $this->conn->prepare("SELECT * FROM deals WHERE deal_id IN ($in)");
            $stmt->bind_param($types, ...$group_ids);
            $stmt->execute();
            $resGroups = $stmt->get_result();
            while ($row = $resGroups->fetch_assoc()) $groups[$row['deal_id']] = $row;
        }

        foreach ($reservations as &$res) {
            $resMenus = json_decode($res['selected_menus'], true) ?: [];
            foreach ($resMenus as &$menuItem) $menuItem['details'] = $menus[(int)$menuItem['id']] ?? [];
            $res['menus_details'] = $resMenus;

            $resPromos = json_decode($res['selected_promos'], true) ?: [];
            foreach ($resPromos as &$promoItem) $promoItem['details'] = $promos[(int)$promoItem['id']] ?? [];
            $res['promos_details'] = $resPromos;

            $resGroups = json_decode($res['selected_groups'], true) ?: [];
            foreach ($resGroups as &$groupItem) $groupItem['details'] = $groups[(int)$groupItem['id']] ?? [];
            $res['groups_details'] = $resGroups;
        }

        $walkins = [];
        $result = $this->conn->query("
            SELECT * FROM walkin_tables 
            WHERE walkin_status = 'unavailable' 
            AND DATE(walkin_created_at) = CURDATE()
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $walkins[] = [
                    'id' => $row['walkin_id'],
                    'table_code' => $row['walkin_table_code'],
                    'status' => $row['walkin_status'],
                    'is_walkin' => true,
                    'source' => 'walkin'
                ];
            }
        }

        return array_merge($reservations, $walkins);
    }

    /**
     * Set table as unavailable for walk-in customers
     * 
     * @param string $table_code Table number
     * @return bool True if operation successful, false otherwise
     */
    public function set_table_unavailable_walking($table_code) {
        $delete_sql = "DELETE FROM walkin_tables WHERE walkin_table_code = ?";
        $stmt_delete = $this->conn->prepare($delete_sql);
        $stmt_delete->bind_param("s", $table_code);
        $stmt_delete->execute();

        $insert_sql = "INSERT INTO walkin_tables (walkin_table_code, walkin_status)
                       VALUES (?, 'unavailable')";
        $stmt_insert = $this->conn->prepare($insert_sql);
        $stmt_insert->bind_param("s", $table_code);

        return $stmt_insert->execute();
    }

    /**
     * Set table as available from walk-in status
     * 
     * @param string $table_code Table number
     * @return bool True if operation successful, false otherwise
     */
    public function set_table_available_from_walkin($table_code) {
        $delete_sql = "DELETE FROM walkin_tables WHERE walkin_table_code = ?";
        $stmt = $this->conn->prepare($delete_sql);
        $stmt->bind_param("s", $table_code);

        return $stmt->execute();
    }

    /**
     * Retrieve all active menus for deals
     * 
     * @return array List of active menu items
     */
    public function fetch_menus_for_deals() {
        $sql = "SELECT * FROM menu WHERE menu_status = 1 ORDER BY menu_category, menu_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $menus = [];
        while ($row = $result->fetch_assoc()) {
            $menus[] = $row;
        }
        return $menus;
    }

    /**
     * Retrieve all group deals
     * 
     * @return array List of group deals
     */
    public function fetch_group_deals() {
        $sql = "SELECT * FROM deals WHERE deal_type = 'group_deals' ORDER BY deal_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $deals = [];
        while ($row = $result->fetch_assoc()) {
            $deals[] = $row;
        }
        return $deals;
    }

    /**
     * Create new group deal
     * 
     * @param string $name Group deal name
     * @param string $description Group deal description
     * @param float $price Group deal price
     * @param string $banner Group deal image filename
     * @param array $menuIds Array of menu item IDs
     * @return bool True if creation successful, false otherwise
     */
    public function createGroupDeal($name, $description, $price, $banner, $menuIds) {
        try {
            $sql = "INSERT INTO deals (deal_name, deal_description, deal_img_banner, deal_ids, deal_type, deal_price) 
                    VALUES (?, ?, ?, ?, 'group_deals', ?)";
            $stmt = $this->conn->prepare($sql);
            $menuIdsJson = json_encode($menuIds);
            $stmt->bind_param("sssss", $name, $description, $banner, $menuIdsJson, $price);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creating group deal: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove group deal from the system
     * 
     * @param int $group_id Group deal identifier
     * @return bool True if removal successful, false otherwise
     */
    public function removeGroupDeal($group_id) {
        try {
            $sql = "DELETE FROM deals WHERE deal_id = ? AND deal_type = 'group_deals'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $group_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error removing group deal: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve user information by ID
     * 
     * @param int $user_id User identifier
     * @return array|null User information or null if not found
     */
    public function getUserById($user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT user_fname, user_lname, user_email FROM user WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            return null;
        } catch (Exception $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all active holiday schedules
     * 
     * @return array List of active holidays
     */
    public function getActiveHolidays() {
        try {
            $sql = "SELECT * FROM holiday_schedules WHERE status = 'active' AND holiday_date >= CURDATE() ORDER BY holiday_date ASC";
            $result = $this->conn->query($sql);
            $holidays = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $holidays[] = $row;
                }
            }
            return $holidays;
        } catch (Exception $e) {
            error_log("Error getting active holidays: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve all active emergency closures
     * 
     * @return array List of active closures
     */
    public function getActiveClosures() {
        try {
            $sql = "SELECT * FROM emergency_closures WHERE status = 'active' AND closure_date >= CURDATE() ORDER BY closure_date ASC";
            $result = $this->conn->query($sql);
            $closures = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $closures[] = $row;
                }
            }
            return $closures;
        } catch (Exception $e) {
            error_log("Error getting active closures: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Send broadcast email to all registered users
     * 
     * @param string $type Type of broadcast (closure, maintenance, event)
     * @param string $date Date of the announcement
     * @param string $reason Detailed explanation
     * @return array Operation result with success status and message
     */
    public function sendBroadcastEmail($type, $date, $reason) {
        try {
            $mailer = new Mailer();
            $users = $this->getAllUsers();
            $successCount = 0;
            
            foreach ($users as $user) {
                $result = $mailer->sendBroadcastNotification(
                    $user['user_email'],
                    $user['user_fname'] . ' ' . $user['user_lname'],
                    $type,
                    $date,
                    $reason
                );
                
                if ($result) $successCount++;
            }
            
            return [
                'success' => true,
                'message' => 'Broadcast notification dispatched to ' . $successCount . ' registered users.'
            ];
        } catch (Exception $e) {
            error_log("Broadcast email error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error encountered during broadcast notification: ' . $e->getMessage()
            ];
        }
    }
}