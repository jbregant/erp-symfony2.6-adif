<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of Cheque
 *
 * @author Manuel Becerra
 * created 05/11/2014
 * 
 * @ORM\Table(name="cheque")
 * @ORM\Entity
 */
class Cheque extends ConciliacionBancaria\MovimientoConciliable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Chequera
     *
     * @ORM\ManyToOne(targetEntity="Chequera", inversedBy="cheques")
     * @ORM\JoinColumn(name="id_chequera", referencedColumnName="id", nullable=false)
     * 
     */
    protected $chequera;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=50, unique=true, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de cheque no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroCheque;

    /**
     * @var EstadoPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoPago")
     * @ORM\JoinColumn(name="id_estado_pago", referencedColumnName="id")
     * 
     */
    protected $estadoPago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ultima_modificacion_estado", type="date", nullable=false)
     */
    protected $fechaUltimaModificacionEstado;

    /**
     * @var PagoOrdenPago
     *
     * @ORM\ManyToOne(targetEntity="PagoOrdenPago", inversedBy="cheques")
     * @ORM\JoinColumn(name="id_pago_orden_pago", referencedColumnName="id", nullable=true) 
     */
    protected $pagoOrdenPago;

    /**
     *
     * @var EstadoPagoHistorico
     * 
     * @ORM\OneToMany(targetEntity="EstadoPagoHistorico", mappedBy="cheque", cascade={"all"})
     * @ORM\OrderBy({"fecha" = "DESC"})
     */
    protected $historicoEstados;
    
    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $monto;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();

        $this->fechaUltimaModificacionEstado = new \DateTime();
        $this->historicoEstados = new ArrayCollection();
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
     * Set numeroCheque
     *
     * @param string $numeroCheque
     * @return Cheque
     */
    public function setNumeroCheque($numeroCheque) {
        $this->numeroCheque = $numeroCheque;

        return $this;
    }

    /**
     * Get numeroCheque
     *
     * @return string 
     */
    public function getNumeroCheque() {
        return $this->numeroCheque;
    }

    /**
     * Set estadoPago
     *
     * @param EstadoPago $estadoPago
     * @return Cheque
     */
    public function setEstadoPago(EstadoPago $estadoPago = null) {
        $this->estadoPago = $estadoPago;

        return $this;
    }

    /**
     * Get estadoPago
     *
     * @return EstadoPago
     */
    public function getEstadoPago() {
        return $this->estadoPago;
    }

    /**
     * Set chequera
     *
     * @param Chequera $chequera
     * @return Cheque
     */
    public function setChequera(Chequera $chequera) {
        $this->chequera = $chequera;

        return $this;
    }

    /**
     * Get chequera
     *
     * @return Chequera 
     */
    public function getChequera() {
        return $this->chequera;
    }

    /**
     * Set pagoOrdenPago
     *
     * @param \ADIF\ContableBundle\Entity\pagoOrdenPago $pagoOrdenPago
     * @return Cheque
     */
    public function setPagoOrdenPago(PagoOrdenPago $pagoOrdenPago = null) {
        $this->pagoOrdenPago = $pagoOrdenPago;

        return $this;
    }

    /**
     * Get pagoOrdenPago
     *
     * @return PagoOrdenPago
     */
    public function getPagoOrdenPago() {
        return $this->pagoOrdenPago;
    }

    /**
     * Set fechaUltimaModificacionEstado
     *
     * @param \DateTime $fechaUltimaModificacionEstado
     * @return Cheque
     */
    public function setFechaUltimaModificacionEstado($fechaUltimaModificacionEstado) {
        $this->fechaUltimaModificacionEstado = $fechaUltimaModificacionEstado;

        return $this;
    }

    /**
     * Get fechaUltimaModificacionEstado
     *
     * @return \DateTime 
     */
    public function getFechaUltimaModificacionEstado() {
        return $this->fechaUltimaModificacionEstado;
    }

    /**
     * Add historicoEstados
     *
     * @param \ADIF\ContableBundle\Entity\EstadoPagoHistorico $chequeHistorico
     * @return Cheque
     */
    public function addHistoricoEstado(\ADIF\ContableBundle\Entity\EstadoPagoHistorico $chequeHistorico) {
        $this->historicoEstados[] = $chequeHistorico;

        return $this;
    }

    /**
     * Remove historicoEstados
     *
     * @param \ADIF\ContableBundle\Entity\EstadoPagoHistorico $chequeHistorico
     */
    public function removeHistoricoEstado(\ADIF\ContableBundle\Entity\EstadoPagoHistorico $chequeHistorico) {
        $this->historicoEstados->removeElement($chequeHistorico);
    }

    /**
     * Get historicoEstados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistoricoEstados() {
        return $this->historicoEstados;
    }

    /**
     * 
     * @param type $cuentaBancaria
     * @param type $fecha_inicio
     * @param type $fecha_fin
     * @return type
     */
    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {

        $chequera = $this->getChequera();

        return
                $chequera->getIdCuenta() == $cuentaBancaria->getId() &&
                ($this->getEstadoPago() && $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_PAGO_ANULADO) &&
                ($this->getPagoOrdenPago()->getOrdenPagoPagada() != null) &&
                ($fecha_inicio ? $this->getFechaParaMayor() >= $fecha_inicio : true) &&
                ($fecha_fin ? $this->getFechaParaMayor() <= $fecha_fin : true);
    }

    /**
     * 
     * @return type
     */
    public function getConcepto() {
        return 'Cheque N&ordm; ' . $this->getNumeroCheque();
    }

    /**
     * 
     * @return type
     */
    public function getReferencia() {
        return $this->getNumeroCheque();
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {
        return 'Cheque';
    }

    /**
     * 
     * @return type
     */
    public function getFecha() {
        return $this->getPagoOrdenPago()->getFechaPago();
    }

    /**
     * 
     * @param type $cuentaBancaria
     * @return type
     */
    public function getMontoMovimiento($cuentaBancaria = null) {

        return ($this->getPagoOrdenPago()->getOrdenPagoPagada() != null) //
                ? $this->getMonto() //
                : 0;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsContabilizable() {
        return false;
    }

    /**
     * 
     * @return int
     */
    public function getCodigoConcepto() {
        return 1;
    }

    /**
     * 
     * @return type
     */
    public function getFechaParaMayor() {
        return $this->getPagoOrdenPago()->getOrdenPagoPagada()->getFechaContable();
    }

    /**
     * 
     * @return boolean
     */
    public function getEsEditable() {

        return $this->getEstadoPago() != null //
                && $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_PAGO_ANULADO //
                && $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_REEMPLAZADO;
    }
    
    /**
     * Set monto
     *
     * @param string $monto
     * @return Cheque
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return double 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * 
     * @return type
     */
        public function getEstaAnulado() {
        return $this->getEstadoPago()->getDenominacionEstado() != ConstanteEstadoPago::ESTADO_PAGO_ANULADO;
    }
    
    /**
     * 
     * @return type
     */
    public function getEsAnulable() {
        return (($this->getPagoOrdenPago() == null)//
        || ($this->getPagoOrdenPago() != null) && ($this->getPagoOrdenPago()->getOrdenPagoPagada() == null));
    }

}
