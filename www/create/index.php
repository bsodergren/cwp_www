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

MediaDevice::getHeader();
$form = new Formr\Formr('bootstrap', 'hush');

$hidden = [
    'FORM_PROCESS' => 'createJob',
    'action' =>'createjob'
];

$params['TEXT_FORMS'] = $form->open('Createjob', '', $addJobURL, '', '', $hidden);

$data = [
    'name' => 'job_number',
    'label' => 'Job Number',
    'id' => 'job_number',
    'value' => '',
    'maxlength' => '32',
    'class' => 'input',
    'placeholder' => 'job number...'
];

$params['TEXT_FORMS'] .= $form->text($data);
$data['name'] = 'media_drop';
$data['label'] = 'Drop Name';
$data['id'] = 'media_drop';
$data['placeholder'] = 'Oct 2023 C-Close...';

$params['TEXT_FORMS'] .= $form->text($data);
$params['TEXT_FORMS'] .= $form->submit_button('Create Job');
$params['TEXT_FORMS'] .= $form->close();

$params['NAME'] = "Create new Job";



$template->template('createjob/job/main', $params);

$template->render();


MediaDevice::getFooter();
