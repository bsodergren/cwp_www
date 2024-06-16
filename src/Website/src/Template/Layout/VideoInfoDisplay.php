<?php
namespace Plex\Modules\Display\Layout;

use Plex\Modules\Display\Layout\ListDisplay;

class VideoInfoDisplay extends ListDisplay
{
    public $showVideoDetails = false;
    public $template_base   = '';


    public function __construct($template_base = 'VideoInfo')
    {
        $this->template_base = 'pages'.DIRECTORY_SEPARATOR. $template_base;

    }
}
