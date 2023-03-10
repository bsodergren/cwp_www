<?php

use Nette\Utils\FileSystem;

define("REFRESH_TIMEOUT", 0);
$form = new Formr\Formr('bootstrap4');

if ($form->submitted()) {

    if (key_exists('delete_logs', $_POST)) {
        $errorArray = getErrorLogs();

        foreach ($errorArray as $k => $file) {
            FileSystem::delete($file);
        }
        define("REFRESH_URL", 'index.php');

        echo HTMLDisplay::JavaRefresh(REFRESH_URL, REFRESH_TIMEOUT);
        exit;
    } else {
        define("REFRESH_URL", '/settings/settings.php?cat='.$_REQUEST['cat']);

        // get our form values and assign them to a variable
        foreach ($_POST as $key => $value) {

            if ($key == 'submit') {
                continue;
            }

            if (!str_contains($key, "-")) {
                $field = "setting_value";
            }

            if (str_contains($key, "setting_")) {
                $field = $key;
                $new_settiings[$field] = $value;
                continue;
            }

            if (str_contains($key, "-description")) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = "setting_" . $pcs[1];
            }

            if (str_contains($key, "-name")) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = "setting_" . $pcs[1];
            }

            if (str_contains($key, "-array")) {
                $pcs = explode('-', $key);
                $key = $pcs[0];
                $field = "setting_value";
                $value = trim($value);
                if ($value != '') {               
                    $value =  MediaSettings::save_post_asJson($value);
                }
            }



            $count = $explorer->table('settings')->where('definedName', $key)->update([$field => $value]);
            //   echo $template->render('process/update_setting', ['KEY' => $key, 'VALUE' => $value, 'FIELD' => $field]);

            ob_flush();
        }
        if ($new_settiings['setting_definedName'] != '') {
            $new_settiings['definedName'] = $new_settiings['setting_definedName'];
            unset($new_settiings['setting_definedName']);
            if ($new_settiings['setting_value'] == '') {

                $new_settiings['setting_value'] = NULL;
            }

            $explorer->table("settings")->insert($new_settiings);
            // echo "Added " . $new_settiings['definedName'] . " with " . $new_settiings['value'] . " <br>";
            ob_flush();
        }
    }
}
