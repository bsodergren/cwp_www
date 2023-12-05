<?php

namespace CWP\Template;

use Rain\Tpl;
use CWP\Core\Media;
use CWP\Utils\MediaDevice;

class Rain extends Tpl
{
    public $nav_bar_links = [];

    public $TplTemplate;

    public function __construct()
    {


        $TemplateSrc = explode(\DIRECTORY_SEPARATOR, __CWP_SOURCE__);
        $commonTemplates = [
            'Templates' => [
                'common' => [
                    'footer', 'navbar', 'header'
                ],
                'pages' => [
                    basename($_SERVER['SCRIPT_FILENAME'], '.php')
                ],
            ]
        ];

        foreach ($commonTemplates as $key => $dirs) {
            foreach($dirs as $keypath => $paths) {
                $templatePath = array_merge($TemplateSrc, [$key,$keypath]);
                $templateDir[] = implode(DIRECTORY_SEPARATOR, $templatePath) . DIRECTORY_SEPARATOR;
                foreach($paths as $path) {
                    $templatePath = array_merge($TemplateSrc, [$key,$keypath,$path]);
                    $templateDir[] = implode(DIRECTORY_SEPARATOR, $templatePath) . DIRECTORY_SEPARATOR;
                }
            }
        }

        Tpl::configure([
            'tpl_dir' => $templateDir,
            'cache_dir' => __TPL_CACHE_DIR__ ,
            'auto_escape' => false,
            'debug' => __DEBUG__
        ]);
    }

    public function init()
    {
        $tpl_nabar_links = $this->nav_bar_links;
        $Tplnav_bar_dropdown = $tpl_nabar_links['Settings'];
        unset($tpl_nabar_links['Settings']);
        $TplTemplate = new Tpl();

        $TplTemplate->assign('headerTemplate', '../../common/header/header');
        $TplTemplate->assign('footerTemplate', '../../common/footer/footer');
        $TplTemplate->assign('navbarTemplate', '../../common/navbar/navbar');

        $TplTemplate->assign('UseNavbar', MediaDevice::$NAVBAR);
        $TplTemplate->assign('nav_bar_links', $tpl_nabar_links);
        $TplTemplate->assign('nav_bar_dropdown', $Tplnav_bar_dropdown);
        $TplTemplate->assign('current', Media::$CurrentVersion);
        $TplTemplate->assign('update', Media::$VersionUpdate);

        if (\array_key_exists('msg', $GLOBALS['_REQUEST'])) {
            $TplTemplate->assign('return_msg', $GLOBALS['_REQUEST']['msg']);
        }

        return $TplTemplate;

    }

    protected static function drawTpl($template, $varName, $varValue)
    {

        $Tpl = new Tpl();

        if(!is_array($varName)) {
            $varName = array($varName);
            if(is_array($varValue)) {

                $varValue[] = $varValue;
            }
        }

        if(!is_array($varValue)) {
            $varValue = array($varValue);
        }

        foreach($varName as $i => $value) {
            $Tpl->assign($value, $varValue[$i]);
        }

        return $Tpl->draw($template, true);

    }




}
