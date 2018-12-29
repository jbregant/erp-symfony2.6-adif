<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * TipoIvaInscripto
 *
 * @ORM\Table("tipo_iva_inscripto")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\TipoIvaInscriptoRepository")
 */
class TipoIvaInscripto extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="TipoIva")
     * @ORM\JoinColumn(name="id_tipo_iva", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoIva;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string")
     */
    private $denominacion;


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
     * Get TipoIva
     *
     * @return integer 
     */
    public function getTipoIva()
    {
        return $this->tipoIva;
    }

    /**
     * Set TipoIva
     *
     * @param integer $tipoIva
     * @return TipoIvaInscripto
     */
    public function setTipoIva($tipoIva)
    {
        $this->tipoIva = $tipoIva;
    }
    
    /**
     * Set denominacion
     *
     * @param integer $denominacion
     * @return TipoIvaInscripto
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;
    
        return $this;
    }

    /**
     * Get denominacion
     *
     * @return integer 
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }
}
