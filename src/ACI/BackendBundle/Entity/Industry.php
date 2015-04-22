<?php

namespace ACI\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ACI\BackendBundle\Entity\Industry
 * @ORM\Table(name="industry")
 * @ORM\Entity(repositoryClass="ACI\BackendBundle\Repository\IndustryRepository")
 */
class Industry {

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
     * @ORM\Column(name="name", type="string", length=512, nullable=false)
     */
    private $name;

    /**
     * @var string $sic
     * @ORM\Column(name="sic", type="string", length=255, nullable=false)
     */
    private $sic;

    /**
     * @var string $naics
     * @ORM\Column(name="naics", type="string", length=255, nullable=true)
     */
    private $naics;

    /**
     * @var string $naics_clasification
     * @ORM\Column(name="naics_clasification", type="string", length=512, nullable=true)
     */
    private $naics_clasification;

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
     * @return Industry
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
     * Set sic
     *
     * @param string $sic
     * @return Industry
     */
    public function setSic($sic) {
        $this->sic = $sic;

        return $this;
    }

    /**
     * Get sic
     *
     * @return string
     */
    public function getSic() {
        return $this->sic;
    }

    /**
     * Set naics
     *
     * @param string $naics
     * @return Industry
     */
    public function setNaics($naics) {
        $this->naics = $naics;

        return $this;
    }

    /**
     * Get naics
     *
     * @return string
     */
    public function getNaics() {
        return $this->naics;
    }

    /**
     * Set naics_clasification
     *
     * @param string $naicsClasification
     * @return Industry
     */
    public function setNaicsClasification($naicsClasification) {
        $this->naics_clasification = $naicsClasification;

        return $this;
    }

    /**
     * Get naics_clasification
     *
     * @return string
     */
    public function getNaicsClasification() {
        return $this->naics_clasification;
    }

}
