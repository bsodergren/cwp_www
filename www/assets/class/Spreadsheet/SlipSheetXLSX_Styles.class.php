<?php


class SlipSheetXLSX_Styles extends Styles
{
    public $colWidth = 0.875;
    public $colWidthUnits = 'in';
    public $totalPages;

    public $obj;
    public $totalRows;


    public $Columns = ['A','B','C','D','E','F','G','H'];
    public $rowHeight = [5,30,30,25,25,30,30,30,5];


   


    public function sheetCommon()
    {
        global $connection;
      

        $this->obj->getPageSetup()->setHorizontalCentered(true);
        $this->obj->getPageSetup()->setVerticalCentered(false);
        $this->obj->getPageMargins()->setTop(0.25);
        $this->obj->getPageMargins()->setRight(0.25);
        $this->obj->getPageMargins()->setLeft(0.25);
        $this->obj->getPageMargins()->setBottom(0.25);


        $row_height = $this->rowHeight;
        $SlipSheetBreak = count($row_height);
        $PageBreak = $SlipSheetBreak;
        $this->totalRows = $this->totalPages * ($SlipSheetBreak * 3);


        $rowHeightIdx = 0;
        $pageBreakIdx = 1;
        $SlipSheetidx = 1;
        $mergeidx = 1;


        for($row=1; $row <= $this->totalRows; $row++) {

            if($rowHeightIdx == $SlipSheetBreak ) {
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

            switch($mergeidx) {
                
                case 1:
                    $styleProps['setMerge'][] = 'A'.$row.":".'D'.$row;
                    break;
                case 2:

                    case 3:
                    $styleProps['setSize'][] = ['cell' => 'A'.$row, 'size' => 32];
                    $styleProps['setBold'][] = 'A'.$row;
                    $styleProps['setAlign'][] = 'A'.$row;
                    $styleProps['setMerge'][] = 'A'.$row.":".'D'.$row;
                    break;

                
                case 4:
                    case 5:
                    $styleProps['setSize'][] = ['cell' => 'A'.$row, 'size' => 16];
                    $styleProps['setBold'][] = 'A'.$row;
                    $styleProps['setAlign'][] = 'A'.$row;
                    $styleProps['setAlign'][] = 'A'.$row;
                    $styleProps['setMerge'][] = 'A'.$row.":".'D'.$row;
                    break;

                case 6: 
                    case 7:
                         case 8:
                    $styleProps['setSize'][] = ['cell' => 'A'.$row, 'size' => 26];
                    $styleProps['setSize'][] = ['cell' => 'C'.$row, 'size' => 26];
                    $styleProps['setAlign'][] = 'C'.$row;
                    $styleProps['setBold'][] = 'A'.$row;
                    $styleProps['setMerge'][] = 'A'.$row.":".'B'.$row;
                    $styleProps['setMerge'][] = 'C'.$row.":".'D'.$row;
                    break;

                default:
                    $mergeidx = 0;
                    break;
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

 



    public function RightBlock($cell)
    {
        $LeftCols = ['A','B','C','D'];
        $RightCols = ['E','F','G','H'];

        $r = str_replace($LeftCols, $RightCols, $cell);
        return $r;

    }

    public function addSheetData($text, $cell)
    {
        $this->setCellText($cell, $text);
    }


 

}
