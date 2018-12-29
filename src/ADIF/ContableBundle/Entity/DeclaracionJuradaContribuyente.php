<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaIIBBContribuyente
 *
 * @author Darío Rapetti
 * created 09/06/2015
 * 
 * @ORM\Table(name="declaracion_jurada_contribuyente")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "declaracion_jurada_iva_contribuyente" = "DeclaracionJuradaIvaContribuyente",
 *      "declaracion_jurada_iibb_contribuyente" = "DeclaracionJuradaIIBBContribuyente"
 * })
 */
abstract class DeclaracionJuradaContribuyente extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\EstadoDeclaracionJuradaContribuyente
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EstadoDeclaracionJuradaContribuyente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_estado_declaracion_jurada_contribuyente", referencedColumnName="id", nullable=false)
     * })
     */
    private $estadoDeclaracionJuradaContribuyente;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="datetime", nullable=false)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="datetime", nullable=false)
     */
    protected $fechaFin;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
     */
    protected $fechaCierre;

    /**
     * @var string
     * @ORM\Column(name="saldo", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $saldo;
    
    /**
     * @var string
     * @ORM\Column(name="saldo_mes_siguiente", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $saldoMesSiguiente;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return DeclaracionJuradaIvaContribuyente
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
     * @return DeclaracionJuradaIvaContribuyente
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
     * Set fechaCierre
     *
     * @param \DateTime $fechaCierre
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setFechaCierre($fechaCierre) {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre
     *
     * @return \DateTime 
     */
    public function getFechaCierre() {
        return $this->fechaCierre;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     * @return DeclaracionJuradaContribuyente
     */
    public function setSaldo($saldo) {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string 
     */
    public function getSaldo() {
        return $this->saldo;
    }
    
    /**
     * Set saldoMesSiguiente
     *
     * @param string $saldoMesSiguiente
     * @return DeclaracionJuradaContribuyente
     */
    public function setSaldoMesSiguiente($saldoMesSiguiente) {
        $this->saldoMesSiguiente = $saldoMesSiguiente;

        return $this;
    }

    /**
     * Get saldoMesSiguiente
     *
     * @return string 
     */
    public function getSaldoMesSiguiente() {
        return $this->saldoMesSiguiente;
    }

    /**
     * Set estadoDeclaracionJuradaContribuyente
     *
     * @param \ADIF\ContableBundle\Entity\EstadoDeclaracionJuradaContribuyente $estadoDeclaracionJuradaContribuyente
     * @return DeclaracionJuradaContribuyente
     */
    public function setEstadoDeclaracionJuradaContribuyente(\ADIF\ContableBundle\Entity\EstadoDeclaracionJuradaContribuyente $estadoDeclaracionJuradaContribuyente) {
        $this->estadoDeclaracionJuradaContribuyente = $estadoDeclaracionJuradaContribuyente;

        return $this;
    }

    /**
     * Get estadoDeclaracionJuradaContribuyente
     *
     * @return \ADIF\ContableBundle\Entity\EstadoDeclaracionJuradaContribuyente 
     */
    public function getEstadoDeclaracionJuradaContribuyente() {
        return $this->estadoDeclaracionJuradaContribuyente;
    }
    
    public function getPeriodo(){
        setlocale(LC_ALL,"es_AR.UTF-8");
        return ucfirst(strftime("%B %Y", $this->getFechaInicio()->getTimestamp()));
    }

}
