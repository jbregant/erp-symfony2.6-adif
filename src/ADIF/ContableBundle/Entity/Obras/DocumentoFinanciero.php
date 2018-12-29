<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentoFinanciero
 * 
 * @ORM\Table(name="documento_financiero")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\DocumentoFinancieroRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "documento_financiero" = "DocumentoFinanciero",
 *      "certificado_obra" = "CertificadoObra",
 *      "redeterminacion_obra" = "RedeterminacionObra",
 *      "anticipo_financiero" = "AnticipoFinanciero",
 *      "fondo_reparo" = "FondoReparo",
 *      "economia" = "Economia",
 *      "demasia" = "Demasia"
 * })
 */
class DocumentoFinanciero extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, nullable=false)
     */
    protected $numeroReferencia;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TipoDocumentoFinanciero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_documento_financiero", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoDocumentoFinanciero;

    /**
     * @ORM\ManyToOne(targetEntity="Tramo", inversedBy="documentosFinancieros")
     * @ORM\JoinColumn(name="id_tramo", referencedColumnName="id", nullable=false)
     */
    protected $tramo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_documento_financiero_inicio", type="datetime", nullable=false)
     */
    protected $fechaDocumentoFinancieroInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_documento_financiero_fin", type="datetime", nullable=false)
     */
    protected $fechaDocumentoFinancieroFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ingreso_adif", type="datetime", nullable=false)
     */
    protected $fechaIngresoADIF;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ingreso_gerencia_administracion", type="datetime", nullable=true)
     */
    protected $fechaIngresoGerenciaAdministracion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_remision_gerencia_administracion", type="datetime", nullable=true)
     */
    protected $fechaRemisionGerenciaAdministracion;

    /**
     * @var double
     * @ORM\Column(name="monto_sin_iva", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $montoSinIVA;

    /**
     * @var double
     * @ORM\Column(name="monto_iva", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $montoIVA;

    /**
     * @var double
     * @ORM\Column(name="monto_percepciones", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $montoPercepciones;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_aprobacion_tecnica", type="datetime", nullable=true)
     */
    protected $fechaAprobacionTecnica;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_anulacion", type="datetime", nullable=true)
     */
    protected $fechaAnulacion;

    /**
     * @var double
     * @ORM\Column(name="porcentaje_certificacion", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $porcentajeCertificacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="corresponde_pago", type="boolean", nullable=false)
     */
    protected $correspondePago;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=1000, nullable=true)
     */
    protected $observacion;

    /**
     * @ORM\OneToMany(targetEntity="DocumentoFinancieroArchivo", mappedBy="documentoFinanciero", cascade={"persist","remove"})
     */
    protected $archivos;

    /**
     * @ORM\OneToMany(targetEntity="ComprobanteObra", mappedBy="documentoFinanciero")
     * */
    protected $comprobantes;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="PolizaSeguroDocumentoFinanciero", mappedBy="documentoFinanciero", cascade={"all"})
     */
    protected $polizasSeguro;

    /**
     * Constructor
     */
    public function __construct() {
        $this->archivos = new ArrayCollection();
        $this->comprobantes = new ArrayCollection();
        $this->polizasSeguro = new ArrayCollection();

        $this->correspondePago = true;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->fechaDocumentoFinancieroInicio->format('d/m/Y');
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
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return DocumentoFinanciero
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }

    /**
     * Set tipoDocumentoFinanciero
     *
     * @param TipoDocumentoFinanciero $tipoDocumentoFinanciero
     * @return DocumentoFinanciero
     */
    public function setTipoDocumentoFinanciero(TipoDocumentoFinanciero $tipoDocumentoFinanciero) {
        $this->tipoDocumentoFinanciero = $tipoDocumentoFinanciero;

        return $this;
    }

    /**
     * Get tipoDocumentoFinanciero
     *
     * @return TipoDocumentoFinanciero 
     */
    public function getTipoDocumentoFinanciero() {
        return $this->tipoDocumentoFinanciero;
    }

    /**
     * Set tramo
     *
     * @param Tramo $tramo
     * @return DocumentoFinanciero
     */
    public function setTramo(Tramo $tramo) {
        $this->tramo = $tramo;

        return $this;
    }

    /**
     * Get tramo
     *
     * @return Tramo 
     */
    public function getTramo() {
        return $this->tramo;
    }

    /**
     * Set fechaDocumentoFinancieroInicio
     *
     * @param \DateTime $fechaDocumentoFinancieroInicio
     * @return DocumentoFinanciero
     */
    public function setFechaDocumentoFinancieroInicio($fechaDocumentoFinancieroInicio) {
        $this->fechaDocumentoFinancieroInicio = $fechaDocumentoFinancieroInicio;

        return $this;
    }

    /**
     * Get fechaDocumentoFinancieroInicio
     *
     * @return \DateTime 
     */
    public function getFechaDocumentoFinancieroInicio() {
        return $this->fechaDocumentoFinancieroInicio;
    }

    /**
     * Set fechaDocumentoFinancieroFin
     *
     * @param \DateTime $fechaDocumentoFinancieroFin
     * @return DocumentoFinanciero
     */
    public function setFechaDocumentoFinancieroFin($fechaDocumentoFinancieroFin) {
        $this->fechaDocumentoFinancieroFin = $fechaDocumentoFinancieroFin;

        return $this;
    }

    /**
     * Get fechaDocumentoFinancieroFin
     *
     * @return \DateTime 
     */
    public function getFechaDocumentoFinancieroFin() {
        return $this->fechaDocumentoFinancieroFin;
    }

    /**
     * Set fechaIngresoADIF
     *
     * @param \DateTime $fechaIngresoADIF
     * @return DocumentoFinanciero
     */
    public function setFechaIngresoADIF($fechaIngresoADIF) {
        $this->fechaIngresoADIF = $fechaIngresoADIF;

        return $this;
    }

    /**
     * Get fechaIngresoADIF
     *
     * @return \DateTime 
     */
    public function getFechaIngresoADIF() {
        return $this->fechaIngresoADIF;
    }

    /**
     * Set fechaIngresoGerenciaAdministracion
     *
     * @param \DateTime $fechaIngresoGerenciaAdministracion
     * @return DocumentoFinanciero
     */
    public function setFechaIngresoGerenciaAdministracion($fechaIngresoGerenciaAdministracion) {
        $this->fechaIngresoGerenciaAdministracion = $fechaIngresoGerenciaAdministracion;

        return $this;
    }

    /**
     * Get fechaIngresoGerenciaAdministracion
     *
     * @return \DateTime 
     */
    public function getFechaIngresoGerenciaAdministracion() {
        return $this->fechaIngresoGerenciaAdministracion;
    }

    /**
     * Set fechaRemisionGerenciaAdministracion
     *
     * @param \DateTime $fechaRemisionGerenciaAdministracion
     * @return DocumentoFinanciero
     */
    public function setFechaRemisionGerenciaAdministracion($fechaRemisionGerenciaAdministracion) {
        $this->fechaRemisionGerenciaAdministracion = $fechaRemisionGerenciaAdministracion;

        return $this;
    }

    /**
     * Get fechaRemisionGerenciaAdministracion
     *
     * @return \DateTime 
     */
    public function getFechaRemisionGerenciaAdministracion() {
        return $this->fechaRemisionGerenciaAdministracion;
    }

    /**
     * Set montoSinIVA
     *
     * @param string $montoSinIVA
     * @return DocumentoFinanciero
     */
    public function setMontoSinIVA($montoSinIVA) {
        $this->montoSinIVA = $montoSinIVA;

        return $this;
    }

    /**
     * Get montoSinIVA
     *
     * @return string 
     */
    public function getMontoSinIVA() {
        return $this->montoSinIVA;
    }

    /**
     * Set montoIVA
     *
     * @param string $montoIVA
     * @return DocumentoFinanciero
     */
    public function setMontoIVA($montoIVA) {
        $this->montoIVA = $montoIVA;

        return $this;
    }

    /**
     * Get montoIVA
     *
     * @return string 
     */
    public function getMontoIVA() {
        return $this->montoIVA;
    }

    /**
     * Set montoPercepciones
     *
     * @param string $montoPercepciones
     * @return DocumentoFinanciero
     */
    public function setMontoPercepciones($montoPercepciones) {
        $this->montoPercepciones = $montoPercepciones;

        return $this;
    }

    /**
     * Get montoPercepciones
     *
     * @return string 
     */
    public function getMontoPercepciones() {
        return $this->montoPercepciones;
    }

    /**
     * Set fechaAprobacionTecnica
     *
     * @param \DateTime $fechaAprobacionTecnica
     * @return DocumentoFinanciero
     */
    public function setFechaAprobacionTecnica($fechaAprobacionTecnica) {
        $this->fechaAprobacionTecnica = $fechaAprobacionTecnica;

        return $this;
    }

    /**
     * Get fechaAprobacionTecnica
     *
     * @return \DateTime 
     */
    public function getFechaAprobacionTecnica() {
        return $this->fechaAprobacionTecnica;
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return DocumentoFinanciero
     */
    public function setFechaAnulacion($fechaAnulacion) {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion() {
        return $this->fechaAnulacion;
    }

    /**
     * Set porcentajeCertificacion
     *
     * @param string $porcentajeCertificacion
     * @return DocumentoFinanciero
     */
    public function setPorcentajeCertificacion($porcentajeCertificacion) {
        $this->porcentajeCertificacion = $porcentajeCertificacion;

        return $this;
    }

    /**
     * Get porcentajeCertificacion
     *
     * @return string 
     */
    public function getPorcentajeCertificacion() {
        return $this->porcentajeCertificacion;
    }

    /**
     * Set correspondePago
     *
     * @param boolean $correspondePago
     * @return DocumentoFinanciero
     */
    public function setCorrespondePago($correspondePago) {
        $this->correspondePago = $correspondePago;

        return $this;
    }

    /**
     * Get correspondePago
     *
     * @return boolean 
     */
    public function getCorrespondePago() {
        return $this->correspondePago;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return DocumentoFinanciero
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
     * Add archivo
     *
     * @param DocumentoFinancieroArchivo $archivo
     * @return DocumentoFinanciero
     */
    public function addArchivo(DocumentoFinancieroArchivo $archivo) {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove archivo
     *
     * @param DocumentoFinancieroArchivo $archivo
     */
    public function removeArchivo(DocumentoFinancieroArchivo $archivo) {
        $this->archivos->removeElement($archivo);
    }

    /**
     * Get archivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchivos() {
        return $this->archivos;
    }

    /**
     * Add comprobantes
     *
     * @param ComprobanteObra $comprobantes
     * @return DocumentoFinanciero
     */
    public function addComprobante(ComprobanteObra $comprobantes) {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param ComprobanteObra $comprobantes
     */
    public function removeComprobante(ComprobanteObra $comprobantes) {
        $this->comprobantes->removeElement($comprobantes);
    }

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        return $this->comprobantes;
    }

    /**
     * Add polizasSeguro
     *
     * @param PolizaSeguroDocumentoFinanciero $polizasSeguro
     * @return DocumentoFinanciero
     */
    public function addPolizasSeguro(PolizaSeguroDocumentoFinanciero $polizasSeguro) {
        $this->polizasSeguro[] = $polizasSeguro;

        return $this;
    }

    /**
     * Remove polizasSeguro
     *
     * @param PolizaSeguroDocumentoFinanciero $polizasSeguro
     */
    public function removePolizasSeguro(PolizaSeguroDocumentoFinanciero $polizasSeguro) {
        $this->polizasSeguro->removeElement($polizasSeguro);
    }

    /**
     * Get polizasSeguro
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPolizasSeguro() {
        return $this->polizasSeguro;
    }

    /**
     * Get comprobantesSinAnular
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantesSinAnular() {

        $comprobantes = [];

        foreach ($this->comprobantes as $comprobante) {

            /* @var $comprobante ComprobanteObra */

            if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {

                $ordenPago = $comprobante->getOrdenPago();

                if ($ordenPago == null || ($ordenPago != null && $ordenPago->getEstadoOrdenPago() != ConstanteEstadoOrdenPago::ESTADO_ANULADA)) {

                    $comprobantes[] = $comprobante;
                }
            }
        }

        return $comprobantes;
    }

    /**
     * 
     * @return int
     */
    public function getMontoFondoReparo() {
        return 0;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsCertificadoObra() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsRedeterminacionObra() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsAnticipoFinanciero() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsFondoReparo() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsEconomia() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsDemasia() {
        return false;
    }

    /**
     * montoTotalDocumentoFinanciero
     * 
     * @return type
     */
    public function getMontoTotalDocumentoFinanciero() {

        return $this->getMontoSinIVA() + $this->getMontoIVA() + $this->getMontoPercepciones() + $this->getMontoFondoReparo();
    }

    /**
     * 
     * @return type
     */
    public function getMontoTotalBruto() {
        return $this->getMontoSinIVA() + $this->getMontoIVA() + $this->getMontoPercepciones();
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return null;
    }

    /**
     * Get comprobante
     *
     * @return string 
     */
    public function getComprobante() {

        if ($this->comprobantes->isEmpty()) {
            return null;
        } else {
            return $this->comprobantes->first();
        }
    }

    /**
     * Get ordenPago
     *
     * @return string 
     */
    public function getOrdenPago() {

        if ($this->getComprobante() == null) {
            return null;
        } else {
            return $this->getComprobante()->getOrdenPago();
        }
    }

    /**
     * Get ordenPago
     *
     * @return string 
     */
    public function getOrdenesPago() {

        $ordenesPago = [];

        foreach ($this->comprobantes as $comprobante) {

            /* @var $comprobante ComprobanteObra */

            if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {

                $ordenPago = $comprobante->getOrdenPago();

                if ($ordenPago != null && $ordenPago->getEstadoOrdenPago() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {

                    if (!in_array($ordenPago, $ordenesPago, true)) {
                        $ordenesPago[] = $ordenPago;
                    }
                }
            }
        }

        return $ordenesPago;
    }

    /**
     * Get pago
     *
     * @return string 
     */
    public function getPagos() {

        $pagos = [];

        foreach ($this->getOrdenesPago() as $ordenPago) {

            $pagos[] = $ordenPago->getPagoOrdenPago();
        }

        return $pagos;
    }

    /**
     * Get saldo
     * 
     * @return type
     */
    public function getSaldo() {

        $saldo = $this->getMontoTotalBruto();

        foreach ($this->comprobantes as $comprobante) {

            if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {
                if (!$comprobante->getEsNotaCredito()) {
                    $saldo -= $comprobante->getTotal();
                } else {
                    $saldo += $comprobante->getTotal();
                }
            }
        }

        return $saldo;
    }

    /**
     * Get esEditable
     */
    public function getEsEditable() {

        $noEsEditable = false;

        foreach ($this->comprobantes as $comprobante) {

            if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {

                $noEsEditable = true;

                break;
            }
        }

        return !$noEsEditable;
    }

    /**
     * Get esAnulable
     */
    public function getEsAnulable() {

        $esAnulable = !$this->getEstaAnulado();

        // Si no está anulado
        if ($esAnulable) {

            foreach ($this->comprobantes as $comprobante) {

                if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {

                    $esAnulable = false;

                    break;
                }
            }
        }

        return $esAnulable;
    }

    /**
     * 
     * @return type
     */
    public function getEstaAnulado() {

        return $this->fechaAnulacion != null;
    }

}
