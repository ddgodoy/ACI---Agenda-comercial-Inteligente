<?php

namespace ACI\BackendBundle\Services;

use Symfony\Component\DomCrawler\Crawler;

/**
 *
 */
class RankandfiledService {

    private $urltickers = "http://rankandfiled.com/#/data/tickers";
    private $urlindustries = "http://rankandfiled.com/#/data/industries";
    private $urlcountries = "http://rankandfiled.com/#/data/countries";

    public function parseHtmlCountries() {


        $crawler = new Crawler(file_get_contents($this->urlcountries));

        $link = $crawler->extract(array('span'));

        echo "<pre>";
        print_r($link);


        echo $this->urlcountries;
        die;
    }

}
