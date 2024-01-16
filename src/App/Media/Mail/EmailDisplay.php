<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Media\Mail;

use CWP\Core\Media;
use CWP\Template\Template;

class EmailDisplay
{
    public $attachments;

    public function drawFileOptions()
    {
        $html = '';
        foreach ($this->attachments as $key => $attachment)
        {

            if(is_array($attachment['JobNumber'])) {
                $jobNumber = implode(",",$attachment['JobNumber']);
            } else {
                $jobNumber = $attachment['JobNumber'];
            }

            self::addImportedPDF($attachment['name'],$jobNumber);

            $html .= template::GetHTML('/import/email/job_row', [
                'OPTION_JOB' => $jobNumber,
                'OPTION_NAME'  => $attachment['name'],
            ]);
        }

        return $html;
    }

    public function drawSelectBox()
    {
        return template::GetHTML(
            '/import/email/job_list',
            [
                'SELECT_OPTIONS' => $this->drawFileOptions(),
            ]
        );
    }

    public static function addImportedPDF($pdf_filename,$jobNumber)
    {

        $query = 'INSERT IGNORE INTO `media_imports` ?';

        Media::$connection->query($query, [
            'job_number' => $jobNumber,
            'pdf_file' => $pdf_filename
        ]);
    }

}
