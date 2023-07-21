<?php
use CWP\HTML\HTMLDisplay;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Utils;
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

function cleanTrimPost($var, $size = false)
{
    if ('' == $var) {
        $var = null;
    } else {
        if (!str_contains($var, '/')) {
            if (true == $size) {
                $var = Utils::DelSizeToFrac($var);
            } else {
                $var = utils::floatToFrac($var);
            }
        }
    }

    return $var;
}

HTMLDisplay::$timeout = 0;
$form                 = new Formr\Formr('bootstrap4');

if ($form->submitted()) {
    if (array_key_exists('trim_add', $_POST)) {
        $publication = $_POST['publication'];
        if ('' != $publication) {
            $bind           = $_POST['bind'];

            $head_trim      = cleanTrimPost($_POST['head_trim']);
            $foot_trim      = cleanTrimPost($_POST['foot_trim']);
            $face_trim      = cleanTrimPost($_POST['face_trim']);
            $delivered_size = cleanTrimPost($_POST['delivered_size'], true);

            $data           = [
                'pub_name'       => MediaXLSX::CleanPublication($publication),
                'bind'           => $bind,
                'head_trim'      => $head_trim,
                'foot_trim'      => $foot_trim,
                'face_trim'      => $face_trim,
                'delivered_size' => $delivered_size,
            ];
            $res            = $explorer->table('pub_trim')->insert($data);
            define('REFRESH_MSG', 'Publication Added');
        } else {
            define('REFRESH_MSG', 'No publication named');
        }
    }

    if (array_key_exists('trim_update', $_POST)) {
        foreach ($_POST as $key => $value) {
            if ('trim_update' == $key) {
                continue;
            }
            if (str_contains($key, 'trim_')) {
                [$_,$id,$type]          = explode('_', $key);
                $updateData[$id][$type] = $value;
            }
        }

        foreach ($updateData as $id => $data) {
            $insert_data = [
                'head_trim'      => cleanTrimPost($data['head']),
                'foot_trim'      => cleanTrimPost($data['foot']),
                'face_trim'      => cleanTrimPost($data['face']),
                'delivered_size' => cleanTrimPost($data['size'], true),
            ];

            $count       = $explorer->table('pub_trim')->where('id', $id)->update($insert_data);
        }
        define('REFRESH_MSG', 'Publications Updated');
    }
}

HTMLDisplay::$url     = '/settings/trim.php';
