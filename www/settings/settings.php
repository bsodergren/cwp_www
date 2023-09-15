<?php
use CWP\Template\Template;
use CWP\Core\MediaSettings;
use CWP\Utils\Utils;
/**
 * CWP Media tool
 */

require_once '../.config.inc.php';
define('TITLE', 'Media Settings');
// $template = new Template();
require __LAYOUT_HEADER__;

$settings_array = [];
$settings_html  = '';
$checkbox_html  = '';
$array_html     = '';
$textbox_html   = '';

$cat            = 'server';
$text_col_width = 'col-2';
if (isset($_GET['cat'])) {
    $cat = $_GET['cat'];
}

if ('lang' == $cat) {
    $text_col_width = 'col-4';
}

foreach (__SETTINGS__[$cat] as $definedName => $array) {
    $value_text        = '';
    $params            = [];
    $name_label        = '';
    $checked           = '';
    $notchecked        = '';
    $description_label = '';

    $id                = $array['id'];
    $type              = $array['type'];
    $value             = $array['value'];
    $name              = $array['name'];
    $description       = $array['description'];
    $tooltip_desc      = $definedName.' '.$description;

    $tooltip           = 'data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"	data-bs-title="'.$tooltip_desc.'" ';

    if (null == $name) {
        $name            = $definedName;
        $text_template   = 'name_textbox';
        $row_name_params = ['DESCRIPTION' => $definedName, 'DEFINED_NAME' => $definedName];
    } else {
        $text_template   = 'name_label';
        $row_name_params = ['NAME' => $name, 'DEFINED_NAME' => $definedName];
    }

    if (null == $description) {
        $desc_template   = 'description_textbox';
        $row_desc_params = ['DESCRIPTION' => $description, 'DEFINED_NAME' => $definedName];
    } else {
        $desc_template   = 'description_label';
        $row_desc_params = ['DESCRIPTION' => $description, 'DEFINED_NAME' => $definedName];
    }

    if ('bool' == $type) {
        $name_label        = Template::GetHTML('settings/checkbox/'.$text_template, $row_name_params);

        $description_label = Template::GetHTML('settings/checkbox/'.$desc_template, $row_desc_params);

        if (1 == $value) {
            $checked = 'checked';
        } else {
            $notchecked = 'checked';
        }

        $params            = [
            'DEFINED_NAME'      => $definedName,
            'TOOLTIP'           => $tooltip,
            'CHECKED'           => $checked,
            'NAME'              => $name,
            'NAME_LABEL'        => $name_label,
            'DESCRIPTION_LABEL' => $description_label,
        ];
        $checkbox_fields .= Template::GetHTML('settings/checkbox/checkbox', $params);
    }

    if ('text' == $type) {
        $place_holder      = $value;
        if ('' == $value) {
            $place_holder = 'no value set';
        }

        $name_label        = Template::GetHTML('settings/text/'.$text_template, $row_name_params);

        $description_label = Template::GetHTML('settings/text/'.$desc_template, $row_desc_params);

        $params            = [
            'DEFINED_NAME'       => $definedName,
            'PLACEHOLDER'        => $place_holder,
            'TOOLTIP'            => $tooltip,
            'VALUE'              => $value,
            'NAME'               => $name,
            'NAME_LABEL'         => $name_label,
            'DESCRIPTION_LABEL'  => $description_label,
            'SETTINGS_COL_WIDTH' => $text_col_width,
        ];
        $text_fields .= Template::GetHTML('settings/text/text', $params);
    }

    if ('array' == $type) {
        $name_label        = Template::GetHTML('settings/array/'.$text_template, $row_name_params);

        $description_label = Template::GetHTML('settings/array/'.$desc_template, $row_desc_params);

        $value_text        = MediaSettings::jsonString_to_TextForm($value);

        $params            = [
            'DEFINED_NAME'      => $definedName.'-array',
            'TOOLTIP'           => $tooltip,
            'VALUE'             => $value_text,
            'NAME'              => $name,
            'NAME_LABEL'        => $name_label,
            'DESCRIPTION_LABEL' => $description_label,
        ];

        $array_fields .= Template::GetHTML('settings/array/array', $params);
    }

    if ('list' == $type) {
        $select_options    = '';
        $selected_options  = '';
        $pub_list          = [];
        $options_group     = '';

        $name_label        = Template::GetHTML('settings/list/'.$text_template, $row_name_params);

        $description_label = Template::GetHTML('settings/list/'.$desc_template, $row_desc_params);

        $table             = $explorer->table('pub_trim');
        $res               = $table->order('bind ASC, pub_name ASC');

        $valueArray        = explode(',', $value);

        foreach ($table->fetchall() as $row => $val) {
            if (in_array($val->id, $valueArray)) {
                $pub_list['selected'][$val->bind][] = ['id' => $val->id, 'name' => $val->pub_name];
                continue;
            }
            $pub_list['not_selected'][$val->bind][] = ['id' => $val->id, 'name' => $val->pub_name];
        }

        $checked           = 'checked';
        foreach ($pub_list['selected'] as $bind => $pubArray) {
            $bind_name = Utils::bindtype($bind);
            foreach ($pubArray as $pub) {
                $pub_name = ucwords(str_replace('_', ' ', $pub['name']));
                $select_options .= Template::GetHTML('settings/list/select_options', [
                    'OPTION_VALUE'    => $pub['id'],
                    'OPTION_ID'       => 'flexCheckDefault-'.$definedName.$pub['id'],
                    'OPTION_NAME'     => $definedName.'-list',
                    'OPTION_TEXT'     => $pub_name,
                    'OPTION_SELECTED' => $checked]);
            }
        }
        if ('' != $select_options) {
            $options_group .= Template::GetHTML('settings/list/option_group', ['SELECT_OPTIONS' => $select_options,
                'SELECT_LABEL'                                                                  => 'Selected Publications']);
        }
        $checked           = '';

        $select_options    = '';
        foreach ($pub_list['not_selected'] as $bind => $pubArray) {
            $bind_name      = utils::bindtype($bind);
            $select_options = '';

            foreach ($pubArray as $pub) {
                $pub_name = ucwords(str_replace('_', ' ', $pub['name']));
                $select_options .= Template::GetHTML('settings/list/select_options', [
                    'OPTION_VALUE'    => $pub['id'],
                    'OPTION_ID'       => 'flexCheckDefault-'.$definedName.$pub['id'],
                    'OPTION_NAME'     => $definedName.'-list',
                    'OPTION_TEXT'     => $pub_name,
                    'OPTION_SELECTED' => $checked]);
            }
            $options_group .= Template::GetHTML('settings/list/option_group', ['SELECT_OPTIONS' => $select_options,
                'SELECT_LABEL'                                                                  => $bind_name]);
        }

        $params            = [
            'DEFINED_NAME'      => $definedName.'-list',
            'TOOLTIP'           => $tooltip,
            'SELECT_OPTIONS'    => $options_group,
            'NAME'              => $name,
            'NAME_LABEL'        => $name_label,
            'DESCRIPTION_LABEL' => $description_label,
        ];

        $list_fields .= Template::GetHTML('settings/list/list', $params);
    }
}

