<?php
namespace ACI\FrontendBundle\EventListener;

use ACI\FrontendBundle\Domain\SiteManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SubdomainListener {

    private $siteManager;
    private $doctrine;
    private $baseHost;

    public function __construct(SiteManager $siteManager, $doctrine, $baseHost="apptibase.com") {
        $this->siteManager = $siteManager;
        $this->doctrine = $doctrine;
        $this->baseHost = $baseHost;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $currentHost = $request->getHttpHost();
        $subdomain = str_replace('.' . $this->baseHost, '', $currentHost);
        $this->siteManager->setCurrentSite($subdomain);
    }

}
