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




}
