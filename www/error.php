<?php 
require_once(".config.inc.php");

define('TITLE', "404 not found");
include_once __LAYOUT_HEADER__;
?>

<main role="main" class="container">
<?php echo $_SERVER['REQUEST_URI']; ?> does not exist, sorry.
</main>
<?php include_once __LAYOUT_FOOTER__; ?>