<?php

namespace ADIF\ContableBundle\Entity\ConciliacionBancaria;

use ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of GastoBancario
 *
 * @author Darío Rapetti
 * created 30/01/2015
 * 
 * @ORM\Table(name="gasto_bancario")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"renglonConciliacion", "fecha"}, 
 *      ignoreNull = false
 * )
 */
class GastoBancario extends MovimientoConciliable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     *
     * @var \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion")
     * @ORM\JoinColumn(name="renglon_conciliacion_id", referencedColumnName="id")
     */
    protected $renglonConciliacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return GastoBancario
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

    /**
     * Set renglonConciliacion
     *
     * @param \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $renglonConciliacion
     * @return GastoBancario
     */
    public function setRenglonConciliacion(\ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion $renglonConciliacion = null) {
        $this->renglonConciliacion = $renglonConciliacion;

        return $this;
    }

    /**
     * Get renglonConciliacion
     *
     * @return \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion 
     */
    public function getRenglonConciliacion() {
        return $this->renglonConciliacion;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->fecha = new \DateTime();
    }

    public function cumpleCondicion($cuentaBancaria, $fecha_inicio, $fecha_fin) {
        return ($fecha_inicio && $fecha_fin ? $this->getFecha() >= $fecha_inicio && $this->getFecha() <= $fecha_fin : true);
    }

    public function getConcepto() {
        return 'Gasto Bancario N&ordm;: ' . $this->getReferencia();
    }

    public function getReferencia() {
        return $this->getRenglonConciliacion()->getNumeroReferencia();
    }

    public function getTipo() {
        return 'Gasto bancario';
    }

    public function getMontoMovimiento($cuentaBancaria = null) {
        return $this->getRenglonConciliacion()->getMonto() * -1;
    }

    public function getMonto() {
        return $this->getMontoMovimiento();
    }

}
