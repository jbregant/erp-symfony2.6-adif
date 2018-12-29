<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * ProveedorRubro
 *
 * @ORM\Table("proveedor_rubro")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorRubroRepository")
 */
class ProveedorRubro extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="proveedorRubro")
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
     * @ORM\ManyToOne(targetEntity="RubroClase", inversedBy="proveedorRubro")
     * @ORM\JoinColumn(name="id_rubro_clase", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $rubroClase;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha_baja", type="text", nullable=true)
     */
    protected $fechaBaja;


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
     * @param Usuario $usuario
     * @return ProveedorRubro
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set rubroClase
     *
     * @param RubroClase $rubroClase
     * @return ProveedorRubro
     */
    public function setRubroClase($rubroClase)
    {
        $this->rubroClase = $rubroClase;
    
        return $this;
    }

    /**
     * Get rubroClase
     *
     * @return RubroClase 
     */
    public function getRubroClase()
    {
        return $this->rubroClase;
    }


    public function setFechaBaja($fechaBaja)
    {
        $this->$fechaBaja = $fechaBaja;

        return $this;
    }


    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorRubro
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
