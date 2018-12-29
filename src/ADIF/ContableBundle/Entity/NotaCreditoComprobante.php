<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoComprobante
 * 
 * @ORM\Table(name="nota_credito_comprobante")
 * @ORM\Entity
 * 
 */
class NotaCreditoComprobante {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \ADIF\ContableBundle\Entity\Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante", inversedBy="notasCredito")
     * @ORM\JoinColumn(name="id_nota_credito", referencedColumnName="id", nullable=false)
     * 
     */
    protected $notaCredito;

    /**
     * @var \ADIF\ContableBundle\Entity\Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante", inversedBy="comprobantesAcreditados")
     * @ORM\JoinColumn(name="id_comprobante", referencedColumnName="id", nullable=false)
     * 
     */
    protected $comprobante;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $monto;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return NotaCreditoComprobante
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set notaCredito
     *
     * @param \ADIF\ContableBundle\Entity\Comprobante $notaCredito
     * @return NotaCreditoComprobante
     */
    public function setNotaCredito(\ADIF\ContableBundle\Entity\Comprobante $notaCredito)
    {
        $this->notaCredito = $notaCredito;

        return $this;
    }

    /**
     * Get notaCredito
     *
     * @return \ADIF\ContableBundle\Entity\Comprobante 
     */
    public function getNotaCredito()
    {
        return $this->notaCredito;
    }

    /**
     * Set comprobante
     *
     * @param \ADIF\ContableBundle\Entity\Comprobante $comprobante
     * @return NotaCreditoComprobante
     */
    public function setComprobante(\ADIF\ContableBundle\Entity\Comprobante $comprobante)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \ADIF\ContableBundle\Entity\Comprobante 
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
}
