<?php
/**
 * CWP Media Load Flag Creator.
 */

use CWP\Core\Media;

require_once '../.config.inc.php';
define('TITLE', 'Media Email List');
// $template = new Template();
use CWP\HTML\Forms\CardForm;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();

$table = Media::$explorer->table('email_list'); // UPDATEME
$table->select('id,name,email');
foreach ($table as $id => $row) {
    $name_id = '['.$row->id.']';
    $updateEmailTxt .= Template::GetHTML('emailList/text', [
        'EMAIL_ID' => $row->id,
        'NAME_ID' => $name_id,
        'EMAIL_NAME' => $row->name,
        'EMAIL' => $row->email,
        'DELETE_BOX' => Template::GetHTML('emailList/delete', ['NAME_ID' => $name_id]),
    ]);
}

$headerText = Template::GetHTML('emailList/header', []);
$updateCard = new CardForm('updateCard', 'updateEmail');
$updateCard->class = ' rounded mb-2 w-75';
$updateCard->cardHeader('Update Email list');
$updateCard->cardbutton('Update Emails', ['CLASS' => 'btn btn-success text-black fw-bold w-50 ']);
$updateCard->cardBody($headerText.$updateEmailTxt);
$param['EMAIL_LIST_ROW'] = $updateCard->card();

$addCard = new CardForm('addCard', 'addEmail');
$addCard->class = ' rounded mb-2 w-75';

$addCard->cardHeader('Add new Email Rcpt');
$addCard->cardbutton('Add Emails');
$addCard->cardBody($headerText.Template::GetHTML('emailList/text', []));

$param['EMAIL_NEW_EMAIL'] = $addCard->card();

$template->template('emailList/main', $param);

$template->render();
MediaDevice::getFooter();
