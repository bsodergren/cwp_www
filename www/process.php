<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Core\MediaError;
use CWP\HTML\HTMLDisplay;

require '.config.inc.php';

$refer_script = basename(parse_url($_SERVER['HTTP_REFERER'], \PHP_URL_PATH), '.php');
if (__SCRIPT_NAME__ == $refer_script) {
    MediaError::msg('info', $refer_script.'< >'.__SCRIPT_NAME__, 0);
}

if (null === $refer_script || '' == $refer_script) {
    MediaError::msg('info', 'referer not set', 0);
    echo HTMLDisplay::JavaRefresh('/index.php', 0);
}

define('__FORM_POST__', $refer_script);

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

$procesClass = ucfirst(__FORM_POST__);
if (array_key_exists('FORM_PROCESS', $_POST)) {
    if ('updateSetting' == $_POST['FORM_PROCESS']) {
        $procesClass = ucfirst('Settings');
    }
}

$procesClass  = 'CWP\\Process\\'.$procesClass;
$mediaProcess = new $procesClass($media);
//dd($procesClass, $_REQUEST);
$mediaProcess->run($_REQUEST);

$mediaProcess->reload();
