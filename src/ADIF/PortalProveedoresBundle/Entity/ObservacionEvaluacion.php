<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ObservacionEvaluacion
 *
 * @ORM\Table("observacion_evaluacion")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ObservacionEvaluacionRepository")
 */
class ObservacionEvaluacion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="ProveedorEvaluacion", inversedBy="observacionEvaluacion")
     * @ORM\JoinColumn(name="id_proveedor_evaluacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorEvaluacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoObservacion", inversedBy="observacionEvaluacion")
     * @ORM\JoinColumn(name="id_tipo_observacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoObservacion;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=false)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones_historico", type="simple_array", nullable=true)
     */
    private $observacionesHistorico;


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
     * @return ObservacionEvaluacion
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
     * Set tipoObservacion
     *
     * @param integer $tipoObservacion
     * @return ObservacionEvaluacion
     */
    public function setTipoObservacion($tipoObservacion)
    {
        $this->tipoObservacion = $tipoObservacion;

        return $this;
    }

    /**
     * Get tipoObservacion
     *
     * @return integer
     */
    public function getTipoObservacion()
    {
        return $this->tipoObservacion;
    }
    
     /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return ObservacionEvaluacion
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }
    /**
     * Set observacionesHistorico
     *
     * @return ObservacionEvaluacion
     */
    public function setObservacionesHistorico($observacion)
    {
        $this->observacionesHistorico = $observacion;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return array
     */
    public function getObservacionesHistorico()
    {
        return $this->observacionesHistorico;
    }
}
