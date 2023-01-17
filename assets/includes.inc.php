<?php

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

require_once __INC_CLASS_DIR__ . "/Media/MediaUpdate.class.php";
//require_once __INC_CLASS_DIR__ . "/HTML/Display.class.php";


$include_array =  MediaUpdate::get_filelist(__INC_CLASS_DIR__, 'php', 1);

foreach ($include_array as $required_file) {
    require_once $required_file;
}