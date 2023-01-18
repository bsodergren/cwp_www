<?php

use Nette\Utils\FileSystem;

define("REFRESH_TIMEOUT", 0);
define("REFRESH_URL", 'index.php');



//echo $_REQUEST[''];
$form = new Formr\Formr('bootstrap4');


if ($form->submitted()) {


    if (key_exists('delete_logs', $_POST)) {

        $errorArray = getErrorLogs();

        foreach ($errorArray as $k => $file) {
            FileSystem::delete($file);
        }

        echo JavaRefresh(REFRESH_URL, REFRESH_TIMEOUT);
        exit;
    } else {

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
                    $arr = explode("\n", $value);
                    $arr2 = [];
                    foreach ($arr as $k => $string) {
                        if ($string) {
                            list($v_key, $value) = explode("=>", $string);
                            $arr2[trim($v_key)] = trim($value);
                        }
                    }
                    $value = json_encode($arr2);
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
