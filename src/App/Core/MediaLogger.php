<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Core;

/*
 * CWP Media tool
 */

use CWP\HTML\Colors;
use Nette\IOException;
use Nette\Utils\DateTime;
use Nette\Database\Helpers;
use Nette\Utils\FileSystem;

class log
{
    public static function append(string $file, string $content, ?int $mode = 0666): void
    {
        FileSystem::createDir(\dirname($file));
        if (false === @file_put_contents($file, $content, \FILE_APPEND)) { // @ is escalated to exception
            throw new IOException(sprintf("Unable to write file '%s'. %s", FileSystem::normalizePath($file), Helpers::getLastError()));
        }

        if (null !== $mode && !@chmod($file, $mode)) { // @ is escalated to exception
            throw new IOException(sprintf("Unable to chmod file '%s' to mode %s. %s", FileSystem::normalizePath($file), decoct($mode), Helpers::getLastError()));
        }
    }
}

class MediaLogger
{
    public static function getErrorLogs()
    {
        $err_array = [];

        if ($all = opendir(__ERROR_LOG_DIRECTORY__)) {
            while ($file = readdir($all)) {
                if (!is_dir(__ERROR_LOG_DIRECTORY__.'/'.$file)) {
                    if (preg_match('/(log)$/', $file)) {
                        $err_array[] = filesystem::normalizePath(__ERROR_LOG_DIRECTORY__.'/'.$file);
                    } // end if
                } // end if
            } // end while
            closedir($all);
        } // end if

        return $err_array;
    }

    public static function log($text, $var = '', $logfile = 'default.log', $html = false)
    {
            $function_list = self::CallingFunctionName();

            $html_var = '';
            $html_string = '';
            $html_msg = '';
            $html_func = '';

            if (\is_array($var) || \is_object($var)) {
                $html_var = "\n".self::printCode($var);
            } else {
                $html_var = $var;
            }
            // $html_var = htmlentities($html_var);
            // $html_var = '<pre>' . $html_var . '</pre>';

            if (true == $html) {
                $html_string = json_encode([
                    'TIMESTAMP' => DateTime::from(null),
                    'FUNCTION' => $function_list,
                    'MSG_TEXT' => $text,
                    'MSG_VALUE' => $html_var,
                ]);
            } else {


                $html_var = str_replace('<br>', "\n\t", $html_var);
                $html_string = DateTime::from(null).':'. $function_list.":".$text.'; '.$html_var;

                $logfile = 'txt_'.$logfile;
            }

            $errorLogFile = __ERROR_LOG_DIRECTORY__.'/'.$logfile;

            Log::append($errorLogFile, $html_string."\n");

    }

    public static function echo($msg, $var = '', $indent = 0)
    {
        $color = new Colors();
        $msg = $color->getColoredSpan($msg, 'blue');
        if (\is_array($var)) {
            $var = '<pre>'.var_export($var, 1).'</pre>';
        }

        $var = $color->getColoredSpan($var, 'green');

        $string = '<div>';
        $string .= '<span class="mx-'.$indent.'">'.$msg.' '.$var.'</span></div>';

        echo $string;
        ob_flush();
    }

    public static function printCode($array, $path = false, $top = true)
    {
        $data = '';
        $delimiter = '~~|~~';

        $p = null;
        if (\is_array($array)) {
            foreach ($array as $key => $a) {
                if (!\is_array($a) || empty($a)) {
                    if (\is_array($a)) {
                        $data .= $path."['{$key}'] = array();".$delimiter;
                    } else {
                        $data .= $path."['{$key}'] = \"".htmlentities(addslashes($a)).'";'.$delimiter;
                    }
                } else {
                    $data .= self::printCode($a, $path."['{$key}']", false);
                }
            }
        }

        if ($top) {
            $return = '';
            foreach (explode($delimiter, $data) as $value) {
                if (!empty($value)) {
                    $return .= '$array'.$value.'<br>';
                }
            }

            return $return;
        }

        return $data;
    }

    public $traceStripPrefix = 'ore';

    private static $padding = [
        'file' => 20,
        'class' => 22,
        'function' => 16,
        'line' => 4,
    ];

    private static $color = [
        'file' => ['red'],
        'class' => ['yellow'],
        'function' => ['blue'],
        'line' => ['green'],
    ];

    public static function print_array($array, $die = 0)
    {
        print_r($array);
        if (1 == $die) {
            exit(\PHP_EOL);
        }
    }


    public static function CallingFunctionName()
    {
        $trace = debug_backtrace();
        $TraceList = '';

        // $class = str_pad('', self::$padding['class'], ' ');
        // $calledFile = str_pad('', self::$padding['file'], ' ');
        // $calledLine = str_pad('', self::$padding['line'], ' ');
        // $function = str_pad('', self::$padding['function'], ' ');
        foreach ($trace as $key => $row) {
            if (\array_key_exists('class', $row)) {
                if (str_contains($row['class'], 'MediaLogger')) {
                    if (str_contains($row['function'], 'log')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                        $TraceList = $calledFile.':'.$calledLine;
                    }
                    continue;
                }
                if (str_contains($row['class'], 'MediaStopWatch')) {
                    if (str_contains($row['function'], 'dump')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                    }
                    if (str_contains($row['function'], 'start')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                    }

                    if (str_contains($row['function'], 'lap')) {
                        $calledFile = self::returnTrace('file', $row);
                        $calledLine = self::returnTrace('line', $row);
                    }
                    $TraceList = $calledFile.':'.$calledLine;

                    continue;
                }
                if ('' != $row['class']) {
                    $class = self::returnTrace('class', $row);
                }
            }
            if (str_contains($row['function'], 'require')) {
                continue;
            }
            if ($row['function']) {
                $function = self::returnTrace('function', $row);
            }

            $TraceList = $calledFile.':'.$class.':'.$function.':'.$calledLine;
            break;
        }
        //  $TraceList = str_pad($TraceList, 100, '.');

        return $TraceList;
    }

    private static function getClassPath($class, $level = 1)
    {
        preg_match('/.*\\\\([A-Za-z]+)\\\\([A-Za-z]+)/', $class, $out);
        if (2 == $level) {
            return $out[1].'\\'.$out[2];
        }

        return $out[2];
    }

    private static function returnTrace($type, $row)
    {
        // return $row[$type];

        if ($row[$type]) {
            $text = $row[$type];

            if ('class' == $type) {
                $text = self::getClassPath($text, 2);
            }

            if ('file' == $type) {
                $text = basename($text);
            }

            // $text = substr($text, 0, self::$padding[$type]);
            // $text = str_pad($text, self::$padding[$type], ' ');

            return MediaStopWatch::formatPrint($text, self::$color[$type]);
        }

        return null;
    }


}
