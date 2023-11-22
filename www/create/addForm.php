<?php

use CWP\Core\Media;
use CWP\Utils\MediaDevice;

define('__AUTH__', true);
require_once '../.config.inc.php';

define('TITLE', APP_NAME);
$job_id = $_GET['job_id'];

MediaDevice::getHeader();

$addJobURL =  __URL_PATH__.'/process.php';
//Former::framework('twitterbootstrap3');
//echo Former::text('foo')->addClass('my_class');
// $params['TEXT_FORMS'] = $form;

$result = Media::$explorer->table('media_job')->where('job_id', $job_id)->select('close')->fetchAll();
$job_close = $result[0]->close;

$form = new Formr\Formr('bootstrap', 'hush');

$hidden = [
    'FORM_PROCESS' => 'createJob',
    'action' => 'addForm',
    'job_id' => $job_id,
    'product' => $job_close,
];

$params['TEXT_FORMS'] = $form->open('addForms', '', $addJobURL, '', '', $hidden);

$data = [
    'name' => 'form_number',
    'label' => 'Form Number',
    'id' => 'form_number',
    'value' => '',
    'maxlength' => '5',
    'class' => 'input',

];

$params['TEXT_FORMS'] .= $form->text($data);
$data['name'] = 'form_count';
$data['label'] = 'Imp Count';
$data['id'] = 'form_count';

$params['TEXT_FORMS'] .= $form->text($data);


// notice that we added a 'selected' value in the 7th parameter
foreach(Media::$pageType as $type) {
    $case = str_replace(" ", "", $type);

    switch ($case) {
        case '2+2pgs4out':
            $key = '4pg';
            $types[$key] = $type;
            break;
        case '2+4pgs2out':

            $key = '6pg';
            $types[$key] = $type;
            break;
        case '4pgs4out':

            $key = '4pg_';
            $types[$key] = $type;
            break;
        case '4+2pgs2out':

            $key = '6pg_';
            $types[$key] = $type;
            break;
        case '6pgs2out':

            $key = '6pg__';
            $types[$key] = $type;
            break;
        case '4+4pgs2out':

            $key = '8pg';
            $types[$key] = $type;
            break;
        case '8pgs2out':
            $key = '8pg_';
            $types[$key] = $type;
            break;
    }
}
foreach(Media::$bindType as $type) {
    $bindTypes[$type] = $type;
}

$params['TEXT_FORMS'] .= $form->select('config', 'Configuration', '', '', '', true, '', $types);
$params['TEXT_FORMS'] .= $form->select('bind', 'Bind Style', '', '', '', '', '', $bindTypes);

// HTMLForms::draw_select("config","Bind Types",$types);



$params['TEXT_FORMS'] .= $form->submit_button('Add Form');
$params['TEXT_FORMS'] .= $form->close();

$template->template('createjob/forms/main', $params);

$template->render();


MediaDevice::getFooter();
