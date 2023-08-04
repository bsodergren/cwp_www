<?php
/**
 * CWP Media tool
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Media\Media;
use CWP\Spreadsheet\Media\MediaXLSX;

class Form extends MediaProcess
{
    public $form_number;

    public function run($req)
    {
        $this->form_number = $req['form_number'];

        $method = $req['submit'];
        $this->updateForm($req);
        $this->$method();
    }

    public function updateForm($req)
    {
        $job_id = $req['job_id'];

        foreach ($req as $key => $value) {
            $break = false;
            if (str_starts_with($key, 'former')) {
                list($front, $id) = explode('_', $key);
                $count = Media::$explorer->table('form_data')->where('id', $id)->update(['former' => $value]);
            }

            if (str_starts_with($key, 'facetrim')) {
                list($front, $id) = explode('_', $key);
                $count = Media::$explorer->table('form_data')->where('id', $id)->update(['face_trim' => $value]);
            }

            if (str_starts_with($key, 'nobindery')) {
                list($front, $id, $letters) = explode('_', $key);

                // dd($front,$id,$letters,$value, $job_id);

                $count = Media::$explorer->table('form_data')->where('job_id', $job_id)
                ->where('form_number', $id)
                ->where('form_letter', $letters)
                ->update(['no_bindery' => $value]);
            }
        }
    }

    public function Edit()
    {
        $this->url = '/form_edit.php?job_id='.$this->job_id.'&form_number='.$this->form_number.'';
        $this->timeout = 0;
    }

    public function Save()
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
        $form_data = Media::$explorer->table('form_data');
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
