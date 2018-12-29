<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of RenglonModeloAsientoContable
 *
 * @author Manuel Becerra
 * created 02/10/2014
 * 
 * @ORM\Table(name="renglon_modelo_asiento_contable")
 * @ORM\Entity
 */
class RenglonModeloAsientoContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\ModeloAsientoContable
     *
     * @ORM\ManyToOne(targetEntity="ModeloAsientoContable", inversedBy="renglonesModeloAsientoContable")
     * @ORM\JoinColumn(name="id_modelo_asiento_contable", referencedColumnName="id")
     * 
     */
    protected $modeloAsientoContable;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaContable
     *
     * @ORM\ManyToOne(targetEntity="CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id")
     * 
     */
    protected $cuentaContable;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoOperacionContable
     *
     * @ORM\ManyToOne(targetEntity="TipoOperacionContable")
     * @ORM\JoinColumn(name="id_tipo_operacion_contable", referencedColumnName="id")
     * 
     */
    protected $tipoOperacionContable;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoMoneda
     *
     * @ORM\ManyToOne(targetEntity="TipoMoneda")
     * @ORM\JoinColumn(name="id_tipo_moneda", referencedColumnName="id")
     * 
     */
    protected $tipoMoneda;

    /**
     * @var float
     * 
     * @ORM\Column(name="importe_mo", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe de moneda origen debe ser de tipo numérico.")
     */
    protected $importeMO;

    /**
     * @var float
     * 
     * @ORM\Column(name="importe_mcl", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El importe de moneda en curso legal debe ser de tipo numérico.")
     */
    protected $importeMCL;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=1024, nullable=true)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="El detalle no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $detalle;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set importeMO
     *
     * @param float $importeMO
     * @return RenglonAsientoContable
     */
    public function setImporteMO($importeMO) {
        $this->importeMO = $importeMO;

        return $this;
    }

    /**
     * Get importeMO
     *
     * @return float 
     */
    public function getImporteMO() {
        return $this->importeMO;
    }

    /**
     * Set importeMCL
     *
     * @param float $importeMCL
     * @return RenglonAsientoContable
     */
    public function setImporteMCL($importeMCL) {
        $this->importeMCL = $importeMCL;

        return $this;
    }

    /**
     * Get importeMCL
     *
     * @return float 
     */
    public function getImporteMCL() {
        return $this->importeMCL;
    }

    /**
     * Set detalle
     *
     * @param string $detalle
     * @return RenglonAsientoContable
     */
    public function setDetalle($detalle) {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle() {
        return $this->detalle;
    }

    /**
     * Set modeloAsientoContable
     *
     * @param \ADIF\ContableBundle\Entity\ModeloAsientoContable $modeloAsientoContable
     * @return RenglonModeloAsientoContable
     */
    public function setModeloAsientoContable(\ADIF\ContableBundle\Entity\ModeloAsientoContable $modeloAsientoContable = null) {
        $this->modeloAsientoContable = $modeloAsientoContable;

        return $this;
    }

    /**
     * Get modeloAsientoContable
     *
     * @return \ADIF\ContableBundle\Entity\ModeloAsientoContable 
     */
    public function getModeloAsientoContable() {
        return $this->modeloAsientoContable;
    }

    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return RenglonAsientoContable
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable = null) {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set tipoOperacionContable
     *
     * @param \ADIF\ContableBundle\Entity\TipoOperacionContable $tipoOperacionContable
     * @return RenglonAsientoContable
     */
    public function setTipoOperacionContable(\ADIF\ContableBundle\Entity\TipoOperacionContable $tipoOperacionContable = null) {
        $this->tipoOperacionContable = $tipoOperacionContable;

        return $this;
    }

    /**
     * Get tipoOperacionContable
     *
     * @return \ADIF\ContableBundle\Entity\TipoOperacionContable 
     */
    public function getTipoOperacionContable() {
        return $this->tipoOperacionContable;
    }

    /**
     * Set tipoMoneda
     *
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     * @return RenglonAsientoContable
     */
    public function setTipoMoneda(\ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda = null) {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return \ADIF\ContableBundle\Entity\TipoMoneda 
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }
    
    public function copiarRenglonAsiento(RenglonAsientoContable $renglonAsientoContable){
        $this->setCuentaContable($renglonAsientoContable->getCuentaContable());
        $this->setDetalle($renglonAsientoContable->getDetalle());
        $this->setImporteMCL(0);
        $this->setImporteMO(0);
        $this->setTipoMoneda($renglonAsientoContable->getTipoMoneda());
        $this->setTipoOperacionContable($renglonAsientoContable->getTipoOperacionContable());        
    }

}
