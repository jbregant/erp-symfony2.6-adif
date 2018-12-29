<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ReposicionEgresoValor
 *
 * @author Manuel Becerra
 * created 14/01/2015
 * 
 * @ORM\Table(name="reposicion_egreso_valor")
 * @ORM\Entity
 */
class ReposicionEgresoValor extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_reposicion", type="datetime", nullable=false)
     */
    protected $fechaReposicion;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    protected $monto;

    /**
     * @var EgresoValor
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\EgresoValor", inversedBy="reposiciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    private $egresoValor;

    /**
     * @var ResponsableEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="ResponsableEgresoValor", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_responsable_egreso_valor", referencedColumnName="id", nullable=true)
     * })
     */
    protected $responsableEgresoValor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_creacion", type="boolean", nullable=false)
     */
    protected $esCreacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cambia_responsable", type="boolean", nullable=false)
     */
    protected $cambiaResponsable;
    
    
    /**
     * @var EstadoReposicionEgresoValor
     *
     * @ORM\ManyToOne(targetEntity="EstadoReposicionEgresoValor")
     * @ORM\JoinColumn(name="id_estado_reposicion_egreso_valor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoReposicionEgresoValor;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaReposicion = new \DateTime();
        $this->monto = 0;
        $this->esCreacion = false;
        $this->cambiaResponsable = false;
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
     * Set fechaReposicion
     *
     * @param \DateTime $fechaReposicion
     * @return ReposicionEgresoValor
     */
    public function setFechaReposicion($fechaReposicion) {
        $this->fechaReposicion = $fechaReposicion;

        return $this;
    }

    /**
     * Get fechaReposicion
     *
     * @return \DateTime 
     */
    public function getFechaReposicion() {
        return $this->fechaReposicion;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return ReposicionEgresoValor
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set egresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor $egresoValor
     * @return ReposicionEgresoValor
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
     * Set responsableEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor $responsableEgresoValor
     * @return ReposicionEgresoValor
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
     * Set esCreacion
     *
     * @param boolean $esCreacion
     * @return ReposicionEgresoValor
     */
    public function setEsCreacion($esCreacion) {
        $this->esCreacion = $esCreacion;

        return $this;
    }

    /**
     * Get esCreacion
     *
     * @return boolean 
     */
    public function getEsCreacion() {
        return $this->esCreacion;
    }

    /**
     * Set cambiaResponsable
     *
     * @param boolean $cambiaResponsable
     * @return ReposicionEgresoValor
     */
    public function setCambiaResponsable($cambiaResponsable) {
        $this->cambiaResponsable = $cambiaResponsable;

        return $this;
    }

    /**
     * Get cambiaResponsable
     *
     * @return boolean 
     */
    public function getCambiaResponsable() {
        return $this->cambiaResponsable;
    }

    
    /**
     * Set estadoReposicionEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\EstadoReposicionEgresoValor $estadoReposicionEgresoValor
     * @return ReposicionEgresoValor
     */
    public function setEstadoReposicionEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\EstadoReposicionEgresoValor $estadoReposicionEgresoValor) {
        $this->estadoReposicionEgresoValor = $estadoReposicionEgresoValor;

        return $this;
    }

    /**
     * Get estadoReposicionEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\EstadoReposicionEgresoValor 
     */
    public function getEstadoReposicionEgresoValor() {
        return $this->estadoReposicionEgresoValor;
    }
}
