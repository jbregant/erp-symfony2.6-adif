<?php

namespace ADIF\RecursosHumanosBundle\Entity\Consultoria;

use ADIF\ComprasBundle\Entity\CodigoAutorizacionImpresion;
use Doctrine\ORM\Mapping as ORM;

/**
 * CodigoAutorizacionImpresionConsultor
 *
 * @author Manuel Becerra
 * created 06/04/2015
 * 
 * @ORM\Table(name="codigo_autorizacion_impresion_consultor")
 * @ORM\Entity
 */
class CodigoAutorizacionImpresionConsultor extends CodigoAutorizacionImpresion {

    /**
     * @var Consultor
     *
     * @ORM\ManyToOne(targetEntity="Consultor", inversedBy="cais")
     * @ORM\JoinColumn(name="id_consultor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $consultor;

    /**
     * @var \integer
     *
     * @ORM\Column(name="punto_venta", type="string", length=4, nullable=false)
     */
    protected $puntoVenta;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set consultor
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor $consultor
     * @return CodigoAutorizacionImpresionConsultor
     */
    public function setConsultor(\ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor $consultor) {
        $this->consultor = $consultor;

        return $this;
    }

    /**
     * Get consultor
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor 
     */
    public function getConsultor() {
        return $this->consultor;
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return CodigoAutorizacionImpresionConsultor
     */
    public function setPuntoVenta($puntoVenta) {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return string 
     */
    public function getPuntoVenta() {
        return $this->puntoVenta;
    }

}
