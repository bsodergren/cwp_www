<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Mail;

use CWP\Media\Mail\Attachment\MediaAttachment;
use CWP\Media\Mail\Jobnumber\Jobnumber;
use CWP\Filesystem\MediaFileSystem;

class EmailImport extends EmailDisplay
{
    use MediaAttachment;
    use Jobnumber;

    public $imap;
    public $mailId = 0;
    public $upload_directory;
    public static $index = 0;

    public $structure;
    public $attachments = [];

    public function __construct()
    {
        $this->imap = imap_open(__IMAP_HOST__.__IMAP_FOLDER__, __IMAP_USER__, __IMAP_PASSWD__); // || exit('Cannot connect to Gmail: '.imap_last_error());
        if (!imap_is_open($this->imap)) {
            dd('imap didnt open');
        }

        $location = new MediaFileSystem();
        $this->upload_directory = $location->getDirectory('upload', true);
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

        if (isset($this->structure->parts) && count($this->structure->parts)) {
            $this->getAttachmentFilename(1);
            $this->getAttachmentName(1);
            $this->getAttachmentFile(1);

            @imap_clearflag_full($this->imap, $this->mailId, '\\Seen');
        }
    }

    public function clean($name)
    {
        $name = basename($name, '.pdf');
        $name = str_replace('.', '', $name);
        $name = str_replace(',', '_', $name);
        $name = str_replace(' ', '_', $name);

        return $name.'.pdf';
    }
}
