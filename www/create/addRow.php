<?php


use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Filesystem\MediaFileSystem;

define('__AUTH__', true);
require_once '../.config.inc.php';
$template_basedir = 'createjob/formRow';
define('TITLE', APP_NAME);
$job_id = $_GET['job_id'];

$form_number = $_GET['form_number'];
MediaDevice::getHeader();

$auto_js = Template::getJavascript($template_basedir.'/autocomplete', []);
$addRow_html = Template::getHTML($template_basedir.'/addRow', ['AUTOCOMPLETE_JS' => $auto_js,'FORM_NUMBER' => $form_number,'JOB_ID' => $job_id]);

$job_table = Media::$explorer->table('form_data');
$job_table->where('job_id = ?', $job_id);
$job_table->where('form_number = ?', $form_number);

$next = '';
foreach ($job_table as $u) {
    if(isset($current_letter)) {
        if($current_letter != $u->form_letter) {
            $next = '<div class="row"><p></p></div>';
        } else {
            $next = '';
        }
    }
    $array = ['LETTER' => $u->form_letter,
        'MARKET' => $u->market,
        'PUBLICATION' => $u->pub,
        'COUNT' => $u->count,
        'SHIP' => $u->ship,
        'NEXT' => $next,
    ];

    $html_rows .= Template::getHTML($template_basedir.'/rows', $array);
    $current_letter = $u->form_letter;
}





$template->template($template_basedir.'/main', ['ROWS' => $html_rows,'ADDROW' => $addRow_html,'NAME' => 'Test Job']);

$template->render();


MediaDevice::getFooter();
