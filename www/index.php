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
    $firstGroup = '';
    $secondGroup = '';
    $deleteGroup = '';

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

    $xlsr_exists = Media::get_exists('xlsx', $row['job_id']);


    if (true == $xlsr_exists && true == is_dir($xlsx_dir)) {
        $rowdisabled = '';
    }
    $tooltip = ' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="process_';

    $firstGroup .= Index::firstGroupLink($url, 'Edit Media Drop', $pdisabled);

    if (false === $run_refresh) {

        if (true == $xlsr_exists )
        {
            $firstGroup .= Index::firstGroup('view_xlsx', '', '', '', $tooltip.'view_xlsx"');

            if(MediaSettings::GoogleAvail())
            {
                $secondGroup.= Index::secondGroup('upload', '', 'Export to Google', '', $tooltip.'Google"');
                $secondGroup.= Index::firstGroupLink('#', 'Open Google Drive', '', 'onclick="OpenNewWindow(\''.__GOOGLE_SHARE_URL__.'\')"');
            }

            if (0 < $updates) {
                $secondGroup .= Index::secondGroup('update_xlsx','','','update_xlsx_'.$row['job_id'],$js.$pdisabled.$tooltip.'update_xlsx"');
            }

            if (__SHOW_ZIP__ == true) {
                if ($mediaDir->exists($zip_file)) {
                    $deleteGroup .= Index::deleteGroup('delete_zip', '', '', '', $tooltip.'delete_zip"');
                    if (__SHOW_MAIL__ == true) {
                        $firstGroup .= Index::firstGroup('email_zip', '', '', '', $tooltip.'email_zip"');
                    }
                }

            }

            $secondGroup.= Index::secondGroup('create_zip', '', '', '', $tooltip.'create_zip"');
            $deleteGroup .= Index::deleteGroup('delete_xlsx', '', '', '', $tooltip.'delete_xlsx"');
        } else {
            $firstGroup .= Index::firstGroup('create_xlsx','','','create_xlsx_'.$row['job_id'],$js.$pdisabled.$tooltip.'create_xlsx"');
        }
    }

    if (false === $customJob) {
        $deleteGroup .= Index::deleteGroup('refresh_import', '', '', '', $tooltip.'refresh_import"');
    } else {
        $firstGroup .= Index::firstGroup('addforms', '', 'Add Forms to Job', '', $tooltip.'addforms"');
    }

    if (false === $run_refresh) {
        $deleteGroup .= Index::secondGroup('export', '', '' , '', $tooltip.'export_job"');
    }

    $deleteGroup .= Index::deleteGroup('delete_job', '', '', '', $delete_js.$tooltip.'delete_job"');

    $replacement['FORM_CLOSE'] = $form->close();
    $replacement['FORM_BUTTONS_HTML'] = $firstGroup;
    $replacement['FORM_EXTRA_HTML'] = $secondGroup;
    $replacement['FORM_DELETE_HTML'] = $deleteGroup;
    $jobArray[] = $replacement;
}

$TplTemplate->assign('jobArray', $jobArray);
$TplTemplate->draw('body');
