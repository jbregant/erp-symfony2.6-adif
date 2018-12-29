<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ImpuestoSuss
 *
 * @ORM\Table("impuesto_suss")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ImpuestoSussRepository")
 */
class ImpuestoSuss extends BaseAuditoria implements BaseAuditable
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
     * @var boolean
     *
     * @ORM\Column(name="personal_a_cargo", type="boolean")
     */
    private $personalACargo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exento", type="boolean")
     */
    private $exento;

    /**
     * @var boolean
     *
     * @ORM\Column(name="retencion", type="boolean")
     */
    private $retencion;


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
     * @return ImpuestoSuss
     */
    public function setTipoIva($tipoIva)
    {
        $this->tipoIva = $tipoIva;
        
        return $this;
    }
    
    /**
     * Set personalACargo
     *
     * @param boolean $personalACargo
     * @return ImpuestoSuss
     */
    public function setPersonalACargo($personalACargo)
    {
        $this->personalACargo = $personalACargo;
    
        return $this;
    }

    /**
     * Get personalACargo
     *
     * @return boolean 
     */
    public function getPersonalACargo()
    {
        return $this->personalACargo;
    }

    /**
     * Set exento
     *
     * @param boolean $exento
     * @return ImpuestoSuss
     */
    public function setExento($exento)
    {
        $this->exento = $exento;
    
        return $this;
    }

    /**
     * Get exento
     *
     * @return boolean 
     */
    public function getExento()
    {
        return $this->exento;
    }

    /**
     * Set retencion
     *
     * @param boolean $retencion
     * @return ImpuestoSuss
     */
    public function setRetencion($retencion)
    {
        $this->retencion = $retencion;
    
        return $this;
    }

    /**
     * Get retencion
     *
     * @return boolean 
     */
    public function getRetencion()
    {
        return $this->retencion;
    }
}
