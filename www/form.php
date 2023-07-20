<?php
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

define('TITLE', 'Form Editor');
$display                       = new MediaDisplay();
// $media = new Media();
// $template = new Template();

include_once __LAYOUT_HEADER__;

$row_html                      = '';
$letter_html                   = '';
// $page_form_html = '';
$next_view                     = 'job';
// $media->job_id = $_REQUEST['job_id'];

$max_forms                     =  $media->get_max_drop_forms();
$first_form                    = $media->get_first_form();
$form_list                     = $media->get_form_list();

if (array_key_exists('form_number', $_REQUEST)) {
    $prev_form_number    = $_REQUEST['form_number'] - 1;
    $current_form_number = $_REQUEST['form_number'];
} else {
    $prev_form_number    = 0;
    $current_form_number = $first_form;
}

$new_forms                     = [];

$next_form_number              = $current_form_number + 1;

$form_data                     = $explorer->table('form_data');
$form_data->where('form_number = ?', $current_form_number);
$form_data->where('job_id = ?', $media->job_id);
$results                       = $form_data->fetch();

if (empty($results)) {
    $current_form_number = $current_form_number + 1;
}
$sort                          = ['SORT_FORMER' => 1, 'SORT_LETTER' => 1];

$result                        = $media->get_drop_form_data($current_form_number, $sort);

foreach ($result as $idx => $form_array) {
    $form_number                                           = $form_array['form_number'];
    // $job_id = $form_array['job_id'];
    $media->job_id                                         = $form_array['job_id'];

    $config                                                = $media->get_drop_details($form_number);

    $new_forms[$form_number][$form_array['form_letter']][] = [
        'id'         => $form_array['id'],
        'market'     => $form_array['market'],
        'pub'        => $form_array['pub'],
        'ship'       => $form_array['ship'],
        'count'      => $form_array['count'],
        'config'     => $config[$form_number]['config'],
        'bind'       => $config[$form_number]['bind'],
        'former'     => $form_array['former'],
        'facetrim'   => $form_array['face_trim'],
        'nobindery'  => $form_array['no_bindery'],
        'job_number' => $form_array['job_number'],
    ];
}

foreach ($new_forms as $form_number => $parts) {
    $next_button           = 'Next';

    if ($current_form_number != $first_form) {
        $page_form_html .= template::GetHTML('/form/page_links', [
            'PAGE_CLASS'       => ' btn-info',
            'PAGE_FORM_URL'    => __URL_PATH__.'/form.php?job_id='.$media->job_id.'&form_number='.$prev_form_number,
            'PAGE_FORM_NUMBER' => 'Previous',
        ]);
    }

    $form_part             = '';
    foreach ($form_list as $n => $list_form_number) {
        $url_form_number  = $list_form_number->form_number;
        $page_html_params = [];
        // if ($n != 0) {
        $form_part        = '&form_number='.$url_form_number;
        // }

        $page_form_number = $list_form_number->form_number;
        if ($current_form_number == $page_form_number) {
            if (true == Media::get_exists('xlsx', $row['job_id'])) {
                $dropdown_links .= template::GetHTML('/form/dropdown/dropdown_link', [
                    'PAGE_CLASS'       => ' btn-success',
                    'PAGE_FORM_URL'    => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&form_number='.$page_form_number,
                    'PAGE_FORM_NUMBER' => 'View',
                ]);
            }
            $edit_url         = __URL_PATH__.'/form_edit.php?job_id='.$media->job_id.'&form_number='.$page_form_number;
            $dropdown_links .= template::GetHTML('/form/dropdown/dropdown_link', [
                'PAGE_CLASS'       => ' btn-danger',
                'PAGE_JS'          => ' onClick="OpenNewWindow(\''.$edit_url.'\')" ',
               // 'PAGE_FORM_URL' => $edit_url,
                'PAGE_FORM_NUMBER' => 'Edit',
            ]);
            $dropdown_links .= template::GetHTML('/form/dropdown/dropdown_link', [
                'PAGE_CLASS'       => ' btn-warning',
                'PAGE_FORM_URL'    => __URL_PATH__.'/update.php?job_id='.$media->job_id.'&form_number='.$page_form_number,
                'PAGE_FORM_NUMBER' => 'Update',
            ]);

            //  $page_form_html .= template::GetHTML('/form/dropdown/dropdown', ['DROPDOWN_LINKS'=>$dropdown_links,'DROPDOWN_TEXT_FORM' => $page_form_number ]);
            $page_html_params = [
              'PAGE_CLASS'       => ' btn-primary',
              'PAGE_FORM_URL'    => __URL_PATH__.'/form.php?job_id='.$media->job_id.$form_part,
              'PAGE_FORM_NUMBER' => $page_form_number,
        ];
        } else {
            $page_html_params = [
                'PAGE_CLASS'       => ' btn-secondary',
                'PAGE_FORM_URL'    => __URL_PATH__.'/form.php?job_id='.$media->job_id.$form_part,
                'PAGE_FORM_NUMBER' => $page_form_number,
            ];
        }
        $page_form_html .= template::GetHTML('/form/page_links', $page_html_params);
    }

    $form_btn_class        = ' btn-info';
    if ($next_form_number > $max_forms) {
        $next_view        = 'save';
        $next_button      = 'Save Form';
        $form_btn_class   = ' btn-success';
        // $previous_form_html =' ';
        $next_form_number = $current_form_number;
    } else {
        $page_form_html .= template::GetHTML('/form/page_links', [
            'PAGE_CLASS'       => ' btn-warning',
            'PAGE_FORM_URL'    => __URL_PATH__.'/update.php?job_id='.$media->job_id,
            'PAGE_FORM_NUMBER' => 'Update',
        ]);
    }
    $page_form_html .= template::GetHTML('/form/page_form_submit', [
        'PAGE_CLASS'   => $form_btn_class,
        'BUTTON_VALUE' => $next_button,
    ]);

    $form_html['FORM_URL'] = __URL_PATH__.'/process.php';
    $form_html['NAME']     = $form_array['job_number'].' - Form Number '.$form_number.' of '.$max_forms.' - '.$config[$form_number]['config'].' - '.$config[$form_number]['bind'];

    foreach ($parts as $form_letter => $form_data) {
        $row_html = $display->display_table_rows($form_data, $form_letter);
        $template->template('form/header', ['NUMBER' => $form_number, 'LETTER' => $form_letter, 'ROWS' => $row_html]);

        // $template->clear();
    }
    $letter_html .= $template->return();
    $template->clear();
}

$form_html['NEXT_FORM_NUMBER'] = $next_form_number;
$form_html['JOB_ID']           = $media->job_id;
$form_html['NEXT_VIEW']        = $next_view;
$form_html['FORM_BODY_HTML']   = $letter_html;
$form_html['FORM_BUTTONS']     = $dropdown_links;
$form_html['FORM_LIST_HTML']   = $page_form_html;

$template->clear();

$template->template('form/main', $form_html);
$template->render();

include_once __LAYOUT_FOOTER__;
