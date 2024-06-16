<?php

namespace  CWPDisplay\Template;

use  CWPDisplay\Template\Layout;
use UTMTemplate\Render as UTMTemplateRender;

class Render extends UTMTemplateRender
{
    // public function __construct() {}

    public static function Display($array = '', $template = 'base/body')
    {
        Layout::Header();
        self::echo($template, ['BODY' => $array]);
        Layout::Footer();
    }

    public $percentDone = 0;
    public $pbid;
    public $pbarid;
    public $tbarid;
    public $textid;
    public $decimals = 1;

    public function __construct($percentDone = 0)
    {
        $this->pbid = 'pb';
        $this->pbarid = 'progress-bar';
        $this->tbarid = 'transparent-bar';
        $this->textid = 'pb_text';
        $this->percentDone = $percentDone;
    }

    public function render()
    {
        // print ($GLOBALS['CONTENT']);
        // $GLOBALS['CONTENT'] = '';
        echo $this->getContent();
        $this->flush();
        // $this->setProgressBarProgress(0);
    }

    public function getContent()
    {
        $this->percentDone = (float) $this->percentDone;
        $percentDone = number_format($this->percentDone, $this->decimals, '.', '');

        $content .= self::html(
            'elements/ProgressBar/progressbar',
            [
                'pbid' => $this->pbid,
                'textid' => $this->textid,
                'percentDone' => $percentDone.'%',
                'BootpercentDone' => $percentDone,
                'pbarid' => $this->pbarid,
                'tbarid' => $this->tbarid,
            ]
        );

        return $content;
    }

    public function setProgressBarHeader($text)
    {
        echo '<script type="text/javascript">';
        echo 'document.getElementById("downloadText").innerHTML = "'.htmlspecialchars($text).'";';
        echo "\n".'</script>'."\n";
        $this->flush();
    }

    public function setProgressBarProgress($percentDone, $text = '')
    {
        // utminfo($percentDone);
        $this->percentDone = $percentDone;
        $text = $text ?: number_format($this->percentDone, $this->decimals, '.', '');

        echo self::html(
            'elements/ProgressBar/javascript',
            [
                'pbid' => $this->pbid,
                'textid' => $this->textid,
                'text' => $text.'%',
                'percentNumber' => $text,
                'percentDone' => $percentDone.'%',
                'stylepercentDone' => $percentDone.'%',
                'pbarid' => $this->pbarid,
                'tbarid' => $this->tbarid,
            ]
        );

        $this->flush();
    }

    public function flush()
    {
        echo str_pad('', (int) \ini_get('output_buffering'))."\n";
        // ob_end_flush();
        flush();
    }
}
