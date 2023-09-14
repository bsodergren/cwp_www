<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML\Desktop;

use CWP\HTML\Template;

class Footer extends Template
{
    // public $html;

    public static function display($template = '', $params = [])
    {
        $templateObj = new Template();
        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo $templateObj->template('base/footer/footer', $params);
    }
}
