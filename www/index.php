<?php
/**
 * CWP Media Load Flag Creator.
 */

use CWP\Core\Media;
use CWP\Core\Bootstrap;
use CWP\HTML\HTMLDisplay;
use CWP\Core\MediaSettings;
use CWP\Template\Pages\Index;
use CWP\Filesystem\MediaFileSystem;

require_once '.config.inc.php';

define('__AUTH__', true);
define('TITLE', APP_NAME);

$table = $explorer->table('media_job'); // UPDATEME
$results = $table->fetchAssoc('job_id');
$cnt = $table->count('*');

if (0 == $cnt) {
    echo HTMLDisplay::JavaRefresh('/import.php', 0);
    exit;
}

foreach ($results as $k => $row) {
    $run_refresh = false;
    $customJob = false;
    unset($replacement);
    $media = new Media($row);
    $mediaDir = new MediaFileSystem($media->pdf_file, $media->job_number);

    $url = __URL_PATH__.'/form.php?job_id='.$row['job_id'];

    $text_close = basename($row['pdf_file'], '.pdf');
    if (str_contains($text_close, 'Created')) {
        $customJob = true;
    }
    $pdf_url = HTMLDisplay::getPdfLink($row['base_dir'].'/pdf/'.$row['pdf_file']);

    $text_job = $row['job_number'];
    $form = new Formr\Formr('', 'hush');

    $hidden = ['job_id' => $row['job_id']];

    $js = ' onclick="window.open(\'about:blank\',\'print_popup\',\'width=1000,height=800\');" formtarget="print_popup" ';

    $delete_js = ' onclick="window.open(\'about:blank\',\'delete_popup\',\'width=400,height=400\');" formtarget="delete_popup" ';

    $replacement['FORM_HTML_START'] = $form->open('', '', __PROCESS_FORM__, 'post', '', $hidden);

    $class_create = 'class="btn  btn-success"';
    $class_delete = 'class="btn  btn-danger"';
    $class_normal = 'class="btn  btn-primary"';

    $num_of_forms = $media->number_of_forms();
    $updates = $media->updatedForms();

    if (0 == $num_of_forms) {
        if (false === $customJob) {
            $run_refresh = true;
            $num_of_forms = '<input type="submit" name="actSubmit" value="Run Refresh Import" id="actSubmit" class="btn btn-danger">';
        }
        $pdisabled = ' disabled';
        if (true === $customJob) {
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

    $zip_file = $media->zip_file;
    $xlsx_dir = $media->xlsx_directory;
    if (true == Media::get_exists('xlsx', $row['job_id']) && true == is_dir($xlsx_dir)) {
        $rowdisabled = '';
    }
    $tooltip = ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="process_';

    $replacement['FORM_BUTTONS_HTML'] .= Index::hrefLink($url, 'Edit Media Drop', $class_normal.$pdisabled);

    if (false === $run_refresh) {


        if (true == Media::get_exists('xlsx', $row['job_id'])) {
            $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('view_xlsx', '', '', '', $class_create.$tooltip.'view_xlsx"');
            if (0 < $updates) {
                $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('update_xlsx','','','update_xlsx_'.$row['job_id'],$js.$class_create.$pdisabled.$tooltip.'update_xlsx"');
            }

            $replacement['FORM_DELETE_HTML'] .= Index::ButtonLink('delete_xlsx', '', '', '', $class_delete.$tooltip.'delete_xlsx"');
        } else {
            $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('create_xlsx','','','create_xlsx_'.$row['job_id'],$js.$class_create.$pdisabled.$tooltip.'create_xlsx"');
        }


        if (__SHOW_ZIP__ == true) {
            if ($mediaDir->exists($zip_file)) {
                $replacement['FORM_DELETE_HTML'] .= Index::ButtonLink('delete_zip', '', '', '', $class_delete.$tooltip.'delete_zip"');
            } else {
                if (true == Media::get_exists('xlsx', $row['job_id'])) {
                $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('create_zip', '', '', '', $class_create.$tooltip.'create_zip"');

                }

            }


            if (__SHOW_MAIL__ == true) {
                if ($mediaDir->exists($zip_file)) {
                    $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('email_zip', '', '', '', $class_create.$tooltip.'email_zip"');
                }
            }
        }


        if (true == Media::get_exists('xlsx', $row['job_id'])) {
            if(MediaSettings::GoogleAvail()){

                $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('upload', '', 'Export to Google', '', $class_create.$tooltip.'Google"');
                $replacement['FORM_BUTTONS_HTML'] .= Index::hrefLink('#', 'Open Google Drive', $class_create,
                'onclick="OpenNewWindow(\''.__GOOGLE_SHARE_URL__.'\')"');
            }
        }
    }

    if (false === $customJob) {
        $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('refresh_import', '', '', '', $class_create.$tooltip.'refresh_import"');
    } else {
        $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('addforms', '', 'Add Forms to Job', '', $class_normal.$tooltip.'addforms"');
    }

    if (false === $run_refresh) {
        $replacement['FORM_BUTTONS_HTML'] .= Index::ButtonLink('export', '', '' , '', $class_create.$tooltip.'export_job"');
    }

    $replacement['FORM_DELETE_HTML'] .= Index::ButtonLink('delete_job', '', '', '', $delete_js.$class_delete.$tooltip.'delete_job"');

    $replacement['FORM_CLOSE'] = $form->close();
    $jobArray[] = $replacement;
}

$TplTemplate->assign('jobArray', $jobArray);
$TplTemplate->draw('body');
