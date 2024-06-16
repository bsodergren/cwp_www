<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\Core\MediaStopWatch;
use Tracy\Debugger;
use UTMTemplate\Template;
use UTMTemplate\UtmDevice;

define('__ROOT_DIRECTORY__', dirname(realpath($_SERVER['CONTEXT_DOCUMENT_ROOT']), 1));

require __ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'bootstrap.php';
// if (__DEBUG__ == 1) {
Debugger::enable(Debugger::Development);

// Debugger::$showLocation = Tracy\Dumper::LOCATION_SOURCE; // Shows path to where the dump() was called
// Debugger::$logSeverity = \E_WARNING | \E_NOTICE;
// Debugger::$dumpTheme = 'dark';
// Debugger::$showBar = true;          // (bool) defaults to true
// Debugger::$strictMode = ~\E_DEPRECATED & ~\E_USER_DEPRECATED & ~\E_NOTICE;

// Debugger::$showLocation = Tracy\Dumper::LOCATION_CLASS | Tracy\Dumper::LOCATION_LINK; // Shows both paths to the classes and link to where the dump() was called
// Debugger::$showLocation = false; // Hides additional location information
// Debugger::$showLocation = true; // Shows all additional location information
// }

// $boot->definePath('__DATABASE_ROOT__', dirname(__FILE__, 2).\DIRECTORY_SEPARATOR.'database');
// MediaStopWatch::$writeNow = false;
// MediaStopWatch::init();
// MediaStopWatch::start();

// MediaStopWatch::dump('Start');





$boot->getDatabase();

define('__TEMP_DIR__', sys_get_temp_dir());
// define('__TEMP_DIR__', __CWP_SOURCE__.DIRECTORY_SEPARATOR.'var/tmp');
Media::$Stash->flush();

require_once __CWP_CONFIGURATION__.\DIRECTORY_SEPARATOR.'Path'.\DIRECTORY_SEPARATOR.'path_constants.php';
require_once __CWP_CONFIGURATION__.\DIRECTORY_SEPARATOR.'Path'.\DIRECTORY_SEPARATOR.'url_paths.php';

require_once __CWP_CONFIGURATION__.\DIRECTORY_SEPARATOR.'Language.php';
require_once __CWP_CONFIGURATION__.\DIRECTORY_SEPARATOR.'boot.php';


Template::$registeredCallbacks = [
    '\CWPDisplay\Template\Callbacks\FunctionCallback::FUNCTION_CALLBACK' => 'callback_parse_function',
    '\CWPDisplay\Template\Callbacks\FunctionCallback::SCRIPTINCLUDE_CALLBACK' => 'callback_script_include'];

// Template::$registeredFilters = [
//     '\Plex\Template\Callbacks\URLFilter::parse_urllink' => ['a=href' => ['library' => $_REQUEST['library']]],
// ];
Template::$TEMPLATE_COMMENTS      = false;

Template::$USER_TEMPLATE_DIR      = __HTML_TEMPLATE__; // /home/bjorn/www/cwp_www/src/Website/Layout/Default
Template::$SITE_URL               = __URL_ASSETS__; // http://wslubuntu/cwp/assets
Template::$ASSETS_URL             = __URL_ASSETS__.\DIRECTORY_SEPARATOR.'Default';

Template::$SITE_PATH              = __PATH_ASSETS__; //'/home/bjorn/www/plex_web/html/assets'
Template::$ASSETS_PATH            = __PATH_ASSETS__.\DIRECTORY_SEPARATOR.'Default';

Template::$CACHE_DIR              = __TPL_CACHE_DIR__; // '/home/bjorn/www/plex_web/src/var/cache/template/'
Template::$USE_TEMPLATE_CACHE     = false;

UtmDevice::$DETECT_BROWSER        = false;
UtmDevice::$USER_DEFAULT_TEMPLATE = __HTML_TEMPLATE__; //'/home/bjorn/www/plex_web/Layout/Default'

// UtmDevice::$USER_MOBILE_TEMPLATE  = __MOBILE_TEMPLATE__; // '/home/bjorn/www/plex_web/Layout/Mobile'
// UtmDevice::$MOBILE_ASSETS_URL     = __URL_ASSETS__.\DIRECTORY_SEPARATOR.'Mobile';
// UtmDevice::$MOBILE_ASSETS_PATH    = __LAYOUT_PATH__.\DIRECTORY_SEPARATOR.'Mobile';

$device = new UtmDevice();
$boot->loadPage();
$const_keys = array_keys(get_defined_constants(true)['user']);
define('__TEMPLATE_CONSTANTS__', $const_keys);

if (array_key_exists('flush', $_GET)) {
    Media::$Stash->flush();

    $urlParts = parse_url($_SERVER['REQUEST_URI']);
    header('Location:  '.$urlParts['path']);
    exit;
}

if (!defined('PROCESS')) {
    $req_file = $_SERVER['REQUEST_URI'];
    $req = '?'.$_SERVER['QUERY_STRING'];
    $req_file = str_replace(__URL_PATH__.'/', '', $req_file);

    if ('' == $req_file) {
        header('Location:  '.__URL_PATH__.'/index.php');
        exit;
    }
}
