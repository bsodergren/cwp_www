<?php
/**
 *  Plexweb
 */

namespace CWPDisplay\Template\Functions\Traits;

use UTMTemplate\HTML\Elements;
use  CWPDisplay\Template\Render;
use CWPDisplay\Template\Display;

trait Breadcrumbs
{
    public function createBreadcrumbs()
    {

        $parts          = [];
        $re_string      = '';
        $request_tag    = [];
        $crumbs         = ['Home' => 'home.php'];
        $sep            = '?';
        $studio_query   = [];

        $url = 'list.php';


        // if (isset(self::$CrubURL['grid'])) {
        //     $url = 'files.php';
        // }








        foreach (Display::$CrubURL as $k => $url) {
            $crumbs[$k] = $url.$re_string.$sep.http_build_query($parts);
        }

        return $crumbs;
    }

    public function breadcrumbs()
    {
        $crumbs_html = '';
        foreach (BREADCRUMB as $text => $url) {
            if ('' == $text) {
                continue;
            }

            $class = 'breadcrumb-item';

            if ('' == $url) {
                $class .= ' active" aria-current="page';
                $url = '#';
            }

            $params['CLASS'] = $class;
            $params['LINK']  = Elements::url($url, $text);

            $crumbs_html .= Render::html(self::$BreadcrumbsDir.'/crumb', $params);
        }

        return Render::html(self::$BreadcrumbsDir.'/breadcrumb', ['CRUMB_LINKS' => $crumbs_html]);
    }
}
