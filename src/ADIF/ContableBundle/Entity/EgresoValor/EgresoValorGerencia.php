<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;

/**
 * EgresoValorGerencia
 * 
 * @ORM\Table(name="egreso_valor_gerencia")
 * @ORM\Entity
 */
class EgresoValorGerencia extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $monto;

    /**
     * @ORM\Column(name="id_gerencia", type="integer", nullable=false)
     */
    protected $idGerencia;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Gerencia
     */
    protected $gerencia;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoEgresoValor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoEgresoValor;
    
    /**
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=false)
     */
    protected $cuentaContable;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return EgresoValorGerencia
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
     * Set idGerencia
     *
     * @param integer $idGerencia
     * @return EgresoValorGerencia
     */
    public function setIdGerencia($idGerencia) {
        $this->idGerencia = $idGerencia;

        return $this;
    }

    /**
     * Get idGerencia
     *
     * @return integer 
     */
    public function getIdGerencia() {
        return $this->idGerencia;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Gerencia $gerencia
     */
    public function setGerencia($gerencia) {

        if (null != $gerencia) {
            $this->idGerencia = $gerencia->getId();
        } else {
            $this->idGerencia = null;
        }

        $this->gerencia = $gerencia;
    }

    /**
     * 
     * @return type
     */
    public function getGerencia() {
        return $this->gerencia;
    }

    /**
     * Set tipoEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor $tipoEgresoValor
     * @return EgresoValorGerencia
     */
    public function setTipoEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor $tipoEgresoValor) {
        $this->tipoEgresoValor = $tipoEgresoValor;

        return $this;
    }

    /**
     * Get tipoEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor 
     */
    public function getTipoEgresoValor() {
        return $this->tipoEgresoValor;
    }
    
    /**
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return EgresoValorGerencia
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
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

}
