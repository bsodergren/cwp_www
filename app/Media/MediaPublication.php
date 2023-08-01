<?php
namespace CWP\Media;


class MediaPublication
{

public static $trim_details = [];




public function getPubList($ids)
{
    // if(is_array($ids)) {
    $ids = implode(',', $ids);
    // }

    $sql = 'SELECT * FROM pub_trim WHERE id IN ('.$ids.');';
    $pubs = Media::$connection->query($sql);
    foreach ($pubs as $row) {
        echo $row->id;
        echo $row->pub_name;
    }

    dd($pubs);
}
    public static function getTrimData($publication, $bind)
    {

        $head   = null;
        $foot   = null;
        $get    = false;
        $insert = false;
        $pub    = self::CleanPublication($publication);
        $b      = strtolower($bind);

        if (!key_exists('pub', self::$trim_details)) {
            $get = true;
        } elseif (self::$trim_details['pub'] != $pub) {
            $get = true;
        }

        if (true === $get) {
            $res = Media::$explorer->table('pub_trim')->select('head_trim,foot_trim,delivered_size')->where('pub_name = ?  AND bind = ? ', $pub, $b)->fetch();
            if (null == $res) {
                $insert = true;

                $res    = Media::$explorer->table('pub_trim')->insert(['pub_name' => $pub, 'bind' => $b]);
            }

            if (is_object($res)) {
                $head               = $res->head_trim;
                $foot               = $res->foot_trim;
                $size               = $res->delivered_size;
                self::$trim_details = ['pub' => $publication, 'bind' => $bind, 'head_trim' => $head, 'foot_trim' => $foot, 'size' => $size];
            }

            if (true === $insert) {
                self::getTrimData($publication, $bind);
            }
        }
        return  self::$trim_details;
    }

     public static function CleanPublication($publication)
    {
        $pcs         = ['+', "'", '&'];
        $publication = str_replace($pcs, '', $publication);
        $publication = str_replace('É', 'E', $publication);
        $publication = str_replace('  ', ' ', $publication);
        $publication = str_replace(' ', '_', $publication);
        $publication = strtolower($publication);

        return $publication;
    }
}