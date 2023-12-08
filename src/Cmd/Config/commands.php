<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Config;

use CWPCLI\Commands\Update\Command as UpdateCommand;
use CWPCLI\Commands\Show\Command as ShowCommand;
use CWPCLI\Commands\Create\Command as CreateCommand;

use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
// %%NEW_USE%%


return new FactoryCommandLoader([
    'update' => function () {return new UpdateCommand(); },
    'show' => function () {return new ShowCommand(); },
    'create' => function () {return new CreateCommand(); },

]);
