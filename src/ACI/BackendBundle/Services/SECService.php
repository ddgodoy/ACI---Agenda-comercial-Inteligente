<?php

namespace ACI\BackendBundle\Services;

use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManager;

/**
 *
 */
class SECService {

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    public function parseAddressData($entity) {
        $url = "http://www.sec.gov/cgi-bin/browse-edgar?action=getcompany&CIK=" . $entity->getCompleteCik() . "&owner=exclude&count=40&hidefilings=0";
        if ($stream = @file_get_contents($url)) {
            try {
                $crawler = new Crawler($stream);
                $link = $crawler->filter('.mailer');
                $mailing = $link->getNode(0)->textContent;
                $bussiness = $link->getNode(1)->textContent;

                $mailing = str_replace("Mailing Address", "", $mailing);
                $bussiness = str_replace("Business Address", "", $bussiness);
                $entity->setMailingAddress($mailing);
                $entity->setBusinessAddress($bussiness);
                $this->em->persist($entity);
            } catch (Exception $e) {

            }
        }
    }

    public function parseTypeData($entity) {
        //$url = "https://www.sec.gov/Archives/edgar/data/1545654/000154565415000005/R4.htm";
        $url = "http://www.sec.gov/Archives/edgar/data/" . $entity->getCik() . "/" . $entity->getCompleteCik() . "15000005/R4.htm";
        $url2 = "http://www.sec.gov/Archives/edgar/data/" . $entity->getCik() . "/" . $entity->getCompleteCik() . "14000045/R5.htm";
        $url3 = "http://www.sec.gov/Archives/edgar/data/" . $entity->getCik() . "/" . $entity->getCompleteCik() . "11000013/R2.xml";
        //$url = "http://www.sec.gov/cgi-bin/browse-edgar?action=getcompany&CIK=" . $entity->getCompleteCik() . "&owner=exclude&count=40&hidefilings=0";


        if ($stream = @file_get_contents($url)) {
            try {
                $crawler = new Crawler($stream);
                $link = $crawler->filter('table')->extract(array('_text'));
                echo "<pre>";
                print_r($link);
            } catch (Exception $e) {

            }
        }
        if ($stream = @file_get_contents($url2)) {
            try {
                $crawler = new Crawler($stream);
                $link = $crawler->filter('table')->extract(array('_text'));
                echo "<pre>";
                print_r($link);
            } catch (Exception $e) {

            }
        }
        if ($stream = @file_get_contents($url3)) {
            try {
                $crawler = new Crawler($stream);
                $link = $crawler->filter('table')->extract(array('_text'));
                echo "<pre>";
                print_r($link);
            } catch (Exception $e) {

            }
        }
    }

}
