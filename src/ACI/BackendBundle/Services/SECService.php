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

                $mailing = str_replace("Mailing Address","", $mailing);
                $bussiness = str_replace("Business Address","", $bussiness);
                $entity->setMailingAddress($mailing);
                $entity->setBusinessAddress($bussiness);
                $this->em->persist($entity);
                
            } catch (Exception $e) {

            }
        }
    }

}
