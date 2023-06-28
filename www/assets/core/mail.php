<?php
require_once('.config.inc.php');

define('REFRESH_URL', '/index.php');
define('REFRESH_TIMEOUT', 0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$product  = $connection->fetch('select product from media_forms where job_id = ? group by product', $job_id);

$product = $product->product;
$job_number = $media->job_number;
$attachment = $media->zip_file;

$sendto = $_POST['mailto'];
$sendname = $_POST['rcpt_name'];

/* connect to gmail */
/* try to connect */
$mail = new PHPMailer(true);

try {
    //Server settings
  //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   =  $conf['gmail']['name'];                     //SMTP username
    $mail->Password   = $conf['gmail']['password'];                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('bjorn.sodergren@gmail.com', 'Mailer');
    $mail->addAddress($sendto, $sendname );     //Add a recipient

    //Attachments
    $mail->addAttachment($attachment );         //Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $product . " " . $job_number;    
    $mail->Body    = Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]);
    $mail->send();

        echo HTMLDisplay::JavaRefresh(REFRESH_URL, REFRESH_TIMEOUT, "Email to " .$sendname. " sent");
        ob_flush();
    } catch (Exception $e) {
        echo HTMLDisplay::JavaRefresh(REFRESH_URL, REFRESH_TIMEOUT, "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
}



