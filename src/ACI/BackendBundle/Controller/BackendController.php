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
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Company')->getMissingData();
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $this->container->get('aci_app.crawler.sec')->parseTypeData($entity);
        }
        $em->flush();
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

    /**
     * @Route("/parse10k", name="admin_parse10k")
     * @Template()
     */
    public function parse10kAction() {
        $em = $this->getDoctrine()->getManager();
        $file = $this->get('kernel')->getRootDir() . "/../data/form.idx";

        if (!file_exists($file)) {
            exit("El archivo no existe.");
        } else {
            $fh = fopen($file, 'r');

            $i = 0;
            while ($line = fgets($fh)) {

                $precik = explode("edgar/data/", $line);
                $cik = explode("/", $precik[1]);
                $company = $this->getDoctrine()->getRepository('BackendBundle:Company')->findOneBy(array("cik" => $cik[0]));

                if ($company) {
                    $lastpart = $cik[1];
                    $lastpart = str_replace("-", "", $lastpart);
                    $lastpart = str_replace(".txt", "", $lastpart);
                    $lastpart = trim($lastpart);

                    $excel_file = "ftp://ftp.sec.gov/edgar/data/" . $cik[0] . "/15/" . $lastpart . "/Financial_Report.xlsx";
                    if (!file_exists($excel_file)) {
                        echo ("El archivo no existe.");
                    } else {

                        $local_file = $this->get('kernel')->getRootDir() . "/../data/FinancialReport/" . $cik[0] . ".xlsx";
                        $server_file = "edgar/data/" . $cik[0] . "/15/" . $lastpart . "/Financial_Report.xlsx";


                        $handle = fopen($local_file, 'w');
                        $ftp_server = "ftp.sec.gov";
                        $conn_id = ftp_connect($ftp_server);
                        $login_result = ftp_login($conn_id, 'anonymous', '');
                        ftp_pasv($conn_id, true);
                        if ((!$conn_id) || (!$login_result)) {
                            echo "FTP connection has failed!";
                            echo "Attempted to connect to $ftp_server";
                            exit;
                        } else {
                            echo "Connected to $ftp_server";
                        }

                        $d = ftp_nb_fget($conn_id, $handle, $server_file, FTP_BINARY);

                        while ($d == FTP_MOREDATA) {
                            $d = ftp_nb_continue($conn_id);
                        }

                        if ($d != FTP_FINISHED) {
                            echo "Error downloading $server_file";
                            exit(1);
                        }


                        fclose($handle);
                        ftp_close($conn_id);

                        $objPHPExcel = PHPExcel_IOFactory::load($local_file);

                        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                            $columnA = 'A';
                            $columnB = 'B';
                            $columnC = 'C';
                            $columnD = 'D';
                            $columnE = 'E';
                            $columnH = 'H';
                            $lastRow = $worksheet->getHighestRow();
                            for ($row = 2; $row <= $lastRow; $row++) {
                                $cellA = $worksheet->getCell($columnA . $row);

                                if (strtolower($cellA) == "total currect assets") {
                                    $company->setTotalCurrentAssets($worksheet->getCell($columnB . $row));
                                }
                                if (strtolower($cellA) == "total assets") {
                                    $company->setTotalAssets($worksheet->getCell($columnB . $row));
                                }
                                if (strtolower($cellA) == "cash and cash equivalents") {
                                    $company->setCashAndCashEquivalents($worksheet->getCell($columnB . $row));
                                }
                                if (strtolower($cellA) == "long-term debt") {
                                    $company->setLongTermDebt($worksheet->getCell($columnB . $row));
                                }

                                $em->persist($company);
                            }
                        }
                    }
                    $em->flush();
                }
            }
            fclose($fh);

            die;
        }
    }

}
