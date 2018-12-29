<?php

namespace ADIF\ComprasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoMoneda;

/**
 * Description of RenglonOrdenCompra
 *
 * @author Manuel Becerra
 * created 14/10/2014
 * 
 * @ORM\Table(name="renglon_orden_compra")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\RenglonOrdenCompraRepository")
 */
class RenglonOrdenCompra {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var OrdenCompra
     *
     * @ORM\ManyToOne(targetEntity="OrdenCompra", inversedBy="renglones")
     * @ORM\JoinColumn(name="id_orden_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $ordenCompra;

    /**
     * @var \ADIF\ComprasBundle\Entity\RenglonCotizacion
     *
     * @ORM\ManyToOne(targetEntity="RenglonCotizacion")
     * @ORM\JoinColumn(name="id_renglon_cotizacion", referencedColumnName="id", nullable=true)
     * 
     */
    protected $renglonCotizacion;

    /**
     * @var \ADIF\ComprasBundle\Entity\BienEconomico
     *
     * @ORM\ManyToOne(targetEntity="BienEconomico")
     * @ORM\JoinColumn(name="id_bien_economico", referencedColumnName="id", nullable=true)
     * 
     */
    protected $bienEconomico;

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
     * @ORM\Column(name="restante", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La cantidad restante debe ser de tipo numérico.")
     */
    protected $restante;

    /**
     * @var \ADIF\ComprasBundle\Entity\UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida", inversedBy="renglonesSolicitudCompra")
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id", nullable=true)
     * 
     */
    protected $unidadMedida;

    /**
     * @var double
     * @ORM\Column(name="precio_unitario", type="decimal", precision=16, scale=4, nullable=false)
     */
    protected $precioUnitario;

    /**
     * @ORM\Column(name="id_alicuota_iva", type="integer", nullable=true)
     */
    protected $idAlicuotaIva;

    /**
     * @var ADIF\ContableBundle\Entity\AlicuotaIva
     */
    protected $alicuotaIva;

    /**
     * @ORM\Column(name="id_centro_costo", type="integer", nullable=true)
     */
    protected $idCentroCosto;

    /**
     * @var ADIF\ContableBundle\Entity\CentroCosto
     */
    protected $centroCosto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_ampliacion", type="boolean", nullable=false)
     */
    protected $esAmpliacion;

    /**
     * @var double
     * @ORM\Column(name="tipo_cambio", type="decimal", precision=10, scale=4, nullable=false, options={"default": 1})
     */
    protected $tipoCambio;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="es_desglosado", type="boolean", nullable=false)
     */
    protected $esDesglosado;

    /**
     * Constructor
     */
    public function __construct() {

        $this->esAmpliacion = false;
        $this->tipoCambio = 1;
		$this->esDesglosado = false;
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
     * Set cantidad
     *
     * @param float $cantidad
     * @return RenglonOrdenCompra
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
     * Set restante
     *
     * @param float $restante
     * @return RenglonOrdenCompra
     */
    public function setRestante($restante) {
        $this->restante = $restante;

        return $this;
    }

    /**
     * Get restante
     *
     * @return float 
     */
    public function getRestante() {
        return $this->restante;
    }

    /**
     * Set precioUnitario
     *
     * @param double $precioUnitario
     * @return RenglonOrdenCompra
     */
    public function setPrecioUnitario($precioUnitario) {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getPrecioUnitario($enMCL = true) {
        return $this->precioUnitario * $this->getTipoCambioCalculado($enMCL);
    }

    /**
     * Set idAlicuotaIva
     *
     * @param integer $idAlicuotaIva
     * @return RenglonOrdenCompra
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
     * Set idCentroCosto
     *
     * @param integer $idCentroCosto
     * @return RenglonOrdenCompra
     */
    public function setIdCentroCosto($idCentroCosto) {
        $this->idCentroCosto = $idCentroCosto;

        return $this;
    }

    /**
     * Get idCentroCosto
     *
     * @return integer 
     */
    public function getIdCentroCosto() {
        return $this->idCentroCosto;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CentroCosto $centroCosto
     */
    public function setCentroCosto($centroCosto) {

        if (null != $centroCosto) {
            $this->idCentroCosto = $centroCosto->getId();
        } //.
        else {
            $this->idCentroCosto = null;
        }

        $this->centroCosto = $centroCosto;
    }

    /**
     * 
     * @return type
     */
    public function getCentroCosto() {
        return $this->centroCosto;
    }

    /**
     * Set ordenCompra
     *
     * @param \ADIF\ComprasBundle\Entity\OrdenCompra $ordenCompra
     * @return RenglonOrdenCompra
     */
    public function setOrdenCompra(\ADIF\ComprasBundle\Entity\OrdenCompra $ordenCompra) {
        $this->ordenCompra = $ordenCompra;

        return $this;
    }

    /**
     * Get ordenCompra
     *
     * @return \ADIF\ComprasBundle\Entity\OrdenCompra 
     */
    public function getOrdenCompra() {
        return $this->ordenCompra;
    }

    /**
     * Set renglonCotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonCotizacion
     * @return RenglonOrdenCompra
     */
    public function setRenglonCotizacion(\ADIF\ComprasBundle\Entity\RenglonCotizacion $renglonCotizacion = null) {
        $this->renglonCotizacion = $renglonCotizacion;

        return $this;
    }

    /**
     * Get renglonCotizacion
     *
     * @return \ADIF\ComprasBundle\Entity\RenglonCotizacion 
     */
    public function getRenglonCotizacion() {
        return $this->renglonCotizacion;
    }

    /**
     * Set unidadMedida
     *
     * @param \ADIF\ComprasBundle\Entity\UnidadMedida $unidadMedida
     * @return RenglonOrdenCompra
     */
    public function setUnidadMedida(\ADIF\ComprasBundle\Entity\UnidadMedida $unidadMedida) {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return \ADIF\ComprasBundle\Entity\UnidadMedida 
     */
    public function getUnidadMedida() {
        return $this->unidadMedida;
    }

    /**
     * Retorna el RenglonPedidoInterno
     * @return RenglonPedidoInterno Renglon del pedido interno
     */
    public function getRenglonPedidoInterno() {

        $renglonPedidoInterno = null;

        if ($this->getRenglonCotizacion() != null) {
            $renglonPedidoInterno = $this
                    ->getRenglonCotizacion()
                    ->getRenglonRequerimiento()
                    ->getRenglonSolicitudCompra()
                    ->getRenglonPedidoInterno();
        }

        return $renglonPedidoInterno;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getPrecioTotal($enMCL = true) {

        return ($this->getPrecioUnitario($enMCL) + $this->getMontoIva($enMCL)) //
                * $this->getCantidad();
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoIva($enMCL = true) {

        $montoIva = 0;

        if (null != $this->alicuotaIva) {

            $montoIva = $this->alicuotaIva->getValor() * $this->getPrecioUnitario($enMCL) / 100;
        }

        return $montoIva;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoTotalIva($enMCL = true) {

        return $this->getMontoIva($enMCL) * $this->cantidad;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMontoNeto($enMCL = true) {
        return $this->getCantidad() * $this->getPrecioUnitario($enMCL);
    }

    /**
     * 
     * @return float
     */
    public function getMontoNetoMasIva($enMCL = true) {

        return $this->getMontoNeto($enMCL) + $this->getMontoTotalIva($enMCL);
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
    public function getPrecioTotalProrrateado($enMCL = true) {

        return $this->getPrecioTotal($enMCL) + $this->getOrdenCompra()->getMontoAdicionalProrrateado($this, $enMCL);
    }

    /**
     *   Retorna la descripción del bien economico asociado al tenglon
     */
    public function getDescripcionBien() {

        $desripcionBienEconomico = null;

        if ($this->getRenglonPedidoInterno() != null) {
            $desripcionBienEconomico = $this->getRenglonPedidoInterno()->getBienEconomico()
                    ->getDenominacionBienEconomico();
        } else {
            $desripcionBienEconomico = $this->getBienEconomico()
                    ->getDenominacionBienEconomico();
        }

        return $desripcionBienEconomico;
    }

    /**
     * Set bienEconomico
     *
     * @param \ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico
     * @return RenglonOrdenCompra
     */
    public function setBienEconomico(\ADIF\ComprasBundle\Entity\BienEconomico $bienEconomico) {
        $this->bienEconomico = $bienEconomico;

        return $this;
    }

    /**
     * Get bienEconomico
     *
     * @return \ADIF\ComprasBundle\Entity\BienEconomico 
     */
    public function getBienEconomico() {
        return $this->bienEconomico;
    }

    /**
     * Set esAmpliacion
     *
     * @param boolean $esAmpliacion
     * @return RenglonOrdenCompra
     */
    public function setEsAmpliacion($esAmpliacion) {
        $this->esAmpliacion = $esAmpliacion;

        return $this;
    }

    /**
     * Get esAmpliacion
     *
     * @return boolean 
     */
    public function getEsAmpliacion() {
        return $this->esAmpliacion;
    }

    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return RenglonOrdenCompra
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
     * 
     * @return type
     */
    public function getTipoMoneda() {
        return $this->ordenCompra->getTipoMoneda();
    }

    /**
     * 
     * @return type
     */
    public function getDescripcionSolicitud() {
        return ($this->getRenglonCotizacion() != null) //
                ? $this->getRenglonCotizacion()->getRenglonRequerimiento()
                        ->getRenglonSolicitudCompra()->getDescripcion() //
                : '-';
    }

    /**
     *
     * @return type
     */
    private function getTipoCambioCalculado($enMCL) {

		// La moneda de curso legal (MCL), se tiene que establecer segun el tipo de moneda que viene seteada desde la OC
		// y no por defecto porque este en todos los metodos seteada en TRUE. La moneda ARS (peso argentino) es MCL y ninguna otra 
		//var_dump( $this->getOrdenCompra()->getId() );exit;
		if (!is_null($this->getOrdenCompra()) && !is_null($this->getOrdenCompra()->getTipoMoneda())) {
			if ($this->getOrdenCompra()->getTipoMoneda()->getCodigoTipoMoneda() == ConstanteTipoMoneda::PESO_ARGENTINO) {
				$enMCL = true;
			} else {
				$enMCL = false;
			}
		}
		
        return $enMCL ? $this->tipoCambio : 1;
    }
	
	public function setEsDesglosado($esDesglosado)
	{
		$this->esDesglosado = $esDesglosado;
		
		return $this;
	}
	
	public function getEsDesglosado()
	{
		return $this->esDesglosado;
	}
	
}
