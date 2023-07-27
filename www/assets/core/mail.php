<?php
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\MediaMailer;

HTMLDisplay::$url     = '/index.php';
HTMLDisplay::$timeout = 0;

$product              = $connection->fetch('select product from media_forms where job_id = ? group by product', $job_id);

$product              = $product->product;
$job_number           = $media->job_number;
$attachment           = $media->zip_file;

$sendto               = $_POST['mailto'];
$sendname             = $_POST['rcpt_name'];

/* connect to gmail */
/* try to connect */
$mail                 = new MediaMailer();

$mail->recpt($sendto, $sendname);     // Add a recipient

// Attachments
$mail->attachment($attachment);         // Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

// Content
$mail->subject($product.' '.$job_number);
$mail->Body(Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]));
$mail->mail();
