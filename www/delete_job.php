<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\HTML\HTMLDisplay;

require_once '.config.inc.php';
define('TITLE', 'Media Job editor');

if (array_key_exists('actSubmit', $_REQUEST)) {
    if ('Confirm' == $_REQUEST['actSubmit']) {
        $media->delete_zip();
        $media->delete_xlsx();
        $media->delete_job();
    }

    echo HTMLDisplay::JavaRefresh('/index.php');
    exit;
}

use CWP\Utils\MediaDevice;

MediaDevice::getHeader();
$form_url = __URL_PATH__.'/delete_job.php';

$form = new Formr\Formr();
$hidden = ['job_id' => $job_id];
$form->open('', '', $form_url, 'post', '', $hidden);
echo HTMLDisplay::output('Are you sure you want to delete this job <br>');
$buttonClass = 'btn fw-bold btn-lg ';
$form->input_submit('actSubmit', '', 'Go Back', '', 'class="'.$buttonClass.' bg-success"');

$form->input_submit('actSubmit', '', 'Confirm', '', 'class="'.$buttonClass.'  bg-danger"');

$form->close();

MediaDevice::getFooter();
