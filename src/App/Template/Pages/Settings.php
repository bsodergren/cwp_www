<?php

namespace CWP\Template\Pages;

use CWP\Core\Media;
use CWP\Template\Template;
use CWP\Utils\Utils;

class Settings extends Template
{
    public function __construct($row)
    {
        $this->id = $row['id'];
        $this->type = $row['type'];
        $this->value = $row['value'];
        $this->name = $row['name'];
        $this->description = $row['description'];
        $this->definedName = $row['definedName'];

        $tooltip_desc = $this->definedName.' '.$this->description;

        if (null == $this->name) {
            $this->name = $this->definedName;
            $this->text_template = 'name_textbox';
            $this->row_name_params = ['DESCRIPTION' => $this->definedName, 'DEFINED_NAME' => $this->definedName];
        } else {
            $this->text_template = 'name_label';
            $this->row_name_params = ['NAME' => $this->name, 'DEFINED_NAME' => $this->definedName];
        }

        if (null == $this->description) {
            $this->desc_template = 'description_textbox';
            $this->row_desc_params = ['DESCRIPTION' => $this->description, 'DEFINED_NAME' => $this->definedName];
        } else {
            $this->desc_template = 'description_label';
            $this->row_desc_params = ['DESCRIPTION' => $this->description . ' '.$this->definedName, 'DEFINED_NAME' => $this->definedName];
        }

        $this->tooltip = 'data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"	data-bs-title="'.$tooltip_desc.'" ';
        if ('lang' == $row['cat']) {
            $this->col_w_one = 'col-sm-4';
            $this->col_w_two = 'col-sm-3';
            $this->col_w_three = 'col-sm-3';
        } else {
            $this->col_w_one = 'col-sm-2';
            $this->col_w_two = 'col-sm-4';
            $this->col_w_three = 'col-sm-6';
        }
    }

    public function getLabels()
    {
        if ('bool' == $this->type) {
            $type = 'checkbox';
        } else {
            $type = $this->type;
        }

        $this->name_label = Template::GetHTML('settings/'.$type.'/'.$this->text_template, $this->row_name_params);

        $this->description_label = Template::GetHTML('settings/'.$type.'/'.$this->desc_template, $this->row_desc_params);
    }

    public function settingsTemplate($template, $params)
    {
        $this->getLabels();

        $default = [
            'DEFINED_NAME' => $this->definedName,
            'TOOLTIP' => $this->tooltip,
            'NAME' => $this->name,
            'NAME_LABEL' => $this->name_label,
            'DESCRIPTION_LABEL' => $this->description_label,
            'COL_ONE' => $this->col_w_one,
            'COL_TWO' => $this->col_w_two,
            'COL_THREE' => $this->col_w_three,
        ];

        $parameters = array_merge($default, $params);

        return Template::GetHTML($template, $parameters);
    }

    public function Checkbox()
    {
        if (1 == $this->value) {
            $checked = 'checked';
        } else {
            $notchecked = 'checked';
        }

        $params = [
            'CHECKED' => $checked,
        ];

        return $this->settingsTemplate('settings/checkbox/checkbox', $params);
    }

    public function Text()
    {
        $place_holder = $this->value;
        if ('' == $this->value) {
            $place_holder = 'no value set';
        }

        $params = [
            'PLACEHOLDER' => $place_holder,
            'VALUE' => $this->value,
        ];

        return $this->settingsTemplate('settings/text/text', $params);
    }

    public function List()
    {
        $select_options = '';
        $selected_options = '';
        $pub_list = [];
        $options_group = '';

        $table = Media::$explorer->table('pub_trim'); // UPDATEME
        $res = $table->order('bind ASC, pub_name ASC');

        $valueArray = explode(',', $this->value);

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
                $select_options .= Template::GetHTML('settings/list/select_options', [
                    'OPTION_VALUE' => $pub['id'],
                    'OPTION_ID' => 'flexCheckDefault-'.$this->definedName.$pub['id'],
                    'OPTION_NAME' => $this->definedName.'-list',
                    'OPTION_TEXT' => $pub_name,
                    'OPTION_SELECTED' => $checked]);
            }
        }
        if ('' != $select_options) {
            $options_group .= Template::GetHTML('settings/list/option_group', ['SELECT_OPTIONS' => $select_options,
                'SELECT_LABEL' => 'Selected Publications']);
        }
        $checked = '';

        $select_options = '';
        foreach ($pub_list['not_selected'] as $bind => $pubArray) {
            $bind_name = Utils::bindtype($bind);
            $select_options = '';

            foreach ($pubArray as $pub) {
                $pub_name = ucwords(str_replace('_', ' ', $pub['name']));
                $select_options .= Template::GetHTML('settings/list/select_options', [
                    'OPTION_VALUE' => $pub['id'],
                    'OPTION_ID' => 'flexCheckDefault-'.$this->definedName.$pub['id'],
                    'OPTION_NAME' => $this->definedName.'-list',
                    'OPTION_TEXT' => $pub_name,
                    'OPTION_SELECTED' => $checked]);
            }
            $options_group .= Template::GetHTML('settings/list/option_group', ['SELECT_OPTIONS' => $select_options,
                'SELECT_LABEL' => $bind_name]);
        }

        $params = [
            'DEFINED_NAME' => $this->definedName.'-list',
            'SELECT_OPTIONS' => $options_group,
        ];

        return $this->settingsTemplate('settings/list/list', $params);
    }
}
