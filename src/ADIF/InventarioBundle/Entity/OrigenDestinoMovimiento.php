<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

use ADIF\InventarioBundle\Entity\Origen;
use ADIF\InventarioBundle\Entity\Fabricante;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\InventarioBundle\Entity\Almacen;

/**
 * OrigenDestinoMovimiento
 *
 * @ORM\Table(name="origen_destino_movimiento")
 * @ORM\Entity
 */
class OrigenDestinoMovimiento extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Origen")
     * @ORM\JoinColumn(name="id_origen", referencedColumnName="id")
     */
    private $origen;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Fabricante")
     * @ORM\JoinColumn(name="id_fabricante", referencedColumnName="id")
     */
    private $fabricante;

    /**
     * @var  integer
     *
     * @ORM\Column(name="id_proveedor", type="integer", nullable=true)
     */
    private $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Almacen")
     * @ORM\JoinColumn(name="id_almacen", referencedColumnName="id")
     */
    private $almacen;

    /**
     * @var string
     *
     * @ORM\Column(name="otro_destino", type="string", length=100)
     */
    private $otroDestino;

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
     * Set Id Origen
     *
     * @param \ADIF\InventarioBundle\Entity\Origen $origen
     * @return OrigenDestinoMovimiento
     */
    public function setOrigen(\ADIF\InventarioBundle\Entity\Origen $id)
    {
        $this->origen = $id;
        return $this;
    }

    /**
     * Get Id Origen
     *
     * @return \ADIF\InventarioBundle\Entity\Origen
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set Id Fabricante
     *
     * @param \ADIF\InventarioBundle\Entity\Fabricante $fabricante
     * @return OrigenDestinoMovimiento
     */
    public function setFabricante(\ADIF\InventarioBundle\Entity\Fabricante $id)
    {
        $this->fabricante = $id;
        return $this;
    }

    /**
     * Get Id Fabricante
     *
     * @return \ADIF\InventarioBundle\Entity\Fabricante
     */
    public function getFabricante()
    {
        return $this->fabricante;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor)
    {
        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * Get proveedor
     *
     * @return integer
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set Id Almacen
     *
     * @param \ADIF\InventarioBundle\Entity\Almacen $almacen
     * @return OrigenDestinoMovimiento
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
     * Set Otro Destino
     *
     * @param string $otroDestino
     * @return OrigenDestinoMovimiento
     */
    public function setOtroDestino($otroDestino)
    {
        $this->otroDestino = $otroDestino;
        return $this;
    }

    /**
     * Get Otro Destino
     *
     * @return string
     */
    public function getOtroDestino()
    {
        return $this->otroDestino;
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
}
