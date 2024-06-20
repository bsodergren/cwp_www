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
    private static $ACTION_URLS = [
        'edit_form' => 'form.php',
        'view_xlsx' => 'view.php',
        'create_xlsx' => 'process.php',
        'create_zip' => 'process.php',
        'email' => 'mail.php',
        'export_google' => 'process.php',
        'delete_job' => 'delete_job.php',
        'delete_xlsx' => 'process.php',
        'delete_zip' => 'process.php',
    ];
    private static $PageSortDir = 'elements/JobMenu';

    public static $jobMenu = [];

    public static function displayJobMenus()
    {
        $jobBlocks = '';
        $table = Media::$explorer->table('media_job'); // UPDATEME
        $results = $table->fetchAssoc('job_id');
        $cnt = $table->count('*');
        if (0 == $cnt) {
            echo Elements::JavaRefresh('/import.php', 0);
            exit;
        }

        $postOptions = ['view XLS' => 'view'];
        foreach ($results as $k => $row) {
            self::$jobMenu = ['ACCORDIAN_LINKS' => ''];

            $media = new Media($row);
            $mediaDir = new MediaFileSystem($media->pdf_file, $media->job_number);

            $zip_file = $media->zip_file;
            $xlsx_dir = $media->xlsx_directory;
            $xlsr_exists = Media::get_exists('xlsx', $row['job_id']);

            self::$jobMenu['ACCORDIAN_ID'] = Display::RandomId('accordian_');
            self::$jobMenu['ACCORDIAN_HEADER'] = $row['job_number'].' '.$row['close'];

            self::getUrl($row['job_id'], 'edit_form', 'Edit Form');

            if (true == $xlsr_exists) {
                self::getUrl($row['job_id'], 'view_xlsx', 'View XLSX');
            } else {
                self::getUrl($row['job_id'], 'create_xlsx', 'Create XLSX', 'success');
            }
            if (MediaSettings::GoogleAvail()) {
                self::getUrl($row['job_id'], 'export_google', 'export to google', 'success');
                self::getUrl($row['job_id'], 'open_google', 'Open  Google drive');
            }
            if (__SHOW_ZIP__ == true) {
                if ($mediaDir->exists($zip_file)) {
                    self::getUrl($row['job_id'], 'delete_zip', 'Delete zip', 'danger');

                    if (__SHOW_MAIL__ == true) {
                        self::getUrl($row['job_id'], 'email', 'Email zip');
                    }
                } else {
                    self::getUrl($row['job_id'], 'create_zip', 'Create zip', 'warning');
                }
            }

            // $accordian['ACCORDIAN_LINKS'].= getUrl($row['job_id'],$action,$text);

            $jobBlocks .= Render::html(self::$PageSortDir.'/jobList', self::$jobMenu);
        }

        return $jobBlocks;
    }

    private static function getUrl($jobid, $action, $text, $class = 'default')
    {
        $url = self::geturlfromAction($action) ."?job_id=".$jobid."&action=".$action;

        self::$jobMenu['ACCORDIAN_LINKS'] .= Render::html(self::$PageSortDir.'/jobItem', [
            'jobId' => $jobid,
            'action' => $action,
            'NAME' => $text,
            //  'class' => 'nav-bg-'.$class,
            'url' => $url,
        ]);
    }

    private static function geturlfromAction($action)
    {
        return __URL_ROOT__.'/'.self::$ACTION_URLS[$action];
    }
}
