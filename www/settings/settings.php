<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\Utils\Utils;
use CWP\Template\Template;

require_once '../.config.inc.php';
define('TITLE', 'Media Settings');
// $template = new Template();
use CWP\Utils\MediaDevice;
use CWP\Core\MediaSettings;
use CWP\Template\Pages\Settings;

MediaDevice::getHeader();

$settings_array = [];
$settings_html = '';
$checkbox_html = '';
$array_html = '';
$textbox_html = '';

$cat = 'server';

if (isset($_GET['cat'])) {
    $cat = $_GET['cat'];
}


$table = Media::$explorer->table('settings'); // UPDATEME
$table->select('setting_group');
$table->group('setting_group');
foreach ($table as $id => $row) {
    if (null !== $row->setting_group) {
        $setting_groups[] = $row->setting_group;
    }
}
$table = Media::$explorer->table('settings'); // UPDATEME
$table->where('setting_cat', $cat);
$table->order('setting_group ASC');
// $table->group('setting_group');
// $results = $table->fetchall();

foreach ($table as $id => $row) {
    $group = '';
    if (null === $row->setting_group) {
        $group = 'Lang';
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
krsort($settings);


foreach ($settings as $setting_group => $setting)
{
    $checkbox = '';
    $list = '';
    $text = '';
    $array = '';


    if($setting_group != ""){
        $groupName = $setting_group;
    } else {
        $groupName = "Other";
    }
        $card['CARD_HEADER'] = Template::GetHTML("settings/card_group/header",["HEADER_TEXT"=>$groupName]);
    foreach($setting as $k=>$row)
    {
        $settingObj = new Settings($row);
        if($row['type'] == 'bool'){
            $checkbox_html .= $settingObj->Checkbox();
        }
        if($row['type'] == 'text'){
            $textbox_html .= $settingObj->Text();
        }
        if($row['type'] == 'list'){
            $list_html .= $settingObj->List();
        }
    }




    $card_group_html = $checkbox_html . $textbox_html . $list_html;

    $card_body_html= Template::GetHTML("settings/card_group/body_header",['COL_ONE' => $settingObj->col_w_one,
    'COL_TWO' => $settingObj->col_w_two,
    'COL_THREE' => $settingObj->col_w_three,"GROUP_HTML"=>$card_group_html]);
    $card['CARD_BODY'] = Template::GetHTML("settings/card_group/body",["CARD_HTML"=>$card_body_html]);

    $setting_group_html .= Template::GetHTML("settings/card_group/card",$card);

    $list_html = '';
    $checkbox_html = '';
    $array_html = '';
    $textbox_html = '';

}

$settings_html = $template->return();
$template->clear();
$template->template('settings/main', [
    'SETTING_GROUPS' => $setting_group_html,
    'CATEGORY' => $cat]);

$template->render();
MediaDevice::getFooter();
