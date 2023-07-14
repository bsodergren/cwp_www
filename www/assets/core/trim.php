<?php

require_once('.config.inc.php');

use Nette\Utils\FileSystem;

HTMLDisplay::$timeout =  0;
$form = new Formr\Formr('bootstrap4');

if ($form->submitted()) {
    if(key_exists("trim_add", $_POST)) {

        $publication = $_POST['publication'];
        if($publication != "") {


            $bind = $_POST['bind'];

            $head_trim = $_POST['head_trim'];
            if($head_trim == "") {
                $head_trim = null;
            }
            $foot_trim = $_POST['foot_trim'];
            if($foot_trim == "") {
                $foot_trim = null;
            }

            $face_trim = $_POST['face_trim'];
            if($face_trim == "") {
                $face_trim = null;
            }

            $delivered_size = $_POST['delivered_size'];
            if($delivered_size == "") {
                $delivered_size = null;
            }

            $data = [
                'pub_name' => MediaXLSX::CleanPublication($publication),
                'bind' => $bind,
                'head_trim' => $head_trim,
                'foot_trim' => $foot_trim,
                'face_trim' => $face_trim,

                'delivered_size' => $delivered_size,

            ];
            $res = $explorer->table("pub_trim")->insert($data);
            define('REFRESH_MSG', 'Publication Added');
        } else {
            define('REFRESH_MSG', 'No publication named');

        }
    }

    if(key_exists("trim_update", $_POST)) {
        foreach ($_POST as $key => $value) {
            if(str_contains($key, "trim_")) {
                [$_,$id,$type] = explode("_", $key);
                $updateData[$id][$type] = $value;
            }
        }

        foreach($updateData as $id => $data) {

            if ($data['size'] == "") {
                $data['size'] = null;
            }

            $count = $explorer->table('pub_trim')
            ->where('id', $id) // must be called before update()
            ->update([
                'head_trim' => $data['head'],
                'foot_trim' => $data['foot'],
                'face_trim' => $data['face'],
                'delivered_size' => $data['size'],
            ]);
        }

        define('REFRESH_MSG', 'Publications Updated');


    }


}



HTMLDisplay::$url =  '/settings/trim.php';
