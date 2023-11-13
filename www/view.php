<?php
/**
 * Command like Metatag writer for video files.
 */

require_once '.config.inc.php';

use CWP\Core\MediaError;
use CWP\Filesystem\MediaFinder;
use CWP\HTML\HTMLDisplay;
use CWP\Spreadsheet\XLSXViewer;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use PhpOffice\PhpSpreadsheet\IOFactory;

if ('email' == $_REQUEST['action']) {
    define('TITLE', 'Email excel zip file');
    MediaDevice::getHeader();
    $template->render('view/mail_form', [
        '__FORM_URL__' => __URL_PATH__.'/process.php',
        'FORM_NUMBER' => $_REQUEST['form_number'],
        'JOB_ID' => $_REQUEST['job_id']]);
    MediaDevice::getFooter();
    exit;
}
$finder = new MediaFinder($media);

define('TITLE', 'View Form');
if (true == $finder->dirExists($media->xlsx_directory)) {
    $form_number = '';
    $file_id = '';
    $sheet_id = 0;
    if (array_key_exists('form_number', $_REQUEST)) {
        $form_number = $_REQUEST['form_number'];
    }

    if (array_key_exists('file_id', $_REQUEST)) {
        $file_id = $_REQUEST['file_id'];
    }

    if (array_key_exists('sheet_id', $_REQUEST)) {
        $sheet_id = $_REQUEST['sheet_id'];
    }
    if (!$finder->dirExists($media->xlsx_directory)) {
        XLSXViewer::checkifexist($media);
    }

    $result = $finder->search($media->xlsx_directory, '*.xlsx');
    $found = false;

    // if (!$finder->hasResults()) {
    //     XLSXViewer::checkifexist($media);
    // }

    $idx = 0;
    $params['FORM_LIST_HTML'] = '';
    $params['EXCEL_DIRECTORY'] = $media->xlsx_directory;
    $excel_link = '';

    foreach ($result as $file) {
        $files[] = $file;
        $class = 'enabled';
        preg_match('/.*_([FM0-9]+).xlsx/', $file, $output_array);
        [$text_form,$text_number] = explode('FM', $output_array[1]);

        if ('' != $form_number) {
            if ($form_number == $text_number) {
                $file_id = $idx;
                $found = true;
            }
        } else {
            $found = true;
        }

        if ($file_id == $idx) {
            $class = 'disabled';
            $current_form_number = $text_number;

            $url_link = HTMLDisplay::draw_excelLink($file);
            if (false != $url_link) {
                $params['EXCEL_LINK'] = Template::GetHTML('/view/sheet_link', [
                    'PAGE_FORM_URL' => $url_link,
                    'PAGE_FORM_NUMBER' => basename($file),
                    'SHEET_DISABLED' => 'enabled',
                    'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',
                    'SHEET_CLASS' => 'btn-info',
                ]);
            }
        }

        $page_form_html .= template::GetHTML('/view/form_link', [
            'PAGE_FORM_URL' => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&file_id='.$idx,
            'PAGE_FORM_NUMBER' => 'FM '.$text_number,
            'FORM_DISABLED' => $class,
        ]);

        if (0 == $idx % 9 && $idx > 0) {
            $params['FORM_LIST_HTML'] .= template::GetHTML('/view/form_list', ['FORM_LINKS_HTML' => $page_form_html]);
            $page_form_html = '';
        }
        ++$idx;
    }

    if (false === $found) {
        XLSXViewer::checkifexist($media);
    }
    if ('' != $page_form_html) {
        $params['FORM_LIST_HTML'] .= template::GetHTML('/view/form_list', ['FORM_LINKS_HTML' => $page_form_html]);
    }

    if ('' != $file_id) {
        $reader = IOFactory::createReader('Xlsx');

        $excel_file = $finder->getFile($files[$file_id]);

        $spreadsheet = $reader->load($excel_file);
        $sheet_names = $spreadsheet->getSheetNames();

        $params['SHEET_LIST_HTML'] = '';
        foreach ($sheet_names as $sheet_index => $sheet_name) {
            if ('Quick Sheet' == $sheet_name) {
                $quicksheet_index = $sheet_index;
                $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
                    'PAGE_FORM_URL' => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&file_id='.$file_id.'&sheet_id='.$sheet_index.'&quicksheet=1',
                    'PAGE_FORM_NUMBER' => 'quicksheet',
                    'SHEET_DISABLED' => 'enabled',
                    'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',

                    'SHEET_CLASS' => 'btn-info',
                ]);
                continue;
            }

            $class = 'enabled';
            if ($sheet_id == $sheet_index) {
                $class = 'disabled';
            }

            [$name,$_] = explode(' ', $sheet_name);
            [$sheetName,$former] = explode('_', $name);
            if (!isset($last)) {
                $last = '';
            }

            $cellValue = ucwords(strtolower($spreadsheet->getSheet($sheet_index)->getCellByColumnAndRow(2, 8)->getValue()));

            $sheet_form_array[$former][] = template::GetHTML('/view/sheet_link', [
                'PAGE_FORM_URL' => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&file_id='.$file_id.'&sheet_id='.$sheet_index,
                'PAGE_FORM_NUMBER' => $sheetName.' '.$cellValue,
                'SHEET_DISABLED' => $class,
                'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',
                'SHEET_CLASS' => 'bg-success',
            ]);
        }

        foreach ($sheet_form_array as $former => $buttons) {
            $button[0] = template::GetHTML('/view/former_button', ['FORMER_DESC' => $former]);
            $buttons = array_merge($button, $buttons);
            $sheet_links_html = implode("\n", $buttons);
            $params['SHEET_LIST_HTML'] .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_links_html]);
        }

        $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
            'PAGE_FORM_URL' => __URL_PATH__.'/form.php?job_id='.$media->job_id.'&form_number='.$current_form_number,
            'PAGE_FORM_NUMBER' => 'Edit Form',
            'SHEET_DISABLED' => 'enabled',
            'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',

            'SHEET_CLASS' => 'btn-info',
        ]);
        $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
            'PAGE_FORM_URL' => __URL_PATH__.'/process.php?job_id='.$media->job_id.'&form_number='.$current_form_number.'&action=update',
            'PAGE_FORM_NUMBER' => 'Update Excel Sheet',
            'SHEET_DISABLED' => 'enabled',
            'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',

            'SHEET_CLASS' => 'btn-info',
        ]);
        if (__SHOW_MAIL__ == true) {
            $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
                'PAGE_FORM_URL' => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&form_number='.$current_form_number.'&action=email',
                'PAGE_FORM_NUMBER' => 'Email Updated Excel Sheet',
                'SHEET_DISABLED' => 'enabled',
                'BUTTON_STYLE' => 'style="--bs-bg-opacity: .5;"',

                'SHEET_CLASS' => 'btn-info',
            ]);
        }
        $params['SHEET_LIST_HTML'] .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_edit_html]);

        $writer = IOFactory::createWriter($spreadsheet, 'Html');

        $writer->setSheetIndex($sheet_id);
        $custom_css = $writer->generateStyles(true);

        $rep_array = [
            '{border: 1px solid black;}' => '{border: 0px dashed red;}',
            'font-size:11pt;' => '',
            'font-size:11pt' => '',
            ' height:5pt' => ' height:0pt',
            ' height:25pt' => ' height:20pt',
            ' height:30pt' => ' height:25pt',
            ' height:35pt' => ' height:30pt',
            'tr { height:15pt }' => 'tr { height:0pt }',
            // 'page-break-before: always;' => 'display: none; page-break-before: always; page-break-after: auto;',
            // 'page-break-after: always;' => 'page-break-after: auto;',
            // '@media screen {' => '@media screen {'."\n".'.header { display: none; }',
            // '@media print {' => '@media print {'."\n".'.header {  }
            // ',
        ];

        if ($quicksheet_index != $sheet_id) {
            $rep_array['page-break-before: always;'] = 'display: none; page-break-before: always; page-break-after: auto;';
            $rep_array['page-break-after: always;'] = 'page-break-after: auto;';
            $rep_array['@media screen {'] = '@media screen {'."\n".'.header { display: none; }';
            $rep_array['@media print {'] = '@media print {'."\n".'.header {  }
    ';
        }

        foreach ($rep_array as $find => $replace) {
            $custom_css = str_replace($find, $replace, $custom_css);
        }

        $message = $writer->generateSheetData();
        /*
            $rep_array                 = [
                // "page: page0'>" => "page: page0' class='scrpgbrk'>",
                // 'column0 style7' => 'column0 style6',
                // 'scrpgbrk' => 'page-break'
            ];

            foreach ($rep_array as $find => $replace) {
                $message = str_replace($find, $replace, $message);
            }
        */

        if (!array_key_exists('quicksheet', $_REQUEST)) {
            $header = "<thead class='media: header'><tr><th colspan='4' class='text-center fs-1'>Media Load Flag</th></tr></thead> <tbody>";
        }

        $params['MESSAGE'] = $message;
    }

    MediaDevice::getHeader('', ['CUSTOM_CSS' => $custom_css]);

    $template->template('view/main', $params);
    $template->render();

    MediaDevice::getFooter();
} else {
    MediaError::msg('warning', 'Excel files are not found, try deleting and recreating');

    echo HTMLDisplay::JavaRefresh('/index.php', 0);
    exit;
}
