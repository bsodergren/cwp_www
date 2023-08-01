<?php
/**
 * CWP Media tool
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Media\Media;
use CWP\HTML\Template;
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaMailer;
use CWP\Spreadsheet\XLSXViewer;
use CWP\Spreadsheet\Media\MediaXLSX;
use Symfony\Component\Finder\Finder;

class Mail extends MediaProcess
{
    public function run($req)
    {
        $this->url = '/index.php';
        $this->timeout = 0;
        $job_id = $req['job_id'];

        $product = Media::$connection->fetch('select product from media_forms where job_id = ? group by product', $job_id);

        $product = $product->product;
        $job_number = $this->media->job_number;
        $attachment = $this->media->zip_file;

        $sendto = $req['mailto'];
        $sendname = $req['rcpt_name'];

        /* connect to gmail */
        /* try to connect */
        $mail = new MediaMailer();

        $mail->recpt($sendto, $sendname);     // Add a recipient

        // Attachments
        $mail->attachment($attachment);         // Add attachments
        //    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        // Content
        $mail->subject($product.' '.$job_number);
        $mail->Body(Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]));
        $mail->mail();
    }
}