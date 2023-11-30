<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\Core\MediaSettings;
use CWP\HTML\HTMLDisplay;
use CWP\Process\Form;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';
// array:2 [▼
//   "job_id" => "1"
//   "form_number" => "2"
// ]
if(count($_POST)> 0){
    $form = new Form($media);
    $form->run($_POST);
   // dd($form->url);
   $parts = parse_url($form->url);
    if(array_key_exists('query',$parts)){
        parse_str($parts['query'],$query);
        foreach($query as $key => $value){
            $_REQUEST[$key] = $value;
        }
    } else {

        $form->reload();
    }
}
define('TITLE', 'Form Editor');
$display = new HTMLDisplay();
// $media = new Media();
// $template = new Template();

MediaDevice::getHeader();

$row_html = '';
$letter_html = '';
$page_form_html = '';
$dropdown_links = '';
$next_view = 'Next';
$media->job_id = $_REQUEST['job_id'];

$max_forms = $media->get_max_drop_forms();
$first_form = $media->get_first_form();
$form_list = $media->get_form_list();

if (array_key_exists('form_number', $_REQUEST)) {
    $prev_form_number = $_REQUEST['form_number'] - 1;
    $current_form_number = $_REQUEST['form_number'];
} else {
    $prev_form_number = 0;
    $current_form_number = $first_form;
}

$new_forms = [];

$next_form_number = $current_form_number + 1;

$form_data = $explorer->table('form_data');
$form_data->where('form_number = ?', $current_form_number);
$form_data->where('job_id = ?', $media->job_id);
$results = $form_data->fetch();

if (empty($results)) {
    ++$current_form_number;
}
$sort = ['SORT_FORMER' => 1, 'SORT_LETTER' => 1];

$result = $media->getFormDrops($current_form_number, $sort);
foreach ($result as $idx => $form_array) {
    $form_number = $form_array['form_number'];
    // $job_id = $form_array['job_id'];
    $media->job_id = $form_array['job_id'];

    $config = $media->getDropDetails($form_number);

    $new_forms[$form_number][$form_array['form_letter']][] = [
        'id' => $form_array['id'],
        'market' => $form_array['market'],
        'pub' => $form_array['pub'],
        'ship' => $form_array['ship'],
        'count' => $form_array['count'],
        'config' => $config[$form_number]['config'],
        'bind' => $config[$form_number]['bind'],
        'former' => $form_array['former'],
        'facetrim' => $form_array['face_trim'],
        'nobindery' => $form_array['no_bindery'],
        'job_number' => $form_array['job_number'],
    ];
}

