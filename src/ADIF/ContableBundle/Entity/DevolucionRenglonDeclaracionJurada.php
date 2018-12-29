<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of DevolucionRenglonDeclaracionJurada
 *
 * @author Darío Rapetti
 * created 13/11/2015
 * 
 * @ORM\Table(name="devolucion_renglon_declaracion_jurada")
 * @ORM\Entity
 */
class DevolucionRenglonDeclaracionJurada extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoDevolucionRenglonDeclaracionJurada", mappedBy="devolucionRenglonDeclaracionJurada")
     * */
    protected $ordenPago;

    /**
     * @var \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada
     *
     * @ORM\ManyToOne(targetEntity="RenglonDeclaracionJurada", inversedBy="devoluciones")
     * @ORM\JoinColumn(name="id_renglon_declaracion_jurada", referencedColumnName="id")
     * 
     */
    protected $renglonDeclaracionJurada;

    /**
     * @var string
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     * 
     */
    private $monto;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set ordenPago
     *
     * @param OrdenPagoDevolucionRenglonDeclaracionJurada $ordenPago
     * @return PagoACuenta
     */
    public function setOrdenPago(OrdenPagoDevolucionRenglonDeclaracionJurada $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return OrdenPagoDevolucionRenglonDeclaracionJurada
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set renglonDeclaracionJurada
     *
     * @param \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJurada
     * @return DevolucionRenglonDeclaracionJurada
     */
    public function setRenglonDeclaracionJurada(\ADIF\ContableBundle\Entity\RenglonDeclaracionJurada $renglonDeclaracionJurada = null) {
        $this->renglonDeclaracionJurada = $renglonDeclaracionJurada;

        return $this;
    }

    /**
     * Get renglonDeclaracionJurada
     *
     * @return \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada 
     */
    public function getRenglonDeclaracionJurada() {
        return $this->renglonDeclaracionJurada;
    }
    
    /**
     * Set monto
     *
     * @param string $monto
     * @return DevolucionRenglonDeclaracionJurada
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return DevolucionRenglonDeclaracionJurada
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha() {
        return $this->fecha;
    }

}
