<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * Rubro
 *
 * @ORM\Table("rubro")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\RubroRepository")
 */
class Rubro extends BaseAuditoria implements BaseAuditable
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
     * @ORM\OneToMany(targetEntity="RubroClase", mappedBy="rubro")
     */
    private $rubroClase;

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
     * @return Rubro
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
    public function getRubroClase()
    {
        return $this->rubroClase;
    }

    /**
     * @param mixed $rubroClase
     *
     * @return self
     */
    public function setRubroClase($rubroClase)
    {
        $this->rubroClase = $rubroClase;

        return $this;
    }
}
