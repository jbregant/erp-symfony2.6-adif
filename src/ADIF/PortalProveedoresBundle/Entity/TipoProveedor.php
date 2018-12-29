<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * TipoProveedor
 *
 * @ORM\Table("tipo_proveedor")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TipoProveedorRepository")
 */
class TipoProveedor extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="denominacion", type="string", length=64)
     */
    private $denominacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="extranjero", type="boolean")
     */
    private $extranjero;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorDatoPersonal", mappedBy="tipoProveedor")
     */
    private $proveedorDatoPersonal;

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
     * @return TipoProveedor
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
     * Set extranjero
     *
     * @param boolean $extranjero
     * @return TipoProveedor
     */
    public function setExtranjero($extranjero)
    {
        $this->extranjero = $extranjero;
    
        return $this;
    }

    /**
     * Get extranjero
     *
     * @return boolean 
     */
    public function getExtranjero()
    {
        return $this->extranjero;
    }

    /**
     * Es extranjero
     *
     * @return boolean 
     */
    public function esExtranjero()
    {
        return $this->extranjero;
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
