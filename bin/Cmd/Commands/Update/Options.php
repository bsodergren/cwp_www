<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Update;

use CWPCLI\Traits\Translate;
use CWPCLI\Core\MediaOptions;
use CWPCLI\Commands\Update\Lang;
use Symfony\Component\Console\Input\InputOption;

class Options extends MediaOptions
{
    use Lang;
    use Translate;

    public function Definitions()
    {
        Translate::$Class = __CLASS__;

        return [
            ['list', 'l', InputOption::VALUE_NONE, Translate::text('L__UPDATE_APPROVE_CHANGES')],
            // ['list', 'l', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, Translate::text('L__UPDATE_LIST_CHANGES'), [], ['file']],
            ['update', 'U', InputOption::VALUE_NONE, Translate::text('L__UPDATE_ALL_TAGS')],
            ['break'],
        ];
    }
}
