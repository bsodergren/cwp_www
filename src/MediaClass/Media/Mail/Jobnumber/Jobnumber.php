<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Mail\Jobnumber;

trait Jobnumber
{
    public function getJobNumbers()
    {
        foreach ($this->attachments as $key => $attachment) {
            if (true === $attachment['is_attachment']) {
                $this->getSubject($key);
                $this->getBody($key);
                $this->reduce($key);
            }
        }

    }

    private function reduce($mailId)
    {
        if(key_exists('JobNumber', $this->attachments[$mailId])) {
            $this->attachments[$mailId]['JobNumber'] = array_unique($this->attachments[$mailId]['JobNumber']);
        }
    }
    private function addJobNumber($mailId, $array)
    {
        $numbers = $array[0];

        foreach($numbers as $number) {
            if($number != '') {
                $this->attachments[$mailId]['JobNumber'][] = $number;
            }
        }
    }

    public function getSubject($mailId)
    {
        $msg_header = imap_headerinfo($this->imap, $mailId);
        $match = preg_match_all('/(23[0-9]{4})/U', $msg_header->subject, $output_array);
        if($match == true) {
            $this->addJobNumber($mailId, $output_array);

        }
    }

    public function getBody($mailId)
    {


        $message = imap_qprint(imap_body($this->imap, $mailId, \FT_PEEK));
        $match = preg_match_all('/(23[0-9]{4})/U', $message, $output_array);
        if($match == true) {
            $this->addJobNumber($mailId, $output_array);

        }
    }

}
