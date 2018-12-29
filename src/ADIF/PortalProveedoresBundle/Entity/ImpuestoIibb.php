<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ImpuestoIibb
 *
 * @ORM\Table("impuesto_iibb")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ImpuestoIibbRepository")
 */
class ImpuestoIibb extends BaseAuditoria implements BaseAuditable
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoIvaInscripto")
     * @ORM\JoinColumn(name="id_tipo_iva_inscripto", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoIvaInscripto;    

    /**
     * @var string
     *
     * @ORM\Column(name="numero_inscripcion", type="string")
     */
    private $numeroInscripcion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_jurisdiccion", type="integer")
     */
    protected $idJurisdiccion;

    /**     
     *
     * @var ADIF\ContableBundle\Entity\Jurisdiccion
     */
    private $jurisdiccion;

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
     * Get TipoIvaInscripto
     *
     * @return integer 
     */
    public function getTipoIvaInscripto()
    {
        return $this->tipoIvaInscripto;
    }

    /**
     * Set TipoIva
     *
     * @param integer $tipoIva
     * @return ImpuestoIibb
     */
    public function setTipoIva($tipoIva)
    {
        $this->tipoIva = $tipoIva;
    }

    /**
     * Set TipoIvaInscripto
     *
     * @param integer $tipoIvaInscripto
     * @return ImpuestoIibb
     */
    public function setTipoIvaInscripto($tipoIvaInscripto)
    {
        $this->tipoIvaInscripto = $tipoIvaInscripto;
    }
    
    /**
     * Set numeroInscripcion
     *
     * @param string $numeroInscripcion
     * @return ImpuestoIibb
     */
    public function setNumeroInscripcion($numeroInscripcion)
    {
        $this->numeroInscripcion = $numeroInscripcion;
    
        return $this;
    }

    /**
     * Get numeroInscripcion
     *
     * @return string
     */
    public function getNumeroInscripcion()
    {
        return $this->numeroInscripcion;
    }

    /**
     * Get idJurisdiccion
     *
     * @return integer 
     */    
    public function getIdJurisdiccion()
    {
        return $this->idJurisdiccion;
    }
    
    /**
     * Set idJurisdiccion
     *
     * @param integer $idJurisdiccion
     * @return ImpuestoIibb
     */
    public function setIdJurisdiccion($idJurisdiccion)
    {
        $this->idJurisdiccion = $idJurisdiccion;
    }
    
    /**
     * Set jurisdiccion
     *
     * @param integer $jurisdiccion
     * @return ImpuestoIibb
     */
    public function setJurisdiccion($jurisdiccion)
    {
        $this->jurisdiccion = $jurisdiccion;
    
        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return integer 
     */
    public function getJurisdiccion()
    {
        return $this->jurisdiccion;
    }

    /**
     * Set exento
     *
     * @param boolean $exento
     * @return ImpuestoIibb
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
     * @return ImpuestoIibb
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
     * @return ImpuestoIibb
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
