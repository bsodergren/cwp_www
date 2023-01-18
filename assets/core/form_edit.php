<?php
define('NO_NAV',true);
//define('__SKIP_JS__',true);

$job_id = $_REQUEST['job_id'];
$form_number = $_REQUEST['form_number'];
$job  = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);

$media = new Media($job);

$form_edit['url'] = __URL_HOME__ . "/form_edit.php?job_id=" . $job_id .  "&form_number=" . $form_number;
$form_edit['timeout'] = 1;

if (key_exists("Return", $_REQUEST))
{
	MediaError::msg("info","Returning home");
}

if (key_exists("Reset", $_REQUEST)) 
{
	$media->delete_form($form_number);
	$pdfObj = new PDFImport($media->pdf_fullname, $media->job_id,$form_number);
	$media->add_form_data($form_number, $pdfObj->form[$form_number]);
	MediaError::msg("info","Resetting all Form Data",$form_edit);
}


if (key_exists("submit", $_REQUEST)) {

	
	foreach ($_REQUEST as $key => $value) {

		if ($key == "job_id") {
			continue;
		}
		if ($key == "form_number") {
			continue;
		}
		if ($key == "submit") {
			continue;
		}
		if ($key == "tracy-session") {
			continue;
		}
		
		list($id, $action) = explode("_", $key);

		if ($deleted_id == $id) {
			continue;
		}

		unset($data);
		if ($value != '') {
			switch ($action) {
				case "delete":

					$media->deleteFormRow($id);
					MediaError::msg("info","Deleting row",$form_edit);
					break;

				case "split":
					$form_data =  $media->getFormRow($id);
					$form_data['count'] = ($form_data['count'] / 2);
					$media->updateFormRow($id, $form_data);
					unset($form_data['id']);
					$media->addFormRow($form_data);
					MediaError::msg("info","Splitting Form",$form_edit);
					break;

				case "formletter":
					$data = array('form_letter' => strtoupper($value));
					break;

				case "facetrim":
					$data = array('face_trim' => $value);
					break;

				case "former":
					$data = array('former' => $value);
					break;

				case "pcscount":
					$value = str_replace("*","x",$value);
					if (str_contains($value, "x")) {
						list($x, $n) = explode("x", $value);
						$value = $x * $n;
					}
					if (str_contains($value, "/")) {
						list($x, $n) = explode("/", $value);
						$value = $x / $n;
					}
					$data = array('count' => $value);

					break;
			}

			if (isset($data)) {
				$msg .= var_export($data,1)."<br>";
				$media->updateFormRow($id, $data);
			}
		}
	}
}
MediaError::msg("warning","Updated form <br> " .$msg ,$form_edit);
