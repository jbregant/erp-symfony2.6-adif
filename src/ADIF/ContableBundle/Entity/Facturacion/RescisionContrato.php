<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RescisionContrato
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="rescision_contrato")
 * @ORM\Entity
 */
class RescisionContrato extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Contrato
     *
     * @ORM\ManyToOne(targetEntity="Contrato")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=false)
     */
    protected $contrato;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_rescision", type="datetime", nullable=false)
     */
    protected $fechaRescision;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\Contrato $contrato
     * @return RescisionContrato
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Facturacion\Contrato $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\Contrato 
     */
    public function getContrato() {
        return $this->contrato;
    }

    /**
     * Set fechaRescision
     *
     * @param \DateTime $fechaRescision
     * @return RescisionContrato
     */
    public function setFechaRescision($fechaRescision) {
        $this->fechaRescision = $fechaRescision;

        return $this;
    }

    /**
     * Get fechaRescision
     *
     * @return \DateTime 
     */
    public function getFechaRescision() {
        return $this->fechaRescision;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return RescisionContrato
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

}
