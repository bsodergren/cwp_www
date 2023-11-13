<?php
/**
 * Command like Metatag writer for video files.
 */

use CWP\Core\Media;
use CWP\Template\Template;
use CWP\Utils\Utils;

/**
 * CWP Media tool.
 */

require_once '../.config.inc.php';
define('TITLE', 'Media Settings');
// $template = new Template();
use CWP\Core\MediaSettings;
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();

$cat = 'server';
if (isset($_GET['cat'])) {
    $cat = $_GET['cat'];
}

$table = Media::$explorer->table('settings');
$table->select('setting_group');
$table->group('setting_group');
foreach ($table as $id => $row) {
    if (null !== $row->setting_group) {
        $setting_groups[] = $row->setting_group;
    }
}

$table = Media::$explorer->table('settings');
$table->where('setting_cat', $cat);
$table->order('setting_group ASC');
// $table->group('setting_group');
// $results = $table->fetchall();

foreach ($table as $id => $row) {
    $group = '';
    if (null === $row->setting_group) {
        $group = 'A';
    } else {
        $group = $row->setting_group;
    }

    $settings[$group][$row->definedName]['id'] = $row->id;
    $settings[$group][$row->definedName]['definedName'] = $row->definedName;
    $settings[$group][$row->definedName]['value'] = $row->setting_value;
    $settings[$group][$row->definedName]['type'] = $row->setting_type;
    $settings[$group][$row->definedName]['name'] = $row->setting_name;
    $settings[$group][$row->definedName]['description'] = $row->setting_description;
    $settings[$group][$row->definedName]['cat'] = $row->setting_cat;
    $settings[$group][$row->definedName]['require'] = $row->require;
    $settings[$group][$row->definedName]['group'] = $group;
}

