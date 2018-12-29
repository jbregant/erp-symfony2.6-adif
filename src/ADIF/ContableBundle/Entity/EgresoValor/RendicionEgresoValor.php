<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;

/**
 * RendicionEgresoValor
 *
 * @author Manuel Becerra
 * created 14/01/2015
 * 
 * @ORM\Table(name="rendicion_egreso_valor")
 * @ORM\Entity
 */
class RendicionEgresoValor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_rendicion", type="datetime", nullable=false)
     */
    protected $fechaRendicion;

    /**
     * @var EgresoValor
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\EgresoValor", inversedBy="rendiciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    protected $egresoValor;

    /**
     * @var EstadoRendicionEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="EstadoRendicionEgresoValor")
     * @ORM\JoinColumn(name="id_estado_rendicion_egreso_valor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoRendicionEgresoValor;

    /**
     * @var ResponsableEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="ResponsableEgresoValor", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_responsable_egreso_valor", referencedColumnName="id", nullable=true)
     * })
     */
    protected $responsableEgresoValor;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="ComprobanteEgresoValor", mappedBy="rendicionEgresoValor", cascade={"all"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $comprobantes;

    /**
     *
     * @var DevolucionDinero
     * 
     * @ORM\OneToMany(targetEntity="DevolucionDinero", mappedBy="rendicionEgresoValor", cascade={"all"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $devoluciones;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaRendicion = new \DateTime();
        $this->comprobantes = new ArrayCollection();
        $this->devoluciones = new ArrayCollection();
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
     * Set fechaRendicion
     *
     * @param \DateTime $fechaRendicion
     * @return RendicionEgresoValor
     */
    public function setFechaRendicion($fechaRendicion) {
        $this->fechaRendicion = $fechaRendicion;

        return $this;
    }

    /**
     * Get fechaRendicion
     *
     * @return \DateTime 
     */
    public function getFechaRendicion() {
        return $this->fechaRendicion;
    }

    /**
     * Set egresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor $egresoValor
     * @return RendicionEgresoValor
     */
    public function setEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\EgresoValor $egresoValor) {
        $this->egresoValor = $egresoValor;

        return $this;
    }

    /**
     * Get egresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor 
     */
    public function getEgresoValor() {
        return $this->egresoValor;
    }

    /**
     * Set estadoRendicionEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\EstadoRendicionEgresoValor $estadoRendicionEgresoValor
     * @return RendicionEgresoValor
     */
    public function setEstadoRendicionEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\EstadoRendicionEgresoValor $estadoRendicionEgresoValor) {
        $this->estadoRendicionEgresoValor = $estadoRendicionEgresoValor;

        return $this;
    }

    /**
     * Get estadoRendicionEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor 
     */
    public function getEstadoRendicionEgresoValor() {
        return $this->estadoRendicionEgresoValor;
    }

    /**
     * Set responsableEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor $responsableEgresoValor
     * @return RendicionEgresoValor
     */
    public function setResponsableEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor $responsableEgresoValor) {
        $this->responsableEgresoValor = $responsableEgresoValor;

        return $this;
    }

    /**
     * Get responsableEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor 
     */
    public function getResponsableEgresoValor() {
        return $this->responsableEgresoValor;
    }

    /**
     * Add devoluciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero $devoluciones
     * @return RendicionEgresoValor
     */
    public function addDevolucion(\ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero $devoluciones) {
        $this->devoluciones[] = $devoluciones;

        return $this;
    }

    /**
     * Remove devoluciones
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero $devoluciones
     */
    public function removeDevolucion(\ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero $devoluciones) {
        $this->devoluciones->removeElement($devoluciones);
    }

    /**
     * Get devoluciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevoluciones() {
        return $this->devoluciones;
    }

    /**
     * Add comprobantes
     *
     * @param ComprobanteEgresoValor $comprobantes
     * @return OrdenPagoEgresoValor
     */
    public function addComprobante(ComprobanteEgresoValor $comprobantes) {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param ComprobanteEgresoValor $comprobantes
     */
    public function removeComprobante(ComprobanteEgresoValor $comprobantes) {
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
     * 
     * @return int
     */
    public function getImporteRendido() {

        $total = 0;
        /* @var $comprobante \ADIF\ContableBundle\Entity\Comprobante */
        foreach ($this->comprobantes as $comprobante) {
            $total += (ConstanteTipoComprobanteCompra::NOTA_CREDITO == $comprobante->getTipoComprobante()->getId()) ? $comprobante->getTotal() * (-1) : $comprobante->getTotal();
        }
        foreach ($this->devoluciones as $devoluciones) {
            $total += $devoluciones->getMontoDevolucion();
        }

        return $total;
    }

    /**
     * 
     * @return type
     */
    public function getNumeroReferencia() {

        $numeroReferencia = null;

        if (!$this->comprobantes->isEmpty()) {
            /* @var $comprobante ComprobanteEgresoValor */
            $comprobante = $this->comprobantes->last();

            $numeroReferencia = $comprobante->getNumeroReferencia();
        }

        return $numeroReferencia;
    }

    /**
     * 
     * @return type
     */
    public function getFechaIngresoADIF() {

        $fechaIngresoADIF = null;

        if (!$this->comprobantes->isEmpty()) {
            /* @var $comprobante ComprobanteEgresoValor */
            $comprobante = $this->comprobantes->last();

            $fechaIngresoADIF = $comprobante->getFechaIngresoADIF();
        }

        return $fechaIngresoADIF;
    }

    /**
     * 
     * @return type
     */
    public function getClaseConcepto() {

        $claseConcepto = null;

        if (!$this->comprobantes->isEmpty()) {

            /* @var $comprobante ComprobanteEgresoValor */
            $comprobante = $this->comprobantes->last();

            $claseConcepto = $comprobante->getClaseConcepto();
        }

        return $claseConcepto;
    }

}
