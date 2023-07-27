<?php
/**
 * CWP Media tool
 */

namespace CWP\Media;

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MediaMailer
{
    public $mail;

    public function __construct()
    {
        $this->mail                 = new PHPMailer(true);
    }

    public function set($key, $value)
    {
        $this->$$key    = $value;
    }

    public function attachment($file)
    {
        $this->mail->addAttachment($file);
    }

    public function subject($text)
    {
        $this->mail->Subject    = $text;
    }

    public function body($text)
    {
        $this->mail->Body = $text; //       = Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]);
    }

    public function recpt($email, $name)
    {
        $this->mail->addAddress($email, $name);     // Add a recipient
    }

    public function mail()
    {
        try {
            // Server settings
            //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $this->mail->isSMTP();                                            // Send using SMTP
            $this->mail->Host       = 'imap.gmail.com';
            $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $this->mail->Username   =  __IMAP_USER__;                     // SMTP username
            $this->mail->Password   = __IMAP_PASSWD__;                               // SMTP password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
            $this->mail->Port       = 465;                                    // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            // Recipients
            $this->mail->setFrom('bjorn.sodergren@gmail.com', 'Mailer');

            // Attachments
            // Add attachments
            //    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            // Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->send();

            echo HTMLDisplay::JavaRefresh(HTMLDisplay::$url, HTMLDisplay::$timeout, 'Email to '.$sendname.' sent');
            ob_flush();
        } catch (Exception $e) {
            echo HTMLDisplay::JavaRefresh(HTMLDisplay::$url, HTMLDisplay::$timeout, "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
