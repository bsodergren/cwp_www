<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Mail\Attachment;

trait MediaAttachment
{
    public function moveAttachments()
    {
        foreach ($this->attachments as $key => $attachment) {
            if (true === $attachment['is_attachment']) {
                $attachment_name = $this->clean($attachment['name']);
                $attachment_filename = $this->clean($attachment['filename']);

                if (true == stripos($attachment_name, 'RunSheets')
                || true == stripos($attachment_name, 'Run_Sheets')) {
                    $this->attachments[$key]['name'] = $attachment_name;
                    $this->attachments[$key]['filename'] = $attachment_filename;
                    $filename = $this->upload_directory.\DIRECTORY_SEPARATOR.$attachment_name;

                    if (!file_exists($filename)) {
                        file_put_contents($filename, $attachment['attachment']);
                    }
                    unset($this->attachments[$key]['attachment']);
                    //  $html .= $this->displayAttachmentSelectOptions($filename, $attachment_name);
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
                    $this->attachments[$this->mailId]['is_attachment'] = true;
                    $this->attachments[$this->mailId]['filename'] = $object->value;
                }
            }
        }
    }

    public function getAttachmentName($i)
    {
        if ($this->structure->parts[$i]->ifdparameters) {
            foreach ($this->structure->parts[$i]->parameters as $object) {
                if ('name' == strtolower($object->attribute)) {
                    $this->attachments[$this->mailId]['is_attachment'] = true;
                    $this->attachments[$this->mailId]['name'] = $object->value;
                }
            }
        }
    }

    public function getAttachmentFile($i)
    {
        if ($this->attachments[$this->mailId]['is_attachment']) {
            $this->attachments[$this->mailId]['attachment'] =
            @imap_fetchbody($this->imap, $this->mailId, $i + 1);
            if (3 == $this->structure->parts[$i]->encoding) { // 3 = BASE64
                $this->attachments[$this->mailId]['attachment'] =
                @base64_decode($this->attachments[$this->mailId]['attachment']);
            } elseif (4 == $this->structure->parts[$i]->encoding) { // 4 = QUOTED-PRINTABLE
                $this->attachments[$this->mailId]['attachment'] =
                 @quoted_printable_decode($this->attachments[$this->mailId]['attachment']);
            }
        }
    }
}
