<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWPDisplay\Template\Functions\Traits;

use CWP\Core\Media;
use CWP\Core\MediaSettings;
use CWP\Filesystem\MediaFileSystem;
use CWPDisplay\Template\Display;
use CWPDisplay\Template\Render;
use UTMTemplate\HTML\Elements;

trait JobMenu
{
    private static $PageSortDir = 'elements/JobMenu';

    public static function displayJobMenus()
    {
        $table = Media::$explorer->table('media_job'); // UPDATEME
        $results = $table->fetchAssoc('job_id');
        $cnt = $table->count('*');
        if (0 == $cnt) {
            echo Elements::JavaRefresh('/import.php', 0);
            exit;
        }
        utmdump($results);

        $postOptions = ['view XLS' => 'view'];
        foreach ($results as $k => $row) {
            $accordian = [];

            $media = new Media($row);
            $mediaDir = new MediaFileSystem($media->pdf_file, $media->job_number);

            $zip_file = $media->zip_file;
            $xlsx_dir = $media->xlsx_directory;
            $xlsr_exists = Media::get_exists('xlsx', $row['job_id']);

            $accordian['ACCORDIAN_ID'] = Display::RandomId('accordian_');
            $accordian['ACCORDIAN_HEADER'] = $row['job_number'] . ' '.$row['close'];

            $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'edit_form', 'Edit Form');

            if (true == $xlsr_exists) {
                $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'view_xlsx', 'View XLSX');
            } else {
                $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'view_xlsx', 'Create XLSX','warning');

            }
            if (MediaSettings::GoogleAvail()) {
                $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'export_google', 'export to google','success');
                $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'open_google', 'Open  Google drive');
            }
            if (__SHOW_ZIP__ == true) {
                if ($mediaDir->exists($zip_file)) {
                    $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'view_xlsx', 'Delete zip','danger');

                    if (__SHOW_MAIL__ == true) {
                        $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'view_xlsx', 'Email zip');
                    }
                } else {
                    $accordian['ACCORDIAN_LINKS'] .= self::getUrl($row['job_id'], 'view_xlsx', 'Create zip','warning');
                }
            }

            // $accordian['ACCORDIAN_LINKS'].= getUrl($row['job_id'],$action,$text);

            $jobBlocks .= Render::html(self::$PageSortDir.'/jobList', $accordian);
        }

        return Render::html(self::$PageSortDir.'/sort', ['SORT_HTML' => $jobBlocks]);
    }

    private static function getUrl($jobid, $action, $text,$class='default')
    {
        return Render::html(self::$PageSortDir.'/jobItem', [
            'jobId' => $jobid,
            'action' => $action,
            'NAME' => $text,
            'class' => 'nav-bg-'.$class,
        ]);
    }
}
