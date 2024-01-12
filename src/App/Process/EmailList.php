<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Utils\Utils;
use Nette\Utils\FileSystem;
use CWP\Filesystem\MediaFileSystem;
use CWP\Spreadsheet\Media\MediaXLSX;

class EmailList extends MediaProcess
{
    public function run($req)
    {

        //$this->url = $req['FORM_PROCESS'];

        $method =  $req['FORM_PROCESS'];
        // if(method_exists($this, $method)) {
        $this->$method($req);
        //   }

    }

    private function updateEmail($req)
    {

        $id = $req['id'];
        $data = [
            'name' => $req['name'],
            'email' => $req['email'],
        ];

        $count = Media::$explorer->table('email_list')->where('id', $id)->update($data); // UPDATEME
        $this->msg = 'Updated email list';
    }

    private function addEmail($req)
    {
        $data = [
            'name' => $req['name'],
            'email' => $req['email'],
        ];

        Media::$explorer->table('email_list')->insert($data);
        $this->msg = 'Added New Email';
    }
}
