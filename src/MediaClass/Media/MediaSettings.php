<?php
/**
 * CWP Media tool
 */

namespace CWP\Media;

use CWP\Media\Bootstrap;

/**
 * CWP Media tool.
 */
class MediaSettings
{
    public static function skipTrimmers($data)
    {
        $data = $data[0];
        $publication = $data['pub'];
        $nobindery = $data['nobindery'];

        if (1 == $nobindery) {
            return 1;
        }

        // foreach()

        if (str_contains(__PDF_NOTRIM__, $publication)) {
            return 1;
        }

        return 0;
    }

    public static function isFacetrim($data)
    {
        $publication = $data['pub'];
        $facetrim = $data['facetrim'];

        if (1 == $facetrim) {
            return 1;
        }
        // foreach()
        if (null === $facetrim) {
            if (str_contains(__PUB_FACETRIM__, $publication)) {
                return 1;
            }
        }

        return 0;
    }

    public static function isTrue($define_name)
    {
        if (defined($define_name)) {
            if (true == constant($define_name)) {
                //  MediaUpdate::echo(constant($define_name));
                return 1;
            }
        }

        return 0;
    }

    public static function isSet($define_name)
    {
        if (defined($define_name)) {
            return 1;
        }

        return 0;
    }

    public static function jsonString_to_TextForm($string)
    {
        $value_text = '';
        $value_array = json_decode($string, 1);

        if (is_array($value_array)) {
            foreach ($value_array as $text => $link) {
                if (is_array($link)) {
                    $value_text .= $text." => [,\n";
                    foreach ($link as $text2 => $link2) {
                        $value_text .= "\t $text2 => $link2,\n";
                    }
                    $value_text .= "],\n";
                    continue;
                }
                if (str_contains($string, '{')) {
                    $value_text .= "$text => $link,\n";
                } else {
                    $value_text .= "$link\n";
                }
            }
        }

        return trim($value_text);
    }

    public static function save_post_asJson($setting_str)
    {
        if (str_contains($setting_str, '=>')) {
            $arr = explode(',', $setting_str);
            $arr2 = null;
            $step = false;
            $nav_array = [];

            foreach ($arr as $k => $string) {
                if (str_contains($string, ']')) {
                    $step = false;
                    $dropdown_key = '';
                    continue;
                }

                if (str_contains($string, '=>')) {
                    list($v_key, $value) = explode('=>', $string);
                    $value = trim($value);
                    $v_key = trim($v_key);

                    if (str_contains($value, '[')) {
                        $step = true;
                        $dropdown_key = $v_key;
                        $nav_array[$dropdown_key] = [];
                        continue;
                    }

                    if (true == $step) {
                        $nav_array[$dropdown_key][$v_key] = $value;
                        continue;
                    }

                    $arr2[$v_key] = $value;
                }
            }
            if (null === $arr2) {
                $arr2 = $arr;
            }

            $array = array_merge($arr2, $nav_array);

            // $arr2['dropdown'] = $nav_array;
        } else {
            $array = explode("\n", $setting_str);
            $array = array_map('trim', $array);
        }

        return json_encode($array);
    }

    private static function setImapDefine($key, $config_key)
    {
        if (!key_exists('email', Bootstrap::$CONFIG)) {
            return false;
        }

        if (!key_exists($config_key, Bootstrap::$CONFIG['email'])) {
            return false;
        }

        if (!defined($key)) {
            if ('' != Bootstrap::$CONFIG['email'][$config_key]) {
                define($key, Bootstrap::$CONFIG['email'][$config_key]);
            }
        }
    }

    public static function configEmail($key)
    {
        switch ($key) {
            case '__SHOW_MAIL__':
            case '__IMAP_ENABLE__':
                self::setImapDefine($key, 'enable');
                break;
            case '__IMAP_USER__':
                self::setImapDefine($key, 'username');
                break;
            case '__IMAP_PASSWD__':
                self::setImapDefine($key, 'password');
                break;
            case '__IMAP_FOLDER__':
                self::setImapDefine($key, 'folder');
                break;
            case '__IMAP_HOST__':
                self::setImapDefine($key, 'imap');
                break;
        }
    }
}
