<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Presupuesto
 * 
 * @ORM\Table(name="presupuesto")
 * @ORM\Entity
 * @UniqueEntity(fields={"ejercicioContable"}, message="Sólo puede haber un presupuesto por ejercicio contable")
 */
class Presupuesto extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="CuentaPresupuestaria", mappedBy="presupuesto", cascade="all")
     */
    protected $cuentasPresupuestarias;

    /**
     * @var \ADIF\ContableBundle\Entity\EjercicioContable
     *
     * 
     * @ORM\OneToOne(targetEntity="EjercicioContable", inversedBy="presupuesto")
     * @ORM\JoinColumn(name="id_ejercicio_contable", referencedColumnName="id")
     * 
     */
    protected $ejercicioContable;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cuentasPresupuestarias = new ArrayCollection();
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
     * Add cuentasPresupuestarias
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentasPresupuestarias
     * @return Presupuesto
     */
    public function addCuentaPresupuestaria(\ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentasPresupuestarias) {
        $this->cuentasPresupuestarias[] = $cuentasPresupuestarias;

        $cuentasPresupuestarias->setPresupuesto($this);

        return $this;
    }

    /**
     * Remove cuentasPresupuestarias
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentasPresupuestarias
     */
    public function removeCuentaPresupuestaria(\ADIF\ContableBundle\Entity\CuentaPresupuestaria $cuentasPresupuestarias) {
        $this->cuentasPresupuestarias->removeElement($cuentasPresupuestarias);
    }

    /**
     * Get cuentasPresupuestarias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasPresupuestarias() {
        return $this->cuentasPresupuestarias;
    }

    /**
     * Set ejercicioContable
     *
     * @param \ADIF\ContableBundle\Entity\EjercicioContable $ejercicioContable
     * @return Presupuesto
     */
    public function setEjercicioContable(\ADIF\ContableBundle\Entity\EjercicioContable $ejercicioContable = null) {
        $this->ejercicioContable = $ejercicioContable;

        return $this;
    }

    /**
     * Get ejercicioContable
     *
     * @return \ADIF\ContableBundle\Entity\EjercicioContable 
     */
    public function getEjercicioContable() {
        return $this->ejercicioContable;
    }

    /**
     * Get presupuestoInicial
     * 
     * @return int
     */
    public function getPresupuestoInicial() {
        $total = 0;

        foreach ($this->cuentasPresupuestarias as $cuenta) {
            $total += $cuenta->getMontoInicial();
        }

        return $total;
    }

    /**
     * Get presupuestoActual
     * 
     * @return int
     */
    public function getPresupuestoActual() {
        $total = 0;

        foreach ($this->cuentasPresupuestarias as $cuenta) {
            $total += $cuenta->getMontoActual();
        }

        return $total;
    }

}
