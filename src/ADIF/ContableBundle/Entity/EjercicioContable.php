<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EjercicioContable
 *
 * @author Manuel Becerra
 * created 24/06/2014
 * 
 * @ORM\Table(name="ejercicio_contable")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\EjercicioContableRepository")
 * @UniqueEntity("denominacionEjercicio", message="La denominación ingresada ya se encuentra en uso.")
 */
class EjercicioContable extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del ejercicio no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionEjercicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=false)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date", nullable=false)
     */
    protected $fechaFin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esta_cerrado", type="boolean", nullable=false)
     */
    protected $estaCerrado;

    /**
     * Inidica la cantidad de veces que se cerró el ejercicio contable.
     * 
     * @var integer
     *
     * @ORM\Column(name="cantidad_cierres", type="integer", nullable=false)
     */
    protected $cantidadCierres;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_enero_habilitado", type="boolean", nullable=false)
     */
    protected $periodoEneroHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_febrero_habilitado", type="boolean", nullable=false)
     */
    protected $periodoFebreroHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_marzo_habilitado", type="boolean", nullable=false)
     */
    protected $periodoMarzoHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_abril_habilitado", type="boolean", nullable=false)
     */
    protected $periodoAbrilHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_mayo_habilitado", type="boolean", nullable=false)
     */
    protected $periodoMayoHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_junio_habilitado", type="boolean", nullable=false)
     */
    protected $periodoJunioHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_julio_habilitado", type="boolean", nullable=false)
     */
    protected $periodoJulioHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_agosto_habilitado", type="boolean", nullable=false)
     */
    protected $periodoAgostoHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_septiembre_habilitado", type="boolean", nullable=false)
     */
    protected $periodoSeptiembreHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_octubre_habilitado", type="boolean", nullable=false)
     */
    protected $periodoOctubreHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_noviembre_habilitado", type="boolean", nullable=false)
     */
    protected $periodoNoviembreHabilitado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="periodo_diciembre_habilitado", type="boolean", nullable=false)
     */
    protected $periodoDiciembreHabilitado;

    /**
     * @var \ADIF\ContableBundle\Entity\Presupuesto
     *
     * @ORM\OneToOne(targetEntity="Presupuesto", mappedBy="ejercicioContable", cascade="all")
     * 
     */
    protected $presupuesto;

    /**
     * Constructor
     */
    public function __construct() {

        $this->estaCerrado = false;

        $this->cantidadCierres = 0;

        $this->periodoEneroHabilitado = true;
        $this->periodoFebreroHabilitado = true;
        $this->periodoMarzoHabilitado = true;
        $this->periodoAbrilHabilitado = true;
        $this->periodoMayoHabilitado = true;
        $this->periodoJunioHabilitado = true;
        $this->periodoJulioHabilitado = true;
        $this->periodoAgostoHabilitado = true;
        $this->periodoSeptiembreHabilitado = true;
        $this->periodoOctubreHabilitado = true;
        $this->periodoNoviembreHabilitado = true;
        $this->periodoDiciembreHabilitado = true;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEjercicio;
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
     * Set denominacionEjercicio
     *
     * @param string $denominacionEjercicio
     * @return EjercicioContable
     */
    public function setDenominacionEjercicio($denominacionEjercicio) {
        $this->denominacionEjercicio = $denominacionEjercicio;

        return $this;
    }

    /**
     * Get denominacionEjercicio
     *
     * @return string 
     */
    public function getDenominacionEjercicio() {
        return $this->denominacionEjercicio;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return EjercicioContable
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
     * @return EjercicioContable
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
     * Set presupuesto
     *
     * @param \ADIF\ContableBundle\Entity\Presupuesto $presupuesto
     * @return EjercicioContable
     */
    public function setPresupuesto(\ADIF\ContableBundle\Entity\Presupuesto $presupuesto = null) {
        $this->presupuesto = $presupuesto;

        return $this;
    }

    /**
     * Get presupuesto
     *
     * @return \ADIF\ContableBundle\Entity\Presupuesto 
     */
    public function getPresupuesto() {
        return $this->presupuesto;
    }

    /**
     * Set estaCerrado
     *
     * @param boolean $estaCerrado
     * @return EjercicioContable
     */
    public function setEstaCerrado($estaCerrado) {

        $this->estaCerrado = $estaCerrado;

        return $this;
    }

    /**
     * Get estaCerrado
     *
     * @return boolean 
     */
    public function getEstaCerrado() {
        return $this->estaCerrado;
    }

    /**
     * Set cantidadCierres
     *
     * @param integer $cantidadCierres
     * @return EjercicioContable
     */
    public function setCantidadCierres($cantidadCierres) {
        $this->cantidadCierres = $cantidadCierres;

        return $this;
    }

    /**
     * Get cantidadCierres
     *
     * @return integer 
     */
    public function getCantidadCierres() {
        return $this->cantidadCierres;
    }

    /**
     * Set periodoEneroHabilitado
     *
     * @param boolean $periodoEneroHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoEneroHabilitado($periodoEneroHabilitado) {
        $this->periodoEneroHabilitado = $periodoEneroHabilitado;

        return $this;
    }

    /**
     * Get periodoEneroHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoEneroHabilitado() {
        return $this->periodoEneroHabilitado;
    }

    /**
     * Set periodoFebreroHabilitado
     *
     * @param boolean $periodoFebreroHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoFebreroHabilitado($periodoFebreroHabilitado) {
        $this->periodoFebreroHabilitado = $periodoFebreroHabilitado;

        return $this;
    }

    /**
     * Get periodoFebreroHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoFebreroHabilitado() {
        return $this->periodoFebreroHabilitado;
    }

    /**
     * Set periodoMarzoHabilitado
     *
     * @param boolean $periodoMarzoHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoMarzoHabilitado($periodoMarzoHabilitado) {
        $this->periodoMarzoHabilitado = $periodoMarzoHabilitado;

        return $this;
    }

    /**
     * Get periodoMarzoHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoMarzoHabilitado() {
        return $this->periodoMarzoHabilitado;
    }

    /**
     * Set periodoAbrilHabilitado
     *
     * @param boolean $periodoAbrilHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoAbrilHabilitado($periodoAbrilHabilitado) {
        $this->periodoAbrilHabilitado = $periodoAbrilHabilitado;

        return $this;
    }

    /**
     * Get periodoAbrilHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoAbrilHabilitado() {
        return $this->periodoAbrilHabilitado;
    }

    /**
     * Set periodoMayoHabilitado
     *
     * @param boolean $periodoMayoHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoMayoHabilitado($periodoMayoHabilitado) {
        $this->periodoMayoHabilitado = $periodoMayoHabilitado;

        return $this;
    }

    /**
     * Get periodoMayoHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoMayoHabilitado() {
        return $this->periodoMayoHabilitado;
    }

    /**
     * Set periodoJunioHabilitado
     *
     * @param boolean $periodoJunioHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoJunioHabilitado($periodoJunioHabilitado) {
        $this->periodoJunioHabilitado = $periodoJunioHabilitado;

        return $this;
    }

    /**
     * Get periodoJunioHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoJunioHabilitado() {
        return $this->periodoJunioHabilitado;
    }

    /**
     * Set periodoJulioHabilitado
     *
     * @param boolean $periodoJulioHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoJulioHabilitado($periodoJulioHabilitado) {
        $this->periodoJulioHabilitado = $periodoJulioHabilitado;

        return $this;
    }

    /**
     * Get periodoJulioHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoJulioHabilitado() {
        return $this->periodoJulioHabilitado;
    }

    /**
     * Set periodoAgostoHabilitado
     *
     * @param boolean $periodoAgostoHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoAgostoHabilitado($periodoAgostoHabilitado) {
        $this->periodoAgostoHabilitado = $periodoAgostoHabilitado;

        return $this;
    }

    /**
     * Get periodoAgostoHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoAgostoHabilitado() {
        return $this->periodoAgostoHabilitado;
    }

    /**
     * Set periodoSeptiembreHabilitado
     *
     * @param boolean $periodoSeptiembreHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoSeptiembreHabilitado($periodoSeptiembreHabilitado) {
        $this->periodoSeptiembreHabilitado = $periodoSeptiembreHabilitado;

        return $this;
    }

    /**
     * Get periodoSeptiembreHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoSeptiembreHabilitado() {
        return $this->periodoSeptiembreHabilitado;
    }

    /**
     * Set periodoOctubreHabilitado
     *
     * @param boolean $periodoOctubreHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoOctubreHabilitado($periodoOctubreHabilitado) {
        $this->periodoOctubreHabilitado = $periodoOctubreHabilitado;

        return $this;
    }

    /**
     * Get periodoOctubreHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoOctubreHabilitado() {
        return $this->periodoOctubreHabilitado;
    }

    /**
     * Set periodoNoviembreHabilitado
     *
     * @param boolean $periodoNoviembreHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoNoviembreHabilitado($periodoNoviembreHabilitado) {
        $this->periodoNoviembreHabilitado = $periodoNoviembreHabilitado;

        return $this;
    }

    /**
     * Get periodoNoviembreHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoNoviembreHabilitado() {
        return $this->periodoNoviembreHabilitado;
    }

    /**
     * Set periodoDiciembreHabilitado
     *
     * @param boolean $periodoDiciembreHabilitado
     * @return EjercicioContable
     */
    public function setPeriodoDiciembreHabilitado($periodoDiciembreHabilitado) {
        $this->periodoDiciembreHabilitado = $periodoDiciembreHabilitado;

        return $this;
    }

    /**
     * Get periodoDiciembreHabilitado
     *
     * @return boolean 
     */
    public function getPeriodoDiciembreHabilitado() {
        return $this->periodoDiciembreHabilitado;
    }

    /**
     * 
     * @return int
     */
    public function getMesCerradoSuperior() {

        $mesCerradoSuperior = 0;

        switch (false) {
            case $this->periodoDiciembreHabilitado:
                $mesCerradoSuperior = 12;
                break;
            case $this->periodoNoviembreHabilitado:
                $mesCerradoSuperior = 11;
                break;
            case $this->periodoOctubreHabilitado:
                $mesCerradoSuperior = 10;
                break;
            case $this->periodoSeptiembreHabilitado:
                $mesCerradoSuperior = 9;
                break;
            case $this->periodoAgostoHabilitado:
                $mesCerradoSuperior = 8;
                break;
            case $this->periodoJulioHabilitado:
                $mesCerradoSuperior = 7;
                break;
            case $this->periodoJunioHabilitado:
                $mesCerradoSuperior = 6;
                break;
            case $this->periodoMayoHabilitado:
                $mesCerradoSuperior = 5;
                break;
            case $this->periodoAbrilHabilitado:
                $mesCerradoSuperior = 4;
                break;
            case $this->periodoMarzoHabilitado:
                $mesCerradoSuperior = 3;
                break;
            case $this->periodoFebreroHabilitado:
                $mesCerradoSuperior = 2;
                break;
            case $this->periodoEneroHabilitado:
                $mesCerradoSuperior = 1;
                break;
        }

        return $mesCerradoSuperior;
    }
    
    /**
     * @return boolean 
     */
    public function getMesEjercicioHabilitado($mes) {
        switch ($mes) {
            case 1:
                $ok = $this->periodoEneroHabilitado;
                break;
            case 2:
                $ok = $this->periodoFebreroHabilitado;
                break;
            case 3:
                $ok = $this->periodoMarzoHabilitado;
                break;
            case 4:
                $ok = $this->periodoAbrilHabilitado;
                break;            
            case 5:
                $ok = $this->periodoMayoHabilitado;
                break;
            case 6:
                $ok = $this->periodoJunioHabilitado;
                break; 
            case 7:
                $ok = $this->periodoJulioHabilitado;
                break;
            case 8:
                $ok = $this->periodoAgostoHabilitado;
                break;            
            case 9:
                $ok = $this->periodoSeptiembreHabilitado;
                break;
            case 10:
                $ok = $this->periodoOctubreHabilitado;
                break;
            case 11:
                $ok = $this->periodoNoviembreHabilitado;
                break;
            case 12:
                $ok = $this->periodoDiciembreHabilitado;
                break;            
        }
        return $ok;
    }    

}
