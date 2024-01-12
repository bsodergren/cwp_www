<?php
/**
 * CWP Media Load Flag Creator.
 */

require_once '.config.inc.php';

define('TITLE', 'Email excel zip file');
use CWP\Core\Media;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLForms;
use CWP\Utils\MediaDevice;

$mediaDir = new MediaFileSystem();

$null_style = 'background-color : #94f9b2;';

$table = Media::$explorer->table('email_list'); // UPDATEME
$table->select('id,name,email');
foreach ($table as $id => $row) {
    $optionArray[$row->name] = $row->email;
}

$select_html = HTMLForms::draw_select('email', 'Bind Style', $optionArray, $null_style, '');

MediaDevice::getHeader();

if (isset($_REQUEST['job_id'])) {
    $zip_file = $media->zip_file;
    if ($mediaDir->exists($zip_file)) {
        $template->render('mail/main', ['JOB_ID' => $_REQUEST['job_id'], 'DROPDOWN_EMAILS' => $select_html]);
    }
} else {
    echo 'No Job ID';
}

MediaDevice::getFooter();
