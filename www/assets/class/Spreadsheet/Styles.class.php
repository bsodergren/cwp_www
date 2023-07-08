<?php


class styles
{
    public function __construct(&$object)
    {
        $this->obj = $object;
    }

    public function setNumberCode($cell, $code)
    {
        $this->obj->getStyle($cell)->getNumberFormat()->setFormatCode($code);

    }

    public function setShrink($cell)
    {
        $this->obj->getStyle($cell)->getAlignment()->setShrinkToFit(true);

    }

    public function setPageBreak($cell)
    {
        $this->obj->setBreak($cell, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    public function setCellText($cell, $text)
    {

        $this->obj->setCellValue($cell, $text);
    }

    public function setHeight($array)
    {
        $this->obj->getRowDimension($array['cell'])->setRowHeight($array['height']);
    }

    public function setBorder($cell, $border = "outline")
    {
        $this->cellBorder($cell, $border);
    }

    public function setColWidths($columns=[], $width=12, $unit=null)
    {

        if(isset($this->colWidthUnits)) {
            $unit = $this->colWidthUnits;
        }

        foreach($columns as $id => $data) {
            if(is_array($data)) {
                if(key_exists('column', $data)) {
                    $col = $data['column'];
                }
                if(key_exists('width', $data)) {
                    $width = $data['width'];
                } else {
                    $width = $this->colWidth;
                }
            } else {
                $col = $data;
                $width = $this->colWidth;
            }
            $this->obj->getColumnDimension($col)->setWidth($width, $unit);
        }
    }


    public function setSize($array)
    {
        $cellArray[] = $array;


        if(method_exists(get_called_class(), 'RightBlock'))
        {
            $cell= $this->RightBlock($array['cell']);
            $cellArray[] = ['cell' => $cell, 'size'=> $array['size']];
        }
        foreach($cellArray as $n => $cellData){
            $this->obj->getStyle($cellData['cell'])->getFont()->setSize($cellData['size']);
        }
    }

    public function setBold($cell, $bold = 1)
    {
        $cellArray[] = $cell;
        if(method_exists(get_called_class(), 'RightBlock')){
            $cellArray[] = $this->RightBlock($cell);
        }

        foreach($cellArray as $n => $cell){
        $this->obj->getStyle($cell)->getFont()->setBold($bold);
        }
        //        $this->obj->getStyle($this->RightBlock($cell))->getFont()->setBold(1);



    }
    public function setAlign($cell, $style = 'H')
    {
        $cellArray[] = $cell;
        if(method_exists(get_called_class(), 'RightBlock')){
            $cellArray[] = $this->RightBlock($cell);
        }

        foreach($cellArray as $n => $cell){
        if($style == 'H') {
            $this->obj->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
        if($style == 'V') {
            $this->obj->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
    }
    }

    public function setMerge($cell)
    {
        $cellArray[] = $cell;
        if(method_exists(get_called_class(), 'RightBlock')){
            $cellArray[] = $this->RightBlock($cell);
        }

        foreach($cellArray as $n => $cell){

            $this->obj->mergeCells($cell);
        }

    }


    







    public function cellBorder($cell, $border = "outline")
    {
        $styleArray = [
            'borders' => [
                $border => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '00000000'],
                ],

            ],
        ];

        $this->obj->getStyle($cell)->applyFromArray($styleArray);
    }










}
