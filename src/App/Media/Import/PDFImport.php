<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Media\Import;

use CWP\Core\Media;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Utils\Utils;
use Smalot\PdfParser\Parser;

class PDFImport extends MediaImport
{
    public $form = [];
    public $PageDetails = [];

    public $job_id;

    private function insertDrop($pdf_file, $update_form)
    {
        $this->processPdf($pdf_file, $this->job_id, $update_form);

        $pdf = $this->form;

        if (0 == \count($pdf)) {
            $this->deleteFromDatabase('media_job');
            $this->status = 2;

            return 2;
        }
        $noPagess = \count($pdf);
        HTMLDisplay::pushhtml('stream/import/msg', ['TEXT' => 'Importing '.$noPagess.' forms']);

        $keyidx = array_key_first($pdf);
        Media::$explorer->table('media_job')->where( // UPDATEME
            'job_id',
            $this->job_id
        )->update([
            'close' => $pdf[$keyidx]['details']['product'],
        ]);

        foreach ($pdf as $form_number => $form_info) {
            HTMLDisplay::pushhtml('stream/import/file_msg', ['TEXT' => 'Importing form '.$form_number]);
            $this->add_form_details($form_info['details']);
            $this->add_form_data($form_number, $form_info);
        }
        $this->status = 1;

        return 1;
    }

    public function Import($pdf_uploaded_file = '', $job_number = 110011, $update_form = '')
    {
        // $pdf_uploaded_file = (new MediaFileSystem())->DownloadFile($pdf_uploaded_file);

        $this->job_id = Media::getJobNumber($pdf_uploaded_file, $job_number);
        if (null !== $this->job_id) {
            return 0;
        }
        $this->job_id = Media::insertJobNumber($pdf_uploaded_file, $job_number);

        $this->insertDrop($pdf_uploaded_file, $update_form);
    }

    public function reImport($pdf_uploaded_file = '', $job_number = 110011, $update_form = '')
    {
        $this->job_id = Media::getJobNumber($pdf_uploaded_file, $job_number);
        if (null === $this->job_id) {
            return 0;
        }

        $this->insertDrop($pdf_uploaded_file, $update_form);
    }

    public function processPdf($file = '', $media_job_id = '', $form_number = '')
    {
        if ('' != $media_job_id) {
            $this->job_id = $media_job_id;
        }
        if (file_exists($file)) {
            $parser = new Parser();
            $pdf = $parser->parseFile($file);
            $pages = $pdf->getPages();
            if ('' != $form_number) {
                --$form_number;
                $page_text = [];

                $text = $pages[$form_number]->getDataTm();
                $page_text = $this->cleanPdfText($text);
                $this->parse_page($page_text);
            } else {
                foreach ($pages as $page) {
                    $page_text = [];

                    $text = $page->getDataTm();

                    $page_text = $this->cleanPdfText($text);
                    $this->parse_page($page_text);
                }
            }
        }
    }

    public function cleanPdfText($text)
    {
        foreach ($text as $n => $row) {
            // $page_text[$n] = trim(str_replace("&","and", $row[1]));
            $page_text[$n] = trim($row[1]);
        }

        return $page_text;
    }

