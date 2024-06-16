<?php
/**
 *  Plexweb
 */

namespace Plex\Modules\Display\Layout;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Display\Display;
use Plex\Modules\Display\VideoDisplay;
use CWP\Template\Render;

class LibraryDisplay extends VideoDisplay
{
    public $showVideoDetails = false;
    public $template_base    = '';
    public $VideoPlaylists   = [];
    public $db;

    public function __construct($template_base = 'Home')
    {
        $this->template_base = 'pages'.\DIRECTORY_SEPARATOR.$template_base;
        $this->db            = PlexSql::$DB;
    }

    public function getDisplay($result, $page_array = [])
    {
        $studio_html = '';
        foreach ($result as $r => $row) {
            if (null === $row['subLibrary']) {
                $row['subLibrary'] = 'Studios';
            }
            $studioArray[$row['subLibrary']][] = ['studio' => $row['studio'], 'cnt' => $row['cnt']];
        }

        foreach ($studioArray as $subLibrary => $studioArr) {
            $studio_box = $this->parseStudio($studioArr);
            $studio_html .= Render::html($this->template_base.'/Library', [
                'STUDIO_BOX_HTML' => $studio_box,
                'LIBRARY_NAME'    => $subLibrary]);
        } // end foreach

        return $studio_html;
    }

    public function parseSubStudios($studio, $count, $results)
    {
        $accordian                     = [];
        $accordian['ACCORDIAN_ID']     = Display::RandomId('accordian_');
        $accordian['ACCORDIAN_HEADER'] = $studio;
        $accordian['STUDIO_LINK']      = Render::html($this->template_base.'/studio/link', [
            'url'         => 'studio',
            'GET_REQUEST' => 'studio='.urlencode($studio),
            'NAME'        => 'All',
            'COUNT'       => $count,
            'CLASS'       => 'btn btn-secondary',
        ]);

        foreach ($results as $ssRow => $ssName) {
            if (null === $ssName['substudio']) {
                $sql_studio    = ' LIKE "'.$studio.'" AND  substudio is NULL ';
                $studioCnt_sql = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA,
                    'count(studio) as cnt, studio', ' studio  '.$sql_studio,
                    'studio', 'studio ASC ');

                $ss_result = $this->db->query($studioCnt_sql);

                $accordian['STUDIO_LINK'] .= Render::html($this->template_base.'/studio/link', [
                    'url'         => 'genre',
                    'GET_REQUEST' => 'studio='.urlencode($studio).'&substudio=null',
                    'NAME'        => $studio,
                    'COUNT'       => $ss_result[0]['cnt'],
                    'CLASS'       => 'btn btn-secondary',
                ]);
                continue;
            }
            $ssCnt     = ' ('.$ssName['cnt'].')';
            $substudio = urlencode($ssName['substudio']);
            $accordian['STUDIO_LINK'] .= Render::html($this->template_base.'/studio/link', [
                'url'         => 'genre',
                'GET_REQUEST' => 'substudio='.$substudio,
                'NAME'        => $ssName['substudio'],
                'COUNT'       => $ssCnt,
                'CLASS'       => 'btn btn-secondary',
            ]);
        }
        $accordian['ACCORDIAN_LINKS'] = Render::html($this->template_base.'/studio/group', $accordian);

        return Render::html($this->template_base.'/accordian/block', $accordian);
    }

    public function parseStudio($studioArr)
    {
        $studio_links = '';
        $studio_box   = '';

        foreach ($studioArr as $row => $sname) {
            $name = ['studio' => $sname['studio']];
            $cnt  = $sname['cnt'];

            if ('' == $name['studio']) {
                $name['studio'] = 'NULL';
                $sql_studio     = ' IS NULL';
            } else {
                $sql_studio = ' LIKE "'.$name['studio'].'"';
            }

            $studio = $name['studio'];
            $url    = 'genre';

            if (isset($cnt)) {
                if (1 == $cnt) {
                    $url = 'list';
                }
                $cnt = ' ('.$cnt.') ';
            }

            $substudio_sql = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA,
                'count(substudio) as cnt, substudio', ' studio  '.$sql_studio,
                'substudio', 'substudio ASC ');

            $ss_result = $this->db->query($substudio_sql);
            if (\count($ss_result) >= 2) {
                $studio_links .= $this->parseSubStudios($studio, $cnt, $ss_result);
            } else {
                $studio_links .= Render::html($this->template_base.'/studio/group', [
                    'STUDIO_LINK' => Render::html($this->template_base.'/studio/link', [
                        'url'         => $url,
                        'GET_REQUEST' => 'studio='.urlencode($studio),
                        'NAME'        => $name['studio'],
                        'COUNT'       => $cnt,
                        'CLASS'       => 'btn btn-secondary',
                    ]),
                ]);
            }
            if ('' != $studio_links) {
                $studio_box .= Render::html($this->template_base.'/block', [
                    'STUDIO_LINKS' => $studio_links,
                    'CLASS'        => '',
                ]);
                $studio_links = '';
            }
            // echo "</ul>";
            // }
        }

        return $studio_box;
    }
}
