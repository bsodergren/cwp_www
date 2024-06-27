<?php

namespace CWP\Process\Traits;

use CWP\Filesystem\Driver\MediaGoogleDrive;
use CWP\Filesystem\MediaFileSystem;
use CWP\Filesystem\MediaFinder;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

trait File
{
    public function getExcelSheet($form_number)
    {
        $mediaLoc = new MediaFileSystem($this->media->pdf_file, $this->media->job_number);
        $mediaLoc->getDirectory();
        $excelDir = $mediaLoc->directory.DIRECTORY_SEPARATOR.'xlsx';
        $basePath = dirname($mediaLoc->directory, 2);
        $mediaFind = new MediaFinder($this->media);
        $files = $mediaFind->search($excelDir, '*FM'.$form_number.'.xlsx');
        dd($files);
    }

    public function upload($form_number = null)
    {
        $pathPrefix = '';
        if (__DEBUG__ == 1) {
            $pathPrefix = '\Dev';
        }
        \define('TITLE', 'Uploading to Google Drive');
        MediaDevice::getHeader();
        Template::echo('stream/start_page', []);

        $google = new MediaGoogleDrive();
        $mediaLoc = new MediaFileSystem($this->media->pdf_file, $this->media->job_number);
        $mediaLoc->getDirectory();
        $excelDir = $mediaLoc->directory.DIRECTORY_SEPARATOR.'xlsx';
        $basePath = dirname($mediaLoc->directory, 2);
        $filePath = $pathPrefix.str_replace($basePath, '/', $mediaLoc->directory()); // . DIRECTORY_SEPARATOR . "xlsx";
        $google->createFolder($filePath);
        HTMLDisplay::pushhtml('stream/excel/msg', ['TEXT' => 'Created DIR '.$filePath]);

        if (null !== $form_number) {
            $mediaFind = new MediaFinder($this->media);
            $files = $mediaFind->search($excelDir, '*FM'.$form_number.'.xlsx');
            //   HTMLDisplay::pushhtml('stream/excel/file_msg', ['TEXT' => 'Updating '.$excelDir]);
        } else {
            $files = $mediaLoc->getContents($excelDir, '*.xlsx');
        }

        utmdump( $files);
        foreach ($files as $filename) {
            $remoteFilename = basename($filename);
            HTMLDisplay::pushhtml('stream/excel/file_msg', ['TEXT' => 'Uploading '.$remoteFilename]);
            $uploadFilename = $filePath.DIRECTORY_SEPARATOR.$remoteFilename;
            $google->UploadFile($filename, $uploadFilename);
        }

        $this->msg = 'Files Uploaded to google drive';
        Template::echo('stream/end_page', []);
    }
}
