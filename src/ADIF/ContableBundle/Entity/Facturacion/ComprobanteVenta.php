<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Comprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAfip;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ADIF\ContableBundle\Entity\RetencionClienteParametrizacion;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ComprasBundle\Entity\Cliente;
/**
 * ComprobanteVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="comprobante_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "comprobante_venta" = "ComprobanteVenta",
 *      "factura_venta" = "FacturaVenta",
 *      "factura_venta_general" = "FacturaVentaGeneral",
 *      "factura_pliego" = "FacturaPliego",
 *      "factura_ingreso" = "FacturaIngreso",
 *      "factura_alquiler" = "FacturaAlquiler",
 *      "factura_chatarra" = "FacturaChatarra",
 *      "nota_debito_venta" = "NotaDebitoVenta",
 *      "nota_debito_venta_general" = "NotaDebitoVentaGeneral",
 *      "nota_debito_pliego" = "NotaDebitoPliego",
 *      "nota_debito_interes" = "NotaDebitoInteres",
 *      "nota_credito_venta" = "NotaCreditoVenta",
 *      "nota_credito_venta_general" = "NotaCreditoVentaGeneral",
 *      "nota_credito_pliego" = "NotaCreditoPliego",
 *      "cupon_venta" = "CuponVenta",
 *      "cupon_venta_general" = "CuponVentaGeneral",
 *      "cupon_venta_plazo" = "CuponVentaPlazo",
 *      "cupon_pliego" = "CuponPliego",
 *		"comprobante_rendicion_liquido_producto" = "ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto"
 * })
 * @UniqueEntity(
 *      fields={"letraComprobante", "puntoVenta", "numero", "idCliente"},
 *      message="El número de comprobante ya se encuentra en uso.",
 *      repositoryMethod="validarNumeroComprobanteUnico",
 *      groups={"create"}
 * )
 */
class ComprobanteVenta extends Comprobante implements BaseAuditable {

    /**
     * @var PuntoVenta
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\PuntoVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_punto_venta", referencedColumnName="id", nullable=true)
     * })
     */
    protected $puntoVenta;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Facturacion\ContratoVenta", inversedBy="comprobantesVenta")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=true)
     */
    protected $contrato;

    /**
     * @ORM\Column(name="id_cliente", type="integer", nullable=true)
     */
    protected $idCliente;

    /**
     * @var Cliente
     */
    protected $cliente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio_servicio", type="datetime", nullable=true)
     */
    protected $fechaInicioServicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin_servicio", type="datetime", nullable=true)
     */
    protected $fechaFinServicio;

    /**
     * @ORM\ManyToMany(targetEntity="ADIF\ContableBundle\Entity\Cobranza\Cobro", inversedBy="comprobantes")
     * @ORM\JoinTable(name="comprobante_venta_cobro",
     *      joinColumns={@ORM\JoinColumn(name="id_comprobante_venta", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_cobro", referencedColumnName="id")}
     *      )
     */
    protected $cobros;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime", nullable=true)
     */
    protected $fechaVencimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_anulacion", type="datetime", nullable=true)
     */
    protected $fechaAnulacion;

    /**
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", nullable=true)
     */
    protected $periodo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigo_barras", type="string", length=26, nullable=true)
     */
    protected $codigoBarras;    

    /**
     * @ORM\Column(name="saldar", type="boolean", nullable=true)
     */
    protected $saldar;
	
	protected $anticipos;

