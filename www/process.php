<?php
/**
 * CWP Media tool
 */

require '.config.inc.php';

define('__FORM_POST__', basename(parse_url($_SERVER['HTTP_REFERER'], \PHP_URL_PATH), '.php'));

define('TITLE', 'processing');

if (
    __FORM_POST__ == __SCRIPT_NAME__
    || __FORM_POST__ == ''
) {
    MediaError::msg('info', __FORM_POST__.'< >'.__SCRIPT_NAME__, 0);
}

$FORM_PROCESS = '';
if (isset($_POST['FORM_PROCESS'])) {
    $FORM_PROCESS =  $_POST['FORM_PROCESS'];
    unset($_POST['FORM_PROCESS']);
}
if (isset($_POST['divClass'])) {
    list($k, $id) = explode('_', $_POST['row_id']);
    if (str_contains($_POST['divClass'], 'show')) {
        $hidden = 1;
    } else {
        $hidden = 0;
    }

    $count        = $explorer->table('media_job')
        ->where('job_id', $id) // must be called before update()
        ->update([
            'hidden' => $hidden,
        ]);
    exit;
}

switch (__FORM_POST__) {
    case 'import':
        include __LAYOUT_HEADER__;
        // no break
    case 'trim':
    case 'view':
    case 'settings':
    case 'form':
    case 'index':
        require_once __INC_CORE_DIR__.'/'.__FORM_POST__.'.php';
        break;
    case 'form_edit':
    case 'mail':
        require_once __INC_CORE_DIR__.'/'.__FORM_POST__.'.php';
        exit;
    default:
        break;
}

if (!defined('REFRESH_MSG')) {
    define('REFRESH_MSG', '');
}

if (false !== HTMLDisplay::$url) {
    echo HTMLDisplay::JavaRefresh(HTMLDisplay::$url, HTMLDisplay::$timeout, REFRESH_MSG);
}

if (isset($_POST['FORM_PROCESS'])) {
    include_once __LAYOUT_FOOTER__;
}
