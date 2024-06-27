<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Show;

use CWPCLI\Traits\Translate;
use CWPCLI\Core\MediaOptions;
use CWPCLI\Commands\Show\Lang;
use Symfony\Component\Console\Input\InputOption;

class Options extends MediaOptions
{
    use Lang;
    use Translate;

    public function Definitions()
    {
        Translate::$Class = __CLASS__;

        return [
           
        ];
    }
}
