<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Mail;

use CWP\HTML\Template;

class EmailDisplay
{
    public $attachments;

    public function drawFileOptions()
    {
        foreach ($this->attachments as $key => $attachment) {
            $html .= template::GetHTML('/import/form_option', [
                'OPTION_VALUE' => $attachment['filename'].'|'.$key,
                'OPTION_NAME' => $attachment['name'],
            ]);
        }

        return $html;
    }

    public function drawJsScript()
    {
        $js_select_options = null;

        foreach ($this->attachments as $key => $attachment) {
            if (key_exists('JobNumber', $attachment)) {
                if (is_array($attachment['JobNumber'])) {
                    $option_html = '';
                    foreach ($attachment['JobNumber'] as $number) {
                        $option_html .= trim(template::GetHTML('/import/js_select_options', [
                            'JOB_NUMBER' => $number,
                        ]));
                    }

                    $js_select_options .= template::GetHTML(
                        '/import/js_select_statement',
                        [
                            'JS_SELECT_KEY' => $attachment['filename'].'|'.$key,
                            'JS_SELECT_OPTIONS' => $option_html,
                    ]);
                }
            }
        }

        if (null !== $js_select_options) {
            return template::GetHTML(
                '/import/js_select',
                [
                    'JS_SELECT_STATEMENTS' => $js_select_options,
        ]);
        }

        return '';
    }

    public function drawSelectBox()
    {
        return template::GetHTML(
            '/import/form_select',
            [
                'SELECT_OPTIONS' => $this->drawFileOptions(),
                'JS_SELECT' => $this->drawJsScript(),
        ]);
    }
}
