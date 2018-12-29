<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfiguracionCuentaContableSueldos
 *
 * @ORM\Table(name="configuracion_cuenta_contable_sueldos")
 * @ORM\Entity
 */
class ConfiguracionCuentaContableSueldos extends BaseAuditoria {
    
    const __CODIGO_PASIVO_SUELDOS = 'PASIVO_SUELDOS';  
    const __CODIGO_PASIVO_CARGAS = 'PASIVO_CARGAS';  
    const __CODIGO_PASIVO_ART = 'PASIVO_ART';  
    const __CODIGO_BASICO = 'BASICO';  
    const __CODIGO_PASIVO_SUELDOS_SAC = 'PASIVO_SUELDOS_SAC';  
    const __CODIGO_ANTICIPO_SUELDOS = 'ANTICIPO_SUELDOS';  
    const __CODIGO_ANTICIPO_PROVEEDORES = 'ANTICIPO_PROVEEDORES';  
    const __CODIGO_ANTICIPO_CLIENTES = 'ANTICIPO_CLIENTES';  
    const __CODIGO_NOTA_DEBITO_INTERESES = 'NOTA_DEBITO_INTERESES';  
    const __CODIGO_IVA_RETENCIONES = 'IVA_RETENCIONES';  
    const __CODIGO_IVA_PERCEPCIONES = 'IVA_PERCEPCIONES';  
    const __CODIGO_IVA_SALDO_LIBRE_DISPONIBLE = 'IVA_SALDO_LIBRE_DISPONIBLE';  
    const __CODIGO_IVA_SALDO_TECNICO = 'IVA_SALDO_TECNICO';  
    const __CODIGO_IVA_SALDO_A_PAGAR = 'IVA_SALDO_A_PAGAR';  
    const __CODIGO_IVA_NO_COMPUTABLE = 'IVA_NO_COMPUTABLE';  

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var ADIF\ContableBundle\Entity\CuentaContable
     */
    protected $cuentaContable;
    

    /**
     * To String
     * 
     * @return string 
     */
    public function __toString() {
        return $this->getDescripcion();
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
     * Set codigo
     *
     * @param string $codigo
     * @return ConfiguracionCuentaContableSueldos
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo() {
        return $this->codigo;
    }
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return ConfiguracionCuentaContableSueldos
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set idCuentaContable
     *
     * @param integer $idCuentaContable
     * @return ConfiguracionCuentaContableSueldos
     */
    public function setIdCuentaContable($idCuentaContable) {
        $this->idCuentaContable = $idCuentaContable;

        return $this;
    }

    /**
     * Get idCuentaContable
     *
     * @return integer 
     */
    public function getIdCuentaContable() {
        return $this->idCuentaContable;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function setCuentaContable($cuentaContable) {
        if (null != $cuentaContable) {
            $this->idCuentaContable = $cuentaContable->getId();
        } else {
            $this->idCuentaContable = null;
        }

        $this->cuentaContable = $cuentaContable;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

}
