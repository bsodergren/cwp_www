<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Core;

use CWPCLI\Locales\Lang;
use CWPCLI\Traits\Translate;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * MediaOptions.
 */
class MediaOptions
{
    use Lang;
    use Translate;


    public static $callingClass;

    public static $classObj;

    public function Arguments($varName = null, $description = null)
    {
        return null;
    }

    public function Definitions()
    {
        return null;
    }

    public static function getClassObject($command)
    {
        $command = ucfirst(strtolower($command));
        $command = str_replace('Db', 'DB', $command);
        //        $command = str_replace("Ph","PH",$command);

        // $className = $command.'\\Options';
        // $className = 'Mediatag\\Commands\\'.$className;

        $className = self::$callingClass;
        $className = str_replace('\\', '/', $className);
        $className = \dirname($className).'/Options';
        $className = str_replace('/', '\\', $className);

        if (class_exists($className)) {
            self::$classObj = new $className();
        }
    }

    /**
     * Method get.
     *
     * @param mixed|null $command
     */
    public static function getDefinition($command = null)
    {
        $testOptions = [];
        $metaOptions = [];
        $commandOptions = [];

        self::getClassObject($command);
        // if (\is_object(self::$classObj)) {
        //     if (isset(self::$classObj->options)) {
        //         foreach (self::$classObj->options as $option => $value) {
        //             if (\is_string($option)) {
        //                 if (false == $value) {
        //                     continue;
        //                 }

        //                 $value = $option;
        //             }
        //             switch ($value) {
        //                 case 'Default':
        //                     $commandOptions = self::getDefaultOptions();
        //                     break;

        //                 case 'Test':
        //                     $testOptions = self::getTestOptions();
        //                     break;

        //                 case 'Meta':
        //                     $metaOptions = self::getMetaOptions();
        //                     break;
        //             }
        //         }
        //         //  else {
        //         //     $method = $option.'Options';
        //         //     if (method_exists(self::$classObj, $method)) {
        //         //         $commandOptions = array_merge($commandOptions, self::$classObj->$method());
        //         //     }
        //         //      }
        //     }


        // }

            $definitions = self::$classObj->Definitions();

            if (\is_array($definitions)) {
                $commandOptions = array_merge(self::getOptions($definitions), $commandOptions);
            }

        //$commandOptions = array_merge($commandOptions, $metaOptions, $testOptions);

        return new InputDefinition($commandOptions);
    }

    public static function getArguments($varName = null, $description = null)
    {
        //    self::getClassObject();
        if (\is_object(self::$classObj)) {
            return self::$classObj->Arguments($varName, $description);
        }

        return null;
    }

    public static function getOptions($optionArray)
    {
        if (!\is_array($optionArray)) {
            return [];
        }

        $cnt = \count($optionArray);
        $commandOptions = [];
        $i = 0;
        $prev = '';

        foreach ($optionArray as $idx => $optionName) {
            ++$i;
            $breakText = '';
            if ('break' == $optionName[0]) {
                $key = $idx - 1;
                $prev[3] .= \PHP_EOL; // ."-------------".PHP_EOL;
                $commandOptions[$key] = new InputOption(...$prev);
                continue;
            }

            if ($i == $cnt) {
                $optionName[3] .= \PHP_EOL;
            }
            $prev = $optionName;
            $commandOptions[] = new InputOption(...$optionName);
        }

        return $commandOptions;
    }

}
