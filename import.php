<?php
require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
$template = new Template();

require_once __LAYOUT_HEADER__;

$template->render('import/main',[]);

require_once __LAYOUT_FOOTER__;
