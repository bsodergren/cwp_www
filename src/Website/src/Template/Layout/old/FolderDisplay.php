<?php

namespace Plex\Modules\Display\Layout;

use Plex\Core\Request;
use Plex\Modules\Database\Traits\FolderFunc;
use Plex\Modules\Display\VideoDisplay;
use Plex\Modules\VideoCard\VideoCard;
use CWP\Template\Render;

class FolderDisplay extends VideoDisplay
{
    use FolderFunc;
    public $showVideoDetails = false;
    private $template_base = '';
    public $parentfolder = '';
    public object $ReqObj;
    public $folderCounts;

    public function __construct($template_base = 'fileBrowser')
    {
        $this->template_base = 'pages'.DIRECTORY_SEPARATOR. $template_base;
        $this->ReqObj = new Request();
        $this->urlPath();
    }

    public function Display($folders, $files, $page_array = [])
    {
        $videohtml = $this->fileDisplay($files);
        // utminfo($videohtml);
        $videohtml['FolderListing'] = $this->folderDisplay($folders);

        return Render::html($this->template_base.'/page', $videohtml);
    }

    private function urlPath()
    {
        $query = parse_url($this->ReqObj->urlPattern);
        parse_str($query['query'], $query_parts);
        // utmdd($query_parts);

        foreach ($query_parts as $k => $v) {
            if ('p' != $k) {
                $req .= '?'.$k.'='.$v;
                continue;
            }
            $this->currentPath = $v.'/';
        }

        $this->url = $query['path'].$req;
    }

    public function getParentUrl($folder)
    {
        $root = $this->fm_clean_path(__PLEX_LIBRARY__);

        $this->CurrentFolderName = $this->fm_clean_path($this->currentPath);

        $f = str_replace($root, '', $folder);

        // $f = $this->fm_get_parent_path($root);
        return $this->fm_clean_path($f);
    }

    public function folderLink($folder)
    {
        if ($folder == $_SESSION['library']) {
            $folder = '';
        }

        return $this->url.'&p='.urlencode($folder);
    }

    public function folderDisplay($folders)
    {
        $parentLink = '..';
        if ('' != $this->parentfolder) {
            $parentLink = $this->getParentUrl($this->parentfolder);
        }
        $parentText = $parentLink;

        $folderLinks = Render::html($this->template_base.'/FolderLink', [
            'FolderLink' => $this->folderLink($parentLink),
            'FolderText' => $parentText]);

        if (\count($folders) > 0) {
            foreach ($folders as $folder) {
                array_walk($this->folderCounts, function ($value, $key, $folder) {
                    if ($value['folder'] == $folder) {
                        $this->FolderCount = $value['folderCount'];
                        $this->videoCount = $value['videoCount'];
                    }
                }, $folder);
                $folderLink = $this->folderLink($this->currentPath.$folder);
                $folderText = $folder;
                $folderLinks .= Render::html($this->template_base.'/FolderLink',
                    ['FolderLink' => $folderLink,
                        'FolderText' => $folderText,
                    'FolderCount' => $this->FolderCount,
                'videoCount' => $this->videoCount]);
            }
        }

        return Render::html($this->template_base.'/FolderList', ['FolderList' => $folderLinks,
    'FolderHeader' => $this->CurrentFolderName]);
    }

    public function fileDisplay($files)
    {
        $total_files = '';
        $videoinfo = new VideoCard();

        foreach ($files as $id => $row) {
            $row_id = $row['id'];
            $row['next'] = 0;
            if (\array_key_exists($id + 1, $files)) {
                $row['next'] = $files[$id + 1]['id'];
            }

            $table_body[] = $videoinfo->VideoInfo($row, $total_files);
        }

        foreach ($table_body as $key => $value) {
            $videohtml['BODY'] .= $value['VIDEO'];

            $videohtml['HIDDEN_STUDIO'] = $value['HIDDEN_STUDIO'];

            $videohtml['VIDEO_KEY'] = $value['VIDEO_KEY'];
        }

        return $videohtml;
    }
}
