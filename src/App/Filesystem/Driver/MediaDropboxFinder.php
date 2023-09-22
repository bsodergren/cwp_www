<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem\Driver;

use CWP\Filesystem\MediaDropbox;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

class MediaDropboxFinder extends MediaDropbox
{
    private object $db;

    public function __construct()
    {
        $dropbox = new MediaDropbox();
        $this->db = $dropbox;
    }

    public function is_dir($dir)
    {
        $r = $this->db->exists(basename($dir));

        return $r;
    }

    public function search($search, $path = '/')
    {
        try {
            $searchResults = $this->db->dropbox->search($path, $search, ['start' => 0, 'max_results' => 50]);
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
            $file = $this->db->dropbox->download($filename);

            $contents = $file->getContents();

            // Save file contents to disk
            file_put_contents($tmp_file, $contents);
        }

        return $tmp_file;
    }
}
