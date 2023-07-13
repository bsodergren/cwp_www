<?php


class MediaXLSX_Styles extends Styles
{
    public $obj;
    public $rowHeight = [
        14.5, 14.5, 14.5, 14.5, 14.5, //Blank lines, row 1-5
        62, //6 Job Number,
        30, //7 Market
        30, //8 Magazine,
        30, //9 Destination,
        30, //10 Packaging,
        10, //11 Blank
        15, //12 Blank

        // 13-15, packing info
        20, 20, 20,
        // 16-21  box info
        20, 20, 20, 20, 20, 20,

        20, 20, // 22,23 blank

        // 24,25,26 //Bindery notes
        50, 50, 50,

        // 27 new page break
        10
    ];

    public function __construct(&$object)
    {
        $this->obj = $object;
    }

    public function sheetCommon()
    {
        global $connection;



        $merg_rows = [6,7,8,9,10];

        foreach($merg_rows as $row) {
            $cell = Styles::row('B', $row).':'.Styles::row('C', $row);
            $this->setMerge($cell);
        }


        $sql = "SELECT ecol,erow, text,bold,font_size,h_align,v_align FROM flag_style WHERE erow IS NOT NULL;";

        $result = $connection->fetchAll($sql);
        foreach ($result as $k => $val) {

            $col = Styles::row($val['ecol'], $val['erow']);
            $text = $val['text'];
            $bold = $val['bold'];
            $font_size = $val['font_size'];
            $h_align = $val['h_align'];
            $v_align = $val['v_align'];


            if (isset($text)) {
                $this->setCellText($col, $text);
            }

            $this->setBold($col, $bold);
            $this->setSize(['cell'=>$col,'size'=>$font_size]);

            if ($h_align == 1) {
                $this->setAlign($col, 'H');
            }

            if ($v_align == 1) {
                $this->setAlign($col, 'V');
            }
        }


        $this->setBorder(['cell'=>Styles::row("A", 10), 'border'=>"bottom"]);
        $this->setBorder(['cell'=>Styles::row("A", 10),  'border'=>"right"]);
        $this->setBorder(['cell'=>Styles::row("B", 10),  'border'=>"bottom"]);

        $this->setNumberCode(Styles::row('B', 21), '#,##0');
        $this->setShrink(Styles::row('B', 7));
        $this->setPageBreak(Styles::row('A', 27));



    }




    public function addSheetData($value, $text, $row)
    {

        $textCell = Styles::row("A", $row);
        $this->setCellText($textCell, $text);
        $this->setBorder($textCell);


        $valCell = Styles::row("B", $row);
        $this->setCellText($valCell, $value);
        $this->setBorder($valCell);
    }


    public function setRowHeights()
    {
        $hn = count($this->rowHeight);
        for ($i = 0; $i < $hn; $i++) {
            $this->setHeight(['cell' => Styles::row(null, $i+1), 'height' =>$this->rowHeight[$i]]);
        }
    }

    public function setColWidth($style = "Load Flag")
    {
        global $connection;
        $result = $connection->fetchAll("SELECT ecol,width FROM flag_style WHERE erow IS NULL and style_name = '" . $style . "' ORDER BY ecol ASC");


        foreach ($result as $k => $v) {
            $columns[] = ['column'=>$v['ecol'],'width'=>$v['width']];
        }
        $this->setColWidths($columns);
    }


    public function addFormText($form)
    {

        $this->setCellText(Styles::row('B', 6), $form['job_number']);
        $this->setCellText(Styles::row('B', 7), $form['market']);
        $this->setCellText(Styles::row('B', 8), $form['pub_value']);
        $this->setCellText(Styles::row('B', 9), $form['ship_value']);
        $this->setShrink(Styles::row('B', 7));

        $this->setCellText(Styles::row('D', 6), $form['form_number'] . "" . $form['form_letter']);
        $this->setAlign(Styles::row('D', 6), 'HV');

        $this->setBorder(['cell'=>Styles::row("A", 6) . ":" . Styles::row("C", 10),  'border'=>"allBorders"]);
        $this->setBorder(['cell'=>Styles::row("D", 6),  'border'=>"outline"]);


        $this->setCellText(Styles::row('D', 7), $form['page_conf']);

        $this->setCellText(Styles::row('B', 10), $form['packaging']);

        if(key_exists("skid_count", $form)) {
            $this->setCellText(Styles::row('D', 8), $form['skid_count']);
        }

        if ($form['bindery_trim']  == true) {


            $merg_rows = [24,25,26];

            foreach($merg_rows as $row) {

                $cell = Styles::row('A', $row).':'.Styles::row('D', $row);
                $this->setMerge($cell);

            }

            $this->setBorder(['cell'=>Styles::row("A", 24) . ":" . Styles::row("D", 26),  'border'=>"outline"]);

            $this->setCellText(Styles::row('A', 24), $form['ship_value']);
            $this->setCellText(Styles::row('A', 25), $form['ship_value']);
            $this->setCellText(Styles::row('A', 26), $form['ship_value']);
        }


        if ($form['head_trim']  != 0) {

            $deliveryInst[24] = ["text" => "Head Trim", "value" => $form['head_trim']];
        }

        if ($form['foot_trim']  != 0) {
            $deliveryInst[25] = ["text" => "Foot Trim", "value" => $form['foot_trim']];
        }

        if ($form['del_size']  != 0) {
            $deliveryInst[26] = ["text" => "Delivered Size", "value" => $form['del_size']];
        }


        if(is_array($deliveryInst)) {

            $this->setBorder(['cell'=>Styles::row("A", 24) . ":" . Styles::row("D", 26),  'border'=>"outline"]);
            foreach($deliveryInst as $row => $data) {

                $this->setSize(['cell'=>Styles::row('A', $row),'size'=>12]);
                $this->setCellText(Styles::row('A', $row), $data['text']);
                $this->setAlign(Styles::row('A', $row), 'HV');

                $this->setSize(['cell'=>Styles::row('B', $row),'size'=>12]);
                $this->setCellText(Styles::row('B', $row), $data['value']);
                $this->setAlign(Styles::row('B', $row), 'HV');

            }


        }

    }

    public function createPage($form, $sheet_labels, $copies = 1)
    {

        $this->setColWidth();

        for ($i = 1; $i <= $copies; $i++) {
            Styles::$offset = $i;
            $this->setRowHeights();
            $this->sheetCommon();
            $this->addFormText($form);

            foreach ($sheet_labels as $key => $val) {
                $this->addSheetData($val[0], $val[1], $key);
            }
        }
    }


}
