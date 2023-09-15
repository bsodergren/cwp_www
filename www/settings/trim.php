<?php
use CWP\HTML\HTMLDisplay;
use CWP\HTML\HTMLForms;
use CWP\Template\Template;
use CWP\Utils\Utils;

require_once '../.config.inc.php';

define('TITLE', 'Media Settings');
//$template = new Template();
use CWP\Utils\MediaDevice;
MediaDevice::getHeader();

//$header_html = Template::GetHTML("trim/table_head", []);

$null_style = 'background-color : #94f9b2;';

$table = $explorer->table('pub_trim');
$table->order('bind DESC, delivered_size ASC, pub_name ASC');
$results = $table->fetchAssoc('id');

$header_html = Template::GetHTML('trim/table_head', []);

foreach ($results as $k => $u) {
    $pub_array[$u['bind']][$u['id']] = $u;
}


function showDecimals($var, $size = false)
{
    if(__SHOW_DECIMAL__ == true) {
        if ($var == '') {
            $var = null;
        } else {
            if ($size == true) {
                $var = Utils::DelSizeToFloat($var);
            } else {
                $var = utils::fracToFloat($var);
            }
        }
    }

    return $var;
}



$idx=0;
foreach ($pub_array as $bind => $row) {
    $bind_name = utils::bindtype($bind);
    $table_pub_head = Template::GetHTML('trim/table_pub_head', ['BIND_NAME'=>$bind_name]);

    foreach ($row as $k => $u) {
        $idx++;
        $pub_name = str_replace('_', ' ', $u['pub_name']);
        $pub_name = ucwords($pub_name);
        $bind_name = utils::bindtype($u['bind']);
        $bind = strtoupper($u['bind']);
        $bind_name = $bind;
        $head_style = '';
        $foot_style = '';
        $size_style = '';

        if ($u['head_trim'] === null) {
            $head_text = 'Head Trim';
            $head_style = $null_style;
            $u['head_trim'] = 0;
        } else {
            $head_text = $u['head_trim'];
        }
        $head_value = showDecimals($u['head_trim']);

        if ($u['foot_trim'] === null) {
            $foot_text = 'Foot Trim';
            $u['foot_trim'] = 0;
            $foot_style = $null_style;
        } else {
            $foot_text = $u['foot_trim'];
        }

        $foot_value = showDecimals($u['foot_trim']);

        if ($u['face_trim'] === null) {
            $face_trim = 'Face Trim';
            $u['face_trim'] = 0;
            $face_trim = $null_style;
        } else {
            $face_trim = $u['face_trim'];
        }
        $face_value = showDecimals($u['face_trim']);

        if ($u['delivered_size'] === null) {
            $size_text = 'Delivered Size';
            $size_style = $null_style;
        } else {
            $size_text = $u['delivered_size'];
            $size_value = showDecimals($size_text,true);
        }




        $head_html = HTMLForms::draw_text(
            'trim_'.$u['id'].'_head',
            [
                'label' => $head_text,
                'placeholder' => $head_text,
                'style' => $head_style,
                'value' => $head_value ,
                'class' => 'form-control',
            ]
        );
        $foot_html = HTMLForms::draw_text(
            'trim_'.$u['id'].'_foot',
            [
                'label' => $foot_text,
                'placeholder' => $foot_text,
                'style' => $foot_style,
                'value' => $foot_value,
                'class' => 'form-control',
            ]
        );
        $face_html = HTMLForms::draw_text(
            'trim_'.$u['id'].'_face',
            [
                'label' => $face_text,
                'placeholder' => $face_text,
                'style' => $face_style,
                'value' => $face_value,
                'class' => 'form-control',
            ]
        );

        $size_html = HTMLForms::draw_text(
            'trim_'.$u['id'].'_size',
            [
                'label' => $size_text,
                'placeholder' => $size_text,
                'style' => $size_style,
                'value' => $size_value,
                'class' => 'form-control',
            ]
        );

        $pub_checkbox = HTMLForms::draw_checkbox( 'trim_'.$u['id'].'_delete', 0,'',);

        $textbox_html .= Template::GetHTML('trim/pub_list', [
            'PUBLICATION' => $pub_name,
            'PUB_ID'=>$idx,
            'PUB_CHECKBOX' => $pub_checkbox,
            //'BIND' => $bind,
            //'BIND_NAME' => $bind_name,
            'TEXT_HEAD' => $head_html,
            'TEXT_FOOT' => $foot_html,
            'TEXT_FACE' => $face_html,
            'TEXT_SIZE' => $size_html,

        ]);
    }
    $pubhtml_list .= Template::GetHTML('trim/pub_cat', [
        'FORM_HEAD' => $table_pub_head,
        'FORM_FIELDS' => $textbox_html,
    ]);
    $textbox_html = '';
}

$pub_list .= Template::GetHTML('trim/form/form', [
    'HIDDEN' => HTMLDisplay::draw_hidden('trim_update', 'update'),
    'FORM_HEAD' => $header_html,
    'FORM_FIELDS' => $pubhtml_list,
    'FORM_BUTTON' => Template::GetHTML('trim/form/submit', ['BUTTON_TEXT'=>'Update publications']),
]);

$bind_type = ['PFS', 'PFM', 'PFL', 'SHS'];

foreach ($bind_type as $bind) {
    $optionArray[strtolower($bind)] = $bind;
}

$select_html = HTMLForms::draw_select('bind', 'Bind Style', $optionArray, $null_style);

$head_html =
HTMLForms::draw_text('publication', ['label' => 'Publication', 'placeholder' => 'Publication Name', 'style' => $null_style, 'class' => 'form-control']);

$addpub_html = Template::GetHTML('trim/new_pub', [
    'PUBLICATION' => HTMLForms::draw_text('publication', ['label' => 'Publication', 'placeholder' => 'Publication Name', 'style' => $null_style, 'class' => 'form-control']),
    'BIND_SELECT' => $select_html,
    'TEXT_HEAD' => HTMLForms::draw_text('head_trim', ['label' => 'Head Trim', 'style' => $null_style, 'class' => 'form-control']),
    'TEXT_FOOT' => HTMLForms::draw_text('foot_trim', ['label' => 'foot trim', 'style' => $null_style, 'class' => 'form-control']),
    'TEXT_FACE' => HTMLForms::draw_text('face_trim', ['label' => 'Face trim', 'style' => $null_style, 'class' => 'form-control']),

    'TEXT_SIZE' => HTMLForms::draw_text('delivered_size', ['label' => 'delivered_ size', 'placeholder' => '8-1/2 x 10-3/4', 'style' => $null_style, 'class' => 'form-control']),
]);

$header_html = Template::GetHTML('trim/table_head', ['BIND_FIELD'=>'<div class="col-sm fs-6 text-nowrap">Bind Style</div>']);

$new_html = Template::GetHTML('trim/form/form', [
    'HIDDEN' => HTMLDisplay::draw_hidden('trim_add', 'add'),
    'FORM_HEAD' => $header_html,
    'FORM_FIELDS' => $addpub_html,
    'FORM_BUTTON' => Template::GetHTML('trim/form/submit', ['BUTTON_TEXT'=>'Add new publication']),
]);

$template->render('trim/main', ['PUB_LIST' => $pub_list, 'NEW_PUB'=>$new_html]);

MediaDevice::getFooter();
