<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Template\Desktop;

use CWP\Template\HTMLDocument;
use CWP\Template\Template;

class Footer extends HTMLDocument
{
    // public $html;

    public static function display($template = '', $params = [])
    {
        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo Template::GetHTML('base/footer/footer', $params);
    }
}
