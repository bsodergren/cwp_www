<?php


class SlipSheetXLSX_Styles extends SlipSheetXLSX
{
    public $colWidth = 0.875;
    public $totalPages;

    public function __construct(&$object)
    {
        $this->obj = $object;
    }


    public function sheetCommon()
    {
        global $connection;
        $w = 40;
        $w2 = 10;

        $this->obj->getPageSetup()->setHorizontalCentered(true);
        $this->obj->getPageSetup()->setVerticalCentered(false);
        $this->obj->getPageMargins()->setTop(0.25);
        $this->obj->getPageMargins()->setRight(0.25);
        $this->obj->getPageMargins()->setLeft(0.25);
        $this->obj->getPageMargins()->setBottom(0.25);

        $this->totalRows = $this->totalPages * 21;

        $PageBreak = 21;
        $pageBreakIdx = 1;

        $SlipSheetBreak = 7;
        $SlipSheetidx = 1;

        $row_height = array($w,$w,$w,$w,$w,$w,$w2);
        $rowHeightIdx = 0;

        $mergeidx = 1;

        for($row=1; $row <= $this->totalRows; $row++) {

            if($rowHeightIdx == count($row_height)) {
                $rowHeightIdx = 0;
            }

            $styleProps['setHeight'][] = ['cell' => $row,'height' => $row_height[$rowHeightIdx]];


            $rowHeightIdx++;

            if($SlipSheetidx == 1) {
                $leftboxStart = 'A'.$row;
                $rightboxStart = 'E'.$row;
            }

            if($SlipSheetBreak == $SlipSheetidx) {
                $leftboxEnd = 'D'.$row;
                $rightboxEnd = 'H'.$row;

                $styleProps['setBorder'][] = $leftboxStart.":".$leftboxEnd;
                $styleProps['setBorder'][] = $rightboxStart.":".$rightboxEnd;

                $SlipSheetidx = 0;

            }

            if($pageBreakIdx == $PageBreak) {
                $styleProps['setPageBreak'][] = "A".$row;
                $pageBreakIdx = 0;
            }

            if($mergeidx >= 1 && $mergeidx <= 2) {

                $styleProps['setFont'][] = ['cell' => 'A'.$row, 'size' => 32];
                $styleProps['setBold'][] = 'A'.$row;
                $styleProps['setAlign'][] = 'A'.$row;
                $styleProps['setMerge'][] = 'A'.$row.":".'D'.$row;

            }

            if($mergeidx == 3) {
                $styleProps['setFont'][] = ['cell' => 'A'.$row, 'size' => 16];
                $styleProps['setBold'][] = 'A'.$row;
                $styleProps['setAlign'][] = 'A'.$row;
                $styleProps['setAlign'][] = 'A'.$row;
                $styleProps['setMerge'][] = 'A'.$row.":".'D'.$row;
            }

            if($mergeidx >= 4 && $mergeidx <= 6) {
                $styleProps['setFont'][] = ['cell' => 'A'.$row, 'size' => 26];
                $styleProps['setFont'][] = ['cell' => 'C'.$row, 'size' => 26];
                $styleProps['setAlign'][] = 'C'.$row;
                $styleProps['setBold'][] = 'A'.$row;
                $styleProps['setMerge'][] = 'A'.$row.":".'B'.$row;
                $styleProps['setMerge'][] = 'C'.$row.":".'D'.$row;

            }

            if($mergeidx == 7) {
                $mergeidx = 0;
            }
            $mergeidx++;



            $SlipSheetidx++;
            $pageBreakIdx++;
        }

        foreach($styleProps as $method => $row) {
            foreach($row as $id => $value) {
                $this->$method($value);
            }

        }

    }

    public function setPageBreak($cell)
    {
        $this->obj->setBreak($cell, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
    }
    public function setHeight($array)
    {
        $this->obj->getRowDimension($array['cell'])->setRowHeight($array['height']);
    }
    public function setBorder($cell)
    {
        $this->cellBorder($cell, "outline");
    }


    public function setFont($array)
    {
        $this->obj->getStyle($array['cell'])->getFont()->setSize($array['size']);
        $this->obj->getStyle($this->RightBlock($array['cell']))->getFont()->setSize($array['size']);
    }

    public function setBold($cell)
    {
        $this->obj->getStyle($cell)->getFont()->setBold(1);
        $this->obj->getStyle($this->RightBlock($cell))->getFont()->setBold(1);



    }
    public function setAlign($cell)
    {
        $this->obj->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $this->obj->getStyle($this->RightBlock($cell))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    }

    public function setMerge($cell)
    {
        $this->obj->mergeCells($cell);
        $this->obj->mergeCells($this->RightBlock($cell));


    }

    public function RightBlock($cell)
    {
        $LeftCols = ['A','B','C','D'];
        $RightCols = ['E','F','G','H'];

        $r = str_replace($LeftCols, $RightCols, $cell);
        return $r;

    }

    /*
            $this->obj->mergeCells('B6:C6');
            $this->obj->mergeCells('B7:C7');

            $this->obj->setCellValue($col, $text);

            $this->obj->getStyle($col)->getFont()->setBold($bold);
            $this->obj->getStyle($col)->getFont()->setSize($font_size);


            $this->obj->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $this->obj->getStyle($col)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    */







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


    public function addSheetData($text, $cell)
    {
        $this->obj->setCellValue($cell, $text);
    }

    public function setColWidths()
    {
        $cols = ['A','B','C','D','E','F','G','H'];
        foreach($cols as $id) {
            $this->obj->getColumnDimension($id)->setWidth($this->colWidth, 'in');
        }

    }
}
