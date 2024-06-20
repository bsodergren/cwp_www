<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWPDisplay\Template\Functions\Traits;

use CWPDisplay\Template\Render;
use Symfony\Component\Yaml\Yaml;
use UTMTemplate\Template;

trait Navbar
{
    private $NavbarDir = 'base/navbar';

    public static function dropdownLink($url, $name, $active, $badge, $Icon = '')
    {
        return Render::html('base/navbar/menu_dropdown_link', [
            'ACTIVE' => $active,
            'DROPDOWN_URL_TEXT' => $name,
            'DROPDOWN_URL' => $url,
            'DROPDOWN_BADGE' => $badge,
            'Icon_Class' => $Icon,
        ]);
    }

    public static function dropdown($array)
    {
        $dropdown_link_html = [];
        $badge = '';
        $pop = false;
        foreach ($array['dropdown'] as $d_name => $d_values) {
            $is_active = '';

            if (\is_array($d_values)) {
                foreach ($d_values as $dd_name => $dd_url) {
                    $icon = '';
                    if (\is_array($dd_url)) {
                        $tmp_arr = $dd_url;
                        unset($dd_url);
                        $icon = $tmp_arr['icon'];
                        $dd_url = $tmp_arr['url'];
                    }
                    $is_active = '';
                    if (__THIS_PAGE__ == basename($dd_url, '.php')) {
                        $is_active = ' active';
                    }
                    $dropdown_link_html[] = self::dropdownLink($dd_url, $dd_name, $is_active, $badge, $icon);
                }
                $dropdown_link_html[] = '<li><hr class="dropdown-divider-settings"></li>';
                $pop = true;
                continue;
            }

            if (true === $pop) {
                array_pop($dropdown_link_html);
                $pop = false;
            }

            if (__THIS_PAGE__ == basename($d_values, '.php')) {
                $is_active = ' active';
            }

            $parts = explode('|', $d_values);
            $url = $parts[0];

            if (\array_key_exists(1, $parts)) {
                $badge = Render::html('base/navbar/menu_dropdown_badge', ['COUNT' => $parts[1]]);
            }

            $dropdown_link_html[] = self::dropdownLink($url, $d_name, $is_active, $badge);
        }

        if (true === $pop) {
            array_pop($dropdown_link_html);
            $pop = false;
        }

        return Render::html('base/navbar/menu_dropdown', [
            'DROPDOWN_TEXT' => $array['text'],
            'Icon_Class' => self::navbarIcon($array),

            'DROPDOWN_LINKS' => implode("\n", $dropdown_link_html),
        ]);
    }

    public static function navbar_leftlinks()
    {
        $library_links = '';
        $links = [
            ['name' => 'home', 'url' => 'home.php'],
            ['name' => 'home3', 'url' => 'home.php'],
            ['name' => 'home4', 'url' => 'home.php'],
        ];
        foreach ($links as $row) {
            $library_links .= Render::html('base/navbar/library_links', [
                'MENULINK_TEXT' => $row['name'],
                'DROPDOWN_URL' => $row['url'],
            ]);
        }

        return $library_links;
    }

    public static function navbar_links()
    {
        $html = '';
        global $_REQUEST;

        $navigation_link_array = Yaml::parseFile(__ROUTE_NAV__, Yaml::PARSE_CONSTANT);

        foreach ($navigation_link_array as $name => $link_array) {
            $is_active = '';
            if (\array_key_exists('dropdown', $link_array)) {
                $html .= self::dropdown($link_array);
                continue;
            }

            if (__THIS_PAGE__ == basename($link_array['url'], '.php')) {
                $is_active = ' active';
            }

            $favPopup = '';
            $template = 'menu_link';

            $array = [
                'MENULINK_URL' => $link_array['url'],
                'MENULINK_JS' => $favPopup,
                'MENULINK_TEXT' => $link_array['text'],
                'Icon_Class' => self::navbarIcon($link_array),
                // '' => ' fa-home',
                'ACTIVE' => $is_active,
            ];

            $url_text = Render::html('base/navbar/'.$template, $array);

            $html = $html.$url_text."\n";
        } // end foreach

        return $html;
    } // end navbar_links()

    public static function navbarIcon($link_array)
    {
        $icon = '';
        if (isset($link_array['icon'])) {
            $icon = $link_array['icon'];
            // $html = ' '.Template::Icons($icon);
        }

        return $icon;
    }
}
