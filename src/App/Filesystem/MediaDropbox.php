<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

use CWP\Template\Template;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Nette\Utils\FileSystem;

// use Symfony\Component\Filesystem\Filesystem;

class MediaDropbox
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

    public function search($search, $path = '/')
    {
        try {
            $searchResults = $this->dropbox->search($path, $search, ['start' => 0, 'max_results' => 100]);
        } catch (DropboxClientException $e) {
            $this->error($e);
        }
        $items = $searchResults->getItems();

        return $items;
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
        // $path = '/'.__DROPBOX_FILES_DIR__.'/'.$path;
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
        $listFolderContents = $this->dropbox->listFolder($path);

        // Fetch Items (Returns an instance of ModelCollection)
        $items = $listFolderContents->getItems();

        // All Items
        $array = $items->all();
        foreach ($array as $item) {
            $return[] = ['name' => $item->getName(), 'path' => $item->getpathDisplay()];
        }

        return $return;
    }

    public function deleteFile($file)
    {
        $deletedFolder = $this->dropbox->delete($file);
    }

    public function save($localfile, $remotefile, $options = [])
    {
        $remotefile = Filesystem::unixSlashes($remotefile);

        if (false !== $this->exists(basename($remotefile))) {
            $this->deleteFile($remotefile);
        }

        try {
            $file = $this->dropbox->upload($localfile, $remotefile, $options);
        } catch (DropboxClientException $e) {
            $this->error($e);
        }

        return $file;
    }

    public function cleanPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function getDirectory($type, $create = false)
    {
        switch ($type) {
            case 'upload':
                $tmp_upload_folder = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$this->upload_dir;
                $tmp_upload_folder = Filesystem::normalizePath($tmp_upload_folder);

                if (true == $create) {
                    Filesystem::createDir($tmp_upload_folder, 0775);
                }

                return $tmp_upload_folder;
            case 'tmp':
                return __TEMP_DIR__;
            case 'pdf':
                if (true == $create) {
                    $this->createFolder($this->upload_dir);
                }

                return $this->upload_dir;
        }

        return null;
    }

    public static function UploadFile($filename, $uploadFilename, $options = [])
    {
        $dropbox = new self();
        $dropbox->save($filename, $uploadFilename, $options);

        return $uploadFilename;
    }

    public static function DownloadFile($filename)
    {
        $dropbox = new self();

        $tmpFilename = __TEMP_DIR__.\DIRECTORY_SEPARATOR.basename($filename);

        if (file_exists($tmpFilename)) {
            Filesystem::delete($tmpFilename);
        }

        $file = $dropbox->dropbox->download($filename, $tmpFilename);

        // Downloaded File Metadata
        $metadata = $file->getMetadata();

        // Name
        $name = $metadata->getName();

        return $tmpFilename;
    }

    public function error($e)
    {
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
        foreach (get_class_methods($e) as $i => $method) {
            if (0 == $i) {
                continue;
            }
            if (1 == $i) {
                continue;
            }
            if (9 == $i) {
                continue;
            }
            $msg = $e->$method();
            dump([$method, $msg]);
            //            $msgs .= Template::gethtml("error/error", ['MSG'=>$msg]);
        }
        //      Template::echo("error/messages", ['MSG_GROUP'=>$msgs]);
        exit;
    }
}
