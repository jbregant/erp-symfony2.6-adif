<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RenglonComprobanteCompra
 *
 * @author Darío Rapetti
 * created 22/10/2014
 * 
 * @ORM\Table(name="renglon_comprobante_compra")
 * @ORM\Entity 
 */
class RenglonComprobanteCompra extends RenglonComprobante {

    /**
     * @ORM\Column(name="id_renglon_orden_compra", type="integer", nullable=true)
     */
    protected $idRenglonOrdenCompra;

    /**
     * @var ADIF\ComprasBundle\Entity\RenglonOrdenCompra
     */
    protected $renglonOrdenCompra;

    /**
     * @ORM\Column(name="id_bien_economico", type="integer", nullable=false)
     */
    protected $idBienEconomico;

    /**
     * @var ADIF\ComprasBundle\Entity\BienEconomico
     */
    protected $bienEconomico;

    /**
     * @var RenglonComprobanteCompraCentrosDeCosto
     * 
     * @ORM\OneToMany(targetEntity="RenglonComprobanteCompraCentroDeCosto", mappedBy="renglonComprobanteCompra", cascade={"all"})
     * 
     */
    protected $renglonComprobanteCompraCentrosDeCosto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_devolucion", type="boolean", nullable=false)
     */
    protected $esDevolucion;

    /**
     * 
     */
    public function __construct() {

        parent::__construct();

        $this->esDevolucion = false;

        $this->renglonComprobanteCompraCentrosDeCosto = new ArrayCollection();
    }

    /**
     * Set idRenglonOrdenCompra
     *
     * @param integer $idRenglonOrdenCompra
     * @return RenglonComprobanteCompra
     */
    public function setIdRenglonOrdenCompra($idRenglonOrdenCompra) {
        $this->idRenglonOrdenCompra = $idRenglonOrdenCompra;

        return $this;
    }

    /**
     * Get idRenglonOrdenCompra
     *
     * @return integer 
     */
    public function getIdRenglonOrdenCompra() {
        return $this->idRenglonOrdenCompra;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglonOrdenCompra
     */
    public function setRenglonOrdenCompra($renglonOrdenCompra) {

        if (null != $renglonOrdenCompra) {
            $this->idRenglonOrdenCompra = $renglonOrdenCompra->getId();
        } //.
        else {
            $this->idRenglonOrdenCompra = null;
        }

        $this->renglonOrdenCompra = $renglonOrdenCompra;
    }

    /**
     * 
     * @return type
     */
    public function getRenglonOrdenCompra() {
        return $this->renglonOrdenCompra;
    }

    /**
     * Retorna el monto del renglón mas los adicionales aplicados a ese renglón
     * @return float Monto
     */
    public function getPrecioConAdicionales() {
        return $this->getMontoNetoBonificado() + $this->getMontoIvaBonificado();
    }

    /**
     * 
     * @param type $incluirSoloAdicionales
     * @return type
     */
    public function getPrecioTotalProrrateado($incluirSoloAdicionales = false) {
        return $this->getPrecioTotal() + $this->getComprobante()->getMontoAdicionalProrrateado($this, $incluirSoloAdicionales);
    }

    /**
     * 
     * @param type $incluirSoloAdicionales
     * @return type
     */
    public function getPrecioNetoTotalProrrateado($incluirSoloAdicionales = false) {
        return $this->getMontoNeto() + $this->getComprobante()->getMontoAdicionalProrrateado($this, $incluirSoloAdicionales);
    }

    /**
     * Get precioTotal
     *
     * @return float 
     */
    public function getPrecioTotal() {

        return $this->getMontoNeto() + $this->getMontoIva();
    }

    /**
     * Set idBienEconomico
     *
     * @param integer $idBienEconomico
     * @return RenglonComprobanteCompra
     */
    public function setIdBienEconomico($idBienEconomico) {
        $this->idBienEconomico = $idBienEconomico;

        return $this;
    }

    /**
     * Get idBienEconomico
     *
     * @return integer 
     */
    public function getIdBienEconomico() {
        return $this->idBienEconomico;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico
     */
    public function setBienEconomico($bienEconomico) {

        if (null != $bienEconomico) {
            $this->idBienEconomico = $bienEconomico->getId();
        } //.
        else {
            $this->idBienEconomico = null;
        }

        $this->bienEconomico = $bienEconomico;
    }

    /**
     * 
     * @return type
     */
    public function getBienEconomico() {
        return $this->bienEconomico;
    }

    /**
     * Set esDevolucion
     *
     * @param boolean $esDevolucion
     * @return RenglonComprobanteCompra
     */
    public function setEsDevolucion($esDevolucion) {
        $this->esDevolucion = $esDevolucion;

        return $this;
    }

    /**
     * Get esDevolucion
     *
     * @return boolean 
     */
    public function getEsDevolucion() {
        return $this->esDevolucion;
    }

    /**
     * Add renglonComprobanteCompraCentrosDeCosto
     *
     * @param \ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto $renglonComprobanteCompraCentroDeCosto
     * @return RenglonComprobanteCompra
     */
    public function addRenglonComprobanteCompraCentrosDeCosto(\ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto $renglonComprobanteCompraCentroDeCosto) {
        $this->renglonComprobanteCompraCentrosDeCosto[] = $renglonComprobanteCompraCentroDeCosto;
        $renglonComprobanteCompraCentroDeCosto->setRenglonComprobanteCompra($this);
        return $this;
    }

    /**
     * Remove renglonComprobanteCompraCentrosDeCosto
     *
     * @param \ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto $renglonComprobanteCompraCentroDeCosto
     */
    public function removeRenglonComprobanteCompraCentrosDeCosto(\ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto $renglonComprobanteCompraCentroDeCosto) {
        $this->renglonComprobanteCompraCentrosDeCosto->removeElement($renglonComprobanteCompraCentroDeCosto);
        $renglonComprobanteCompraCentroDeCosto->setRenglonComprobanteCompra(null);
    }

    /**
     * Get adicionales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonComprobanteCompraCentrosDeCosto() {
        return $this->renglonComprobanteCompraCentrosDeCosto;
    }

}
