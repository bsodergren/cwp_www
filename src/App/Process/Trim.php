<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Media\MediaPublication;
use CWP\Utils\Utils;
use Formr\Formr;

class Trim extends MediaProcess
{
    public function run($req)
    {
        global $_POST;

        $form = new Formr('bootstrap4');

        if ($form->submitted()) {
            if (\array_key_exists('trim_update', $_POST)) {
                $this->trim_update();
            }
            if (\array_key_exists('trim_add', $_POST)) {
                $this->trim_add();
            }
        }

        $this->url = '/settings/trim.php';
    }

    public function trim_update()
    {
        global $_POST;
        $deleteData = [];
        foreach ($_POST as $key => $value) {
            if ('trim_update' == $key) {
                continue;
            }
            if (str_contains($key, 'trim_')) {
                [$_,$id,$type] = explode('_', $key);
                if ('delete' == $type) {
                    if (1 == $value) {
                        $deleteData[] = $id;
                        $cacheNames[] = $value;
                    }
                } elseif ('name' == $type) {
                    $cacheNames[] = $value;
                } else {
                    $updateData[$id][$type] = $value;
                }
            }
        }

        if (\count($deleteData) > 0) {
            foreach ($deleteData as $key) {
                if (\array_key_exists($key, $updateData)) {
                    unset($updateData[$key]);
                }
                $count = Media::$explorer->table('pub_trim')->where('id', $key)->delete(); // UPDATEME
            }
        }
        foreach ($updateData as $id => $data) {
            $insert_data = [
                'head_trim' => $this->cleanTrimPost($data['head']),
                'foot_trim' => $this->cleanTrimPost($data['foot']),
                'face_trim' => $this->cleanTrimPost($data['face']),
                'delivered_size' => $this->cleanTrimPost($data['size'], true),
            ];

            $count = Media::$explorer->table('pub_trim')->where('id', $id)->update($insert_data); // UPDATEME
        }
        foreach ($cacheNames as $name) {
            if (Media::$Stash->has($name)) {
                Media::$Stash->forget($name);
            }
        }

        $this->msg = 'Publications Updated';
    }

    public function trim_add()
    {
        global $_POST;

        $publication = $_POST['publication'];
        if ('' != $publication) {
            $bind = $_POST['bind'];

            $head_trim = $this->cleanTrimPost($_POST['head_trim']);
            $foot_trim = $this->cleanTrimPost($_POST['foot_trim']);
            $face_trim = $this->cleanTrimPost($_POST['face_trim']);
            $delivered_size = $this->cleanTrimPost($_POST['delivered_size'], true);

            $data = [
                'pub_name' => MediaPublication::CleanPublication($publication),
                'bind' => $bind,
                'head_trim' => $head_trim,
                'foot_trim' => $foot_trim,
                'face_trim' => $face_trim,
                'delivered_size' => $delivered_size,
            ];
            $res = Media::$explorer->table('pub_trim')->insert($data); // UPDATEME
            $this->msg = 'Publication Added';
        } else {
            $this->msg = 'No publication named';
        }
    }

    public function cleanTrimPost($var, $size = false)
    {
        if ('' == $var) {
            $var = null;
        } else {
            if (!str_contains($var, '/')) {
                if (true == $size) {
                    $var = Utils::DelSizeToFrac($var);
                } else {
                    $var = utils::floatToFrac($var);
                }
            }
        }

        return $var;
    }
}
