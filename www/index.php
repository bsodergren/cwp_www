<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\Core\MediaStopWatch;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Utils\MediaDevice;


require_once '.config.inc.php';

define('__AUTH__', true);
define('TITLE', APP_NAME);

$table = $explorer->table('media_job');
$results = $table->fetchAssoc('job_id');
$cnt = $table->count('*');

if($cnt == 0 ){
    echo HTMLDisplay::JavaRefresh('/import.php', 0);
    exit;
}

foreach ($results as $k => $row) {
    $customJob = false;
    unset($replacement);
    $media = new Media($row);
    $mediaDir = new MediaFileSystem($media->pdf_file, $media->job_number);

    $url = __URL_PATH__.'/form.php?job_id='.$row['job_id'];

    $text_close = basename($row['pdf_file'], '.pdf');
    if(str_contains($text_close, "Created")) {
        $customJob = true;
    }
    $pdf_url = HTMLDisplay::getPdfLink($row['base_dir'].'/pdf/'.$row['pdf_file']);

    $text_job = $row['job_number'];
    $form = new Formr\Formr('', 'hush');

    $hidden = ['job_id' => $row['job_id']];

    $js = ' onclick="window.open(\'about:blank\',\'print_popup\',\'width=1000,height=800\');" formtarget="print_popup" ';

    $delete_js = ' onclick="window.open(\'about:blank\',\'delete_popup\',\'width=400,height=400\');" formtarget="delete_popup" ';

    $replacement['FORM_HTML_START']= $form->open('', '', __PROCESS_FORM__, 'post', '', $hidden);

    $class_create = 'class="btn  btn-success"';
    $class_delete = 'class="btn  btn-danger"';
    $class_normal = 'class="btn  btn-primary"';

    $num_of_forms = $media->number_of_forms();


    if (0 == $num_of_forms) {
        if($customJob === false) {
            $num_of_forms = '<input type="submit" name="actSubmit" value="Run Refresh Import" id="actSubmit" class="btn btn-danger">';
        }
        $pdisabled = ' disabled';
        if($customJob === true) {
            $num_of_forms = '';
        }
    } else {
        $pdisabled = '';
        $num_of_forms = 'Number of Forms: '.$num_of_forms;
    }


    $replacement['TEXT_JOB'] = $text_job;
    $replacement['JOB_ID'] = $row['job_id'];

    $replacement['HIDDEN_CLASS'] = 'collapse.show';

    if (1 == $row['hidden']) {
        $replacement['HIDDEN_CLASS'] = 'collapse';
    }

    $replacement['TEXT_CLOSE'] = $text_close;

    $replacement['TEXT_CLOSE_URL'] = $pdf_url;
    $replacement['NUM_OF_FORMS'] = $num_of_forms;

    $rowdisabled = ' disabled';
    $zdisabled = ' disabled';

    $zip_file = $media->zip_file;
    $xlsx_dir = $media->xlsx_directory;
    if (true == Media::get_exists('xlsx', $row['job_id']) && true == is_dir($xlsx_dir)) {
        $rowdisabled = '';
    }
    $tooltip = ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="process_';
    //$javascript_click = ' onclick="return createButton(this.id);" ';

    $replacement['FORM_BUTTONS_HTML'] = $form->input_submit('submit[process]', '', 'Process PDF Form', '', $class_normal.$pdisabled.$tooltip.'process"');


    // $form->input_submit('actSubmit', '', 'View Forms', '', class_normal.$rowdisabled);

    if (true == Media::get_exists('xlsx', $row['job_id'])) {
        $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[view_xlsx]', '', 'view xlsx', '', $class_create.$tooltip.'view_xlsx"');
        $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[delete_xlsx]', '', 'delete xlsx', '', $class_delete.$tooltip.'delete_xlsx"');
    } else {

        $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[create_xlsx]',
         '', 'create xlsx', 'create_xlsx_'.$row['job_id'],$js.$class_create.$pdisabled.$tooltip.'create_xlsx"');

    }

    if (true == Media::get_exists('xlsx', $row['job_id'])) {
        //   $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('create_slip', '', 'create_slip', '', $class_create.$tooltip.'create_slip"');
    }

    if (__SHOW_ZIP__ == true) {
        if ($mediaDir->exists($zip_file)) {
            $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[delete_zip]', '', 'delete zip', '', $class_delete.$tooltip.'delete_zip"');
        } else {
            if (true == Media::get_exists('xlsx', $row['job_id'])) {
                $zdisabled = '';
            }

            $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[create_zip]', '', 'create zip', '', $class_create.$zdisabled.$tooltip.'create_zip"');
        }
        if (__SHOW_MAIL__ == true) {
            if ($mediaDir->exists($zip_file)) {
                $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[email_zip]', '', 'email zip', '', $class_create.$tooltip.'email_zip"');
            }
        }
    }
    //   $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('export_job', '', 'Export Job', '', $class_create.$tooltip.'export"');
    if($customJob === false) {
        $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[refresh_import]', '', 'refresh import', '', $class_create.$tooltip.'refresh_import"');
    } else {
        $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[addforms]', '', 'Add Forms to Job', '', $class_normal.$tooltip.'addforms"');
    }

    $replacement['FORM_BUTTONS_HTML'] .= $form->input_submit('submit[delete_job]', '', 'delete job', '', $delete_js . $class_delete.$tooltip.'delete_job"');
    $replacement['FORM_CLOSE'] = $form->close();
    $jobArray[] = $replacement;
}



    $TplTemplate->assign('jobArray',$jobArray);
    $TplTemplate->draw('body');
