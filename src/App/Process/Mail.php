<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Core\Media;
use CWP\Media\MediaMailer;
use  CWPDisplay\Template\Template;

class Mail extends MediaProcess
{
    public function run($req)
    {
        if ('' == $req['email']) {
            $list = new EmailList();

            $data = ['name' => $req['rcpt_name'],  'email' => $req['mailto']];
            $list->addEmail($data);
        }

        $this->url = '/index.php';
        $this->timeout = 0;
        $job_id = $req['job_id'];

        $product = Media::$connection->fetch('select product from media_forms where job_id = ? group by product', $job_id);

        $product = $product->product;
        $job_number = $this->media->job_number;
        $attachment = $this->media->zip_file;

        $sendto = $req['mailto'];
        $sendname = $req['rcpt_name'];
        $subject = $req['subject'];
        /* connect to gmail */
        /* try to connect */
        $mail = new MediaMailer();

        $mail->recpt($sendto, $sendname);     // Add a recipient

        // Attachments
        $mail->attachment($attachment);         // Add attachments
        //    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        // Content
        $mail->subject($subject.' '.$product.' '.$job_number);
        $mail->Body(Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]));
        $mail->mail();
    }
}
