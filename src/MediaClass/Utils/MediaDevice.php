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
}
