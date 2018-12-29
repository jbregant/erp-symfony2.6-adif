<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos;
use ADIF\InventarioBundle\Entity\UnidadMedida;
use ADIF\InventarioBundle\Entity\GrupoAduana;

/**
 * CatalogoMaterialesNuevosCompra
 *
 * @ORM\Table(name="catalogo_material_nuevo_compra")
 * @ORM\Entity
 */
class CatalogoMaterialesNuevosCompra extends BaseAuditoria implements BaseAuditable
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
     * @ORM\OneToOne(targetEntity="CatalogoMaterialesNuevos", inversedBy="catalogoMaterialesNuevosCompra", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_catalogo_material_nuevo", referencedColumnName="id", nullable=false)
     */
    private $catalogoMaterialesNuevos;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id", nullable=true)
     */
    private $unidadMedida;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_por_unidad_compra", type="integer", nullable=true)
     */
    private $itemPorUnidadCompra;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumn(name="id_unidad_medida_packaging", referencedColumnName="id", nullable=true)
     */
    private $unidadMedidaPackaging;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="GrupoAduana")
     * @ORM\JoinColumn(name="id_grupo_aduana", referencedColumnName="id", nullable=true)
     */
    private $grupoAduana;

    /**
     * @var integer
     *
     * @ORM\Column(name="factor_1", type="integer", nullable=true)
     */
    private $factor1;

    /**
     * @var integer
     *
     * @ORM\Column(name="factor_2", type="integer", nullable=true)
     */
    private $factor2;

    /**
     * @var integer
     *
     * @ORM\Column(name="factor_3", type="integer", nullable=true)
     */
    private $factor3;

    /**
     * @var integer
     *
     * @ORM\Column(name="factor_4", type="integer", nullable=true)
     */
    private $factor4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;

    public function __construct() {
//        $this->catalogoMaterialesNuevos = new ArrayCollection();
//        $this->catalogoMaterialesNuevos = new CatalogoMaterialesNuevos();
        $this->unidadMedida = new ArrayCollection();
        $this->grupoAduana = new ArrayCollection();
//        $this->idEmpresa = 1;
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
     * Set catalogoMaterialNuevo
     *
     * @param integer $catalogoMaterialNuevo
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setCatalogoMaterialNuevo(CatalogoMaterialesNuevos $catalogoMaterialNuevo = null)
    {
        $this->catalogoMaterialNuevo = $catalogoMaterialNuevo;
        $catalogoMaterialNuevo->setCatalogoMaterialesNuevosCompra($this);

        return $this;
    }

    /**
     * Set CatalogoMaterialesNuevos
     *
     * @param integer $catalogoMaterialesNuevos
     * @return CatalogoMaterialesNuevos
     */
    public function setCatalogoMaterialesNuevos($catalogoMaterialesNuevos)
    {
        $this->catalogoMaterialesNuevos = $catalogoMaterialesNuevos;

        return $this;
    }

    /**
     * Get CatalogoMaterialesNuevos
     *
     * @return integer
     */
    public function getCatalogoMaterialesNuevos()
    {
        return $this->catalogoMaterialesNuevos;
    }



    /**
     * Get catalogoMaterialNuevo
     *
     * @return integer
     */
    public function getCatalogoMaterialNuevo()
    {
        return $this->catalogoMaterialNuevo;
    }

    /**
     * Set unidadMedida
     *
     * @param integer $unidadMedida
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setUnidadMedida(UnidadMedida $unidadMedida = null)
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return integer
     */
    public function getUnidadMedida()
    {
        return $this->unidadMedida;
    }

    /**
     * Set itemPorUnidadCompra
     *
     * @param integer $itemPorUnidadCompra
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setItemPorUnidadCompra($itemPorUnidadCompra)
    {
        $this->itemPorUnidadCompra = $itemPorUnidadCompra;

        return $this;
    }

    /**
     * Get itemPorUnidadCompra
     *
     * @return integer
     */
    public function getItemPorUnidadCompra()
    {
        return $this->itemPorUnidadCompra;
    }

    /**
     * Set unidadMedidaPackaging
     *
     * @param integer $unidadMedidaPackaging
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setUnidadMedidaPackaging(UnidadMedida $unidadMedidaPackaging = null)
    {
        $this->unidadMedidaPackaging = $unidadMedidaPackaging;

        return $this;
    }

    /**
     * Get unidadMedidaPackaging
     *
     * @return integer
     */
    public function getUnidadMedidaPackaging()
    {
        return $this->unidadMedidaPackaging;
    }

    /**
     * Set grupoAduana
     *
     * @param integer $grupoAduana
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setGrupoAduana(GrupoAduana $grupoAduana = null)
    {
        $this->grupoAduana = $grupoAduana;

        return $this;
    }

    /**
     * Get grupoAduana
     *
     * @return integer
     */
    public function getGrupoAduana()
    {
        return $this->grupoAduana;
    }

    /**
     * Set factor1
     *
     * @param integer $factor1
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setFactor1($factor1)
    {
        $this->factor1 = $factor1;

        return $this;
    }

    /**
     * Get factor1
     *
     * @return integer
     */
    public function getFactor1()
    {
        return $this->factor1;
    }

    /**
     * Set factor2
     *
     * @param integer $factor2
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setFactor2($factor2)
    {
        $this->factor2 = $factor2;

        return $this;
    }

    /**
     * Get factor2
     *
     * @return integer
     */
    public function getFactor2()
    {
        return $this->factor2;
    }

    /**
     * Set factor3
     *
     * @param integer $factor3
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setFactor3($factor3)
    {
        $this->factor3 = $factor3;

        return $this;
    }

    /**
     * Get factor3
     *
     * @return integer
     */
    public function getFactor3()
    {
        return $this->factor3;
    }

    /**
     * Set factor4
     *
     * @param integer $factor4
     * @return CatalogoMaterialesNuevosCompra
     */
    public function setFactor4($factor4)
    {
        $this->factor4 = $factor4;

        return $this;
    }

    /**
     * Get factor4
     *
     * @return integer
     */
    public function getFactor4()
    {
        return $this->factor4;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return CatalogoMaterialesNuevosCompra
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
