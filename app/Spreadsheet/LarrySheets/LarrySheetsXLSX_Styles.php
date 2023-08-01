<?php
/**
 * CWP Media tool
 */

namespace CWP\Spreadsheet\LarrySheets;

use CWP\Spreadsheet\Styles;

/**
 * CWP Media tool.
 */

/**
 * CWP Media tool.
 */
class LarrySheetsXLSX_Styles extends Styles
{
    public $colWidth      = 0.875;
    public $colWidthUnits = 'in';
    public $totalPages;

    public $obj;
    public $totalRows;

    public $Columns       = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    public $rowHeight     = [5, 35, 35, 30, 30, 30, 25, 25, 5];

    public function sheetCommon()
    {
        $this->createSlipPage();

        $row_height      = $this->rowHeight;
        $SlipSheetBreak  = count($row_height);
        $PageBreak       = $SlipSheetBreak * 3;
        $this->totalRows = $this->totalPages * ($SlipSheetBreak * 3);

        $rowHeightIdx    = 0;
        $pageBreakIdx    = 1;
        $SlipSheetidx    = 1;
        $mergeidx        = 1;

        for ($row = 1; $row <= $this->totalRows; ++$row) {
            if ($rowHeightIdx == $SlipSheetBreak) {
                $rowHeightIdx = 0;
            }

            $styleProps['setHeight'][] = ['cell' => $row, 'height' => $row_height[$rowHeightIdx]];

            ++$rowHeightIdx;

            if (1 == $SlipSheetidx) {
                $leftboxStart  = 'A'.$row;
                $rightboxStart = 'E'.$row;
            }

            if ($SlipSheetBreak == $SlipSheetidx) {
                $leftboxEnd                = 'D'.$row;
                $rightboxEnd               = 'H'.$row;

                $styleProps['setBorder'][] = ['cell' => $leftboxStart.':'.$leftboxEnd,
                    'border'                         => 'outline',
                    'style'                          => 'BORDER_DOTTED'];
                $styleProps['setBorder'][] = ['cell' => $rightboxStart.':'.$rightboxEnd,
                'border'                             => 'outline',
                'style'                              => 'BORDER_DOTTED'];
                $SlipSheetidx              = 0;
            }

            if ($pageBreakIdx == $PageBreak) {
                $styleProps['setPageBreak'][] = 'A'.$row;
                $pageBreakIdx                 = 0;
            }

            switch ($mergeidx) {
                case 1:
                    $styleProps['setMerge'][]   = 'A'.$row.':D'.$row;
                    break;
                case 2:
                case 3:
                    $styleProps['setSize'][]    = ['cell' => 'A'.$row, 'size' => 26];
                    $styleProps['setBold'][]    = 'A'.$row;
                    $styleProps['setAlign'][]   = 'A'.$row;
                    $styleProps['setMerge'][]   = 'A'.$row.':D'.$row;
                    break;
                case 4:
                case 5:
                case 6:
                    $styleProps['setSize'][]    = ['cell' => 'A'.$row, 'size' => 22];
                    $styleProps['setSize'][]    = ['cell' => 'C'.$row, 'size' => 22];
                    $styleProps['setAlign'][]   = ['cell' => 'C'.$row];
                    $styleProps['setBold'][]    = 'A'.$row;
                    $styleProps['setIndent'][]  = 'A'.$row;
                    $styleProps['setMerge'][]   = 'A'.$row.':B'.$row;
                    $styleProps['setMerge'][]   = 'C'.$row.':D'.$row;

                    break;
                case 7:
                    $styleProps['setSize'][]    = ['cell' => 'A'.$row, 'size' => 16];
                    $styleProps['setBold'][]    = 'A'.$row;
                    $styleProps['setBold'][]    = 'A'.$row;
                    $styleProps['setShrink'][]  = 'A'.$row;
                    $styleProps['setAlign'][]   = 'A'.$row;
                    $styleProps['setMerge'][]   = 'A'.$row.':D'.$row;
                    break;

                case 8:
                    $styleProps['setSize'][]    = ['cell' => 'A'.$row, 'size' => 16];
                    $styleProps['setBold'][]    = 'A'.$row;
                    $styleProps['setBold'][]    = 'A'.$row;
                    $styleProps['setShrink'][]  = 'A'.$row;
                    // $styleProps['setAlign'][]   = 'A'.$row;
                    $styleProps['setAlign'][]   = ['cell' => 'A'.$row, 'style' => 'H', 'align' => 'C'];

                    $styleProps['setMerge'][]   = 'A'.$row.':B'.$row;
                    // $styleProps['setIndent'][]  = 'A'.$row;

                    $styleProps['setSize'][]    = ['cell' => 'C'.$row, 'size' => 16];
                    $styleProps['setBold'][]    = 'C'.$row;
                    $styleProps['setBold'][]    = 'C'.$row;
                    $styleProps['setShrink'][]  = 'C'.$row;
                    // $styleProps['setAlign'][]   = 'C'.$row;
                    $styleProps['setAlign'][]   = ['cell' => 'C'.$row, 'style' => 'H', 'align' => 'C'];
                    $styleProps['setMerge'][]   = 'C'.$row.':D'.$row;

                    break;

                default:
                    $mergeidx                   = 0;
                    break;
            }

            ++$mergeidx;

            ++$SlipSheetidx;
            ++$pageBreakIdx;
        }

        foreach ($styleProps as $method => $row) {
            foreach ($row as $id => $value) {
                $this->$method($value);
            }
        }
    }

    public function RightBlock($cell)
    {
        $LeftCols  = ['A', 'B', 'C', 'D'];
        $RightCols = ['E', 'F', 'G', 'H'];

        $r         = str_replace($LeftCols, $RightCols, $cell);

        return $r;
    }

    public function addSheetData($text, $cell)
    {
        $this->setCellText($cell, $text);
    }
}
