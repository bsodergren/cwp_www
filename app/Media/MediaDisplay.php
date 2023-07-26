<?php
/**
 * CWP Media tool
 */

namespace CWP\Media;

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;

class MediaDisplay extends HTMLDisplay
{
    public function display_table_rows($array, $letter)
    {
        $html             = '';
        $start            = '';
        $end              = '';
        $row_template     = new Template();

        foreach ($array as $part) {
            if ('' == $start) {
                $start = $part['id'];
            }

            $end         = $part['id'];

            $check_front = '';
            $check_back  = '';

            $classFront  = 'Front'.$letter;
            $classBack   = 'Back'.$letter;

            if ('Back' == $part['former']) {
                $check_back = 'checked';
            }
            if ('Front' == $part['former']) {
                $check_front = 'checked';
            }
            $radio_check = '';

            if ('4pg' == $part['config']) {
                $value       = [
                    'Front' => ['value' => 'Front', 'checked' => $check_front, 'text' => 'Front', 'class' => $classFront],
                    'Back'  => ['value' => 'Back', 'checked' => $check_back, 'text' => 'Back', 'class' => $classBack],
                ];
                $radio_check = $this->draw_radio('former_'.$part['id'], $value);
            }

            $facetrim    = MediaSettings::isFacetrim($part);


            $array       = [
                'MARKET'      => $part['market'],
                'PUBLICATION' => $part['pub'],
                'COUNT'       => $part['count'],
                'SHIP'        => $part['ship'],
                'RADIO_BTNS'  => $radio_check,
                'FACE_TRIM'   => $this->draw_checkbox('facetrim_'.$part['id'], $facetrim, 'Face Trim'),
              //  'NO_TRIM'     => $this->draw_checkbox('nobindery_'.$part['id'], $nobindery, 'No Trimmers'),
            ];

            $row_template->template('form/row', $array);
        }

        $AllCheckBoxFront = 'all'.$classFront;
        $AllCheckBoxBack  = 'all'.$classBack;
        if ($end > $start + 1 && '' != $radio_check) {
            $radio_check_array = [
                'LETTER'           => $letter,
                'ALLCHECKBOXFRONT' => $AllCheckBoxFront,
                'ALLCHECKBOXBACK'  => $AllCheckBoxBack,
                'CLASSFRONT'       => $classFront,
                'CLASSBACK'        => $classBack,
            ];
            $row_template->template('form/all_parts', $radio_check_array);
        }

        $html = $row_template->return();

        return $html;
    }
}
