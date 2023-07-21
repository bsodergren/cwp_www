<?php

use CWP\HTML\HTMLDisplay;
use CWP\Spreadsheet\Media\MediaXLSX;
/**
 * CWP Media tool
 */

require_once '.config.inc.php';
$break = false;
if ('Edit' == $_POST['submit']) {
    HTMLDisplay::$url     =  '/form_edit.php?job_id='.$_REQUEST['job_id'].'&form_number='.$_REQUEST['form_number'].'';
    HTMLDisplay::$timeout =  0;
    $break                = true;
} else {
    HTMLDisplay::$timeout =  0;
    foreach ($_REQUEST as $key => $value) {
        $break = false;
        if (str_starts_with($key, 'former')) {
            list($front, $id) = explode('_', $key);
            $count            = $explorer->table('form_data')->where('id', $id)->update(['former' => $value]);
        }

        if (str_starts_with($key, 'facetrim')) {
            list($front, $id) = explode('_', $key);
            $count            = $explorer->table('form_data')->where('id', $id)->update(['face_trim' => $value]);
        }

        if (str_starts_with($key, 'nobindery')) {
            list($front, $id) = explode('_', $key);
            $count            = $explorer->table('form_data')->where('id', $id)->update(['no_bindery' => $value]);
        }
    }

    if (true == array_key_exists('view', $_REQUEST)) {
        if ('save' == $_REQUEST['view']) {
            HTMLDisplay::$url =  '/index.php';
            define('REFRESH_MSG', 'Form finished');

            $media->excelArray();
            $excel            = new MediaXLSX($media, true);
            $break            = true;
        }
    }

    if (false == $break) {
        $next_form_number = $_REQUEST['form_number'];

        if (true == array_key_exists('submit_back', $_REQUEST)) {
            $next_form_number = $next_form_number - 2;
        }

        nextForm:
        $form_data        = $explorer->table('form_data');
        $form_data->where('form_number = ?', $next_form_number);
        $form_data->where('job_id = ?', $_REQUEST['job_id']);
        $results          = $form_data->fetch();

        if (empty($results)) {
            if (true == array_key_exists('submit_back', $_REQUEST)) {
                $next_form_number = $next_form_number - 2;
            } else {
                $next_form_number = $next_form_number + 1;
            }
            goto nextForm;
        }

        if ($next_form_number < 0) {
            $next_form_number = 1;
        }
        HTMLDisplay::$url =   '/form.php?job_id='.$_REQUEST['job_id'].'&form_number='.$next_form_number.'';
    }
}