    public function parse_page($page_text)
    {
        $page_text = $this->getFormDetails($page_text);

        $form_number = $this->PageDetails['form'];
        $config_type = $this->PageDetails['config'];
        if (isset($config_type) && 'sheeter' == $config_type) {
            return;
        }

        if (isset($form_number)) {
            $this->form[$form_number]['details']['config'] = $config_type;
            $this->form[$form_number]['details']['bind'] = $this->PageDetails['bind'];

            $this->form[$form_number]['details']['count'] = $this->PageDetails['count'];

            $this->form[$form_number]['details']['product'] = $this->PageDetails['production'];
            $this->form[$form_number]['details']['job_id'] = $this->job_id;
            $this->form[$form_number]['details']['form_number'] = $form_number;

            $page_text = array_values($page_text);
            $page_count = \count($page_text);
            $pageStr = implode('|', $page_text);
            $page_Array = explode('#'.$form_number, $pageStr);
            unset($page_Array[0]);
            rsort($page_Array);
            unset($prevLetter);
            foreach ($page_Array as $i => $pageStr) {
                $pageArr = explode('|', $pageStr);

                $currentLtr = str_replace(',', '', $pageArr[0]);
                $currentLtr = str_replace(')', '', $currentLtr);

                array_shift($pageArr);

                if (isset($prevLetter)) {
                    $key = array_search($prevLetter, $pageArr, true);
                    $pageArr = array_slice($pageArr, 0, $key);
                }
                $pageArray[$currentLtr] = $pageArr;
                $prevLetter = $currentLtr;
            }
            ksort($pageArray);

            foreach ($pageArray as $letter => $letter_array) {
                $form_rows[$letter] = $this->rowDdata($letter_array);
            }
            $this->form[$form_number]['forms'] = $form_rows;
        }
    }

    public function getFormDetails($page_text)
    {
        dd($page_text);
        $this->PageDetails = [];
        foreach ($page_text as $k => $line) {
            unset($page_text[$k]);
            if (str_contains(strtolower($line), strtolower('production'))) {
                $printer_peices = explode(':', $line);
                $this->PageDetails['production'] = trim(str_replace('PRINTER', '', $printer_peices[1]));
                continue;
            }

            if (str_contains(strtolower($line), strtolower('run#'))) {
                $form_peices = explode('Run#', $line);
                $this->PageDetails['form'] = trim($form_peices[1]);
                continue;
            }

            if (str_contains(strtolower($line), strtolower('count'))) {
                $peices = explode(':', $line);
                $this->PageDetails['count'] = Utils::toint(trim($peices[1]));
                continue;
            }

            if (str_contains(strtolower($line), strtolower('bind'))) {
                $peices = explode(':', $line);
                $type = str_replace(' ', '', $peices[1]);

                $this->PageDetails['bind'] = trim($type);
                continue;
            }
            if (str_contains(strtolower($line), strtolower('config'))) {
                $peices = explode(':', $line);
                $type = str_replace(' ', '', $peices[1]);
                $type = $this->getPageCount($type);
                $this->PageDetails['config'] = trim($type);

                return $page_text;
            }
        }
    }

    public function rowDdata($form_row)
    {
        $tip = ' ';
        dump($form_row);
        $rowCount = count($form_row);
        if (($rowCount % 4) == 0) {
            $break = 3;
        } elseif (($rowCount % 5) == 0) {
            $break = 4;
        }

        $r = 0;
        $i = 0;
        for ($idx = 0; $idx <= $rowCount; ++$idx) {
            switch ($r) {
                case 0:
                    $market = $form_row[$idx];
                    break;
                case 1:
                    $pub = $form_row[$idx];
                    break;
                case 2:
                    $count = str_replace(',', '', $form_row[$idx]);
                    break;
                case 3:
                    $ship = $form_row[$idx];
                    break;
                case 4:
                    $tip = $form_row[$idx];
                    break;
            }

            if ($r < $break) {
                ++$r;
            } else {
                $row_array = [
                    'original' => $market.' '.$pub.' '.$count.' '.$ship,
                    'market' => $market,
                    'pub' => $pub,
                    'count' => $count,
                    'ship' => $ship,
                    'tip' => $tip,
                ];
                $r = 0;
                $rows[$i] = $row_array;
                ++$i;
            }
        }

        return $rows;
    }

    public function getPageCount($config_type)
    {
        switch ($config_type) {
            case '2+2pgs4out':
                return '4pg';
            case '2+4pgs2out':
                return '6pg';
            case '4pgs4out':
                return '4pg';
            case '4+2pgs2out':
                return '6pg';
            case '6pgs2out':
                return '6pg';
            case '4+4pgs2out':
                return '8pg';
            case '8pgs2out':
                return '8pg';
            default:
                return 'sheeter';
        }
    }
}
