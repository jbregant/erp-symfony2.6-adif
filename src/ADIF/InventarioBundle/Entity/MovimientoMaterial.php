<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;


use ADIF\InventarioBundle\Entity\TiposMovimientoMaterial;
use ADIF\InventarioBundle\Entity\EstadoMovimiento;
use ADIF\InventarioBundle\Entity\MovimientoMaterial;
use ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido;
use ADIF\InventarioBundle\Entity\OrigenDestinoMovimiento;
use ADIF\InventarioBundle\Entity\UnidadMedida;
use ADIF\InventarioBundle\Entity\Transporte;
use ADIF\ComprasBundle\Entity\Proveedor;

/**
 * MovimientoMaterial
 *
 * @ORM\Table(name="movimiento_material")
 * @ORM\Entity
 */
class MovimientoMaterial extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="numero_acta_expediente", type="string", length=100)
     */
    private $numeroActaExpediente;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_subasta", type="string", length=100)
     */
    private $numeroSubasta;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="TiposMovimientoMaterial")
     * @ORM\JoinColumn(name="id_tipo_movimiento", referencedColumnName="id")
     */
    private $tipoMovimiento;

    /**
     * @var  integer
     *
     * @ORM\Column(name="id_despachante", type="integer", nullable=true)
     */
    private $idDespachante;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $despachante;

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\InventarioBundle\Entity\MovimientoMaterial", cascade={"all"})
     * @ORM\JoinColumn(name="id_movimiento_padre", referencedColumnName="id", nullable=true)
     */
    protected $movimientoPadre;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoMovimiento")
     * @ORM\JoinColumn(name="id_estado_movimiento", referencedColumnName="id")
     */
    private $estadoMovimiento;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="AprobacionMovimiento")
     * @ORM\JoinColumn(name="id_aprobacion_movimiento", referencedColumnName="id")
     */
    private $aprobacionMovimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_movimiento", type="datetime")
     */
    private $fechaMovimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_carga", type="datetime")
     */
    private $fechaCarga;

    /**
     * @var string
     *
     * @ORM\Column(name="cuenta_mayor", type="string", length=100)
     */
    private $cuentaMayor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="movimiento_observado", type="boolean")
     */
    private $movimientoObservado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cross_docking", type="boolean")
     */
    private $crossDocking;

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
     * Set Id Tipo Movimiento Material
     *
     * @param \ADIF\InventarioBundle\Entity\TiposMovimientoMaterial $tipoMovimiento
     * @return MovimientoMaterial
     */
    public function setTipoMovimiento(\ADIF\InventarioBundle\Entity\TiposMovimientoMaterial $id)
    {
        $this->tipoMovimiento = $id;
        return $this;
    }

    /**
     * Set Numero Acta Expediente
     *
     * @param string $numeroActaExpediente
     * @return MovimientoMaterial
     */
    public function setNumeroActaExpediente($num_acta)
    {
        $this->numeroActaExpediente = $num_acta;
        return $this;
    }

    /**
     * Get Numero Acta Expediente
     *
     * @return string
     */
    public function getNumeroActaExpediente()
    {
        return $this->numeroActaExpediente;
    }

    /**
     * Set Numero Subasta
     *
     * @param string $numeroSubasta
     * @return MovimientoMaterial
     */
    public function setNumeroSubasta($num_subasta)
    {
        $this->numeroSubasta = $num_subasta;
        return $this;
    }

    /**
     * Get Numero Subasta
     *
     * @return string
     */
    public function getNumeroSubasta()
    {
        return $this->numeroSubasta;
    }

    /**
     * Get Id Tipo Movimiento Material
     *
     * @return \ADIF\InventarioBundle\Entity\TiposMovimientoMaterial
     */
    public function getTipoMovimiento()
    {
        return $this->tipoMovimiento;
    }

    /**
     * Set Id Estado Movimiento
     *
     * @param \ADIF\InventarioBundle\Entity\EstadoMovimiento $estadoMovimiento
     * @return MovimientoMaterial
     */
    public function setEstadoMovimiento(\ADIF\InventarioBundle\Entity\EstadoMovimiento $id)
    {
        $this->estadoMovimiento = $id;
        return $this;
    }

    /**
     * Get Id Estado Movimiento
     *
     * @return \ADIF\InventarioBundle\Entity\EstadoMovimiento
     */
    public function getEstadoMovimiento()
    {
        return $this->estadoMovimiento;
    }

    /**
     * Set Despachante
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $despachante
     */
    public function setDespachante($despachante)
    {
        if (null != $despachante) {
            $this->idDespachante = $despachante->getId();
        } else {
            $this->idDespachante = null;
        }

        $this->despachante = $despachante;
    }

    /**
     * Get Despachante
     *
     * @return integer
     */
    public function getDespachante()
    {
        return $this->despachante;
    }

    /**
     * Set Movimiento Padre
     *
     * @param \ADIF\InventarioBundle\Entity\MovimientoMaterial $movimientoPadre
     * @return MovimientoMaterial
     */
    public function setMovimientoPadre(\ADIF\InventarioBundle\Entity\MovimientoMaterial $movimiento = null) {
        $this->movimientoPadre = $movimiento;
        return $this;
    }

    /**
     * Get Movimiento Padre
     *
     * @return \ADIF\InventarioBundle\Entity\MovimientoMaterial
     */
    public function getMovimientoPadre() {
        return $this->movimientoPadre;
    }

    /**
     * Set Id Aprobacion Movimiento
     *
     * @param \ADIF\InventarioBundle\Entity\AprobacionMovimiento $aprobacionMovimiento
     * @return MovimientoMaterial
     */
    public function setAprobacionMovimiento(\ADIF\InventarioBundle\Entity\AprobacionMovimiento $id)
    {
        $this->aprobacionMovimiento = $id;
        return $this;
    }

    /**
     * Get Id Aprobacion Movimiento
     *
     * @return \ADIF\InventarioBundle\Entity\AprobacionMovimiento
     */
    public function getAprobacionMovimiento()
    {
        return $this->aprobacionMovimiento;
    }

    /**
     * Set fecha Movimiento
     *
     * @param \DateTime $fechaMovimiento
     * @return MovimientoMaterial
     */
    public function setFechaMovimiento($fecha)
    {
        $this->fechaMovimiento = $fecha;

        return $this;
    }

    /**
     * Get fecha Movimiento
     *
     * @return \DateTime
     */
    public function getFechaMovimiento()
    {
        return $this->fechaMovimiento;
    }

    /**
     * Set fecha Carga
     *
     * @param \DateTime $fechaCarga
     * @return MovimientoMaterial
     */
    public function setFechaCarga($fecha)
    {
        $this->fechaCarga = $fecha;

        return $this;
    }

    /**
     * Get fecha Carga
     *
     * @return \DateTime
     */
    public function getFechaCarga()
    {
        return $this->fechaCarga;
    }

    /**
     * Set cuentaMayor
     *
     * @param string $cuentaMayor
     * @return MovimientoMaterial
     */
    public function setCuentaMayor($cuentaMayor)
    {
        $this->cuentaMayor = $cuentaMayor;

        return $this;
    }

    /**
     * Get cuentaMayor
     *
     * @return string
     */
    public function getCuentaMayor()
    {
        return $this->cuentaMayor;
    }

    /**
     * Set Movimiento Observado
     *
     * @param boolean $movimientoObservado
     * @return MovimientoMaterial
     */
    public function setMovimientoObservado($observado)
    {
        $this->movimientoObservado = $observado;

        return $this;
    }

    /**
     * Get Movimiento Observado
     *
     * @return boolean
     */
    public function getMovimientoObservado()
    {
        return $this->movimientoObservado;
    }

    /**
     * Set Cross Docking
     *
     * @param boolean $crossDocking
     * @return MovimientoMaterial
     */
    public function setCrossDocking($cross)
    {
        $this->crossDocking = $cross;
        return $this;
    }

    /**
     * Get Cross Docking
     *
     * @return boolean
     */
    public function getCrossDocking()
    {
        return $this->crossDocking;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return MovimientoMaterial
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
}
