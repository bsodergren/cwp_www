<?php
/**
 * CWP Media Load Flag Creator

 */

 phpinfo();
 die();
use CWP\Core\Media;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Core\MediaStopWatch;

require_once '.config.inc.php';

// dd($localFile, $RemoteFile);

MediaDevice::getHeader();



$table = $explorer->table('media_job');
$results = $table->fetchAssoc('job_id');
$cnt = $table->count('*');
MediaStopWatch::stop();
$name = "Stuff";
$value = [1,2,3,4,5];

if (Media::$Stash->has($name) == false) {
    Media::$Stash->put($name, $value, 1);
    dump("Didnt exist");
}

dump(["get",Media::$Stash->get($name)]);

MediaDevice::getFooter();
