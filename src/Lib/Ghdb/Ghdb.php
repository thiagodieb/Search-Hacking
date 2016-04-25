<?php

namespace Aszone\Component\SearchHacking\Lib\Ghdb;

use Aszone\Component\SearchHacking\Lib\Ghdb\Engineers\GoogleApi;
use Aszone\Component\SearchHacking\Lib\Ghdb\Engineers\Google;
use Aszone\Component\SearchHacking\Lib\Ghdb\Engineers\Bing;
use Aszone\Component\SearchHacking\Lib\Ghdb\Engineers\Yandex;
use Aszone\Component\SearchHacking\Lib\Ghdb\Engineers\Yahoo;


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
        $google = new Google($this->commandData);
        if($google->error)
        {
            return $google;
        }
        return $google->run();

	}

    public function runGoogleApi()
    {
        $googleApi = new GoogleApi($this->commandData);
        if($googleApi->error)
        {
            return $googleApi;
        }
        return $googleApi->run();
    }

    public function runBing()
    {
        $bing = new Bing($this->commandData);
        if($bing->error)
        {
            return $bing;
        }
        return $bing->run();
    }

    public function runYandex()
    {
        $yandex = new Yandex($this->commandData);
        if($yandex->error)
        {
            return $yandex;
        }
        return $yandex->run();
    }

    public function runYahoo()
    {
        $yahoo = new Yahoo ($this->commandData);
        if($yahoo->error)
        {
            return $yahoo;
        }
        return $yahoo->run();
    }


    public function runDuckduckGo(){
        //https://api.duckduckgo.com/html/?q=[DORK]&kl=en-us&p=-1&s=[PAG]&dc=[PAG3]&o=json&api=d.js
    }



}