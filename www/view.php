<?php
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

use CWP\HTML\Header;
use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Spreadsheet\XLSXViewer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Finder\Finder;

if ('email' == $_REQUEST['action']) {
    define('TITLE', 'Email excel zip file');
    require_once __LAYOUT_HEADER__;
    $template->render('view/mail_form', [
        '__FORM_URL__' => '/process.php',
        'FORM_NUMBER'  => $_REQUEST['form_number'],
        'JOB_ID'       => $_REQUEST['job_id']]);
    require_once __LAYOUT_FOOTER__;
    exit;
}

define('TITLE', 'View Form');

$form_number               = '';
$file_id                   = '';
$sheet_id                  = 0;

if (array_key_exists('form_number', $_REQUEST)) {
    $form_number = $_REQUEST['form_number'];
}

if (array_key_exists('file_id', $_REQUEST)) {
    $file_id = $_REQUEST['file_id'];
}

if (array_key_exists('sheet_id', $_REQUEST)) {
    $sheet_id = $_REQUEST['sheet_id'];
}
if (!is_dir($media->xlsx_directory)) {
    XLSXViewer::checkifexist($media);
}

$finder                    = new Finder();

$finder->files()->in($media->xlsx_directory)->name('*.xlsx')->notName('~*')->sortByName(true);
$found                     = false;

if (!$finder->hasResults()) {
    XLSXViewer::checkifexist($media);
}

$idx                       = 0;
$params['FORM_LIST_HTML']  = '';
$params['EXCEL_DIRECTORY'] = $media->xlsx_directory;
$excel_link                = '';

