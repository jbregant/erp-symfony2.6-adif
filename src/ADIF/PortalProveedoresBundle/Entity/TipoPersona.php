<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * TipoPersona
 *
 * @ORM\Table("tipo_persona")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TipoPersonaRepository")
 */
class TipoPersona extends BaseAuditoria implements BaseAuditable {

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
     * @Assert\NotBlank()
     * 
     * 
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="proveedorDatoPersonal", mappedBy="tipoPersona")
     */
    protected $proveedorDatoPersonal;

    public function __construct() {}

    public function toArray() {
        return get_object_vars($this);
    }
    
    public function __clone() {
        $this->id = null;
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoPersona
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;
        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion() {
        return $this->denominacion;
    }


    /**
     * @return mixed
     */
    public function getProveedorDatoPersonal()
    {
        return $this->proveedorDatoPersonal;
    }

    /**
     * @param mixed $proveedorDatoPersonal
     *
     * @return self
     */
    public function setProveedorDatoPersonal($proveedorDatoPersonal)
    {
        $this->proveedorDatoPersonal = $proveedorDatoPersonal;

        return $this;
    }
}
