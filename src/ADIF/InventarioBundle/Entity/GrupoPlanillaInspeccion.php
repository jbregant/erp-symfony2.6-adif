<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\PlanillaInspeccion;

/**
 * GrupoPlanillaInspeccion
 *
 * @ORM\Table(name="grupo_planilla_inspeccion")
 * @ORM\Entity
 */
class GrupoPlanillaInspeccion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="PlanillaInspeccion")
     * @ORM\JoinColumn(name="id_planilla_inspeccion", referencedColumnName="id", nullable=false)
     */
    private $planillaInspeccion;
    

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100)
     */
    private $denominacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;


    public function __construct() {
        $this->planillaInspeccion= new ArrayCollection();
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
     * Set planillaInspeccion
     *
     * @param integer $planillaInspeccion
     * @return GrupoPlanillaInspeccion
     */
    public function setPlanillaInspeccion(PlanillaInspeccion $planillaInspeccion)
    {
        $this->planillaInspeccion = $planillaInspeccion;
    
        return $this;
    }

    /**
     * Get PlanillaInspeccion
     *
     * @return integer 
     */
    public function getPlanillaInspeccion()
    {
        return $this->planillaInspeccion;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return GrupoPlanillaInspeccion
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
     * Set orden
     *
     * @param integer $orden
     * @return GrupoPlanillaInspeccion
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return GrupoPlanillaInspeccion
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
}