foreach ($finder as $file) {
    $files[]                  = $file->getRealPath();
    $class                    = 'enabled';
    preg_match('/.*_([FM0-9]+).xlsx/', $file->getRealPath(), $output_array);
    [$text_form,$text_number] = explode('FM', $output_array[1]);

    if ('' != $form_number) {
        if ($form_number == $text_number) {
            $file_id = $idx;
            $found   = true;
        }
    } else {
        $found = true;
    }

    if ($file_id == $idx) {
        $class                = 'disabled';
        $current_form_number  = $text_number;

        // $url_link             = HTMLDisplay::draw_excelLink($file->getRealPath());
        // $params['EXCEL_LINK'] = Template::GetHTML('/view/sheet_link', [
        //     'PAGE_FORM_URL'    => $url_link,
        //     'PAGE_FORM_NUMBER' => $file->getfilename(),
        //     'SHEET_DISABLED'   => 'enabled',
        //     'BUTTON_STYLE'     => 'style="--bs-bg-opacity: .5;"',
        //     'SHEET_CLASS'      => 'btn-info',
        // ]);
    }

    $page_form_html .= template::GetHTML('/view/form_link', [
        'PAGE_FORM_URL'    => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&file_id='.$idx,
        'PAGE_FORM_NUMBER' => 'FM '.$text_number,
        'FORM_DISABLED'    => $class,
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
    $reader                    = IOFactory::createReader('Xlsx');
    $spreadsheet               = $reader->load($files[$file_id]);
    $sheet_names               = $spreadsheet->getSheetNames();

    $params['SHEET_LIST_HTML'] = '';
    foreach ($sheet_names as $sheet_index => $sheet_name) {
        if ('Quick Sheet' == $sheet_name) {
            $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
                'PAGE_FORM_URL'    => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&file_id='.$file_id.'&sheet_id='.$sheet_index.'&quicksheet=1',
                'PAGE_FORM_NUMBER' => 'quicksheet',
                'SHEET_DISABLED'   => 'enabled',
                'BUTTON_STYLE'     => 'style="--bs-bg-opacity: .5;"',

                'SHEET_CLASS'      => 'btn-info',
            ]);
            continue;
        }

        $class                       = 'enabled';
        if ($sheet_id == $sheet_index) {
            $class = 'disabled';
        }

        [$name,$_]                   = explode(' ', $sheet_name);
        [$sheetName,$former]         = explode('_', $name);
        if (!isset($last)) {
            $last = '';
        }

        $cellValue                   = ucwords(strtolower($spreadsheet->getSheet($sheet_index)->getCellByColumnAndRow(2, 8)->getValue()));

        $sheet_form_array[$former][] = template::GetHTML('/view/sheet_link', [
            'PAGE_FORM_URL'    => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&file_id='.$file_id.'&sheet_id='.$sheet_index,
            'PAGE_FORM_NUMBER' => $sheetName.' '.$cellValue,
            'SHEET_DISABLED'   => $class,
            'BUTTON_STYLE'     => 'style="--bs-bg-opacity: .5;"',
            'SHEET_CLASS'      => 'bg-success',
        ]);
    }

    foreach ($sheet_form_array as $former => $buttons) {
        $button[0]        = template::GetHTML('/view/former_button', ['FORMER_DESC' => $former]);
        $buttons          = array_merge($button, $buttons);
        $sheet_links_html = implode("\n", $buttons);
        $params['SHEET_LIST_HTML'] .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_links_html]);
    }

    $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
        'PAGE_FORM_URL'    => __URL_PATH__.'/form.php?job_id='.$media->job_id.'&form_number='.$current_form_number,
        'PAGE_FORM_NUMBER' => 'Edit Form',
        'SHEET_DISABLED'   => 'enabled',
        'BUTTON_STYLE'     => 'style="--bs-bg-opacity: .5;"',

        'SHEET_CLASS'      => 'btn-info',
    ]);
    $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
        'PAGE_FORM_URL'    => __URL_PATH__.'/process.php?job_id='.$media->job_id.'&form_number='.$current_form_number.'&action=update',
        'PAGE_FORM_NUMBER' => 'Update Excel Sheet',
        'SHEET_DISABLED'   => 'enabled',
        'BUTTON_STYLE'     => 'style="--bs-bg-opacity: .5;"',

        'SHEET_CLASS'      => 'btn-info',
    ]);

    $params['SHEET_LINKS'] .= template::GetHTML('/view/sheet_link', [
        'PAGE_FORM_URL'    => __URL_PATH__.'/view.php?job_id='.$media->job_id.'&form_number='.$current_form_number.'&action=email',
        'PAGE_FORM_NUMBER' => 'Email Updated Excel Sheet',
        'SHEET_DISABLED'   => 'enabled',
        'BUTTON_STYLE'     => 'style="--bs-bg-opacity: .5;"',

        'SHEET_CLASS'      => 'btn-info',
    ]);
    $params['SHEET_LIST_HTML'] .= template::GetHTML('/view/sheet_list', ['SHEET_LINKS_HTML' => $sheet_edit_html]);

    $writer                    = IOFactory::createWriter($spreadsheet, 'Html');

    $writer->setSheetIndex($sheet_id);

    $custom_css                = $writer->generateStyles(true);

    $rep_array                 = [
        '{border: 1px solid black;}' => '{border: 0px dashed red;}',
        'font-size:11pt;'            => '',
        'font-size:11pt'             => '',
        ' height:5pt'                => ' height:0pt',
        ' height:25pt'               => ' height:20pt',
        ' height:30pt'               => ' height:25pt',
        ' height:35pt'               => ' height:30pt',
        'tr { height:15pt }'         => 'tr { height:0pt }',
        'page-break-before: always;' => 'display: none; page-break-before: always; page-break-after: auto;',
        'page-break-after: always;'  => 'page-break-after: auto;',
        '@media screen {'            => '@media screen {'."\n".'.header { display: none; }',
        '@media print {'             => '@media print {'."\n".'.header {  }
        ',
    ];

    foreach ($rep_array as $find => $replace) {
        $custom_css = str_replace($find, $replace, $custom_css);
    }

    $message                   = $writer->generateSheetData();
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

    $params['MESSAGE']         = $message;
}

Header::Display('', ['CUSTOM_CSS' => $custom_css]);

$template->template('view/main', $params);
$template->render();

include_once __LAYOUT_FOOTER__;
