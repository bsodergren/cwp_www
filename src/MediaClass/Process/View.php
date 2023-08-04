<?php
/**
 * CWP Media tool
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\MediaMailer;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Spreadsheet\XLSXViewer;
use Symfony\Component\Finder\Finder;

class View extends MediaProcess
{
    public $form_number;
    public $page_end;
    public function header()
    {
        define('TITLE', 'Updating excel sheet');
        include_once __LAYOUT_HEADER__;
        Template::echo('stream/start_page', []);
        $this->page_end = Template::GetHTML('stream/end_page', []);
    }

    public function footer()
    {
        echo $this->page_end;
        include_once __LAYOUT_FOOTER__;
    }

    public function run($req)
    {
        $this->form_number = $req['form_number'];
        $method = $req['action'];

        $this->$method();
    }

    public function send()
    {
        global $_POST;

        $finder = new Finder();

        $finder->files()->in($this->media->xlsx_directory)->name('*.xlsx')->notName('~*')->sortByName(true);

        if (!$finder->hasResults()) {
            XLSXViewer::checkifexist($this->media);
        }

        foreach ($finder as $file) {
            preg_match('/.*_([FM0-9]+).xlsx/', $file->getRealPath(), $output_array);
            [$text_form,$text_number] = explode('FM', $output_array[1]);

            if ($this->form_number == $text_number) {
                $pdf_file = $file->getRealPath();
            }
        }

        $sendto = $_POST['mailto'];
        $sendname = $_POST['rcpt_name'];

        $mail = new MediaMailer();
        $mail->recpt($sendto, $sendname);

        $mail->attachment($pdf_file);
        $mail->subject($product.' '.$job_number);
        $mail->Body(Template::GetHTML('mail/body', ['PRODUCT_NAME' => $product, 'JOB_NUMBER' => $job_number]));
        $mail->mail();

        $this->msg = 'XLSX File emailed';
        $this->url = '/view.php?job_id='.$this->job_id.'&form_number='.$this->form_number;
    }

    public function update()
    {
        $this->header();
        $this->media->excelArray($this->form_number);
        $this->media->deleteFromDatabase('form_data_count', $this->form_number);
        $excel = new MediaXLSX($this->media);
        $excel->writeWorkbooks();

        ob_flush();
        $msg = 'XLSX Files Created';
        $this->msg;
        $this->url = '/view.php?job_id='.$this->job_id.'&form_number='.$this->form_number;
    }
}
