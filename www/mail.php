<?php
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

define('TITLE', 'Import new Media drop');
require_once __LAYOUT_HEADER__;

if (isset($_REQUEST['job_id'])) {
    $zip_file = $media->zip_file;
    if (is_file($zip_file)) {
        $template->render('mail/main', ['__FORM_URL__' => '/process.php', 'JOB_ID' => $_REQUEST['job_id']]);
    }
} else {
    echo 'No Job ID';
}

require_once __LAYOUT_FOOTER__;
