<?php

define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

define('__WEB_ROOT__', $_SERVER['SERVER_ROOT']);
define('__COMPOSER_DIR__', __WEB_ROOT__.'/library/vendor');
define('__ASSETS_DIR__', __WEB_ROOT__ . '/assets');
define('__INC_CORE_DIR__', __ASSETS_DIR__ . '/core');
define('__ERROR_LOG_DIRECTORY__', __WEB_ROOT__. '/logs');
define('__INC_CLASS_DIR__', __ASSETS_DIR__ . '/class');

define('__LAYOUT_DIR__', '/assets/layout');
define('__LAYOUT_ROOT__', __WEB_ROOT__ . __LAYOUT_DIR__);
define('__TEMPLATE_DIR__', __LAYOUT_ROOT__ . '/template');

require_once __INC_CLASS_DIR__ . "/Utils.class.php";
require_once __INC_CLASS_DIR__ . "/HTML/Template.class.php";


set_include_path(get_include_path().PATH_SEPARATOR.__COMPOSER_DIR__);
require_once __COMPOSER_DIR__.'/autoload.php';
// require_once(".config.inc.php");
//define("ERROR_LOG_FILE", __WEB_ROOT__.'/debug.log');

//$errorArray = getErrorLogs();

//foreach($errorArray as $k =>$file){
    $file = str_replace("_",".",$_REQUEST['log']);
//    $logArray[$key] = $file;
//}
$file = __ERROR_LOG_DIRECTORY__.'/'.$file;

?>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>    
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

<script type="text/javascript">

jQuery(document).ready(function(){
jQuery('body,html').animate({scrollTop: 1000000}, 800);
})

</script>
</head>
<body>

<table class="table table-info table-striped">
<?php 


    if (($handle = fopen($file, "r")) !== FALSE)
    {
		$pos = -2; // Skip final new line character (Set to -1 if not present)
		$idx=0;
	    while (($str_data = fgets($handle, 5000)) !== FALSE)
        {

           Template::echo("debug/logentry",json_decode($str_data,1));
        }
        fclose($handle)			;
    }
    


?>
    </table>
</body>
</html>