<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;


/**
 * Timeline
 *
 * @ORM\Table(name="proveedor_timeline")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorTimelineRepository")
 */
class ProveedorTimeline extends BaseAuditoria implements BaseAuditable
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var TipoTimeline
     *
     * @ORM\ManyToOne(targetEntity="TipoTimeline")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_denominacion", referencedColumnName="id")
     * })
     */
    private $denominacion;

    /**
     * @var int
     *
     * @ORM\Column(name="id_status", type="integer")
     */
    private $idStatus;

    /**
     * @var ADIF\PortalProveedoresBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="ADIF\PortalProveedoresBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;


    /**
     * @var ProveedorDatoPersonal
     *
     * @ORM\ManyToOne(targetEntity="ProveedorDatoPersonal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     * })
     */
    private $idDatoPersonal;

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorTimeline
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return ProveedorDatoPersonal
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return ProveedorTimeline
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set idStatus
     *
     * @param int $idStatus
     *
     * @return ProveedorTimeline
     */
    public function setIdStatus($idStatus)
    {
        $this->idStatus = $idStatus;

        return $this;
    }

    /**
     * Get idStatus
     *
     * @return int
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * @return ADIF\PortalProveedoresBundle\Entity\Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param ADIF\PortalProveedoresBundle\Entity\Usuario $idUsuario
     * @return ProveedorTimeline
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    /**
     * @return TipoTimeline
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * @param TipoTimeline $denominacion
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;
    }
}

