<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;

/**
 * ContratoVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="contrato_venta")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ContratoVentaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "contrato_venta_plazo" = "ContratoVentaPlazo",
 *      "contrato_alquiler" = "ContratoAlquiler",
 *      "contrato_alquiler_vivienda" = "ContratoAlquilerVivienda",
 *      "contrato_alquiler_comercial" = "ContratoAlquilerComercial",
 *      "contrato_alquiler_agropecuario" = "ContratoAlquilerAgropecuario",
 *      "contrato_tenencia_precaria" = "ContratoTenenciaPrecaria",
 *      "contrato_chatarra" = "ContratoChatarra",
 *      "contrato_servidumbre_de_paso" = "ContratoServidumbreDePaso",
 *      "contrato_asunto_oficial_municipalidad" = "ContratoAsuntoOficialMunicipalidad"
 * })
 * )
 */
class ContratoVenta extends Contrato implements BaseAuditable {

    /**
     * @ORM\Column(name="id_cliente", type="integer", nullable=true)
     */
    protected $idCliente;

    /**
     * @var ADIF\ComprasBundle\Entity\Cliente
     */
    protected $cliente;

    /**
     * @var integer
     *
     * @ORM\Column(name="dia_vencimiento", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 31,
     *      minMessage = "El día de vencimiento debe ser mayor o igual a {{ limit }}.",
     *      maxMessage = "El día de vencimiento debe ser menor o igual a {{ limit }}."
     * )
     */
    protected $diaVencimiento;

