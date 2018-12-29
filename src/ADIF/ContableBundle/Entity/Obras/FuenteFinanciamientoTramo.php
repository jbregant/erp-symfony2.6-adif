<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FuenteFinanciamientoTramo
 * 
 * @ORM\Table(name="fuente_financiamiento_tramo")
 * @ORM\Entity 
 */
class FuenteFinanciamientoTramo extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tramo", inversedBy="fuentesFinanciamiento")
     * @ORM\JoinColumn(name="id_tramo", referencedColumnName="id", nullable=false)
     */
    protected $tramo;

    /**
     * @var FuenteFinanciamiento
     *
     * @ORM\ManyToOne(targetEntity="FuenteFinanciamiento")
     * @ORM\JoinColumn(name="id_fuente_financiamiento", referencedColumnName="id", nullable=false)
     * 
     */
    protected $fuenteFinanciamiento;

    /**
     * @var double
     * @ORM\Column(name="porcentaje", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentaje;

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->fuenteFinanciamiento;
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
     * Set tramo
     *
     * @param \ADIF\ContableBundle\Entity\Obras\Tramo $tramo
     *
     * @return FuenteFinanciamientoTramo
     */
    public function setTramo(\ADIF\ContableBundle\Entity\Obras\Tramo $tramo) {
        $this->tramo = $tramo;

        return $this;
    }

    /**
     * Get tramo
     *
     * @return \ADIF\ContableBundle\Entity\Obras\Tramo
     */
    public function getTramo() {
        return $this->tramo;
    }

    /**
     * Set fuenteFinanciamiento
     *
     * @param \ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento $fuenteFinanciamiento
     *
     * @return FuenteFinanciamientoTramo
     */
    public function setFuenteFinanciamiento(\ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento $fuenteFinanciamiento) {
        $this->fuenteFinanciamiento = $fuenteFinanciamiento;

        return $this;
    }

    /**
     * Get fuenteFinanciamiento
     *
     * @return \ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento
     */
    public function getFuenteFinanciamiento() {
        return $this->fuenteFinanciamiento;
    }

    /**
     * Set porcentaje
     *
     * @param string $porcentaje
     *
     * @return FuenteFinanciamientoTramo
     */
    public function setPorcentaje($porcentaje) {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return string
     */
    public function getPorcentaje() {
        return $this->porcentaje;
    }

}
