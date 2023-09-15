<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class Dropbox
{
    public object $adapter;
    public object $client;
    public object $filesystem;
    public $rootPath = __DROPBOX_FILES_DIR__;

    public function __construct()
    {
        $this->client = new Client(__DROPBOX_AUTH_TOKEN__);
        $this->adapter = new DropboxAdapter($this->client);
        $this->filesystem = new Filesystem($this->adapter);
    }

    public function cleanPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function createFolder($path)
    {
        $path = $this->rootPath.'/'.$path;
        $path = $this->cleanPath($path);
        $this->client->createFolder($path);
    }
}
