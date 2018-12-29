<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoDeclaracionJuradaIIBBContribuyente
 *
 * @author Darío Rapetti
 * created 23/04/2015
 * 
 * @ORM\Table(name="orden_pago_declaracion_jurada_iibb_contribuyente")
 * @ORM\Entity
 */
class OrdenPagoDeclaracionJuradaIIBBContribuyente extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\DeclaracionJuradaIIBBContribuyente
     *
     * @ORM\OneToOne(targetEntity="DeclaracionJuradaIIBBContribuyente", inversedBy="ordenPago", cascade={"all"})
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=false)
     * 
     */
    protected $declaracionJuradaIIBBContribuyente;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set declaracionJuradaIIBBContribuyente
     *
     * @param \ADIF\ContableBundle\Entity\DeclaracionJuradaIIBBContribuyente $declaracionJurada
     * @return OrdenPagoDeclaracionJurada
     */
    public function setDeclaracionJuradaIIBBContribuyente(\ADIF\ContableBundle\Entity\DeclaracionJuradaIIBBContribuyente $declaracionJurada = null) {
        $this->declaracionJuradaIIBBContribuyente = $declaracionJurada;

        return $this;
    }

    /**
     * Get declaracionJuradaIIBBContribuyente
     *
     * @return \ADIF\ContableBundle\Entity\DeclaracionJuradaIIBBContribuyente
     */
    public function getDeclaracionJuradaIIBBContribuyente() {
        return $this->declaracionJuradaIIBBContribuyente;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoDeclaracionJuradaImpuesto
     */
    public function setImporte($importe) {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string 
     */
    public function getImporte() {
        return $this->importe;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagodeclaracionjuradaiibbcontribuyente';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontabledeclaracionjuradaiibbcontribuyente';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return "ADIF";
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return "30-71069599-3";
    }    

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        return new ArrayCollection();
    }

    /**
     * Get totalBruto
     *
     * @return double
     */
    public function getTotalBruto() {
        return $this->getImporte();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return new AdifDatos();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoDeclaracionJuradaIIBBContribuyenteController();
    }
}
