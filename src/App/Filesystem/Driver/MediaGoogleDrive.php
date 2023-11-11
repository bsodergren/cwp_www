<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem\Driver;

use CWP\Core\MediaError;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Nette\Utils\FileSystem;

// use Symfony\Component\Filesystem\Filesystem;
/*


$this->google

0 => "__construct"
  1 => "fileExists"
  2 => "directoryExists"
  3 => "has"
  4 => "write"
  5 => "writeStream"
  6 => "read"
  7 => "readStream"
  8 => "delete"
  9 => "deleteDirectory"
  10 => "createDirectory"
  11 => "listContents"
  12 => "move"
  13 => "copy"
  14 => "lastModified"
  15 => "fileSize"
  16 => "mimeType"
  17 => "setVisibility"
  18 => "visibility"
  19 => "publicUrl"
  20 => "temporaryUrl"
  21 => "checksum"


$item
   0 => "__construct"
  1 => "path"
  2 => "type"
  3 => "visibility"
  4 => "lastModified"
  5 => "extraMetadata"
  6 => "isFile"
  7 => "isDir"
  8 => "withPath"
  9 => "fromArray"
  10 => "jsonSerialize"
  11 => "offsetExists"
  12 => "offsetGet"
  13 => "offsetSet"
  14 => "offsetUnset"
]
*/

class MediaGoogleDrive implements MediaFileInterface
{
    public object $google;

    public object $localfs;

    private $upload_dir = '/Uploads';

    public function __construct()
    {
        $client        = new \Google\Client();
        $client->setClientId('882775659043-hc67vibec4eeio5bkb1t5mdnlk1nkeju.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->refreshToken('1//05PW3oMgnMfnwCgYIARAAGAUSNwF-L9IrWsQ6cqqq0TauLTQxBTOn63BonmoFRoFgTbHRHYLNagRbTXb6fhTNO67BmQ0-RBaa9ww');
        $client->setApplicationName('plexmediabackupserver');

        $service       = new \Google\Service\Drive($client);
        $adapter       = new \Masbug\Flysystem\GoogleDriveAdapter($service, 'CWPMediaFolder');
        $this->google  = new \League\Flysystem\Filesystem($adapter);

        $localAdapter  = new \League\Flysystem\Local\LocalFilesystemAdapter('/');
        $this->localfs = new \League\Flysystem\Filesystem($localAdapter,
            [\League\Flysystem\Config::OPTION_VISIBILITY => \League\Flysystem\Visibility::PRIVATE]);
    }

    public function dirExists($dir)
    {

        $r = $this->google->directoryExists($dir);

        return $r;
    }

    public function search($search, $path = '/')
    {
        return $this->getContents($path,true);


    }

    public function getFile($filename)
    {
        $tmp_filename = basename($filename);
        $tmp_file     = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$tmp_filename;
        if (! file_exists($tmp_file)) {
            $this->localfs->writeStream($tmp_file, $this->google->readStream($filename));
        }

        return $tmp_file;
    }

    public function exists($filename)
    {
        $r = $this->google->fileExists($filename);

        return $r;
    }

    public function createFolder($path)
    {
        $path = Filesystem::normalizePath($path);

        $path = Filesystem::unixSlashes($path);

        if (str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }
        $this->google->createDirectory($path);

        // Name
        return $path;
    }

    public function getContents($path,$recursive = false)
    {
        $contents = $this->google->listContents($path, $recursive /* is_recursive */);
        // Fetch Items (Returns an instance of ModelCollection)

        foreach ($contents as $item) {
            if($item->isFile() == true){
                $files[]= $item->path();
            }
        }

        return $files;
    }

    public function delete($file)
    {
        $file = Filesystem::unixSlashes($file);

            return $this->google->delete($file);

    }

    public function save($localfile, $remotefile, $options = [])
    {
        $remotefile = Filesystem::unixSlashes($remotefile);

        if (false !== $this->exists($remotefile)) {
            $this->google->delete($remotefile);
        }

        $this->google->writeStream($remotefile, $this->localfs->readStream($localfile));

    }

    public function UploadFile($filename, $uploadFilename, $options = [])
    {
        dd("Upload File");
        $filename = __TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename);

        $this->save($filename, $uploadFilename, $options);

        return $uploadFilename;
    }

    public function DownloadFile($filename)
    {
        dd("Download File");
        $tmpFilename = __TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename);

        if (file_exists($tmpFilename)) {
            Filesystem::delete($tmpFilename);
        }

        $file        = $this->google->download($filename, $tmpFilename);

        // Downloaded File Metadata
        $metadata    = $file->getMetadata();

        // Name
        $name        = $metadata->getName();

        return $tmpFilename;
    }

    public function rename($old, $new)
    {
        return null;
    }

    public function error($e)
    {
        $code    = $e->getCode();
        $message = $e->getMessage();


        echo HTMLDisplay::JavaRefresh('/error.php', 0, ['type' => 'Dropbox', 'code' => $code, 'message' => $message]);
        exit;

        MediaDevice::getFooter();
        exit;
    }
}
