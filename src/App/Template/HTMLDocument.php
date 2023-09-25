<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Template;

use CWP\Core\Media;
use CWP\Core\MediaSettings;
use CWP\Utils\MediaDevice;

class HTMLDocument
{
    public $nav_list_dir = 'list';
    public $template;

    public function __construct()
    {
        $this->template = new Template();
    }

    public static function __callStatic($method, $args)
    {
        $new = new self();
        $method = str_replace('_', '', $method);

        return $new->$method();
    }

    public function NavbarDropDown()
    {
        foreach (__NAVBAR_LINKS__ as $text => $url) {
            if (\is_array($url)) {
                $dropddown_menu_text = $text;

                foreach ($url as $dropdown_text => $dropdown_url) {
                    $dropdown_link_html .= $this->template->template(
                        'base/navbar/'.$this->nav_list_dir.'/navbar_link',
                        ['DROPDOWN_URL' => $dropdown_url, 'DROPDOWN_URL_TEXT' => $dropdown_text]
                    );
                }

                continue;
            }
            $nav_link_html .= $this->template->template('base/navbar/navbar_item_link', ['NAV_LINK_URL' => $url, 'NAV_LINK_TEXT' => $text]);
        }

        return [$dropdown_link_html, $nav_link_html, $dropddown_menu_text];
    }

    public function NavbarLatestVersion()
    {
        $latest = Media::$VersionUpdate;
        $installed = Media::$CurrentVersion;
        $dropdown_link_html = $this->template->template(
            'base/navbar/'.$this->nav_list_dir.'/navbar_item',
            ['DROPDOWN_TEXT' => 'Version '.$installed]
        );

        $latest_version_html = '';
        if (null !== $latest) {
            $dropdown_link_html .= $this->template->template(
                'base/navbar/'.$this->nav_list_dir.'/navbar_item',
                ['DROPDOWN_TEXT' => 'New! '.$latest]
            );
            //  $latest_version_html = $this->template->template('base/footer/version_latest', ['VERSION' => $latest]);
        }

        return [$dropdown_link_html, $latest_version_html];
    }

    public function getNavbar()
    {
        if (!MediaSettings::isTrue('NO_NAV')) {
            return MediaDevice::getNavbar();
        }
    }

    public function headerJS()
    {
        $path = '/'.__SCRIPT_NAME__;
        if (MediaSettings::isTrue('__FORM_POST__')) {
            $path = '/'.__FORM_POST__;
        }

        $js = trim(Template::GetHTML($path.'/javascript', [], false));

        $onload = trim(Template::GetHTML($path.'/onload', [], false));

        return [$js, $onload];
    }

    public function headerCSS()
    {
        $bootstrap = Template::GetHTML('base/header/bootstrap_5', [], false);
        $custom_css = Template::GetHTML('base/header/css', [], false);

        return [$bootstrap, $custom_css];
    }

    public function headerVersionUpdates()
    {
        $this->template->error = false;

        return $this->template->template('base/header/updates');
    }

    public function footerVersionUpdates()
    {
        $latest = Media::$VersionUpdate;
        $installed = Media::$CurrentVersion;
        // dd($latest, $installed);
        $version_html = Template::GetHTML('base/footer/version_current', ['VERSION' => $installed]);
        if (null != $latest) {
            $version_html = Template::GetHTML('base/footer/version_latest', ['VERSION' => $latest]);
        }

        return $version_html;
    }

    public static function displayMsg()
    {
        if (isset($GLOBALS)) {
            if (\is_array($GLOBALS['_REQUEST'])) {
                if (\array_key_exists('msg', $GLOBALS['_REQUEST'])) {
                    return Template::GetHTML('base/header/return_msg', ['MSG' => urldecode($GLOBALS['_REQUEST']['msg'])], false);
                }
            }
        }

        return '';
    }
}
