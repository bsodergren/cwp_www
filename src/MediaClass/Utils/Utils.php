<?php
/**
 * CWP Media tool
 */

namespace CWP\Utils;

use Lamansky\Fraction\Fraction;
use Nette\Utils\FileSystem;

/**
 * CWP Media tool.
 */
class Utils
{
    public static function get_filelist($directory, $ext = 'log', $basename = false)
    {
        $files_array = [];

        if (is_dir($directory)) {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
            foreach ($rii as $file) {
                if ($file->isDir()) {
                    continue;
                }
                $filename = $file->getPathname();
                $filename = FileSystem::normalizePath($filename);
                if (preg_match('/('.$ext.')$/', $filename)) {
                    if (true == $basename) {
                        $files_array[] = basename($filename);
                    } else {
                        $files_array[] = $filename;
                    }
                }
            }
        }

        return $files_array;
    }

    public static function skipFile($filename)
    {
        $f = fopen($filename, 'r');
        $line = fgets($f);
        fclose($f);

        return strpos($line, '#skip');
    }

    public static function setSkipFile($filename)
    {
        if (!self::skipFile($filename)) {
            $replacement = '<?php';
            $replacement .= ' #skip';
            $__db_string = FileSystem::read($filename);
            $__db_write = str_replace('<?php', $replacement, $__db_string);
            FileSystem::write($filename, $__db_write);
        }
    }

    public static function toint($string)
    {
        $string_ret = str_replace(',', '', $string);

        return $string_ret;
    }

    public static function bindtype($bind)
    {
        $bind_name = str_replace(
            ['pfl', 'pfm', 'pfs', 'shs', 'phl', 'phm', 'pfs'],
            [
                'Perfect Foot Large',
                'Perfect Foot Medium',
                'Perfect Foot Small',
                'Saddle Head Small',
                'Perfect Head Large',
                'Perfect Head Medium',
                'Perfect Head Small'], $bind);

        return $bind_name;
    }

    public static function fracToFloat($number)
    {
        $float = '000';
        $digits = '0';

        if (0 != $number) {
            preg_match('/([0-9]+-?)?([0-9]+)?\/?([0-9]+)?/', $number, $output_array);
            $float = '000';
            $digits = $output_array[1];
            $digits = str_replace('-', '', $digits);
            if (array_key_exists(2, $output_array)) {
                if ('' == $output_array[2]) {
                    $digits = 0;
                    $output_array[2] = $output_array[1];
                }

                $num = $output_array[2];
                $den = $output_array[3];
                $float = fdiv($num, $den);
                $float = str_replace('0.', '', $float);
                $float = str_pad($float, 3, '0', \STR_PAD_RIGHT);
            }
        }

        return $digits.'.'.$float;
    }

    public static function floatToFrac($f)
    {
        $f = floatval($f);

        // keep the original sign so that the numerator could be converted later
        $is_negative = ($f < 0);
        if ($is_negative) {
            $f *= -1;
        }

        // get the part before the floating point
        $int = floor($f);

        // make the float belonging to the interval [0, 1)
        $flt = $f - $int;
        // strip the zero and the floating point
        $flt = substr($flt, 2);
        if ('' == $flt) {
            $flt = 0;
        }
        do {
            $len = strlen($flt);

            $val = $int * pow(10, $len) + $flt;
            $flt = substr($flt, 0, -1);
        } while ($val > intval($val));

        if ($is_negative) {
            $val *= -1;
        }

        $num = intval($val);
        $den = pow(10, $len);
        $f = new Fraction($num, $den);
        $string = str_replace(' ', '-', $f->toString());

        return $string;
    }

    public static function DelSizeToFrac($dec)
    {
        [$height,$width] = explode(' x ', $dec);
        $size_height = self::floattofrac($height);
        $size_width = self::floattofrac($width);

        return $size_height.' x '.$size_width;
    }

    public static function DelSizeToFloat($frac)
    {
        [$height,$width] = explode(' x ', $frac);
        $size_height = self::fracToFloat($height);
        $size_width = self::fracToFloat($width);

        return $size_height.' x '.$size_width;
    }
}
