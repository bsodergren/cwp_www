<?php

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaSettings;
use Nette\Utils\FileSystem;

HTMLDisplay::$timeout = 0;
$form = new Formr\Formr('bootstrap4');

if ($form->submitted()) {
    if (array_key_exists('delete_logs', $_POST)) {
        $errorArray = getErrorLogs();

        foreach ($errorArray as $k => $file) {
            FileSystem::delete($file);
        }

        HTMLDisplay::$url = 'index.php';

        echo HTMLDisplay::JavaRefresh(HTMLDisplay::$url, HTMLDisplay::$timeout);
        exit;
    } else {
        HTMLDisplay::$url = '/index.php';

        // get our form values and assign them to a variable
        foreach ($_POST as $key => $value) {
            if ($key == 'submit') {
                continue;
            }

            if (! str_contains($key, '-')) {
                $field = 'setting_value';
            }

            if (str_contains($key, 'setting_')) {
                $field = $key;
                $new_settiings[$field] = $value;
                continue;
            }

            if (str_contains($key, '-description')) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = 'setting_'.$pcs[1];
            }

            if (str_contains($key, '-name')) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = 'setting_'.$pcs[1];
            }

            if (str_contains($key, '-array')) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = 'setting_value';
                $value = trim($value);
                if ($value != '') {
                    $value = MediaSettings::save_post_asJson($value);
                }
            }

            if (str_contains($key, '-list')) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = 'setting_value';
                if (is_array($value)){
                    $value = implode(',', $value);
                }
                $value = str_ireplace("XX","",$value);
                $value = trim($value,",");
                //if ($value != '') {
                //    $value = MediaSettings::save_post_asJson($value);
                //}
            }

            $count = $explorer->table('settings')->where('definedName', $key)->update([$field => $value]);
            //   echo $template->render('process/update_setting', ['KEY' => $key, 'VALUE' => $value, 'FIELD' => $field]);

            ob_flush();
        }
        if ($new_settiings['setting_definedName'] != '') {
            $new_settiings['definedName'] = $new_settiings['setting_definedName'];
            unset($new_settiings['setting_definedName']);
            if ($new_settiings['setting_value'] == '') {
                $new_settiings['setting_value'] = null;
            }

            $explorer->table('settings')->insert($new_settiings);
            // echo "Added " . $new_settiings['definedName'] . " with " . $new_settiings['value'] . " <br>";
            ob_flush();
        }
    }
}
