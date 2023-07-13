<?php

use Nette\Utils\FileSystem;

HTMLDisplay::$timeout =  0;
$form = new Formr\Formr('bootstrap4');

if ($form->submitted()) {
    foreach ($_POST as $key => $value) {
        if(str_contains($key, "trim_")) {
            [$_,$id,$type] = explode("_", $key);
            $updateData[$id][$type] = $value;
        }



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
        'delivered_size' => $data['size'],
    ]);
}

HTMLDisplay::$url =  '/settings/trim.php';
