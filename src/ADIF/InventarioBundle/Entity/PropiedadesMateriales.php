<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra;
use ADIF\InventarioBundle\Entity\PropiedadValor;

/**
 * PropiedadesMateriales
 *
 * @ORM\Table(name="propiedad_material")
 * @ORM\Entity
 */
class PropiedadesMateriales extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesNuevos")
     * @ORM\JoinColumn(name="id_catalogo_material_nuevo", referencedColumnName="id", nullable=false)
     */
    private $catalogoMaterialNuevo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesProducidosDeObra")
     * @ORM\JoinColumn(name="id_catalogo_material_producido_obra", referencedColumnName="id", nullable=false)
     */
    private $catalogoMaterialProducidoObra;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="PropiedadValor")
     * @ORM\JoinColumn(name="id_propiedad_valor", referencedColumnName="id", nullable=false)
     */
    private $propiedadValor;

    public function __construct() {
        $this->catalogoMaterialNuevo = new ArrayCollection();
        $this->catalogoMaterialProducidoObra = new ArrayCollection();
        $this->propiedadValor = new ArrayCollection();
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
     * @return PropiedadesMateriales
     */
    public function setCatalogoMaterialNuevo(CatalogoMaterialesNuevos $catalogoMaterialNuevo = null)
    {
        $this->catalogoMaterialNuevo = $catalogoMaterialNuevo;
    
        return $this;
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
     * Set catalogoMaterialProducidoObra
     *
     * @param integer $catalogoMaterialProducidoObra
     * @return PropiedadesMateriales
     */
    public function setCatalogoMaterialProducidoObra(CatalogoMaterialesProducidosDeObra $catalogoMaterialProducidoObra = null)
    {
        $this->catalogoMaterialProducidoObra = $catalogoMaterialProducidoObra;
    
        return $this;
    }

    /**
     * Get catalogoMaterialProducidoObra
     *
     * @return integer 
     */
    public function getCatalogoMaterialProducidoObra()
    {
        return $this->catalogoMaterialProducidoObra;
    }

    /**
     * Set propiedadValor
     *
     * @param integer $propiedadValor
     * @return PropiedadesMateriales
     */
    public function setPropiedadValor(PropiedadValor $propiedadValor = null)
    {
        $this->propiedadValor = $propiedadValor;
    
        return $this;
    }

    /**
     * Get propiedadValor
     *
     * @return integer 
     */
    public function getPropiedadValor()
    {
        return $this->propiedadValor;
    }
}
