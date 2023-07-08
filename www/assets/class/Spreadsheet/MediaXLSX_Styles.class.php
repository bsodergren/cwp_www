<?php


class set
{
    public static $offset;
    public static function row($letter, $row)
    {
        $offset = self::$offset - 1;
        $offset = $offset * 27;
        $row = $offset + (int)$row;
        return $letter.(string) $row;
    }
}

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
            $cell = Set::row('B', $row).':'.Set::row('C',$row);
            $this->setMerge($cell);
        }
        $merg_rows = [24,25,26];

        foreach($merg_rows as $row) {

            $cell = Set::row('A', $row).':'.Set::row('D',$row);
            $this->setMerge($cell);

        }

        $sql = "SELECT ecol,erow, text,bold,font_size,h_align,v_align FROM flag_style WHERE erow IS NOT NULL;";

        $result = $connection->fetchAll($sql);
        foreach ($result as $k => $val) {

            $col = Set::row($val['ecol'], $val['erow']);
            $text = $val['text'];
            $bold = $val['bold'];
            $font_size = $val['font_size'];
            $h_align = $val['h_align'];
            $v_align = $val['v_align'];


            if (isset($text)) {
                $this->setCellText($col, $text);
            }

            $this->setBold($col,$bold);
            $this->setSize(['cell'=>$col,'size'=>$font_size]);

            if ($h_align == 1) {
                $this->setAlign($col,'H');
            }

            if ($v_align == 1) {
                $this->setAlign($col,'V');
            }
        }

        #$this->obj->getStyle('A24')->getAlignment()->setShrinkToFit(true);

        $this->setBorder(set::row("A", 10), "bottom");
        $this->setBorder(set::row("A", 10), "right");
        $this->setBorder(set::row("B", 10), "bottom");

        $this->setNumberCode(set::row('B', 21),'#,##0');
        $this->setShrink(set::row('B', 7));
        $this->setPageBreak(set::row('A', 27));

        //	$this->obj->getStyle("B6:B10")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


    }




    public function addSheetData($value, $text, $row)
    {

        $textCell = Set::row("A", $row);
        $this->setCellText($textCell, $text);
        $this->setBorder($textCell);


        $valCell = Set::row("B", $row);
        $this->setCellText($valCell, $value);
        $this->setBorder($valCell);
    }


    public function setRowHeights()
    {
        $hn = count($this->rowHeight);
        for ($i = 0; $i < $hn; $i++) {
            $this->setHeight(['cell' => Set::row(null, $i+1), 'height' =>$this->rowHeight[$i]]);
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

        $this->setCellText(set::row('B', 6), $form['job_number']);
        $this->setCellText(set::row('B', 7), $form['market']);
        $this->setCellText(set::row('B', 8), $form['pub_value']);
        $this->setCellText(set::row('B', 9), $form['ship_value']);
        $this->setShrink(set::row('B', 7));

        $this->setCellText(set::row('D', 6), $form['form_number'] . "" . $form['form_letter']);

        $this->setBorder(set::row("A", 6) . ":" . set::row("C", 10), "allBorders");
        $this->setBorder(set::row("D", 6), "outline");


        $this->setCellText(set::row('D', 7), $form['page_conf']);

        $this->setCellText(set::row('B', 10), $form['packaging']);

        if(key_exists("skid_count", $form)) {
            $this->setCellText(set::row('D', 8), $form['skid_count']);
        }

        if ($form['bindery_trim']  == true)
        {
            $this->setCellText(set::row('A', 24), $form['ship_value']);
            $this->setCellText(set::row('A', 25), $form['ship_value']);
            $this->setCellText(set::row('A', 26), $form['ship_value']);
        }




    }

    public function createPage($form, $sheet_labels, $copies = 1)
    {

        $this->setColWidth();

        for ($i = 1; $i <= $copies; $i++) {
            set::$offset = $i;
            $this->setRowHeights();
            $this->sheetCommon();
            $this->addFormText($form);

            foreach ($sheet_labels as $key => $val) {
                $this->addSheetData($val[0], $val[1], $key);
            }
        }
    }
 

}