    /**
     * @var double
     * @ORM\Column(name="porcentaje_tasa_interes_mensual", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeTasaInteresMensual;

    /**
     * @var boolean
     *
     * @ORM\Column(name="calcula_iva", type="boolean", nullable=false)
     */
    protected $calculaIVA;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_exportacion", type="boolean", nullable=false)
     */
    protected $esExportacion;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ComprobanteVenta", mappedBy="contrato", cascade={"all"})
     * @ORM\OrderBy({"fechaComprobante" = "ASC", "numero" = "ASC"})
     */
    protected $comprobantesVenta;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_onabe", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número Onabe no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroOnabe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desocupacion", type="date", nullable=true)
     */
    protected $fechaDesocupacion;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->porcentajeTasaInteresMensual = 0;
        $this->calculaIVA = TRUE;
        $this->esExportacion = FALSE;
        $this->comprobantesVenta = new ArrayCollection();
    }

    /**
     * 
     * @param type $idCliente
     * @return ContratoVenta
     */
    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
        return $this;
    }

    /**
     * Get idCliente
     *
     * @return integer 
     */
    public function getIdCliente() {
        return $this->idCliente;
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
        return $this->cliente;
    }

    /**
     * Set diaVencimiento
     *
     * @param integer $diaVencimiento
     * @return ContratoVenta
     */
    public function setDiaVencimiento($diaVencimiento) {
        $this->diaVencimiento = $diaVencimiento;

        return $this;
    }

    /**
     * Get diaVencimiento
     *
     * @return integer 
     */
    public function getDiaVencimiento() {
        return $this->diaVencimiento;
    }

    /**
     * Set porcentajeTasaInteresMensual
     *
     * @param string $porcentajeTasaInteresMensual
     * @return ContratoVenta
     */
    public function setPorcentajeTasaInteresMensual($porcentajeTasaInteresMensual) {
        $this->porcentajeTasaInteresMensual = $porcentajeTasaInteresMensual;

        return $this;
    }

    /**
     * Get porcentajeTasaInteresMensual
     *
     * @return string 
     */
    public function getPorcentajeTasaInteresMensual() {
        return $this->porcentajeTasaInteresMensual;
    }

    /**
     * Set calculaIVA
     *
     * @param boolean $calculaIVA
     * @return Contrato
     */
    public function setCalculaIVA($calculaIVA) {
        $this->calculaIVA = $calculaIVA;

        return $this;
    }

    /**
     * Get calculaIVA
     *
     * @return boolean 
     */
    public function getCalculaIVA() {
        return $this->calculaIVA;
    }

    /**
     * Set esExportacion
     *
     * @param boolean $esExportacion
     * @return Contrato
     */
    public function setEsExportacion($esExportacion) {
        $this->esExportacion = $esExportacion;

        return $this;
    }

    /**
     * Get esExportacion
     *
     * @return boolean 
     */
    public function getEsExportacion() {
        return $this->esExportacion;
    }

    /**
     * Add comprobantesVenta
     *
     * @param ComprobanteVenta $comprobantesVenta
     * @return ContratoVenta
     */
    public function addComprobantesVenta(ComprobanteVenta $comprobantesVenta) {

        $this->comprobantesVenta[] = $comprobantesVenta;

        return $this;
    }

    /**
     * Remove comprobantesVenta
     *
     * @param ComprobanteVenta $comprobantesVenta
     */
    public function removeComprobantesVenta(ComprobanteVenta $comprobantesVenta) {
        $this->comprobantesVenta->removeElement($comprobantesVenta);
    }

    /**
     * Get comprobantesVenta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantesVenta($recursivo = true) {

        $comprobantesVenta = array();

        if ($recursivo && $this->contratoOrigen != null) {

            $comprobantesVenta = array_merge($comprobantesVenta, $this->contratoOrigen->getComprobantesVenta($recursivo)->toArray());
        }

        return new ArrayCollection(array_merge($comprobantesVenta, $this->comprobantesVenta->toArray()));
    }

    /**
     * Set numeroOnabe
     *
     * @param string $numeroOnabe
     * @return ContratoVenta
     */
    public function setNumeroOnabe($numeroOnabe) {
        $this->numeroOnabe = $numeroOnabe;

        return $this;
    }

    /**
     * Get numeroOnabe
     *
     * @return string 
     */
    public function getNumeroOnabe() {
        return $this->numeroOnabe;
    }

    /**
     * Getter esContratoAlquiler
     * 
     * @return boolean
     */
    public function getEsContratoAlquiler() {
        return false;
    }

    /**
     * Getter esContratoVentaPlazo
     * 
     * @return boolean
     */
    public function getEsContratoVentaPlazo() {
        return false;
    }

    /**
     * Getter esContratoLocacionServicios
     * 
     * @return boolean
     */
    public function getEsContratoLocacionServicios() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsContratoServidumbreDePaso() {
        return false;
    }

    /**
     * Getter indicaNumeroInmueble
     * 
     * @return boolean
     */
    public function getIndicaNumeroInmueble() {
        return false;
    }

    /**
     * 
     * @return array
     */
    public function getCuponesGarantia() {

        $cuponesGarantia = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {
            // Si el comprobante es un cupon de garantia
            if ($comprobanteVenta->getEsCupon() && $comprobanteVenta->getEsCuponGarantia()) {
                $cuponesGarantia[] = $comprobanteVenta;
            }
        }

        return $cuponesGarantia;
    }

    /**
     * Get saldoPendienteFacturacion
     * 
     * @return type
     */
    public function getSaldoPendienteFacturacion() {

        $saldoPendienteFacturacion = 0;

        if ($this->getCiclosFacturacion() != null) {
            /* @var $cicloFacturacion CicloFacturacion */
            foreach ($this->getCiclosFacturacion() as $cicloFacturacion) {
                $saldoPendienteFacturacion += $cicloFacturacion->getCantidadFacturasPendientes() * $cicloFacturacion->getImporte();
            }
        }
        return $saldoPendienteFacturacion;
    }

    /**
     * 
     * @return type
     */
    public function getComprobantesModificanSaldo() {

        $comprobantesModificanSaldo = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {

            // Si el comprobante modifica el saldo del contrato
            if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::FACTURA //
                    || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO //
                    || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO) {
                $comprobantesModificanSaldo[] = $comprobanteVenta;
            }
        }

        return $comprobantesModificanSaldo;
    }

    /**
     * 
     * @return type
     */
    public function getComprobantesNoModificanSaldo() {

        $comprobantesNoModificanSaldo = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {

            // Si el comprobante NO modifica el saldo del contrato
            if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::CUPON //
                    || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
                $comprobantesNoModificanSaldo[] = $comprobanteVenta;
            }
        }

        return $comprobantesNoModificanSaldo;
    }

    /**
     * 
     * @return type
     */
    public function getComprobantesEnCuentaCorriente() {

        $comprobantesEnCuentaCorriente = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {

            // Si el comprobante debe aparecer en la cuenta corriente
            if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::FACTURA //
                    || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO //
                    || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES //
                    || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO || $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::CUPON) {
                if ((!$comprobanteVenta->getEsCupon()) || 
                        ($comprobanteVenta->getEsCupon() && 
                            ($comprobanteVenta->getEsCuponGarantia() 
                                || $comprobanteVenta->getEsCuponVentaPlazo() 
                                || $comprobanteVenta->getEsMigracionAabe()
                            )
                        )
                    ) {
                    $comprobantesEnCuentaCorriente [] = $comprobanteVenta;
                }
            }
        }

        return $comprobantesEnCuentaCorriente;
