<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Update;

const DESCRIPTION = 'Updates metatags on files';
const NAME = 'update';

use CWPCLI\Utilities\Option;
use CWPCLI\Core\MediaCommand;
use CWPCLI\Commands\Update\Lang;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: NAME, description: DESCRIPTION)]
class Command extends MediaCommand
{
    use Lang;


    public $process;

    public function handleSignal(int $signal): void
    {
        if (\SIGINT === $signal) {
            echo \PHP_EOL;
            echo 'Exiting, cleaning up';
            echo \PHP_EOL;
       

            exit;
        }
    }
}
