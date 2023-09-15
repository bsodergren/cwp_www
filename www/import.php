<?php
/**
 * CWP Media tool
 */

use CWP\Core\Media;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Media\Mail\EmailImport;
use CWP\Filesystem\MediaFileSystem;

/**
 * CWP Media tool.
 */
require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
// $template = new Template();

MediaDevice::getHeader();

/* connect to gmail */

$locations = new MediaFileSystem();
$upload_directory = $locations->getDirectory('upload', true);
$params = [];
$upload_params = [];
$pdf_select_options = [];

if (1 == __IMAP_ENABLE__)
{
    /* try to connect */
    $import = new EmailImport();
    $emails = $import->search();

    if ($emails) {

        foreach ($emails as $i => $m)
        {
            $import->mailId = $m;
            $import->hasAttachment();
        }
        $import->moveAttachments();
        $import->getJobNumbers();
        $mail_import_card['FIRST_FORM']=$import->drawSelectBox();
        $params['EMAIL_IMPORT_HTML'] = template::GetHTML('/import/form_card', $mail_import_card);

    }




//    dd($import->attachments);
}
//	echo $output;

/* close the connection */
$import_card['CARD_HEADER'] = 'Import from Computer';
$import_card['FIRST_FORM'] = template::GetHTML('/import/form_text', ['JN_NAME' => 'job_number']);
$import_card['SECOND_FORM'] = template::GetHTML('/import/form_upload', []);
$import_card['BUTTON_SUBMIT'] = template::GetHTML('/import/form_submit', []);
$params['UPLOAD_IMPORT_HTML'] = template::GetHTML('/import/form_card', $import_card);

$template->render('import/main', $params);

MediaDevice::getFooter();
