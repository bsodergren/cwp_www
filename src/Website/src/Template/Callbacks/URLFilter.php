<?php

namespace  CWPDisplay\Template\Callbacks;

use  CWPDisplay\Template\Functions\Functions;
use KubAT\PhpSimple\HtmlDomParser;


class URLFilter
{
    public static function parse_urllink($text,$vars)
    {
        $dom = HtmlDomParser::str_get_html($text);

        if (false === $dom) {
            return $text;
        }

        $lookup = array_key_first($vars);
        list($element,$attribute) = explode("=",$lookup);

        $elems = $dom->find($element);

        if (0 == \count($elems)) {
            return $text;
        }
        $query = array_key_first($vars[$lookup]);
        $value = $vars[$lookup][$query];

        foreach ($elems as $a) {
            $url = $a->getAttribute($attribute);
             if (!str_contains($url, $query)) {
                 $url = str_replace('?', '?'.$query.'='.$value.'&', $url);
            }
             $a->setAttribute($attribute, $url);

        //     //     $a->setAttribute('data-bs-placement', 'top');
        //     //     $a->setAttribute('data-bs-toggle', 'tooltip');
        //     //     $a->setAttribute('title', $a->href);
         }

         return $dom;
    }
}
