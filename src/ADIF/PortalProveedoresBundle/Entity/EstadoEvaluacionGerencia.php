<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * EstadoEvaluacionGerencia
 *
 * @ORM\Table("estado_evaluacion_gerencia")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\EstadoEvaluacionGerenciaRepository")
 */
class EstadoEvaluacionGerencia extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="denominacion", type="string", length=64, nullable=false)
     */
    private $denominacion;


    /**
     * @ORM\OneToMany(targetEntity="ProveedorEvaluacion", mappedBy="estadoEvaluacionGalo")
     */
    protected $proveedorEvaluacionGalo;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorEvaluacion", mappedBy="estadoEvaluacionGcshm")
     */
    protected $proveedorEvaluacionGcshm;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorEvaluacion", mappedBy="estadoEvaluacionGafFinanzas")
     */
    protected $proveedorEvaluacionGafFinanzas;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorEvaluacion", mappedBy="estadoEvaluacionGafImpuestos")
     */
    protected $proveedorEvaluacionGafImpuestos;


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
     * @return EstadoEvaluacionGerencia
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
    public function getProveedorEvaluacionGalo()
    {
        return $this->proveedorEvaluacionGalo;
    }

    /**
     * @param mixed $proveedorEvaluacionGalo
     *
     * @return self
     */
    public function setProveedorEvaluacionGalo($proveedorEvaluacionGalo)
    {
        $this->proveedorEvaluacionGalo = $proveedorEvaluacionGalo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacionGcshm()
    {
        return $this->proveedorEvaluacionGcshm;
    }

    /**
     * @param mixed $proveedorEvaluacionGcshm
     *
     * @return self
     */
    public function setProveedorEvaluacionGcshm($proveedorEvaluacionGcshm)
    {
        $this->proveedorEvaluacionGcshm = $proveedorEvaluacionGcshm;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacionGafFinanzas()
    {
        return $this->proveedorEvaluacionGafFinanzas;
    }

    /**
     * @param mixed $proveedorEvaluacionGafFinanzas
     *
     * @return self
     */
    public function setProveedorEvaluacionGafFinanzas($proveedorEvaluacionGafFinanzas)
    {
        $this->proveedorEvaluacionGafFinanzas = $proveedorEvaluacionGafFinanzas;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacionGafImpuestos()
    {
        return $this->proveedorEvaluacionGafImpuestos;
    }

    /**
     * @param mixed $proveedorEvaluacionGafImpuestos
     *
     * @return self
     */
    public function setProveedorEvaluacionGafImpuestos($proveedorEvaluacionGafImpuestos)
    {
        $this->proveedorEvaluacionGafImpuestos = $proveedorEvaluacionGafImpuestos;

        return $this;
    }
}
