<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorDatoImpositivo
 *
 * @ORM\Table("proveedor_dato_impositivo")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorDatoImpositivoRepository")
 */
class ProveedorDatoImpositivo extends BaseAuditoria implements BaseAuditable
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
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="proveedorDatoImpositivo")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $usuario;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoIva")
     * @ORM\JoinColumn(name="id_proveedor_iva", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorIva;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoSuss")
     * @ORM\JoinColumn(name="id_proveedor_suss", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorSuss;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoGanancias")
     * @ORM\JoinColumn(name="id_proveedor_ganancias", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorGanancias;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ImpuestoIibb")
     * @ORM\JoinColumn(name="id_proveedor_iibb", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $proveedorIibb;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cae", type="boolean")
     */
    private $cae;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="cai", type="boolean")
     */
    private $cai;    
    

    /**
     * @var string
     *
     * @ORM\Column(name="otros", type="string", length=255, nullable=true)
     */
    private $otros;

    /**
     * @var ProveedorDatoPersonal
     *
     * @ORM\ManyToOne(targetEntity="ProveedorDatoPersonal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     * })
     */
    private $idDatoPersonal;

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorDatoImpositivo
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return string
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }

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
     * Set otros
     *
     * @param string $otros
     * @return ProveedorDatoImpositivo
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
    
    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getProveedorIva()
    {
        return $this->proveedorIva;
    }

    public function getProveedorSuss()
    {
        return $this->proveedorSuss;
    }

    public function getProveedorGanancias()
    {
        return $this->proveedorGanancias;
    }

    public function getProveedorIibb()
    {
        return $this->proveedorIibb;
    }

    public function getCae()
    {
        return $this->cae;
    }

    public function getCai()
    {
        return $this->cai;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setProveedorIva($proveedorIva)
    {
        $this->proveedorIva = $proveedorIva;
    }

    public function setProveedorSuss($proveedorSuss)
    {
        $this->proveedorSuss = $proveedorSuss;
    }

    public function setProveedorGanancias($proveedorGanancias)
    {
        $this->proveedorGanancias = $proveedorGanancias;
    }

    public function setProveedorIibb($proveedorIibb)
    {
        $this->proveedorIibb = $proveedorIibb;
    }

    public function setCae($cae)
    {
        $this->cae = $cae;
    }

    public function setCai($cai)
    {
        $this->cai = $cai;
    }
}
