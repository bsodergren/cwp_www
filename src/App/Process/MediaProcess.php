<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Core\MediaError;
use CWP\HTML\HTMLDisplay;

class MediaProcess
{
    public object $media;

    public $job_id;

    public $url = '/index.php';

    public $msg = '';

    public $timeout = '0';
    public $request = [];

    public static function Check($media)
    {
        if (
            array_key_exists('FORM_PROCESS', $_REQUEST)
            || array_key_exists('submit', $_REQUEST)
            || array_key_exists('update_job', $_REQUEST)
        ) {
            $refer_script = basename(parse_url($_SERVER['HTTP_REFERER'], \PHP_URL_PATH), '.php');

            if (__SCRIPT_NAME__ == $refer_script) {
                MediaError::msg('info', $refer_script.'< >'.__SCRIPT_NAME__, 0);
            }

            if (null === $refer_script || '' == $refer_script) {
                MediaError::msg('info', 'referer not set', 0);
                echo HTMLDisplay::JavaRefresh('/index.php', 0);
            }

            define('__FORM_POST__', $refer_script);

            if (isset($_POST['divClass'])) {
                list($k, $id) = explode('_', $_POST['row_id']);
                if (str_contains($_POST['divClass'], 'show')) {
                    $hidden = 1;
                } else {
                    $hidden = 0;
                }

                $count = Media::$explorer->table('media_job') // UPDATEME
                    ->where('job_id', $id) // must be called before update()
                    ->update([
                        'hidden' => $hidden,
                    ])
                ;
                exit;
            }

            $procesClass = ucfirst(__FORM_POST__);
            if (array_key_exists('FORM_PROCESS', $_REQUEST)) {
                switch ($_REQUEST['FORM_PROCESS']) {
                    case 'updateSetting':
                        $procesClass = ucfirst('Settings');
                        break;
                    case 'createJob':
                        $procesClass = ucfirst('createJob');
                        break;
                }
            }

            $procesClass = 'CWP\\Process\\'.$procesClass;

            $mediaProcess = new $procesClass($media);
            $mediaProcess->run($_REQUEST);

            $mediaProcess->reload();
        }

        return null;
    }

    public function __construct($media)
    {
        if (\is_object($media)) {
            $this->media = $media;
            $this->job_id = $media->job_id;
        }
    }

    public function run($req)
    {
        $class = static::class;
        (new $class())->run($req);
    }

    public function reload()
    {
        echo HTMLDisplay::JavaRefresh($this->url, $this->timeout, $this->msg);
    }
}
