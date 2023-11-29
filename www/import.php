<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTML_Import;
use CWP\Media\Mail\EmailImport;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
// $template = new Template();
$files = [];
MediaDevice::getHeader();
$table = $explorer->table('media_job');
$results = $table->fetchAssoc('job_id');
foreach ($results as $id => $row) {
    $files[] = $row['pdf_file'];
}
/* connect to gmail */

$locations = new MediaFileSystem();
// $upload_directory = $locations->getDirectory('upload', true);

$params = [];
$upload_params = [];
$pdf_select_options = [];

if (1 == __IMAP_ENABLE__) {
    /* try to connect */
    $import = new EmailImport();
    $emails = $import->search();

    if ($emails) {
        $hasAttachment = false;
        foreach ($emails as $i => $m) {
            $has = null;
            $import->mailId = $m;

            $has = $import->hasAttachment();
            if($has === true){
                $hasAttachment = true;
            }
        }

        if($hasAttachment === true)
        {

            $import->moveAttachments();
            $import->getJobNumbers();

            $mail_import_card['CARD_HEADER'] = 'New jobs from Gmail';
            $mail_import_card['FIRST_FORM'] = $import->drawSelectBox();
            $params['EMAIL_IMPORT_HTML'] = Template::GetHTML('/import/form_card', $mail_import_card);
        }
    }
}

$pdfArray = [];
$dirs = $locations->getDirectory('upload', false);
$results = $locations->getContents($dirs);
foreach ($results as $key => $pdf_file) {
    if (!in_array($pdf_file, $files)) {
        $pdfArray[] = ["name" => basename($pdf_file), 'filename' => $pdf_file];
    }
}

if(count($pdfArray) > 0){
    $import_card['FIRST_FORM'] = HTML_Import::drawSelectBox($pdfArray);

    $import_card['CARD_HEADER'] = 'Import from Folder';
    $params['DROPBOX_FILES_HTML'] = template::GetHTML('/import/form_card', $import_card);
}

/* close the connection */
$import_card['CARD_HEADER'] = 'Upload from Computer';
$import_card['SECOND_FORM'] = template::GetHTML('/import/upload/form_text', ['JN_NAME' => 'upload[job_number]']);
$import_card['FIRST_FORM'] = template::GetHTML('/import/upload/form_upload', []);
$import_card['BUTTON_SUBMIT'] = template::GetHTML('/import/form_submit', []);
$params['UPLOAD_IMPORT_HTML'] = template::GetHTML('/import/form_card', $import_card);

$template->render('import/main', $params);

MediaDevice::getFooter();