if ('' != $checkbox_fields) {
    $checkbox_html = Template::GetHTML('settings/checkbox/main', ['CHECKBOX_FIELDS' => $checkbox_fields]);
}
if ('' != $text_fields) {
    $textbox_html = Template::GetHTML('settings/text/main', ['TEXTBOX_FIELDS' => $text_fields,
    'SETTINGS_COL_WIDTH'                                                      => $text_col_width,
]);
}
if ('' != $array_fields) {
    $array_html = Template::GetHTML('settings/array/main', ['ARRAY_FIELDS' => $array_fields]);
}
if ('' != $list_fields) {
    $list_html = Template::GetHTML('settings/list/main', ['LIST_FIELDS' => $list_fields]);
}

// $template->template('settings/new_setting', ['CATEGORY' => $cat]);

$settings_html  = $template->return();

$template->clear();

$template->template('settings/main', [
    'CHECKBOX_HTML' => $checkbox_html,
    'TEXTBOX_HTML'  => $textbox_html,
    'ARRAY_HTML'    => $array_html,
    'LIST_HTML'     => $list_html,

    'SETTINGS_HTML' => $settings_html,
    'DELETE_LOG'    => $delete_log,
    'CATEGORY'      => $cat,
]);

$template->render();
include_once __LAYOUT_FOOTER__;
