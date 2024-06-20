<?php

namespace Plex\Modules\Display\Layout;

use Plex\Modules\Database\FavoriteDB;
use Plex\Modules\Database\PlexSql;
use Plex\Modules\Display\FavoriteDisplay;
use Plex\Modules\Display\VideoDisplay;
use Plex\Modules\Playlist\Playlist;
use Plex\Modules\VideoCard\VideoCard;
use CWP\Template\Functions\Traits\Video;
use CWP\Template\Render;
use UTMTemplate\HTML\Elements;

/**
 * plex web viewer.
 */
class GridDisplay extends VideoDisplay
{
    use Video;
    public $totalRecords;
    public $showVideoDetails = false;
    public $template_base = '';
    public $VideoPlaylists = [];
    private $AltClass;

    public function __construct($template_base = 'Grid')
    {
        $this->template_base = 'pages'.DIRECTORY_SEPARATOR. $template_base;
        $this->VideoPlaylists = (new Playlist())->showPlaylists(true);
    }

    public function gridPlaylistDropdown($playlists, $video_id)
    {
        $current = [];
        $playlist_html = '';
        $ids = Playlist::getVideoPlaylists($video_id);
        if (\count($ids) > 0) {
            foreach ($ids as $k => $pl) {
                $current[$pl['playlist_id']] = true;
            }
        }

        foreach ($playlists as $p) {
            if (\array_key_exists('id', $p)) {
                if (\array_key_exists($p['id'], $current)) {
                    continue;
                }

                $playlist_html .= Render::html(
                    'pages/Grid/playlist_link',
                    [
                        'PL_NAME' => $p['name'],
                        'PL_ID' => $p['id'],
                        'VIDEO_DATA' => 'VIDEO_ID',
                    ]
                );
            }
        }

        return $playlist_html;
    }

    public function videoCell($videoInfo)
    {
        $playlist_html = $this->gridPlaylistDropdown($this->VideoPlaylists, $videoInfo['id']);
        $full_filename = $videoInfo['fullpath'].\DIRECTORY_SEPARATOR.$videoInfo['filename'];

        // %%FILE_MISSING%%
        $class_missing = '';
        if (!file_exists($full_filename)) {
            $class_missing = 'bg-danger';
        }

        $file_info = [
            'title' => $videoInfo['title'],
            'library' => $videoInfo['Library'],
            'studio' => $videoInfo['studio'],
            'substudio' => $videoInfo['substudio'],
            'artist' => $videoInfo['artist'],
            'genre' => $videoInfo['genre'],
            'added' => $videoInfo['added'],
            // 'filename'  => $videoInfo['filename'],
            'duration' => VideoCard::videoDuration($videoInfo['duration']),

            // 'Duration' => videoDuration($videoInfo['duration']),
        ];
        if ('' != $videoInfo['substudio']) {
            $substudio = $videoInfo['substudio'];
        }
        if ('' != $videoInfo['studio']) {
            $studio = $videoInfo['studio'];
        }
        $videoFields = '';
        foreach ($file_info as $field => $value) {
            // if ($value != '') {
            $videoFields .= Render::html(
                'pages/Grid/cell/video_data',
                [
                    'FILE_FIELD' => ucfirst($field),
                    'FILE_INFO' => $value,
                    'FILE_MISSING' => $class_missing,
                ]
            );
            // }
        }
        $thumbnail = '';
        if (OptionIsTrue(SHOW_THUMBNAILS)) {
            $plLinks = null;
            if (null !== $playlist_html) {
                $plLinks = str_replace('VIDEO_ID', $videoInfo['id'], $playlist_html);
            }
            $thumbnail = Render::html(
                'pages/Grid/cell/thumbnail',
                ['PLAYLIST_LINKS' => $plLinks,
                    'THUMBNAIL' => $this->fileThumbnail($videoInfo['id']),
                    'ROW_ID' => $videoInfo['id'],
                    'VIDEO_DATA' => $videoFields,
                    'ROWNUM' => $videoInfo['rownum'],
                    'ROW_TOTAL' => $this->totalRecords, ]);
        }
        $favRow = $this->favorite($videoInfo['id']);

        $params = [
            'FILE_MISSING' => $class_missing,

            'PLAYLIST_LINKS' => str_replace('VIDEO_ID', $videoInfo['id'], $playlist_html),
            'THUMBNAIL' => $thumbnail,
            'ROW_ID' => $videoInfo['id'],
            'VIDEO_DATA' => $videoFields,
            'ROWNUM' => $videoInfo['rownum'],
            'ROW_TOTAL' => $this->totalRecords,
            'STAR_RATING' => $videoInfo['rating'],
            'FAVRow' => $favRow,
        ];

        // THUMB_EXTRA = ' alt="#" class="img-fluid" '
        return Render::html(
            'pages/Grid/cell/video', $params
        );
    }


    public function getDisplay($results, $page_array = [])
    {
        global $_SESSION,$_REQUEST;
        global $sort_type_map;
        $cell_html = '';
        $db = PlexSql::$DB;
        if (isset($page_array['total_files'])) {
            $this->totalRecords = $page_array['total_files'];
        }

        if (isset($page_array['redirect_string'])) {
            $redirect_string = $page_array['redirect_string'];
        }

        $total = \count($results);
        if (0 == $total) {
            return 'No results';
        }

        foreach ($results as $k => $videoRow) {
            $cell_html .= Render::html('pages/Grid/cell', [
                'VIDEO_CELL' => $this->videoCell($videoRow),
                'ROW_ID' => $videoRow['id']]
            );
        }
        $table_body_html = Render::html('/pages/Grid/table', [
          //  'HIDDEN_STUDIO_NAME' => Elements::add_hidden('studio', $studio).Elements::add_hidden('substudio', $substudio),
            'ROWS_HTML' => $cell_html,
          //  'INFO_NAME' => $sort_type_map['map'][$_REQUEST['sort']],
        ]);
        return $table_body_html;
    }

    public function favorite($videoid)
    {
        if (true == FavoriteDB::get($videoid)) {
            $favoriteBtn = FavoriteDisplay::RemoveFavoriteVideo($videoid);
        } else {
            $favoriteBtn = FavoriteDisplay::addFavoriteVideo($videoid);
        }

        return $favoriteBtn;
    }
}
