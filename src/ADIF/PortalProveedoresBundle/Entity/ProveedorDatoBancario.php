<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorDatoBancario
 *
 * @ORM\Table("proveedor_dato_bancario")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ProveedorDatoBancarioRepository")
 */
class ProveedorDatoBancario extends BaseAuditoria implements BaseAuditable
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
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="proveedorDatoBancario")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)       
     * @Assert\NotBlank()
     */
    private $usuario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cuenta_local", type="boolean")
     */
    private $cuentaLocal;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_entidad_bancaria", type="integer")
     */
    private $idEntidadBancaria;

    /**
     *
     * @var ADIF\RecursosHumanosBundle\Entity\Banco
     */
    private $entidadBancaria;

    /**
     * @var string
     *
     * @ORM\Column(name="sucursal_bancaria", type="string", length=64)
     */
    private $sucursalBancaria;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_sucursal", type="integer", nullable=false)
     */
    private $numeroSucursal;

    /**
     * @var string
     *
     * @ORM\Column(name="cbu", type="string", nullable=false, length=23)
     */
    private $cbu;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_cuenta", type="integer")
     */
    private $numeroCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="swift", type="string", length=255, nullable=true)
     */
    private $swift;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=true)
     */
    private $tipoMoneda;
    
    /** 
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    private $moneda;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad_extranjero", type="string", nullable=true)
     */
    private $localidadExtranjero;
    
    /**
     * @var string
     *
     * @ORM\Column(name="aba", type="string", length=64, nullable=true)
     */
    private $aba;
    
    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=64, nullable=true)
     */
    private $iban;
    
    /**
     * @var string
     *
     * @ORM\Column(name="beneficiario", type="string", length=64, nullable=true)
     */
    private $beneficiario;
    
    /**
     * @var string
     *
     * @ORM\Column(name="banco_corresponsal", type="string", length=64, nullable=true)
     */
    private $bancoCorresponsal;

    /**
     * @var string
     *
     * @ORM\Column(name="swift_banco_corresponsal", type="string", length=64, nullable=true)
     */
    private $swiftBancoCorresponsal;


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
     * @return ProveedorDatoBancario
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
     * Set usuario
     *
     * @param integer $usuario
     * @return ProveedorDatoBancario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return integer 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set cuentaLocal
     *
     * @param boolean $cuentaLocal
     * @return ProveedorDatoBancario
     */
    public function setCuentaLocal($cuentaLocal)
    {
        $this->cuentaLocal = $cuentaLocal;

        return $this;
    }

    /**
     * Get cuentaLocal
     *
     * @return boolean 
     */
    public function getCuentaLocal()
    {
        return $this->cuentaLocal;
    }

    /**
     * Set idEntidadBancaria
     *
     * @param integer $idEntidadBancaria
     * @return ProveedorDatoBancario
     */
    public function setIdEntidadBancaria($idEntidadBancaria)
    {
        $this->idEntidadBancaria = $idEntidadBancaria;

        return $this;
    }

    /**
     * Get idEntidadBancaria
     *
     * @return integer 
     */
    public function getIdEntidadBancaria()
    {
        return $this->idEntidadBancaria;
    }
    
    /**
     * Get entidadBancaria
     *
     * @return integer 
     */
    public function getEntidadBancaria()
    {
        return $this->entidadBancaria;
    }

    /**
     * Set entidadBancaria
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Banco $entidadBancaria
     */
    public function setEntidadBancaria($entidadBancaria)
    {
        if (null != $entidadBancaria) {
            $this->idEntidadBancaria = $entidadBancaria->getId();
        } else {
            $this->idEntidadBancaria = null;
        }

        $this->entidadBancaria = $entidadBancaria;
    }
    
    /**
     * Set sucursalBancaria
     *
     * @param string $sucursalBancaria
     * @return ProveedorDatoBancario
     */
    public function setSucursalBancaria($sucursalBancaria)
    {
        $this->sucursalBancaria = $sucursalBancaria;

        return $this;
    }

    /**
     * Get sucursalBancaria
     *
     * @return string 
     */
    public function getSucursalBancaria()
    {
        return $this->sucursalBancaria;
    }

    /**
     * Set numeroSucursal
     *
     * @param integer $numeroSucursal
     * @return ProveedorDatoBancario
     */
    public function setNumeroSucursal($numeroSucursal)
    {
        $this->numeroSucursal = $numeroSucursal;

        return $this;
    }

    /**
     * Get numeroSucursal
     *
     * @return integer 
     */
    public function getNumeroSucursal()
    {
        return $this->numeroSucursal;
    }

    /**
     * Set cbu
     *
     * @param integer $cbu
     * @return ProveedorDatoBancario
     */
    public function setCbu($cbu)
    {
        $this->cbu = $cbu;

        return $this;
    }

    /**
     * Get cbu
     *
     * @return integer 
     */
    public function getCbu()
    {
        return $this->cbu;
    }

    /**
     * Set numeroCuenta
     *
     * @param integer $numeroCuenta
     * @return ProveedorDatoBancario
     */
    public function setNumeroCuenta($numeroCuenta)
    {
        $this->numeroCuenta = $numeroCuenta;

        return $this;
    }

    /**
     * Get numeroCuenta
     *
     * @return integer 
     */
    public function getNumeroCuenta()
    {
        return $this->numeroCuenta;
    }

    /**
     * Set swift
     *
     * @param string $swift
     * @return ProveedorDatoBancario
     */
    public function setSwift($swift)
    {
        $this->swift = $swift;

        return $this;
    }

    /**
     * Get swift
     *
     * @return string 
     */
    public function getSwift()
    {
        return $this->swift;
    }

    /**
     * Set tipoMoneda
     *
     * @param integer $tipoMoneda
     * @return ProveedorDatoBancario
     */
    public function setTipoMoneda($tipoMoneda)
    {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return integer 
     */
    public function getTipoMoneda()
    {
        return $this->tipoMoneda;
    }
    
    public function getMoneda()
    {
        return $this->moneda;
    }

    public function getAba()
    {
        return $this->aba;
    }

    public function getIban()
    {
        return $this->iban;
    }

    public function getBeneficiario()
    {
        return $this->beneficiario;
    }

    public function getBancoCorresponsal()
    {
        return $this->bancoCorresponsal;
    }

    public function getSwiftBancoCorresponsal()
    {
        return $this->swiftBancoCorresponsal;
    }

    /**
     * Set moneda
     *
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $moneda
     */
    public function setMoneda($moneda)
    {
        if (null != $moneda) {
            $this->tipoMoneda = $moneda->getId();
        } else {
            $this->tipoMoneda = null;
        }

        $this->moneda = $moneda;
    }

    public function setAba($aba)
    {
        $this->aba = $aba;
    }

    public function setIban($iban)
    {
        $this->iban = $iban;
    }

    public function setBeneficiario($beneficiario)
    {
        $this->beneficiario = $beneficiario;
    }

    public function setBancoCorresponsal($bancoCorresponsal)
    {
        $this->bancoCorresponsal = $bancoCorresponsal;
    }

    public function setSwiftBancoCorresponsal($swiftBancoCorresponsal)
    {
        $this->swiftBancoCorresponsal = $swiftBancoCorresponsal;
    }
    
        /**
     * Set localidad
     *
     * @param \ADIF\ComprasBundle\Entity\ProveedorDatoBancario $localidadExtranjero
     */
    public function setLocalidadExtranjero($localidadExtranjero) {        
        $this->localidadExtranjero = $localidadExtranjero;
        return $this;
    }

    /**
     * Get localidad
     *
     * @return string
     */
    public function getLocalidadExtranjero() {
        return $this->localidadExtranjero;
    }
}
