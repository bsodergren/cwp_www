<?php

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Filesystem\MediaFileSystem;
use CWP\Utils\Zip;
use Nette\Utils\FileSystem;

class Export extends MediaProcess
{
    private $explorer;
    private $backupArray = [];

    private $backupDir;
    private $tempDir;

    private $zip_file;

    private $fs;

    public function __construct()
    {
        $this->explorer = Media::$explorer;

        $this->tempDir = FileSystem::platformSlashes(__TEMP_DIR__.DIRECTORY_SEPARATOR.'mediaBackup'.DIRECTORY_SEPARATOR.bin2hex(random_bytes(5)));
        $this->backupDir = FileSystem::platformSlashes(__FILES_DIR__.DIRECTORY_SEPARATOR.'mediaBackup');
        $this->fs = new MediaFileSystem();
        $this->fs->createFolder($this->backupDir);
        $this->fs->createFolder($this->tempDir);
    }

    public function export($job_id)
    {
        $this->getJobJson($job_id);
        $this->backupPDF();
        $this->writeJson();
        $this->zipFiles();

        $this->download();

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
                $backup['forms'][$form->form_number] = ['bind' => $form->bind, 'count' => $form->count, 'config' => $form->config,
                'no_trimmers' => $form->no_trimmers];
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
        $this->fs->copy($pdf_file, $this->tempDir.DIRECTORY_SEPARATOR.$pdf_name.'.pdf');
        $this->backupArray['pdf_file'] = $pdf_name.'.pdf';

        $this->zip_file = $this->backupDir.DIRECTORY_SEPARATOR.'backup_'.$pdf_name.'.zip';
    }

    private function writeJson()
    {
        $string = json_encode($this->backupArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $json_file = $this->tempDir.DIRECTORY_SEPARATOR.'backup.json';

        $this->fs->write($json_file, $string);
    }

    private function zipFiles()
    {
        $zip = new Zip();

        $d = $zip->zip($this->zip_file, $this->tempDir);
        $this->fs->delete($this->tempDir);
    }

    private function download()
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($this->zip_file).'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($this->zip_file));
        ob_end_flush();
        @readfile($this->zip_file);
    }
}
