<?php
/**
 * CWP Media tool
 */

use CWP\HTML\Template;
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaError;
use CWP\Process\MediaProcess;


/**
 * CWP Media tool.
 */
require '.config.inc.php';

define('__FORM_POST__', basename(parse_url($_SERVER['HTTP_REFERER'], \PHP_URL_PATH), '.php'));

/*
if (
    __FORM_POST__ == __SCRIPT_NAME__
    || __FORM_POST__ == ''
) {
    MediaError::msg('info', __FORM_POST__.'< >'.__SCRIPT_NAME__, 0);
}

$FORM_PROCESS = '';
if (isset($_POST['FORM_PROCESS'])) {
    $FORM_PROCESS = $_POST['FORM_PROCESS'];
    unset($_POST['FORM_PROCESS']);
}
*/

if (isset($_POST['divClass'])) {
    list($k, $id) = explode('_', $_POST['row_id']);
    if (str_contains($_POST['divClass'], 'show')) {
        $hidden = 1;
    } else {
        $hidden = 0;
    }

    $count = $explorer->table('media_job')
        ->where('job_id', $id) // must be called before update()
        ->update([
            'hidden' => $hidden,
        ]);
    exit;
}

$procesClass = 'CWP\\Process\\'.ucfirst(__FORM_POST__);

$mediaProcess = new $procesClass($media);
$mediaProcess->run($_REQUEST);
$mediaProcess->reload();
