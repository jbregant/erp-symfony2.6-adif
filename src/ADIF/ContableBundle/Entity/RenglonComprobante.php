<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RenglonComprobante
 *
 * 
 * @author Darío Rapetti
 * created 22/10/2014
 * 
 * @ORM\Table(name="renglon_comprobante")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "renglon_comprobante_general" = "RenglonComprobante",
 *      "renglon_comprobante_compra" = "RenglonComprobanteCompra",
 *      "renglon_comprobante_obra" = "ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra",
 *      "renglon_comprobante_venta" = "ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVenta",
 *      "renglon_comprobante_venta_general" = "ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVentaGeneral",
 *      "renglon_comprobante_egreso_valor" = "ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor",
 *      "renglon_comprobante_consultoria" =  "ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria",
 *      "renglon_comprobante_rendicion_liquido_producto" = "ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteRendicionLiquidoProducto"
 * })
 */
class RenglonComprobante extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Comprobante
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Comprobante", inversedBy="renglonesComprobante", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    protected $comprobante;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    protected $descripcion;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="decimal", precision=15, scale=2, nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="La cantidad debe ser de tipo numérico.")
     */
    protected $cantidad;

    /**
     * @var float
     * 
     * @ORM\Column(name="precio_unitario", type="decimal", precision=16, scale=4, nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El precio unitario debe ser de tipo numérico.")
     */
    protected $precioUnitario;

    /**
     * @var double
     * @ORM\Column(name="monto_neto", type="decimal", precision=16, scale=4, nullable=false)
     * 
     */
    protected $montoNeto;

    /**
     * @var string
     * @ORM\Column(name="tipo_bonificacion", type="string", columnDefinition="ENUM('porcentaje', 'valor')")
     * 
     */
    protected $bonificacionTipo;

    /**
     * @var double
     * @ORM\Column(name="bonificacion", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $bonificacionValor;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\AlicuotaIva", fetch="EAGER")
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
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=1000, nullable=true)
     * @Assert\Length(
     *      max="1000", 
     *      maxMessage="La observación del comprobante no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $observaciones;

    /**
     * @ORM\OneToOne(targetEntity="RenglonComprobante")
     * @ORM\JoinColumn(name="id_renglon_acreditado", referencedColumnName="id")
     */
    private $renglonAcreditado;

    /**
     * 
     */
    public function __construct() {
        
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
     * Set comprobante
     *
     * @param Comprobante $comprobante
     * @return RenglonComprobante
     */
    public function setComprobante($comprobante) {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return RenglonComprobante
     */
    public function getComprobante() {
        return $this->comprobante;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return RenglonComprobante
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set precioUnitario
     *
     * @param float $precioUnitario
     * @return RenglonComprobante
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
    public function getPrecioUnitario() {
        return $this->precioUnitario;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     * @return RenglonComprobante
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string 
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Set bonificacionTipo
     *
     * @param string $bonificacionTipo
     * @return RenglonComprobante
     */
    public function setBonificacionTipo($bonificacionTipo) {
        $this->bonificacionTipo = $bonificacionTipo;

        return $this;
    }

    /**
     * Get bonificacionTipo
     *
     * @return string 
     */
    public function getBonificacionTipo() {
        return $this->bonificacionTipo;
    }

    /**
     * Set bonificacionValor
     *
     * @param string $bonificacionValor
     * @return RenglonComprobante
     */
    public function setBonificacionValor($bonificacionValor) {
        $this->bonificacionValor = $bonificacionValor;

        return $this;
    }

    /**
     * Get bonificacionValor
     *
     * @return string 
     */
    public function getBonificacionValor() {
        return $this->bonificacionValor;
    }

    /**
     * Set alicuotaIva
     *
     * @param \ADIF\ContableBundle\Entity\AlicuotaIva $alicuotaIva
     * @return RenglonComprobante
     */
    public function setAlicuotaIva(\ADIF\ContableBundle\Entity\AlicuotaIva $alicuotaIva) {
        $this->alicuotaIva = $alicuotaIva;

        return $this;
    }

    /**
     * Get alicuotaIva
     *
     * @return \ADIF\ContableBundle\Entity\AlicuotaIva 
     */
    public function getAlicuotaIva() {
        return $this->alicuotaIva;
    }

    /**
     * Set montoIva
     *
     * @param double $montoIva
     * @return RenglonComprobante
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
     * Set observaciones
     *
     * @param string $observaciones
     * @return RenglonComprobante
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * 
     * @return float
     */
    public function getPrecioTotal() {

        return $this->cantidad * $this->precioUnitario;
    }

    /**
     * Set montoNeto
     *
     * @param double $montoNeto
     * @return RenglonComprobante
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
    public function getBonificacionEnValor() {

        $valor = $this->getBonificacionValor();

        if ($this->getBonificacionTipo() == 'porcentaje') {

            $valor *= $this->getPrecioTotal() / 100;
        }

        return $valor;
    }

    /**
     * 
     * @return float
     */
    public function getIva() {

        $iva = 0;

        if (null != $this->getAlicuotaIva()) {

            $iva = $this->getAlicuotaIva()->getValor() * $this->getPrecioTotal() / 100;
        }

        return $iva;
    }

    /**
     * 
     * @return type
     */
    public function getPorcentajeIva() {

        if (null != $this->getAlicuotaIva()) {

            return $this->getAlicuotaIva()->getValor();
        }

        return null;
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

    public function getMontoNetoBonificado() {
        $montoNetoBonificado = $this->getMontoNeto();
        $totalNeto = $this->getComprobante()->getTotalNeto();

        foreach ($this->getComprobante()->getAdicionales() as $adicional) {
            $porcentajeAdicional = $adicional->getMontoNetoMasIva() * 100 / $totalNeto;
            $montoNetoBonificado += $this->getMontoNeto() * $porcentajeAdicional / 100;
        }

        return $montoNetoBonificado;
    }

    public function getMontoIvaBonificado() {
        $montoIvaBonificado = $this->getMontoIva();
        $totalIva = $this->getComprobante()->getTotalIva();
        if ($totalIva) {
            foreach ($this->getComprobante()->getAdicionales() as $adicional) {
                $porcentajeAdicional = $adicional->getMontoNetoMasIva() * 100 / $totalIva;
                $montoIvaBonificado += $this->getMontoIva() * $porcentajeAdicional / 100;
            }
        }

        return $montoIvaBonificado;
    }

    public function getMontoAdicionalProrrateadoDiscriminado() {
        $totalAdicionalProrrateado = array(
            'neto' => 0,
            'iva' => 0
        );
        foreach ($this->getComprobante()->getAdicionales() as $adicional) {
            /* @var $adicional AdicionalComprobanteCompra */
            $montoIvaAdicional = $adicional->getMontoIva();
            if ($montoIvaAdicional == 0) {
                $porcentajeAdicional = $adicional->getMontoNeto() / $this->getComprobante()->getTotalNeto();
                $neto = $porcentajeAdicional * $this->getMontoNeto();
                if ($adicional->getTipoValor() == "$") {
                    $iva = 0;
                } else {
                    $iva = $porcentajeAdicional * $this->getMontoIva();
                }
            } else {
                $porcentajeAdicional = $adicional->getMontoNeto() / $this->getComprobante()->getTotalNeto();

                $neto = $porcentajeAdicional * $this->getMontoNeto();
                //$proporcionIva = $adicional->getMontoIva() / $adicional->getMontoNeto();
                //$neto = $montoASumar - ($montoASumar * $proporcionIva);
                //$iva = $montoASumar * $proporcionIva;
                $iva = 0;
            }
            $totalAdicionalProrrateado['neto'] += $neto;
            $totalAdicionalProrrateado['iva'] += $iva;
        }
        $totalAdicionalProrrateado['neto'] += $this->getMontoNeto();
        $totalAdicionalProrrateado['iva'] += $this->getMontoIva();

        return $totalAdicionalProrrateado;
    }

    /**
     * 
     * @return float
     */
    public function getMontoTotalIva() {

        return $this->getMontoIva() * $this->cantidad;
    }

    /**
     * 
     * @return float
     */
    public function getMontoNetoMasIva() {
        return $this->getMontoNeto() + $this->getMontoTotalIva();
    }

    /**
     * 
     * @return float
     */
    public function getMontoBruto() {
        return $this->getMontoNeto() + $this->getMontoIva();
    }

    public function getMontoAdicionalProrrateadoDiscriminadoReporteIVACompras() {
        $totalAdicionalProrrateado = array(
            'neto' => 0,
            'iva' => 0
        );
        foreach ($this->getComprobante()->getAdicionales() as $adicional) {
            /* @var $adicional AdicionalComprobanteCompra */
            $montoIvaAdicional = $adicional->getMontoIva();
            if ($montoIvaAdicional == 0) {
                $porcentajeAdicional = $adicional->getMontoNeto() / $this->getComprobante()->getTotalNeto();
                $neto = $porcentajeAdicional * $this->getMontoNeto();
                if ($adicional->getTipoValor() == "$") {
                    $iva = 0;
                } else {
                    $iva = $porcentajeAdicional * $this->getMontoIva();
                }
            } else {
                $porcentajeAdicional = $adicional->getMontoNeto() / $this->getComprobante()->getTotalNeto();

                $neto = $porcentajeAdicional * $this->getMontoNeto();
                //$proporcionIva = $adicional->getMontoIva() / $adicional->getMontoNeto();
                //$neto = $montoASumar - ($montoASumar * $proporcionIva);
                //$iva = $montoASumar * $proporcionIva;
                $iva = 0;
            }
            $totalAdicionalProrrateado['neto'] += $neto;
            $totalAdicionalProrrateado['iva'] += $iva;
        }
        $totalAdicionalProrrateado['neto'] += $this->getMontoNeto();
        $totalAdicionalProrrateado['iva'] += $this->getMontoIva();

        return $totalAdicionalProrrateado;
    }


    /**
     * Set renglonAcreditado
     *
     * @param \ADIF\ContableBundle\Entity\RenglonComprobante $renglonAcreditado
     * @return RenglonComprobante
     */
    public function setRenglonAcreditado(\ADIF\ContableBundle\Entity\RenglonComprobante $renglonAcreditado = null)
    {
        $this->renglonAcreditado = $renglonAcreditado;

        return $this;
    }

    /**
     * Get renglonAcreditado
     *
     * @return \ADIF\ContableBundle\Entity\RenglonComprobante 
     */
    public function getRenglonAcreditado()
    {
        return $this->renglonAcreditado;
    }

    /**
     * Get conceptoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor
     */
    public function getConceptoEgresoValor() {
        return null;
    }
    
    /**
     * @override
     */
    public function getRegimenRetencionSUSS() 
    {
        return null;
    }
    
    /**
     * @override
     */
    public function getRegimenRetencionIVA() 
    {
        return null;
    }
    
    /**
     * @override
     */
    public function getRegimenRetencionIIBB() 
    {
        return null;
    }
    
    /**
     * @override
     */
    public function getRegimenRetencionGanancias() 
    {
        return null;
    }

}
