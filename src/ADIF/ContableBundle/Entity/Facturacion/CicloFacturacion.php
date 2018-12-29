<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CicloFacturacion 
 * 
 * Indica el ciclo de facturación
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="ciclo_facturacion")
 * @ORM\Entity
 */
class CicloFacturacion extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Contrato
     *
     * @ORM\ManyToOne(targetEntity="Contrato", inversedBy="ciclosFacturacion")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=false)
     */
    protected $contrato;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="datetime", nullable=false)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="datetime", nullable=false)
     */
    protected $fechaFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_unidad_tiempo", type="integer", nullable=false)
     */
    protected $cantidadUnidadTiempo;

    /**
     * @var UnidadTiempo
     *
     * @ORM\ManyToOne(targetEntity="UnidadTiempo")
     * @ORM\JoinColumn(name="id_unidad_tiempo", referencedColumnName="id", nullable=false)
     */
    protected $unidadTiempo;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $importe;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_facturas", type="integer", nullable=false)
     */
    protected $cantidadFacturas;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_facturas_pendientes", type="integer", nullable=false)
     */
    protected $cantidadFacturasPendientes;

    /**
     * Constructor
     */
    public function __construct() {

        $this->cantidadUnidadTiempo = 1;

        $this->cantidadFacturas = 0;
        $this->cantidadFacturasPendientes = 0;
    }

    /**
     * 
     * @param type $id
     * @return \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion
     */
    public function setId($id = null) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\Contrato $contrato
     * @return CicloFacturacion
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Facturacion\Contrato $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\Contrato 
     */
    public function getContrato() {
        return $this->contrato;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return CicloFacturacion
     */
    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return CicloFacturacion
     */
    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin() {
        return $this->fechaFin;
    }

    /**
     * Set cantidadUnidadTiempo
     *
     * @param integer $cantidadUnidadTiempo
     * @return CicloFacturacion
     */
    public function setCantidadUnidadTiempo($cantidadUnidadTiempo) {
        $this->cantidadUnidadTiempo = $cantidadUnidadTiempo;

        return $this;
    }

    /**
     * Get cantidadUnidadTiempo
     *
     * @return integer 
     */
    public function getCantidadUnidadTiempo() {
        return $this->cantidadUnidadTiempo;
    }

    /**
     * Set unidadTiempo
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\UnidadTiempo $unidadTiempo
     * @return CicloFacturacion
     */
    public function setUnidadTiempo(\ADIF\ContableBundle\Entity\Facturacion\UnidadTiempo $unidadTiempo) {
        $this->unidadTiempo = $unidadTiempo;

        return $this;
    }

    /**
     * Get unidadTiempo
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\UnidadTiempo 
     */
    public function getUnidadTiempo() {
        return $this->unidadTiempo;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return CicloFacturacion
     */
    public function setImporte($importe) {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string 
     */
    public function getImporte() {
        return $this->importe;
    }

    /**
     * Set cantidadFacturas
     *
     * @param integer $cantidadFacturas
     * @return CicloFacturacion
     */
    public function setCantidadFacturas($cantidadFacturas) {
        $this->cantidadFacturas = $cantidadFacturas;

        return $this;
    }

    /**
     * Get cantidadFacturas
     *
     * @return integer 
     */
    public function getCantidadFacturas() {
        return $this->cantidadFacturas;
    }

    /**
     * Set cantidadFacturasPendientes
     *
     * @param integer $cantidadFacturasPendientes
     * @return CicloFacturacion
     */
    public function setCantidadFacturasPendientes($cantidadFacturasPendientes) {
        $this->cantidadFacturasPendientes = $cantidadFacturasPendientes;

        return $this;
    }

    /**
     * Get cantidadFacturasPendientes
     *
     * @return integer 
     */
    public function getCantidadFacturasPendientes() {
        return $this->cantidadFacturasPendientes;
    }

    /**
     * Get cantidadFacturasEmitidas
     *
     * @return integer 
     */
    public function getCantidadFacturasEmitidas() {
        return $this->cantidadFacturas - $this->cantidadFacturasPendientes;
    }

}
