<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ImpuestoIva
 *
 * @ORM\Table("impuesto_iva")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ImpuestoIvaRepository")
 */
class ImpuestoIva extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="otros", type="string", length=64)
     */
    private $otros;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="DatoExento", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="id_dato_exento", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $datoExento;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getTipoIva()
    {
        return $this->tipoIva;
    }

    public function getExento()
    {
        return $this->exento;
    }

    public function getRetencion()
    {
        return $this->retencion;
    }

    public function setTipoIva($tipoIva)
    {
        $this->tipoIva = $tipoIva;
    }

    public function setExento($exento)
    {
        $this->exento = $exento;
    }

    public function setRetencion($retencion)
    {
        $this->retencion = $retencion;
    }

    /**
     * Set otros
     *
     * @param string $otros
     * @return ImpuestoIva
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


    /**
     * Get the value of datoExento
     *
     * @return  integer
     */
    public function getDatoExento()
    {
        return $this->datoExento;
    }

    /**
     * Set the value of datoExento
     *
     * @param  integer  $datoExento
     *
     * @return  self
     */
    public function setDatoExento($datoExento)
    {
        $this->datoExento = $datoExento;

        return $this;
    }
}
