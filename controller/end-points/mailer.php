<?php
require '../../vendor/autoload.php';
include('../class.php');
$db = new global_class();

date_default_timezone_set('Asia/Manila');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$reservations_id = $_POST['reservations_id'];
$actionStatus = ucfirst($_POST['actionStatus']); // Make first letter uppercase

$fetch_reservation = $db->fetch_reservation($reservations_id);
$data = mysqli_fetch_assoc($fetch_reservation);

$date_schedule = $data['date_schedule'];
$time_schedule = $data['time_schedule'];
$table_code = $data['table_code'];
$Email = $data["user_email"];
$Fullname = $data["user_fname"] . ' ' . $data["user_lname"];
$order_code = $data["reserve_unique_code"];

$date_scheduleTime = new DateTime("$date_schedule $time_schedule");
$formatted = $date_scheduleTime->format('l, F j, Y - g:i A');

class Mailer extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

    public function sendAccountNotification($Email, $Fullname, $order_code, $formatted, $actionStatus, $table_code)
    {
        try {
            $subject = "Reservation {$actionStatus} - {$order_code}";

            // --- generate QR code image (server-side) ---
            $qrData = rawurlencode($order_code);
            $qrSize = "300x300";
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$qrSize}&data={$qrData}";
            $qrImage = @file_get_contents($qrUrl);
            if ($qrImage === false) {
                $qrImage = null;
            }

            // Normalize status for comparison
            $status = strtolower(trim($actionStatus));

            // ✉️ Different messages for each status
            if ($status === "confirmed") {
                $message = "
                    <h2>Hi {$Fullname},</h2>
                    <p>Your reservation has been <strong>confirmed</strong>.</p>
                    <p>Table <strong>{$table_code}</strong> has been reserved for you on: {$formatted}</p>
                    <p>Please arrive on time to enjoy your dining experience with us.</p>
                    <p><strong>Note:</strong> Present the QR code below to the cashier upon arrival.</p>
                    <p>We look forward to serving you!</p>
                ";
                $altMessage = "Hello {$Fullname},\n\nYour reservation {$order_code} has been confirmed.\nTable: {$table_code}\nSchedule: {$formatted}\n\nPlease arrive on time.\nNOTE: Present the QR code to the cashier upon arrival.\nWe look forward to serving you!";
            } 
            elseif ($status === "completed") {
                $message = "
                    <h2>Hi {$Fullname},</h2>
                    <p>Thank you for dining with us!</p>
                    <p>Your reservation <strong>{$order_code}</strong> has been successfully <strong>completed</strong>.</p>
                    <p>We hope you had a wonderful experience at our restaurant.</p>
                    <p>We look forward to serving you again soon!</p>
                ";
                $altMessage = "Hello {$Fullname},\n\nThank you for dining with us!\nYour reservation {$order_code} has been successfully completed.\nWe hope you had a great time and we look forward to seeing you again soon!";
            }
            elseif ($status === "cancelled") {
                $message = "
                    <h2>Hi {$Fullname},</h2>
                    <p>We regret to inform you that your reservation <strong>{$order_code}</strong> has been <strong>cancelled</strong>.</p>
                    <p>If you have any questions or wish to rebook, please contact our support team.</p>
                    <p>We hope to see you another time!</p>
                ";
                $altMessage = "Hello {$Fullname},\n\nYour reservation {$order_code} has been cancelled.\n\nIf you have any questions or wish to rebook, please contact our support team.\nWe hope to see you another time!";
            }
            elseif ($status === "request new schedule") {
                $message = "
                    <h2>Hi {$Fullname},</h2>
                    <p>We’ve received your <strong>request for a new schedule</strong> for reservation <strong>{$order_code}</strong>.</p>
                    <p>Our team will review your request and confirm the new schedule shortly.</p>
                    <p>We’ll notify you once it’s approved. Thank you for your patience!</p>
                ";
                $altMessage = "Hello {$Fullname},\n\nWe received your request for a new schedule for reservation {$order_code}.\nOur team will review and confirm soon.\nThank you for your patience!";
            }
            elseif ($status === "request cancel") {
                $message = "
                    <h2>Hi {$Fullname},</h2>
                    <p>We’ve received your <strong>request to cancel</strong> your reservation <strong>{$order_code}</strong>.</p>
                    <p>Our team will review your request and confirm the cancellation shortly.</p>
                    <p>We’ll update you once it’s finalized. Thank you for reaching out!</p>
                ";
                $altMessage = "Hello {$Fullname},\n\nWe received your request to cancel reservation {$order_code}.\nOur team will review and confirm shortly.\nThank you for reaching out!";
            }
            else {
                // Fallback message for any unknown status
                $message = "
                    <h2>Hi {$Fullname},</h2>
                    <p>Your reservation status has been updated to: <strong>{$actionStatus}</strong>.</p>
                    <p>Order Code: {$order_code}</p>
                ";
                $altMessage = "Hello {$Fullname},\n\nYour reservation status has been updated to: {$actionStatus}.\nOrder Code: {$order_code}";
            }

            // --- PHPMailer setup ---
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rodriguezryan325@gmail.com';
            $mail->Password = 'ofvf yxut wpcc iecx'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('rodriguezryan325@gmail.com', 'GRILLBOOK');
            $mail->addAddress($Email, $Fullname);
            $mail->addReplyTo('rodriguezryan325@gmail.com', 'No Reply');

            // --- Attach QR only for confirmed ---
            $qrCid = null;
            if ($qrImage !== null && $status === "confirmed") {
                $qrCid = 'qrcode_' . md5($order_code);
                $mail->addStringEmbeddedImage($qrImage, $qrCid, "{$order_code}.png", 'base64', 'image/png');
                $mail->addStringAttachment($qrImage, "{$order_code}.png", 'base64', 'image/png');
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;

            $qrHtml = '';
            $downloadLinkHtml = '';
            if ($qrCid !== null) {
                $qrHtml = "<p><strong>Reservation QR Code:</strong><br><img src='cid:{$qrCid}' alt='QR Code' style='width:250px;height:250px;border:1px solid #ddd;border-radius:6px;'></p>";
                $downloadLinkHtml = "<p><a href='{$qrUrl}' target='_blank' style='display:inline-block;padding:8px 12px;border-radius:6px;border:1px solid #ccc;text-decoration:none;'>Download QR Image</a></p>";
            }

            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head><meta charset='UTF-8'></head>
                <body>
                    <div style='max-width:600px;margin:auto;padding:20px;background:#f9f9f9;border-radius:8px;font-family:sans-serif;'>
                        {$message}
                        {$qrHtml}
                        {$downloadLinkHtml}
                        <p style='font-size:13px;color:#666;'>Order Code: <strong>{$order_code}</strong></p>
                    </div>
                </body>
                </html>
            ";

            $mail->AltBody = $altMessage . ($qrUrl ? "\n\nQR: {$qrUrl}" : "");

            $mail->send();
            echo json_encode(['status' => 200, 'message' => 'Email sent successfully.']);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 500,
                'message' => 'Mailer Error: ' . $e->getMessage()
            ]);
        }
    }


}

$mailer = new Mailer();
$mailer->sendAccountNotification($Email, $Fullname, $order_code, $formatted, $actionStatus,$table_code);
