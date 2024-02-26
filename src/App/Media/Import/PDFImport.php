<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Media\Import;

use CWP\Core\Media;
use CWP\Core\MediaLogger;
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
        $noPagess = \count($pdf);

        if (0 == $noPagess) {
            $this->deleteFromDatabase('media_job');
            $this->status = 2;

            return 2;
        }

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
            $config = new \Smalot\PdfParser\Config();

            $config->setFontSpaceLimit(-20);

            $parser = new Parser([], $config);

            $pdf = $parser->parseFile($file);
            $pages = $pdf->getPages();

            foreach ($pages as $page) {
                $text = $page->getText();
                $formRow = $this->cleanPdfText($text);
                $this->parse_page($formRow);
            }
        }
    }

    public function cleanPdfText($text)
    {
        $text = str_replace("\t", "','", $text);
        $page_text = explode("\n", $text);

        $formRow = [];
        $letter = '';
        foreach ($page_text as $n => $row) {
            $row = preg_replace('/\s+/', ' ', $row);
            if (Utils::contains($row, ['MNI', '(', 'Market', 'Tip Prod', 'Ltr Lap', '#', 'SHEETS'])) {
                if (preg_match('/(#[0-9]+)([A-Z,]+)/', $row, $match)) {
                    $letter = str_replace(',', '', $match[2]);
                }
                if (Utils::contains($row, ['Run#'])) {
                    continue;
                }
                unset($page_text[$n]);
                continue;
            }

            if (str_contains($row, "','")) {
                $text = "'".trim($row)."'";
                MediaLogger::file(__FUNCTION__.'.txt', '--------------------------------------');
                MediaLogger::file(__FUNCTION__.'.txt', $text);

                preg_match('/(.*)\'([a-zA-Z ]+)?([0-9 ,]+) ([ a-zA-Z]+)\'/', $text, $output_array);
                if (true == $output_array[2]) {
                    $output_array[2] = "'".$output_array[2]."',";
                }
                $text = $output_array[1].$output_array[2]."'".$output_array[3]."','".$output_array[4]."'";
                // $text = preg_replace('/\'([0-9]+),?([0-9]+)\s(.*)\'/', "'$1$2','$3'", $text);
                MediaLogger::file(__FUNCTION__.'.txt', $text);
                $text = preg_replace('/\'([a-zA-Z ]+), ([a-zA-Z ]+)\'/', "'$1','$2'", $text);
                MediaLogger::file(__FUNCTION__.'.txt', $text);

                $formRow[$letter][] = $text;
                unset($page_text[$n]);
            } else {
                $page_text[$n] = trim($row);
            }
        }

        $this->getFormDetails($page_text);

        return $formRow;
    }

    public function parse_page($formData)
    {
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

            foreach ($formData as $letter => $letter_array) {
                $form_rows[$letter] = $this->rowDdata($letter_array);
            }

            $this->form[$form_number]['forms'] = $form_rows;
        }
    }

    public function getFormDetails($page_text)
    {
        unset($this->PageDetails); // = [];
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

                continue;
            }
        }
    }

    public function rowDdata($form_row)
    {
        foreach ($form_row as $i => $rowData) {
            list($market, $pub, $count, $ship, $tip) = str_getcsv($rowData, ',', "'");
            MediaLogger::file(__FUNCTION__.'.txt', [$market, $pub, $count, $ship, $tip]);
            $rows[$i] = [
                'original' => $rowData,
                'market' => trim($market, "'"),
                'pub' => trim($pub, "'"),
                'count' => trim($count, "'"),
                'ship' => trim($ship, "'"),
                'tip' => trim($tip, "'"),
            ];
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
