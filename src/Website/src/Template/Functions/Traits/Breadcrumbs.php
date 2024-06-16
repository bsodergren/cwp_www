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
        $tag_types      = Request::$tag_types;
        $request_string = [];
        $parts          = [];
        $re_string      = '';
        $request_tag    = [];
        $crumbs         = ['Home' => 'home.php'];
        $sep            = '?';
        $studio_query   = [];

        $url = 'list.php';
        if (__THIS_FILE__ == 'search.php') {
            $url = 'search.php';
        }

        // if (isset(self::$CrubURL['grid'])) {
        //     $url = 'files.php';
        // }

        if (isset(Display::$CrubURL['list'])) {
            $url = 'gridview.php';
        }
        $allUrl = $url;
        // $crumbs[$in_directory] = '';
        parse_str($_SERVER['QUERY_STRING'], $query_parts);
        if (\count($query_parts) > 0) {
            if (__THIS_FILE__ == 'search.php') {
                if (!\array_key_exists('view', $query_parts)) {
                    $query_parts['view'] = 'List';
                }
            }

            foreach ($query_parts as $key => $value) {
                if (\in_array($key, $tag_types)) {
                    if ('' != $value) {
                        $request_tag[$key] = $value;
                    }
                } else {
                    if ('alpha' == $key) {
                        continue;
                    }
                    if (__THIS_FILE__ == 'search.php') {
                        $request_tag = [];
                        if ('view' == $key) {
                            if ('List' == $value) {
                                $value = 'Grid';
                            } else {
                                $value = 'List';
                            }

                            $request_string['view'] = $value;
                            continue;
                        }
                    }
                    $request_string[$key] = $value;
                }
            }

            if (\array_key_exists('genre', $request_tag)) {
                $url       = 'genre.php';
                $genre_url = $url;
            }
            if (\array_key_exists('studio', $request_tag)) {
                $url          = 'genre.php';
                $studio_query = ['studio' => $request_tag['studio']];
                //  unset($request_tag['studio']);
            }
            if (\array_key_exists('substudio', $request_tag)) {
                if ('null' != $_REQUEST['substudio']) {
                    $url                       = 'studio.php';
                    $substudio_key             = urldecode($request_tag['substudio']);
                    $studio_query['substudio'] = $request_tag['substudio'];
                    $res                       = PlexSql::$DB->rawQueryOne("SELECT studio FROM `mediatag_video_metadata` WHERE substudio = '".$substudio_key."';");
                    // utminfo($res);
                    if (\count($res) > 0) {
                        $studio_query['studio'] = $res['studio'];
                        $request_tag['studio']  = $res['studio'];
                    }
                }
                //  unset($request_tag['substudio']);
            }

            if (\count($request_string) > 0) {
                $re_string = $sep.http_build_query($request_string);
                $sep       = '&';
            }

            $crumb_url = $url.$re_string;
            ksort($request_tag);

            if (\count($request_tag) > 0) {
                if (\array_key_exists('studio', $request_tag)) {
                    $req_array['studio'] = $request_tag['studio'];
                }
                if (\array_key_exists('substudio', $request_tag)) {
                    if ('null' != $request_tag['substudio']) {
                        $req_array['substudio'] = $request_tag['substudio'];
                    }
                }
                if (\array_key_exists('genre', $request_tag)) {
                    $req_array['genre'] = $request_tag['genre'];
                }

                // $crumbs[$_SESSION['library']] = $crumb_url.$sep.http_build_query($studio_query);
                foreach ($req_array as $key => $value) {
                    $parts[$key] = $value;
                    if ('genre' == $key) {
                        $crumb_url = $genre_url.$re_string;
                    }
                    $crumbs[$value] = $crumb_url.$sep.http_build_query($parts);
                    $last           = $value;
                }
                // if ('genre' == $key) {
                //    $crumbs[$last] = '';
                // } else {
                //     $crumbs[$last] = 'genre.php?'.http_build_query($parts);
                // }
            }
        }

        $req = '';
        if (__THIS_FILE__ == 'genre.php' || __THIS_FILE__ == 'studio.php') {
            $req = '&'.http_build_query($parts);
        }

        if (__THIS_FILE__ == 'search.php') {
         //   $crumbs['List'] = $crumb_url;
        }

        $crumbs['All'] = $allUrl.'?allfiles=1'.$req;

        if (isset(Display::$CrubURL['grid'])) {
            $crumbs['Grid'] = Display::$CrubURL['grid'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
            unset(Display::$CrubURL['grid']);
        }

        // $crumbs['List'] = "";
        if (isset(Display::$CrubURL['list'])) {
            $crumbs['List'] = Display::$CrubURL['list'].$re_string.$sep.http_build_query($parts);
            unset($crumbs['All']);
            unset(Display::$CrubURL['list']);
        }

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
