<?php
/**
 * CWP Media Load Flag Creator
 */

require_once '.config.inc.php';

use CWP\Core\MediaError;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Template\Pages\View;
use CWP\Filesystem\MediaFinder;
use CWP\Spreadsheet\XLSXViewer;
use PhpOffice\PhpSpreadsheet\IOFactory;

if ('email' == $_REQUEST['action']) {
    define('TITLE', 'Email excel zip file');
    MediaDevice::getHeader();
    $template->render('view/mail_form', [
        'FORM_NUMBER' => $_REQUEST['form_number'],
        'JOB_ID' => $_REQUEST['job_id']]);
    MediaDevice::getFooter();
    exit;
}

define('TITLE', 'View Form');


$finder = new MediaFinder($media);
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
                $params['EXCEL_LINK'] = View::SheetLink(basename($file), $url_link, 'btn-info', '--bs-bg-opacity: .5;', 'enabled');
            }
        }

        $page_form_html .= View::FormButton(
            'FM ' . $text_number,
            __URL_PATH__ . '/view.php?job_id=' . $media->job_id . '&file_id=' . $idx,
            $class
        );

        if (0 == $idx % 9 && $idx > 0) {
            $params['FORM_LIST_HTML'] .= View::FormButtonList($page_form_html);
            $page_form_html = '';
        }
        ++$idx;
    }

    if (false === $found) {
        XLSXViewer::checkifexist($media);
    }
    if ('' != $page_form_html) {
        $params['FORM_LIST_HTML'] .= View::FormButtonList($page_form_html);
    }

    if ('' != $file_id) {

        $excel_file = $finder->getFile($files[$file_id]);
        $viewer = new XLSXViewer($excel_file, $file_id);
        $viewer->media = $media;

        $viewer->buildPage();
        $params = array_merge($params, $viewer->params);


        $name[] =  "Edit Form";
        $name[] = 'Update Excel Sheet';
        $name[] = 'Email Updated Excel Sheet';


        $url[] = __URL_PATH__ . '/form.php?job_id=' . $media->job_id . '&form_number=' . $current_form_number;
        $url[] = __PROCESS_FORM__ . '?job_id=' . $media->job_id . '&form_number=' . $current_form_number . '&action=update';
        $url[] = __URL_PATH__ . '/view.php?job_id=' . $media->job_id . '&form_number=' . $current_form_number . '&action=email';


        $params['SHEET_LINKS'] =  View::SheetLink($name, $url, 'btn-warning', '--bs-bg-opacity: .5;', 'enabled');

        //$params['SHEET_LIST_HTML'] .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_edit_html]);

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

        $params['MESSAGE'] = $viewer->getExcelPage();
    }

    $TplTemplate->assign('custom_css', $viewer->custom_css);
    $TplTemplate->assign('Array', $params);
    $TplTemplate->draw('body');

} else {
    MediaError::msg('warning', 'Excel files are not found, try deleting and recreating');

    echo HTMLDisplay::JavaRefresh('/index.php', 0);
    exit;
}
