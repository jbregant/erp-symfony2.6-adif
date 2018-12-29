<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\RecursosHumanosBundle\Entity\Provincia;
use ADIF\InventarioBundle\Entity\HojaRuta;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Almacen;
use ADIF\InventarioBundle\Entity\TipoMaterial;
use ADIF\InventarioBundle\Entity\GrupoMaterial;
use ADIF\InventarioBundle\Entity\EstadoConservacion;
use ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido;

/**
 * ItemHojaRutaNuevoProducido
 *
 * @ORM\Table("item_hoja_ruta_nuevo_producido")
 * @ORM\Entity
 */
class ItemHojaRutaNuevoProducido extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="HojaRuta", inversedBy="itemsHojaRutaNuevoProducido")
     * @ORM\JoinColumn(name="id_hoja_ruta", referencedColumnName="id", nullable=false)
     */
    private $hojaRuta;

     /**
     * @ORM\Column(name="id_provincia", type="integer", nullable=true)
     */
    private $idProvincia;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Provincia
     */
    protected $provincia;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $linea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Almacen")
     * @ORM\JoinColumn(name="id_almacen", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $almacen;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoMaterial")
     * @ORM\JoinColumn(name="id_tipo_material", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoMaterial;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="GrupoMaterial")
     * @ORM\JoinColumn(name="id_grupo_material", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    private $grupoMaterial;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoConservacion")
     * @ORM\JoinColumn(name="id_estado_conservacion", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    private $estadoConservacion;

    /** @var  integer
     *
     * @ORM\ManyToOne(targetEntity="InventarioMatNuevoProducido")
     * @ORM\JoinColumn(name="id_inventario", referencedColumnName="id", nullable=true)
     */
    private $inventario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="itemRelevado", type="boolean", nullable=false)
     */
    private $itemRelevado;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

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
     * Set hojaRuta
     *
     * @param integer $hojaRuta
     * @return ItemHojaRutaNuevoProducido
     */
    public function setHojaRuta($hojaRuta)
    {
        $this->hojaRuta = $hojaRuta;
    
        return $this;
    }

    /**
     * Get hojaRuta
     *
     * @return integer 
     */
    public function getHojaRuta()
    {
        return $this->hojaRuta;
    }

    /**
     * Set linea
     *
     * @param integer $linea
     * @return ItemHojaRutaNuevoProducido
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;
    
        return $this;
    }

    /**
     * Get linea
     *
     * @return integer 
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set almacen
     *
     * @param integer $almacen
     * @return ItemHojaRutaNuevoProducido
     */
    public function setAlmacen($almacen)
    {
        $this->almacen = $almacen;
    
        return $this;
    }

    /**
     * Get almacen
     *
     * @return integer 
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set tipoMaterial
     *
     * @param integer $tipoMaterial
     * @return ItemHojaRutaNuevoProducido
     */
    public function setTipoMaterial($tipoMaterial)
    {
        $this->tipoMaterial = $tipoMaterial;
    
        return $this;
    }

    /**
     * Get tipoMaterial
     *
     * @return integer 
     */
    public function getTipoMaterial()
    {
        return $this->tipoMaterial;
    }

    /**
     * Set grupoMaterial
     *
     * @param integer $grupoMaterial
     * @return ItemHojaRutaNuevoProducido
     */
    public function setGrupoMaterial($grupoMaterial)
    {
        $this->grupoMaterial = $grupoMaterial;
    
        return $this;
    }

    /**
     * Get grupoMaterial
     *
     * @return integer 
     */
    public function getGrupoMaterial()
    {
        return $this->grupoMaterial;
    }

    /**
     * Set estadoConservacion
     *
     * @param integer $estadoConservacion
     * @return ItemHojaRutaNuevoProducido
     */
    public function setEstadoConservacion($estadoConservacion)
    {
        $this->estadoConservacion = $estadoConservacion;
    
        return $this;
    }

    /**
     * Get estadoConservacion
     *
     * @return integer 
     */
    public function getEstadoConservacion()
    {
        return $this->estadoConservacion;
    }

    /**
     * Set inventario
     *
     * @param integer $inventario
     * @return ItemHojaRutaNuevoProducido
     */
    public function setInventario($inventario)
    {
        $this->inventario = $inventario;
    
        return $this;
    }

    /**
     * Get inventario
     *
     * @return integer 
     */
    public function getInventario()
    {
        return $this->inventario;
    }
    
    //Provincia de RecursosHumanosBundle:

    public function getIdProvincia()
    {
        return $this->idProvincia;
    }

    public function setIdProvincia($idProvincia)
    {
        $this->idProvincia = $idProvincia;

        return $this;
    }

    /**
     * Set provincia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Provincia $provincia
     */
    public function setProvincia($provincia)
    {
        if (null != $provincia) {
            $this->idProvincia = $provincia->getId();
        } else {
            $this->idProvincia = null;
        }

        $this->provincia = $provincia;
    }

    /**
     * Get provincia
     *
     * @return integer
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set itemRelevado
     *
     * @param boolean $itemRelevado
     * @return ItemHojaRutaNuevoProducido
     */
    public function setItemRelevado($itemRelevado)
    {
        $this->itemRelevado = $itemRelevado;
    
        return $this;
    }

    /**
     * Get itemRelevado
     *
     * @return boolean 
     */
    public function getItemRelevado()
    {
        return $this->itemRelevado;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return ItemHojaRutaNuevoProducido
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return ItemHojaRutaNuevoProducido
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
