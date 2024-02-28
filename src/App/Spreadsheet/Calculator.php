<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Spreadsheet;

use CWP\Core\Media;
use CWP\Core\MediaLogger;
use CWP\Utils\Utils;

class Calculator
{
    public $form_details;

    public $media;

    public $exp;

    public $box;

    public function __construct($media)
    {
        $this->media = $media;
    }

    public function calculateBox($form_details)
    {
        $face_trim = $form_details['face_trim'];
        $pcs = (int) Utils::toint($form_details['count']);
        // (int) $form_details['count'];
        $config = $this->media->form_configuration['configuration'];
        $paper_wieght = $this->media->form_configuration['paper_wieght'];
        $carton_size = $this->media->form_configuration['carton_size'];

        $delivery = strtolower($form_details['former']);

        $paper_size = $this->media->form_configuration['paper_size'];
        $config = str_replace('pg', '', $config);

        $res = Media::$explorer->table('paper_type')->select('id')->where('paper_wieght = ?  AND paper_size = ?  AND pages = ?', $paper_wieght, $paper_size, $config)->fetch(); // UPDATEME
        $res = Media::$explorer->table('paper_count')->where('paper_id', $res['id'])->fetch(); // UPDATEME

        foreach ($res as $var => $value) {
            $$var = $value;
        }

        if ($pcs <= $max_carton && 1 != $face_trim) {
            $package = 'carton';
        } elseif (($pcs > $max_carton || 1 == $face_trim) && $pcs <= $max_half) {
            $package = 'half';
        } else {
            $package = 'full';
        }

        if ('back' == $delivery) {
            if ($pcs <= $max_half) {
                $package = 'half';
            } else {
                $package = 'full';
            }
        }

        $max_skid_var = 'max_'.$package;
        $max_skid = $$max_skid_var;

        $lift_size = $delivery.'_lift';

        if ('carton' == $package) {
            // lifts per carton
            $lifts_per_layer = $pcs_carton / $$lift_size;

            $full_boxes = floor($pcs / $pcs_carton);

            $lifts_last_layer = $pcs - ($pcs_carton * $full_boxes);

            $package = $carton_size.' '.$package.'s';

            $layers_last_box = $pcs_carton;
        } else {

            $lifts_per_layer = $package.'_skid_lifts_layer';
            $layers_per_skid = $delivery.'_'.$package.'_skid_layers';

            $number_of_lifts = ceil($pcs / $$lift_size);
            $lifts_in_box = $max_skid / $$lift_size;
            $full_boxes = floor($number_of_lifts / $lifts_in_box);

            $lifts_last_box = $number_of_lifts - ($full_boxes * $lifts_in_box);
            $layers_last_box = floor($lifts_last_box / $$lifts_per_layer);
            $lifts_last_layer = ceil($lifts_last_box - ($layers_last_box * $$lifts_per_layer));
            $lifts_per_layer = $$lifts_per_layer;
        }

        $result = [
            'packaging' => $package,
            'full_boxes' => $full_boxes,
            'layers_last_box' => $layers_last_box,
            'lifts_last_layer' => $lifts_last_layer,
            'lift_size' => $$lift_size,
            'lifts_per_layer' => $lifts_per_layer,
            'max_skid' => $max_skid,
        ];

        if (isset($$layers_per_skid)) {
            $result['layers_per_skid'] = $$layers_per_skid;
        }

        // return [ $package,$full_boxes,$layers_last_box,$lifts_last_layer,$$lift_size,$lifts_per_layer];
        return $result;
    }
}
