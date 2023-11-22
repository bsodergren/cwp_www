<?php


use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLForms;

define('__AUTH__', true);
require_once '../.config.inc.php';

define('TITLE', APP_NAME);
$view = $_GET['v'];



MediaDevice::getHeader();

$infotable =  "job_".$view;
$templatePath = "createjob/view";
$table = Media::$explorer->table($infotable);

//$table->order('setting_type ASC');
$results = $table->fetchAssoc('name');
//$query = "SELECT name FROM ".$table; // WHERE search_table = '".$table."' ORDER BY search_id DESC LIMIT 10";
)
foreach ($results as $k => $u) {
    $params['CELL_HTML'] .= Template::GetHTML($templatePath."/cell", ['NAME' => $u['name']]);
}
//$result = $connect->query($query);
$params['NAME'] = $view;



$template->template($templatePath.'/main', $params);

$template->render();


MediaDevice::getFooter();