    /**
     * Set puntoVenta
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta $puntoVenta
     * @return ComprobanteVenta
     */
    public function setPuntoVenta(\ADIF\ContableBundle\Entity\Facturacion\PuntoVenta $puntoVenta) {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta 
     */
    public function getPuntoVenta() {
        return $this->puntoVenta;
    }

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ContratoVenta $contrato
     * @return ComprobanteVenta
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Facturacion\ContratoVenta $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\ContratoVenta 
     */
    public function getContrato() {
        return $this->contrato;
    }

    /**
     * Set idCliente
     *
     * @param integer $idCliente
     * @return FacturaPliego
     */
    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCliente() {

        return $this->getContrato() != null //
                ? $this->getContrato()->getIdCliente() //
                : $this->idCliente;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Cliente $cliente
     */
    public function setCliente($cliente) {

        if (null != $cliente) {
            $this->idCliente = $cliente->getId();
        } else {
            $this->idCliente = null;
        }

        $this->cliente = $cliente;
    }

    /**
     * 
     * @return type
     */
    public function getCliente() {

        return $this->getContrato() != null //
                ? $this->getContrato()->getCliente() //
                : $this->cliente;
    }

    /**
     * Set renglonesComprobante
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return ComprobanteVenta
     */
    public function setRenglonesComprobante(ArrayCollection $renglonesComprobante) {
        $this->renglonesComprobante = $renglonesComprobante;

        return $this;
    }

    /**
     * Get renglonesComprobante
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRenglonesComprobante() {
        return $this->renglonesComprobante;
    }

    /**
     * 
     * @return type
     */
    public function getNumeroCompleto() {

        return ($this->puntoVenta ? $this->puntoVenta->getNumero() . '-' : '' ) . $this->numero;
    }

    /**
     * Get esCupon
     *
     * @return boolean 
     */
    public function getEsCupon() {
        return false;
    }

    /**
     * Get esRendicionLiquidoProducto
     *
     * @return boolean 
     */
    public function getEsRendicionLiquidoProducto() {
        return false;
    }

    /**
     * 
     * @return type
     */
    public function getDocNro() {
        return floatval(str_replace('-', '', $this->getCliente()->getCUIT()));
    }

    /**
     * 
     * @return type
     */
    public function getImpTotal() { //IMPORTE TOAL DEL COMPROBANTE = IMP NETO NO GRAVADO + IMP EXENTO + IMP NETO GAVADO + IVA + IMPORTE DE TRIBUTOS
        return $this->getTotal(); //ESTA ES LA PROPIEDAD DE LA CLASE
        //return $this->getImporteTotalNeto(); //ESTE DEBERIA SER LA SUMA DE NO GRAVADO, GRAVADO Y EXENTO
    }

    /**
     * 
     * @return type
     */
    public function getImpTotConc() { //NETO NO GRAVADO
        return $this->getContrato()->getImpTotConc($this);
    }

    /**
     * 
     * @return type
     */
    public function getImpNeto() { //NETO GRAVADO
        return $this->getImporteNetoGravado();
    }

    /**
     * 
     * @return type
     */
    public function getImpOpEx() { //IMPORTE EXENTO
        return $this->getContrato()->getImpOpEx($this);
    }

    /**
     * 
     * @return type
     */
    public function getImpIVA() {
        return $this->getImporteTotalIVA();
    }

    /**
     * 
     * @return type
     */
    public function getImpTrib() {
        return $this->getImporteTotalPercepcion();
    }

    /**
     * 
     * @return type
     */
    public function getFchVtoPago() {

        if (ConstanteAfip::getTipoConcepto($this) != ConstanteAfip::PRODUCTO) {

            $fechaHoy = new \DateTime();

            if ($this->fechaVencimiento == null) {

                $diaVencimiento = ($this->getContrato()->getDiaVencimiento() == null) ? 10 : $this->getContrato()->getDiaVencimiento();
                if ($fechaHoy->format('d') > $diaVencimiento) {
                    $fechaVencimiento = DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($fechaHoy->format('Y-m-d') . ' + 10 days')));
                } else {
                    $fechaVencimiento = DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($fechaHoy->format('Y-m-') . $diaVencimiento)));
                }
                $this->setFechaVencimiento($fechaVencimiento);
            }
            return (int) $this->fechaVencimiento->format('Ymd');
        } else {
            return null;
        }
    }

    /**
     * Set fechaInicioServicio
     *
     * @param \DateTime $fechaInicioServicio
     * @return ComprobanteVenta
     */
    public function setFechaInicioServicio($fechaInicioServicio) {
        $this->fechaInicioServicio = $fechaInicioServicio;

        return $this;
    }

    /**
     * Get fechaInicioServicio
     *
     * @return \DateTime 
     */
    public function getFechaInicioServicio() {
        return $this->fechaInicioServicio;
    }

    /**
     * Set fechaFinServicio
     *
     * @param \DateTime $fechaFinServicio
     * @return ComprobanteVenta
     */
    public function setFechaFinServicio($fechaFinServicio) {
        $this->fechaFinServicio = $fechaFinServicio;

        return $this;
    }

    /**
     * Get fechaFinServicio
     *
     * @return \DateTime 
     */
    public function getFechaFinServicio() {
        return $this->fechaFinServicio;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getCodigoBarrasNacion() {
        return ($this->getCodigoBarras() == null ? $this->generarCodigoBarras() : $this->getCodigoBarras());
    }

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->cobros = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cobros
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\Cobro $cobros
     * @return ComprobanteVenta
     */
    public function addCobro(\ADIF\ContableBundle\Entity\Cobranza\Cobro $cobros) {
        $this->cobros[] = $cobros;

        return $this;
    }

    /**
     * Remove cobros
     *
     * @param \ADIF\ContableBundle\Entity\Cobranza\Cobro $cobros
     */
    public function removeCobro(\ADIF\ContableBundle\Entity\Cobranza\Cobro $cobros) {
        $this->cobros->removeElement($cobros);
    }

    /**
     * Get cobros
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCobros() {
        return $this->cobros;
    }

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     * @return ComprobanteVenta
     */
    public function setFechaVencimiento($fechaVencimiento) {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaVencimiento() {
        return $this->fechaVencimiento;
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return ComprobanteVenta
     */
    public function setFechaAnulacion($fechaAnulacion) {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion() {
        return $this->fechaAnulacion;
    }
    
    /**
     * Set codigoBarras
     *
     * @param string $codigoBarras
     * @return ComprobanteVenta
     */
    public function setCodigoBarras($codigoBarras) {
        $this->codigoBarras = $codigoBarras;

        return $this;
    }

    /**
     * Get codigoBarras
     *
     * @return string 
     */
    public function getCodigoBarras() {
        return $this->codigoBarras;
    }    

    /**
     *
     * @return string 
     */
    public function anular() {
        //var_dump(sizeOf($this->getCobros()));var_dump($this->getSaldo());var_dump($this->getTotal());die();
        if ((sizeOf($this->getCobros()) == 0)) {
            $rsp = '';
        } //tb podría preguntarse && $this->getSaldo() == $this->getTotal()
        else {
            $rsp = "No se puede anular el comprobante de venta porque posee cobros asociados";
        }
        return $rsp;
    }

    public function getCicloFacturacion() {
        return null;
    }

    /**
     * 
     * @return type
     */
    public function getCodigoClaseContrato() {

        return $this->getContrato() != null //
                ? $this->getContrato()->getClaseContrato()->getCodigo() //
                : null;
    }

    public function tieneRetencionClienteImputada(RetencionClienteParametrizacion $impuesto) {
        foreach ($this->getCobros() as $cobro) {
            if ($cobro->getEsCobroRetencionCliente() && $impuesto->getId() == $cobro->getRetencionesCliente()->first()->getTipoImpuesto()->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get diaVencimiento
     *
     * @return integer 
     */
    public function getDiaVencimiento() {

        $diaVencimiento = 1;

        if ($this->fechaVencimiento != null) {
            $diaVencimiento = $this->fechaVencimiento->format('d');
        }

        return $diaVencimiento;
    }

    public function getSaldoHastaFechaContable($fecha) {

        $saldo = 0;

        if ($this->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO && $this->getFechaContable() <= $fecha) {

            if (!$this->getEsNotaCredito()) {

                $saldo += $this->getTotal();

                foreach ($this->getCobros() as $cobro) {

                    /* @var $cobro \ADIF\ContableBundle\Entity\Cobranza\Cobro */

                    if ($cobro->getFecha() <= $fecha) {

                        $saldo -= $cobro->getMonto();
                    }
                }
            } else {

                $saldo -= $this->getSaldo();
            }
        }

        return $saldo;
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     *
     * @return ComprobanteVenta
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo() {
        return $this->periodo;
    }
    
    public function tieneCobroConReferencia($referencia) {
        foreach ($this->getCobros() as $cobro) {
            if ($cobro->tieneCobranzaConReferencia($referencia)) {
                return true;
            }
        }
        return false;
    }
    
    public function generarCodigoBarras() {
        if ($this->getPuntoVenta() == null) {
            return null;
        }
        $idClienteAdif = '4687';
        $punto_venta = substr($this->getPuntoVenta()->getNumero(), -2, 2);
        $tipo_cbte = ConstanteAfip::getTipoComprobante($this->getLetraComprobante()->getLetra(), $this->getTipoComprobante()->getId());
        return $idClienteAdif . $punto_venta . $this->getNumero() . (strlen($tipo_cbte) == 1 ? '0' . $tipo_cbte : $tipo_cbte) . '9999000000';
    }    
    
    public function getGeneraDeuda() {
        return (!$this->getEsCupon()) || 
                            ($this->getEsCupon() && ($this->getEsCuponGarantia() || $this->getEsCuponVentaPlazo()));
    }

    /**
     * Get saldar
     *
     * @return \boolean 
     */
    public function getSaldar() {
        return $this->saldar;
    }

    /**
     * Set saldar
     *
     * @param \boolean $saldar
     * @return ComprobanteVenta
     */
    public function setSaldar($saldar) {
        $this->saldar = $saldar;

        return $this;
    }
	
	/** 
	* Overrides de Comprobante::getSaldoALaFecha()
	*/
	public function getSaldoALaFecha($fecha) {
       
		// Si el saldo del comprobante esta en null, seguro que se trate de comprobantes viejos importados
		if ($this->getSaldo() == null) {
			return 0;
		}
		
		$saldo = $this->getSaldoHastaFechaContable($fecha);
		
        
        /* @var $comprobante Comprobante */
        foreach ($this->getComprobantesAcreditadosNoAnuladosALaFecha($fecha) as $comprobante) {
            $notaCredito = $comprobante->getNotaCredito();
            foreach ($notaCredito->getRenglonesComprobante() as $renglonComprobante) {

                if ($renglonComprobante->getRenglonAcreditado()->getComprobante()->getId() == $comprobante->getId()) {

                    $saldo -= $renglonComprobante->getMontoBruto();
                }
            }
        }
		
		foreach($this->comprobantesAjustes as $comprobanteAjuste) {
			$sumaResta = $comprobanteAjuste->getEsNotaCredito() ? -1 : 1;
			$saldo += $comprobanteAjuste->getTotal() * $sumaResta;
		}
				
        return $saldo;
    }
    
    public function getEsMigracionAabe()
    {
        return false;
    }
}
