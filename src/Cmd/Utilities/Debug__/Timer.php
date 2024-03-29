<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWPCLI\Utilities\Debug;

use CWPCLI\Bundle\Monolog\MediaLog;

class Timer extends MediaStopWatch
{
    public static $logProc = false;

    public static function startwatch($input, $output, $options = [])
    {
        parent::init($input, $output);
        if (\array_key_exists('log', $options)) {
            parent::$writeNow = $options['log'];
        }
        if (\array_key_exists('display', $options)) {
            parent::$display = $options['display'];
        }
    }

    public static function watch($text = 'Watch Timer', $var = null)
    {
        $caller = Debug::CallingFunctionName();

        $text = str_pad($text, 18, ' ');
        $text = MediaLog::formatPrint($text, ['blue']);
        $logText = $caller.'::'.$text;
        parent::dump($logText, $var);
    }

    public static function startLap($text = 'lap', $_ = '')
    {
        $caller = Debug::CallingFunctionName();
        $text = trim(MediaLog::formatPrint($text, ['blue']));
        $logText = $caller.'::'.$text;
        parent::lap($logText, null);
    }

    public static function watchlap($text = 'lap', $var = null)
    {
        $text = trim(MediaLog::formatPrint($text, ['cyan']));
        $logText = "\t".$text;
        parent::lap($logText, $var);
    }

    public static function stopwatch($text = 'stop', $var = null)
    {
        parent::stop($text, $var);
    }
}
