<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProveedorEvaluacionLog
 *
 * @ORM\Table("proveedor_evaluacion_log")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorEvaluacionLogRepository")
 */
class ProveedorEvaluacionLog extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="ProveedorEvaluacion", inversedBy="proveedorEvaluacionLog")
     * @ORM\JoinColumn(name="id_proveedor_evaluacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorEvaluacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=1024)
     */
    private $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    public function __toString()
    {
        return $this->descripcion;
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
     * Set proveedorEvaluacion
     *
     * @param integer $proveedorEvaluacion
     * @return ProveedorEvaluacionLog
     */
    public function setProveedorEvaluacion($proveedorEvaluacion)
    {
        $this->proveedorEvaluacion = $proveedorEvaluacion;

        return $this;
    }

    /**
     * Get proveedorEvaluacion
     *
     * @return integer
     */
    public function getProveedorEvaluacion()
    {
        return $this->proveedorEvaluacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return ProveedorEvaluacionLog
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return ProveedorEvaluacionLog
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }
}
