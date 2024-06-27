<?php
/**
 * Command like Metatag writer for video files.
 */

use CWPCLI\Core\MediaApplication;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

$cmdName = str_replace('media', '', __SCRIPT_NAME__);
$className = 'CWPCLI\\Commands\\'.ucfirst($cmdName).'\\Command';

// $customCommands = new FactoryCommandLoader([
//     $cmdName      => function () use($className) {return new $className(); },
// ]);

$application = new MediaApplication(__SCRIPT_NAME__, '1.0.0');
$application->add(new $className());
$application->setDefaultCommand($cmdName, true);
$application->run();
