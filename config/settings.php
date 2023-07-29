<?php
/**
 * CWP Media tool
 */

use CWP\Media\Media;
use CWP\Media\MediaSettings;

$table = Media::$explorer->table('settings');
$table->order('setting_type ASC');
$results = $table->fetchAssoc('id');

if ($results) {
    foreach ($results as $k => $u) {
        $setting[$u['setting_cat']][$u['definedName']] = [
            'id' => $u['id'],
            'type' => $u['setting_type'],
            'value' => $u['setting_value'],
            'name' => $u['setting_name'],
            'description' => $u['setting_description'],
            'category' => $u['setting_cat'],
            'require' => $u['require'],
        ];
    }
    define('__SETTINGS__', $setting);
}

foreach ($setting['local'] as $key => $array) {
    if (null !== $array['require']) {
        if (key_exists($array['require'], $setting['local'])) {
            if (true == $setting['local'][$array['require']]['value']
            ) {
                $setting['local'][$key]['value'] = $array['value'];
            } else {
                $setting['local'][$key]['value'] = 0;
            }
        }
    }
}

foreach ($setting['lang'] as $key => $array) {
    if (!defined($key)) {
        define($key, $array['value']);
    }
}

foreach ($setting['local'] as $key => $array)
{
    MediaSettings::configEmail($key);
    if (!defined($key)) {
        define($key, $array['value']);
    }
}

// $const = get_defined_constants(true);

unset($setting);
