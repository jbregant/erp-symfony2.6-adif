<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PagoACuenta
 * 
 * @author Darío Rapetti
 * created 17/04/2015
 * 
 * @ORM\Table(name="pago_a_cuenta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\PagoACuentaRepository")
 */
class PagoACuenta extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var EstadoPagoACuenta
     *
     * @ORM\ManyToOne(targetEntity="EstadoPagoACuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_pago_a_cuenta", referencedColumnName="id", nullable=false)
     * })
     */
    protected $estadoPagoACuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_declaracion_jurada", type="string", length=250, nullable=false)
     */
    protected $tipoDeclaracionJurada;

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoPagoACuenta", mappedBy="pagoACuenta")
     * */
    protected $ordenPago;

    /**
     * @ORM\ManyToMany(targetEntity="RenglonDeclaracionJurada", inversedBy="pagosACuenta")
     * @ORM\JoinTable(name="pago_a_cuenta_renglon_declaracion_jurada")
     * */
    protected $renglonesDeclaracionJurada;

    /**
     * Constructor
     */
    public function __construct() {
        $this->renglonesDeclaracionJurada = new ArrayCollection();
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
     * Set estadoPagoACuenta
     *
     * @param EstadoPagoACuenta $estadoPagoACuenta
     * @return PagoACuenta
     */
    public function setEstadoPagoACuenta(EstadoPagoACuenta $estadoPagoACuenta) {
        $this->estadoPagoACuenta = $estadoPagoACuenta;

        return $this;
    }

    /**
     * Get estadoPagoACuenta
     *
     * @return EstadoPagoACuenta 
     */
    public function getEstadoPagoACuenta() {
        return $this->estadoPagoACuenta;
    }

    /**
     * Set ordenPago
     *
     * @param OrdenPagoPagoACuenta $ordenPago
     * @return PagoACuenta
     */
    public function setOrdenPago(OrdenPagoPagoACuenta $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return OrdenPagoPagoACuenta 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set tipoDeclaracionJurada
     *
     * @param string
     * @return PagoACuenta
     */
    public function setTipoDeclaracionJurada($tipoDeclaracionJurada) {
        $this->tipoDeclaracionJurada = $tipoDeclaracionJurada;

        return $this;
    }

    /**
     * Get tipoDeclaracionJurada
     *
     * @return string
     */
    public function getTipoDeclaracionJurada() {
        return $this->tipoDeclaracionJurada;
    }

    /**
     * Add renglonesDeclaracionJurada
     *
     * @param RenglonDeclaracionJurada $renglonesDeclaracionJurada
     * @return PagoACuenta
     */
    public function addRenglonesDeclaracionJurada(RenglonDeclaracionJurada $renglonesDeclaracionJurada) {
        $this->renglonesDeclaracionJurada[] = $renglonesDeclaracionJurada;

        return $this;
    }

    /**
     * Remove renglonesDeclaracionJurada
     *
     * @param RenglonDeclaracionJurada $renglonesDeclaracionJurada
     */
    public function removeRenglonesDeclaracionJurada(RenglonDeclaracionJurada $renglonesDeclaracionJurada) {
        $this->renglonesDeclaracionJurada->removeElement($renglonesDeclaracionJurada);
    }

    /**
     * Get renglonesDeclaracionJurada
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesDeclaracionJurada() {
        return $this->renglonesDeclaracionJurada;
    }

    /**
     * Get importe
     * 
     * @return double
     */
    public function getImporte() {

        $importe = 0;

        foreach ($this->renglonesDeclaracionJurada as $renglonDeclaracionJurada) {
            $importe += $renglonDeclaracionJurada->getMonto();
        }

        return $importe;
    }

    /**
     * 
     * @return type
     */
    public function getFechaPeriodo() {

        $fechaSuperior = null;

        foreach ($this->renglonesDeclaracionJurada as $renglonDeclaracionJurada) {

            /* @var $renglonDeclaracionJurada RenglonDeclaracionJurada */

            $fechaRenglon = $renglonDeclaracionJurada->getFecha();

            $fechaSuperior = $fechaSuperior == null //
                    ? $fechaRenglon //
                    : ($fechaRenglon > $fechaSuperior ? $fechaRenglon : $fechaSuperior);
        }

        return $fechaSuperior;
    }

    /**
     * 
     * @return type
     */
    public function getPeriodo() {

        setlocale(LC_ALL,"es_AR.UTF-8");

        return $this->getFechaPeriodo() != null //
                ? ucfirst(strftime("%B %Y", $this->getFechaPeriodo()->getTimestamp())) //
                : '-';
    }

}
