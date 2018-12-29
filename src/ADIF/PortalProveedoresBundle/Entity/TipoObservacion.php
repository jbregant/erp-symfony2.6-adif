<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * TipoObservacion
 *
 * @ORM\Table("tipo_observacion")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TipoObservacionRepository")
 */
class TipoObservacion extends BaseAuditoria implements BaseAuditable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    private $denominacion;

     /**
     * @ORM\OneToMany(targetEntity="ObservacionEvaluacion", mappedBy="tipoObservacion")
     */
    protected $observacionEvaluacion;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoObservacion
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;
    
        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * @return mixed
     */
    public function getObservacionEvaluacion()
    {
        return $this->observacionEvaluacion;
    }

    /**
     * @param mixed $observacionEvaluacion
     *
     * @return self
     */
    public function setObservacionEvaluacion($observacionEvaluacion)
    {
        $this->observacionEvaluacion = $observacionEvaluacion;

        return $this;
    }
}
