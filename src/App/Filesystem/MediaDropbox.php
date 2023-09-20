<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Nette\Utils\FileSystem;

class MediaDropbox
{
    public $rootPath = __DROPBOX_FILES_DIR__;
    public object $dropbox;

    public function __construct()
    {
        $app = new DropboxApp(__DROPBOX_APP_KEY__, __DROPBOX_APP_SECRET__, __DROPBOX_AUTH_TOKEN__);
        $this->dropbox = new Dropbox($app);
    }

    public function exists($path)
    {

        $searchResults = $this->dropbox->search('/', $path, ['start' => 0, 'max_results' => 1]);
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
        $path = '/'.__DROPBOX_FILES_DIR__.'/'.$path;
        $path = Filesystem::normalizePath($path);

        $path = Filesystem::unixSlashes($path);

        if (str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        try {
            $folder = $this->dropbox->createFolder($path);
        } catch (DropboxClientException $e) {
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
            $return[] = ['name' => $item->getName(), 'path' => $item->getPathLower()];
        }

        return $return;
    }

    public function deleteFile($file){
        $deletedFolder = $this->dropbox->delete($file);
    }

    public function save($localfile, $remotefile, $options = [])
    {
        $remotefile = Filesystem::unixSlashes($remotefile);

        if($this->exists(basename($remotefile)) !== false)
        {
            $this->deleteFile($remotefile);
        }

        try {
            $file = $this->dropbox->upload($localfile, $remotefile, $options);
        } catch (DropboxClientException $e) {
            dd($e, $remotefile);
        }

        return $file;
    }

    public function cleanPath($path)
    {
        return str_replace('\\', '/', $path);
    }
}
