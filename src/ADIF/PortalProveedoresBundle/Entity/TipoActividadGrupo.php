<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * TipoActividadGrupo
 *
 * @ORM\Table("tipo_actividad_grupo")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TipoActividadGrupoRepository")
 */
class TipoActividadGrupo extends BaseAuditoria implements BaseAuditable
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoActividadSeccion", inversedBy="tipoActividadGrupo")
     * @ORM\JoinColumn(name="id_seccion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $seccion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=16, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="TipoActividad", mappedBy="grupo")
     */
    private $tipoActividad;


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
     * Set seccion
     *
     * @param integer $seccion
     * @return ActividadGrupo
     */
    public function setSeccion($seccion)
    {
        $this->seccion = $seccion;
    
        return $this;
    }

    /**
     * Get seccion
     *
     * @return integer 
     */
    public function getSeccion()
    {
        return $this->seccion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return ActividadGrupo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return ActividadGrupo
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
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * @param mixed $actividad
     *
     * @return self
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;

        return $this;
    }
}
