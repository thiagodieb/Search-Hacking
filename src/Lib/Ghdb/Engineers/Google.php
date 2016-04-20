<?php

namespace Aszone\Component\SearchHacking\Lib\Ghdb\Engineers;

use Aszone\Component\SearchHacking\Lib\Ghdb\Utils;
use Aszone\Component\SearchHacking\Lib\FakeHeaders\FakeHeaders;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Aszone\Component\SearchHacking\Lib\ProxiesAvenger;
use Aszone\Component\SearchHacking\Lib\ProxiesAvenger\Proxies;

class Google
{
    public $siteGoogle;
    public $listOfVirginProxies;
    public $usginVirginProxies;
    public $tor;
    public $commandData;
    public $proxy;
    public $error;

    public function __construct($data)
    {
        $this->commandData=$data;

        //check if set vp and initialize method Proxyvirgin of ProxyAvenger
        if($this->commandData['virginProxies'] OR $this->commandData['proxyOfSites'] OR $this->commandData['tor'])
        {
            $this->Proxies = new Proxies();
        }

        if($this->commandData['tor'])
        {
            $this->proxy=$this->Proxies->getTor();

        }

        if($this->commandData['proxyOfSites'])
        {
            $this->proxy=$this->Proxies->getProxyOfSites();
        }

        $this->getSiteGoogle();
        if($this->commandData['virginProxies'])
        {
            $this->listOfVirginProxies  = $this->Proxies->getVirginSiteProxies();
            $this->usginVirginProxies   =true;
        }

        $result=$this->validation();

        if($result)
        {
            $this->error=$result;
        }
    }

    public function getSiteGoogle()
    {
        $ini_google_sites = parse_ini_file(__DIR__."/../resource/AllGoogleSites.ini");
        $this->siteGoogle=$ini_google_sites[array_rand($ini_google_sites)];
    }

    private function validation()
    {

        if($this->commandData['virginProxies'] AND !$this->Proxies->checkVirginProxiesExist())
        {
            $error['type']="vp";
            $error['result']="Not exist list of botnets Virgin Proxy";
            return $error;
        }
        return;
    }

    public function run()
    {

        $exit=false;
        $count=0;
        $paginator="";
        $countProxyVirgin = rand(0,count($this->listOfVirginProxies)-1);
        $resultFinal=array();

        while ($exit == false) {
            if($count!=0){
                $numPaginator=100*$count;
                $paginator="&start=".$numPaginator;
            }

            $urlOfSearch="https://".$this->siteGoogle."/search?q=".urlencode($this->commandData['dork'])."&num=100&btnG=Search&pws=1".$paginator;
            echo "Page ".$count."\n";

            if($this->commandData['virginProxies']) {
                $arrLinks=$this->getLinksByVirginProxies($urlOfSearch,$this->listOfVirginProxies[$countProxyVirgin]);

                if($arrLinks=="repeat")
                {
                    $count--;
                }

                if($countProxyVirgin==count($this->listOfVirginProxies)-1)
                {
                    $countProxyVirgin=0;
                }
                else
                {
                    $countProxyVirgin++;
                }
            }
            else{
                $arrLinks=$this->getLinks($urlOfSearch);
            }

            echo "\n".$urlOfSearch."\n";

            $utils=new Utils();
            $results=$utils->sanitazeLinks($arrLinks);

            if( (count($results)==0 AND $arrLinks!="repeat") OR ($this->commandData['virginProxies']) ){
                $exit=true;
            }
            $resultFinal=array_merge($resultFinal,$results);
            $count++;
        }

        return $resultFinal;
    }



    private function getLinksByVirginProxies($urlOfSearch,$urlProxie)
    {
        $header= new FakeHeaders();

        echo "Proxy : ".$urlProxie."\n";

        $dataToPost=['body' =>
            ['url' => $urlOfSearch ]
        ];

        $valid=true;
        while($valid==true)
        {
            try{
                $client 	= new Client([
                    'defaults' => [
                        'headers' => ['User-Agent' => $header->getUserAgent()],
                        'proxy'   => $this->proxy,
                        'timeout' => 60
                    ]
                ]);
                $body = $client->post($urlProxie,$dataToPost)->getBody()->getContents();

                $valid      =false;
                break;
            }catch(\Exception $e){
                echo "ERROR : ".$e->getMessage()."\n";
                if($this->proxy==false){
                    echo "Your ip is blocked, we are using proxy at now...\n";
                    $this->pl= true;
                }

                return "repeat";

                sleep(2);
            }
        }

        $crawler 	= new Crawler($body);
        $arrLinks 	= $crawler->filter('a');
        return $arrLinks;
    }

    public function getLinks($urlOfSearch)
    {
        echo "*";
        $header= new FakeHeaders();
        $valid=true;
//        while($valid==true)
//        {
            try{
                $client 	= new Client([
                    'defaults' => [
                        'headers' => ['User-Agent' => $header->getUserAgent()],
                        'proxy'   => $this->proxy,
                        'timeout' => 60
                    ]
                ]);
                $body 		= $client->get($urlOfSearch)->getBody()->getContents();
                //$valid=false;
                $crawler 	= new Crawler($body);
                $arrLinks 	= $crawler->filter('a');
                return $arrLinks;

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