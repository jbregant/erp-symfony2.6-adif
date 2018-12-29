<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

use ADIF\InventarioBundle\Entity\MovimientoMaterial;
use ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido;
use ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento;
use ADIF\InventarioBundle\Entity\UnidadMedida;

/**
 * MovimientoInventario
 *
 * @ORM\Table(name="movimiento_inventario")
 * @ORM\Entity
 */
class MovimientoInventario extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="MovimientoMaterial")
     * @ORM\JoinColumn(name="id_movimiento", referencedColumnName="id", nullable=true)
     */
    private $movimiento;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="InventarioMatNuevoProducido")
     * @ORM\JoinColumn(name="id_inventario", referencedColumnName="id", nullable=true)
     */
    private $inventario;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="OrigenDestinoMovimiento")
     * @ORM\JoinColumn(name="id_origen_movimiento", referencedColumnName="id", nullable=true)
     */
    private $origenMovimiento;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="OrigenDestinoMovimiento")
     * @ORM\JoinColumn(name="id_destino_movimiento", referencedColumnName="id", nullable=true)
     */
    private $destinoMovimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_bruto", type="decimal")
     */
    private $pesoBruto;

    /**
     * @var string
     *
     * @ORM\Column(name="peso_neto", type="decimal")
     */
    private $pesoNeto;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Transporte")
     * @ORM\JoinColumn(name="id_transporte", referencedColumnName="id", nullable=true)
     */
    private $transporte;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumn(name="id_unidad_medida", referencedColumnName="id", nullable=true)
     */
    private $unidadMedida;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=100)
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
     * Set Id Movimiento
     *
     * @param \ADIF\InventarioBundle\Entity\MovimientoMaterial $movimiento
     * @return MovimientoInventario
     */
    public function setMovimiento(\ADIF\InventarioBundle\Entity\MovimientoMaterial $id)
    {
        $this->movimiento = $id;
        return $this;
    }

    /**
     * Get Id Movimiento
     *
     * @return \ADIF\InventarioBundle\Entity\MovimientoMaterial
     */
    public function getMovimiento()
    {
        return $this->movimiento;
    }

    /**
     * Set Id Inventario
     *
     * @param \ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido $inventario
     * @return MovimientoInventario
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
     * Set Id Origen Movimiento
     *
     * @param \ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento $origenMovimiento
     * @return MovimientoInventario
     */
    public function setOrigenMovimiento(\ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento $id)
    {
        $this->origenMovimiento = $id;
        return $this;
    }

    /**
     * Get Id Origen Movimiento
     *
     * @return \ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento
     */
    public function getOrigenMovimiento()
    {
        return $this->origenMovimiento;
    }

    /**
     * Set Id Destino Movimiento
     *
     * @param \ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento $destinoMovimiento
     * @return MovimientoInventario
     */
    public function setDestinoMovimiento(\ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento $id)
    {
        $this->destinoMovimiento = $id;
        return $this;
    }

    /**
     * Get Id Destino Movimiento
     *
     * @return \ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento
     */
    public function getDestinoMovimiento()
    {
        return $this->destinoMovimiento;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     * @return MovimientoInventario
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
     * Set pesoBruto
     *
     * @param string $pesoBruto
     * @return MovimientoInventario
     */
    public function setPesoBruto($pesoBruto)
    {
        $this->pesoBruto = $pesoBruto;

        return $this;
    }

    /**
     * Get pesoBruto
     *
     * @return string
     */
    public function getPesoBruto()
    {
        return $this->pesoBruto;
    }

    /**
     * Set pesoNeto
     *
     * @param string $pesoNeto
     * @return MovimientoInventario
     */
    public function setPesoNeto($pesoNeto)
    {
        $this->pesoNeto = $pesoNeto;

        return $this;
    }

    /**
     * Get pesoNeto
     *
     * @return string
     */
    public function getPesoNeto()
    {
        return $this->pesoNeto;
    }

    /**
     * Set Id Transporte
     *
     * @param \ADIF\InventarioBundle\Entity\Transporte $transporte
     * @return MovimientoInventario
     */
    public function setTransporte(\ADIF\InventarioBundle\Entity\Transporte $id)
    {
        $this->transporte = $id;
        return $this;
    }

    /**
     * Get Id Transporte
     *
     * @return \ADIF\InventarioBundle\Entity\Transporte
     */
    public function getTransporte()
    {
        return $this->transporte;
    }

    /**
     * Set Id Unidad Medida
     *
     * @param \ADIF\InventarioBundle\Entity\UnidadMedida $unidadMedida
     * @return MovimientoInventario
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
     * Set observaciones
     *
     * @param string $observaciones
     * @return MovimientoInventario
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
     * @return MovimientoInventario
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
}
