<?php
/**
 * CWP Media tool
 */

namespace CWP\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * CWP Media tool.
 */

/**
 * CWP Media tool.
 */
class Styles
{
    public static $offset;
    public $obj;

    public static function row($letter, $row)
    {
        $offset = self::$offset - 1;
        $offset = $offset * 27;
        $row = $offset + (int) $row;

        return $letter.(string) $row;
    }

    public function __construct(&$object)
    {
        $this->obj = $object;
    }

    public function createSlipPage()
    {
        $this->obj->getPageSetup()->setHorizontalCentered(true);
        $this->obj->getPageSetup()->setVerticalCentered(false);
        $this->obj->getPageMargins()->setTop(0.25);
        $this->obj->getPageMargins()->setRight(0.25);
        $this->obj->getPageMargins()->setLeft(0.25);
        $this->obj->getPageMargins()->setBottom(0.25);
    }

    public function setNumberCode($cell, $code = null)
    {
        $this->obj->getStyle($cell)->getNumberFormat()->setFormatCode($code);
    }

    public function setShrink($cell)
    {
        $this->obj->getStyle($cell)->getAlignment()->setShrinkToFit(true);
    }

    public function setIndent($cell)
    {
        $this->obj->getStyle($cell)->getAlignment()->setIndent(1);
    }

    public function setPageBreak($cell)
    {
        $this->obj->setBreak($cell, Worksheet::BREAK_ROW);
    }

    public function setCellText($cell, $text)
    {
        $this->obj->setCellValue($cell, $text);
    }

    public function setHeight($array)
    {
        $this->obj->getRowDimension($array['cell'])->setRowHeight($array['height']);
    }

    public function setBorder($array)
    {
        $cell = $array;
        $border = 'outline';
        $style = 'BORDER_THIN';

        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $$k = $v;
            }
        }

        $this->cellBorder($cell, $border, $style);
    }

    public function setColWidths($columns = [], $width = 12, $unit = null)
    {
        if (isset($this->colWidthUnits)) {
            $unit = $this->colWidthUnits;
        }

        foreach ($columns as $id => $data) {
            if (is_array($data)) {
                if (key_exists('column', $data)) {
                    $col = $data['column'];
                }
                if (key_exists('width', $data)) {
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

        if (method_exists(get_called_class(), 'RightBlock')) {
            $cell = $this->RightBlock($array['cell']);
            $cellArray[] = ['cell' => $cell, 'size' => $array['size']];
        }
        foreach ($cellArray as $n => $cellData) {
            $this->obj->getStyle($cellData['cell'])->getFont()->setSize($cellData['size']);
        }
    }

    public function setBold($cell, $bold = 1)
    {
        $cellArray[] = $cell;
        if (method_exists(get_called_class(), 'RightBlock')) {
            $cellArray[] = $this->RightBlock($cell);
        }

        foreach ($cellArray as $n => $cell) {
            $this->obj->getStyle($cell)->getFont()->setBold($bold);
        }
        //        $this->obj->getStyle($this->RightBlock($cell))->getFont()->setBold(1);
    }

    public function setAlign($cell, $style = 'H', $align = 'C')
    {
        if (is_array($cell)) {
            $tmpCellArray = $cell;
            unset($cell);
            foreach ($tmpCellArray as $var => $value) {
                $$var = $value;
            }
        }

        $cellArray[] = $cell;
        if (method_exists(get_called_class(), 'RightBlock')) {
            $cellArray[] = $this->RightBlock($cell);
        }

        $replace = ['H' => 'HORIZONTAL', 'V' => 'VERTICAL'];
        $style_c = strtr($style, $replace);

        $replace = ['C' => 'CENTER', 'L' => 'LEFT', 'R' => 'RIGHT', 'T' => 'TOP', 'B' => 'BOTTOM'];
        $align_c = strtr($align, $replace);

        $constant = constant("\PhpOffice\PhpSpreadsheet\Style\Alignment::".strtoupper($style_c).'_'.strtoupper($align_c));

        foreach ($cellArray as $n => $cell) {
            if ('H' == $style) {
                $this->obj->getStyle($cell)->getAlignment()->setHorizontal($constant);
            }
            if ('V' == $style) {
                $this->obj->getStyle($cell)->getAlignment()->setVertical($constant);
            }
        }
    }

    public function setMerge($cell)
    {
        $cellArray[] = $cell;
        if (method_exists(get_called_class(), 'RightBlock')) {
            $cellArray[] = $this->RightBlock($cell);
        }

        foreach ($cellArray as $n => $cell) {
            $this->obj->mergeCells($cell);
        }
    }

    public function cellBorder($cell, $border = 'outline', $style = 'BORDER_THICK')
    {
        $styleArray = [
            'borders' => [
                $border => [
                    'borderStyle' => constant('\PhpOffice\PhpSpreadsheet\Style\Border::'.$style),
                    // 'borderStyle' => $style,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $this->obj->getStyle($cell)->applyFromArray($styleArray);
    }
}
