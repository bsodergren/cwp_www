<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Core;

use CWPCLI\Core\MediaCWP;
use CWPCLI\Utilities\Option;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class MediaStopWatch
{
    public static $clock = false;
    public static $DisplayEvent = '';

    public static $display = false;

    public static $writeNow = true;

    private static $stopwatch;

    private static $timerLog = __ERROR_LOG_DIRECTORY__.'/timer.log';

    private static $watchArray = [];
    private static $io;

    private static $stopWatchName = __SCRIPT_NAME__;

    public static function varexport($expression, $return = false)
    {
        $export = var_export($expression, true);
        $patterns = [
            '/array \\(/' => '[',
            '/^([ ]*)\\)(,?)$/m' => '$1]$2',
            "/=>[ ]?\n[ ]+\\[/" => '=> [',
            "/([ ]*)(\\'[^\\']+\\') => ([\\[\\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ((bool) $return) {
            return $export;
        }  echo $export;
    }

    private static function getName($name)
    {
        if (null === $name) {
            $name = self::$stopWatchName;
        }

        if (!self::$stopwatch->isStarted($name)) {
            self::$stopwatch->start($name);
        }

        return $name;
    }

    public static function init()
    {
        if (!\is_object(self::$stopwatch)) {
            $file = self::$timerLog;
            $string = '-----------------------'.__SCRIPT_NAME__.'-------------------------------------'.\PHP_EOL;
            if (\array_key_exists('APP_CMD', $_ENV)) {
                if (isset(MediaCWP::$input)) {
                    $input = MediaCWP::$input;
                    $output = MediaCWP::$output;
                    Option::init($input);

                    if (Option::isTrue('time')) {
                        self::$display = true;
                        $string = self::formatPrint(implode(' ', $_SERVER['argv']), ['green', 'italic']).\PHP_EOL;
                        self::$io = new SymfonyStyle($input, $output);
                    }
                }
            }
            file_put_contents($file, $string);
            self::$stopwatch = new StopWatch();
            self::start();
        }
    }

    public static function start($event = null)
    {
        $event = self::getName($event);
        self::$stopwatch->start($event);
    }

    public static function dump($text = '', $var = '', $event = null)
    {
        if (\is_object(self::$stopwatch)) {
            $event = self::getName($event);

            $indent = '';
            if ($event != self::$stopWatchName) {
                $indent = '  ';
            }
            self::$clock = (int) self::$stopwatch->getEvent($event)->getDuration();
            // $text = sprintf("%-20s",   $text);
            // $var = str_replace("\n"," ", var_export($var,1));
            $var = preg_replace('/(\s{1,})/m', ' ', var_export($var, 1));
            $cmd = MediaLogger::CallingFunctionName();
            //                $var = self::varexport($var,true);

            self::log([(string) (self::$clock / 1000), $cmd, $text, $var]);
        }
    }

    public static function stop($text = '', $var = '', $event = null)
    {
        if (\is_object(self::$stopwatch)) {
            $event = self::getName($event);
            self::$stopwatch->stop($event);
            self::dump($text, $var, $event);
            if (false === self::$writeNow) {
                self::$writeNow = true;
                self::log(self::$watchArray);
                self::$writeNow = false;
            }
        }
    }

    public static function lap($text, $var = '', $event = null)
    {
        if (\is_object(self::$stopwatch)) {
            $event = self::getName($event);
            self::$stopwatch->lap($event);
            self::dump($text, $var, $event);
        }
    }

    public static function log($array)
    {
        if (true == self::$display) {
            if (\array_key_exists('APP_CMD', $_ENV)) {
                if (isset(MediaCWP::$input)) {
                    $string[] = trim(implode(' ', $array));
                    self::$io->text($string[0]);
                }
            }
        } else {
            if (true === self::$writeNow) {
                self::writeLog([0 => $array]);
            } else {
                self::$watchArray[] = $array;
            }
        }
    }

    private static function writeLog($array)
    {
        $file = self::$timerLog;
        $maxtxtLen = 0;
        $maxTimeLen = 0;
        $maxCmdLen = 0;
        if (\count($array) > 0) {
            foreach ($array as $n => $row) {
                $len1 = \strlen($row[0]);
                if ($len1 > $maxTimeLen) {
                    $maxTimeLen = $len1;
                }

                $len2 = \strlen($row[1]);
                if ($len2 > $maxCmdLen) {
                    $maxCmdLen = $len2;
                }

                $len3 = \strlen($row[2]);
                if ($len3 > $maxtxtLen) {
                    $maxtxtLen = $len3;
                }
            }
            $lineArray = [];
            foreach ($array as $n => $row) {
                $lineArray[] = str_pad($row[0], $maxTimeLen);
                $lineArray[] = str_pad($row[1], $maxCmdLen);
                $lineArray[] = str_pad($row[2], $maxtxtLen);
                $lineArray[] = $row[3];

                $strArray[] = implode(', ', $lineArray); // $time.', '.$txt.', '.$var;
            }

            $string = implode(\PHP_EOL, $strArray).\PHP_EOL;
            $string = str_replace("''", '', $string);
            $string = str_replace('default/', '', $string);
            // $string = var_export($strArray,1);
            file_put_contents($file, $string, \FILE_APPEND);
        }
    }

    public static function flushLogs()
    {
        if (false === self::$writeNow) {
            self::writeLog(self::$watchArray);
        }
    }

    public static function formatPrint(string $text = '', array $format = [])
    {
        if (false == self::$display) {
            return $text;
        }

        $codes = [
            'bold' => 1,
            'italic' => 3, 'underline' => 4, 'strikethrough' => 9,
            'black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37,
            'blackbg' => 40, 'redbg' => 41, 'greenbg' => 42, 'yellowbg' => 44, 'bluebg' => 44, 'magentabg' => 45, 'cyanbg' => 46, 'lightgreybg' => 47,
        ];
        $formatMap = array_map(function ($v) use ($codes) {
            return $codes[$v];
        }, $format);

        return "\e[".implode(';', $formatMap).'m'.$text."\e[0m";
    }
}
