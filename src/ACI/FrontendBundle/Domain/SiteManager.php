<?php
namespace ACI\FrontendBundle\Domain;

class SiteManager
{
    private $currentSite;
 
    public function getCurrentSite()
    {
        return $this->currentSite;
    }
 
    public function setCurrentSite($currentSite)
    {
        $this->currentSite = $currentSite;
    }
}
