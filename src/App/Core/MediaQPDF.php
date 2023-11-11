<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Core;

use CWP\Media\MediaExec;
use CWP\HTML\HTMLDisplay;
use Nette\Utils\FileSystem;
use CWP\Filesystem\MediaFileSystem;

class MediaQPDF
{
    public static function cleanPDF($pdf_file)
    {
        $pdf_file = FileSystem::normalizePath($pdf_file);
        // $start    = microtime(true);
        // while (true) {
             usleep(5000);
            // $time   = microtime(true);
            // $passed = ($time - $start);
            // HTMLDisplay::put(' > '.$pdf_file, 'black');
             if (!(new MediaFileSystem)->exists($pdf_file)) {
                HTMLDisplay::put(' NOT Ready > '.$pdf_file, 'black');
            }

        // }
        $process  = new MediaExec();
        $process->cleanPdf($pdf_file);
    }
}
