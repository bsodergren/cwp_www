<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Core;

use CWP\Media\MediaExec;
use Nette\Utils\FileSystem;

class MediaQPDF
{
    public static function cleanPDF($pdf_file)
    {
        $pdf_file = FileSystem::normalizePath($pdf_file);
        $process  = new MediaExec();
        $process->cleanPdf($pdf_file);
    }
}
