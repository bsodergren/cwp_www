<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Core\Media;
use CWP\Core\MediaSettings;
use Formr\Formr;

class Settings extends MediaProcess
{
    public function run($req)
    {
        $form = new Formr('bootstrap4');
        if ($form->submitted()) {
            $this->url = '/index.php';
            // get our form values and assign them to a variable
            foreach ($req as $key => $value) {
                if ('submit' == $key) {
                    continue;
                }

                if (!str_contains($key, '-')) {
                    $field = 'setting_value';
                }

                if (str_contains($key, 'setting_')) {
                    $field = $key;
                    $new_settiings[$field] = $value;
                    continue;
                }
                if (str_contains($key, '-group')) {
                    $pcs = explode('-', $key);
                    $key = $pcs[0];
                    $field = 'setting_'.$pcs[1];
                }
                if (str_contains($key, '-description')) {
                    $pcs = explode('-', $key);
                    $key = $pcs[0];
                    $field = 'setting_'.$pcs[1];
                }

                if (str_contains($key, '-name')) {
                    continue;
                    // $pcs = explode('-', $key);
                    // $key = $pcs[0];
                    // $field = 'setting_'.$pcs[1];
                }

                if (str_contains($key, '-array')) {
                    $pcs = explode('-', $key);
                    $key = $pcs[0];
                    $field = 'setting_value';
                    $value = trim($value);
                    if ('' != $value) {
                        $value = MediaSettings::save_post_asJson($value);
                    }
                }

                if (str_contains($key, '-list')) {
                    $pcs = explode('-', $key);
                    $key = $pcs[0];
                    $field = 'setting_value';
                    if (\is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $value = str_ireplace('XX', '', $value);
                    $value = trim($value, ',');
                    // if ($value != '') {
                    //    $value = MediaSettings::save_post_asJson($value);
                    // }
                }

               // dump([ $key,$field, $value]);
                $count = Media::$explorer->table('settings')->where('definedName', $key)->update([$field => $value]); // UPDATEME
                //   echo $template->render('process/update_setting', ['KEY' => $key, 'VALUE' => $value, 'FIELD' => $field]);

                ob_flush();
            }
            //dd("fsd");

            if ('' != $new_settiings['setting_definedName']) {
                $new_settiings['definedName'] = $new_settiings['setting_definedName'];
                unset($new_settiings['setting_definedName']);
                if ('' == $new_settiings['setting_value']) {
                    $new_settiings['setting_value'] = null;
                }

                Media::$explorer->table('settings')->insert($new_settiings); // UPDATEME
                // echo "Added " . $new_settiings['definedName'] . " with " . $new_settiings['value'] . " <br>";
                ob_flush();
            }
        }
    }
}
