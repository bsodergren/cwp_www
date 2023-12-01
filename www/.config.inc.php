<?php
/**
 * CWP Media tool for load flags
 */


use Rain\Tpl;
use CWP\Core\Media;
use Tracy\Debugger;
use Nette\Utils\FileSystem;
use CWP\Core\MediaStopWatch;

define('__PROJECT_ROOT__', dirname(__FILE__, 3));
define('__PUBLIC_ROOT__', dirname(__FILE__, 2));
define('__HTTP_ROOT__', dirname(__FILE__, 1));
/*
xdebug_set_filter(
    XDEBUG_FILTER_TRACING,
    XDEBUG_PATH_EXCLUDE,
    [ __PUBLIC_ROOT__ . "/vendor/" ]
);

xdebug_set_filter(
    XDEBUG_FILTER_STACK,
    XDEBUG_PATH_EXCLUDE,
    [ __PUBLIC_ROOT__ . "/vendor/" ]
 );
 */
require __PUBLIC_ROOT__ . \DIRECTORY_SEPARATOR . 'bootstrap.php';


// if (__DEBUG__ == 1) {

//  Debugger::enable();

//     Debugger::$showLocation = Tracy\Dumper::LOCATION_SOURCE; // Shows path to where the dump() was called
//     //Debugger::$logSeverity  = \E_WARNING | \E_NOTICE;
//     Debugger::$dumpTheme    = 'dark';
//     Debugger::$showBar      = true;          // (bool) defaults to true
//     //Debugger::$strictMode   = ~\E_DEPRECATED & ~\E_USER_DEPRECATED & ~\E_NOTICE;

//     Debugger::$showLocation = Tracy\Dumper::LOCATION_CLASS | Tracy\Dumper::LOCATION_LINK; // Shows both paths to the classes and link to where the dump() was called
//     Debugger::$showLocation = false; // Hides additional location information
//     Debugger::$showLocation = true; // Shows all additional location information
// }

// $boot->definePath('__DATABASE_ROOT__', dirname(__FILE__, 2).\DIRECTORY_SEPARATOR.'database');
MediaStopWatch::$writeNow = false;
MediaStopWatch::init();
MediaStopWatch::dump("Start");
$boot->definePath('__DATABASE_ROOT__', $boot->Config['db']['path'] . \DIRECTORY_SEPARATOR . 'database');
$boot->directory(__DATABASE_ROOT__);

$boot->definePath('__SQL_CONFIG_DIR__', __CWP_SOURCE__ . \DIRECTORY_SEPARATOR . 'Database');
$boot->definePath('__SQL_UPDATES_DIR__', __SQL_CONFIG_DIR__ . \DIRECTORY_SEPARATOR . 'updates');

$boot->definePath('__ASSETS_DIR__', __HTTP_ROOT__ . \DIRECTORY_SEPARATOR . 'assets');
$boot->definePath('__INC_CORE_DIR__', __ASSETS_DIR__ . \DIRECTORY_SEPARATOR . 'core');

$boot->getDatabase();

define('__TEMP_DIR__', sys_get_temp_dir());

require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'path_constants.php';


require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'boot.php';
require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'auth.php';

require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'variables.php';
require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'url_paths.php';
require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'settings.php';

// if (!defined("PROCESS")) {
require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'init.php';
// }

if(array_key_exists("flush", $_GET)) {
    Media::$Stash->flush();

    $urlParts =  parse_url($_SERVER['REQUEST_URI']);
    header('Location:  ' . $urlParts['path']);
    exit;
}

if (!defined("PROCESS")) {
    $req_file               = $_SERVER['REQUEST_URI'];
    $req                    = '?' . $_SERVER['QUERY_STRING'];
    $req_file               = str_replace(__URL_PATH__ . '/', '', $req_file);

    if ('' == $req_file) {
        header('Location:  ' . __URL_PATH__ . '/index.php');
        exit;
    }
}

$TemplateSrc = explode(DIRECTORY_SEPARATOR,__CWP_SOURCE__);
$TemplateSrc[] = "Templates";
$commonTemplates = ['footer','navbar','header'];
$TemplateCommon = implode(DIRECTORY_SEPARATOR,array_merge($TemplateSrc,['common'])).DIRECTORY_SEPARATOR;
$TemplatePages = implode(DIRECTORY_SEPARATOR,array_merge($TemplateSrc,['pages'])).DIRECTORY_SEPARATOR;

$templateDir[] = Filesystem::platformSlashes($TemplateCommon);
$templateDir[] = Filesystem::platformSlashes($TemplatePages);
foreach($commonTemplates as $tempDir)
{
    $templateDir[] = Filesystem::platformSlashes($TemplateCommon . $tempDir).DIRECTORY_SEPARATOR;
}
$templateDir[] = Filesystem::platformSlashes($TemplatePages . basename($_SERVER['SCRIPT_FILENAME'], ".php").DIRECTORY_SEPARATOR);

Tpl::configure(["tpl_dir" => $templateDir,"cache_dir" => __CACHE_DIR__,"debug"=>true]);
//Tpl::registerPlugin( new PathReplace );

$tpl_nabar_links = $nav_bar_links;
$Tplnav_bar_dropdown = $tpl_nabar_links['Settings'];
unset($tpl_nabar_links['Settings']);

$TplTemplate = new Tpl();

$TplTemplate->assign('nav_bar_links', $tpl_nabar_links);
$TplTemplate->assign('nav_bar_dropdown', $Tplnav_bar_dropdown);
$TplTemplate->assign('current', Media::$CurrentVersion);
$TplTemplate->assign('update', Media::$VersionUpdate);

if (\array_key_exists('msg', $GLOBALS['_REQUEST'])) {
    $TplTemplate->assign('return_msg', $GLOBALS['_REQUEST']['msg']);
}