foreach ($settings as $setting_group => $setting) {
    $settings_html = '';
    $checkbox_html = '';
    $array_html = '';
    $textbox_html = '';
    $list_html = '';

    $checkbox_fields = '';
    $text_fields = '';
    $array_fields = '';
    $list_fields = '';
    // $setting_group_html='';
    $group_name_html = '';
    if ('' != $setting_group) {
        $group_name_html = $setting_group;
    }
    foreach ($setting as $definedName => $array) {
        $value_text = '';
        $params = [];
        $name_label = '';
        $checked = '';
        $notchecked = '';
        $description_label = '';
        $text_settings_html = '';
        $id = $array['id'];
        $type = $array['type'];
        $value = $array['value'];
        $name = $array['name'];
        $description = $array['description'];
        $tooltip_desc = $definedName.' '.$description;
        $setting_group_option_list_html = '';
        $selected = '';
        foreach ($setting_groups as $group_name) {
            if ('A' == $group_name) {
                $group_name = '';
            }
            if ($group_name == $array['group']) {
                $selected = $group_name;
                continue;
            }

            $setting_group_option_list_html .= template::getHtml('testsettings/select/option', [
                'OPTION_VALUE' => $group_name,
                'OPTION_TEXT' => $group_name,
            ]);
        }

        $setting_group_select_list_html = template::getHtml('testsettings/select/select',
            ['SELECT_OPTIONS' => $setting_group_option_list_html,
                'SELECTED' => $selected,
                'SELECT_NAME' => $definedName.'-group']);

        // if (null == $name) {
        //     $name = $definedName;
        //     $text_template = 'name_textbox';
        //     $row_name_params = ['DESCRIPTION' => $definedName, 'DEFINED_NAME' => $definedName];
        // } else {
        //     $text_template = 'name_label';
        //     $row_name_params = ['NAME' => $name, 'DEFINED_NAME' => $definedName];
        // }

        // if (null == $description) {
        //     $desc_template = 'description_textbox';
        //     $row_desc_params = ['DESCRIPTION' => $description, 'DEFINED_NAME' => $definedName];
        // } else {
        //     $desc_template = 'description_label';
        //     $row_desc_params = ['DESCRIPTION' => $description, 'DEFINED_NAME' => $definedName];
        // }

        if ('bool' == $type) {
            // $name_label = Template::GetHTML('testsettings/checkbox/'.$text_template, $row_name_params);

            // $description_label = Template::GetHTML('testsettings/checkbox/'.$desc_template, $row_desc_params);

            if (1 == $value) {
                $checked = 'checked';
            } else {
                $notchecked = 'checked';
            }

            $params = [
                'SETTING_GROUP' => $setting_group_select_list_html,
                'DEFINED_NAME' => $definedName,
                'CHECKED' => $checked,
                'NAME' => $name,
                'NAME_LABEL' => $name_label,
                'DESCRIPTION_LABEL' => $description_label,
                'COL_ONE' => $col_w_one,
                'COL_TWO' => $col_w_two,
                'COL_THREE' => $col_w_three,
                'COL_FOUR' => $col_w_four,
            ];
            $checkbox_fields .= Template::GetHTML('testsettings/checkbox/checkbox', $params);
        }

        if ('text' == $type) {
            $place_holder = $value;
            if ('' == $value) {
                $place_holder = 'no value set';
            }

            // $name_label = Template::GetHTML('testsettings/text/'.$text_template, $row_name_params);

            // $description_label = Template::GetHTML('testsettings/text/'.$desc_template, $row_desc_params);

            $params = [
                'DEFINED_NAME' => $definedName,
                'PLACEHOLDER' => $place_holder,
                'VALUE' => $value,
                'NAME' => $name,
                'NAME_LABEL' => $name_label,
                'DESCRIPTION_LABEL' => $description_label,
                'COL_ONE' => $col_w_one,
                'COL_TWO' => $col_w_two,
                'COL_THREE' => $col_w_three,
                'COL_FOUR' => $col_w_four,
            ];
            $text_fields = Template::GetHTML('testsettings/text/text', $params);
            $text_settings_html .= Template::GetHTML('testsettings/setting', ['FORM_HTML' => $text_fields,
                'SETTING_NAME' => $definedName,
                'SETTING_LABEL' => $description,
                'GROUP_SELECT' => $setting_group_select_list_html,
            ]);
        }

        if ('array' == $type) {
            // $name_label = Template::GetHTML('testsettings/array/'.$text_template, $row_name_params);

            // $description_label = Template::GetHTML('testsettings/array/'.$desc_template, $row_desc_params);

            $value_text = MediaSettings::jsonString_to_TextForm($value);

            $params = [
                'SETTING_GROUP' => $setting_group_select_list_html,
                'DEFINED_NAME' => $definedName.'-array',
                'VALUE' => $value_text,
                'NAME' => $name,
                'NAME_LABEL' => $name_label,
                'DESCRIPTION_LABEL' => $description_label,
            ];

            $array_fields .= Template::GetHTML('testsettings/array/array', $params);
        }

        if ('list' == $type) {
            $select_options = '';
            $selected_options = '';
            $pub_list = [];
            $options_group = '';

            // $name_label = Template::GetHTML('testsettings/list/'.$text_template, $row_name_params);

            // $description_label = Template::GetHTML('testsettings/list/'.$desc_template, $row_desc_params);

            $table = $explorer->table('pub_trim');
            $res = $table->order('bind ASC, pub_name ASC');

            $valueArray = explode(',', $value);

            foreach ($table->fetchall() as $row => $val) {
                if (in_array($val->id, $valueArray)) {
                    $pub_list['selected'][$val->bind][] = ['id' => $val->id, 'name' => $val->pub_name];
                    continue;
                }
                $pub_list['not_selected'][$val->bind][] = ['id' => $val->id, 'name' => $val->pub_name];
            }

            $checked = 'checked';
            foreach ($pub_list['selected'] as $bind => $pubArray) {
                $bind_name = Utils::bindtype($bind);
                foreach ($pubArray as $pub) {
                    $pub_name = ucwords(str_replace('_', ' ', $pub['name']));
                    $select_options .= Template::GetHTML('testsettings/list/select_options', [
                        'OPTION_VALUE' => $pub['id'],
                        'OPTION_ID' => 'flexCheckDefault-'.$definedName.$pub['id'],
                        'OPTION_NAME' => $definedName.'-list',
                        'OPTION_TEXT' => $pub_name,
                        'OPTION_SELECTED' => $checked]);
                }
            }
            if ('' != $select_options) {
                $options_group .= Template::GetHTML('testsettings/list/option_group', ['SELECT_OPTIONS' => $select_options,
                    'SELECT_LABEL' => 'Selected Publications']);
            }
            $checked = '';

            $select_options = '';
            foreach ($pub_list['not_selected'] as $bind => $pubArray) {
                $bind_name = utils::bindtype($bind);
                $select_options = '';

                foreach ($pubArray as $pub) {
                    $pub_name = ucwords(str_replace('_', ' ', $pub['name']));
                    $select_options .= Template::GetHTML('testsettings/list/select_options', [
                        'OPTION_VALUE' => $pub['id'],
                        'OPTION_ID' => 'flexCheckDefault-'.$definedName.$pub['id'],
                        'OPTION_NAME' => $definedName.'-list',
                        'OPTION_TEXT' => $pub_name,
                        'OPTION_SELECTED' => $checked]);
                }
                $options_group .= Template::GetHTML('testsettings/list/option_group', ['SELECT_OPTIONS' => $select_options,
                    'SELECT_LABEL' => $bind_name]);
            }

            $params = [
                'SETTING_GROUP' => $setting_group_select_list_html,
                'DEFINED_NAME' => $definedName.'-list',
                'SELECT_OPTIONS' => $options_group,
                'NAME' => $name,
                'NAME_LABEL' => $name_label,
                'DESCRIPTION_LABEL' => $description_label,
            ];

            $list_fields .= Template::GetHTML('testsettings/list/list', $params);
        }
    }

    // if ('' != $checkbox_fields) {
    //     $checkbox_html = Template::GetHTML('testsettings/checkbox/main', ['CHECKBOX_FIELDS' => $checkbox_fields,
    //     'COL_ONE' => $col_w_one,
    //     'COL_TWO' => $col_w_two,
    //     'COL_THREE' => $col_w_three,
    //     'COL_FOUR' => $col_w_four]);
    // }
    // if ('' != $text_settings_html) {
    //     $textbox_html = Template::GetHTML('testsettings/text/main', ['TEXTBOX_FIELDS' => $text_fields,
    //     'SETTINGS_COL_WIDTH' => $text_col_width,
    //     'COL_ONE' => $col_w_one,
    //     'COL_TWO' => $col_w_two,
    //     'COL_THREE' => $col_w_three,
    //     'COL_FOUR' => $col_w_four
    // ]);
    // }
    // if ('' != $array_fields) {
    //     $array_html = Template::GetHTML('testsettings/array/main', ['ARRAY_FIELDS' => $array_fields,

    //     'COL_ONE' => $col_w_one,
    //     'COL_TWO' => $col_w_two,
    //     'COL_THREE' => $col_w_three,
    //     'COL_FOUR' => $col_w_four]);
    // }
    // if ('' != $list_fields) {
    //     $list_html = Template::GetHTML('testsettings/list/main', ['LIST_FIELDS' => $list_fields,

    //     'COL_ONE' => $col_w_one,
    //     'COL_TWO' => $col_w_two,
    //     'COL_THREE' => $col_w_three,
    //     'COL_FOUR' => $col_w_four]);
    // }

    // $template->template('testsettings/new_setting', ['CATEGORY' => $cat]);

    $setting_group_html .= Template::GetHTML('testsettings/group', [
        'GROUP_NAME' => $group_name_html,
        // 'CHECKBOX_HTML' => $checkbox_html,
        'TEXTBOX_HTML' => $text_settings_html,
        // 'ARRAY_HTML' => $array_html,
        // 'LIST_HTML' => $list_html,
    ]);
}

$settings_html = $template->return();
$template->clear();
$template->template('testsettings/main', [
    'SETTING_GROUPS' => $setting_group_html,
    'SETTINGS_HTML' => $settings_html,
    'CATEGORY' => $cat]);

$template->render();
MediaDevice::getFooter();
