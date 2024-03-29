<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Media\Mail\Attachment;

use CWP\Core\Media;
use CWP\Core\MediaQPDF;

trait MediaAttachment
{
    public function moveAttachments()
    {
        foreach ($this->attachments as $key => $attachment) {
            if (true === $attachment['is_attachment']) {
                $attachment_name     = $this->clean($attachment['name']);
                $attachment_filename = $this->clean($attachment['filename']);

                if ($this->findRunSheets($attachment_filename) )
                {
                    $this->attachments[$key]['name']     = $attachment_name;
                    $this->attachments[$key]['filename'] = $attachment_filename;
                    if (false == $this->fs->exists($attachment_name)) {
                        $pdf_filename = $this->pdf_directory.\DIRECTORY_SEPARATOR.$attachment_name;
                        $filename        = $this->upload_directory.\DIRECTORY_SEPARATOR.$attachment_name;
                        file_put_contents($filename, $attachment['attachment']);
                        MediaQPDF::cleanPDF($filename);
                        $this->fs->UploadFile($filename, $pdf_filename, ['autorename' => false]);

                    }
                    unset($this->attachments[$key]['attachment']);
                } else {
                    unset($this->attachments[$key]);
                }
            }
        }

        //  return $html;
    }

    public function getAttachmentFilename($i)
    {
        if ($this->structure->parts[$i]->ifdparameters) {
            foreach ($this->structure->parts[$i]->dparameters as $object) {
                if ('filename' == strtolower($object->attribute)) {
                    $this->attachments[$this->mailId]['filename']      = $object->value;
                    if ($this->findRunSheets($object->value))
                    {
                        $this->attachments[$this->mailId]['is_attachment'] = true;
                    } else {
                        unset($this->attachments[$this->mailId]);
                    }
                }
            }
        }
    }

    public function getAttachmentName($i)
    {
        if ($this->structure->parts[$i]->ifdparameters) {
            foreach ($this->structure->parts[$i]->parameters as $object) {
                if ('name' == strtolower($object->attribute))
                {
                    $this->attachments[$this->mailId]['name']          = $object->value;
                    if ($this->findRunSheets($object->value))
                    {
                        $this->attachments[$this->mailId]['is_attachment'] = true;
                    } else {
                        unset($this->attachments[$this->mailId]);
                    }
                }
            }
        }
    }

    public function getAttachmentFile($i)
    {
        if ($this->attachments[$this->mailId]['is_attachment']) {
            $this->attachments[$this->mailId]['attachment'] = @imap_fetchbody($this->imap, $this->mailId, $i + 1);
            if (3 == $this->structure->parts[$i]->encoding) { // 3 = BASE64
                $this->attachments[$this->mailId]['attachment'] = @base64_decode($this->attachments[$this->mailId]['attachment']);
            } elseif (4 == $this->structure->parts[$i]->encoding) { // 4 = QUOTED-PRINTABLE
                $this->attachments[$this->mailId]['attachment'] = @quoted_printable_decode($this->attachments[$this->mailId]['attachment']);
            }
        }
    }

    private function findRunSheets($name){
        if (str_contains($name, 'RunSheets')){
            return true;
        }
        if (str_contains($name, 'Run_Sheets')){
            return true;
        }
        return false;

    }

}
