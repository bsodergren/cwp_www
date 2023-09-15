<?php
require_once '.config.inc.php';

define('TITLE', '404 not found');
use CWP\Utils\MediaDevice;
MediaDevice::getHeader();
?>

<main role="main" class="container">
	<?php echo $_SERVER['REQUEST_URI']; ?> does
	not exist, sorry.
</main>
<?php MediaDevice::getFooter(); ?>