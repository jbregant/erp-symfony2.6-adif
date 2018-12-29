<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * TipoDomicilio
 *
 * @ORM\Table("tipo_domicilio")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TipoDomicilioRepository")
 */
class TipoDomicilio extends BaseAuditoria implements BaseAuditable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="ProveedorDomicilio", mappedBy="id_tipo_domicilio")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=64)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="proveedorDomicilio", mappedBy="tipoDomicilio")
     */
    protected $proveedorDomicilio;

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
     * @return TipoDomicilio
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
    public function getProveedorDomicilio()
    {
        return $this->proveedorDomicilio;
    }

    /**
     * @param mixed $proveedorDomicilio
     *
     * @return self
     */
    public function setProveedorDomicilio($proveedorDomicilio)
    {
        $this->proveedorDomicilio = $proveedorDomicilio;

        return $this;
    }
}
