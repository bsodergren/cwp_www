<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Create;

use CWPCLI\Traits\Translate;
use CWPCLI\Core\MediaOptions;
use CWPCLI\Commands\Create\Lang;
use Symfony\Component\Console\Input\InputOption;

class Options extends MediaOptions
{
    use Lang;
    use Translate;

    public function Definitions()
    {
        Translate::$Class = __CLASS__;

        return [
            ['time', null, InputOption::VALUE_NONE, Translate::text('L__DEFAULT_TEST_TIME')],

            ['jobId', 'j',  InputOption::VALUE_REQUIRED, Translate::text('L__MAP_LANG')],
            ['form_number', 'f',  InputOption::VALUE_REQUIRED, Translate::text('L__MAP_FORM')],

        ];
    }
}
