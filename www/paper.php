<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLForms;
use CWP\Template\Template;

require_once '.config.inc.php';
define('TITLE', 'Paper Editor');
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();

$form_url = __URL_HOME__.'/process.php';
define('__FORM_URL__', $form_url);

/*
function display_array_as_row($array)
{
    $html=process_template("form_letter_header",array("NUMBER" => "Paper Editor"))."\n";

    foreach($array as $row => $values)
    {
        $html .= "\t<tr>"."\n";
        foreach($values as $k => $v)
        {

            if($k == "id") {
                $html .= "\t\t<td><a href=\"".__FORM_URL__."?edit&id=".$v."\"> edit </a></td>"."\n";
                if(TITLE != "Paper Editor"){  $html .= "\t\t".'<td><a href="'.__FORM_URL__.'?delete&id='.$v.'">Delete</a></td>'."\n";}
            } elseif($k != "trim" ) {
                $html .= "\t\t<td>".$v."</td>\n";
            }
        }
        $html .=  "\t</tr>"."\n";
    }

  $html .= "\t".'<tr><td colspan=2><a href="'.__FORM_URL__.'?add"> Add new data </a></td></tr>'."\n";

    return $html;
}
*/

$paper_type = $explorer->table('paper_type');
// $paper_type->limit(4);
foreach ($paper_type as $paper) {
    $header_param['PAPER_INFO'] = $paper->paper_wieght.' '.$paper->paper_size.' '.$paper->pages;

    foreach ($paper->related('paper_count', 'paper_id') as $paper_details) {
        $row_params           = [];
        $header_param['ROWS'] = '';
        $row_html             = '';
        $i                    = 0;
        foreach ($paper_details as $key => $val) {
            $text_params = [];
            if ('id' == $key) {
                $row_id = $val;
                continue;
            }
            if ('paper_id' == $key) {
                continue;
            }
            $i++;

            if (6 == $paper->pages || 8 == $paper->pages) {
                if (str_contains($key, 'back')) {
                    continue;
                }
            }

            $bg_class = ' bg-warning-subtle ';
            if ('' == $val) {
                $bg_class = ' bg-danger-subtle ';
            }

            $text_params = [
                'FORM_VALUE_CLASS' => $bg_class,
                'FORM_LABEL'       => 'label_'.$key,
                'FORM_TEXT'        => ucwords(str_replace('_', ' ', $key)),
                'FORM_NAME'        => $row_id.'['.$key.']',
                'FORM_VALUE'       => $val,
            ];

            if (str_contains($key, 'carton')) {
                $carton_params[strtoupper($key)] = Template::GetHTML('paper/text_row', $text_params);
            }

            $header_param['ROWS'] .= Template::GetHTML('paper/text_row', $text_params);
            /*
                        $row_params[strtoupper($key)]=HTMLForms::draw_text($row_id."[".$key."]",
                        [
                            'label' => $key,
                            'placeholder' => $key ." ".$row_id,

                            'value' => $val ,
                            'class' => 'form-control',
                        ]);
                        */
        }

        $row_header_html .= Template::GetHTML('paper/paper_header', $header_param);
    }
}

echo Template::GetHTML('paper/main', ['FORM_URL' => $form_url,
    'PAPER_BODY_HTML'                            => $row_header_html,
    'FORM_BUTTON'                                => Template::GetHTML('trim/form/submit', ['BUTTON_TEXT' => 'Update publications']),
]);
// $results        = $paper_type->fetchAll();

// dd($results);

MediaDevice::getFooter();
