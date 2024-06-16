<?php

namespace CWP\Template;

use Rain\Tpl;

class Rain extends Tpl
{
    public $nav_bar_links = [];

    public $TplTemplate;

    public function __construct()
    {
        $TemplateSrc     = explode(\DIRECTORY_SEPARATOR, __RAIN_TEMPLATE_DIR__);
        $commonTemplates = [
            'Templates' => [
                'common' => [
                    'footer', 'navbar', 'header',
                ],
                'pages'  => [
                    basename($_SERVER['SCRIPT_FILENAME'], '.php'),
                ],
            ],
        ];

        foreach ($commonTemplates as $key => $dirs) {
            foreach ($dirs as $keypath => $paths) {
                $templatePath  = array_merge($TemplateSrc, [$key, $keypath]);
                $templateDir[] = implode(\DIRECTORY_SEPARATOR, $templatePath).\DIRECTORY_SEPARATOR;
                foreach ($paths as $path) {
                    $templatePath  = array_merge($TemplateSrc, [$key, $keypath, $path]);
                    $templateDir[] = implode(\DIRECTORY_SEPARATOR, $templatePath).\DIRECTORY_SEPARATOR;
                }
            }
        }
        Tpl::configure([
            'tpl_dir'     => $templateDir,
            'cache_dir'   => __TPL_CACHE_DIR__,
            'auto_escape' => false,
            'debug'       => true,
        ]);
    }

    public function init()
    {
        $TplTemplate = new Tpl();

        $TplTemplate->assign('headerTemplate', '../../common/header/header');
        $TplTemplate->assign('footerTemplate', '../../common/footer/footer');
        $TplTemplate->assign('navbarTemplate', '../../common/navbar/navbar');

        return $TplTemplate;
    }

    protected static function drawTpl($template, $varName, $varValue)
    {
        $Tpl = new Tpl();

        if (!\is_array($varName)) {
            $varName = [$varName];
            if (\is_array($varValue)) {
                $varValue[] = $varValue;
            }
        }

        if (!\is_array($varValue)) {
            $varValue = [$varValue];
        }

        foreach ($varName as $i => $value) {
            $Tpl->assign($value, $varValue[$i]);
        }

        return $Tpl->draw($template, true);
    }
}
