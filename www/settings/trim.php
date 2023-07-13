<?php

require_once("../.config.inc.php");
define('TITLE', "Media Settings");
//$template = new Template();
require __LAYOUT_HEADER__;


$table = $explorer->table("pub_trim");
$table->order("pub_name ASC");
$results = $table->fetchAssoc('id');

foreach ($results as $k => $u) {

    $pub_name = str_replace("_", " ", $u['pub_name']);
    $pub_name = ucwords($pub_name);

    $bind = strtoupper($u['bind']);
    $head_style = '';
    $foot_style = '';
    if($u['head_trim'] === null) {
        $head_text = "Head Trim";
        $head_style = 'style="background-color : #94f9b2;"';
        $u['head_trim'] = 0;
    } else {
        $head_text = $u['head_trim'];
    }

    if($u['foot_trim'] === null) {
        $foot_text = "Foot Trim";
        $u['foot_trim'] = 0;
        $foot_style = 'style="background-color : #94f9b2;"';
    } else {
        $foot_text = $u['foot_trim'];
    }

    if($u['delivered_size'] === null) {
        $size_text = "Delivered Size";
        //$u['delivered_size'] = ;
        $size_style = 'style="background-color : #94f9b2;"';
    } else {
        $size_text = $u['delivered_size'];
    }


    $textbox_html .= Template::GetHTML("trim/text/text", [
        'NAME' => "trim_".$u['id'],
        'PUBLICATION' => $pub_name,

        'HEAD_TRIM' => $head_text,
        'FOOT_TRIM' => $foot_text,
        'DEL_SIZE' => $size_text,

        'HEAD_STYLE' => $head_style,
        'FOOT_STYLE' => $foot_style,
        'DEL_SIZE_STYLE' => $size_style,

        'HEAD_TRIM_V' => $u['head_trim'],
        'FOOT_TRIM_V' => $u['foot_trim'],
        'DEL_SIZE_V' => $u['delivered_size'],

        'BIND' => $bind,
    ]);


}


$template->render('trim/main', ['TEXTBOX_HTML' => $textbox_html]);

include_once __LAYOUT_FOOTER__;
