<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Traits;

use CWPCLI\Utilities\Option;


trait CmdProcess
{
    public $default = [
        'exec' => null,
        'print' => null,
    ];

    public function runCommand()
    {
        $array = $this->commandList;

        $default = $this->default;
        if (isset($this->defaultCommands)) {
            $default = $this->defaultCommands;
        }

        foreach (Option::getOptions() as $option => $value) {
            if (\array_key_exists($option, $array)) {
                $cmd = $option;
                foreach ($array[$option] as $method => $args) {
                    if (null !== $args) {
                        if ('default' == $args) {
                            $default = [$method => null];
                            continue;
                        }
                        $commandArgs = option::getValue($cmd);

                        if (\is_array($commandArgs)) {
                            if (\array_key_exists(0, $commandArgs)) {
                                if ('isset' == $args) {
                                    $Commands[$method] = $commandArgs[0];

                                    continue;
                                }
                            }
                        }
                        $args = $commandArgs;
                    }
                    $Commands[$method] = $args; // => $value];
                    if ('default' == $method) {
                        unset($Commands[$method]);
                        $Commands = array_merge($Commands, $default);
                    }
                }
            }
        }

        if (!isset($Commands)) {
            $Commands = $default;
        }


        return $Commands;
    }
}
