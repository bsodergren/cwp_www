<?php

namespace CWP\Media\Import;

use CWP\Media\Media;

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
