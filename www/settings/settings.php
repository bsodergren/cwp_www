<?php
require_once("../.config.inc.php");
define('TITLE', "Media Settings");
//$template = new Template();
require __LAYOUT_HEADER__;

$settings_array = [];
$settings_html = '';
$checkbox_html = '';
$array_html = '';
$textbox_html = '';

$cat = 'server';

if (isset($_GET['cat'])) {
	$cat = $_GET['cat'];
}

foreach (__SETTINGS__[$cat] as $definedName => $array) {

	$value_text = '';
	$params = [];
	$name_label = '';
	$checked = '';
	$notchecked = '';
	$description_label = '';

	$id =  $array['id'];
	$type =  $array['type'];
	$value =  $array['value'];
	$name = $array['name'];
	$description  = $array['description'];
	$tooltip_desc = $definedName . " " . $description;

	$tooltip = 'data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"	data-bs-title="' . $tooltip_desc . '" ';

	if ($name == null) {
		$name = $definedName;
		$text_template = "name_textbox";
		$row_name_params = ['DESCRIPTION' => $definedName, 'DEFINED_NAME' => $definedName];
	} else {
		$text_template = "name_label";
		$row_name_params = ['NAME' => $name, 'DEFINED_NAME' => $definedName];
	}

	if ($description == null) {
		$desc_template = "description_textbox";
		$row_desc_params = ['DESCRIPTION' => $description, 'DEFINED_NAME' => $definedName];
	} else {
		$desc_template = "description_label";
		$row_desc_params = ['DESCRIPTION' => $description, 'DEFINED_NAME' => $definedName];
	}


	if ($type == "bool") {

		$name_label = Template::GetHTML("settings/checkbox/" . $text_template, $row_name_params);

		$description_label = Template::GetHTML("settings/checkbox/" . $desc_template, $row_desc_params);

		if ($value == 1) {
			$checked = "checked";
		} else {
			$notchecked = "checked";
		}

		$params = [
			'DEFINED_NAME' => $definedName,
			'TOOLTIP' => $tooltip,
			'CHECKED' => $checked,
			'NAME' => $name,
			'NAME_LABEL' => $name_label,
			'DESCRIPTION_LABEL' => $description_label,
		];
		$checkbox_fields .= Template::GetHTML("settings/checkbox/checkbox", $params);
	}

	if ($type == "text") {

		$place_holder = $value;
		if ($value == '') {
			$place_holder = "no value set";
		}

		$name_label = Template::GetHTML("settings/text/" . $text_template, $row_name_params);

		$description_label = Template::GetHTML("settings/text/" . $desc_template, $row_desc_params);



		$params = [
			'DEFINED_NAME' => $definedName,
			'PLACEHOLDER' => $place_holder,
			'TOOLTIP' => $tooltip,
			'VALUE' => $value,
			'NAME' => $name,
			'NAME_LABEL' => $name_label,
			'DESCRIPTION_LABEL' => $description_label,

		];
		$text_fields .= Template::GetHTML("settings/text/text", $params);
	}

	if ($type == "array") {


		$name_label = Template::GetHTML("settings/array/" . $text_template, $row_name_params);

		$description_label = Template::GetHTML("settings/array/" . $desc_template, $row_desc_params);

		$value_text = MediaSettings::jsonString_to_TextForm($value);

		$params = [
			'DEFINED_NAME' => $definedName . "-array",
			'TOOLTIP' => $tooltip,
			'VALUE' => $value_text,
			'NAME' => $name,
			'NAME_LABEL' => $name_label,
			'DESCRIPTION_LABEL' => $description_label,

		];

		$array_fields   .= Template::GetHTML("settings/array/array", $params);;
	}
}

if ($checkbox_fields != '') {
	$checkbox_html = Template::GetHTML("settings/checkbox/main", ['CHECKBOX_FIELDS' => $checkbox_fields]);
}
if ($text_fields != '') {
	$textbox_html = Template::GetHTML("settings/text/main", ['TEXTBOX_FIELDS' => $text_fields]);
}
if ($array_fields != '') {
	$array_html = Template::GetHTML("settings/array/main", ['ARRAY_FIELDS' => $array_fields]);
}

$delete_log = Template::GetHTML("settings/delete_log");

$template->template("settings/new_setting", ['CATEGORY' => $cat]);

$settings_html = $template->return();
$template->clear();

$template->template("settings/main", [
	'CHECKBOX_HTML' => $checkbox_html,
	'TEXTBOX_HTML' => $textbox_html,
	'ARRAY_HTML' => $array_html,
	'SETTINGS_HTML' => $settings_html,
	'DELETE_LOG' => $delete_log,
	'CATEGORY' => $cat
]);

$template->render();
include_once __LAYOUT_FOOTER__;
