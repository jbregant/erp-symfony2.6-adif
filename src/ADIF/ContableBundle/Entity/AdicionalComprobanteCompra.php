<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ComprasBundle\Entity\Adicional;
use ADIF\ComprasBundle\Entity\TipoAdicional;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdicionalComprobanteCompra 
 * 
 * Indica el adicional que se establece en un comprobante de compra. 
 * 
 * 
 * @author Darío Rapetti
 * created 10/12/2014
 * 
 * @ORM\Table(name="adicional_comprobante_compra")
 * @ORM\Entity
 */
class AdicionalComprobanteCompra extends Adicional {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ComprobanteCompra
     *
     * @ORM\ManyToOne(targetEntity="ComprobanteCompra", inversedBy="adicionales")
     * @ORM\JoinColumn(name="id_comprobante_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $comprobanteCompra;

    /**
     * @ORM\Column(name="id_tipo_adicional", type="integer", nullable=false)
     */
    protected $idTipoAdicional;

    /**
     * @var TipoAdicional
     */
    protected $tipoAdicional;

    /**
     * @ORM\Column(name="id_adicional_cotizacion", type="integer", nullable=true)
     */
    protected $idAdicionalCotizacion;

    /**
     * @var ADIF\ComprasBundle\Entity\AdicionalCotizacion
     */
    protected $adicionalCotizacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AlicuotaIva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_alicuota_iva", referencedColumnName="id", nullable=false)
     * })
     */
    protected $alicuotaIva;

    /**
     * @var double
     * @ORM\Column(name="monto_iva", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    protected $montoIva;

    /**
     * @var double
     * @ORM\Column(name="monto_neto", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    protected $montoNeto;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set comprobanteCompra
     *
     * @param ComprobanteCompra $comprobanteCompra
     * @return AdicionalComprobanteCompra
     */
    public function setComprobanteCompra(ComprobanteCompra $comprobanteCompra) {
        $this->comprobanteCompra = $comprobanteCompra;

        return $this;
    }

    /**
     * Get comprobanteCompra
     *
     * @return ComprobanteCompra
     */
    public function getComprobanteCompra() {
        return $this->comprobanteCompra;
    }

    /**
     * Get idTipoAdicional
     *
     * @return integer 
     */
    public function getIdTipoAdicional() {
        return $this->idTipoAdicional;
    }

    /**
     * 
     * @param type $idTipoAdicional
     */
    public function setIdTipoAdicional($idTipoAdicional) {
        $this->idTipoAdicional = $idTipoAdicional;
    }

    /**
     * 
     * @param TipoAdicional $tipoAdicional
     */
    public function setTipoAdicional($tipoAdicional) {

        if (null != $tipoAdicional) {
            $this->idTipoAdicional = $tipoAdicional->getId();
        } //.
        else {
            $this->idTipoAdicional = null;
        }

        $this->tipoAdicional = $tipoAdicional;
    }

    /**
     * 
     * @return type
     */
    public function getTipoAdicional() {
        return $this->tipoAdicional;
    }

    /**
     * Set idAdicionalCotizacion
     *
     * @param integer $idAdicionalCotizacion
     * @return AdicionalComprobanteCompra
     */
    public function setIdAdicionalCotizacion($idAdicionalCotizacion) {
        $this->idAdicionalCotizacion = $idAdicionalCotizacion;

        return $this;
    }

    /**
     * Get idAdicionalCotizacion
     *
     * @return integer 
     */
    public function getIdAdicionalCotizacion() {
        return $this->idAdicionalCotizacion;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\AdicionalCotizacion $adicionalCotizacion
     */
    public function setAdicionalCotizacion($adicionalCotizacion) {

        if (null != $adicionalCotizacion) {
            $this->idAdicionalCotizacion = $adicionalCotizacion->getId();
        } //.
        else {
            $this->idAdicionalCotizacion = null;
        }

        $this->adicionalCotizacion = $adicionalCotizacion;
    }

    /**
     * 
     * @return type
     */
    public function getAdicionalCotizacion() {
        return $this->adicionalCotizacion;
    }

    /**
     * Set alicuotaIva
     *
     * @param AlicuotaIva $alicuotaIva
     * @return AdicionalComprobanteCompra
     */
    public function setAlicuotaIva(AlicuotaIva $alicuotaIva) {
        $this->alicuotaIva = $alicuotaIva;

        return $this;
    }

    public function getIdAlicuotaIva() {
        return $this->alicuotaIva;
    }

    public function setIdAlicuotaIva(AlicuotaIva $alicuotaIva) {
        $this->alicuotaIva = $alicuotaIva;
        return $this;
    }

    /**
     * Get alicuotaIva
     *
     * @return AlicuotaIva 
     */
    public function getAlicuotaIva() {
        return $this->alicuotaIva;
    }

    /**
     * Set montoIva
     *
     * @param double $montoIva
     * @return AdicionalComprobanteCompra
     */
    public function setMontoIva($montoIva) {
        $this->montoIva = $montoIva;

        return $this;
    }

    /**
     * Get montoIva
     *
     * @return double
     */
    public function getMontoIva() {
        return $this->montoIva;
    }

    /**
     * Set montoNeto
     *
     * @param double $montoNeto
     * @return AdicionalComprobanteCompra
     */
    public function setMontoNeto($montoNeto) {
        $this->montoNeto = $montoNeto;
        return $this;
    }

    /**
     * Get montoNeto
     *
     * @return double
     */
    public function getMontoNeto() {
        return $this->montoNeto;
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

    public function getMontoNetoMasIva() {
        return $this->getMontoNeto() + $this->getMontoIva();
    }

    /**
     * 
     * @return type
     */
    public function getPorcentajeIvaReal() {
        if ($this->getMontoNeto() != 0) {
            return $this->getMontoIva() / $this->getMontoNeto() * 100;
        }
        return 0;
    }

}
