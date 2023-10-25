<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Core\Bootstrap;
use CWP\Core\Media;
use CWP\Core\MediaSettings;

$table          = Media::$explorer->table('settings');
$table->order('setting_type ASC');
$results        = $table->fetchAssoc('id');

if ($results) {
    foreach ($results as $k => $u) {
        $setting[$u['setting_cat']][$u['definedName']] = [
            'id'          => $u['id'],
            'type'        => $u['setting_type'],
            'value'       => $u['setting_value'],
            'name'        => $u['setting_name'],
            'description' => $u['setting_description'],
            'category'    => $u['setting_cat'],
            'require'     => $u['require'],
        ];
    }
    define('__SETTINGS__', $setting);
}

foreach ($setting['local'] as $key => $array) {
    if (null !== $array['require']) {
        if (array_key_exists($array['require'], $setting['local'])) {
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
    if (! defined($key)) {
        define($key, $array['value']);
    }
}

foreach ($setting['local'] as $key => $array) {
    MediaSettings::configEmail($key);

    if (! defined($key)) {
        define($key, $array['value']);
    }
}

// $const = get_defined_constants(true);
Media::$Dropbox = __USE_DROPBOX__;

if (Media::$Dropbox) {
    define('__FILES_DIR__', '');
} else {
    if (array_key_exists('media_files', Bootstrap::$CONFIG['server'])) {
        if (true == Bootstrap::$CONFIG['server']['media_files']) {
            define('__FILES_DIR__', __HTTP_ROOT__.Bootstrap::$CONFIG['server']['media_files']);
        }
    }
}
unset($setting);
