<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Core;

use CWPCLI\Core\MediaOptions;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;

class MediaDoctrineCommand extends DoctrineCommand
{
    public function configure(): void
    {
        $this->setName(static::$defaultName)->setDescription(static::$defaultDescription);

        $definition = MediaOptions::get($this->getName());

        $this->setDefinition($definition);
    }
}
