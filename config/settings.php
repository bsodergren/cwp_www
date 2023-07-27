<?php
/**
 * CWP Media tool
 */

use CWP\Media\Media;

/**
 * CWP Media tool.
 */
$table          = $explorer->table('settings');
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
        ];
        if (!defined($u['definedName'])) {
            define($u['definedName'], $u['setting_value']);
        }
    }

    define('__SETTINGS__', $setting);
}
$const          = get_defined_constants(true);


unset($setting);
