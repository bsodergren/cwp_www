<?php
/**
 * CWP Media tool
 */

namespace CWP\Utils;

use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Device;
use Sinergi\BrowserDetector\Os;

class MediaDevice
{


    public static $DEVICE = "Unknown";

    public function __construct()
    {
        self::$DEVICE = $this->run();
    }


    public function run()
    {

        $device = new Device();
        $os = new Os();
        $browser = new Browser();

        /*
         *  windows desktop
         * Edge
         * Windows
         * unknown
         *
         * Media App
         * Chrome
         * Windows
         * Unknown
         *
         *
         * iPhone
         * Edge
         * iOS
         * iPhone
         *
         *
         */

        if ('Edge' == $browser->getName()) {
            if ('Windows' == $os->getName()) {
                return 'DESKTOP';
            } elseif ('iOS' == $os->getName()) {
                return  'MOBILE';
            }
        } elseif ('Chrome' == $browser->getName()) {
            if ('Windows' == $os->getName()) {
                return 'APPLICATION';
            }
        } elseif ('Safari' == $browser->getName()) {
            if ('iOS' == $os->getName()) {
                return 'MOBILE';
            }
        }

        return [$browser->getName(),$device->getName(),$os->getName()];
    }

    private static function getDevicePath()
    {
        $ClassName = ucfirst(strtolower(__DEVICE__));
        return 'CWP\\HTML\\'.$ClassName;

    }

    public static function getHeader($template='', $params=[])
    {
        $className = self::getDevicePath(). '\\Header';
        if (class_exists($className)) {
            return  $className::Display($template, $params );
        }
    }
    public static function getNavbar($template='', $params=[])
    {
        $className = self::getDevicePath(). '\\Navbar';
        if (class_exists($className)) {
           return  $className::Display($template, $params );
        }
    }
    public static function getFooter($template='', $params=[])
    {
        $className = self::getDevicePath(). '\\Footer';
        if (class_exists($className)) {
            return  $className::Display($template, $params );
        }
    }


}
