<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Create;

const DESCRIPTION = 'Create jobs';
const NAME = 'create';

use CWPCLI\Utilities\Option;
use CWPCLI\Core\MediaCommand;
use CWPCLI\Commands\Create\Lang;
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
