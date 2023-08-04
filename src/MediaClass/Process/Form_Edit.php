<?php
/**
 * CWP Media tool
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Media\Import\PDFImport;
use CWP\Media\Media;
use CWP\Media\MediaError;

class Form_Edit extends MediaProcess
{
    public $form_number;
    public $form_edit;

    public function run($req)
    {
        $this->form_number = $req['form_number'];
        $this->form_edit['url'] = __URL_PATH__.'/form_edit.php?job_id='.$this->job_id.'&form_number='.$this->form_number;
        $this->form_edit['timeout'] = 0;

        if (key_exists('Reset', $req)) {
            $this->Reset();
        }

        if (array_key_exists('submit', $req)) {
            $this->Save();
        }


        // $method = $req['submit'];
        // $this->updateForm($req);
        // $this->$method();
    }

    public function Reset()
    {
        $this->media->delete_form($this->form_number);
        $pdfObj = new PDFImport();
        $pdfObj->processPdf($this->media->pdf_fullname, $this->media->job_id, $this->form_number);
        $this->media->add_form_data($this->form_number, $pdfObj->form[$this->form_number]);
        MediaError::msg('info', 'Resetting all Form Data', $this->form_edit);
    }

    public function save()
    {
        global $_REQUEST;

        foreach ($_REQUEST as $key => $value) {
            if ('job_id' == $key) {
                continue;
            }
            if ('form_number' == $key) {
                continue;
            }
            if ('submit' == $key) {
                continue;
            }
            if ('tracy-session' == $key) {
                continue;
            }

            list($id, $action) = explode('_', $key);

            if ($deleted_id == $id) {
                continue;
            }

            unset($data);
            if ('' != $value) {
                switch ($action) {
                    case 'delete':
                        if (1 == $value) {
                            $this->media->deleteFormRow($id);
                            $deleted_id = $id;
                        }
                        //	MediaError::msg("info","Deleting row",$form_edit);
                        break;

                    case 'split':
                        if (1 == $value) {
                            $form_data = $this->media->getFormRow($id);
                            $form_data['count'] = ($form_data['count'] / 2);
                            $this->media->updateFormRow($id, $form_data);
                            unset($form_data['id']);
                            $this->media->addFormRow($form_data);
                        }
                        //	MediaError::msg("info","Splitting Form",$form_edit);
                        break;

                    case 'formletter':
                        $data = ['form_letter' => strtoupper($value)];
                        break;

                    case 'facetrim':
                        $data = ['face_trim' => $value];
                        break;

                    case 'former':
                        $data = ['former' => $value];
                        break;

                    case 'pcscount':
                        $value = str_replace('*', 'x', $value);
                        if (str_contains($value, 'x')) {
                            list($x, $n) = explode('x', $value);
                            $value = $x * $n;
                        }
                        if (str_contains($value, '/')) {
                            list($x, $n) = explode('/', $value);
                            $value = $x / $n;
                        }
                        $data = ['count' => $value];

                        break;
                }

                if (isset($data)) {
                    $this->media->updateFormRow($id, $data);
                }
            }
        }

        MediaError::msg('warning', 'Updated form <br> ', $this->form_edit);

    }
}
