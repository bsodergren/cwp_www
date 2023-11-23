<?php

use CWP\Core\Media;
use CWP\HTML\HTMLForms;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

define('__AUTH__', true);
require_once '../.config.inc.php';

define('TITLE', APP_NAME);
$job_id = $_GET['job_id'];

MediaDevice::getHeader();
$templateBaseDir = 'createjob/forms';

$addJobURL =  __URL_PATH__.'/process.php';
//Former::framework('twitterbootstrap3');
//echo Former::text('foo')->addClass('my_class');
// $params['TEXT_FORMS'] = $form;

$result = Media::$explorer->table('media_job')->where('job_id', $job_id)->select('close')->fetchAll();
$job_close = $result[0]->close;

$result = Media::$explorer->table('media_forms')->where('job_id', $job_id)->select('form_number')->fetchAll();

foreach($result as $row) {
    $form_button_links .= Template::GetHTML($templateBaseDir.'/formlink', [
        'PAGE_CLASS' => ' btn-success',
        'PAGE_FORM_URL' => __URL_PATH__.'/create/addRow.php?job_id='.$job_id.'&form_number='. $row->form_number,
        'PAGE_FORM_NUMBER' => $row->form_number
    ]);
}

$params['FORM_BUTTONS'] =  $form_button_links ;

$card_params['URL'] = $addJobURL;
$card_params['HIDDEN_FIELDS'] =  HTMLForms::draw_hidden('FORM_PROCESS', 'createJob');
$card_params['HIDDEN_FIELDS'] .= HTMLForms::draw_hidden('action', 'addForm');
$card_params['HIDDEN_FIELDS'] .= HTMLForms::draw_hidden('job_id', $job_id);
$card_params['HIDDEN_FIELDS'] .= HTMLForms::draw_hidden('product', $job_close);


$card_params['FORMNUMBER_HTML'] = Template::GetHTML($templateBaseDir."/form/textbox", [
    'NAME' => 'form_number',
    'DESCRIPTION' => 'Form Number',
    'PLACEHOLDER' => 'Form Number',
    'LABEL_TEXT' => 'Form Number',
]);
$card_params['FORM_PCCOUNT'] = Template::GetHTML($templateBaseDir."/form/textbox", [
    'NAME' => 'pcs_count',
    'DESCRIPTION' => 'Pcs Count',
    'PLACEHOLDER' => 'Pcs Count',
    'LABEL_TEXT' => 'Pcs Count',
]);
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

    $config_options .= Template::GetHTML($templateBaseDir."/form/form_option", [
        'OPTION_VALUE' => $key,
        'OPTION_NAME' => $type
    ]);
}

foreach(Media::$bindType as $type) {
    $bind_options .= Template::GetHTML($templateBaseDir."/form/form_option", [
        'OPTION_VALUE' => $type,
        'OPTION_NAME' => $type
    ]);
}

$card_params['BINDTYPE_HTML'] = Template::GetHTML($templateBaseDir."/form/form_select", [
    'SELECT_OPTIONS' => $bind_options,
    'SELECT_NAME' => 'bind',
    'SELECT_ID' => 'BindName',
    'SELECT_DESC' => 'Book Binding',
]);
$card_params['CONFIG_HTML'] = Template::GetHTML($templateBaseDir."/form/form_select", [
    'SELECT_OPTIONS' => $config_options,
    'SELECT_NAME' => 'config',
    'SELECT_ID' => 'ConfigName',
    'SELECT_DESC' => 'Configuration',
]);



$params['TEXT_FORMS'] =  Template::GetHTML($templateBaseDir."/form/card", $card_params);
// HTMLForms::draw_select("config","Bind Types",$types);



$params['NAME'] = "Add Forms to Job";
$template->template('createjob/forms/main', $params);

$template->render();


MediaDevice::getFooter();
