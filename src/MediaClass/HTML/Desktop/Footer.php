<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML\Desktop;

use CWP\HTML\HTMLDocument;
use CWP\HTML\Template;

class Footer extends HTMLDocument
{
    // public $html;

    public static function display($template = '', $params = [])
    {
        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo Template::GetHTML('base/footer/footer', $params);
    }
}
