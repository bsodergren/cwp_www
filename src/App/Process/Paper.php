<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Media\MediaMailer;
use CWP\Process\Traits\File;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Spreadsheet\XLSXViewer;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;

use Symfony\Component\Finder\Finder;

class Paper extends MediaProcess
{
    use File;

    public $form_number;

    public $page_end;

    public function run($req)
    {
        //utmdd($req);
        $this->request = $req;

        
        $this->update();
        echo HTMLDisplay::JavaRefresh('/paper.php', 0);
    }

    public function update()
    {
        if(array_key_exists('submit',$this->request)){
            unset($this->request['submit']);

        }
        foreach($this->request as $paper_id => $data)
        {
            if(!is_array($data)){
                continue;
            }
            foreach($data as $k => $v){
                if($v == ''){
                    $newdata[$k] = 0;
                } else {
                    $newdata[$k] = $v;
                }
            }

            $count = Media::$explorer->table('paper_count')->where('paper_id', $paper_id)->update($newdata);
        }
    }
}
