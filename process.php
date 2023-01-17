<?php
ob_start();
ob_implicit_flush(true);
require('.config.inc.php');
$template = new Template();

define('__FORM_POST__', basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '.php'));

define('TITLE', '');

if (
    __FORM_POST__ == __SCRIPT_NAME__ ||
    __FORM_POST__ == ''
) {
    MediaError::msg("info",__FORM_POST__."< >" . __SCRIPT_NAME__,0);
}

$FORM_PROCESS = '';
if (isset($_POST['FORM_PROCESS'])) {
    $FORM_PROCESS =  $_POST['FORM_PROCESS'];
    unset($_POST['FORM_PROCESS']);
}



switch (__FORM_POST__) {

    case "settings":
        require_once(__PROCESS_DIR__ . "/" . __FORM_POST__ . ".php");
        ob_flush();

        break;
    case "import":
        include __LAYOUT_HEADER__;
        require_once(__PROCESS_DIR__ . "/" . __FORM_POST__ . ".php");
        ob_flush();
        break;

    case "form":
        require_once(__PROCESS_DIR__ . "/" . __FORM_POST__ . ".php");
        break;
    case "index":
        require_once(__PROCESS_DIR__ . "/" . __FORM_POST__ . ".php");
        break;

    case "form_edit":
        require_once(__PROCESS_DIR__ . "/" . __FORM_POST__ . ".php");
        exit;
        break;
    default:
        dump(__FORM_POST__);
        dump($_POST);
        exit;
        break;
}

if (defined('REFRESH_URL')) {
    if (!defined('REFRESH_TIMEOUT')) {
        define('REFRESH_TIMEOUT', 0);
    }
    echo JavaRefresh(REFRESH_URL, REFRESH_TIMEOUT);
    ob_flush();
}


if (isset($_POST['FORM_PROCESS'])) {
    include_once __LAYOUT_FOOTER__;
}

ob_end_flush();
