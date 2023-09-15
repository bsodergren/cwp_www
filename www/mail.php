<?php
/**
 * CWP Media tool for load flags
 */

require_once '.config.inc.php';

define('TITLE', 'Email excel zip file');
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();

if (isset($_REQUEST['job_id'])) {
    $zip_file = $media->zip_file;
    if (is_file($zip_file)) {
        $template->render('mail/main', ['__FORM_URL__' => __URL_PATH__.'/process.php', 'JOB_ID' => $_REQUEST['job_id']]);
    }
} else {
    echo 'No Job ID';
}

MediaDevice::getFooter();
