<?php
/**
 * CWP Media tool
 */

$constants    = [
    '__APP_ROOT__',
    '__WEB_ROOT__',
    '__ROOT_BIN_DIR__',
    //  '__SQLITE_DIR__',
];

$urlconstants = [
    '__URL_PATH__',
    '__URL_HOME__',
    '__URL_LAYOUT__',
];

$pass         = true;
foreach ($constants as $const) {
    $value = constant($const);
    if (is_dir($value)) {
        $msg[] = $value;
    } else {
        $msg[] = 'Dir not found - '.$value;
        $pass  = false;
    }
}
foreach ($urlconstants as $const) {
    $value = constant($const);
    $msg[] = $value;
}

foreach ($includes as $file) {
    if (!file_exists($file)) {
        $msg[] = 'Include file '.$file.' not found';
        $pass  = false;
    }
}

if (true === $pass) {
    touch($__conf_checked);
} else {
    foreach ($msg as $txt) {
        echo $txt.'<br>';
    }
    exit;
}

// dd("Config Checker");
