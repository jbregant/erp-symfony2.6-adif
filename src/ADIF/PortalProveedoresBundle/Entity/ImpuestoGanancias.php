<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ImpuestoGanancias
 *
 * @ORM\Table("impuesto_ganancias")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ImpuestoGananciasRepository")
 */
class ImpuestoGanancias extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="otros", type="string", length=255)
     */
    private $otros;


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
     * @return ImpuestoGanancias
     */
    public function setTipoIva($tipoIva)
    {
        $this->tipoIva = $tipoIva;
    }
    
    /**
     * Set exento
     *
     * @param boolean $exento
     * @return ImpuestoGanancias
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
     * @return ImpuestoGanancias
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

    /**
     * Set otros
     *
     * @param string $otros
     * @return ImpuestoGanancias
     */
    public function setOtros($otros)
    {
        $this->otros = $otros;
    
        return $this;
    }

    /**
     * Get otros
     *
     * @return string 
     */
    public function getOtros()
    {
        return $this->otros;
    }
}
