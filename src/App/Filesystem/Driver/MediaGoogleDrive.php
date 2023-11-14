<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Filesystem\Driver;

use CWP\Core\Bootstrap;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Utils\MediaDevice;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
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

    private $upload_dir = '\Uploads';

    public function __construct()
    {
        $client = new \Google\Client();
        $client->setClientId(Bootstrap::$CONFIG['google']['clientid']);
        $client->setClientSecret(Bootstrap::$CONFIG['google']['secret']);
        $client->refreshToken(Bootstrap::$CONFIG['google']['token']);
        $client->setApplicationName('plexmediabackupserver');

        $service = new \Google\Service\Drive($client);
        $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, 'CWPMediaFolder');
        $this->google = new \League\Flysystem\Filesystem($adapter);

        $localAdapter = new \League\Flysystem\Local\LocalFilesystemAdapter('/',
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => 0640,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0740,
                    'private' => 7604,
                ],
            ])
        );
        $this->localfs = new \League\Flysystem\Filesystem(
            $localAdapter,
            // [\League\Flysystem\Config::OPTION_VISIBILITY => \League\Flysystem\Visibility::PRIVATE]
        );
    }

    private function path($path, $create = false)
    {
        $path = Filesystem::normalizePath($path);
        $path = Filesystem::platformSlashes($path);

        if (true == $create) {
            if (is_dir($path)) {
                $dir = $path;
            } else {
                $dir = \dirname($path);
            }
            FileSystem::createDir($dir);
        }

        return $path;
    }

    public function postSaveFile($postFileArray)
    {
        $fileName = $postFileArray['the_file']['name'];
        $fileTmpName = $postFileArray['the_file']['tmp_name'];

        $loc = new MediaFileSystem();
        $pdf_directory = $loc->getDirectory('upload', true);

        $upload_file = $this->createFolder($pdf_directory).\DIRECTORY_SEPARATOR.basename($fileName);

        $pdf_file = $this->path(__TEMP_DIR__.\DIRECTORY_SEPARATOR.'MediaUpload'.\DIRECTORY_SEPARATOR.basename($fileName), true);
        $res = move_uploaded_file($fileTmpName, $pdf_file);
        if (true != $res) {
            dd($res);
        }

        $loc->UploadFile($pdf_file, $upload_file, ['autorename' => false]);

        // dd($fileTmpName, $pdf_file, $upload_file);
        return $pdf_file;
    }

    public function dirExists($dir)
    {
        return $this->google->directoryExists($dir);
    }

    public function search($search, $path = '/')
    {
        return $this->getContents($path, true);
    }

    public function getFile($filename)
    {
        $tmp_file = $this->path(__TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename));

        if (file_exists($tmp_file)) {
            FileSystem::delete($tmp_file);
        }

        $this->localfs->writeStream($tmp_file, $this->google->readStream($filename));

        return $tmp_file;
    }

    public function exists($filename)
    {
        return $this->google->fileExists($filename);
    }

    public function createFolder($path)
    {
        $path = $this->path($path);
        if (str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }
        if (!str_starts_with($path, __HOME__)) {
            $this->google->createDirectory($path);
        }

        // Name
        return $path;
    }

    public function getContents($path, $recursive = false)
    {
        $path = $this->path($path);

        if ($this->dirExists($path)) {
            $contents = $this->google->listContents($path, $recursive /* is_recursive */);
            // Fetch Items (Returns an instance of ModelCollection)

            foreach ($contents as $item) {
                if (true == $item->isFile()) {
                    $files[] = $item->path();
                }
            }

            return $files;
        }

        return [];
    }

    public function delete($file)
    {
        $file = $this->path($file);

        return $this->google->delete($file);
    }

    public function save($localfile, $remotefile, $options = [])
    {
        $remotefile = $this->path($remotefile);

        if (false !== $this->exists($remotefile)) {
            $this->google->delete($remotefile);
        }

        $this->google->writeStream($remotefile, $this->localfs->readStream($localfile));
    }

    public function UploadFile($filename, $uploadFilename, $options = [])
    {
        // dd("Upload File");
        // dd($filename, $uploadFilename);
        //  $filename = __TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename);
        $this->save($filename, $uploadFilename, $options);

        return $uploadFilename;
    }

    public function DownloadFile($filename, $path = __TEMP_DIR__)
    {
        $tmpFilename = $this->path($path.\DIRECTORY_SEPARATOR.basename($filename), true);

        if (file_exists($tmpFilename)) {
            Filesystem::delete($tmpFilename);
        }

        $this->localfs->writeStream($tmpFilename, $this->google->readStream($filename));

        return $tmpFilename;
    }

    public function rename($old, $new)
    {
        return null;
    }

    public function error($e)
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        echo HTMLDisplay::JavaRefresh('/error.php', 0, ['type' => 'Dropbox', 'code' => $code, 'message' => $message]);
        exit;

        MediaDevice::getFooter();
        exit;
    }

    public function downloadXLSXFiles($xlsDir)
    {
        $files = $this->getContents($xlsDir);
        foreach ($files as $file) {
            $tmpFile[] = $this->DownloadFile($file, $this->getXLSXDir($xlsDir));
        }
    }

    public function getXLSXDir($xlsDir)
    {
        return $this->path(__TEMP_DIR__.\DIRECTORY_SEPARATOR.$xlsDir, true);
    }

    public function getZipFile($zipFile, $path)
    {
        return $this->path($path.\DIRECTORY_SEPARATOR.'zip'.\DIRECTORY_SEPARATOR.basename($zipFile), true);
    }
}
