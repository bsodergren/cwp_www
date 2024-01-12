<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\Utils\Utils;

require_once '../.config.inc.php';
define('TITLE', 'Media Email List');
// $template = new Template();
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Core\MediaSettings;
use CWP\HTML\Forms\CardForm;

MediaDevice::getHeader();



$table = Media::$explorer->table('email_list'); // UPDATEME
$table->select('id,name,email');
foreach ($table as $id => $row) {

    $updateEmailTxt .= Template::GetHTML('emailList/text', [
        'EMAIL_ID' => $row->id,
        'EMAIL_NAME' => $row->name,
        'EMAIL' => $row->email,
    ]);
}

$updateCard = new CardForm("updateCard", "updateEmail");
$updateCard->class = " rounded mb-2";
$updateCard->cardHeader("Update Email list");
$updateCard->cardBody($updateEmailTxt, "Update Emails");
$param['EMAIL_LIST_ROW'] = $updateCard->card();

$addCard = new CardForm("addCard", "addEmail");
$addCard->class = " rounded mb-2";

$addCard->cardHeader("Add new Email Rcpt");
$addCard->cardBody(Template::GetHTML('emailList/text', []), "Add Emails");

$param['EMAIL_NEW_EMAIL']  = $addCard->card();

$template->template('emailList/main', $param);

$template->render();
MediaDevice::getFooter();
