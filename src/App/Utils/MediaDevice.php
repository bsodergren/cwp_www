<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Utils;

use CWP\Browser\Browser;
use CWP\Browser\Device;
use CWP\Browser\Os;
use Nette\Utils\FileSystem;

class MediaDevice
{
    public static $DEVICE = 'APPLICATION';

    public static $default_theme = 'application';

    public static $NAVBAR = true;
    public static $USEJAVASCRIPT = false;

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
        //return 'APPLICATION';
        if ('Edge' == $browser->getName()) {
            if ('Windows' == $os->getName()) {
                return 'DESKTOP';
            } elseif ('iOS' == $os->getName()) {
                return 'MOBILE';
            } elseif ('unknown' == $device->getName()) {
                return 'DESKTOP';
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

        return 'APPLICATION';
        // return [$browser->getName(), $device->getName(), $os->getName()];
    }

    private static function getDevicePath()
    {
        $ClassName = ucfirst(strtolower(self::$DEVICE));

        return 'CWP\\Template\\'.$ClassName;
    }

    public static function getHeader($template = '', $params = [])
    {
        $className = self::getDevicePath().'\\Header';

        if (class_exists($className)) {
            return $className::Display($template, $params);
        }
    }

    public static function getNavbar($template = '', $params = [])
    {
        $className = self::getDevicePath().'\\Navbar';
        if (class_exists($className)) {
            return $className::Display($template, $params);
        }
    }

    public static function getFooter($template = '', $params = [])
    {
        $className = self::getDevicePath().'\\Footer';
        if (class_exists($className)) {
            return $className::Display($template, $params);
        }
    }

    public static function getAssetURL($type, $files)
    {
        $html = null;

        foreach ($files as $file) {
            $filePath = self::getThemepath(__LAYOUT_URL_PATH__).DIRECTORY_SEPARATOR.$file;
            $url = __URL_LAYOUT__.'/'.strtolower(self::$DEVICE).'/'.$file;
            if (!file_exists($filePath)) {
                $filePath = self::getDefaultTheme(__LAYOUT_URL_PATH__).DIRECTORY_SEPARATOR.$file;
                $url = __URL_LAYOUT__.'/'.strtolower(self::$default_theme).'/'.$file;
                if (!file_exists($filePath)) {
                    $url = null;
                    dd($filePath);
                }
            }
            if (null !== $url) {
                $url = $url.'?'.random_int(100000, 999999);
                switch ($type) {
                    case 'image':
                        $html .= $url;
                        break;
                    case 'css':
                        $html .= '<link rel="stylesheet" href="'.$url.'">'.\PHP_EOL;
                        break;
                    case 'js':
                        $html .= '<script src="'.$url.'" crossorigin="anonymous"></script>'.\PHP_EOL;
                        break;
                }
            }
        }

        return $html;
    }

    public static function getThemePath($dir = __THEME_DIR__)
    {
        // dd(self::$DEVICE);
        return $dir.'/'.strtolower(self::$DEVICE);
    }

    public static function getDefaultTheme($dir = __THEME_DIR__)
    {
        return $dir.'/'.strtolower(self::$default_theme);
    }

    public static function getTemplateFile($template, $js = false)
    {
        $extension = '.html';
        if (true === $js) {
            $extension = '.js';
        }

        $template = str_replace($extension, '', $template);

        $template_file = self::getThemePath().DIRECTORY_SEPARATOR.$template.$extension;
        $template_file = FileSystem::platformSlashes($template_file);
        $template_file = FileSystem::normalizePath($template_file);

        if (!file_exists($template_file)) {
            $template_file = self::getDefaultTheme().DIRECTORY_SEPARATOR.$template.$extension;
            $template_file = FileSystem::platformSlashes($template_file);
            $template_file = FileSystem::normalizePath($template_file);

            if (!file_exists($template_file)) {
                $template_file = null;
            }
        }

        return $template_file;
    }
}
