<?php
require_once(".config.inc.php");
define('TITLE', "Media Settings");
$template = new Template();
require __LAYOUT_HEADER__;

$settings_html='';
$checkbox_html='';

$textbox_html='';
//$form->messages(); 
	# $form->create_form('Name, Email, Comments|textarea');
	if (defined('__SETTINGS__')) {
		foreach (__SETTINGS__ as $definedName => $array) {

			$params = [];
			$type =  $array['type'];
			$value =  $array['value'];
			$description  = $array['description'] ;
			$name = $array['name'];

			$tooltip_desc = $definedName ." " . $description;	
			$name_label = '';
			$tooltip = 'data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"	data-bs-title="'.$tooltip_desc.'" ';


			if ($type == "bool") {
				$checked = '';
				$notchecked = '';

				if ( $name == null )
				{
					$name = $definedName;
					$name_label = Template::GetHTML("settings/checkbox/textbox",['DESCRIPTION' => $definedName,'DEFINED_NAME'=>$definedName]);
				} else {
					$name_label = Template::GetHTML("settings/checkbox/label",['NAME' => $name,'DEFINED_NAME'=>$definedName]);
	
				}
				
				if($description == null )
				{
					$description_label = Template::GetHTML("settings/checkbox/description_textbox",['DESCRIPTION' => $definedName,'DEFINED_NAME'=>$definedName]);
				} else {
					$description_label = Template::GetHTML("settings/checkbox/description_label",['DESCRIPTION' => $description,'DEFINED_NAME'=>$definedName]);
				}

				if ($value == 1) {
					$checked = "checked";
				} else {
					$notchecked = "checked";;
				}

				$params = [
					'DEFINED_NAME' => $definedName,
					'TOOLTIP' => $tooltip,
					'CHECKED' => $checked,
					'NAME' => $name,
					'NAME_LABEL' => $name_label,
					'DESCRIPTION_LABEL' => $description_label,
				];
				$checkbox_html .= Template::GetHTML("settings/checkbox/checkbox",$params);
				
			}

			if ($type == "text") {

				$place_holder = $value;
				if($value == '' ){
					$place_holder = "no value set";
				}

				if ( $name == null )
				{
					$name = $definedName;
					$name_label = Template::GetHTML("settings/text/textbox",['DESCRIPTION' => $definedName,'DEFINED_NAME'=>$definedName]);
				} else {
					$name_label = Template::GetHTML("settings/text/label",['NAME' => $name,'DEFINED_NAME'=>$definedName]);
	
				}

				if($description == null )
				{
					$description_label = Template::GetHTML("settings/checkbox/description_textbox",['DESCRIPTION' => $definedName,'DEFINED_NAME'=>$definedName]);
				} else {
					$description_label = Template::GetHTML("settings/checkbox/description_label",['DESCRIPTION' => $description,'DEFINED_NAME'=>$definedName]);
				}
				$params = [
					'DEFINED_NAME' => $definedName,
					'PLACEHOLDER' => $place_holder,
					'TOOLTIP' => $tooltip,
					'VALUE' => $value,
					'NAME' => $name,
					'NAME_LABEL' => $name_label,
					'DESCRIPTION_LABEL' => $description_label,

				];
				$textbox_html .= Template::GetHTML("settings/text/text",$params);
				
			}

			if ($type == "array")
			 {

			

				if ( $name == null )
				{
					$name = $definedName;
					$name_label = Template::GetHTML("settings/array/textbox",['DESCRIPTION' => $definedName,'DEFINED_NAME'=>$definedName]);
				} else {
					$name_label = Template::GetHTML("settings/array/label",['NAME' => $name,'DEFINED_NAME'=>$definedName]);
	
				}

				if($description == null )
				{
					$description_label = Template::GetHTML("settings/array/description_textbox",['DESCRIPTION' => $definedName,'DEFINED_NAME'=>$definedName]);
				} else {
					$description_label = Template::GetHTML("settings/array/description_label",['DESCRIPTION' => $description,'DEFINED_NAME'=>$definedName]);
				}

				
				$value_array = json_decode($value,1);
				if(is_array($value_array))
				{
					foreach($value_array as $text => $link){
						$value_text .= "$text => $link\n";
					}

				}

				$params = [
					'DEFINED_NAME' => $definedName."-array",
//					'PLACEHOLDER' => $place_holder,
					'TOOLTIP' => $tooltip,
					'VALUE' => $value_text,
					'NAME' => $name,
					'NAME_LABEL' => $name_label,
					'DESCRIPTION_LABEL' => $description_label,

				];

				$array_html   .= Template::GetHTML("settings/array/text",$params);;



			}
		}
	}

	$delete_log = Template::GetHTML("settings/delete_log");

	$template->template("settings/new_setting");

	$settings_html = $template->return();
	$template->clear();


	$template->template("settings/main",[
		'CHECKBOX_HTML' => $checkbox_html,
		'TEXTBOX_HTML' => $textbox_html,
		'ARRAY_HTML' => $array_html,
		'SETTINGS_HTML' => $settings_html,
		'DELETE_LOG' => $delete_log	]);

		$template->render();
include_once __LAYOUT_FOOTER__;  ?>