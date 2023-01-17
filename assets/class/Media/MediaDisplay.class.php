<?php


class MediaDisplay extends HTMLDisplay
{


    public function display_table_rows($array, $letter)
    {
        $html = '';
        $start = '';
        $end = '';
        $row_template = new template();

        foreach ($array as $part) {
            if ($start == '') {
                $start = $part["id"];
            }

            $end = $part["id"];

            $check_front = "";
            $check_back = "";

            $classFront = "Front" . $letter;
            $classBack = "Back" . $letter;


            if ($part["former"] == "Back") {
                $check_back = "checked";
            }
            if ($part["former"] == "Front") {
                $check_front = "checked";
            }
            $radio_check = '';

            if ($part['config'] == "4pg") {
                $value = array(
                    "Front" => array("value" => "Front", "checked" => $check_front, "text" => "Front", "class" => $classFront),
                    "Back" => array("value" => "Back", "checked" => $check_back, "text" => "Back", "class" => $classBack)
                );
                $radio_check = $this->draw_radio("former_" . $part["id"], $value);
            }



            $array = array(
                "MARKET" => $part["market"],
                "PUBLICATION" => $part["pub"],
                "COUNT" => $part["count"],
                "SHIP" => $part["ship"],
                "RADIO_BTNS" => $radio_check,
                "FACE_TRIM" => $this->draw_checkbox("facetrim_" . $part["id"], $part["facetrim"], 'Face Trim'),
                "NO_TRIM" => $this->draw_checkbox("nobindery_" . $part["id"], $part["nobindery"], 'No Trimmers')
            );

            $row_template->template("form/row", $array);
        }


        $AllCheckBoxFront = "all" . $classFront;
        $AllCheckBoxBack = "all" . $classBack;
        if ($end > $start + 1 && $radio_check != '') {


            $radio_check_array = [
                'LETTER' => $letter,
                'ALLCHECKBOXFRONT' => $AllCheckBoxFront,
                'ALLCHECKBOXBACK' => $AllCheckBoxBack,
                'CLASSFRONT' => $classFront,
                'CLASSBACK' => $classBack
            ];
            $row_template->template("form/all_parts", $radio_check_array);
        }

        $html = $row_template->return();

        return $html;
    }
}
