<?php

use CWP\Media\Media;
use CWP\HTML\Template;
use CWP\Media\MediaFileSystem;
/**
 * CWP Media tool
 */

require_once '.config.inc.php';
define('TITLE', 'Import new Media drop');
// $template = new Template();

require_once __LAYOUT_HEADER__;

/* connect to gmail */

$locations                    = new MediaFileSystem();
$upload_directory             = $locations->getDirectory('upload', true);
$params                       = [];
$upload_params                = [];
$pdf_select_options           = [];

if (1 == __IMAP_ENABLE__) {
    /* try to connect */
    $imap = imap_open(__IMAP_HOST__.__IMAP_FOLDER__, __IMAP_USER__, __IMAP_PASSWD__); // || exit('Cannot connect to Gmail: '.imap_last_error());
    // dd($imap);

    if (imap_is_open($imap)) {
        $emails                       = imap_search($imap, 'UNSEEN');

        if ($emails) {
            foreach ($emails as $i => $m) {
                $message     = imap_qprint(imap_body($imap, $m, \FT_PEEK));
                $matched     =  preg_match_all('/(23[0-9]{4})/U', $message, $output_array);

                $structure   = imap_fetchstructure($imap, $m);

                $attachments = [];
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); ++$i) {
                        $attachments[$i] = [
                            'is_attachment' => false,
                            'filename'      => '',
                            'name'          => '',
                            'attachment'    => '',
                        ];

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if ('filename' == strtolower($object->attribute)) {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename']      = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if ('name' == strtolower($object->attribute)) {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name']          = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($imap, $m, $i + 1);
                            if (3 == $structure->parts[$i]->encoding) { // 3 = BASE64
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            } elseif (4 == $structure->parts[$i]->encoding) { // 4 = QUOTED-PRINTABLE
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }

                    imap_clearflag_full($imap, $m, '\\Seen');
                }

                foreach ($attachments as $attachment) {
                    if (true === $attachment['is_attachment']) {
                        $attachment_name = str_replace(',', '_', $attachment['name']);
                        $attachment_name = str_replace(' ', '_', $attachment_name);
                        if (true == stripos($attachment_name, 'RunSheets')
                        || true == stripos($attachment_name, 'Run_Sheets')) {
                            $filename = $upload_directory.\DIRECTORY_SEPARATOR.$attachment_name;
                            file_put_contents($filename, $attachment['attachment']);

                            $pdf_select_options['SELECT_OPTIONS'] .= template::GetHTML('/import/form_option', [
                                'OPTION_VALUE' => $filename.'|'.$m,
                                'OPTION_NAME'  => $attachment_name,
                            ]);
                        }
                    }
                }
                if (key_exists('SELECT_OPTIONS', $pdf_select_options)) {
                    $pdf_select_options['SELECT_NAME'] = 'mail_file';
                    $pdf_select_options['SELECT_DESC'] =  'Job Name';
                    $mail_import_card['FIRST_FORM']    =  template::GetHTML('/import/form_select', $pdf_select_options);

                    foreach (array_unique($output_array[0]) as $v => $job_number) {
                        $jn_select_options['SELECT_OPTIONS'] .= template::GetHTML('/import/form_option', [
                            'OPTION_VALUE' => $job_number,
                            'OPTION_NAME'  => $job_number,
                        ]);
                    }
                    $jn_select_options['SELECT_NAME']  = 'mail_job_number';
                    $jn_select_options['SELECT_DESC']  =  'Job Number';
                    $mail_import_card['SECOND_FORM']   =  template::GetHTML('/import/form_select', $jn_select_options);

                    // } else {
                    //    $mail_import_card['SECOND_FORM'] =  template::GetHTML('/import/form_text', ['JN_NAME' => 'mail_job_number']);
                    $mail_import_card['CARD_HEADER']   = 'Import from Gmail';
                    $params['EMAIL_IMPORT_HTML']       =  template::GetHTML('/import/form_card', $mail_import_card);
                }
            }
            imap_close($imap);
        }
    }
}
//	echo $output;

/* close the connection */
$import_card['CARD_HEADER']   = 'Import from Computer';
$import_card['FIRST_FORM']    =  template::GetHTML('/import/form_text', ['JN_NAME' => 'job_number']);
$import_card['SECOND_FORM']   =  template::GetHTML('/import/form_upload', []);
$import_card['BUTTON_SUBMIT'] =  template::GetHTML('/import/form_submit', []);
$params['UPLOAD_IMPORT_HTML'] =  template::GetHTML('/import/form_card', $import_card);

$template->render('import/main', $params);

require_once __LAYOUT_FOOTER__;
