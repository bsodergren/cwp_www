<?php
namespace CWP\Media;
/**
 * CWP Media tool
 */

use Nette\IOException;
use Nette\Utils\DateTime;
use Nette\Database\Helpers;
use Nette\Utils\FileSystem;
use CWP\Media\MediaSettings;

class log
{
    public static function append(string $file, string $content, ?int $mode = 0666): void
    {
        FileSystem::createDir(dirname($file));
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

    private static function get_caller_info()
    {
        $trace = debug_backtrace();

        $s     = '';
        $file  = $trace[2]['file'];
        foreach ($trace as $row) {
            $class = '';
            switch ($row['function']) {
                case __FUNCTION__:
                    break;
                case 'MediaLogger':
                    $lineno = $row['line'];
                    break;
                case 'log':
                    break;
                case 'require_once':
                    break;
                case 'include_once':
                    break;
                case 'require':
                    break;
                case 'include':
                    break;
                case '__construct':
                    break;
                case '__directory':
                    break;
                case '__filename':
                    break;

                default:
                    if ('' != $row['class']) {
                        $class = $row['class'].$row['type'];
                    }
                    $s      = $class.$row['function'].':'.$s;
                    $file   = $row['file'];
                    break;
            }
        }
        $file  = pathinfo($file, \PATHINFO_BASENAME);

        return $file.':'.$lineno.':'.$s;
    }

    public static function log($text, $var = '', $logfile = 'default.log', $html = true)
    {
        if (MediaSettings::isTrue('__SHOW_DEBUG_PANEL__')) {
            $function_list = self::get_caller_info();

            $html_var      = '';
            $html_string   = '';
            $html_msg      = '';
            $html_func     = '';

            if (is_array($var) || is_object($var)) {
                $html_var = self::printCode($var);
            } else {
                $html_var = $var;
            }
            // $html_var = htmlentities($html_var);
            // $html_var = '<pre>' . $html_var . '</pre>';

            if (true == $html) {
                $html_string = json_encode([
                    'TIMESTAMP' => DateTime::from(null),
                    'FUNCTION'  => $function_list,
                    'MSG_TEXT'  => $text,
                    'MSG_VALUE' => $html_var,
                ]);
            } else {
                $html_string = $text.' '.$html_var;
                $html_string = str_replace('<br>', "\n", $html_string);
                $logFile     = 'txt_'.$logFile;
            }

            $errorLogFile  = __ERROR_LOG_DIRECTORY__.'/'.$logfile;

            Log::append($errorLogFile, $html_string."\n");
        }
    }

    public static function echo($msg, $var = '', $indent = 0)
    {
        $color  = new Colors();
        $msg    = $color->getColoredSpan($msg, 'blue');
        if (is_array($var)) {
            $var = '<pre>'.var_export($var, 1).'</pre>';
        }

        $var    = $color->getColoredSpan($var, 'green');

        $string = '<div>';
        $string .= '<span class="mx-'.$indent.'">'.$msg.' '.$var.'</span></div>';

        echo $string;
        ob_flush();
    }

    public static function printCode($array, $path = false, $top = true)
    {
        $data      = '';
        $delimiter = '~~|~~';

        $p         = null;
        if (is_array($array)) {
            foreach ($array as $key => $a) {
                if (!is_array($a) || empty($a)) {
                    if (is_array($a)) {
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
}
