<?php
require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
//$template = new Template();

require_once __LAYOUT_HEADER__;



if ( isset($_REQUEST['job_id']))
{
    
    $job_id = $_REQUEST['job_id'];
    $job  = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media = new Media($job);
$zip_file = $media->zip_file;
    if (is_file($zip_file)) {
        echo  $zip_file . " exists ";
    }
}



$template->render('mail/main',[]);

require_once __LAYOUT_FOOTER__;
