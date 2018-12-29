<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoDeclaracionJuradaIvaContribuyente
 *
 * @author Darío Rapetti
 * created 23/04/2015
 * 
 * @ORM\Table(name="orden_pago_declaracion_jurada_iva_contribuyente")
 * @ORM\Entity
 */
class OrdenPagoDeclaracionJuradaIvaContribuyente extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente
     *
     * @ORM\OneToOne(targetEntity="DeclaracionJuradaIvaContribuyente", inversedBy="ordenPago", cascade={"all"})
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=false)
     * 
     */
    protected $declaracionJuradaIvaContribuyente;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set declaracionJuradaIvaContribuyente
     *
     * @param \ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente $declaracionJurada
     * @return OrdenPagoDeclaracionJurada
     */
    public function setDeclaracionJuradaIvaContribuyente(\ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente $declaracionJurada = null) {
        $this->declaracionJuradaIvaContribuyente = $declaracionJurada;

        return $this;
    }

    /**
     * Get declaracionJuradaIvaContribuyente
     *
     * @return \ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente
     */
    public function getDeclaracionJuradaIvaContribuyente() {
        return $this->declaracionJuradaIvaContribuyente;
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
        return 'ordenpagodeclaracionjuradaivacontribuyente';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontabledeclaracionjuradaivacontribuyente';
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
        return new \ADIF\ContableBundle\Controller\OrdenPagoDeclaracionJuradaIvaContribuyenteController();
    }
}
