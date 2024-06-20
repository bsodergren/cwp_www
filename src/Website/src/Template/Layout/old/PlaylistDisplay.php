<?php
namespace Plex\Modules\Display\Layout;

use CWP\Template\Render;
use UTMTemplate\HTML\Elements;
use Plex\Modules\Process\Forms;
use Plex\Modules\Display\Display;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\Display\VideoDisplay;
use CWP\Template\Functions\Functions;

class PlaylistDisplay extends VideoDisplay
{
    public $template_base   = '';
    public $library;
    public $playlist_body_html = '';
public $playlist_links = '';
private $playlist;
public $playlist_id = null;

    public function __construct($template_base = 'Playlist')
    {
        $this->template_base = 'pages'.DIRECTORY_SEPARATOR. $template_base;
        $this->playlist = new Playlist();
    }


    public function displayAllPlaylists($results)
    {

        for ($i = 0; $i < count($results); ++$i) {
            $playlist_image = '';
            $library = $results[$i]['Library'];

            if (0 == $i) {
                $prev = $library;
            }
            $preview = $this->playlist->showPlaylistPreview($results[$i]['playlist_id']);
            foreach ($preview as $r => $row) {
                $playlist_image .= Render::html( $this->template_base . '/List/image', ['image' => __URL_ROOT__.$row['thumbnail']]);
                // utminfo($row);
            }

            $params = [
                'PLAYLIST_ID' => $results[$i]['playlist_id'],
                'PLAYLIST_NAME' => $results[$i]['name'],
                'PLAYLIST_COUNT' => $results[$i]['count'],
                'ThumbnailPreview' => Render::html( $this->template_base .
                 '/List/thumbnail', ['PlaylistPreviewImage' => $playlist_image]),
            ];
            if ($library == $prev) {
                $this->playlist_links .= Render::html( $this->template_base . '/List/playlist_link', $params);
            } else {
                $this->playlist_body_html .= Render::html( $this->template_base . '/List/main', [
                    'PLAYLIST_LIST' => $this->playlist_links,
                    'PLAYLIST_LIBRARY' => $prev,
                ]);
                $this->playlist_links = Render::html( $this->template_base . '/List/playlist_link', $params);

                $prev = $library;
            }
            $this->library = $library;
        }

    }

    public function displayPlaylist($results)
    {
        $VideoDisplay = new Functions();
        $playlist_id = $this->playlist_id;

        $list = $this->playlist->showPlaylists(true);
        $playlist_LinkArray['All'] = __URL_ROOT__.'/playlist.php';
        foreach ($list as $l => $plRow)
        {
            $plCanvas_id = $plRow['id'];
            if ($playlist_id == $plCanvas_id) {
                continue;
            }
            $plCanvas_name = $plRow['name'];
            $playlist_LinkArray[$plCanvas_name] = __URL_ROOT__.'/playlist.php?playlist_id='.$plCanvas_id;
        }


        $total = count($results);
        $cell_html = '';
        for ($i = 0; $i < count($results); ++$i)
        {
            $thumbnail = '';
            if (OptionIsTrue(SHOW_THUMBNAILS))
            {
                $thumbnail = Render::html(
                $this->template_base . '/Grid/thumbnail',
                [
                    'THUMBNAIL' => $VideoDisplay->fileThumbnail($results[$i]['id'], 'alt="#" class="img-fluid" '),
                    'VIDEO_ID' => $results[$i]['id'],
                    'PLAYLIST_VIDEO_ID' => $results[$i]['playlist_video_id'],
                ]);
            }

            $cell_html .= Render::html(
            $this->template_base . '/Grid/cell',
            [
                // 'VID_NUMBER' => $i +1,
                'TITLE' => $results[$i]['title'],
                'THUMBNAIL' => $thumbnail,
                'VIDEO_ID' => $results[$i]['id'],
                'PLAYLIST_ID' => $playlist_id,
                'PLAYLIST_VIDEO_ID' => $results[$i]['playlist_video_id'],
            ]);
        }

        $form_url = __URL_ROOT__.'/playlist.php?playlist_id='.$playlist_id.'';
        $form_action = Elements::add_hidden('playlist_id', $playlist_id);

        $this->playlist_body_html = Render::html( $this->template_base . '/Grid/table', [
            'FORM_URL' => $form_url,
            'HIDDEN' => $form_action,
            'PLAYLIST_ID' => $playlist_id,
            'PLAYLIST_VIDEOS' => $total,
            'PLAYLIST_GENRE' => $results[0]['genre'],
            'PLAYLIST_NAME' => $results[0]['name'],
            'CELLS_HTML' => $cell_html,
        ]);
        define('PLAYLIST_DROPDOWN', $playlist_LinkArray);
    }





    public function getDisplay($results, $page_array = [])
    {
        if($this->playlist_id === null) {
            $this->displayAllPlaylists($results);

            $this->playlist_body_html .= Render::html($this->template_base . '/List/main', [
                'PLAYLIST_LIST' => $this->playlist_links,
                'PLAYLIST_LIBRARY' => $this->library,
            ]);
        } else {
            $this->displayPlaylist($results);
        }
        return $this->playlist_body_html;
    }
}
