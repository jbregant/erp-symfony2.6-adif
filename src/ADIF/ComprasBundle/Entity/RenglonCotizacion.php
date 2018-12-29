<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonCotizacion
 *
 * @author Carlos Sabena
 * created 14/07/2014
 * 
 * @ORM\Table(name="renglon_cotizacion")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\RenglonCotizacionRepository")
 */
class RenglonCotizacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\Cotizacion
     *
     * @ORM\ManyToOne(targetEntity="Cotizacion", inversedBy="renglonesCotizacion")
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
     * @var \ADIF\ComprasBundle\Entity\RenglonRequerimiento
     *
     * @ORM\ManyToOne(targetEntity="RenglonRequerimiento", inversedBy="renglonesCotizacion")
     * @ORM\JoinColumn(name="id_renglon_requerimiento", referencedColumnName="id", nullable=false)
     * 
     */
    protected $renglonRequerimiento;

    /**
     * @var float
     * 
     * @ORM\Column(name="cantidad", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La cantidad debe ser de tipo numérico.")
     */
    protected $cantidad;

    /**
     * @var float
     * 
     * @ORM\Column(name="precio_unitario", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El precio unitario debe ser de tipo numérico.")
     */
    protected $precioUnitario;

    /**
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=true)
     */
    protected $idTipoMoneda;

    /**
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    protected $tipoMoneda;

    /**
     * @var double
     * @ORM\Column(name="tipo_cambio", type="decimal", precision=10, scale=4, nullable=false, options={"default": 1})
     */
    protected $tipoCambio;

    /**
     * @ORM\Column(name="id_alicuota_iva", type="integer", nullable=true)
     */
    protected $idAlicuotaIva;

    /**
     * @var ADIF\ContableBundle\Entity\AlicuotaIva
     */
    protected $alicuotaIva;

    /**
     * @var string
     *
     * @ORM\Column(name="obsrevacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cotizacion_elegida", type="boolean", nullable=false)
     */
    protected $cotizacionElegida;

    /**
     * @var string
     *
     * @ORM\Column(name="justificacion", type="text", nullable=true)
     */
    protected $justificacion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->precioUnitario = 0;
        $this->tipoCambio = 1;
        $this->cotizacionElegida = FALSE;
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
     * Set observacion
     *
     * @param string $observacion
     * @return RenglonCotizacion
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Set cotizacionElegida
     *
     * @param boolean $cotizacionElegida
     * @return RenglonCotizacion
     */
    public function setCotizacionElegida($cotizacionElegida) {
        $this->cotizacionElegida = $cotizacionElegida;

        return $this;
    }

    /**
     * Get cotizacionElegida
     *
     * @return boolean 
     */
    public function getCotizacionElegida() {
        return $this->cotizacionElegida;
    }

    /**
     * Set justificacion
     *
     * @param string $justificacion
     * @return RenglonCotizacion
     */
    public function setJustificacion($justificacion) {
        $this->justificacion = $justificacion;

        return $this;
    }

    /**
     * Get justificacion
     *
     * @return string 
     */
    public function getJustificacion() {
        return $this->justificacion;
    }

    /**
     * Set cotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\Cotizacion $cotizacion
     * @return RenglonCotizacion
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
     * @return RenglonCotizacion
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
     * Set renglonRequerimiento
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonRequerimiento
     * @return RenglonCotizacion
     */
    public function setRenglonRequerimiento(\ADIF\ComprasBundle\Entity\RenglonRequerimiento $renglonRequerimiento) {
        $this->renglonRequerimiento = $renglonRequerimiento;

        return $this;
    }

    /**
     * Get renglonRequerimiento
     *
     * @return \ADIF\ComprasBundle\Entity\RenglonRequerimiento 
     */
    public function getRenglonRequerimiento() {
        return $this->renglonRequerimiento;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return RenglonCotizacion
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float 
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Set precioUnitario
     *
     * @param float $precioUnitario
     * @return RenglonCotizacion
     */
    public function setPrecioUnitario($precioUnitario) {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return float 
     */
    public function getPrecioUnitario($enMCL = true) {

        return $this->precioUnitario * $this->getTipoCambioCalculado($enMCL);
    }

    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return RenglonCotizacion
     */
    public function setTipoCambio($tipoCambio) {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return string 
     */
    public function getTipoCambio() {
        return $this->tipoCambio;
    }

    /**
     * Set idTipoMoneda
     *
     * @param integer $idTipoMoneda
     * @return RenglonCotizacion
     */
    public function setIdTipoMoneda($idTipoMoneda) {
        $this->idTipoMoneda = $idTipoMoneda;

        return $this;
    }

    /**
     * Get idTipoMoneda
     *
     * @return integer 
     */
    public function getIdTipoMoneda() {
        return $this->idTipoMoneda;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     */
    public function setTipoMoneda($tipoMoneda) {

        if (null != $tipoMoneda) {
            $this->idTipoMoneda = $tipoMoneda->getId();
        } //.
        else {
            $this->idTipoMoneda = null;
        }

        $this->tipoMoneda = $tipoMoneda;
    }

    /**
     * 
     * @return type
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * Set idAlicuotaIva
     *
     * @param integer $idAlicuotaIva
     * @return RenglonCotizacion
     */
    public function setIdAlicuotaIva($idAlicuotaIva) {
        $this->idAlicuotaIva = $idAlicuotaIva;

        return $this;
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
     * 
     * @param type $enMCL
     * @return type
     */
    public function getPrecioUnitarioMasIva($enMCL = true) {

        return ($this->precioUnitario + $this->getMontoIva()) * $this->getTipoCambioCalculado($enMCL);
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoTotal($enMCL = true) {

        return $this->getPrecioUnitarioMasIva($enMCL) * $this->cantidad;
    }

    /**
     * 
     * @return type
     */
    public function getMontoIva() {

        $montoIva = 0;

        if (null != $this->alicuotaIva) {

            $montoIva = $this->alicuotaIva->getValor() * $this->precioUnitario / 100;
        }

        return $montoIva;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoTotalIva($enMCL = true) {

        return $this->getMontoIva() * $this->cantidad * $this->getTipoCambioCalculado($enMCL);
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoTotalNeto($enMCL = true) {

        return $this->getPrecioUnitario($enMCL) * $this->cantidad;
    }

    /**
     * 
     * @return type
     */
    public function getNumero() {

        return str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    private function getTipoCambioCalculado($enMCL = true) {

        return $enMCL ? $this->tipoCambio : 1;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {

        $tipoMoneda = "$";

        if ($this->idTipoMoneda != null) {

            $tipoMoneda = $this->getTipoMoneda()->getSimboloTipoMoneda();
        }

        return $tipoMoneda;
    }

}
