<?php
require_once(".config.inc.php");
$break = false;
if ($_POST["submit"] == "Edit")
{
  //  require_once(__PROCESS_DIR__ . "/form_edit.php");

    define("REFRESH_URL", __URL_HOME__ . "/form_edit.php?job_id=" . $_REQUEST['job_id'] . "&form_number=" . $_REQUEST['form_number'] . "");
    define("REFRESH_TIMEOUT", 0);
    $break = true;

} else {
    define("REFRESH_TIMEOUT", 0);
    foreach ($_REQUEST as $key => $value) {
        $break = false;
        if (str_starts_with($key, "former")) {
            list($front, $id) = explode("_", $key);
            $count = $explorer->table('form_data')->where('id', $id)->update(["former" => $value]);
        }

        if (str_starts_with($key, "facetrim")) {
            list($front, $id) = explode("_", $key);
            $count = $explorer->table('form_data')->where('id', $id)->update(["face_trim" => $value]);
        }

        if (str_starts_with($key, "nobindery")) {
            list($front, $id) = explode("_", $key);
            $count = $explorer->table('form_data')->where('id', $id)->update(["no_bindery" => $value]);
        }
    }

    if (array_key_exists("view", $_REQUEST) == TRUE) {
        if ($_REQUEST['view'] == "save") {

            define("REFRESH_URL", "/index.php");
            $break = true;
        }
    }

    if ($break == false) {
        $next_form_number = $_REQUEST['form_number'];

        if (array_key_exists("submit_back", $_REQUEST) == true) {
            $next_form_number = $next_form_number - 2;


            $form_data = $explorer->table('form_data');
            $form_data->where('form_number = ?', $next_form_number + 1);
            $form_data->where('job_id = ?', $_REQUEST['job_id']);
            $results = $form_data->fetch();

            if (empty($results)) {
                $next_form_number = $next_form_number - 1;
            }

            if ($next_form_number < 0) {
                $next_form_number = 1;
            }
        }
        define("REFRESH_URL", __URL_HOME__ . "/form.php?job_id=" . $_REQUEST['job_id'] . "&form_number=" . $next_form_number . "");
    }
}
