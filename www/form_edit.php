<?php
use CWP\HTML\HTMLDisplay;
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

define('TITLE', 'Media Job editor');
define('NO_NAV', true);

use CWP\Utils\MediaDevice;
MediaDevice::getHeader();

// $media = new Media();
// $template = new Template();

$deleted_id  = 0;

// $job_id=$_REQUEST['job_id'];
$form_number = $_REQUEST['form_number'];

// $media->job_id = $job_id;

function toArray($obj)
{
    $vars  = get_object_vars($obj);
    $array = [];
    foreach ($vars as $key => $value) {
        $array[ltrim($key, '_')] = $value;
    }

    return $array;
}

$var         = $media->getFormDrops($form_number, ['SORT_FORMER' => 1, 'SORT_LETTER' => 1]);

$display     = new HTMLDisplay();

foreach ($var as $obj) {
    $form_row[] =  toArray($obj);
}

$rows_html   = '';
foreach ($form_row as $idx => $row) {
    $params                    = [];
    $params['FORM_NUMBER']     =  $row['form_number'];
    $params['ROW_LETTER']      =  $row['form_letter'];
    $params['ROW_ID']          =  $row['id'];
    $params['ROW_DESC']        = $row['market'].' '.$row['pub'].' '.$row['ship'];
    $params['PCS_COUNT']       = $row['count'];
    $params['DELETE_CHECKBOX'] = $display->draw_checkbox($row['id'].'_delete', '', 'delete');
    $params['SPLIT_CHECKBOX']  = $display->draw_checkbox($row['id'].'_split', '', 'split');

    $classFront                = 'Front'.$letter;
    $classBack                 = 'Back'.$letter;

    if ('Back' == $row['former']) {
        $check_back = 'checked';
    }

    if ('Front' == $row['former']) {
        $check_front = 'checked';
    }
    $radio_check               = '';

    $value                     = [
        'Front' => ['value' => 'Front', 'checked' => $check_front, 'text' => 'Front', 'class' => $classFront],
        'Back'  => ['value' => 'Back', 'checked' => $check_back, 'text' => 'Back', 'class' => $classBack],
    ];
    $radio_check               = $display->draw_radio($row['id'].'_former', $value);

    $params['RADIO_BTNS']      = $radio_check;

    $params['FT_VALUE']        = $display->draw_checkbox($row['id'].'_facetrim', $row['face_trim'], 'Face Trim');

    $rows_html .= $template->return('form_edit/row', $params);
}

$body_html   = $template->return('form_edit/table_body', ['TABLE_ROWS' => $rows_html]);

$html_array  = [
    'JOB_ID'       => $job_id,
    'FORM_NUMBER'  => $form_number,
    'TABLE_BODY'   => $body_html,
    'TABLE_HEADER' => $template->return('form_edit/table_header'),
];

echo $template->return('form_edit/main', $html_array);

MediaDevice::getFooter();
