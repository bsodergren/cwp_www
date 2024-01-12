<?php

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Filesystem\MediaFileSystem;
use CWP\Utils\Zip;

class Export extends MediaProcess
{
    private $explorer;
    private $backupArray = [];

    private $backupDir;

    private $zip_file;

    private $fs;

    public function __construct()
    {
        $this->explorer = Media::$explorer;
        $this->backupDir = __TEMP_DIR__.DIRECTORY_SEPARATOR.'mediaBackup'.DIRECTORY_SEPARATOR.bin2hex(random_bytes(5));
        $this->fs = new MediaFileSystem();
        $this->fs->createFolder($this->backupDir);
    }

    public function export($job_id)
    {
        $this->getJobJson($job_id);
        $this->backupPDF();
        $this->writeJson();
        $this->zipFiles();

        return 'Exported ';
    }

    private function getJobJson($id)
    {
        $results = $this->explorer->table('media_job')->where('job_id', $id);
        foreach ($results as $id => $row) {
            $backup['pdf_file'] = $row['pdf_file'];
            $backup['close'] = $row['close'];
            $backup['job_number'] = $row['job_number'];
            $forms = $this->explorer->table('media_forms')->where('job_id', $id);
            foreach ($forms as $form) {
                $backup['forms'][$form->form_number] = ['bind' => $form->bind, 'count' => $form->count, 'config' => $form->config];
                $form_data = $this->explorer->table('form_data')->where('job_id', $id)->where('form_number', $form->form_number)->fetchAll();
                foreach ($form_data as $form_row) {
                    $backup['forms'][$form->form_number]['data'][] = [
                        'form_letter' => $form_row->form_letter,
                        'original' => $form_row->original,
                        'market' => $form_row->market,
                        'pub' => $form_row->pub,
                        'count' => $form_row->count,
                        'ship' => $form_row->ship,
                        'former' => $form_row->former,
                        'face_trim' => $form_row->face_trim,
                        'no_bindery' => $form_row->no_bindery,
                    ];
                }
                // dd($form_data);
            }
        }
        $this->backupArray = $backup;
    }

    private function backupPDF()
    {
        $pdf_file = $this->backupArray['pdf_file'];
        $pdf_name = basename($this->backupArray['pdf_file'], '.pdf');
        $this->fs->copy($pdf_file, $this->backupDir.DIRECTORY_SEPARATOR.$pdf_name.'.pdf');
        $this->backupArray['pdf_file'] = $pdf_name.'.pdf';

        $this->zip_file = __FILES_DIR__.DIRECTORY_SEPARATOR.'backup_'.$pdf_name.'.zip';
    }

    private function writeJson()
    {
        $string = json_encode($this->backupArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $this->fs->write($this->backupDir.DIRECTORY_SEPARATOR.'backup.json', $string);
    }

    private function zipFiles()
    {
        $zip = new Zip();
        $d = $zip->zip($this->zip_file, $this->backupDir);
        $this->fs->delete($this->backupDir);
    }
}
