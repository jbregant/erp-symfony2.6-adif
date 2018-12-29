<?php

//namespace ADIF\ContableBundle\Entity;
//
//use ADIF\AutenticacionBundle\Entity\BaseAuditable;
//use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * Description of RenglonIvaCompras
// * 
// * @ORM\Table(name="renglon_iva_compras")
// * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\RenglonIvaComprasRepository")
// */
//class RenglonIvaCompras extends BaseAuditoria implements BaseAuditable {
//
//    /**
//     * @var integer
//     *
//     * @ORM\Column(name="id", type="integer")
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="IDENTITY")
//     */
//    private $id;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion")
//     * @ORM\JoinColumn(name="conciliacion_id", referencedColumnName="id")
//     * 
//     */
//    protected $conciliacion;
//
//    /**
//     * @var boolean
//     *
//     * @ORM\Column(name="es_suma", type="boolean", nullable=false)
//     */
//    protected $esSuma;
//
//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="fecha", type="datetime", nullable=false)
//     */
//    protected $fecha;
//
//    /**
//     * @var double
//     * @ORM\Column(name="otros_impuestos", type="decimal", precision=10, scale=2, nullable=false)
//     * 
//     */
//    protected $otrosImpuestos;
//
//    /**
//     * @var double
//     * @ORM\Column(name="neto_21", type="decimal", precision=10, scale=2, nullable=false)
//     * 
//     */
//    protected $neto21;
//
//    /**
//     * @var double
//     * @ORM\Column(name="iva_21", type="decimal", precision=10, scale=2, nullable=false)
//     * 
//     */
//    protected $iva21;
//
//    /**
//     * @var double
//     * @ORM\Column(name="gastos_exentos", type="decimal", precision=10, scale=2, nullable=false)
//     * 
//     */
//    protected $gastosExentos;
//
//    /**
//     * @var double
//     * @ORM\Column(name="iibb_901", type="decimal", precision=10, scale=2, nullable=false)
//     * 
//     */
//    protected $IIBB901;
//
//    /**
//     * @var double
//     * @ORM\Column(name="iibb_902", type="decimal", precision=10, scale=2, nullable=false)
//     * 
//     */
//    protected $IIBB902;
//
//    /**
//     * @var double
//     */
//    protected $gastosBancarios;
//
//    /**
//     * Constructor
//     */
//    public function __construct() {
//        //$this->fecha = new \DateTime();
//        $this->esSuma = true;
//        $this->IIBB901 = 0;
//        $this->IIBB902 = 0;
//        $this->iva21 = 0;
//        $this->otrosImpuestos = 0;
//        $this->gastosExentos = 0;
//        $this->neto21 = 0;
//        $this->gastosBancarios = 0;
//    }
//
//    /**
//     * Get id
//     *
//     * @return integer 
//     */
//    public function getId() {
//        return $this->id;
//    }
//
//    /**
//     * Set esSuma
//     *
//     * @param boolean $esSuma
//     * @return RenglonIvaCompras
//     */
//    public function setEsSuma($esSuma) {
//        $this->esSuma = $esSuma;
//
//        return $this;
//    }
//
//    /**
//     * Get esSuma
//     *
//     * @return boolean 
//     */
//    public function getEsSuma() {
//        return $this->esSuma;
//    }
//
//    /**
//     * Set fecha
//     *
//     * @param \DateTime $fecha
//     * @return RenglonIvaCompras
//     */
//    public function setFecha($fecha) {
//        $this->fecha = $fecha;
//
//        return $this;
//    }
//
//    /**
//     * Get fecha
//     *
//     * @return \DateTime 
//     */
//    public function getFecha() {
//        return $this->fecha;
//    }
//
//    /**
//     * Set otrosImpuestos
//     *
//     * @param string $otrosImpuestos
//     * @return RenglonIvaCompras
//     */
//    public function setOtrosImpuestos($otrosImpuestos) {
//        $this->otrosImpuestos = $otrosImpuestos;
//
//        return $this;
//    }
//
//    /**
//     * Get otrosImpuestos
//     *
//     * @return string 
//     */
//    public function getOtrosImpuestos() {
//        return $this->otrosImpuestos;
//    }
//
//    /**
//     * Set neto21
//     *
//     * @param string $neto21
//     * @return RenglonIvaCompras
//     */
//    public function setNeto21($neto21) {
//        $this->neto21 = $neto21;
//
//        return $this;
//    }
//
//    /**
//     * Get neto21
//     *
//     * @return string 
//     */
//    public function getNeto21() {
//        return $this->neto21;
//    }
//
//    /**
//     * Set iva21
//     *
//     * @param string $iva21
//     * @return RenglonIvaCompras
//     */
//    public function setIva21($iva21) {
//        $this->iva21 = $iva21;
//
//        return $this;
//    }
//
//    /**
//     * Get iva21
//     *
//     * @return string 
//     */
//    public function getIva21() {
//        return $this->iva21;
//    }
//
//    /**
//     * Set gastosExentos
//     *
//     * @param string $gastosExentos
//     * @return RenglonIvaCompras
//     */
//    public function setGastosExentos($gastosExentos) {
//        $this->gastosExentos = $gastosExentos;
//
//        return $this;
//    }
//
//    /**
//     * Get gastosExentos
//     *
//     * @return string 
//     */
//    public function getGastosExentos() {
//        return $this->gastosExentos;
//    }
//
//    /**
//     * Set IIBB901
//     *
//     * @param string $iIBB901
//     * @return RenglonIvaCompras
//     */
//    public function setIIBB901($iIBB901) {
//        $this->IIBB901 = $iIBB901;
//
//        return $this;
//    }
//
//    /**
//     * Get IIBB901
//     *
//     * @return string 
//     */
//    public function getIIBB901() {
//        return $this->IIBB901;
//    }
//
//    /**
//     * Set IIBB902
//     *
//     * @param string $iIBB902
//     * @return RenglonIvaCompras
//     */
//    public function setIIBB902($iIBB902) {
//        $this->IIBB902 = $iIBB902;
//
//        return $this;
//    }
//
//    /**
//     * Get IIBB902
//     *
//     * @return string 
//     */
//    public function getIIBB902() {
//        return $this->IIBB902;
//    }
//
//    /**
//     * Set conciliacion
//     *
//     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliacion
//     * @return RenglonIvaCompras
//     */
//    public function setConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion $conciliacion = null) {
//        $this->conciliacion = $conciliacion;
//
//        return $this;
//    }
//
//    /**
//     * Get conciliacion
//     *
//     * @return \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion 
//     */
//    public function getConciliacion() {
//        return $this->conciliacion;
//    }
//
//    /**
//     * Set gastosBancarios
//     *
//     * @param string $gastosBancarios
//     * @return RenglonIvaCompras
//     */
//    public function setGastosBancarios($gastosBancarios) {
//        $this->gastosBancarios = $gastosBancarios;
//
//        return $this;
//    }
//
//    /**
//     * Get gastosBancarios
//     *
//     * @return string 
//     */
//    public function getGastosBancarios() {
//        return $this->gastosBancarios;
//    }
//
//    /**
//     * Prorrateo los gastos bancarios
//     * @return RenglonIvaCompras
//     */
//    public function setProrrateo() {
//        if ($this->gastosBancarios > 0) {
//            $this->neto21 = $this->getIva21() / 0.21;
//            $this->gastosExentos += $this->gastosBancarios - $this->neto21;
//        }
//    }
//
//    /**
//     * Pasa todos los montos a negativo
//     * @return RenglonIvaCompras
//     */
//    public function setResta() {
//        $this->IIBB901 *= -1;
//        $this->IIBB902 *= -1;
//        $this->iva21 *= -1;
//        $this->otrosImpuestos *= -1;
//        $this->gastosExentos *= -1;
//        $this->neto21 *= -1;
//    }
//
//    public function getTotal() {
//        return $this->IIBB901 + $this->IIBB902 + $this->iva21 + $this->otrosImpuestos + $this->gastosExentos + $this->neto21;
//    }
//
//}
