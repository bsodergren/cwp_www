<?php
/**
 *  Plexweb
 */

namespace  CWPDisplay\Template\Functions;

use UTMTemplate\HTML\Elements;
use  CWPDisplay\Template\Render;
use UTMTemplate\Functions\Traits\Parser;
use  CWPDisplay\Template\Functions\Traits\Navbar;
use CWPDisplay\Template\Functions\Traits\JobMenu;
use  CWPDisplay\Template\Functions\Traits\Breadcrumbs;

class Functions extends Render
{
    use Breadcrumbs;
    use Navbar;
    use Parser;
    use JobMenu;


    public static $ElementsDir    = 'elements';
    public static $ButtonDir      = 'elements/Buttons';
    public static $BreadcrumbsDir = 'elements/Breadcrumb';

    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
    }

    public function hiddenSearch()
    {
        if (null === FileListing::$searchId) {
            return '';
        }

        return Elements::add_hidden('search_id', FileListing::$searchId, 'id="searchId"');
    }

    public function displayFilters()
    {
        return (new metaFilters())->displayFilters();
    }

    public function metaFilters($match)
    {
        $method = $match[2];

        return (new metaFilters())->{$method}();
    }

    public function playListButton()
    {
        $playlists               = (new Playlist())->getPlaylistSelectOptions();
        $params['CANVAS_HEADER'] = Render::html(self::$ButtonDir.'/Playlist/canvas_header', []);
        $params['CANVAS_BODY']   = Render::html(self::$ButtonDir.'/Playlist/canvas_body', ['SelectPlaylists' => $playlists]);
        // $params['CANVAS_BODY'] = Render::html('elements/Playlist/canvas_body', []);

        return Render::html(self::$ButtonDir.'/Playlist/canvas', $params);
    }

    public function AlphaBlock($match)
    {
        return (new AlphaSort())->displayAlphaBlock();
    }
}
