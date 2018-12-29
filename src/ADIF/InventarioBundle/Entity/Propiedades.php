<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Propiedades
 *
 * @ORM\Table(name="Propiedades")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 */
class Propiedades extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     */
    private $denominacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="habilitado_material_nuevo", type="boolean")
     */
    private $habilitadoMaterialNuevo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="habilitado_material_producido", type="boolean")
     */
    private $habilitadoMaterialProducido;

    /**
     * @var boolean
     *
     * @ORM\Column(name="habilitado_material_rodante", type="boolean")
     */
    private $habilitadoMaterialRodante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="habilitado_activo_lineal", type="boolean")
     */
    private $habilitadoActivoLineal;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;
    
    /**
     * @ORM\OneToMany(targetEntity="PropiedadValor", mappedBy="idPropiedad")
     */
    private $valoresPropiedad;
    
    public function __construct() {

        $this->valoresPropiedad = new ArrayCollection();
    }

    public function __toString() {
        return $this->getDenominacion();
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return Propiedades
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set habilitadoMaterialNuevo
     *
     * @param boolean $habilitadoMaterialNuevo
     * @return Propiedades
     */
    public function setHabilitadoMaterialNuevo($habilitadoMaterialNuevo)
    {
        $this->habilitadoMaterialNuevo = $habilitadoMaterialNuevo;

        return $this;
    }

    /**
     * Get habilitadoMaterialNuevo
     *
     * @return boolean
     */
    public function getHabilitadoMaterialNuevo()
    {
        return $this->habilitadoMaterialNuevo;
    }

    /**
     * Set habilitadoMaterialProducido
     *
     * @param boolean $habilitadoMaterialProducido
     * @return Propiedades
     */
    public function setHabilitadoMaterialProducido($habilitadoMaterialProducido)
    {
        $this->habilitadoMaterialProducido = $habilitadoMaterialProducido;

        return $this;
    }

    /**
     * Get habilitadoMaterialProducido
     *
     * @return boolean
     */
    public function getHabilitadoMaterialProducido()
    {
        return $this->habilitadoMaterialProducido;
    }

    /**
     * Set habilitadoMaterialRodante
     *
     * @param boolean $habilitadoMaterialRodante
     * @return Propiedades
     */
    public function setHabilitadoMaterialRodante($habilitadoMaterialRodante)
    {
        $this->habilitadoMaterialRodante = $habilitadoMaterialRodante;

        return $this;
    }

    /**
     * Get habilitadoMaterialRodante
     *
     * @return boolean
     */
    public function getHabilitadoMaterialRodante()
    {
        return $this->habilitadoMaterialRodante;
    }

    /**
     * Set habilitadoActivoLineal
     *
     * @param boolean $habilitadoActivoLineal
     * @return Propiedades
     */
    public function setHabilitadoActivoLineal($habilitadoActivoLineal)
    {
        $this->habilitadoActivoLineal = $habilitadoActivoLineal;

        return $this;
    }

    /**
     * Get habilitadoActivoLineal
     *
     * @return boolean
     */
    public function getHabilitadoActivoLineal()
    {
        return $this->habilitadoActivoLineal;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Propiedades
     */
    public function setIdEmpresa($idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa
     *
     * @return integer
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }
    
    
    public function getValoresPropiedad()
    {
        return $this->valoresPropiedad;
    }

    /**
      * @Assert\Callback
      */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->habilitadoActivoLineal && !$this->habilitadoMaterialNuevo &&
            !$this->habilitadoMaterialRodante && !$this->habilitadoMaterialProducido)
        {
            $context->buildViolation('<strong>Material:</strong> Seleccione al menos un tipo de Material')
               ->atPath('')
               ->addViolation();
        }
    }
}
