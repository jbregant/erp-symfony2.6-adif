<?php

namespace ADIF\ComprasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdicionalCotizacion 
 * 
 * Indica el adicional que se establece en una cotización. 
 * 
 * 
 * @author Carlos Sabena
 * created 15/07/2014
 * 
 * @ORM\Table(name="adicional_cotizacion")
 * @ORM\Entity
 */
class AdicionalCotizacion extends Adicional {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\TipoAdicional
     *
     * @ORM\ManyToOne(targetEntity="TipoAdicional")
     * @ORM\JoinColumn(name="id_tipo_adicional", referencedColumnName="id", nullable=false)
     * 
     */
    protected $tipoAdicional;

    /**
     * @ORM\Column(name="id_alicuota_iva", type="integer", nullable=true)
     */
    protected $idAlicuotaIva;

    /**
     * @var ADIF\ContableBundle\Entity\AlicuotaIva
     */
    protected $alicuotaIva;

    /**
     * @var \ADIF\ComprasBundle\Entity\Cotizacion
     *
     * @ORM\ManyToOne(targetEntity="Cotizacion", inversedBy="adicionalesCotizacion")
     * @ORM\JoinColumn(name="id_cotizacion", referencedColumnName="id", nullable=false)
     * 
     */
    protected $cotizacion;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoComparacionCotizacion
     *
     * @ORM\ManyToOne(targetEntity="EstadoComparacionCotizacion")
     * @ORM\JoinColumn(name="id_estado_comparacion_cotizacion", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoComparacionCotizacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adicional_elegido", type="boolean", nullable=false)
     */
    protected $adicionalElegido;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->adicionalElegido = false;
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
     * Set tipoAdicional
     *
     * @param \ADIF\ComprasBundle\Entity\TipoAdicional $tipoAdicional
     * @return AdicionalCotizacion
     */
    public function setTipoAdicional(\ADIF\ComprasBundle\Entity\TipoAdicional $tipoAdicional) {
        $this->tipoAdicional = $tipoAdicional;

        return $this;
    }

    /**
     * Get tipoAdicional
     *
     * @return \ADIF\ComprasBundle\Entity\TipoAdicional 
     */
    public function getTipoAdicional() {
        return $this->tipoAdicional;
    }

    /**
     * Get idAlicuotaIva
     *
     * @return integer 
     */
    public function getIdAlicuotaIva() {
        return $this->idAlicuotaIva;
    }

    /**
     * 
     * @param type $idAlicuotaIva
     */
    public function setIdAlicuotaIva($idAlicuotaIva) {
        $this->idAlicuotaIva = $idAlicuotaIva;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\AlicuotaIva $alicuotaIva
     */
    public function setAlicuotaIva($alicuotaIva) {

        if (null != $alicuotaIva) {
            $this->idAlicuotaIva = $alicuotaIva->getId();
        } //.
        else {
            $this->idAlicuotaIva = null;
        }

        $this->alicuotaIva = $alicuotaIva;
    }

    /**
     * 
     * @return type
     */
    public function getAlicuotaIva() {
        return $this->alicuotaIva;
    }

    /**
     * Set adicionalElegido
     *
     * @param boolean $adicionalElegido
     * @return AdicionalCotizacion
     */
    public function setAdicionalElegido($adicionalElegido) {
        $this->adicionalElegido = $adicionalElegido;

        return $this;
    }

    /**
     * Get adicionalElegido
     *
     * @return boolean 
     */
    public function getAdicionalElegido() {
        return $this->adicionalElegido;
    }

    /**
     * Set cotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\Cotizacion $cotizacion
     * @return AdicionalCotizacion
     */
    public function setCotizacion(\ADIF\ComprasBundle\Entity\Cotizacion $cotizacion) {
        $this->cotizacion = $cotizacion;

        return $this;
    }

    /**
     * Get cotizacion
     *
     * @return \ADIF\ComprasBundle\Entity\Cotizacion 
     */
    public function getCotizacion() {
        return $this->cotizacion;
    }

    /**
     * Set estadoComparacionCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoCotizacion $estadoComparacionCotizacion
     * @return AdicionalCotizacion
     */
    public function setEstadoComparacionCotizacion(\ADIF\ComprasBundle\Entity\EstadoComparacionCotizacion $estadoComparacionCotizacion) {
        $this->estadoComparacionCotizacion = $estadoComparacionCotizacion;

        return $this;
    }

    /**
     * Get estadoComparacionCotizacion
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoCotizacion 
     */
    public function getEstadoComparacionCotizacion() {
        return $this->estadoComparacionCotizacion;
    }

    /**
     * 
     * @return type
     */
    public function getPorcentajeIva() {
        if (null != $this->alicuotaIva) {
            return $this->alicuotaIva->getValor();
        }

        return null;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoNetoMasIva($enMCL = true) {

        return $this->getValor($enMCL) + $this->getMontoIva($enMCL);
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoIva($enMCL = true) {

        $montoIva = 0;

        if (null != $this->getPorcentajeIva()) {

            $montoIva = $this->getPorcentajeIva() * $this->getValor($enMCL) / 100;
        }

        return $montoIva;
    }

}
