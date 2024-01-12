<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

use CWP\Core\Media;

class EmailList extends MediaProcess
{
    public function __construct()
    {
    }

    public function run($req)
    {
        // $this->url = $req['FORM_PROCESS'];

        $method = $req['FORM_PROCESS'];
        // if(method_exists($this, $method)) {
        $this->$method($req);
        //   }
    }

    private function updateEmail($req)
    {
        $delete = [];
        $data = [];

        foreach ($req['id'] as $key) {
            if ($req['delete'][$key]) {
                $delete[] = $key;
                continue;
            }
            if ('' != $req['name'][$key]) {
                $data[$key]['name'] = $req['name'][$key];
            }
            if ('' != $req['email'][$key]) {
                $data[$key]['email'] = $req['email'][$key];
            }
        }
        if (count($data) > 0) {
            foreach ($data as $id => $values) {
                $count = Media::$explorer->table('email_list')->where('id', $id)->update($values); // UPDATEME
            }
        }
        if (count($delete) > 0) {
            foreach ($delete as $k => $id) {
                $count = Media::$explorer->table('email_list')->where('id', $id)->delete();
            }
        }
        $this->msg = 'Updated email list';
    }

    public function addEmail($req)
    {
        $data = [
            'name' => $req['name'],
            'email' => $req['email'],
        ];

        $res = Media::$explorer->table('email_list')->insert($data);
        $this->msg = 'Added New Email';
    }
}
