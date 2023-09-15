<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Media\Import;

use CWP\Core\Media;

class MediaImport extends Media
{
    public $job_id = '';

    public $status = '';

    public $conn;

    public $exp;

    public function __construct()
    {
    }
}
