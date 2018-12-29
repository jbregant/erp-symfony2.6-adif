<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author Manuel Becerra
 * created 30/07/2015
 * 
 * @ORM\Table(name="devolucion_garantia")
 * @ORM\Entity
 */
class DevolucionGarantia extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_devolucion", type="date", nullable=false)
     */
    protected $fechaDevolucion;

    /**
     * @var CategoriaContrato
     *
     * @ORM\ManyToOne(targetEntity="CuponVenta", inversedBy="devolucionesGarantia")
     * @ORM\JoinColumn(name="id_cupon_garantia", referencedColumnName="id", nullable=false)
     */
    protected $cuponGarantia;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $importe;

    /**
     *
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="OrdenPagoDevolucionGarantia", mappedBy="devolucionGarantia")
     */
    protected $ordenesPago;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ordenesPago = new ArrayCollection();
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
     * Set fechaDevolucion
     *
     * @param \DateTime $fechaDevolucion
     * @return DevolucionGarantia
     */
    public function setFechaDevolucion($fechaDevolucion) {
        $this->fechaDevolucion = $fechaDevolucion;

        return $this;
    }

    /**
     * Get fechaDevolucion
     *
     * @return \DateTime 
     */
    public function getFechaDevolucion() {
        return $this->fechaDevolucion;
    }

    /**
     * Set cuponGarantia
     *
     * @param CuponVenta $cuponGarantia
     * @return DevolucionGarantia
     */
    public function setCuponGarantia(CuponVenta $cuponGarantia) {
        $this->cuponGarantia = $cuponGarantia;

        return $this;
    }

    /**
     * Get cuponGarantia
     *
     * @return CuponVenta 
     */
    public function getCuponGarantia() {
        return $this->cuponGarantia;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return DevolucionGarantia
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
     * Add ordenesPago
     *
     * @param OrdenPagoDevolucionGarantia $ordenesPago
     * @return DevolucionGarantia
     */
    public function addOrdenesPago(OrdenPagoDevolucionGarantia $ordenesPago) {
        $this->ordenesPago[] = $ordenesPago;

        return $this;
    }

    /**
     * Remove ordenesPago
     *
     * @param OrdenPagoDevolucionGarantia $ordenesPago
     */
    public function removeOrdenesPago(OrdenPagoDevolucionGarantia $ordenesPago) {
        $this->ordenesPago->removeElement($ordenesPago);
    }

    /**
     * Get ordenesPago
     *
     * @return ArrayCollection
     */
    public function getOrdenesPago() {
        return $this->ordenesPago;
    }

    /**
     * Get estaCreada
     * 
     * @return boolean
     */
    public function getEsEditable() {

        $esEditable = true;

        if (!$this->ordenesPago->isEmpty()) {

            /* @var $ordenPago OrdenPagoDevolucionGarantia */
            foreach ($this->ordenesPago as $ordenPago) {

                if (null != $ordenPago->getEstadoOrdenPago()) {

                    if ($ordenPago->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA && $ordenPago->getNumeroOrdenPago() == null) {
                        $esEditable = false;

                        break;
                    }
                }
            }
        }

        return $esEditable;
    }

}
