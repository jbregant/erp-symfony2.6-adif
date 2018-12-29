<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BienEconomico 
 *
 * @author Carlos Sabena
 * created 12/07/2014
 * 
 * @ORM\Table(name="bien_economico")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\BienEconomicoRepository")
 * @UniqueEntity("denominacionBienEconomico", message="La denominación ingresada ya se encuentra en uso.")
 */
class BienEconomico extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionBienEconomico;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionBienEconomico;

    /**
     * @var \ADIF\ComprasBundle\Entity\Rubro
     *
     * @ORM\ManyToOne(targetEntity="Rubro", inversedBy="bienesEconomicos")
     * @ORM\JoinColumn(name="id_rubro", referencedColumnName="id")
     * 
     */
    protected $rubro;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoBienEconomico
     *
     * @ORM\ManyToOne(targetEntity="EstadoBienEconomico", inversedBy="bienesEconomicos")
     * @ORM\JoinColumn(name="id_estado_bien_economico", referencedColumnName="id")
     * 
     */
    protected $estadoBienEconomico;

    /**
     * @var boolean
     *
     * @ORM\Column(name="requiere_especificacion_tecnica", type="boolean", nullable=false)
     */
    protected $requiereEspecificacionTecnica;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_producto", type="boolean", nullable=false)
     */
    protected $esProducto;

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var ADIF\ContableBundle\Entity\CuentaContable
     */
    protected $cuentaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_interno", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código interno no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoInterno;

    /**
     * Constructor
     */
    public function __construct() {
        $this->requiereEspecificacionTecnica = false;
        $this->esProducto = true;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionBienEconomico;
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
     * Get codigoBienEconomico
     *
     * @return string 
     */
    public function getCodigoBienEconomico() {

        return str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Set denominacionBienEconomico
     *
     * @param string $denominacionBienEconomico
     * @return BienEconomico
     */
    public function setDenominacionBienEconomico($denominacionBienEconomico) {
        $this->denominacionBienEconomico = $denominacionBienEconomico;

        return $this;
    }

    /**
     * Get denominacionBienEconomico
     *
     * @return string 
     */
    public function getDenominacionBienEconomico() {
        return $this->denominacionBienEconomico;
    }

    /**
     * Set descripcionBienEconomico
     *
     * @param string $descripcionBienEconomico
     * @return BienEconomico
     */
    public function setDescripcionBienEconomico($descripcionBienEconomico) {
        $this->descripcionBienEconomico = $descripcionBienEconomico;

        return $this;
    }

    /**
     * Get descripcionBienEconomico
     *
     * @return string 
     */
    public function getDescripcionBienEconomico() {
        return $this->descripcionBienEconomico;
    }

    /**
     * Set requiereEspecificacionTecnica
     *
     * @param boolean $requiereEspecificacionTecnica
     * @return BienEconomico
     */
    public function setRequiereEspecificacionTecnica($requiereEspecificacionTecnica) {
        $this->requiereEspecificacionTecnica = $requiereEspecificacionTecnica;

        return $this;
    }

    /**
     * Get requiereEspecificacionTecnica
     *
     * @return boolean 
     */
    public function getRequiereEspecificacionTecnica() {
        return $this->requiereEspecificacionTecnica;
    }

    /**
     * Set rubro
     *
     * @param \ADIF\ComprasBundle\Entity\Rubro $rubro
     * @return BienEconomico
     */
    public function setRubro(\ADIF\ComprasBundle\Entity\Rubro $rubro = null) {
        $this->rubro = $rubro;

        return $this;
    }

    /**
     * Get rubro
     *
     * @return \ADIF\ComprasBundle\Entity\Rubro 
     */
    public function getRubro() {
        return $this->rubro;
    }

    /**
     * Set estadoBienEconomico
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoBienEconomico $estadoBienEconomico
     * @return BienEconomico
     */
    public function setEstadoBienEconomico(\ADIF\ComprasBundle\Entity\EstadoBienEconomico $estadoBienEconomico = null) {
        $this->estadoBienEconomico = $estadoBienEconomico;

        return $this;
    }

    /**
     * Get estadoBienEconomico
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoBienEconomico 
     */
    public function getEstadoBienEconomico() {
        return $this->estadoBienEconomico;
    }

    /**
     * 
     * @return type
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
        } //.
        else {
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

    /**
     * Set esProducto
     *
     * @param boolean $esProducto
     * @return BienEconomico
     */
    public function setEsProducto($esProducto) {
        $this->esProducto = $esProducto;

        return $this;
    }

    /**
     * Get esProducto
     *
     * @return boolean 
     */
    public function getEsProducto() {
        return $this->esProducto;
    }

    /**
     * Set codigoInterno
     *
     * @param string $codigoInterno
     * @return BienEconomico
     */
    public function setCodigoInterno($codigoInterno) {
        $this->codigoInterno = $codigoInterno;

        return $this;
    }

    /**
     * Get codigoInterno
     *
     * @return string 
     */
    public function getCodigoInterno() {
        return $this->codigoInterno;
    }

}
