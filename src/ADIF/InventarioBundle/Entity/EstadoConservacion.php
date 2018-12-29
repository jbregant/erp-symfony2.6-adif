<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * EstadoConservacion
 *
 * @ORM\Table(name="estado_conservacion")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoConservacion extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="denominacion_corta", type="string", length=20, nullable=true)
     */
    private $denominacionCorta;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    /**
     * @var integer
     *
     * @ORM\Column(name="habilitado_material_nuevo", type="boolean")
     */
    private $habilitadoMaterialNuevo;

    /**
     * @var integer
     *
     * @ORM\Column(name="habilitado_material_producido", type="boolean")
     */
    private $habilitadoMaterialProducido;

    /**
     * @var integer
     *
     * @ORM\Column(name="habilitado_material_rodante", type="boolean")
     */
    private $habilitadoMaterialRodante;

    /**
     * @var integer
     *
     * @ORM\Column(name="habilitado_activo_lineal", type="boolean")
     */
    private $habilitadoActivoLineal;


    /**
     * @return string
     */
    public function __toString() {
        //return (!$this->getDenominacionCorta())?$this->getDenominacion():$this->getDenominacionCorta();
        return $this->getDenominacionCorta() .' - '.$this->getDenominacion();
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
     * @return EstadoConservacion
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
     * Set denominacionCorta
     *
     * @param string $denominacionCorta
     * @return EstadoConservacion
     */
    public function setDenominacionCorta($denominacionCorta)
    {
        $this->denominacionCorta = $denominacionCorta;

        return $this;
    }

    /**
     * Get denominacionCorta
     *
     * @return string
     */
    public function getDenominacionCorta()
    {
        return $this->denominacionCorta;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return EstadoConservacion
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

    /**
     * Set habilitadoMaterialNuevo
     *
     * @param integer $habilitadoMaterialNuevo
     * @return EstadoConservacion
     */
    public function setHabilitadoMaterialNuevo($habilitadoMaterialNuevo)
    {
        $this->habilitadoMaterialNuevo = $habilitadoMaterialNuevo;

        return $this;
    }

    /**
     * Get habilitadoMaterialNuevo
     *
     * @return integer
     */
    public function getHabilitadoMaterialNuevo()
    {
        return $this->habilitadoMaterialNuevo;
    }

    /**
     * Set habilitadoMaterialProducido
     *
     * @param integer $habilitadoMaterialProducido
     * @return EstadoConservacion
     */
    public function setHabilitadoMaterialProducido($habilitadoMaterialProducido)
    {
        $this->habilitadoMaterialProducido = $habilitadoMaterialProducido;

        return $this;
    }

    /**
     * Get habilitadoMaterialProducido
     *
     * @return integer
     */
    public function getHabilitadoMaterialProducido()
    {
        return $this->habilitadoMaterialProducido;
    }

    /**
     * Set habilitadoMaterialRodante
     *
     * @param integer $habilitadoMaterialRodante
     * @return EstadoConservacion
     */
    public function setHabilitadoMaterialRodante($habilitadoMaterialRodante)
    {
        $this->habilitadoMaterialRodante = $habilitadoMaterialRodante;

        return $this;
    }

    /**
     * Get habilitadoMaterialRodante
     *
     * @return integer
     */
    public function getHabilitadoMaterialRodante()
    {
        return $this->habilitadoMaterialRodante;
    }

    /**
     * Set habilitadoActivoLineal
     *
     * @param integer $habilitadoActivoLineal
     * @return EstadoConservacion
     */
    public function setHabilitadoActivoLineal($habilitadoActivoLineal)
    {
        $this->habilitadoActivoLineal = $habilitadoActivoLineal;

        return $this;
    }

    /**
     * Get habilitadoActivoLineal
     *
     * @return integer
     */
    public function getHabilitadoActivoLineal()
    {
        return $this->habilitadoActivoLineal;
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
