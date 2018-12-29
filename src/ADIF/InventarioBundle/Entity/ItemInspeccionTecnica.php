<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\GrupoInspeccionTecnica;
use ADIF\InventarioBundle\Entity\ItemGrupoInspeccion;
use ADIF\InventarioBundle\Entity\EstadoConservacion;

/**
 * ItemInspeccionTecnica
 *
 * @ORM\Table(name="item_inspeccion_tecnica")
 * @ORM\Entity
 */
class ItemInspeccionTecnica extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="GrupoInspeccionTecnica")
     * @ORM\JoinColumn(name="id_grupo_inspeccion_tecnica", referencedColumnName="id", nullable=false)
     */
    private $grupoInspeccionTecnica;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="ItemGrupoInspeccion")
     * @ORM\JoinColumn(name="id_item_grupo_inspeccion", referencedColumnName="id", nullable=false)
     */
    private $itemGrupoInspeccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="descripcion_adicional", type="integer")
     */
    private $descripcionAdicional;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoConservacion")
     * @ORM\JoinColumn(name="id_estado_conservacion", referencedColumnName="id", nullable=false)
     */
    private $estadoConservacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="posee_componente", type="boolean")
     */
    private $poseeComponente;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje_faltante", type="decimal")
     */
    private $porcentajeFaltante;

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
     * Set Id Grupo Inspeccion Tecnica
     *
     * @param \ADIF\InventarioBundle\Entity\GrupoInspeccionTecnica $grupoInspeccionTecnica
     * @return ItemInspeccionTecnica
     */
    public function setGrupoInspeccionTecnica(\ADIF\InventarioBundle\Entity\GrupoInspeccionTecnica $id)
    {
        $this->grupoInspeccionTecnica = $id;
        return $this;
    }

    /**
     * Get Id Grupo Inspeccion Tecnica
     *
     * @return \ADIF\InventarioBundle\Entity\GrupoInspeccionTecnica
     */
    public function getGrupoInspeccionTecnica()
    {
        return $this->grupoInspeccionTecnica ;
    }

    /**
     * Set Id Item Grupo Inspeccion
     *
     * @param \ADIF\InventarioBundle\Entity\ItemGrupoInspeccion $itemGrupoInspeccion
     * @return ItemInspeccionTecnica
     */
    public function setItemGrupoInspeccion(\ADIF\InventarioBundle\Entity\ItemGrupoInspeccion $id)
    {
        $this->itemGrupoInspeccion = $id;
        return $this;
    }

    /**
     * Get Id Item Grupo Inspeccion
     *
     * @return \ADIF\InventarioBundle\Entity\ItemGrupoInspeccion
     */
    public function getItemGrupoInspeccion()
    {
        return $this->itemGrupoInspeccion ;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return ItemInspeccionTecnica
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set descripcionAdicional
     *
     * @param integer $descripcionAdicional
     * @return ItemInspeccionTecnica
     */
    public function setDescripcionAdicional($descripcionAdicional)
    {
        $this->descripcionAdicional = $descripcionAdicional;

        return $this;
    }

    /**
     * Get descripcionAdicional
     *
     * @return integer
     */
    public function getDescripcionAdicional()
    {
        return $this->descripcionAdicional;
    }

    /**
     * Set Id Estado Conservacion
     *
     * @param \ADIF\InventarioBundle\Entity\EstadoConservacion $estadoConservacion
     * @return ItemInspeccionTecnica
     */
    public function setEstadoConservacion(\ADIF\InventarioBundle\Entity\EstadoConservacion $id)
    {
        $this->estadoConservacion = $id;
        return $this;
    }

    /**
     * Get Id Estado Conservacion
     *
     * @return \ADIF\InventarioBundle\Entity\EstadoConservacion
     */
    public function getEstadoConservacion()
    {
        return $this->estadoConservacion ;
    }

    /**
     * Set poseeComponente
     *
     * @param boolean $poseeComponente
     * @return ItemInspeccionTecnica
     */
    public function setPoseeComponente($poseeComponente)
    {
        $this->poseeComponente = $poseeComponente;

        return $this;
    }

    /**
     * Get poseeComponente
     *
     * @return boolean
     */
    public function getPoseeComponente()
    {
        return $this->poseeComponente;
    }

    /**
     * Set porcentajeFaltante
     *
     * @param string $porcentajeFaltante
     * @return ItemInspeccionTecnica
     */
    public function setPorcentajeFaltante($porcentajeFaltante)
    {
        $this->porcentajeFaltante = $porcentajeFaltante;

        return $this;
    }

    /**
     * Get porcentajeFaltante
     *
     * @return string
     */
    public function getPorcentajeFaltante()
    {
        return $this->porcentajeFaltante;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return ItemInspeccionTecnica
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
