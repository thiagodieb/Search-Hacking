<?php
/**
 * Created by PhpStorm.
 * User: lenon
 * Date: 03/04/16
 * Time: 19:24
 */
namespace Aszone\Component\SearchHacking\Lib\Ghdb;

use Symfony\Component\DomCrawler\Crawler;
use Aszone\Component\SearchHacking\Lib\FakeHeaders\FakeHeaders;
use GuzzleHttp\Client;

class Utils
{
    public function sanitazeLinks($links=array()){

        $hrefs=array();
        if(!empty($links)) {
            foreach ($links as $keyLink => $valueLink) {
                echo "\n";
                $url = $this->clearLink($valueLink->getAttribute('href'));
                echo $url;
                $validResultOfBlackList = $this->checkBlacklist($url);
                if (!$validResultOfBlackList AND $url) {
                    $hrefs[] = $url;
                }
            }
            $hrefs = array_unique($hrefs);
        }
        return $hrefs;
    }

    public function checkBlacklist($url="")
    {
        if(!empty($url)){

            $validXmlrpc = preg_match("/(https?\:\/\/|^)(.+?)\//", $url, $matches, PREG_OFFSET_CAPTURE);
            $url="";
            if(isset($matches[2][0]))
            {
                $url=$matches[2][0];
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
        echo $url."=>";
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

            $validXmlrpc = preg_match("/url=(.*?)&tld/", $url, $matches, PREG_OFFSET_CAPTURE);

            if(isset($matches[1][0]) )
            {
                return urldecode($matches[1][0]);
            }

            $validXmlrpc = preg_match("/^((http|https):\/\/|www)(.+?)\//", $url, $matches, PREG_OFFSET_CAPTURE);
            if(isset($matches[0][0]))
            {
                //var_dump($matches);
                //$url= $matches[0][0];
                echo $url;
                $pos1 = strpos($url, "www.blogger.com");
                $pos2 = strpos($url,"youtube.com");
                $pos3 = strpos($url,".google.");
                $pos4 = strpos($url,"yandex.ru");
                $pos5 = strpos($url,"microsoft.com");
                $pos6 = strpos($url,"microsofttranslator.com");


                if($pos1 === false AND $pos2 === false AND $pos3 === false
                    AND $pos4 === false AND $pos5 === false AND $pos6 === false)
                {
                    return trim($url);
                }
            }

        }


        return false;
    }

    public function validation($command,$chekVirginProxy)
    {

        if($command AND !$chekVirginProxy)
        {
            $error['type']="vp";
            $error['result']="Not exist list of botnets Virgin Proxy";
            return $error;
        }
        return;
    }

    public function getLinks($body){
        $crawler 	= new Crawler($body);
        return $crawler->filter('a');
    }

    public function getBody($urlOfSearch,$proxy)
    {
        $header= new FakeHeaders();
        $valid=true;
//        while($valid==true)
//        {
        try{
            $client 	= new Client([
                'defaults' => [
                    'headers' => ['User-Agent' => $header->getUserAgent()],
                    'proxy'   => $proxy,
                    'timeout' => 60
                ]
            ]);
            $body 		= $client->get($urlOfSearch)->getBody()->getContents();
            //$valid=false;
            /*$crawler 	= new Crawler($body);
            $arrLinks 	= $crawler->filter('a');*/
            return $body;

        }catch(\Exception $e){

            echo "ERROR : ".$e->getMessage()."\n";
            if($this->proxy==false){
                echo "Your ip is blocked, we are using proxy at now...\n";
                //$valid=false;
            }
            //$this->setProxyOfSites();
            //sleep(2);
        }

        return false;
//        }


    }
}