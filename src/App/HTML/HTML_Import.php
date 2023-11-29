<?php
namespace CWP\HTML;


use CWP\Core\Media;
use CWP\Template\Template;

class HTML_Import
{


    public static function drawFileOptions($params)
    {

        $html = '';

        foreach ($params as $key => $attachment) {
            $html .= template::GetHTML('/import/file/form_option', [
                'OPTION_VALUE' => $attachment['filename'],
                'OPTION_NAME' => $attachment['name'],
            ]);
        }

        return $html;
    }

    public static function drawJsScript($array)
    {
        $js_select_options = null;

        foreach ($array as $key => $attachment) {
            if (\array_key_exists('JobNumber', $attachment)) {
                if (\is_array($attachment['JobNumber'])) {
                    $option_html = '';
                    foreach ($attachment['JobNumber'] as $number) {
                        $option_html .= trim(template::GetHTML('/import/file/js_select_options', [
                            'JOB_NUMBER' => $number,
                        ]));
                    }

                    $js_select_options .= template::GetHTML(
                        '/import/file/js_select_statement',
                        [
                            'JS_SELECT_KEY'     => $attachment['name'],
                            'JS_SELECT_OPTIONS' => $option_html,
                        ]
                    );
                }
            }
        }

        if (null !== $js_select_options) {
            return template::GetHTML(
                '/import/file/js_select',
                [
                    'JS_SELECT_STATEMENTS' => $js_select_options,
                ]
            );
        }

        return '';
    }

    public  static function drawSelectBox($pdfArray)
    {


        foreach($pdfArray as $k => $data){
            $v = self::getImportJobNumbers($data['name']);
            $params[] = ['name' => $data['name'],
            'JobNumber' => explode(",",$v->job_number),
            'filename' => $data['filename']];
        }
        return template::GetHTML(
            '/import/file/form_select',
            [
                'SELECT_OPTIONS' => self::drawFileOptions($params),
                'JS_SELECT'      => self::drawJsScript($params),
            ]
        );
    }

    public static function getImportJobNumbers($pdf_file)
    {
        return Media::$explorer->table('media_imports')->where('pdf_file = ?', $pdf_file)->fetch();

    }
}