foreach ($new_forms as $form_number => $parts) {
    $next_button = 'Next';

    if ($current_form_number != $first_form) {
        $dropdown_links .= template::GetHTML('/form/page_form_submit', [
            'PAGE_CLASS' => ' btn-info',
            'BUTTON_VALUE' => 'Previous',
        ]);

        // $dropdown_links .= template::GetHTML('/form/page_links', [
        //     'PAGE_CLASS'       => ' btn-info',
        //     'PAGE_FORM_URL'    => __URL_PATH__.'/form.php?job_id='.$media->job_id.'&form_number='.$prev_form_number,
        //     'PAGE_FORM_NUMBER' => 'Previous',
        // ]);
    }

    $form_part = '';
    foreach ($form_list as $n => $list_form_number) {
        $url_form_number = $list_form_number->form_number;
        $page_html_params = [];
        // if ($n != 0) {
        $form_part = '&form_number='.$url_form_number;
        // }

        $page_form_number = $list_form_number->form_number;
        if ($current_form_number == $page_form_number) {
            // if (true == Media::get_exists('xlsx', $row['job_id'])) {
            //     $dropdown_links .= template::GetHTML('/form/dropdown/dropdown_link', [
            //         'PAGE_CLASS'       => ' btn-success',
            //         'PAGE_FORM_URL'    => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&form_number='.$page_form_number,
            //         'PAGE_FORM_NUMBER' => 'View',
            //     ]);
            // }
            $edit_url = __URL_PATH__.'/form_edit.php?job_id='.$media->job_id.'&form_number='.$page_form_number;
            $dropdown_links .= template::GetHTML('/form/dropdown/dropdown_link', [
                'PAGE_CLASS' => ' btn-danger',
                'PAGE_JS' => ' onClick="OpenNewWindow(\''.$edit_url.'\')" ',
                // 'PAGE_FORM_URL' => $edit_url,
                'PAGE_FORM_NUMBER' => 'Edit',
            ], false,false);
            $dropdown_links .= template::GetHTML('/form/dropdown/dropdown_link', [
                'PAGE_CLASS' => ' btn-warning',
                'PAGE_FORM_URL' => __URL_PATH__.'/update.php?job_id='.$media->job_id.'&form_number='.$page_form_number,
                'PAGE_FORM_NUMBER' => 'Update',
            ], false,false);

            //  $page_form_html .= template::GetHTML('/form/dropdown/dropdown', ['DROPDOWN_LINKS'=>$dropdown_links,'DROPDOWN_TEXT_FORM' => $page_form_number ]);
            $page_html_params = [
                'PAGE_CLASS' => ' btn-primary',
                'PAGE_FORM_URL' => __URL_PATH__.'/form.php?job_id='.$media->job_id.$form_part,
                'PAGE_FORM_NUMBER' => $page_form_number,
            ];
        } else {
            $page_html_params = [
                'PAGE_CLASS' => ' btn-secondary',
                'PAGE_FORM_URL' => __URL_PATH__.'/form.php?job_id='.$media->job_id.$form_part,
                'PAGE_FORM_NUMBER' => $page_form_number,
            ];
        }
        $page_form_html .= template::GetHTML('/form/page_links', $page_html_params, false,false);
    }

    $form_btn_class = ' btn-info';
    if ($next_form_number > $max_forms) {
        $next_view = 'save';
        $next_button = 'Save Form';
        $form_btn_class = ' btn-success';
        // $previous_form_html =' ';
        $next_form_number = $current_form_number;
    } else {
        $page_form_html .= template::GetHTML('/form/page_form_submit', [
            'PAGE_CLASS' => ' btn-success',
            'BUTTON_VALUE' => 'Save Form',
        ]);
        // $page_form_html .= Template::GetHTML('/form/page_links', [
        //     'PAGE_CLASS'       => ' btn-warning',
        //     'PAGE_FORM_URL'    => __URL_PATH__.'/update.php?job_id='.$media->job_id,
        //     'PAGE_FORM_NUMBER' => 'Update',
        // ]);
    }
    $dropdown_links .= template::GetHTML('/form/page_form_submit', [
        'PAGE_CLASS' => $form_btn_class,
        'BUTTON_VALUE' => $next_button,
    ]);

    $form_html['FORM_URL'] = __URL_PATH__.'/form.php';
    $form_html['NAME'] = $form_array['job_number'].' - Form Number '.$form_number.' of '.$max_forms.' - '.$config[$form_number]['config'].' - '.$config[$form_number]['bind'];
    $form_html['CHECKBOX_PARTS'] = '';

    $columns = 12 / count($parts);
    $ColClass = 'col-'.$columns;

    foreach ($parts as $form_letter => $form_data) {
        $frontChecked = '';
        $backChecked = '';

        if ('Front' == $form_data[0]['former']) {
            $frontChecked = 'checked';
        }
        if ('Back' == $form_data[0]['former']) {
            $backChecked = 'checked';
        }

        $BtnClass = 'btn-check';
        $LabelClass = 'btn btn-outline-success';

        $classFront = 'Front'.$form_letter;
        $classBack = 'Back'.$form_letter;

        $radio_check_array = [
            'NAME' => 'All'.$form_letter,
            'FRONTID' => 'Front-'.$form_letter.'-outlined',
            'BACKID' => 'Back-'.$form_letter.'-outlined',
            'COLUMS' => $ColClass,
            'LETTER' => $form_letter,

            'FRONTCHECKED' => $frontChecked,
            'BACKCHECKED' => $backChecked,

            'FRONTLABELCLASS' => $LabelClass.' all'.$classFront,

            'FRONTBUTTONCLASS' => $BtnClass.' all'.$classFront,

            'BACKLABELCLASS' => $LabelClass.' all'.$classBack,
            'BACKBUTTONCLASS' => $BtnClass.' all'.$classBack,

            'ALLCHECKBOXFRONT' => 'all'.$classFront,
            'ALLCHECKBOXBACK' => 'all'.$classBack,

            'CLASSFRONT' => $classFront,
            'CLASSBACK' => $classBack,
        ];

        // dump([$radio_check_array]);
        $form_html['CHECKBOX_PARTS'] .= Template::GetHTML('form/quickselect/letter_select', $radio_check_array,false,false);
        $form_html['CHECKBOX_JAVA'] .= Template::GetHTML('form/quickselect/javascript', $radio_check_array,false,false);
        // $form_html['CHECKBOX_PARTS'] .= $display->draw_checkbox('quickselect['.$form_letter.'_'.$list.']', 'Front', $form_letter, 'form/checkbox');
        $row_html = $display->display_table_rows($form_data, $form_letter);
        $nobindery = MediaSettings::skipTrimmers($form_data);
        $checkbox = $display->draw_checkbox('nobindery_'.$form_number, $nobindery, 'No Trimmers', 'form/checkbox');
        $template->template('form/header', ['NUMBER' => $form_number, 'LETTER' => $form_letter, 'TRIMMERS' => $checkbox,
            'ROWS' => $row_html],false,false);

        // $template->clear();
    }
    $letter_html .= $template->return();
    $template->clear();
}

$form_html['FORM_NUMBER'] = $form_number;
$form_html['NEXT_FORM_NUMBER'] = $next_form_number;
$form_html['PREV_FORM_NUMBER'] = $prev_form_number;

$form_html['JOB_ID'] = $media->job_id;
$form_html['NEXT_VIEW']        = $next_view;
$form_html['FORM_BODY_HTML'] = "\n<!-- --------------------- -->\n".$letter_html."\n<!-- --------------------- -->\n";
$form_html['FORM_BUTTONS'] = $dropdown_links;
$form_html['FORM_LIST_HTML'] = $page_form_html;

$template->clear();

$template->template('form/main', $form_html,false,false);
$template->render();

MediaDevice::getFooter();
