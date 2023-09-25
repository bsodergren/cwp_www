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

class MediaDropbox implements MediaFileInterface
{
    public object $dropbox;

    private $upload_dir = '/Uploads';

    public function __construct()
    {
        try {
            $app = new DropboxApp(__DROPBOX_APP_KEY__, __DROPBOX_APP_SECRET__, __DROPBOX_AUTH_TOKEN__);
        } catch (DropboxClientException $e) {
            $this->error($e);
        }
        try {
            $this->dropbox = new Dropbox($app);
        } catch (DropboxClientException $e) {
            $this->error($e);
        }
    }

    public function dirExists($dir)
    {
        dd($dir);

        $r = $this->exists(basename($dir));

        return $r;
    }

    public function search($search, $path = '/')
    {
        dd($path);

        try {
            $searchResults = $this->dropbox->search($path, $search, ['start' => 0, 'max_results' => 50]);
        } catch (DropboxClientException $e) {
            dd($e);
        }
            $items = $searchResults->getItems();

        // All Items
        foreach ($items->all() as $item) {
            $file[] = $item->getMetadata()->path_display;
        }
        natsort($file);

        return $file;
    }

    public function getFile($filename)
    {
        $tmp_filename = basename($filename);
        $tmp_file = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$tmp_filename;
        if (!file_exists($tmp_file)) {
            $file = $this->dropbox->download($filename);

            $contents = $file->getContents();

            // Save file contents to disk
            file_put_contents($tmp_file, $contents);
        }

        return $tmp_file;
    }

    public function exists($path)
    {
        try {
            $searchResults = $this->dropbox->search('/', $path, ['start' => 0, 'max_results' => 1]);
        } catch (DropboxClientException $e) {
            $this->error($e);
        }
        $items = $searchResults->getItems();

        // Fetch Items
        $item = $items->first();
        if (null === $item) {
            return false;
        }

        return $item->getMetadata();
    }

    public function createFolder($path)
    {
        $path = Filesystem::normalizePath($path);

        $path = Filesystem::unixSlashes($path);

        if (str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        try {
            $folder = $this->dropbox->createFolder($path);
        } catch (DropboxClientException $e) {
            // $this->error($e);
        }

        // Name
        return $path;
    }

    public function getContents($path)
    {
        try {
            $path = Filesystem::unixSlashes($path);


            $listFolderContents = $this->dropbox->listFolder($path);

        } catch (DropboxClientException $e) {
            $this->error($e);
        }

        // Fetch Items (Returns an instance of ModelCollection)
        $items = $listFolderContents->getItems();

        // All Items
        $array = $items->all();
        foreach ($array as $item) {
            $return[] = ['name' => $item->getName(), 'path' => $item->getpathDisplay()];
        }

        return $return;
    }

    public function delete($file)
    {
        $file = Filesystem::unixSlashes($file);

        //  $file = ltrim($file, '\\');
        try {
            return $this->dropbox->delete($file);
        } catch (DropboxClientException $e) {
            //  dd( $file,$e);
            return false;
        }
    }







    public function save($localfile, $remotefile, $options = [])
    {
        $remotefile = Filesystem::unixSlashes($remotefile);

        if (false !== $this->exists(basename($remotefile))) {
            $this->delete($remotefile);
        }

        try {
            $file = $this->dropbox->upload($localfile, $remotefile, $options);
        } catch (DropboxClientException $e) {
            $this->error($e);
        }


        return $file;
    }

    public function UploadFile($filename, $uploadFilename, $options = [])
    {
        $filename = __TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename);

        $this->save($filename, $uploadFilename, $options);

        return $uploadFilename;
    }

    public function DownloadFile($filename)
    {
        $tmpFilename = __TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename);

        if (file_exists($tmpFilename)) {
            Filesystem::delete($tmpFilename);
        }

        $file = $this->dropbox->download($filename, $tmpFilename);

        // Downloaded File Metadata
        $metadata = $file->getMetadata();

        // Name
        $name = $metadata->getName();

        return $tmpFilename;
    }

    public function rename($old, $new)
    {
        return $msg;
    }

    public function error($e)
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        //        $url = $_SERVER[''];
        // switch ($code) {
        //     case '401':
        // MediaError::msg('info', $code, 0);
        echo HTMLDisplay::JavaRefresh('/error.php', 0, ['type' => 'Dropbox', 'code' => $code, 'message' => $message]);
        exit;
        // Template::echo('error/dropbox/'.$code, ['TOKEN' => __DROPBOX_AUTH_TOKEN__]);
        //     break;
        // default:
        //     Template::echo('error/dropbox/default', ['CODE' => $code, 'MSG' => $message]);
        //     break;
        // }

        // 0 => "__construct"
        // 1 => "__wakeup"
        // 2 => "getMessage"
        // 3 => "getCode"
        // 4 => "getFile"
        // 5 => "getLine"
        // 6 => "getTrace"
        // 7 => "getPrevious"
        // 8 => "getTraceAsString"
        // 9 => "__toString"
        // https://www.dropbox.com/developers/apps
        // foreach (get_class_methods($e) as $i => $method) {
        //     if (0 == $i) {
        //         continue;
        //     }
        //     if (1 == $i) {
        //         continue;
        //     }
        //     if (9 == $i) {
        //         continue;
        //     }
        //     $msg = $e->$method();
        //     dump([$method, $msg]);
        //     //            $msgs .= Template::gethtml("error/error", ['MSG'=>$msg]);
        // }
        //      Template::echo("error/messages", ['MSG_GROUP'=>$msgs]);

        MediaDevice::getFooter();
        exit;
    }
}
