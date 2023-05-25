<?php
require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
//$template = new Template();

require_once __LAYOUT_HEADER__;


/* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}CWP';
$username =  $conf['gmail']['name'];
$password = $conf['gmail']['password'];

/* try to connect */
$imap = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

$emails = imap_search($imap, 'UNSEEN');

$locations = new MediaFileSystem();
$upload_directory = $locations->getDirectory('upload', true);
$params = [];
$upload_params = [];

if ($emails) {

    foreach ($emails as $i => $m) {

        $message = imap_qprint(imap_body($imap, $m, FT_PEEK));
        $matched =  preg_match_all('/(23[0-9]{4})/U', $message, $output_array);

        $structure = imap_fetchstructure($imap, $m);

        $attachments = array();
        if (isset($structure->parts) && count($structure->parts)) {

            for ($i = 0; $i < count($structure->parts); $i++) {

                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($imap, $m, $i + 1);
                    if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }

            imap_clearflag_full($imap, $m, "\\Seen");
        }

        foreach ($attachments as $attachment) {
            if ($attachment['name'] == true) {
                if (str_contains($attachment['name'], "Itasca") == true) {
                    $filename = $upload_directory . DIRECTORY_SEPARATOR . $attachment['name'];
                    file_put_contents($filename, $attachment['attachment']);

                    $upload_params['EMAIL_INPORT_ROWS'] .=  template::GetHTML('/import/email_inport_row', [
                        'MAIL_PDF_FILE' => $filename . "|" . $m,
                        'MAIL_PDF_FILENAME' => $attachment['name'],
                    ]);
                }
            }
        }
        if ($matched == true) {

            foreach (array_unique($output_array[0]) as $v => $job_number) {
                $params['EMAIL_JOB_NUMBER_ROWS'] .=  template::GetHTML('/import/email_job_number_row', [
                    'MAIL_JOB_NUMBER' => $job_number,
                ]);
            }
        }
    }

    $params['EMAIL_INPORT_HTML'] =  template::GetHTML('/import/email_inport', $upload_params);
}

//	echo $output;

/* close the connection */
imap_close($imap);

$template->render('import/main', $params);

require_once __LAYOUT_FOOTER__;
