<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Facturacion\Contrato;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContratoConsultoria
 *
 * @author Manuel Becerra
 * created 04/03/2015
 * 
 * @ORM\Table(name="contrato_consultoria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ContratoConsultoriaRepository")
 */
class ContratoConsultoria extends Contrato implements BaseAuditable {

    /**
     * @ORM\Column(name="id_consultor", type="integer", nullable=false)
     */
    protected $idConsultor;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor
     */
    protected $consultor;

    /**
     * @ORM\Column(name="id_area", type="integer", nullable=true)
     */
    protected $idArea;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Area
     */
    protected $area;

    /**
     * @ORM\Column(name="id_gerencia", type="integer", nullable=true)
     */
    protected $idGerencia;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Gerencia
     */
    protected $gerencia;

    /**
     * @ORM\Column(name="id_subgerencia", type="integer", nullable=true)
     */
    protected $idSubgerencia;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Subgerencia
     */
    protected $subgerencia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esHonorarioProfesional", type="boolean", nullable=false)
     */
    protected $esHonorarioProfesional;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ComprobanteConsultoria", mappedBy="contrato", cascade={"all"})
     * @ORM\OrderBy({"fechaComprobante" = "ASC", "numero" = "ASC"})
     */
    protected $comprobantesConsultoria;

    /**
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\AnticipoContratoConsultoria", mappedBy="contrato")
     */
    protected $anticipos;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->esHonorarioProfesional = true;

