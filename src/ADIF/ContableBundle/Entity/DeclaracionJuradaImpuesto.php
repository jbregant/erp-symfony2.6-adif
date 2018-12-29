<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaImpuesto
 * 
 * @author Darío Rapetti
 * created 17/04/2015
 * 
 * @ORM\Table(name="declaracion_jurada_impuesto")
 * @ORM\Entity 
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "sicore" = "DeclaracionJuradaImpuestoSICORE",
 *      "sicoss" = "DeclaracionJuradaImpuestoSICOSS",
 *      "sijp" = "DeclaracionJuradaImpuestoSIJP",
 *      "iibb" = "DeclaracionJuradaImpuestoIIBB" 
 * })
 */
abstract class DeclaracionJuradaImpuesto extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    protected $fecha;

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoDeclaracionJurada", mappedBy="declaracionJurada")
     * */
    protected $ordenPago;

    /**
     * @ORM\ManyToMany(targetEntity="RenglonDeclaracionJurada")
     * @ORM\JoinTable(name="declaracion_jurada_impuesto_renglon_declaracion_jurada")
     * */
    protected $renglonesDeclaracionJurada;

    /**
     * @ORM\ManyToMany(targetEntity="PagoACuenta")
     * @ORM\JoinTable(name="declaracion_jurada_impuesto_pago_a_cuenta")
     * */
    protected $pagosACuenta;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fecha = new \DateTime();
        $this->renglonesDeclaracionJurada = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pagosACuenta = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return DeclaracionJuradaImpuesto
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Add renglonesDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonesDeclaracionJurada
     * @return DeclaracionJuradaImpuesto
     */
    public function addRenglonesDeclaracionJurada(\ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonesDeclaracionJurada) {
        $this->renglonesDeclaracionJurada[] = $renglonesDeclaracionJurada;

        return $this;
    }

    /**
     * Remove renglonesDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonesDeclaracionJurada
     */
    public function removeRenglonesDeclaracionJurada(\ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonesDeclaracionJurada) {
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
     * Add pagosACuenta
     *
     * @param \ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta
     * @return DeclaracionJuradaImpuesto
     */
    public function addPagosACuentum(\ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta) {
        $this->pagosACuenta[] = $pagosACuenta;

        return $this;
    }

    /**
     * Remove pagosACuenta
     *
     * @param \ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta
     */
    public function removePagosACuentum(\ADIF\ContableBundle\Entity\PagoACuenta $pagosACuenta) {
        $this->pagosACuenta->removeElement($pagosACuenta);
    }

    /**
     * Get pagosACuenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPagosACuenta() {
        return $this->pagosACuenta;
    }

    /**
     * 
     * @return double
     */
    public function getImporteTotalPagosACuenta() {

        $importe = 0;

        foreach ($this->pagosACuenta as $pagoACuenta) {
            $importe += $pagoACuenta->getImporte();
        }

        return $importe;
    }

    /**
     * 
     * @return double
     */
    public function getImporteTotalRenglonesDeclaracionJurada() {

        $importe = 0;

        foreach ($this->renglonesDeclaracionJurada as $renglonDeclaracionJurada) {
            $importe += $renglonDeclaracionJurada->getMonto();
        }

        return $importe;
    }

    /**
     * 
     * @return double
     */
    public function getImporteTotalRenglonesDeclaracionJuradaByTipoImpuesto($tipoImpuesto) {

        $importe = 0;

        foreach ($this->renglonesDeclaracionJurada as $renglonDeclaracionJurada) {

            if ($renglonDeclaracionJurada->getTipoImpuesto()->getDenominacion() == $tipoImpuesto) {
                $importe += $renglonDeclaracionJurada->getMonto();
            }
        }

        return $importe;
    }

    /**
     * 
     * @return double
     */
    public function getImporteTotalRenglonesDeclaracionJuradaPagosACuentaByTipoImpuesto($tipoImpuesto) {

        $importe = 0;

        foreach ($this->renglonesDeclaracionJurada as $renglonDeclaracionJurada) {
            if ($renglonDeclaracionJurada->getTipoImpuesto()->getDenominacion() == $tipoImpuesto) {
                $importe += $renglonDeclaracionJurada->getMonto();
            }
        }

        foreach ($this->pagosACuenta as $pagoACuenta) {
            foreach ($pagoACuenta->getRenglonesDeclaracionJurada() as $renglonDeclaracionJurada) {
                if ($renglonDeclaracionJurada->getTipoImpuesto()->getDenominacion() == $tipoImpuesto) {
                    $importe += $renglonDeclaracionJurada->getMonto();
                }
            }
        }

        return $importe;
    }

    /**
     * 
     * @param type $codigoTipoRenglonDDJJ
     * @return type
     */
    public function getImporteTotalRenglonesDeclaracionJuradaByTipoRenglon($codigoTipoRenglonDDJJ) {

        $importe = 0;

        foreach ($this->renglonesDeclaracionJurada as $renglonDeclaracionJurada) {

            if ($renglonDeclaracionJurada->getTipoRenglonDeclaracionJurada()->getCodigo() == $codigoTipoRenglonDDJJ) {
                $importe += $renglonDeclaracionJurada->getMonto();
            }
        }

        return $importe;
    }

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJurada $ordenPago
     * @return DeclaracionJuradaImpuesto
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJurada $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJurada 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * 
     * @return type
     */
    public function getImporte() {
        /*
        if ($this->ordenPago != null) {
            return $this->ordenPago->getImporte();
        }        
        
        return null;
        */
        return $this->getImporteTotalPagosACuenta() + $this->getImporteTotalRenglonesDeclaracionJurada();
    }

    /**
     * 
     * @return type
     */
    public function getTipoDeclaracionJurada() {
        return null;
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
