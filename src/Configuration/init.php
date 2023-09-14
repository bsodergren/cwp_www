<?php
/**
 * CWP Media tool
 */

use CWP\AutoUpdate\AutoUpdate;
use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\Media;
use Monolog\Logger;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

$userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
$clientHints = ClientHints::factory($_SERVER); // client hints are optional

$dd = new DeviceDetector($userAgent, $clientHints);
$dd->parse();

if ($dd->isBot()) {
  // handle bots,spiders,crawlers,...
  $botInfo = $dd->getBot();
} else {
  $aa = [
    $dd->isSmartphone(),
$dd->isFeaturePhone(),
$dd->isTablet(),
$dd->isPhablet(),
$dd->isConsole(),
$dd->isPortableMediaPlayer(),
$dd->isCarBrowser(),
$dd->isTV(),
$dd->isSmartDisplay(),
$dd->isSmartSpeaker(),
$dd->isCamera(),
$dd->isWearable(),
$dd->isPeripheral(),
    
    $dd->getClient() // holds information about browser, feed reader, media player, ...
  , $dd->getOs()
  ,$dd->getDeviceName()
  ,$dd->getBrandName()
  ,$dd->getModel()];
}

dd($aa);

$__test_nav_links = __PUBLIC_ROOT__.'/test_navlinks.php';

if (file_exists($__test_nav_links)) {
    require_once $__test_nav_links;
} else {
    define('__DEV_LINKS__', []);
}

$template = new Template();

if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job = Media::$connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media = new Media($job);
}
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

define('__UPDATE_CURRENT_VER__', trim(file_get_contents(__UPDATE_CURRENT_FILE__)));
Media::$AutoUpdate = new AutoUpdate(__UPDATE_TMP_DIR__, __PUBLIC_ROOT__, 600);
Media::$AutoUpdate->setCurrentVersion(__UPDATE_CURRENT_VER__);
Media::$AutoUpdate->setUpdateUrl(__UPDATE_URL__);

$logger = new Logger('default');
$logger->pushHandler(new Monolog\Handler\StreamHandler(__UPDATE_LOG_FILE__));
Media::$AutoUpdate->setLogger($logger);

// Cache (optional but recommended)
$cache = new \CWP\Cache\File(__UPDATE_CACHE_DIR__);
Media::$AutoUpdate->setCache($cache, 3600);

new HTMLDisplay();
