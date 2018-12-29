<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\Relevamiento;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes;
use ADIF\InventarioBundle\Entity\ActivoLineal;
use ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido;

/**
 * ItemRelevamiento
 *
 * @ORM\Table(name="item_relevamiento")
 * @ORM\Entity
 */
class ItemRelevamiento extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Relevamiento")
     * @ORM\JoinColumn(name="id_relevamiento", referencedColumnName="id", nullable=false)
     */
    private $relevamiento;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesRodantes")
     * @ORM\JoinColumn(name="id_material_rodante", referencedColumnName="id")
     */
    private $materialRodante;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="ActivoLineal")
     * @ORM\JoinColumn(name="id_activo_lineal", referencedColumnName="id")
     */
    private $activoLineal;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="InventarioMatNuevoProducido")
     * @ORM\JoinColumn(name="id_inventario", referencedColumnName="id")
     */
    private $inventario;

    /**
     * @var string
     *
     * @ORM\Column(name="latitud", type="string", length=100)
     */
    private $latitud;

    /**
     * @var string
     *
     * @ORM\Column(name="longitud", type="string", length=100)
     */
    private $longitud;

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
     * Set Id Relevamiento
     *
     * @param \ADIF\InventarioBundle\Entity\Relevamiento $relevamiento
     * @return ItemRelevamiento
     */
    public function setRelevamiento(\ADIF\InventarioBundle\Entity\Relevamiento $id)
    {
        $this->relevamiento = $id;
        return $this;
    }

    /**
     * Get Id Relevamiento
     *
     * @return \ADIF\InventarioBundle\Entity\Relevamiento
     */
    public function getRelevamiento()
    {
        return $this->relevamiento;
    }

    /**
     * Set Id Material Rodantes
     *
     * @param \ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes $materialRodante
     * @return ItemRelevamiento
     */
    public function setMaterialRodante(\ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes $id)
    {
        $this->materialRodante = $id;
        return $this;
    }

    /**
     * Get Id Material Rodantes
     *
     * @return \ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes
     */
    public function getMaterialRodante()
    {
        return $this->materialRodante;
    }

    /**
     * Set Id Activo Lineal
     *
     * @param \ADIF\InventarioBundle\Entity\ActivoLineal $activoLineal
     * @return ItemRelevamiento
     */
    public function setActivoLineal(\ADIF\InventarioBundle\Entity\ActivoLineal $id)
    {
        $this->activoLineal = $id;
        return $this;
    }

    /**
     * Get Id Activo Lineal
     *
     * @return \ADIF\InventarioBundle\Entity\ActivoLineal
     */
    public function getActivoLineal()
    {
        return $this->activoLineal;
    }

    /**
     * Set Id Inventario
     *
     * @param \ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido $inventario
     * @return ItemRelevamiento
     */
    public function setInventario(\ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido $id)
    {
        $this->inventario = $id;
        return $this;
    }

    /**
     * Get Id Inventario
     *
     * @return \ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido
     */
    public function getInventario()
    {
        return $this->inventario;
    }

    /**
     * Set latitud
     *
     * @param string $latitud
     * @return ItemRelevamiento
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Get latitud
     *
     * @return string
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longitud
     *
     * @param string $longitud
     * @return ItemRelevamiento
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return string
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return ItemRelevamiento
     */
    public function setIdEmpresa($id)
    {
        $this->idEmpresa = $id;
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
