<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorUteMiembros
 *
 * @ORM\Table("proveedor_ute_miembros")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ProveedorUteMiembrosRepository")
 * 
 */
class ProveedorUteMiembros extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="ProveedorUte" , inversedBy="proveedorUteMiembros")
     * @ORM\JoinColumn(name="id_ute", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorUteMiembros;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=13, nullable=false)
     * @Assert\Regex(
     *   pattern="/^[0-9]{2}-[0-9]{8}-[0-9]{1}$/",
     *   match=false,
     *   message="Formato de CUIT inválido")
     */
    protected $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=64, nullable=false)
     */
    private $razonSocial;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_inscripcion", type="integer")
     */
    private $numeroInscripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="participacion_ganancias", type="decimal", precision=4, scale=2, nullable=false)
     */
    private $participacionGanancias;

    /**
     * @var string
     *
     * @ORM\Column(name="participacion_remunerativa", type="decimal", precision=4, scale=2, nullable=false)
     */
    private $participacionRemunerativa;

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
     * Set proveedorUteMiembros
     *
     * @param integer $proveedorUteMiembros
     * @return ProveedorUteMiembros
     */
    public function setProveedorUteMiembros($proveedorUteMiembros)
    {
        $this->proveedorUteMiembros = $proveedorUteMiembros;

        return $this;
    }

    /**
     * Get proveedorUteMiembros
     *
     * @return integer 
     */
    public function getProveedorUteMiembros()
    {
        return $this->proveedorUteMiembros;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return ProveedorUteMiembros
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string 
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return ProveedorUteMiembros
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set numeroInscripcion
     *
     * @param integer $numeroInscripcion
     * @return ProveedorUteMiembros
     */
    public function setNumeroInscripcion($numeroInscripcion)
    {
        $this->numeroInscripcion = $numeroInscripcion;

        return $this;
    }

    /**
     * Get numeroInscripcion
     *
     * @return integer 
     */
    public function getNumeroInscripcion()
    {
        return $this->numeroInscripcion;
    }

    /**
     * Set participacionGanancias
     *
     * @param string $participacionGanancias
     * @return ProveedorUteMiembros
     */
    public function setParticipacionGanancias($participacionGanancias)
    {
        $this->participacionGanancias = $participacionGanancias;

        return $this;
    }

    /**
     * Get participacionGanancias
     *
     * @return string 
     */
    public function getParticipacionGanancias()
    {
        return $this->participacionGanancias;
    }

    /**
     * Set participacionRemunerativa
     *
     * @param string $participacionRemunerativa
     * @return ProveedorUteMiembros
     */
    public function setParticipacionRemunerativa($participacionRemunerativa)
    {
        $this->participacionRemunerativa = $participacionRemunerativa;

        return $this;
    }

    /**
     * Get participacionRemunerativa
     *
     * @return string 
     */
    public function getParticipacionRemunerativa()
    {
        return $this->participacionRemunerativa;
    }
}
