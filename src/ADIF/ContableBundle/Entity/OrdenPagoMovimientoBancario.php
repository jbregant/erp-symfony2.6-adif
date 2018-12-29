<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenPagoMovimientoBancario
 *
 * @author Manuel Becerra
 * created 13/08/2015
 * 
 * @ORM\Table(name="orden_pago_movimiento_bancario")
 * @ORM\Entity
 */
class OrdenPagoMovimientoBancario extends OrdenPago {

    /**
     * @var MovimientoBancario
     *
     * @ORM\ManyToOne(targetEntity="MovimientoBancario", inversedBy="ordenesPago")
     * @ORM\JoinColumn(name="id_movimiento_bancario", referencedColumnName="id", nullable=false)
     */
    protected $movimientoBancario;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set movimientoBancario
     *
     * @param MovimientoBancario $movimientoBancario
     * @return OrdenPagoMovimientoBancario
     */
    public function setMovimientoBancario(MovimientoBancario $movimientoBancario) {
        $this->movimientoBancario = $movimientoBancario;

        return $this;
    }

    /**
     * Get movimientoBancario
     *
     * @return MovimientoBancario 
     */
    public function getMovimientoBancario() {
        return $this->movimientoBancario;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoMovimientoBancario
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
        return 'ordenpagomovimientobancario';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontablemovimientobancario';
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
        return $this->getMovimientoBancario()->getMonto();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return new AdifDatos();
    }

    /**
     * 
     * @return boolean
     */
    public function getRequiereVisado() {
        return false;
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoMovimientoBancarioController();
    }
}
