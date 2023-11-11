<?php
namespace CWP\Core;



use Symfony\Component\Stopwatch\Stopwatch;

class MediaStopWatch
{
    private static $stopwatch;

    public static $stopWatchName = 'StopWatch';

    public function __construct($name)
    {
        self::$stopwatch = new StopWatch();
        self::$stopwatch->start(self::$stopWatchName);
    }

}