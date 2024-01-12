<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Template\Template;

use CWP\Utils\MediaDevice;

use CWP\Template\HTMLDocument;

use CWP\Updater\MediaAppUpdater;

define('NO_DB_CHECK', true);

define('__PROJECT_ROOT__', dirname(__FILE__, 4));
define('__PUBLIC_ROOT__', dirname(__FILE__, 3));
define('__HTTP_ROOT__', dirname(__FILE__, 2));
define('__LAYOUT_DIR__', \DIRECTORY_SEPARATOR.'assets');
define('__LAYOUT_ROOT__', __HTTP_ROOT__.__LAYOUT_DIR__);
define('__THEME_DIR__', __LAYOUT_ROOT__.\DIRECTORY_SEPARATOR.'themes');
require __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'bootstrap.php';

(new MediaDevice())->run();
define('__DEVICE__', MediaDevice::$DEVICE);

$appUpdate = new MediaAppUpdater();

$update = $appUpdate->isUpdate();
if($update != '') {
    echo Template::GetHTML('base/header/updates', ['VERSION_UPDATES' => $update]);
    exit;
}

echo '';
