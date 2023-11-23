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

$addJobURL =  __URL_PATH__.'/process.php';

$templateBaseDir = 'createjob/job';
MediaDevice::getHeader();

$card_params['URL'] = $addJobURL;

$card_params['HIDDEN_FIELDS'] =  HTMLForms::draw_hidden('FORM_PROCESS', 'createJob');
$card_params['HIDDEN_FIELDS'] .= HTMLForms::draw_hidden('action', 'createjob');

$card_params['CARD_HEADER'] = ' Created By '.$auth->getUsername();
$card_params['JOBNUMBER_HTML'] = Template::GetHTML($templateBaseDir."/form/jobnumber", []);
$card_params['JOBNAME_HTML'] = Template::GetHTML($templateBaseDir."/form/jobname", ['PLACEHOLDER' => 'Oct 2023 C-Close...']);

$params['TEXT_FORMS'] = Template::GetHTML($templateBaseDir."/form/card", $card_params);
$params['NAME'] = 'Add Forms to Job';

$template->template('createjob/job/main', $params);

$template->render();


MediaDevice::getFooter();
