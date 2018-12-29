<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

use ADIF\InventarioBundle\Entity\TransporteMaritimo;
use ADIF\InventarioBundle\Entity\TransporteCamion;
use ADIF\InventarioBundle\Entity\TransporteTren;
use ADIF\ComprasBundle\Entity\Proveedor;

/**
 * Transporte
 *
 * @ORM\Table(name="transporte")
 * @ORM\Entity
 */
class Transporte extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="TransporteMaritimo")
     * @ORM\JoinColumn(name="id_transporte_maritimo", referencedColumnName="id", nullable=false)
     */
    private $transporteMaritimo;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="TransporteCamion")
     * @ORM\JoinColumn(name="id_transporte_camion", referencedColumnName="id", nullable=false)
     */
    private $transporteCamion;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="TransporteTren")
     * @ORM\JoinColumn(name="id_transporte_tren", referencedColumnName="id", nullable=false)
     */
    private $transporteTren;

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
     * Set Id Transporte Maritimo
     *
     * @param \ADIF\InventarioBundle\Entity\TransporteMaritimo $transporteMaritimo
     * @return Transporte
     */
    public function setTransporteMaritimo(\ADIF\InventarioBundle\Entity\TransporteMaritimo $id)
    {
        $this->transporteMaritimo = $id;
        return $this;
    }

    /**
     * Get Id Transporte Maritimo
     *
     * @return \ADIF\InventarioBundle\Entity\TransporteMaritimo
     */
    public function getTransporteMaritimo()
    {
        return $this->transporteMaritimo;
    }

    /**
     * Set Id Transporte Camion
     *
     * @param \ADIF\InventarioBundle\Entity\TransporteCamion $transporteCamion
     * @return Transporte
     */
    public function setTransporteCamion(\ADIF\InventarioBundle\Entity\TransporteCamion $id)
    {
        $this->transporteCamion = $id;
        return $this;
    }

    /**
     * Get Id Transporte Camion
     *
     * @return \ADIF\InventarioBundle\Entity\TransporteCamion
     */
    public function getTransporteCamion()
    {
        return $this->transporteCamion;
    }

    /**
     * Set Id Transporte Tren
     *
     * @param \ADIF\InventarioBundle\Entity\TransporteTren $transporteTren
     * @return Transporte
     */
    public function setTransporteTren(\ADIF\InventarioBundle\Entity\TransporteTren $id)
    {
        $this->transporteTren = $id;
        return $this;
    }

    /**
     * Get Id Transporte Tren
     *
     * @return \ADIF\InventarioBundle\Entity\TransporteTren
     */
    public function getTransporteTren()
    {
        return $this->transporteTren;
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
     * Set observaciones
     *
     * @param string $observaciones
     * @return Transporte
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
     * @return Transporte
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
