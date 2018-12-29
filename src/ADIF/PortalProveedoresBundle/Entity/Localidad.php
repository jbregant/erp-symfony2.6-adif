<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Localidad
 *
 * @ORM\Table("localidad")
 * @ORM\Entity
 */
class Localidad extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumn(name="id_provincia", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=64, nullable=false)
     * * @Assert\NotBlank()
     */
    private $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", length=16, nullable=false)
     * * @Assert\NotBlank()
     */
    private $codigoPostal;

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
     * Set provincia
     *
     * @param integer $provincia
     * @return Localidad
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return integer
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return Localidad
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
     * Set codigoPostal
     *
     * @param string $codigoPostal
     * @return Localidad
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return string
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }
}
