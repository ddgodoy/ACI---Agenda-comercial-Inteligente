<?php

namespace ACI\BackendBundle\Services;

use Symfony\Component\DomCrawler\Crawler;

/**
 *
 */
class SECService {

    public function parseAddressData($cik) {
        $url = "http://www.sec.gov/cgi-bin/browse-edgar?action=getcompany&CIK=" . $cik . "&owner=exclude&count=40&hidefilings=0";
        if ($stream = @file_get_contents($url)) {
            try {
                $crawler = new Crawler($stream);
                $link = $crawler->filter('.mailer');
                $mailing = $link->getNode(0)->textContent;
                $bussiness = $link->getNode(1)->textContent;

                echo $mailing . "<br>";
                echo $bussiness;
            } catch (Exception $e) {

            }
        }
    }

}
