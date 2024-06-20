<?php
/**
 *  Plexweb
 */

namespace  CWPDisplay\Template;

use  CWPDisplay\Template\Functions\Functions;
use  CWPDisplay\Template\Render;
use UTMTemplate\UtmDevice;

class Layout
{
    public static function Header($body = false)
    {
        $params = [];

        Render::echo('base/header/header', $params);

        if (OptionIsTrue(NAVBAR)) {
            $crumbs = (new Functions())->createBreadcrumbs();
            \define('BREADCRUMB', $crumbs);
            self::Navbar($params);
        }
        if (true === $body) {
            Render::echo('base/push', []);
        }
    }

    public static function Navbar($params)
    {
        // $db            = PlexSql::$DB;
        $library_links = '';
        // $sql           = PlexSql::query_builder(Db_TABLE_VIDEO_METADATA, 'DISTINCT(Library) as Library ');
        // foreach ($db->query($sql) as $k => $v) {
        //     $library_links .= Display::navbar_left_links('home.php?library='.$v['Library'], $v['Library']);
        // }
        // $library_links .= Display::navbar_left_links('home.php?library=All', 'All');
        // $params['CURRENT_DEVICE'] = UtmDevice::$DEVICE;
        // $params['Device']         = ucfirst(strtolower(UtmDevice::$DEVICE));

        $params['NAV_BAR_LEFT_LINKS'] = Render::html('base/navbar/library_menu',
            ['LIBRARY_SELECT_LINKS' => $library_links]);
        Render::echo('base/navbar/main', $params);
    }

    public static function Footer()
    {
        global $pageObj;
        $params    = [];
        $page_html = '';
        $navbar    = '';


        Render::echo('base/footer/main', ['FOOT_NAVBAR' => $navbar]);
    }
}
