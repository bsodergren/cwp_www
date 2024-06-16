<?php
/**
 * CWP Media Load Flag Creator

 */

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Core\MediaStopWatch;

require_once '.config.inc.php';

// dd($localFile, $RemoteFile);

MediaDevice::getHeader();

define('__MYSQL_TRUNC_TABLES__', ['form_data','form_data_count','media_forms','media_imports','media_job']);

foreach (__MYSQL_TRUNC_TABLES__ as $table) {
    $result = Media::$connection->query('TRUNCATE ' . $table);
}

echo Elements::JavaRefresh(__URL_PATH__ . '/index.php', 0);
exit;
