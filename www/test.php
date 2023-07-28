<?php
/**
 * CWP Media tool
 */




use CWP\Media\Media;
use CWP\HTML\Template;
use CWP\HTML\HTMLDisplay;


/**
 * CWP Media tool.
 */



require_once '.config.inc.php';
define('TITLE', 'Form Editor');



include_once __LAYOUT_HEADER__;


for ($i=0; $i<5; $i++) {
    sleep(1);

    HTMLDisplay::put('etate'. $i,'green');
    //echo "here I am<br />", $flushdummy;

}

