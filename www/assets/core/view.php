<?php
/**
 * CWP Media tool
 */

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\MediaMailer;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Spreadsheet\XLSXViewer;
use Symfony\Component\Finder\Finder;

/**
 * CWP Media tool.
 */
require_once '.config.inc.php';

$form_number      = $_REQUEST['form_number'];

if (key_exists('action', $_REQUEST)) {
    if ('update' == $_REQUEST['action']) {
        $media->excelArray($form_number);

        $excel            = new MediaXLSX($media);
        $excel->writeWorkbooks();

        ob_flush();
        $msg              = 'XLSX Files Created';
        define('REFRESH_MSG', $msg);
        HTMLDisplay::$url = '/view.php?job_id='.$job_id.'&form_number='.$form_number;
    }

    if ('send' == $_REQUEST['action']) {
        $finder                    = new Finder();

        $finder->files()->in($media->xlsx_directory)->name('*.xlsx')->notName('~*')->sortByName(true);

        if (!$finder->hasResults()) {
            XLSXViewer::checkifexist($media);
        }

        foreach ($finder as $file) {
            preg_match('/.*_([FM0-9]+).xlsx/', $file->getRealPath(), $output_array);
            [$text_form,$text_number] = explode('FM', $output_array[1]);

            if ($form_number == $text_number) {
                $pdf_file       = $file->getRealPath();
            }
        }

        $sendto                    = $_POST['mailto'];
        $sendname                  = $_POST['rcpt_name'];

        $mail                      = new MediaMailer();
        $mail->recpt($sendto, $sendname);

        $mail->attachment($pdf_file);
        $mail->subject($product.' '.$job_number);
        $mail->Body(Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]));
        $mail->mail();

        $msg                       = 'XLSX File emailed';
        define('REFRESH_MSG', $msg);
        HTMLDisplay::$url          = '/view.php?job_id='.$job_id.'&form_number='.$form_number;
    }
}
