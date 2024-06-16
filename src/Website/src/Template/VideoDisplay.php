<?php
namespace Plex\Modules\Display;

use Plex\Modules\Database\PlexSql;
use CWP\Template\Render;


class VideoDisplay
{

    private $class;
    private $template_display = '';

    public function __construct($display = 'List')
    {
        $this->template_display = $display;
        $this->class = 'Plex\\Modules\\Display\\Layout\\'.$display.'Display';
    }


    public function init($template=null)
    {
        if($template !== null) {
            $this->template_display = $template;
        }
        return new $this->class($this->template_display);
    }
    public function RenderDisplay($displayHtml)
    {

        Render::Display( $displayHtml, $this->template_base.'/body');
    }
    public function Display($results,$page_array = [])
    {
        $html = $this->getDisplay($results,$page_array);
        $this->RenderDisplay($html);

    }
}
