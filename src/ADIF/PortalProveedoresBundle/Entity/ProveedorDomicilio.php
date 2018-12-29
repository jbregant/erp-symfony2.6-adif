<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProveedorDomicilio
 *
 * @ORM\Table("proveedor_domicilio")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorDomicilioRepository")
 */
class ProveedorDomicilio extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="proveedorDomicilio")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $usuario;

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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoDomicilio", inversedBy="proveedorDomicilio")
     * @ORM\JoinColumn(name="id_tipo_domicilio", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoDomicilio;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_pais", type="integer", nullable=true)
     */
    private $idPais;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Nacionalidad
     */
    private $pais;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_provincia", type="integer", nullable=true)
     */
    private $idProvincia;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Provincia
     */
    private $provincia;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_localidad", type="integer", nullable=true)
     */
    private $idLocalidad;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Localidad
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_postal", type="string", length=64, nullable=true)
     */
    private $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=64, nullable=true)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="departamento", type="string", length=64, nullable=true)
     */
    private $departamento;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string",length=64, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=64, nullable=true)
     */
    private $telefono;
    
    /**
     * @var string
     *
     * @ORM\Column(name="provincia_estado_exterior", type="string", length=255, nullable=true)
     */
    private $provinciaEstadoExterior;

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
     * @return ProveedorDomicilio
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
     * Set tipoDomicilio
     *
     * @param integer $tipoDomicilio
     * @return ProveedorDomicilio
     */
    public function setTipoDomicilio($tipoDomicilio)
    {
        $this->ipoDomicilio = $tipoDomicilio;

        return $this;
    }

    /**
     * Get ipoDomicilio
     *
     * @return integer
     */
    public function getTipoDomicilio()
    {
        return $this->tipoDomicilio;
    }

    public function getIdPais()
    {
        return $this->idPais;
    }

    public function getPais()
    {
        return $this->pais;
    }

    public function getIdLocalidad()
    {
        return $this->idLocalidad;
    }

    public function getLocalidad()
    {
        return $this->localidad;
    }

    public function getIdProvincia()
    {
        return $this->idProvincia;
    }  

    public function getProvincia()
    {
        return $this->provincia;
    }

      

    public function setIdPais($idPais)
    {
        $this->idPais = $idPais;
    }

    /**
     * Set pais
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Nacionalidad $pais
     */
    public function setPais($pais)
    {
        if (null != $pais) {
            $this->idPais = $pais->getId();
        } else {
            $this->idPais = null;
        }

        $this->pais = $pais;
    }  

    public function setIdProvincia($idProvincia)
    {
        $this->idProvincia = $idProvincia;
    }  

    /**
     * Set provincia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Provincia $provincia
     */
    public function setProvincia($provincia)
    {
        if (null != $provincia) {
            $this->idProvincia = $provincia->getId();
        } else {
            $this->idProvincia = null;
        }

        $this->provincia = $provincia;
    }

    /**
     * Set idLocalidad
     *
     * @param string $idLocalidad
     * @return ProveedorDomicilio
     */
    public function setIdLocalidad($idLocalidad)
    {
        $this->idLocalidad = $idLocalidad;
    
        return $this;
    }
    

    /**
     * Set localidad
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Localidad $localidad
     */
    public function setLocalidad($localidad)
    {
        if (null != $localidad) {
            $this->idLocalidad = $localidad->getId();
        } else {
            $this->idLocalidad = null;
        }

        $this->localidad = $localidad;
    }

    

    /**
     * Set codigoPostal
     *
     * @param string $codigoPostal
     * @return ProveedorDomicilio
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

    /**
     * Set calle
     *
     * @param string $calle
     * @return ProveedorDomicilio
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set departamento
     *
     * @param string $departamento
     * @return ProveedorDomicilio
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * Get departamento
     *
     * @return string
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set piso
     *
     * @param string $piso
     * @return ProveedorDomicilio
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso
     *
     * @return string
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * Set telefono
     *
     * @param integer $telefono
     * @return ProveedorDomicilio
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @return string
     */
    public function getProvinciaEstadoExterior()
    {
        return $this->provinciaEstadoExterior;
    }
    
    /**
     * @param string $provinciaEstadoExterior
     * @return ProveedorDomicilio
     */
    public function setProvinciaEstadoExterior($provinciaEstadoExterior)
    {
        $this->provinciaEstadoExterior = $provinciaEstadoExterior;
        return $this;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorDomicilio
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return ProveedorDatoPersonal
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }
}