        $this->comprobantesConsultoria = new ArrayCollection();
        $this->anticipos = new ArrayCollection();
    }

    /**
     * 
     * @return type
     */
    public function getIdConsultor() {
        return $this->idConsultor;
    }

    /**
     * 
     * @param type $idConsultor
     * @return type
     */
    public function setIdConsultor($idConsultor) {
        $this->idConsultor = $idConsultor;

        return $this;
    }

    /**
     * Set area
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor $consultor
     * @return ContratoConsultoria
     */
    public function setConsultor(\ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor $consultor) {

        if (null != $consultor) {
            $this->idConsultor = $consultor->getId();
        } //.
        else {
            $this->idConsultor = null;
        }

        $this->consultor = $consultor;

        return $this;
    }

    /**
     * Get area
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor 
     */
    public function getConsultor() {
        return $this->consultor;
    }

    /**
     * 
     * @return type
     */
    public function getIdArea() {
        return $this->idArea;
    }

    /**
     * Set area
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Area $area
     * @return ContratoConsultoria
     */
    public function setArea(\ADIF\RecursosHumanosBundle\Entity\Area $area) {

        if (null != $area) {
            $this->idArea = $area->getId();
        } //.
        else {
            $this->idArea = null;
        }

        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Area 
     */
    public function getArea() {
        return $this->area;
    }

    /**
     * 
     * @return type
     */
    public function getIdGerencia() {
        return $this->idGerencia;
    }

    /**
     * Set gerencia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Gerencia $gerencia
     * @return ContratoConsultoria
     */
    public function setGerencia(\ADIF\RecursosHumanosBundle\Entity\Gerencia $gerencia = null) {

        if (null != $gerencia) {
            $this->idGerencia = $gerencia->getId();
        } //.
        else {
            $this->idGerencia = null;
        }

        $this->gerencia = $gerencia;

        return $this;
    }

    /**
     * Get gerencia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Gerencia 
     */
    public function getGerencia() {
        return $this->gerencia;
    }

    /**
     * 
     * @return type
     */
    public function getIdSubgerencia() {
        return $this->idSubgerencia;
    }

    /**
     * Set subgerencia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Subgerencia $subgerencia
     * @return ContratoConsultoria
     */
    public function setSubgerencia(\ADIF\RecursosHumanosBundle\Entity\Subgerencia $subgerencia = null) {

        if (null != $subgerencia) {
            $this->idSubgerencia = $subgerencia->getId();
        } //.
        else {
            $this->idSubgerencia = null;
        }

        $this->subgerencia = $subgerencia;

        return $this;
    }

    /**
     * Get subgerencia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Subgerencia 
     */
    public function getSubgerencia() {
        return $this->subgerencia;
    }

    /**
     * Set esHonorarioProfesional
     *
     * @param boolean $esHonorarioProfesional
     * @return ContratoConsultoria
     */
    public function setEsHonorarioProfesional($esHonorarioProfesional) {
        $this->esHonorarioProfesional = $esHonorarioProfesional;

        return $this;
    }

    /**
     * Get esHonorarioProfesional
     *
     * @return boolean 
     */
    public function getEsHonorarioProfesional() {
        return $this->esHonorarioProfesional;
    }

    /**
     * Add comprobantesConsultoria
     *
     * @param ComprobanteConsultoria $comprobanteConsultoria
     * @return ContratoConsultoria
     */
    public function addComprobantesConsultoria(ComprobanteConsultoria $comprobanteConsultoria) {
        $this->comprobantesConsultoria[] = $comprobanteConsultoria;

        return $this;
    }

    /**
     * Remove comprobantesConsultoria
     *
     * @param ComprobanteConsultoria $comprobantesConsultoria
     */
    public function removeComprobantesConsultoria(ComprobanteConsultoria $comprobantesConsultoria) {
        $this->comprobantesConsultoria->removeElement($comprobantesConsultoria);
    }

    /**
     * Get comprobantesConsultoria
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantesConsultoria($recursivo = true) {
        
        $comprobantesConsultoria = array();

        if ($recursivo && $this->contratoOrigen != null) {

            $comprobantesConsultoria = array_merge($comprobantesConsultoria, $this->contratoOrigen->getComprobantesConsultoria($recursivo)->toArray());
        }

        return new ArrayCollection(array_merge($comprobantesConsultoria, $this->comprobantesConsultoria->toArray()));

    }

    /**
     * Add anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos
     * @return ContratoConsultoria
     */
    public function addAnticipo(\ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos) {
        $this->anticipos[] = $anticipos;

        return $this;
    }

    /**
     * Remove anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos
     */
    public function removeAnticipo(\ADIF\ContableBundle\Entity\AnticipoContratoConsultoria $anticipos) {
        $this->anticipos->removeElement($anticipos);
    }

    /**
     * Get anticipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnticipos() {
        return $this->anticipos;
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
     * Get saldoPendienteFacturacion
     * 
     * @return float
     */
    public function getSaldoPendienteFacturacion() {
        return $this->getImporteTotal() - $this->getTotalNetoComprobantes();
    }

    public function getTotalNetoComprobantes() {

        $total = 0;
        foreach ($this->getComprobantesConsultoria() as $comprobanteConsultoria) {
            $total += $comprobanteConsultoria->getTotalNeto();
        }
        if ($this->getContratoOrigen() == null) {
            return $total;
        } else {
            return $total + $this->getContratoOrigen()->getTotalNetoComprobantes();
        }
    }

    /**
     * Get ciclosFacturacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCiclosFacturacionPendientes() {


        return $this->ciclosFacturacion->filter(
                        function($cicloFacturacion) {

                    $fechaVencimiento = new \DateTime(date((new \DateTime())->format('Y-m-t')));

                    return $cicloFacturacion->getCantidadFacturasPendientes() > 0 && ($cicloFacturacion->getFechaInicio() <= $fechaVencimiento || $cicloFacturacion->getFechaFin() <= $fechaVencimiento);
                }
        );
    }

    /**
     * Get saldoPendientePago
     * 
     * @return type
     */
    public function getSaldoPendientePago() {

        $saldo = 0;

        foreach ($this->getComprobantesConsultoria() as $comprobanteConsultoria) {

            /* @var $comprobanteConsultoria ComprobanteConsultoria */
            if ($comprobanteConsultoria->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {

                if (!$comprobanteConsultoria->getEsNotaCredito()) {

                    $saldo += $comprobanteConsultoria->getTotal();
                } else {

                    $saldo -= $comprobanteConsultoria->getTotal();
                }
            }
        }

        foreach ($this->getAnticipos() as $anticipo) {

            /* @var $anticipo AnticipoContratoConsultoria */
            if ($anticipo->getOrdenPagoCancelada() == null && $anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
                $saldo -= $anticipo->getMonto();
            }
        }

        return $saldo;
    }

}
