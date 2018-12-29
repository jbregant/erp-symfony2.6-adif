<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCategoriaContrato;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contrato
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="contrato")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "contrato" = "Contrato",
 *      "contrato_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria",
 *      "contrato_venta" = "ContratoVenta",
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
class Contrato extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var ClaseContrato
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\ClaseContrato")
     * @ORM\JoinColumn(name="id_clase_contrato", referencedColumnName="id", nullable=false)
     */
    protected $claseContrato;

    /**
     * @var CategoriaContrato
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato")
     * @ORM\JoinColumn(name="id_categoria_contrato", referencedColumnName="id", nullable=false)
     */
    protected $categoriaContrato;

    /**
     * @var EstadoContrato
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\EstadoContrato")
     * @ORM\JoinColumn(name="id_estado_contrato", referencedColumnName="id", nullable=false)
     */
    protected $estadoContrato;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=false)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date", nullable=false)
     */
    protected $fechaFin;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_contrato", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de contrato no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroContrato;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_carpeta", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de carpeta no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroCarpeta;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoMoneda
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\TipoMoneda")
     * @ORM\JoinColumn(name="id_tipo_moneda", referencedColumnName="id", nullable=false)
     */
    protected $tipoMoneda;

    /**
     * @var double
     * @ORM\Column(name="importe_total", type="decimal", precision=15, scale=2, nullable=false)
     */
    protected $importeTotal;

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\Contrato")
     * @ORM\JoinColumn(name="id_contrato_origen", referencedColumnName="id")
     */
    protected $contratoOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion", mappedBy="contrato", cascade={"all"})
     * @ORM\OrderBy({"fechaInicio" = "ASC"})
     */
    protected $ciclosFacturacion;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato", mappedBy="contrato", cascade={"all"})
     */
    protected $polizasSeguro;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ciclosFacturacion = new ArrayCollection();
        $this->polizasSeguro = new ArrayCollection();
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->numeroContrato;
    }

    /**
     * 
     */
    public function __clone() {
        $this->id = null;
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
     * Set claseContrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\ClaseContrato $claseContrato
     * @return Contrato
     */
    public function setClaseContrato(\ADIF\ContableBundle\Entity\Facturacion\ClaseContrato $claseContrato) {
        $this->claseContrato = $claseContrato;

        return $this;
    }

    /**
     * Get claseContrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\ClaseContrato 
     */
    public function getClaseContrato() {
        return $this->claseContrato;
    }

    /**
     * Set categoriaContrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato $categoriaContrato
     * @return Contrato
     */
    public function setCategoriaContrato(\ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato $categoriaContrato) {
        $this->categoriaContrato = $categoriaContrato;

        return $this;
    }

    /**
     * Get categoriaContrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato 
     */
    public function getCategoriaContrato() {
        return $this->categoriaContrato;
    }

    /**
     * Set estadoContrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\EstadoContrato $estadoContrato
     * @return Contrato
     */
    public function setEstadoContrato(\ADIF\ContableBundle\Entity\Facturacion\EstadoContrato $estadoContrato) {
        $this->estadoContrato = $estadoContrato;

        return $this;
    }

    /**
     * Get estadoContrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\EstadoContrato 
     */
    public function getEstadoContrato() {
        return $this->estadoContrato;
    }

    /**
     * Set contratoOrigen
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\Contrato $contratoOrigen
     * @return Contrato
     */
    public function setContratoOrigen(\ADIF\ContableBundle\Entity\Facturacion\Contrato $contratoOrigen = null) {
        $this->contratoOrigen = $contratoOrigen;

        return $this;
    }

    /**
     * Get contratoOrigen
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\Contrato 
     */
    public function getContratoOrigen() {
        return $this->contratoOrigen;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return Contrato
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
     * Add ciclosFacturacion
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $ciclosFacturacion
     * @return Contrato
     */
    public function addCiclosFacturacion(\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $ciclosFacturacion) {
        $this->ciclosFacturacion[] = $ciclosFacturacion;

        return $this;
    }

    /**
     * Remove ciclosFacturacion
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $ciclosFacturacion
     */
    public function removeCiclosFacturacion(\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $ciclosFacturacion) {
        $this->ciclosFacturacion->removeElement($ciclosFacturacion);
    }

    /**
     * Get ciclosFacturacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCiclosFacturacion() {
        return $this->ciclosFacturacion;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Contrato
     */
    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return Contrato
     */
    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin() {
        return $this->fechaFin;
    }

    /**
     * Set numeroContrato
     *
     * @param string $numeroContrato
     * @return Contrato
     */
    public function setNumeroContrato($numeroContrato) {
        $this->numeroContrato = $numeroContrato;

        return $this;
    }

    /**
     * Get numeroContrato
     *
     * @return string 
     */
    public function getNumeroContrato() {
        return $this->numeroContrato;
    }

    /**
     * Set numeroCarpeta
     *
     * @param string $numeroCarpeta
     * @return Contrato
     */
    public function setNumeroCarpeta($numeroCarpeta) {
        $this->numeroCarpeta = $numeroCarpeta;

        return $this;
    }

    /**
     * Get numeroCarpeta
     *
     * @return string 
     */
    public function getNumeroCarpeta() {
        return $this->numeroCarpeta;
    }

    /**
     * Set tipoMoneda
     *
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     * @return Contrato
     */
    public function setTipoMoneda(\ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda) {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return \ADIF\ContableBundle\Entity\TipoMoneda 
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * Set importeTotal
     *
     * @param string $importeTotal
     * @return Contrato
     */
    public function setImporteTotal($importeTotal) {
        $this->importeTotal = $importeTotal;

        return $this;
    }

    /**
     * Get importeTotal
     *
     * @return string 
     */
    public function getImporteTotal() {
        return $this->importeTotal;
    }

    /**
     * Add polizasSeguro
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato $polizasSeguro
     * @return Contrato
     */
    public function addPolizasSeguro(\ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato $polizasSeguro) {
        $this->polizasSeguro[] = $polizasSeguro;

        return $this;
    }

    /**
     * Remove polizasSeguro
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato $polizasSeguro
     */
    public function removePolizasSeguro(\ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato $polizasSeguro) {
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
     * 
     * @return type
     */
    private function getCantidadProrrogas() {
        $deep = $this->categoriaContrato->getCodigo() == ConstanteCategoriaContrato::PRORROGA ? 1 : 0;

        if ($this->contratoOrigen != null) {
            $deep += intval($this->contratoOrigen->getCantidadProrrogas());
        } else {
            $deep += 1;
        }

        return str_pad($deep, 2, '0', STR_PAD_LEFT);
    }

    /**
     * 
     * @return type
     */
    private function getNumeroContratoOriginal() {
        $numero = '';

        if ($this->contratoOrigen == null) {
            $numero = $this->numeroContrato;
        } else {
            $numero = $this->contratoOrigen->getNumeroContratoOriginal();
        }

        return $numero;
    }

    /**
     * 
     * @return type
     */
    public function getNumeroProrroga() {
        return $this->getNumeroContratoOriginal() . ' - ' . $this->getCantidadProrrogas();
    }

    /**
     * Get historico
     */
    public function getHistorico() {
        $historico = new ArrayCollection();
        $historico->add($this);

        if ($this->contratoOrigen != null) {
            foreach ($this->contratoOrigen->getHistorico() as $contrato) {
                $historico->add($contrato);
            }
        }

        return $historico;
    }

    /**
     * Get ciclosFacturacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCiclosFacturacionPendientes() {

        return $this->ciclosFacturacion->filter(
                        function($entry) {
                    $fecha_vencimiento = new \DateTime(date((new \DateTime())->format('Y-m') . '-' . $entry->getContrato()->getDiaVencimiento()));
                    //$fecha_vencimiento = new \DateTime(date((new \DateTime())->format('Y') . '-08-' . $entry->getContrato()->getDiaVencimiento()));
                    return $entry->getCantidadFacturasPendientes() > 0 && ($entry->getFechaInicio() <= $fecha_vencimiento || $entry->getFechaFin() <= $fecha_vencimiento);
                }
        );
    }

    /**
     * Get esEditableTotalidad
     *
     * @return boolean
     */
    public function getEsEditableTotalidad() {

        $editable = true;

        foreach ($this->ciclosFacturacion as $ciclo) {
            /* @var $ciclo CicloFacturacion */
            $editable &= ($ciclo->getCantidadFacturas() == $ciclo->getCantidadFacturasPendientes());
        }

        return $editable;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {
        return $this->tipoMoneda->getSimboloTipoMoneda();
    }

    /**
     * Get ciclosFacturacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPermiteGenerarComprobante() {

        $permiteGenerarComprobante = false;

        if ($this->getCiclosFacturacion() != null) {
            foreach ($this->getCiclosFacturacionPendientes() as $cicloFacturacion) {
                $fecha_vencimiento = new \DateTime(date((new \DateTime())->format('Y-m') . '-' . $cicloFacturacion->getContrato()->getDiaVencimiento()));
                //$fecha_vencimiento = new \DateTime(date((new \DateTime())->format('Y') . '-08-' . $cicloFacturacion->getContrato()->getDiaVencimiento()));
                /* @var $cicloFacturacion CicloFacturacion */
                $mes_siguiente_factura = $cicloFacturacion->getFechaInicio()->format('m') + ($cicloFacturacion->getCantidadFacturasEmitidas() * $cicloFacturacion->getCantidadUnidadTiempo() * $cicloFacturacion->getUnidadTiempo()->getCantidadMeses());
                $fecha_limite_factura = new \DateTime(date($cicloFacturacion->getFechaInicio()->format('Y-') . $mes_siguiente_factura . '-' . $cicloFacturacion->getContrato()->getDiaVencimiento()));
                $fecha_limite_ciclo = $cicloFacturacion->getFechaFin() > $fecha_vencimiento ? $fecha_vencimiento : $cicloFacturacion->getFechaFin();
                $permiteGenerarComprobante |= ($fecha_limite_factura <= $fecha_limite_ciclo);
            }
            return $permiteGenerarComprobante;
        } else {
            return true;
        }
    }

    /**
     * Get getSiguienteNumeroComprobante
     * 
     * @return type
     */
    public function getSiguienteNumeroComprobante() {

        $siguienteNumeroComprobante = 1;

        if ($this->getCiclosFacturacion() != null) {
            foreach ($this->getCiclosFacturacion() as $cicloFacturacion) {
                $siguienteNumeroComprobante += $cicloFacturacion->getCantidadFacturasEmitidas();
            }
        }
        return $siguienteNumeroComprobante;
    }

    /**
     * 
     * @param type $ejercicio
     * @return type
     */
    public function getImporteCiclosFacturacionByEjercicio($ejercicio) {

        $total = 0;

        foreach ($this->getCiclosFacturacion() as $cicloFacturacion) {

            /* @var $cicloFacturacion CicloFacturacion */
            $ejercicioCiclo = $cicloFacturacion->getFechaInicio()->format('Y');

            if ($ejercicio == $ejercicioCiclo) {

                $total += ($cicloFacturacion->getImporte() * $cicloFacturacion->getCantidadFacturas());
            }
        }

        return $total;
    }

    /**
     * 
     * @return type
     */
    public function getEjerciciosCiclosFacturacion() {

        $ejercicios = [];

        foreach ($this->getCiclosFacturacion() as $cicloFacturacion) {

            /* @var $cicloFacturacion CicloFacturacion */

            $ejercicios[] = $cicloFacturacion->getFechaInicio()->format('Y');
        }

        return array_unique($ejercicios);
    }

    /**
     * 
     * @return type
     */
    public function getIdContratoInicial() {

        if ($this->contratoOrigen == null) {
            return $this->id;
        } else {
            return $this->contratoOrigen->getIdContratoInicial();
        }
    }

}
