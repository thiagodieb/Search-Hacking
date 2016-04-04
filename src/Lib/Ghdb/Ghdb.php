<?php

namespace Aszone\Component\SearchHacking\Lib\Ghdb;

use Aszone\Component\SearchHacking\Lib\FakeHeaders\FakeHeaders;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Aszone\Component\SearchHacking\Lib\Ghdb\Engineers\Google;


class Ghdb{

	public $dork;

    public $pathProxy;

    public $proxy;

    public $tor;

    public $proxylist;

    public $countProxylist;

    public $usginVirginProxies;

    public $virginProxies;

    public $coutnVirginProxy;

    public $siteGoogle;

    public $Proxies;

    public $commandData;

	public function __construct($commandData)
	{
        //Check command of entered.
        $defaultEnterData=$this->defaultEnterData();
        $this->commandData=array_merge($defaultEnterData,$commandData);
//        $this->dork = $this->commandData['dork'];
//        $this->proxylist=$this->commandData['proxylist'];
//        $this->pathProxy = __DIR__ . '/resource/proxys.json';
//        $this->countProxylist=1;

        if(file_exists($this->pathProxy))
        {
            unlink($this->pathProxy);
        }

	}

    private function defaultEnterData()
    {
        $dataDefault['dork']=false;
        $dataDefault['pl']=false;
        $dataDefault['tor']=false;
        $dataDefault['virginProxies']=false;
        $dataDefault['proxyOfSites']=false;

        return $dataDefault;
    }

	public function runGoogle()
    {

//        $listOfVirginProxies = $this->Proxies->getVirginSiteProxies();
//        $google = new Google($this->dork,$listOfVirginProxies);
        $google = new Google($this->commandData);
        if($google->error)
        {
            return $google;
        }
        return $google->run();

	}

    public function runGoogleApi()
    {
        $exit=false;
        $count=0;
        $paginator="";
        $resultFinal=array();
        while ($exit == false) {
            if($count!=0){
                $numPaginator=100*$count;
                $paginator="&start=".$numPaginator;
            }
            $urlOfSearch="http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=8&q=".$this->dork.$paginator."&userip=".$this->getIp()."&filter=1&safe=off&num=100";
            echo $urlOfSearch;

            $arrLinks=$this->getJsonSearch($urlOfSearch);
            $results=$this->getJsonGoogleApi($arrLinks);
            $results=$this->sanitazeLinksJson($results);
            if(count($results)==0){
                $exit=true;
            }
            $resultFinal=array_merge($results,$resultFinal);
            $count++;
        }
        return $resultFinal;
    }

    public function runDuckduckGo(){
        //https://api.duckduckgo.com/html/?q=[DORK]&kl=en-us&p=-1&s=[PAG]&dc=[PAG3]&o=json&api=d.js
    }

    public function sanitazeLinksJson($links)
    {
        $hrefs=array();
        foreach ($links as $keyLink => $valueLink)
        {
            $url=$this->clearLink($valueLink);
            $validResultOfBlackList=$this->checkBlacklist($url);
            if(!$validResultOfBlackList)
            {
                $hrefs[]=$valueLink;
            }
        }
        $hrefs = array_unique($hrefs);
        return $hrefs;

    }

    public function getIp(){
        return intval(rand() % 255) . "." . intval(rand() % 255) . "." . intval(rand() % 255) . "." . intval(rand() % 255);
    }

    public function getJsonSearch($urlOfSearch){
        $client 	= new Client();
        $body 		= $client->get($urlOfSearch)->getBody()->getContents();
        $result=json_decode($body);
        return $result;
        //return $arrLinks;
    }

    public function getJsonGoogleApi($listGoogleApi=""){
        $arrayFinal=array();
        if(isset($listGoogleApi->responseData->results)){
            foreach($listGoogleApi->responseData->results as $result){
                $arrayFinal[]=$result->url;
            }
        }
        return $arrayFinal;
    }



}