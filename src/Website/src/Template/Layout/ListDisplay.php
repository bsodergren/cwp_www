<?php

namespace Plex\Modules\Display\Layout;

use CWP\Template\Render;
use Plex\Modules\VideoCard\VideoCard;
use Plex\Modules\Display\VideoDisplay;
use Plex\Modules\Playlist\Playlist;

class ListDisplay extends VideoDisplay
{
    public $showVideoDetails = false;
    public $template_base   = '';
    public $VideoPlaylists = [];


    public function __construct($template_base = 'List')
    {
        $this->template_base = 'pages'.DIRECTORY_SEPARATOR. $template_base;
        $this->VideoPlaylists = (new Playlist())->showPlaylists(true);

    }

    public function getDisplay($results, $page_array = [])
    {
        $videohtml ='';// ['BODY'=>'','HIDDEN_STUDIO'=>''];
        $total_files     = '';

        if (isset($page_array['total_files'])) {
            $total_files = $page_array['total_files'];
        }

        if (isset($page_array['redirect_string'])) {
            $redirect_string = $page_array['redirect_string'];
        }

        $videoinfo       = new VideoCard();
        foreach ($results as $id => $row) {
            $row_id       = $row['id'];
            $row['next']  = 0;
            if (\array_key_exists($id + 1, $results)) {
                $row['next'] = $results[$id + 1]['id'];
            }

            $table_body[] = $videoinfo->VideoInfo($row, $total_files);
        }
        foreach ($table_body as $key => $value) {
            $videohtml.= $value['VIDEO'];
        }
        return $videohtml;
    }
}
