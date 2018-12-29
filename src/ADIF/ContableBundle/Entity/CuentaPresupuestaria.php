<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of CuentaPresupuestaria    
 *
 * @author Manuel Becerra
 * created 01/10/2014
 * 
 * @ORM\Table(name="cuenta_presupuestaria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuentaPresupuestariaRepository")
 */
class CuentaPresupuestaria extends BaseAuditoria {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica
     *
     * @ORM\ManyToOne(targetEntity="CuentaPresupuestariaEconomica")
     * @ORM\JoinColumn(name="id_cuenta_presupuestaria_economica", referencedColumnName="id")
     * 
     */
    protected $cuentaPresupuestariaEconomica;

    /**
     * @var \ADIF\ContableBundle\Entity\Presupuesto
     *
     * @ORM\ManyToOne(targetEntity="Presupuesto", inversedBy="cuentasPresupuestarias" )
     * @ORM\JoinColumn(name="id_presupuesto", referencedColumnName="id", nullable=false)
     */
    protected $presupuesto;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_inicial", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto inicial debe ser de tipo numérico.")
     */
    protected $montoInicial;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_actual", type="float", nullable=false)
     * @Assert\Type(
     *   type="numeric",
     *   message="El monto actual debe ser de tipo numérico.")
     */
    protected $montoActual;

    /**
     * @ORM\OneToMany(targetEntity="Provisorio", mappedBy="cuentaPresupuestaria")
     */
    protected $provisorios;

    /**
     * @ORM\OneToMany(targetEntity="Definitivo", mappedBy="cuentaPresupuestaria")
     */
    protected $definitivos;

    /**
     * @ORM\OneToMany(targetEntity="Devengado", mappedBy="cuentaPresupuestaria")
     */
    protected $devengados;

    /**
     * @ORM\OneToMany(targetEntity="Ejecutado", mappedBy="cuentaPresupuestaria")
     */
    protected $ejecutados;

    /**
     * @ORM\OneToMany(targetEntity="MovimientoPresupuestario", mappedBy="cuentaPresupuestariaOrigen")
     */
    protected $movimientosPresupuestariosOrigen;

    /**
     * @ORM\OneToMany(targetEntity="MovimientoPresupuestario", mappedBy="cuentaPresupuestariaDestino")
     */
    protected $movimientosPresupuestariosDestino;

    /**
     * Constructor
     */
    public function __construct() {
        $this->provisorios = new ArrayCollection();
        $this->definitivos = new ArrayCollection();
        $this->devengados = new ArrayCollection();
        $this->ejecutados = new ArrayCollection();

        $this->movimientosPresupuestariosOrigen = new ArrayCollection();
        $this->movimientosPresupuestariosDestino = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->cuentaPresupuestariaEconomica->__toString();
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
     * Set montoInicial
     *
     * @param float $montoInicial
     * @return CuentaPresupuestaria
     */
    public function setMontoInicial($montoInicial) {
        $this->montoInicial = $montoInicial;

        return $this;
    }

    /**
     * Get montoInicial
     *
     * @return float 
     */
    public function getMontoInicial() {
        return $this->montoInicial;
    }

    /**
     * Set montoActual
     *
     * @param float $montoActual
     * @return CuentaPresupuestaria
     */
    public function setMontoActual($montoActual) {
        $this->montoActual = $montoActual;

        return $this;
    }

    /**
     * Get montoActual
     *
     * @return float 
     */
    public function getMontoActual() {
        return $this->montoActual;
    }

    /**
     * Set cuentaPresupuestariaEconomica
     *
     * @param \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return CuentaPresupuestaria
     */
    public function setCuentaPresupuestariaEconomica(\ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica = null) {
        $this->cuentaPresupuestariaEconomica = $cuentaPresupuestariaEconomica;

        return $this;
    }

    /**
     * Get cuentaPresupuestariaEconomica
     *
     * @return \ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica 
     */
    public function getCuentaPresupuestariaEconomica() {
        return $this->cuentaPresupuestariaEconomica;
    }

    /**
     * Set presupuesto
     *
     * @param \ADIF\ContableBundle\Entity\Presupuesto $presupuesto
     * @return CuentaPresupuestaria
     */
    public function setPresupuesto(\ADIF\ContableBundle\Entity\Presupuesto $presupuesto) {
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
     * Get ejercicioContable
     *
     * @return \ADIF\ContableBundle\Entity\EjercicioContable 
     */
    public function getEjercicioContable() {
        return $this->presupuesto->getEjercicioContable();
    }

    /**
     * 
     * @return string
     */
    public function getCuentaSaldo() {
        return $this->cuentaPresupuestariaEconomica . " - Saldo actual: $" . $this->getSaldo();
    }

    /**
     * 
     * @param type $em
     * @return type
     */
    public function actualizarMontoActual($em) {
        if (sizeof($this->getCuentaPresupuestariaEconomica()->getCuentasPresupuestariasEconomicasHijas()->toArray()) > 0) {
            $monto = 0;
            foreach ($this->getCuentaPresupuestariaEconomica()->getCuentasPresupuestariasEconomicasHijas() as $cuentaPresupuestariaEconomica) {
                $cuentaPresupuestaria = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')->findOneBy(
                        array(
                            'cuentaPresupuestariaEconomica' => $cuentaPresupuestariaEconomica
                        )
                );
                $monto += $cuentaPresupuestaria->actualizarMontoActual($em);
            }
            return $monto;
        } else {
            return $this->getMontoActual();
        }
    }

    /**
     * Add provisorios
     *
     * @param \ADIF\ContableBundle\Entity\Provisorio $provisorios
     * @return CuentaPresupuestaria
     */
    public function addProvisorio(\ADIF\ContableBundle\Entity\Provisorio $provisorios) {
        $this->provisorios[] = $provisorios;

        return $this;
    }

    /**
     * Remove provisorios
     *
     * @param \ADIF\ContableBundle\Entity\Provisorio $provisorios
     */
    public function removeProvisorio(\ADIF\ContableBundle\Entity\Provisorio $provisorios) {
        $this->provisorios->removeElement($provisorios);
    }

    /**
     * Get provisorios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProvisorios() {
        return $this->provisorios;
    }

    /**
     * Add definitivos
     *
     * @param \ADIF\ContableBundle\Entity\Definitivo $definitivos
     * @return CuentaPresupuestaria
     */
    public function addDefinitivo(\ADIF\ContableBundle\Entity\Definitivo $definitivos) {
        $this->definitivos[] = $definitivos;

        return $this;
    }

    /**
     * Remove definitivos
     *
     * @param \ADIF\ContableBundle\Entity\Definitivo $definitivos
     */
    public function removeDefinitivo(\ADIF\ContableBundle\Entity\Definitivo $definitivos) {
        $this->definitivos->removeElement($definitivos);
    }

    /**
     * Get definitivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDefinitivos() {
        return $this->definitivos;
    }

    /**
     * Add devengados
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengados
     * @return CuentaPresupuestaria
     */
    public function addDevengado(\ADIF\ContableBundle\Entity\Devengado $devengados) {
        $this->devengados[] = $devengados;

        return $this;
    }

    /**
     * Remove devengados
     *
     * @param \ADIF\ContableBundle\Entity\Devengado $devengados
     */
    public function removeDevengado(\ADIF\ContableBundle\Entity\Devengado $devengados) {
        $this->devengados->removeElement($devengados);
    }

    /**
     * Get devengados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDevengados() {
        return $this->devengados;
    }

    /**
     * Add ejecutados
     *
     * @param \ADIF\ContableBundle\Entity\Ejecutado $ejecutados
     * @return CuentaPresupuestaria
     */
    public function addEjecutado(\ADIF\ContableBundle\Entity\Ejecutado $ejecutados) {
        $this->ejecutados[] = $ejecutados;

        return $this;
    }

    /**
     * Remove ejecutados
     *
     * @param \ADIF\ContableBundle\Entity\Ejecutado $ejecutados
     */
    public function removeEjecutado(\ADIF\ContableBundle\Entity\Ejecutado $ejecutados) {
        $this->ejecutados->removeElement($ejecutados);
    }

    /**
     * Get ejecutados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEjecutados() {
        return $this->ejecutados;
    }

    /**
     * Get provisorios sin definitivos
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProvisoriosSinDefinitivo() {
        return $this->provisorios->filter(
                        function($provisorio) {
                    return $provisorio->getDefinitivo() == null;
                }
        );
    }

    /**
     * Get definitivos sin devengado
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDefinitivosSinDevengado() {
        return $this->definitivos->filter(
                        function($definitivo) {
                    return $definitivo->getSaldo() == 0;
                }
        );
    }

    /**
     * Get definitivos sin devengado
     * 
     * @return Collection 
     */
    public function getDevengadosSinEjecutado() {
        return $this->devengados->filter(
                        function($devengado) {
                    return $devengado->getEjecutado() == null;
                }
        );
    }

    /**
     * Add movimientosPresupuestariosOrigen
     *
     * @param \ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosOrigen
     * @return CuentaPresupuestaria
     */
    public function addMovimientosPresupuestariosOrigen(\ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosOrigen) {
        $this->movimientosPresupuestariosOrigen[] = $movimientosPresupuestariosOrigen;

        return $this;
    }

    /**
     * Remove movimientosPresupuestariosOrigen
     *
     * @param \ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosOrigen
     */
    public function removeMovimientosPresupuestariosOrigen(\ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosOrigen) {
        $this->movimientosPresupuestariosOrigen->removeElement($movimientosPresupuestariosOrigen);
    }

    /**
     * Get movimientosPresupuestariosOrigen
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientosPresupuestariosOrigen() {
        return $this->movimientosPresupuestariosOrigen;
    }

    /**
     * Add movimientosPresupuestariosDestino
     *
     * @param \ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosDestino
     * @return CuentaPresupuestaria
     */
    public function addMovimientosPresupuestariosDestino(\ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosDestino) {
        $this->movimientosPresupuestariosDestino[] = $movimientosPresupuestariosDestino;

        return $this;
    }

    /**
     * Remove movimientosPresupuestariosDestino
     *
     * @param \ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosDestino
     */
    public function removeMovimientosPresupuestariosDestino(\ADIF\ContableBundle\Entity\MovimientoPresupuestario $movimientosPresupuestariosDestino) {
        $this->movimientosPresupuestariosDestino->removeElement($movimientosPresupuestariosDestino);
    }

    /**
     * Get movimientosPresupuestariosDestino
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientosPresupuestariosDestino() {
        return $this->movimientosPresupuestariosDestino;
    }

    /**
     * Suma de todos los provisorios sin los definitivos
     * @return float Suma total
     */
    public function getTotalProvisorio() {
        $suma = 0;
        foreach ($this->getProvisoriosSinDefinitivo() as $provisorio) {
            $suma += $provisorio->getMonto();
        }
        return $suma;
    }

    /**
     * Suma de todos los definitivos sin devengados
     * @return float Suma total
     */
    public function getTotalDefinitivo() {
        $suma = 0;
        foreach ($this->getDefinitivosSinDevengado() as $definitivo) {
            $suma += $definitivo->getMonto();
        }
        return $suma;
    }

    /**
     * Suma de todos los devengados sin ejecutado
     * @return float Suma total
     */
    public function getTotalDevengado() {
        $suma = 0;
        foreach ($this->getDevengadosSinEjecutado() as $devengado) {
            $suma += $devengado->getMonto();
        }
        return $suma;
    }

    /**
     * Suma de todos los ejecutados
     * @return float Suma total
     */
    public function getTotalEjecutado() {
        $suma = 0;
        foreach ($this->getEjecutados() as $ejecutado) {
            $suma += $ejecutado->getMonto();
        }
        return $suma;
    }

    /**
     * 
     * @return type
	 * @TODO: deprecar esta funcion, no va mas!!! Tarda mucho, se come toda la memoria del servidor, entra en un bucle infinito y va 
	 * guardando todas las Entities del Doctrine en memoria y el servidor empieza a swapear, horribleeeee este metodo!!! - gluis - 12/01/2017
     */
    public function getSaldo() {

		// Posible solucion:
		// Ir a buscar el saldo del warehouse creado adifprod_warehouse.presupuesto_ejecucion
	
        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomica();

        $fechaEjercicioInicio = $this->getEjercicioContable()->getFechaInicio();
        $fechaEjercicioFin = $this->getEjercicioContable()->getFechaFin();

        return $this->montoActual //
                - $cuentaPresupuestariaEconomica->getTotalProvisorio($fechaEjercicioInicio, $fechaEjercicioFin) //
                - $cuentaPresupuestariaEconomica->getTotalDefinitivo($fechaEjercicioInicio, $fechaEjercicioFin) //
                - $cuentaPresupuestariaEconomica->getTotalDevengado($fechaEjercicioInicio, $fechaEjercicioFin) //
                - $cuentaPresupuestariaEconomica->getTotalEjecutado($fechaEjercicioInicio, $fechaEjercicioFin);
    }

    /**
     * 
     * @return type
     */
    public function getTieneSaldo() {

        return $this->getSaldo() > 0;
    }

}
