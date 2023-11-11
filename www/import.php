<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Core\Media;
use CWP\Filesystem\MediaFileSystem;
use CWP\Media\Mail\EmailImport;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
// $template = new Template();
$files                        = [];
MediaDevice::getHeader();
$table                        = $explorer->table('media_job');
$results                      = $table->fetchAssoc('job_id');
foreach ($results as $id => $row) {
    $files[] = $row['pdf_file'];
}
/* connect to gmail */

$locations                    = new MediaFileSystem();
$upload_directory             = $locations->getDirectory('upload', true);
$params                       = [];
$upload_params                = [];
$pdf_select_options           = [];
if (1 == __IMAP_ENABLE__) {
    /* try to connect */
    $import = new EmailImport();
    $emails = $import->search();

    if ($emails) {
        foreach ($emails as $i => $m) {
            $import->mailId = $m;
            $import->hasAttachment();
        }
        $import->moveAttachments();
        // $import->getJobNumbers();
        $mail_import_card['CARD_HEADER'] = 'New jobs from Gmail';
        $mail_import_card['FIRST_FORM']  = $import->drawSelectBox();
        $params['EMAIL_IMPORT_HTML']     = Template::GetHTML('/import/form_card', $mail_import_card);
    }
    // dd($import->attachments);
}
//dd($locations->getDirectory('pdf', false));
$results                      = $locations->getContents($locations->getDirectory('pdf', false));
foreach ($results as $key => $pdf_file) {
    if (! in_array($pdf_file, $files)) {
        $dropbox_options_html .= template::GetHTML('/import/dropbox/form_option', [
            'OPTION_VALUE' => $pdf_file,
            'OPTION_NAME'  => basename($pdf_file, '.pdf'),
        ]);
    }
}

$import_card['SECOND_FORM']   = template::GetHTML('/import/dropbox/jobnumber', ['JN_NAME' => 'dropbox[job_number]']);
$import_card['FIRST_FORM']    = template::GetHTML('/import/dropbox/form_select', [
    'SELECT_OPTIONS' => $dropbox_options_html,
    'SELECT_NAME'    => 'dropbox[pdf_file]',
]);

$import_card['CARD_HEADER']   = 'Import from Folder';
$params['DROPBOX_FILES_HTML'] = template::GetHTML('/import/form_card', $import_card);

//	echo $output;

/* close the connection */
$import_card['CARD_HEADER']   = 'Upload from Computer';
$import_card['SECOND_FORM']   = template::GetHTML('/import/upload/form_text', ['JN_NAME' => 'upload[job_number]']);
$import_card['FIRST_FORM']    = template::GetHTML('/import/upload/form_upload', []);
$import_card['BUTTON_SUBMIT'] = template::GetHTML('/import/form_submit', []);
$params['UPLOAD_IMPORT_HTML'] = template::GetHTML('/import/form_card', $import_card);

$template->render('import/main', $params);

MediaDevice::getFooter();
