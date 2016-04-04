<?php
/**
 * Created by PhpStorm.
 * User: lenon
 * Date: 03/04/16
 * Time: 19:24
 */
namespace Aszone\Component\SearchHacking\Lib\Ghdb;

class Utils
{
    public function sanitazeLinks($links){
        $hrefs= array();
        foreach ($links as $keyLink => $valueLink)
        {

            $url=$this->clearLink($valueLink->getAttribute('href'));
            $validResultOfBlackList=$this->checkBlacklist($url);

            if(!$validResultOfBlackList AND $url)
            {
                $hrefs[]=$url;

            }
        }
        $hrefs = array_unique($hrefs);

        return $hrefs;
    }

    public function checkBlacklist($url="")
    {
        if(!empty($url)){

            $validXmlrpc = preg_match("/\/\/(.+?)\//", $url, $matches, PREG_OFFSET_CAPTURE);
            $url="";
            if(isset($matches[1][0]))
            {
                $url=$matches[1][0];
            }
            $ini_blakclist = parse_ini_file(__DIR__."/resource/Blacklist.ini");
            $key=array_search($url,$ini_blakclist);
            if($key!=false){
                return true;
            }
        }
        return false;
    }

    public function clearLink($url="")
    {
        if(!empty($url))
        {
            $validXmlrpc = preg_match("/search%3Fq%3Dcache:.+?:(.+?)%252B/", $url, $matches, PREG_OFFSET_CAPTURE);
            if(isset($matches[1][0]) )
            {
                return $matches[1][0];
            }

            $validXmlrpc = preg_match("/search\?q=cache:.+?:(.+?)\+/", $url, $matches, PREG_OFFSET_CAPTURE);
            if(isset($matches[1][0]) )
            {
                return $matches[1][0];
            }

            $validXmlrpc = preg_match("/^((http|https):\/\/|www)(.+?)\//", $url, $matches, PREG_OFFSET_CAPTURE);
            if(isset($matches[0][0]))
            {
                //var_dump($matches);
                //$url= $matches[0][0];
                $pos1 = strpos($url, "www.blogger.com");
                $pos2 = strpos($url,"youtube.com");
                $pos3 = strpos($url,".google.");
                if($pos1 === false AND $pos2 === false AND $pos3 === false)
                {
                    return trim($url);
                }
            }

        }


        return false;
    }
}