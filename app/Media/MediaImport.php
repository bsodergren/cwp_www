<?php
namespace CWP\Media;
/**
 * CWP Media tool
 */

use coderofsalvation\BrowserStream;
use CWP\Media\Media;
use CWP\PDFImport;

class MediaImport extends Media
{
    public $job_id = '';

    public $status = '';

    protected $conn;

    protected $exp;

    public function __construct($pdf_uploaded_file = '', $job_number = 110011, $update_form = '')
    {
        global $connection;
        global $explorer;

        $this->conn   = $connection;
        $this->exp    = $explorer;

        $pdf_filename = basename($pdf_uploaded_file);

        $base_dir     = dirname($pdf_uploaded_file, 2);

        $this->job_id = media::getJobNumber($pdf_filename, $job_number);

        $pdfObj       = new PDFImport($pdf_uploaded_file, $this->job_id, $update_form);

        $pdf          = $pdfObj->form;
        if (count($pdf) < 1) {
            $this->status = 0;

            return 0;
        }

        $keyidx       = array_key_first($pdf);

        $this->exp->table('media_job')->where('job_id', $this->job_id)->update(['close' => $pdf[$keyidx]['details']['product'], 'base_dir' => $base_dir]);

        foreach ($pdf as $form_number => $form_info) {
            BrowserStream::put('Importing form '.$form_number.'<BR>');
            $this->add_form_details($form_info['details']);
            $this->add_form_data($form_number, $form_info);
        }

        $this->status = 1;
    }
}
