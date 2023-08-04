<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Import;

use CWP\HTML\HTMLDisplay;
use CWP\Media\Media;
use CWP\Utils\Utils;
use Smalot\PdfParser\Parser;

class OrigPDFImport extends MediaImport
{
    public $form = [];

    public $job_id;

    private function insertDrop($pdf_file, $update_form)
    {
        $this->processPdf($pdf_file, $this->job_id, $update_form);

        $pdf = $this->form;
        if (count($pdf) < 1) {
            $this->status = 0;

            return 0;
        }

        $noPagess = count($pdf);
        HTMLDisplay::pushhtml('stream/import/msg', ['TEXT' => 'Importing '.$noPagess.' forms']);

        $keyidx = array_key_first($pdf);
        $base_dir = dirname($pdf_file, 2);

        Media::$explorer->table('media_job')->where('job_id',
            $this->job_id)->update([
                'close' => $pdf[$keyidx]['details']['product'],
                'base_dir' => $base_dir,
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

    public function processPdf($pdf_file = '', $media_job_id = '', $form_number = '')
    {
        if ('' != $media_job_id) {
            $this->job_id = $media_job_id;
        }

        if (file_exists($pdf_file)) {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdf_file);
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
                    //                    $array = unpack('cchars/nint', $page_text[1]);
                    $this->parse_page($page_text);
                }
            }
        }
    }

    public function hex2str($hex)
    {
        $str = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $str .= chr(hexdec(substr($hex, $i, 2)));
        }

        return $str;
    }

    public function cleanPdfText($text)
    {
        foreach ($text as $n => $row) {
            $array = unpack('ch', $row[1]);

            // $page_text[$n] = trim(str_replace("&","and", $row[1]));
            $page_text[$n] = trim($row[1]);
        }
        dump(['original', $text]);

        return $page_text;
    }

    public function parse_page($page_text)
    {
        $page_count = count($page_text);
        dd($page_text);

        $form_number = $this->find_key('run#', $page_text);
        $config_type = $this->find_key('config', $page_text);

        if (isset($config_type) && 'sheeter' == $config_type) {
            return;
        }

        if (isset($form_number)) {
            $this->form[$form_number]['details']['config'] = $config_type;
            $this->form[$form_number]['details']['bind'] = $this->find_key('bind', $page_text);

            $this->form[$form_number]['details']['count'] = Utils::toint($this->find_key('count', $page_text));

            $this->form[$form_number]['details']['product'] = $this->find_key('production', $page_text);
            $this->form[$form_number]['details']['job_id'] = $this->job_id;
            $this->form[$form_number]['details']['form_number'] = $form_number;

            for ($idx = 0; $idx <= $page_count; ++$idx) {
                if (!isset($res)) {
                    $res = $this->find_first($form_number, $idx, $page_text);
                    $current_key = key($res);
                    $idx = $res[$current_key]['start'];
                } else {
                    $res2 = $this->find_end($form_number, $res, $page_text);
                    $form_number_array[] = $res2;
                    $r_letter = key($res2);
                    $idx = $res2[$r_letter]['stop'] + 1;
                    unset($res);
                }
            }
            foreach ($form_number_array as $_ => $letter_array) {
                $letter = key($letter_array);

                $start = $letter_array[$letter]['start'];
                $stop = $letter_array[$letter]['stop'];

                $form_rows[$letter] = $this->row_data($start, $stop, $page_text);
            }

            $this->form[$form_number]['forms'] = $form_rows;
        }
    }

    public function find_key($needle, $haystack, $value = '', $strict = false)
    {
        if ('' == $value) {
            $value = strtolower($needle);
        }

        foreach ($haystack as $k => $item) {
            if (true == $strict) {
                $item = trim(str_replace(',', '', $item));
                if ('letter' == $value) {
                    preg_match('/^[ABCD,]+\b/', $item, $matches);
                    if (count($matches) > 0) {
                        if ($matches[0] == trim($needle)) {
                            $search = true;
                        } else {
                            $search = false;
                        }
                    } else {
                        $search = false;
                    }
                } else {
                    $search = str_starts_with(strtolower($item), strtolower($needle));
                }
            } else {
                $search = strpos(strtolower($item), strtolower($needle));
            }

            if (false !== $search) {
                switch ($value) {
                    case 'run#':
                        $form_peices = explode('Run#', $item);

                        return trim($form_peices[1]);

                    case 'production':
                        $printer_peices = explode(':', $item);

                        return trim(str_replace('PRINTER', '', $printer_peices[1]));

                    case 'count':
                        $peices = explode(':', $item);

                        return Utils::toint(trim($peices[1]));

                    case 'config':
                        $peices = explode(':', $item);
                        $type = str_replace(' ', '', $peices[1]);
                        $type = $this->getPageCount($type);

                        return trim($type);

                    case 'bind':
                        $peices = explode(':', $item);
                        $type = str_replace(' ', '', $peices[1]);

                        return trim($type);

                    case 'letter':
                        return $k;

                    case 'key':
                        return $k;
                }
                break;
            }
        }
    }

    public function find_first($form_number, $start, $array)
    {
        $result = [];
        $row_count = count($array);
        $array = array_slice($array, $start, $row_count, true);

        $key = $this->find_key('#'.$form_number, $array, 'key');

        if ($key) {
            $peices = explode('#'.$form_number, $array[$key]);
            $letter = str_replace(',', '', $peices[1]);
            $result = [$letter => ['start' => $key + 1]];

            return $result;
        }
    }

    public function find_end($form_number, $start_array, $array)
    {
        $result = [];

        $row_count = count($array);
        $letter = key($start_array);

        $start_array[$letter]['stop'] = $row_count - 1;
        $start = $start_array[$letter]['start'] + 1;
        $array = array_slice($array, $start, $row_count, true);

        $key = $this->find_key('#'.$form_number, $array, 'key');

        if (null != $key) {
            $peices = explode('#'.$form_number, $array[$key]);

            $t_letter = str_replace(',', '', $peices[1]);
            if (null != $t_letter) {
                $stop_key = $this->find_key($t_letter, $array, 'letter', true);
                $start_array[$letter]['stop'] = $stop_key - 1;
            }
        }

        return $start_array;
    }

    public function row_data($start, $stop, $page_text)
    {
        $tip = ' ';
        if (((($stop + 1) - $start) % 4) == 0) {
            $break = 3;
        } elseif (((($stop + 1) - $start) % 5) == 0) {
            $break = 4;
        }

        $r = 0;
        $i = 0;
        for ($idx = $start; $idx <= $stop; ++$idx) {
            switch ($r) {
                case 0:
                    $market = $page_text[$idx];
                    break;
                case 1:
                    $pub = $page_text[$idx];
                    break;
                case 2:
                    $count = str_replace(',', '', $page_text[$idx]);
                    break;
                case 3:
                    $ship = $page_text[$idx];
                    break;
                case 4:
                    $tip = $page_text[$idx];
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
