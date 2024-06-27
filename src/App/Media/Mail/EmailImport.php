<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Media\Mail;

use CWP\Filesystem\MediaFileSystem;
use CWP\Media\Mail\Attachment\MediaAttachment;
use CWP\Media\Mail\Jobnumber\Jobnumber;

class EmailImport extends EmailDisplay
{
    use Jobnumber;
    use MediaAttachment;

    public $imap;

    public $fs;
    public $pdf_directory;

    public $mailId       = 0;

    public $upload_directory;

    public static $index = 0;

    public $structure;

    public $attachments  = [];

    public function __construct()
    {
        $this->imap             = imap_open(__IMAP_HOST__.__IMAP_FOLDER__, __IMAP_USER__, __IMAP_PASSWD__); // || exit('Cannot connect to Gmail: '.imap_last_error());
        if (! imap_is_open($this->imap)) {
            dd('imap didnt open');
        }

        $location               = new MediaFileSystem();
        $this->fs               = $location;
        $this->upload_directory = $location->getDirectory('upload', true);
        $this->pdf_directory    = $location->getDirectory('pdf', true);
    }

    public function __destruct()
    {
        if (imap_is_open($this->imap)) {
            $errs = imap_errors();
            imap_close($this->imap);
        }
    }

    public function search($search = 'UNSEEN')
    {
        return imap_search($this->imap, $search);
    }

    public function getStructure()
    {
        $this->structure = @imap_fetchstructure($this->imap, $this->mailId);
    }

    public function hasAttachment()
    {
        // $this->attachments = [];
        $this->getStructure();

        if (isset($this->structure->parts) && \count($this->structure->parts)) {
            $this->getAttachmentFilename(1);
            $this->getAttachmentName(1);
            $this->getAttachmentFile(1);
        }
        imap_clearflag_full($this->imap, $this->mailId, '\\Seen');
        imap_setflag_full($this->imap, $this->mailId, '\\Seen');
       return $this->attachments[$this->mailId]['is_attachment'];
    }

    public function clean($name)
    {
        // Trimmer_Position_0623-C_RunSheets_Itsaca.pdf
        preg_match('/([0-9]{3,4})[ -_.]([ABC])[ -_.]([A-Za-z]+)[ -_.]([A-Za-z]+)(.pdf)/', $name, $output_array);

        $name = $output_array[1].'-'.$output_array[2].'_'.$output_array[3].'_'.$output_array[4].$output_array[5];

        return $name;
    }
}
