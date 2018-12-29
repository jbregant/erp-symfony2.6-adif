<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * Actividad
 *
 * @ORM\Table("tipo_actividad")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TipoActividadRepository")
 */
class TipoActividad extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="TipoActividadGrupo", inversedBy="tipoActividad")
     * @ORM\JoinColumn(name="id_grupo", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $grupo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=16, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=1024, nullable=false)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorActividad", mappedBy="tipoActividad")
     */
    private $proveedorActividad;

    public function __toString() {
        return $this->codigo . ' - ' . $this->denominacion;
    }


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
     * Set grupo
     *
     * @param integer $grupo
     * @return Actividad
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;
    
        return $this;
    }

    /**
     * Get grupo
     *
     * @return integer 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return Actividad
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
     * @return Actividad
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
    public function getProveedorActividad()
    {
        return $this->proveedorActividad;
    }

    /**
     * @param mixed $proveedorActividad
     *
     * @return self
     */
    public function setProveedorActividad($proveedorActividad)
    {
        $this->proveedorActividad = $proveedorActividad;

        return $this;
    }
}
