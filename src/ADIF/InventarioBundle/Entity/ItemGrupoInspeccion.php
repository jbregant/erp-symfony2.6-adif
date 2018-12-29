<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\GrupoPlanillaInspeccion;

/**
 * ItemGrupoInspeccion
 *
 * @ORM\Table(name="item_grupo_inspeccion")
 * @ORM\Entity
 */
class ItemGrupoInspeccion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="GrupoPlanillaInspeccion")
     * @ORM\JoinColumn(name="id_grupo_inspeccion", referencedColumnName="id", nullable=false)
     */
    private $grupoInspeccion;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100)
     */
    private $denominacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;

    
    public function __construct() {
        $this->grupoInspeccion = new ArrayCollection();
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
     * Set grupoInspeccion
     *
     * @param integer $grupoInspeccion
     * @return ItemGrupoInspeccion
     */
    public function setGrupoInspeccion(GrupoPlanillaInspeccion $grupoInspeccion)
    {
        $this->grupoInspeccion = $grupoInspeccion;
    
        return $this;
    }

    /**
     * Get grupoInspeccion
     *
     * @return integer 
     */
    public function getGrupoInspeccion()
    {
        return $this->grupoInspeccion;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return ItemGrupoInspeccion
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return ItemGrupoInspeccion
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
