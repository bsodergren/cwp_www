<?php
/**
 * CWP Media tool
 */

use CWP\Media\Media;
use CWP\Media\Update\MediaAppUpdater;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

/**
 * CWP Media tool.
 */
require_once '.config.inc.php';
define('TITLE', 'Form Editor');

include_once __LAYOUT_HEADER__;


$update = new MediaAppUpdater();

echo $update->isUpdate();



// $adapter = new LocalFilesystemAdapter(
//     // Determine root directory
//     __PROJECT_ROOT__
// );

//  $appKey = 'm2xqkk0ojabhluo';
//  $appSecret = 'fcy77exrlrh03g1';

// $client = new Client(__DROPBOX_AUTH_TOKEN__);
// $adapter = new DropboxAdapter($client);
// $filesystem = new Filesystem($adapter);
// $path = '.';
// try {
//     $listing = $filesystem->listContents($path, 0);
//     /** @var \League\Flysystem\StorageAttributes $item */
//     foreach ($listing as $item) {

//         $path = $item->path();
//         if ($item instanceof FileAttributes) {
//             echo $path.'<br>';
//             // handle the file
//         } elseif ($item instanceof DirectoryAttributes) {
//             // handle the directory
//             echo $path.'<br>';
//         }
//     }
// } catch (FilesystemException $exception) {
//     dd($exception);
//     // handle the error
// }
