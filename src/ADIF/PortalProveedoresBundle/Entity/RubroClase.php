<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * RubroClase
 *
 * @ORM\Table("rubro_clase")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\RubroClaseRepository")
 */
class RubroClase extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Rubro", inversedBy="rubroClase")
     * @ORM\JoinColumn(name="id_rubro", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $rubro;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=64)
     */
    private $denominacion;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRubro", mappedBy="rubroClase")
     */
    private $proveedorRubro;

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
     * Set rubro
     *
     * @param Rubro $rubro
     * @return RubroClase
     */
    public function setRubro($rubro)
    {
        $this->rubro = $rubro;

        return $this;
    }

    /**
     * Get rubro
     *
     * @return Rubro
     */
    public function getRubro()
    {
        return $this->rubro;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return RubroClase
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
    public function getProveedorRubro()
    {
        return $this->proveedorRubro;
    }

    /**
     * @param mixed $proveedorRubro
     *
     * @return self
     */
    public function setProveedorRubro($proveedorRubro)
    {
        $this->proveedorRubro = $proveedorRubro;

        return $this;
    }
}
