<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\HTML\HTMLForms;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;


require_once '.config.inc.php';
define('TITLE', 'Paper Editor');

$row_header_html = '';
MediaDevice::getHeader();

$paper_type = $explorer->table('paper_type'); // UPDATEME
// $paper_type->limit(4);
foreach ($paper_type as $paper) {
    $header_param = [];

    $header_param['PAPER_INFO'] = $paper->paper_wieght . '# ' . $paper->paper_size . ' ' . $paper->pages . 'pgs';

    foreach ($paper->related('paper_count', 'paper_id') as $paper_details) {
        $row_params = [];
        $header_param['CLASS'] = 'm-0 p-0 gy-1 gx-2'; // border-1 border-bottom border-dark';
        $row_html = '';
        $i = 0;
        foreach ($paper_details as $key => $val) {
            $text_params = [];
            if ('id' == $key) {
                $row_id = $val;
                continue;
            }
            if ('paper_id' == $key) {
                continue;
            }
            ++$i;

            $form_section = 'GENERAL';
            if (str_contains($key, 'back')) {
                if (6 == $paper->pages || 8 == $paper->pages) {
                    continue;
                }
                $form_section = 'BACK';
            } elseif (str_contains($key, 'front')) {
                $form_section = 'FRONT';
            }

            $bg_class = ' bg-warning-subtle ';
            if ('0' == $val) {
                $bg_class = ' bg-danger-subtle ';
            }

            $text_params = [
                'FORM_VALUE_CLASS' => $bg_class,
                'FORM_LABEL' => 'label_' . $key,
                'FORM_TEXT' => ucwords(str_replace('_', ' ', $key)),
                'FORM_NAME' => $row_id . '[' . $key . ']',
                'FORM_VALUE' => $val,
            ];


            $header_param[$form_section] .= Template::GetHTML('paper/text_row', $text_params);

        }

        $row_header_html .= Template::GetHTML('paper/paper_header', $header_param);
    }
}

echo Template::GetHTML('paper/main', [
    'PAPER_BODY_HTML' => $row_header_html,
    'FORM_BUTTON' => Template::GetHTML('trim/form/submit', ['BUTTON_TEXT' => 'Update publications']),
]);


MediaDevice::getFooter();
