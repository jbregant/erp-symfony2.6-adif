<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoFuenteFinanciamiento;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoTramo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tramo
 *
 * @author Darío Rapetti
 * created 29/11/2014
 * 
 * @ORM\Table(name="obras_tramo")
 * @ORM\Entity 
 */
class Tramo extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var LicitacionObra
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\LicitacionObra", inversedBy="tramos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_licitacion", referencedColumnName="id", nullable=false)
     * })
     */
    protected $licitacion;

    /**
     * @var EstadoTramo
     *
     * @ORM\ManyToOne(targetEntity="EstadoTramo")
     * @ORM\JoinColumn(name="id_estado_tramo", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoTramo;

    /**
     * @var CategoriaObra
     *
     * @ORM\ManyToOne(targetEntity="CategoriaObra")
     * @ORM\JoinColumn(name="id_categoria_obra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $categoriaObra;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoObra")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_obra", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoObra;

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var double
     * @ORM\Column(name="total_contrato", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    protected $totalContrato;

    /**
     * @var intenger
     * 
     * @ORM\Column(name="plazo_dias", type="integer", nullable=true)
     */
    protected $plazoDias;

    /**
     * @var double
     * @ORM\Column(name="porcentaje_anticipo_inicial", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeAnticipoInicial;

    /**
     * @var double
     * @ORM\Column(name="porcentaje_avance_inicial", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeAvanceInicial;

    /**
     * @var double
     * @ORM\Column(name="porcentaje_fondo_reparo", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $porcentajeFondoReparo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_firma_contrato", type="datetime", nullable=false)
     */
    protected $fechaFirmaContrato;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_recepcion_provisoria", type="datetime", nullable=true)
     */
    protected $fechaRecepcionProvisoria;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_recepcion_definitiva", type="datetime", nullable=true)
     */
    protected $fechaRecepcionDefinitiva;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="PolizaSeguroObra", mappedBy="tramo", cascade={"all"})
     */
    protected $polizasSeguro;

    /**
     * @ORM\OneToMany(targetEntity="DocumentoFinanciero", mappedBy="tramo")
     * */
    protected $documentosFinancieros;

    /**
     * @ORM\OneToMany(targetEntity="FuenteFinanciamientoTramo", mappedBy="tramo", cascade={"persist", "remove"})
     */
    protected $fuentesFinanciamiento;

    /**
     * Constructor
     */
    public function __construct() {

        $this->totalContrato = 0;

        $this->porcentajeAnticipoInicial = 0;
        $this->porcentajeAvanceInicial = 0;
        $this->porcentajeFondoReparo = 0;

        $this->polizasSeguro = new ArrayCollection();
        $this->documentosFinancieros = new ArrayCollection();
        $this->fuentesFinanciamiento = new ArrayCollection();
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->licitacion . " - " . $this->descripcion;
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
     * Set licitacion
     *
     * @param \ADIF\ContableBundle\Entity\LicitacionObra $licitacion
     * @return Tramo
     */
    public function setLicitacion(\ADIF\ContableBundle\Entity\LicitacionObra $licitacion) {
        $this->licitacion = $licitacion;

        return $this;
    }

    /**
     * Get licitacion
     *
     * @return \ADIF\ContableBundle\Entity\LicitacionObra
     */
    public function getLicitacion() {
        return $this->licitacion;
    }

    /**
     * Set categoriaObra
     *
     * @param CategoriaObra $categoriaObra
     * @return Tramo
     */
    public function setCategoriaObra(CategoriaObra $categoriaObra) {
        $this->categoriaObra = $categoriaObra;

        return $this;
    }

    /**
     * Get categoriaObra
     *
     * @return CategoriaObra
     */
    public function getCategoriaObra() {
        return $this->categoriaObra;
    }

    /**
     * Set estadoTramo
     *
     * @param EstadoTramo $estadoTramo
     * @return Tramo
     */
    public function setEstadoTramo(EstadoTramo $estadoTramo) {
        $this->estadoTramo = $estadoTramo;

        return $this;
    }

    /**
     * Get estadoTramo
     *
     * @return EstadoTramo 
     */
    public function getEstadoTramo() {
        return $this->estadoTramo;
    }

    /**
     * Set tipoObra
     *
     * @param \ADIF\ContableBundle\Entity\TipoObra $tipoObra
     * @return Tramo
     */
    public function setTipoObra(TipoObra $tipoObra) {
        $this->tipoObra = $tipoObra;

        return $this;
    }

    /**
     * Get tipoObra
     *
     * @return TipoObra 
     */
    public function getTipoObra() {
        return $this->tipoObra;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return Tramo
     */
    public function setIdProveedor($idProveedor) {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor() {
        return $this->idProveedor;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor) {

        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } //.
        else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Tramo
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
     * Set totalContrato
     *
     * @param string $totalContrato
     * @return Tramo
     */
    public function setTotalContrato($totalContrato) {
        $this->totalContrato = $totalContrato;

        return $this;
    }

    /**
     * Get totalContrato
     * 
     * @param type $conRedeterminaciones
     * @return type
     */
    public function getTotalContrato($conRedeterminaciones = true, $conEconomiasYDemasias = true) {

        $totalContrato = $this->totalContrato;

        if ($conRedeterminaciones || $conEconomiasYDemasias) {

            foreach ($this->documentosFinancieros as $documentoFinanciero) {

                if ($conRedeterminaciones && !$documentoFinanciero->getEstaAnulado() && $documentoFinanciero->getEsRedeterminacionObra()) {
                    $totalContrato += $documentoFinanciero->getMontoTotalDocumentoFinanciero();
                }

                if ($conEconomiasYDemasias && !$documentoFinanciero->getEstaAnulado() && $documentoFinanciero->getEsEconomia()) {
                    $totalContrato -= $documentoFinanciero->getMontoTotalDocumentoFinanciero();
                }

                if ($conEconomiasYDemasias && !$documentoFinanciero->getEstaAnulado() && $documentoFinanciero->getEsDemasia()) {
                    $totalContrato += $documentoFinanciero->getMontoTotalDocumentoFinanciero();
                }
            }
        }

        return $totalContrato;
    }

    /**
     * Set plazoDias
     *
     * @param integer $plazoDias
     * @return Tramo
     */
    public function setPlazoDias($plazoDias) {
        $this->plazoDias = $plazoDias;

        return $this;
    }

    /**
     * Get plazoDias
     *
     * @return integer 
     */
    public function getPlazoDias() {
        return $this->plazoDias;
    }

    /**
     * Set porcentajeAnticipoInicial
     *
     * @param string $porcentajeAnticipoInicial
     * @return Tramo
     */
    public function setPorcentajeAnticipoInicial($porcentajeAnticipoInicial) {
        $this->porcentajeAnticipoInicial = $porcentajeAnticipoInicial;

        return $this;
    }

    /**
     * Get porcentajeAnticipoInicial
     *
     * @return string 
     */
    public function getPorcentajeAnticipoInicial() {
        return $this->porcentajeAnticipoInicial;
    }

    /**
     * Set porcentajeAvanceInicial
     *
     * @param string $porcentajeAvanceInicial
     * @return Tramo
     */
    public function setPorcentajeAvanceInicial($porcentajeAvanceInicial) {
        $this->porcentajeAvanceInicial = $porcentajeAvanceInicial;

        return $this;
    }

    /**
     * Get porcentajeAvanceInicial
     *
     * @return string 
     */
    public function getPorcentajeAvanceInicial() {
        return $this->porcentajeAvanceInicial;
    }

    /**
     * Set porcentajeFondoReparo
     *
     * @param string $porcentajeFondoReparo
     * @return Tramo
     */
    public function setPorcentajeFondoReparo($porcentajeFondoReparo) {
        $this->porcentajeFondoReparo = $porcentajeFondoReparo;

        return $this;
    }

    /**
     * Get porcentajeFondoReparo
     *
     * @return string 
     */
    public function getPorcentajeFondoReparo() {
        return $this->porcentajeFondoReparo;
    }

    /**
     * Set fechaFirmaContrato
     *
     * @param \DateTime $fechaFirmaContrato
     * @return Tramo
     */
    public function setFechaFirmaContrato($fechaFirmaContrato) {
        $this->fechaFirmaContrato = $fechaFirmaContrato;

        return $this;
    }

    /**
     * Get fechaFirmaContrato
     *
     * @return \DateTime 
     */
    public function getFechaFirmaContrato() {
        return $this->fechaFirmaContrato;
    }

    /**
     * Set fechaRecepcionProvisoria
     *
     * @param \DateTime $fechaRecepcionProvisoria
     * @return Tramo
     */
    public function setFechaRecepcionProvisoria($fechaRecepcionProvisoria) {
        $this->fechaRecepcionProvisoria = $fechaRecepcionProvisoria;

        return $this;
    }

    /**
     * Get fechaRecepcionProvisoria
     *
     * @return \DateTime 
     */
    public function getFechaRecepcionProvisoria() {
        return $this->fechaRecepcionProvisoria;
    }

    /**
     * Set fechaRecepcionDefinitiva
     *
     * @param \DateTime $fechaRecepcionDefinitiva
     * @return Tramo
     */
    public function setFechaRecepcionDefinitiva($fechaRecepcionDefinitiva) {
        $this->fechaRecepcionDefinitiva = $fechaRecepcionDefinitiva;

        return $this;
    }

    /**
     * Get fechaRecepcionDefinitiva
     *
     * @return \DateTime 
     */
    public function getFechaRecepcionDefinitiva() {
        return $this->fechaRecepcionDefinitiva;
    }

    /**
     * Add polizasSeguro
     *
     * @param PolizaSeguroObra $polizasSeguro
     * @return Tramo
     */
    public function addPolizasSeguro(PolizaSeguroObra $polizasSeguro) {
        $this->polizasSeguro[] = $polizasSeguro;

        return $this;
    }

    /**
     * Remove polizasSeguro
     *
     * @param PolizaSeguroObra $polizasSeguro
     */
    public function removePolizasSeguro(PolizaSeguroObra $polizasSeguro) {
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
     * Add documentosFinancieros
     *
     * @param DocumentoFinanciero $documentosFinancieros
     * @return Tramo
     */
    public function addDocumentosFinanciero(DocumentoFinanciero $documentosFinancieros) {
        $this->documentosFinancieros[] = $documentosFinancieros;

        return $this;
    }

    /**
     * Remove documentosFinancieros
     *
     * @param DocumentoFinanciero $documentosFinancieros
     */
    public function removeDocumentosFinanciero(DocumentoFinanciero $documentosFinancieros) {
        $this->documentosFinancieros->removeElement($documentosFinancieros);
    }

    /**
     * Get documentosFinancieros
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocumentosFinancieros() {
        return $this->documentosFinancieros;
    }

    /**
     * Get importeTotalAnticipo
     */
    public function getImporteTotalAnticipo() {

        return 0;
    }

    /**
     * Get porcentajeTotalCertificado
     */
    public function getPorcentajeTotalCertificado() {

        $porcentajeTotalCertificado = $this->porcentajeAvanceInicial;

        if (!$this->documentosFinancieros->isEmpty()) {

            foreach ($this->documentosFinancieros as $documentoFinanciero) {

                if (!$documentoFinanciero->getEstaAnulado() && $documentoFinanciero->getEsCertificadoObra()) {

                    $porcentajeTotalCertificado = $documentoFinanciero->getPorcentajeCertificacion();
                }
            }
        }

        return $porcentajeTotalCertificado;
    }

    /**
     * Get importeTotalCertificado
     */
    public function getImporteTotalCertificado() {

        return 0;
    }

    /**
     * Get esEditable
     * 
     * @return boolean
     */
    public function getEsEditable() {

        return $this->estadoTramo->getCodigo() != ConstanteEstadoTramo::ESTADO_FINALIZADO;
    }

    /**
     * Get esEliminable
     * 
     * @return boolean
     */
    public function getEsEliminable() {

        $noEstaFinalizado = $this->estadoTramo->getCodigo() != ConstanteEstadoTramo::ESTADO_FINALIZADO;

        return $this->documentosFinancieros->isEmpty() && $noEstaFinalizado;
    }

    /**
     * Get saldo
     * 
     * @return type
     */
    public function getSaldo() {

        $saldo = $this->getTotalContrato($conRedeterminaciones = true, $conEconomiasYDemasias = true);

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado()) {
				
                if (!$documentoFinanciero->getEsRedeterminacionObra() && !$documentoFinanciero->getEsDemasia() && !$documentoFinanciero->getEsEconomia() ) {
					// Todo lo que no sea rederminacion, demasia y economia va a restar del saldo del renglon
					$saldo -= $documentoFinanciero->getMontoSinIVA();
                }
            }
        }
        
        return $saldo;
    }

    /**
     * Get saldoFinanciero
     * 
     * @param type $fechaFin
     * @return type
     */
    public function getSaldoFinanciero($fechaFin = null) {

        $saldoFinanciero = $this->getTotalContrato(true);

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado()) {

                foreach ($documentoFinanciero->getComprobantes() as $comprobante) {

                    if ($comprobante->getOrdenPago() != null && $comprobante->getOrdenPago()->getNumeroOrdenPago() != null) {
                        
                        $esNotaCredito = $comprobante->getEsNotaCredito();

                        if ($fechaFin == null || ($fechaFin != null && $comprobante->getOrdenPago()->getFechaContable() <= $fechaFin)) {

                            $saldoFinanciero -= (!$esNotaCredito) ? $comprobante->getTotalNeto() : $comprobante->getTotalNeto() * -1;
                        }
                    }
                }
            }
        }

        return $saldoFinanciero;
    }

    /**
     * Get montoFondoReparo
     * 
     * @return type
     */
    function getMontoFondoReparo() {

        $montoFondoReparo = 0;

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado()) {

                $montoFondoReparo += $documentoFinanciero->getMontoFondoReparo();
            }
        }

        return $montoFondoReparo;
    }

    /**
     * Get saldoFondoReparo
     * 
     * @return type
     */
    public function getSaldoFondoReparo() {

        $saldoFondoReparo = $this->getMontoFondoReparo();

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado() && $documentoFinanciero->getEsFondoReparo()) {

                $saldoFondoReparo -= $documentoFinanciero->getMontoSinIVA();
            }
        }

        return $saldoFondoReparo;
    }

    /**
     * 
     * @return type
     */
    public function getSaldoTotalDocumentosFinancieros() {

        $saldo = 0;

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado()) {

                $saldo += $documentoFinanciero->getSaldo();
            }
        }

        return $saldo;
    }

    /**
     * Get saldoFacturable
     * 
     * @return type
     */
    public function getSaldoFacturable() {

        $saldoFacturable = $this->getTotalContrato(true);

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado()) {

                foreach ($documentoFinanciero->getComprobantes() as $comprobante) {


                    $saldoFacturable -= $comprobante->getTotalNeto();
                }
            }
        }

        return $saldoFacturable;
    }

    /**
     * 
     * @return type
     */
    public function getMontoActivado() {

        $montoActivado = 0;

        foreach ($this->documentosFinancieros as $documentoFinanciero) {

            if (!$documentoFinanciero->getEstaAnulado() &&
                    ($documentoFinanciero->getEsCertificadoObra() ||
                    $documentoFinanciero->getEsRedeterminacionObra() ||
                    $documentoFinanciero->getEsFondoReparo())) {

                foreach ($documentoFinanciero->getComprobantesSinAnular() as $comprobante) {

                    /* @var $comprobante ComprobanteObra */

                    $montoActivado += $comprobante->getImporteTotalNeto();
                }
            }
        }

        return $montoActivado;
    }

    /**
     * 
     * @param type $ejercicio
     * @return type
     */
    public function getImporteByEjercicio($ejercicio) {

        $cantidadDiasEnEjercicio = 0;

        $fechaInicio = clone $this->fechaFirmaContrato;

        $fechaFin = $fechaInicio->add(new \DateInterval('P' . $this->plazoDias . 'D'));

        $ejercicioInicial = $this->fechaFirmaContrato->format('Y');

        $ejercicioFinal = $fechaFin->format('Y');

        // Si la fecha de inicio se encuentra dentro del ejercicio
        if ($ejercicioInicial == $ejercicio) {

            // Si la fecha de fin se encuentra dentro del ejercicio
            if ($ejercicioFinal == $ejercicio) {
                $cantidadDiasEnEjercicio = $this->plazoDias;
            }
            // Sino, si la fecha de fin se encuentra en otro ejercicio
            else {

                $fechaFinDate = new \DateTime($ejercicio . '-12-31');

                $cantidadDiasEnEjercicio = $this->fechaFirmaContrato->diff($fechaFinDate)->format("%a");
            }
        }
        // Sino, si la fecha de fin se encuentra dentro del ejercicio
        elseif ($ejercicioFinal == $ejercicio) {

            $fechaInicioDate = new \DateTime($ejercicio . '-01-01');

            $cantidadDiasEnEjercicio = $fechaInicioDate->diff($fechaFin)->format("%a") + 1;
        }
        // Sino
        else {

            $cantidadDiasEnEjercicio = date('L', mktime(1, 1, 1, 1, 1, $ejercicio)) ? 366 : 365;
        }

        $porcentajeDiasEnEjercicio = $cantidadDiasEnEjercicio * 100 / $this->plazoDias;

        $importe = $porcentajeDiasEnEjercicio * $this->totalContrato / 100;

        return $importe;
    }

    /**
     * 
     * @return type
     */
    public function getEjercicios() {

        $ejercicios = [];

        $fechaInicio = clone $this->fechaFirmaContrato;

        $fechaFin = $fechaInicio->add(new \DateInterval('P' . $this->plazoDias . 'D'));

        $ejercicioInicial = $this->fechaFirmaContrato->format('Y');

        $ejercicioFinal = $fechaFin->format('Y');

        $cantidadEjercicios = ((int) $ejercicioFinal - (int) $ejercicioInicial) + 1;

        for ($index = 0; $index < $cantidadEjercicios; $index++) {

            $ejercicios[] = $ejercicioInicial + $index;
        }

        return array_unique($ejercicios);
    }

    /**
     * Add fuentesFinanciamiento
     *
     * @param FuenteFinanciamientoTramo $fuentesFinanciamiento
     *
     * @return Tramo
     */
    public function addFuentesFinanciamiento(FuenteFinanciamientoTramo $fuentesFinanciamiento) {
        $this->fuentesFinanciamiento[] = $fuentesFinanciamiento;

        return $this;
    }

    /**
     * Remove fuentesFinanciamiento
     *
     * @param FuenteFinanciamientoTramo $fuentesFinanciamiento
     */
    public function removeFuentesFinanciamiento(FuenteFinanciamientoTramo $fuentesFinanciamiento) {
        $this->fuentesFinanciamiento->removeElement($fuentesFinanciamiento);
    }

    /**
     * Get fuentesFinanciamiento
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFuentesFinanciamiento() {
        return $this->fuentesFinanciamiento;
    }

    /**
     * Get tieneFuenteCAF
     *
     * @return boolean
     */
    public function getTieneFuenteCAF() {

        foreach ($this->fuentesFinanciamiento as $fuenteFinanciamiento) {
            if ($fuenteFinanciamiento->getFuenteFinanciamiento()->getCodigo() == ConstanteCodigoFuenteFinanciamiento::CODIGO_CAF) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get cuentaContable
     *
     */
    public function getCuentaContable() {

        $cuentaContable = null;

        if (!$this->fuentesFinanciamiento->isEmpty()) {

            if ($this->getTieneFuenteCAF()) {

                foreach ($this->fuentesFinanciamiento as $fuenteFinanciamientoTramo) {

                    if ($fuenteFinanciamientoTramo->getFuenteFinanciamiento()->getCodigo() == ConstanteCodigoFuenteFinanciamiento::CODIGO_CAF) {

                        $cuentaContable = $fuenteFinanciamientoTramo->getFuenteFinanciamiento()->getCuentaContable();

                        break;
                    }
                }
            }
        }

        return $cuentaContable;
    }

}
