<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Spreadsheet\Media\MediaXLSX;

class Form extends MediaProcess
{
    public $form_number;

    public function run($req)
    {
        $this->form_number = $req['form_number'];
        $method = str_replace(' ', '', $req['submit']);
        $this->updateForm($req);

        // CheckMethod:
        if (!method_exists($this, $method)) {
            dd('missing Method', $method);
            // goto CheckMethod;
        }

        $this->$method();
    }

    public function updateForm($req)
    {
        $job_id = $req['job_id'];
        $skip_forms = '';
        $former = [];
        $updated = false;
        foreach ($req as $key => $value) {
            $break = false;

            if (str_starts_with($key, 'former')) {
                list($_, $id) = explode('_', $key);
                $count = Media::$explorer->table('form_data')->where('id', $id)->update(['former' => $value]); // UPDATEME
                if ($count > 0) {
                    $updated = true;
                }
            }

            if (str_starts_with($key, 'facetrim')) {
                list($_, $id) = explode('_', $key);
                $count = Media::$explorer->table('form_data')->where('id', $id)->update(['face_trim' => $value]); // UPDATEME
                if ($count > 0) {
                    $updated = true;
                }
            }

            if (str_starts_with($key, 'nobindery')) {
                list($_, $id, $letters) = explode('_', $key);
                $form_number = $id;

                $count = Media::$explorer->table('form_data')
                ->where('id', $id)
                ->update(['no_bindery' => $value]);
                if ($count > 0) {
                    $updated = true;
                }
            }
        }

        if (true == $updated) {
            Media::formUpdated($form_number, $job_id);
        }
    }

    public function Edit()
    {
        $this->url = '/form_edit.php?job_id='.$this->job_id.'&form_number='.$this->form_number.'';
        $this->timeout = 0;
    }

    public function SaveForm()
    {
        // if (true == array_key_exists('view', $_REQUEST)) {
        //     if ('save' == $_REQUEST['view']) {
        $this->url = '/index.php';
        $this->msg = 'Form finished';

        $this->media->excelArray();
        $excel = new MediaXLSX($this->media, true);
        $break = true;
    }

    public function Previous()
    {
        global $_REQUEST;
        $next_form_number = $_REQUEST['prev_form_number'];
        $this->getFormUrl($next_form_number);
    }

    public function Next()
    {
        global $_REQUEST;
        $next_form_number = $_REQUEST['form_number'];
        $this->getFormUrl($next_form_number);
    }

    public function getFormUrl($next_form_number)
    {
        global $_REQUEST;

        nextForm:
        $form_data = Media::$explorer->table('form_data'); // UPDATEME
        $form_data->where('form_number = ?', $next_form_number);
        $form_data->where('job_id = ?', $this->job_id);
        $results = $form_data->fetch();

        if (empty($results)) {
            if ('Previous' == $_REQUEST['submit']) {
                $next_form_number = $next_form_number - 1;
            } else {
                $next_form_number = $next_form_number + 1;
            }
            goto nextForm;
        }

        if ($next_form_number < 0) {
            $next_form_number = 1;
        }
        $this->url = '/form.php?job_id='.$this->job_id.'&form_number='.$next_form_number.'';
    }
}
