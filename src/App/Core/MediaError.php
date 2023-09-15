<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Core;

use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

/**
 * CWP Media tool.
 */
class MediaError
{
    public static function msg($severity, $msg = '', $refresh = 5)
    {
        $url = '/index.php';
        $timeout = $refresh;

        if (\is_array($refresh)) {
            $timeout = 0;
            if (\array_key_exists('url', $refresh)) {
                $url = $refresh['url'];
            }

            if (\array_key_exists('timeout', $refresh)) {
                $timeout = $refresh['timeout'];
            }
        }

        if ('' != $msg) {
            MediaDevice::getHeader();
            Template::echo('error/'.$severity, ['MSG' => $msg]);
        }

        HTMLDisplay::javaRefresh($url, $timeout);
        exit;
    }
}
