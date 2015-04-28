<?php

namespace ACI\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PHPExcel;
use PHPExcel_IOFactory;
use ACI\BackendBundle\Entity\Country;

/**
 * @Route("/admin")
 */
class BackendController extends Controller {

    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Template()
     */
    public function dashboardAction() {
        return array();
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {

    }

    /**
     * @Route("/crawlerdata", name="admin_data")
     * @Template()
     */
    public function crawlerCountryAction() {
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Company')->findAll();
        foreach ($entities as $entity) {
            $this->container->get('aci_app.crawler.sec')->parseAddressData($entity->getCompleteCik());
        }
    }

    /**
     * @Route("/importcountry", name="admin_importcountry")
     * @Template()
     */
    public function importCountryAction() {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('BackendBundle:Country');
        $file = $this->get('kernel')->getRootDir() . "/../data/edgar_state_country.xlsx";
        if (!file_exists($file)) {
            exit("El archivo no existe.");
        }
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $columnA = 'A';
            $columnB = 'B';
            $counter = 0;
            $lastRow = $worksheet->getHighestRow();
            for ($row = 2; $row <= $lastRow; $row++) {
                $cellA = $worksheet->getCell($columnA . $row);
                $cellB = $worksheet->getCell($columnB . $row);

                $country = $country = new Country();
                $country->setName($cellB->getValue());
                $country->setCode($cellA->getValue());
                $em->persist($country);
                $counter++;
            }

            try {
                $em->flush();
                echo "Se procesaron " . $counter . " ciudades";
                return new \Symfony\Component\HttpFoundation\Response("!Listo");
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }


        // Echo memory peak usage
    }

    /**
     * @Route("/importindustry", name="admin_importindustry")
     * @Template()
     */
    public function importIndustryAction() {
        $em = $this->getDoctrine()->getManager();
        $file = $this->get('kernel')->getRootDir() . "/../data/sic_naics.xlsx";
        if (!file_exists($file)) {
            exit("El archivo no existe.");
        }
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $columnA = 'A';
            $columnB = 'B';
            $columnC = 'C';
            $columnD = 'D';
            $counter = 0;
            $lastRow = $worksheet->getHighestRow();
            for ($row = 2; $row <= $lastRow; $row++) {
                $cellA = $worksheet->getCell($columnA . $row);
                $cellB = $worksheet->getCell($columnB . $row);
                $cellC = $worksheet->getCell($columnC . $row);
                $cellD = $worksheet->getCell($columnD . $row);

                $industry = $country = new \ACI\BackendBundle\Entity\Industry();

                $industry->setSic($cellA->getValue());
                $industry->setName($cellB->getValue());
                $industry->setNaics($cellC->getValue());
                $industry->setNaicsClasification($cellD->getValue());

                $em->persist($industry);
                $counter++;
            }

            try {
                $em->flush();
                echo "Se procesaron " . $counter . " industrias.";
                return new \Symfony\Component\HttpFoundation\Response("!Listo");
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }


        // Echo memory peak usage
    }

    /**
     * @Route("/importcompany", name="admin_importcompany")
     * @Template()
     */
    public function importCompanyAction() {
        $em = $this->getDoctrine()->getManager();
        $file = $this->get('kernel')->getRootDir() . "/../data/cik_ticker.xlsx";
        if (!file_exists($file)) {
            exit("El archivo no existe.");
        }
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $columnA = 'A';
            $columnB = 'B';
            $columnC = 'C';
            $columnD = 'D';
            $columnE = 'E';
            $columnH = 'H';
            $counter = 0;
            $lastRow = $worksheet->getHighestRow();
            for ($row = 2; $row <= $lastRow; $row++) {
                $cellA = $worksheet->getCell($columnA . $row);
                $cellB = $worksheet->getCell($columnB . $row);
                $cellC = $worksheet->getCell($columnC . $row);
                $cellD = $worksheet->getCell($columnD . $row);
                $cellE = $worksheet->getCell($columnE . $row);
                $cellH = $worksheet->getCell($columnH . $row);

                $company = new \ACI\BackendBundle\Entity\Company();
                $company->setCik($cellA->getValue());
                $company->setName($cellC->getValue());
                $company->setTicker($cellB->getValue());
                $company->setSic($cellE->getValue());
                $company->setIrsNumber($cellH->getValue());
                $company->setExchange($cellD->getValue());
                $em->persist($company);
                $counter++;
            }

            try {
                $em->flush();
                echo "Se procesaron " . $counter . " compañias.";
                return new \Symfony\Component\HttpFoundation\Response("!Listo");
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

}
