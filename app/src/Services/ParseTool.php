<?php
/**
 * Created by PhpStorm.
 * User: #xHome#
 * Date: 15/08/2018
 * Time: 14:17
 */

namespace App\Services;

class ParseTool
{
    /**
     * @param $xmlElement
     * @return mixed|string
     */
    public function getAsXMLContent($xmlElement)
    {
        $content=$xmlElement->asXML();
        $end=strpos($content, '>');
        if ($end!==false) {
            $tag=substr($content, 1, $end-1);
            return str_replace(array('<'.$tag.'>', '</'.$tag.'>'), '', $content);
        } else {
            return '';
        }
    }
}
