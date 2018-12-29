<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra;
use ADIF\InventarioBundle\Entity\TipoMaterial;
use ADIF\InventarioBundle\Entity\Almacen;
use ADIF\InventarioBundle\Entity\UnidadMedida;
use ADIF\InventarioBundle\Entity\EstadoConservacion;
use ADIF\InventarioBundle\Entity\EstadoServicio;

/**
 * InventarioMatNuevoProducido
 *
 * @ORM\Table(name="inventario_mat_nuevo_producido")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\InventarioMatNuevoProducidoRepository")
 */
class InventarioMatNuevoProducido extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesNuevos")
     * @ORM\JoinColumn(name="id_material_nuevo", referencedColumnName="id")
     */
    private $materialNuevo;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesProducidosDeObra")
     * @ORM\JoinColumn(name="id_producido_obra", referencedColumnName="id")
     */
    private $materialProducidoObra;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="TipoMaterial")
     * @ORM\JoinColumn(name="id_tipo_material", referencedColumnName="id", nullable=false)
     */
    private $tipoMaterial;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Almacen")
     * @ORM\JoinColumn(name="id_almacen", referencedColumnName="id", nullable=false)
     */
    private $almacen;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Almacen")
     * @ORM\JoinColumn(name="id_buque", referencedColumnName="id", nullable=false)
     */
    private $buque;

    /**
     * @var string
     *
     * @ORM\Column(name="num", type="string", length=100)
     */
    private $num;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id", nullable=true)
     */
    private $unidadMedida;

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
     * @ORM\Column(name="es_item_por_lote", type="boolean")
     */
    private $esItemPorLote;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_valoracion", type="string", length=50)
     */
    private $metodoValoracion;

    /**
     * @var string
     *
     * @ORM\Column(name="ubicacion", type="string", length=100)
     */
    private $ubicacion;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal")
     */
    private $cantidad;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoServicio")
     * @ORM\JoinColumn(name="id_estado_servicio", referencedColumnName="id", nullable=true)
     */
    private $estadoServicio;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_lote", type="string", length=20)
     */
    private $numeroLote;

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
     * Set Id Material Nuevo
     *
     * @param \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos $materialNuevo
     * @return InventarioMatNuevoProducido
     */
    public function setMaterialNuevo(\ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos $id)
    {
        $this->materialNuevo = $id;
        return $this;
    }

    /**
     * Get Id Material Nuevo
     *
     * @return \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos
     */
    public function getMaterialNuevo()
    {
        return $this->materialNuevo;
    }

    /**
     * Set Id Material Producido Obra
     *
     * @param \ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra $materialProducidoObra
     * @return InventarioMatNuevoProducido
     */
    public function setMaterialProducidoObra(\ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra $id)
    {
        $this->materialProducidoObra = $id;
        return $this;
    }

    /**
     * Get Id Material Nuevo
     *
     * @return \ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra
     */
    public function getMaterialProducidoObra()
    {
        return $this->materialProducidoObra;
    }

    /**
     * Set Id Tipo Material
     *
     * @param \ADIF\InventarioBundle\Entity\TipoMaterial $tipoMaterial
     * @return InventarioMatNuevoProducido
     */
    public function setTipoMaterial(\ADIF\InventarioBundle\Entity\TipoMaterial $id)
    {
        $this->tipoMaterial = $id;
        return $this;
    }

    /**
     * Get Id Tipo Material
     *
     * @return \ADIF\InventarioBundle\Entity\TipoMaterial
     */
    public function getTipoMaterial()
    {
        return $this->tipoMaterial;
    }

    /**
     * Set Id Almacen
     *
     * @param \ADIF\InventarioBundle\Entity\Almacen $almacen
     * @return InventarioMatNuevoProducido
     */
    public function setAlmacen(\ADIF\InventarioBundle\Entity\Almacen $id)
    {
        $this->almacen = $id;
        return $this;
    }

    /**
     * Get Id Almacen
     *
     * @return \ADIF\InventarioBundle\Entity\Almacen
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set Id Buque
     *
     * @param \ADIF\InventarioBundle\Entity\Almacen $buque
     * @return InventarioMatNuevoProducido
     */
    public function setBuque(\ADIF\InventarioBundle\Entity\Almacen $id)
    {
        $this->buque = $id;
        return $this;
    }

    /**
     * Get Id Buque
     *
     * @return \ADIF\InventarioBundle\Entity\Almacen
     */
    public function getBuque()
    {
        return $this->buque;
    }

    /**
     * Set num
     *
     * @param string $num
     * @return InventarioMatNuevoProducido
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set Id Unidad Medida
     *
     * @param \ADIF\InventarioBundle\Entity\UnidadMedida $unidadMedida
     * @return InventarioMatNuevoProducido
     */
    public function setUnidadMedida(\ADIF\InventarioBundle\Entity\UnidadMedida $id)
    {
        $this->unidadMedida = $id;
        return $this;
    }

    /**
     * Get Id Unidad Medida
     *
     * @return \ADIF\InventarioBundle\Entity\UnidadMedida
     */
    public function getUnidadMedida()
    {
        return $this->unidadMedida;
    }

    /**
     * Set Id Estado Conservacion
     *
     * @param \ADIF\InventarioBundle\Entity\EstadoConservacion $estadoConservacion
     * @return InventarioMatNuevoProducido
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
        return $this->estadoConservacion;
    }

    /**
     * Set esItemPorLote
     *
     * @param boolean $esItemPorLote
     * @return InventarioMatNuevoProducido
     */
    public function setEsItemPorLote($esItemPorLote)
    {
        $this->esItemPorLote = $esItemPorLote;

        return $this;
    }

    /**
     * Get esItemPorLote
     *
     * @return boolean
     */
    public function getEsItemPorLote()
    {
        return $this->esItemPorLote;
    }

    /**
     * Set metodoValoracion
     *
     * @param string $metodoValoracion
     * @return InventarioMatNuevoProducido
     */
    public function setMetodoValoracion($metodoValoracion)
    {
        $this->metodoValoracion = $metodoValoracion;

        return $this;
    }

    /**
     * Get metodoValoracion
     *
     * @return string
     */
    public function getMetodoValoracion()
    {
        return $this->metodoValoracion;
    }

    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return InventarioMatNuevoProducido
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     * @return InventarioMatNuevoProducido
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set Id Estado Servicio
     *
     * @param \ADIF\InventarioBundle\Entity\EstadoServicio $estadoServicio
     * @return InventarioMatNuevoProducido
     */
    public function setEstadoServicio(\ADIF\InventarioBundle\Entity\EstadoServicio $id)
    {
        $this->estadoServicio = $id;
        return $this;
    }

    /**
     * Get Id Estado Servicio
     *
     * @return \ADIF\InventarioBundle\Entity\EstadoServicio
     */
    public function getEstadoServicio()
    {
        return $this->estadoServicio;
    }

    /**
     * Set numeroLote
     *
     * @param string $numeroLote
     * @return InventarioMatNuevoProducido
     */
    public function setNumeroLote($numeroLote)
    {
        $this->numeroLote = $numeroLote;

        return $this;
    }

    /**
     * Get numeroLote
     *
     * @return string
     */
    public function getNumeroLote()
    {
        return $this->numeroLote;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return InventarioMatNuevoProducido
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
     * Set Empresa
     *
     * @param integer $idEmpresa
     * @return MovimientoMaterial
     */
    public function setIdEmpresa($id)
    {
        $this->idEmpresa = $id;

        return $this;
    }

    /**
     * Get Empresa
     *
     * @return integer
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

     public function __toString()
    {
        return $this->getNum();
    }
}
