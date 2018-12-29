<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SegmentoPlanDeCuentas
 *
 * @author Manuel Becerra
 * created 24/06/2014
 * 
 * @ORM\Table(name="segmento_plan_de_cuentas")
 * @ORM\Entity
 * @UniqueEntity("posicion", message="La posición ingresada ya se encuentra en uso.")
 * 
 */
class SegmentoPlanDeCuentas extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\PlanDeCuentas
     *
     * @ORM\ManyToOne(targetEntity="PlanDeCuentas", inversedBy="segmentos")
     * @ORM\JoinColumn(name="id_plan_de_cuentas", referencedColumnName="id")
     * 
     */
    protected $planDeCuentas;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoSegmentoPlanDeCuentas
     *
     * @ORM\ManyToOne(targetEntity="TipoSegmentoPlanDeCuentas")
     * @ORM\JoinColumn(name="id_tipo_segmento", referencedColumnName="id")
     * 
     */
    protected $tipoSegmento;

    /**
     * @var integer
     *
     * @ORM\Column(name="posicion", type="decimal", precision=2, scale=0, unique=true, nullable=false)
     * @Assert\Range(
     *      min = 1,
     *      minMessage = "La posición mínima es {{ limit }}."
     * )
     */
    protected $posicion;

    /**
     * @var integer
     *
     * @ORM\Column(name="longitud", type="decimal", precision=2, scale=0, nullable=false)
     * @Assert\Range(
     *      min = 1,
     *      max = 10,
     *      minMessage = "La longitud mínima es {{ limit }}.",
     *      maxMessage = "La longitud máxima es {{ limit }}."
     * )
     */
    protected $longitud;

    /**
     * @var string
     *
     * @ORM\Column(name="separador", type="string", length=5, nullable=true)
     * @Assert\Length(
     *      max="2", 
     *      maxMessage="El separador no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $separador;

    /**
     * @var boolean
     *
     * @ORM\Column(name="indica_centro_de_costo", type="boolean", nullable=false)
     */
    protected $indicaCentroDeCosto;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del segmento no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionSegmento;

    /**
     * Constructor
     */
    public function __construct() {
        $this->longitud = 1;
        $this->indicaCentroDeCosto = false;
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
     * Set tipoSegmento
     *
     * @param \ADIF\ContableBundle\Entity\TipoSegmentoPlanDeCuentas $tipoSegmento
     * @return SegmentoPlanDeCuentas
     */
    public function setTipoSegmento(\ADIF\ContableBundle\Entity\TipoSegmentoPlanDeCuentas $tipoSegmento = null) {
        $this->tipoSegmento = $tipoSegmento;

        return $this;
    }

    /**
     * Get tipoSegmento
     *
     * @return \ADIF\ContableBundle\Entity\TipoSegmentoPlanDeCuentas 
     */
    public function getTipoSegmento() {
        return $this->tipoSegmento;
    }

    /**
     * Set posicion
     *
     * @param integer $posicion
     * @return SegmentoPlanDeCuentas
     */
    public function setPosicion($posicion) {
        $this->posicion = $posicion;

        return $this;
    }

    /**
     * Get posicion
     *
     * @return integer 
     */
    public function getPosicion() {
        return $this->posicion;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return SegmentoPlanDeCuentas
     */
    public function setLongitud($longitud) {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return integer 
     */
    public function getLongitud() {
        return $this->longitud;
    }

    /**
     * Set separador
     *
     * @param string $separador
     * @return SegmentoPlanDeCuentas
     */
    public function setSeparador($separador) {
        $this->separador = $separador;

        return $this;
    }

    /**
     * Get separador
     *
     * @return string 
     */
    public function getSeparador() {
        return $this->separador;
    }

    /**
     * Set indicaCentroDeCosto
     *
     * @param boolean $indicaCentroDeCosto
     * @return SegmentoPlanDeCuentas
     */
    public function setIndicaCentroDeCosto($indicaCentroDeCosto) {
        $this->indicaCentroDeCosto = $indicaCentroDeCosto;

        return $this;
    }

    /**
     * Get indicaCentroDeCosto
     *
     * @return boolean 
     */
    public function getIndicaCentroDeCosto() {
        return $this->indicaCentroDeCosto;
    }

    /**
     * Set denominacionSegmento
     *
     * @param string $denominacionSegmento
     * @return SegmentoPlanDeCuentas
     */
    public function setDenominacionSegmento($denominacionSegmento) {
        $this->denominacionSegmento = $denominacionSegmento;

        return $this;
    }

    /**
     * Get denominacionSegmento
     *
     * @return string 
     */
    public function getDenominacionSegmento() {
        return $this->denominacionSegmento;
    }

    /**
     * Set planDeCuentas
     *
     * @param \ADIF\ContableBundle\Entity\PlanDeCuentas $planDeCuentas
     * @return SegmentoPlanDeCuentas
     */
    public function setPlanDeCuentas(\ADIF\ContableBundle\Entity\PlanDeCuentas $planDeCuentas = null) {
        $this->planDeCuentas = $planDeCuentas;

        return $this;
    }

    /**
     * Get planDeCuentas
     *
     * @return \ADIF\ContableBundle\Entity\PlanDeCuentas 
     */
    public function getPlanDeCuentas() {
        return $this->planDeCuentas;
    }

}
