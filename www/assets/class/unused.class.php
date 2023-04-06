<?php



/*

    public function create_form($url, $method, $input)
    {

        $html = '';
        $html .= '<form action="' . $url . '" method="' . $method . '">' . "\n";
        $html .= $input;
        $html .= "</form>\n";
        self::output($html);
    }


    public function add_submit_button($name, $value, $attributes = '')
    {
        $html = '';
        $html .= '<input ' . $attributes . ' type="submit" name="' . $name . '"  value="' . $value . '">';
        return $html . "\n";
    }

    public function add_hidden($name, $value, $attributes = '')
    {
        $html = '';
        $html .= '<input ' . $attributes . ' type="hidden" name="' . $name . '"  value="' . $value . '">';
        return $html . "\n";
    }

    public function draw_link($url, $text, $attributes = '', $return = true)
    {

        $html = '';
        $html .= '<a ' . $attributes . '  href="' . $url . '">' . $text . '</a>';
        if ($return == true) {
            return $html . "\n";
        } else {
            self::output($html);
        }
    }


    public function draw_textbox($name, $value, $attributes = '')
    {
        $html = '';
        $html .= '<input ' . $attributes . ' type="text" name="' . $name . '" placeholder="' . $value . '" value="' . $value . '">';
        return $html;
    }
*/


/*
    public function display_navbar_links()
    {

        global $navigation_link_array;
        global $_SERVER;
        $html = '';
        $dropdown_html = '';

        foreach ($navigation_link_array as $name => $link_array) {
            if ($name == "dropdown") {
                $dropdown_html = '';

                foreach ($link_array as $dropdown_name => $dropdown_array) {
                    $dropdown_link_html = '';

                    foreach ($dropdown_array as $d_name => $d_values) {
                        $array = array(
                            "DROPDOWN_URL_TEXT" => $d_name,
                            "DROPDOWN_URL" => $d_values
                        );
                        $dropdown_link_html .= process_template("menu_dropdown_link", $array);
                    }


                    $array = array(
                        "DROPDOWN_TEXT" => $dropdown_name,
                        "DROPDOWN_LINKS" => $dropdown_link_html
                    );

                    $dropdown_html .= process_template("menu_dropdown", $array);
                }
            } else {

                $array = array(
                    "MENULINK_URL" => $link_array["url"],
                    "MENULINK_JS" => $link_array["js"],
                    "MENULINK_TEXT" => $link_array["text"]
                );
                $url_text = process_template("menu_link", $array);

                if ($link_array["secure"] == true && $_SERVER['REMOTE_USER'] != "bjorn") {
                    $html = $html . $url_text . "\n";
                } else {
                    $html = $html . $url_text . "\n";
                }
            }
        }

        return $html . $dropdown_html;
    }
    */
    
    /*
    public  function display_paper_as_cols($array)
    {
        $html = process_template("form_letter_header", array("NUMBER" => "Paper Editor"));
        foreach ($array as $name => $v) {
            if ($name == "id") {
                continue;
            }
            if ($name == "paper_id") {
                $html .= '<input type="hidden" name="' . $name . '" value="' . $v . '">';
                continue;
            }

            if ($name == "paper_wieght" || $name == "paper_size"  || $name == "pages") {
                $html .=   "<tr><td>" . $name . "</td><td>$v</td></tr>" . "\n";
                continue;
            }
            $html .=   "<tr>
            <td>" . $name . "</td>\n
            <td><input type=\"text\" name=\"" . $name . "\"  placeholder=\"" . $v . "\" value=\"" . $v . "\"></td>
            </tr>\n";
        }

        return $html;
    }

    public function display_array_as_cols($array)
    {
        $html = process_template("form_letter_header", array("NUMBER" => "Paper Editor"));
        foreach ($array as $name => $v) {
            if ($name == "id") {
                $html .= '<input type="hidden" name="' . $name . '" value="' . $v . '">';
                continue;
            }

            $html .=   "<tr>
            <td>" . $name . "</td>\n
            <td><input type=\"text\" name=\"" . $name . "\"  placeholder=\"" . $v . "\" value=\"" . $v . "\"></td>
            </tr>\n";
        }

        return $html;
    }




    public function display_array_as_row($array)
    {
        $html = process_template("form_letter_header", array("NUMBER" => "Paper Editor")) . "\n";

        foreach ($array as $row => $values) {
            $html .= "\t<tr>" . "\n";
            foreach ($values as $k => $v) {

                if ($k == "id") {
                    $html .= "\t\t<td><a href=\"" . __FORM_URL__ . "?edit&id=" . $v . "\"> edit </a></td>" . "\n";
                    if (TITLE != "Paper Editor") {
                        $html .= "\t\t" . '<td><a href="' . __FORM_URL__ . '?delete&id=' . $v . '">Delete</a></td>' . "\n";
                    }
                } elseif ($k != "trim") {
                    $html .= "\t\t<td>" . $v . "</td>\n";
                }
            }
            $html .=  "\t</tr>" . "\n";
        }

        $html .= "\t" . '<tr><td colspan=2><a href="' . __FORM_URL__ . '?add"> Add new data </a></td></tr>' . "\n";

        return $html;
    }


    public function display_paper($paper_id)
    {
        global $db;

        $db->where("id", $paper_id);
        $paper_type = $db->getone("paper_type");

        $db->where("paper_id", $paper_id);
        $res = $db->getone("paper_count");

        output($paper_type['paper_wieght'] . "## - " .
            $paper_type['paper_size'] . " - " .
            $paper_type['pages'] . "pgs <br>");

        foreach ($res as $key => $value) {
            if ($key == "id" || $key == "paper_id") {
                $this->HTMLDisplay::output('<input type="hidden" name="paper_' . $paper_id . '_' . $key . '" value="' . $value . '"><br>');
            } else {
                $this->output($key . '<input type="text" name="paper_' . $paper_id . '_' . $key . '" placeholder="' . $value . '"><br>');
            }
        }
    }



    public function display_page()
    {

        return $html;
    }
*/

/*
    public function display_table_LetterHeader($number, $letter, $array)
    {
        $html =  process_template("form_letter_header", array("NUMBER" => $number, "LETTER" => $letter));
        $html .= $this->display_table_rows($array, $letter);
        return $html;
    }
*/