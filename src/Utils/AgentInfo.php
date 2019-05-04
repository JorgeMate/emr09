<?php

namespace App\Utils;

use UserAgentParser\Exception\NoResultFoundException;
use UserAgentParser\Provider\WhichBrowser;

class AgentInfo
{
    private $provider;
    private $result;

    private $ai;

    public function check() {

        $this->provider = new WhichBrowser();

        try {
            /* @var $result \UserAgentParser\Model\UserAgent */
            //$this->result = $this->provider->parse('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36');

            $this->result = $this->provider->parse($_SERVER['HTTP_USER_AGENT']);
            
        } catch (NoResultFoundException $ex){
            // nothing found
        }

        if($this->result->isBot() === true) {
         // if one part has no result, it's always set not null
            $ai = $this->result->getBot()->getName();
            $ai .= ' ' . $this->result->getBot()->getType();
        } else {
         // if one part has no result, it's always set not null
            $ai = $this->result->getBrowser()->getName();
            $ai .= ' ' . $this->result->getBrowser()->getVersion()->getComplete();
              
            $ai .= ' ' . $this->result->getRenderingEngine()->getName();
            $ai .= ' ' . $this->result->getRenderingEngine()->getVersion()->getComplete();
              
            $ai .= ' ' . $this->result->getOperatingSystem()->getName();
            $ai .= ' ' . $this->result->getOperatingSystem()->getVersion()->getComplete();
              
            $ai .= ' ' . $this->result->getDevice()->getModel();
            $ai .= ' ' . $this->result->getDevice()->getBrand();
            $ai .= ' ' . $this->result->getDevice()->getType();
            $ai .= ' ' . $this->result->getDevice()->getIsMobile();
            $ai .= ' ' . $this->result->getDevice()->getIsTouch();
        }

     // var_dump($ai);die;
        return $ai;
    }
}


