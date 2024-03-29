<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Media;

use CWP\Core\Media;

class MediaPublication
{
    public static $trim_details = [];

    public function getPubList($ids)
    {
        // if(is_array($ids)) {
        $ids  = implode(',', $ids);
        // }

        $sql  = 'SELECT * FROM pub_trim WHERE id IN ('.$ids.');';
        $pubs = Media::$connection->query($sql);
        foreach ($pubs as $row) {
            echo $row->id;
            echo $row->pub_name;
        }

        dd($pubs);
    }

//     public static function getTrimData($publication, $bind)
//     {
//         $head   = null;
//         $foot   = null;
//         $get    = false;
//         $insert = false;
//         $pub    = self::CleanPublication($publication);
//         $b      = strtolower($bind);



//         if (! \array_key_exists('pub', self::$trim_details)) {
//             $get = true;
//         } elseif (self::$trim_details['pub'] != $pub) {
//             $get = true;
//         }

//    //dump([$b,$pub]);

// dd($res);
//             if (null == $res) {
//                 $insert = true;

//                 $res    = Media::$explorer->table('pub_trim')->insert(['pub_name' => $pub, 'bind' => $b]); // UPDATEME
//             }



//             if (true === $insert) {
//                 self::getTrimData($publication, $bind);
//             }
//       //  }

//         return self::$trim_details;
//     }


    public static function getTrimData($publication,$bind)
    {
        $pub    = self::CleanPublication($publication);
        $b      = strtolower($bind);
        $head   = null;
        $foot   = null;
        $size = null;
        $cacheName = $pub."-".$b;
        if(Media::$Stash->has($cacheName)) {
           $trim_details = Media::$Stash->get($cacheName);
        } else {
            $res = Media::$explorer->table('pub_trim')->select('head_trim,foot_trim,delivered_size')->where('pub_name = ?  AND bind = ? ', $pub, $b)->fetch(); // UPDATEME
            if (null == $res) {
                $res = Media::$explorer->table('pub_trim')->insert(['pub_name' => $pub, 'bind' => $b]); // UPDATEME
                $trim_details = ['pub' => $publication, 'bind' => $bind, 'head_trim' => $head,
                'foot_trim' => $foot, 'size' => $size];
            } else {
                if (\is_object($res)) {
                    $head               = $res->head_trim;
                    $foot               = $res->foot_trim;
                    $size               = $res->delivered_size;
                    $trim_details = ['pub' => $publication, 'bind' => $bind, 'head_trim' => $head, 'foot_trim' => $foot, 'size' => $size];
                }
            }
            Media::$Stash->put($cacheName,$trim_details,60);

        }

        return $trim_details;


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
