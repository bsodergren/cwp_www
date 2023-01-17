<?php
require_once ".config.inc.php";

define('TITLE', APP_NAME);
define('__CUSTOM_JS__', Template::GetHTML("index/javascript"));


$template = new Template();

include_once __LAYOUT_HEADER__;

$table = $explorer->table("media_job");
$results = $table->fetchAssoc('job_id');
$cnt = $table->count('*');

MediaLogger("Test Message");

if ($cnt > 0) {
	foreach ($results as $k => $row) {
		unset($replacement);
		$media = new Media($row);
		$mediaDir = new MediaFileSystem($media->pdf_file, $media->job_number);

		$url = __URL_HOME__ . "/form.php?job_id=" . $row['job_id'];

		$text_close = basename($row['pdf_file'], ".pdf");

		$text_job =  $row['job_number'];
		$form = new Formr\Formr('', 'hush');

		$hidden = ["job_id" => $row['job_id']];

		$replacement['FORM_OPEN_HTML'] = $form->open("", '', __URL_HOME__ . "/process.php", 'post', '', $hidden);

		$class_create = 'class="btn btn-success"';
		$class_delete = 'class="btn btn-danger"';
		$class_normal = 'class="btn btn-primary"';

		$num_of_forms = $media->number_of_forms();

		if ($num_of_forms == 0) {
			$pdisabled = ' disabled';
			$num_of_forms = '<input type="submit" name="actSubmit" value="Run Refresh Import" id="actSubmit" class="btn btn-danger">';
		} else {
			$pdisabled = '';
			$num_of_forms = "Number of Forms: " . $num_of_forms;
		}

		$replacement['TEXT_JOB'] = $text_job;
		$replacement['JOB_ID'] = $row['job_id'];

		$replacement['TEXT_CLOSE'] = $text_close;
		$replacement['NUM_OF_FORMS'] = $num_of_forms;


		$rowdisabled = " disabled";
		$zdisabled = " disabled";

		$zip_file = $media->zip_file;
		$xlsx_dir = $media->xlsx_directory;

		if (Media::get_exists("xlsx", $row['job_id']) == true && is_dir($xlsx_dir) == true) {
			$rowdisabled = "";
		}
		$tooltip = ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="process.php ';

		$replacement['FORM_BUTTONS_HTML'] =	$form->input_submit('process', '', "Process PDF Form", '', $class_normal . $pdisabled . $tooltip . 'process"');
		//$form->input_submit('actSubmit', '', 'View Forms', '', class_normal.$rowdisabled);

		if (Media::get_exists("xlsx", $row['job_id']) == true) {
			$replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('delete_xlsx', '', 'delete xlsx', '', $class_delete . $tooltip . 'delete_xlsx"');
		} else {
			$replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('create_xlsx', '', 'create xlsx', '', $class_create . $pdisabled . $tooltip . 'create_xlsx"');
		}

		if (is_file($zip_file)) {
			$replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('delete_zip', '', 'delete zip', '', $class_delete . $tooltip . 'delete_zip"');
		} else {
			if (Media::get_exists("xlsx", $row['job_id'])	 == true) {
				$zdisabled = "";
			}

			$replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('create_zip', '', 'create zip', '', $class_create . $zdisabled . $tooltip . 'create_zip"');
		}

		$replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('refresh_import', '', 'refresh import', '', $class_create . $tooltip . 'refresh_import"');
		$replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('delete_job', '', 'delete job', '', $class_delete . $tooltip . 'delete_job"');
		$replacement['FORM_CLOSE'] = $form->close();
		$template->template('index/job', $replacement);
	}

	$media_html = $template->return();

	$template->clear();

	$template->template('index/main', ['MEDIA_JOB_ROW' => $media_html]);

	$template->render();
} else {
	echo JavaRefresh('/import.php', 0);
}



include_once __LAYOUT_FOOTER__;
