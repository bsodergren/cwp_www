<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Template\Template;
use CWP\Template\HTMLDocument;
use CWP\Updater\MediaAppUpdater;

define('NO_DB_CHECK', true);

define('__PROJECT_ROOT__', dirname(__FILE__, 4));
define('__PUBLIC_ROOT__', dirname(__FILE__, 3));
define('__HTTP_ROOT__', dirname(__FILE__, 2));

require __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'bootstrap.php';

$appUpdate = new MediaAppUpdater();

$update = $appUpdate->isUpdate();
if($update != '') {
    return Template::GetHTML('base/header/updates', ['VERSION_UPDATES' => $update]);
}
echo '';
