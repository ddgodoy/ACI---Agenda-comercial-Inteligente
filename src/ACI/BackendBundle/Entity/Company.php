<?php

namespace ACI\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ACI\BackendBundle\Entity\Company
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="ACI\BackendBundle\Repository\CompanyRepository")
 */
class Company {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string $cik
     * @ORM\Column(name="cik", type="string", length=10, nullable=false)
     */
    private $cik;

    /**
     * @var string $ticker
     * @ORM\Column(name="ticker", type="string", length=50, nullable=false)
     */
    private $ticker;

    /**
     * @var string $irs_number
     * @ORM\Column(name="irs_number", type="string", length=50, nullable=false)
     */
    private $irs_number;

    /**
     * @var string $sic
     * @ORM\Column(name="sic", type="string", length=50, nullable=false)
     */
    private $sic;

    public function __construct() {

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Company
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }


    /**
     * Set cik
     *
     * @param string $cik
     * @return Company
     */
    public function setCik($cik)
    {
        $this->cik = $cik;
    
        return $this;
    }

    /**
     * Get cik
     *
     * @return string 
     */
    public function getCik()
    {
        return $this->cik;
    }

    /**
     * Set ticker
     *
     * @param string $ticker
     * @return Company
     */
    public function setTicker($ticker)
    {
        $this->ticker = $ticker;
    
        return $this;
    }

    /**
     * Get ticker
     *
     * @return string 
     */
    public function getTicker()
    {
        return $this->ticker;
    }

    /**
     * Set irs_number
     *
     * @param string $irsNumber
     * @return Company
     */
    public function setIrsNumber($irsNumber)
    {
        $this->irs_number = $irsNumber;
    
        return $this;
    }

    /**
     * Get irs_number
     *
     * @return string 
     */
    public function getIrsNumber()
    {
        return $this->irs_number;
    }

    /**
     * Set sic
     *
     * @param string $sic
     * @return Company
     */
    public function setSic($sic)
    {
        $this->sic = $sic;
    
        return $this;
    }

    /**
     * Get sic
     *
     * @return string 
     */
    public function getSic()
    {
        return $this->sic;
    }
}