//        return usort($comprobantesEnCuentaCorriente, function($a, $b){
//            return $a->getFechaComprobante() > $b->getFechaComprobante(); 
//        });
    }

    /**
     * Get saldoPendienteCobro
     * 
     * @return type
     */
    //public function getSaldoPendienteCobro() {
    public function getSaldoPendienteCobro($fecha) {

        $saldo = 0;

        foreach ($this->getComprobantesEnCuentaCorriente() as $comprobanteVenta) {

            /* @var $comprobanteVenta ComprobanteVenta */
            $saldo += $comprobanteVenta->getSaldoHastaFechaContable($fecha);
        }

        return $saldo;
    }

    public function getContratoFinalizado() {

        $pendientes = 0;

        if ($this->getCiclosFacturacion() != null) {
            foreach ($this->getCiclosFacturacion() as $cicloFacturacion) {
                $pendientes += $cicloFacturacion->getCantidadFacturasPendientes();
            }
        }
        return $pendientes == 0;
    }

    /**
     * 
     * @return type
     */
    public function getImpTotConc($comprobante) {
        return 0;
    }

    /**
     * 
     * @return type
     */
    public function getImpOpEx($comprobante) {
        return 0;
    }

    /**
     * Set fechaDesocupacion
     *
     * @param \DateTime $fechaDesocupacion
     * @return Contrato
     */
    public function setFechaDesocupacion($fechaDesocupacion) {
        $this->fechaDesocupacion = $fechaDesocupacion;

        return $this;
    }

    /**
     * Get fechaDesocupacion
     *
     * @return \DateTime 
     */
    public function getFechaDesocupacion() {
        return $this->fechaDesocupacion;
    }

    /**
     * Get ciclosFacturacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCiclosFacturacionPendientes() {

        $estadoContrato = $this->getEstadoContrato();

        if ($estadoContrato->getCodigo() == ConstanteEstadoContrato::DESOCUPADO) {
            if ($this->getFechaDesocupacion() == null) {
                return new ArrayCollection();
            } else {
                return $this->ciclosFacturacion->filter(
                                function($entry) {
                            return $entry->getCantidadFacturasPendientes() > 0 && ($entry->getFechaInicio()->format('Ym') <= $this->getFechaDesocupacion()->format('Ym') || $entry->getFechaFin()->format('Ym') <= $this->getFechaDesocupacion()->format('Ym'));
                        }
                );
            }
        } else {
            return parent::getCiclosFacturacionPendientes();
        }
    }

}
