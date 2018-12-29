<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\InspeccionTecnica;
use ADIF\InventarioBundle\Entity\GrupoPlanillaInspeccion;

/**
 * GrupoInspeccionTecnica
 *
 * @ORM\Table(name="grupo_inspeccion_tecnica")
 * @ORM\Entity
 */
class GrupoInspeccionTecnica extends BaseAuditoria implements BaseAuditable
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
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="PlanillaInspeccion")
     * @ORM\JoinColumn(name="id_inspeccion_tecnica", referencedColumnName="id", nullable=false)
     */
    private $inspeccionTecnica;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="GrupoPlanillaInspeccion")
     * @ORM\JoinColumn(name="id_grupo_planilla_inspeccion", referencedColumnName="id", nullable=false)
     */
    private $grupoPlanillaInspeccion;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=100)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;


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
     * Set Id Planilla Inspeccion
     *
     * @param \ADIF\InventarioBundle\Entity\InspeccionTecnica $inspeccionTecnica
     * @return GrupoInspeccionTecnica
     */
    public function setInspeccionTecnica(\ADIF\InventarioBundle\Entity\InspeccionTecnica $id)
    {
        $this->inspeccionTecnica = $id;
        return $this;
    }

    /**
     * Get Id Planilla Inspeccion
     *
     * @return \ADIF\InventarioBundle\Entity\InspeccionTecnica
     */
    public function getInspeccionTecnica()
    {
        return $this->inspeccionTecnica ;
    }

    /**
     * Set Id Grupo Planilla Inspeccion
     *
     * @param \ADIF\InventarioBundle\Entity\GrupoPlanillaInspeccion $grupoPlanillaInspeccion
     * @return GrupoInspeccionTecnica
     */
    public function setGrupoPlanillaInspeccion(\ADIF\InventarioBundle\Entity\GrupoPlanillaInspeccion $id)
    {
        $this->grupoPlanillaInspeccion = $id;
        return $this;
    }

    /**
     * Get Id Grupo Planilla Inspeccion
     *
     * @return \ADIF\InventarioBundle\Entity\GrupoPlanillaInspeccion
     */
    public function getGrupoPlanillaInspeccion()
    {
        return $this->grupoPlanillaInspeccion ;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return GrupoInspeccionTecnica
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return GrupoInspeccionTecnica
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return GrupoInspeccionTecnica
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
