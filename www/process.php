<?php

ob_start();
ob_implicit_flush(true);
require('.config.inc.php');

define('__FORM_POST__', basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '.php'));

define('TITLE', '');

if (
    __FORM_POST__ == __SCRIPT_NAME__ ||
    __FORM_POST__ == ''
) {
    MediaError::msg("info", __FORM_POST__ . "< >" . __SCRIPT_NAME__, 0);
}

$FORM_PROCESS = '';
if (isset($_POST['FORM_PROCESS'])) {
    $FORM_PROCESS =  $_POST['FORM_PROCESS'];
    unset($_POST['FORM_PROCESS']);
}
if (isset($_POST['divClass'])) {

    list($k, $id) = explode("_", $_POST['row_id']);
    if (str_contains($_POST['divClass'], "show")) {

        $hidden = 1;
    } else {
        $hidden = 0;
    }

    $count = $explorer->table('media_job')
        ->where('job_id', $id) // must be called before update()
        ->update([
            'hidden' => $hidden
        ]);
    exit;
}

switch (__FORM_POST__) {

    case "settings":
        require_once(__INC_CORE_DIR__ . "/" . __FORM_POST__ . ".php");
        ob_flush();

        break;
    case "import":
        include __LAYOUT_HEADER__;
        require_once(__INC_CORE_DIR__ . "/" . __FORM_POST__ . ".php");
        ob_flush();
        break;

    case "form":
        require_once(__INC_CORE_DIR__ . "/" . __FORM_POST__ . ".php");
        break;
    case "index":
        require_once(__INC_CORE_DIR__ . "/" . __FORM_POST__ . ".php");
        break;

    case "form_edit":
        require_once(__INC_CORE_DIR__ . "/" . __FORM_POST__ . ".php");
        exit;
        break;
    case "mail":
        require_once(__INC_CORE_DIR__ . "/" . __FORM_POST__ . ".php");
        exit;
        break;
    default:

        exit;
        break;
}

if (!defined("REFRESH_MSG")) {
    define('REFRESH_MSG', '');
}

if (HTMLDisplay::$url !== false) {
    echo HTMLDisplay::JavaRefresh(HTMLDisplay::$url, HTMLDisplay::$timeout, REFRESH_MSG);
    ob_flush();
}


if (isset($_POST['FORM_PROCESS'])) {
    include_once __LAYOUT_FOOTER__;
}

ob_end_flush();
