<?php


class MediaXLSX_Styles extends MediaXLSX
{


    public function __construct(&$object)
    {
        $this->obj = $object;
    }


    public function sheetCommon()
    {
        global $connection;

        $this->obj->mergeCells('B6:C6');
        $this->obj->mergeCells('B7:C7');
        $this->obj->mergeCells('B8:C8');
        $this->obj->mergeCells('B9:C9');
        $this->obj->mergeCells('B10:C10');

        $this->obj->mergeCells('A24:D24');
        $this->obj->mergeCells('A25:D25');
        $this->obj->mergeCells('A26:D26');

        $sql = "SELECT ecol ||  erow as location, text,bold,font_size,h_align,v_align FROM flag_style WHERE erow IS NOT NULL;";

        $result = $connection->fetchAll($sql);
        foreach ($result as $k => $val) {

            $col = $val['location'];
            $text = $val['text'];
            $bold = $val['bold'];
            $font_size = $val['font_size'];
            $h_align = $val['h_align'];
            $v_align = $val['v_align'];


            if (isset($text)) {
                $this->obj->setCellValue($col, $text);
            }

            $this->obj->getStyle($col)->getFont()->setBold($bold);
            $this->obj->getStyle($col)->getFont()->setSize($font_size);

            if (isset($h_align)) {
                $this->obj->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            if (isset($v_align)) {
                $this->obj->getStyle($col)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
        }

        #$this->obj->getStyle('A24')->getAlignment()->setShrinkToFit(true);
        $this->obj->getStyle('B7')->getAlignment()->setShrinkToFit(true);


        //	$this->obj->getStyle("B6:B10")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


    }




    public function cellBorder($cell, $border = "outline")
    {
        $styleArray = [
            'borders' => [
                $border => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $this->obj->getStyle($cell)->applyFromArray($styleArray);
    }


    public function addSheetData( $cell_value, $cell_text, $text_col, $val_col)
    {


        $this->obj->setCellValue($text_col, $cell_text);
        $this->obj->setCellValue($val_col, $cell_value);
        $this->cellBorder( $text_col);
        $this->cellBorder( $val_col);
    }


    public function setRowHeights()
    {

        $row_height = array(14.5, 14.5, 14.5, 14.5, 14.5, 62, 30, 30, 30, 30, 10, 15, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 50, 50, 50, 10, 10, 10, 10, 10);
        $hn = count($row_height);
        for ($i = 0; $i < $hn; $i++) {
            $this->obj->getRowDimension($i + 1)->setRowHeight($row_height[$i]);
        }
    }

    public function setColWidths($style = "Load Flag")
    {
        global $connection;
        $result = $connection->fetchAll("SELECT ecol,width FROM flag_style WHERE erow IS NULL and style_name = '" . $style . "' ORDER BY ecol ASC");


        foreach ($result as $k => $v) {
            $this->obj->getColumnDimension($v['ecol'])->setWidth($v['width']);
        }
    }
}
