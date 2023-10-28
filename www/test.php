<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';

$client   = new \Google\Client();
$client->setClientId('882775659043-hc67vibec4eeio5bkb1t5mdnlk1nkeju.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->refreshToken('1//05PW3oMgnMfnwCgYIARAAGAUSNwF-L9IrWsQ6cqqq0TauLTQxBTOn63BonmoFRoFgTbHRHYLNagRbTXb6fhTNO67BmQ0-RBaa9ww');
$client->setApplicationName('plexmediabackupserver');

$service  = new \Google\Service\Drive($client);
$adapter  = new \Masbug\Flysystem\GoogleDriveAdapter($service, 'plex_backups');
$fs       = new \League\Flysystem\Filesystem($adapter);
$contents = $fs->listContents('', false /* is_recursive */)->toarray();
dd($contents);

MediaDevice::getHeader();

Template::echo('test/main');
MediaDevice::getFooter();
