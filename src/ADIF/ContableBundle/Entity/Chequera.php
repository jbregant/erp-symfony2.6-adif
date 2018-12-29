<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Chequera
 *
 * @author Manuel Becerra
 * created 07/10/2014
 * 
 * @ORM\Table(name="chequera")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ChequeraRepository")
 * @UniqueEntity(
 *      fields = {"idCuenta", "numeroSerie", "numeroInicial", "numeroFinal", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="No se puede crear la chequera ya que existe otra con igual cuenta bancaria, n&uacute;mero de serie, n&uacute;mero inicial y n&uacute;mero final."
 * )
 */
class Chequera extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\TipoChequera
     *
     * @ORM\ManyToOne(targetEntity="TipoChequera")
     * @ORM\JoinColumn(name="id_tipo_chequera", referencedColumnName="id")
     * 
     */
    protected $tipoChequera;

    /**
     * @var \ADIF\ContableBundle\Entity\EstadoChequera
     *
     * @ORM\ManyToOne(targetEntity="EstadoChequera")
     * @ORM\JoinColumn(name="id_estado_chequera", referencedColumnName="id")
     * 
     */
    protected $estadoChequera;

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=true)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF
     */
    protected $cuenta;

    /**
     * @var integer
     *
     * @ORM\Column(name="responsable", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="El responsable no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $responsable;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_inicial", type="integer", nullable=false)
     */
    protected $numeroInicial;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_final", type="integer", nullable=false)
     */
    protected $numeroFinal;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_serie", type="string", length=5, nullable=false)
     * @Assert\Length(
     *      max="5", 
     *      maxMessage="El número de serie no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroSerie;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_siguiente", type="integer", nullable=false)
     */
    protected $numeroSiguiente;

    /**
     * @ORM\OneToMany(targetEntity="Cheque", mappedBy="chequera")
     */
    protected $cheques;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cheques = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->numeroInicial . ' - ' . $this->numeroFinal;
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
     * Set tipoChequera
     *
     * @param \ADIF\ContableBundle\Entity\TipoChequera $tipoChequera
     * @return Chequera
     */
    public function setTipoChequera(\ADIF\ContableBundle\Entity\TipoChequera $tipoChequera = null) {
        $this->tipoChequera = $tipoChequera;

        return $this;
    }

    /**
     * Get tipoChequera
     *
     * @return \ADIF\ContableBundle\Entity\TipoChequera 
     */
    public function getTipoChequera() {
        return $this->tipoChequera;
    }

    /**
     * Set estadoChequera
     *
     * @param \ADIF\ContableBundle\Entity\EstadoChequera $estadoChequera
     * @return Chequera
     */
    public function setEstadoChequera(\ADIF\ContableBundle\Entity\EstadoChequera $estadoChequera = null) {
        $this->estadoChequera = $estadoChequera;

        return $this;
    }

    /**
     * Get estadoChequera
     *
     * @return \ADIF\ContableBundle\Entity\EstadoChequera 
     */
    public function getEstadoChequera() {
        return $this->estadoChequera;
    }

// CUENTA  

    /**
     * 
     * @param type $idCuenta
     * @return \ADIF\ComprasBundle\Entity\Proveedor
     */
    public function setIdCuenta($idCuenta) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCuenta() {
        return $this->idCuenta;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF $cuenta
     */
    public function setCuenta($cuenta) {

        if (null != $cuenta) {
            $this->idCuenta = $cuenta->getId();
        } //.
        else {
            $this->idCuenta = null;
        }

        $this->cuenta = $cuenta;
    }

    /**
     * 
     * @return type
     */
    public function getCuenta() {
        return $this->cuenta;
    }

// FIN CUENTA

    /**
     * Set responsable
     *
     * @param string $responsable
     * @return Chequera
     */
    public function setResponsable($responsable) {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string 
     */
    public function getResponsable() {
        return $this->responsable;
    }

    /**
     * Set numeroInicial
     *
     * @param integer $numeroInicial
     * @return Chequera
     */
    public function setNumeroInicial($numeroInicial) {
        $this->numeroInicial = $numeroInicial;

        return $this;
    }

    /**
     * Get numeroInicial
     *
     * @return integer 
     */
    public function getNumeroInicial() {
        return $this->numeroInicial;
    }

    /**
     * Set numeroFinal
     *
     * @param integer $numeroFinal
     * @return Chequera
     */
    public function setNumeroFinal($numeroFinal) {
        $this->numeroFinal = $numeroFinal;

        return $this;
    }

    /**
     * Get numeroFinal
     *
     * @return integer 
     */
    public function getNumeroFinal() {
        return $this->numeroFinal;
    }

    /**
     * Set numeroSerie
     *
     * @param string $numeroSerie
     * @return Chequera
     */
    public function setNumeroSerie($numeroSerie) {
        $this->numeroSerie = $numeroSerie;

        return $this;
    }

    /**
     * Get numeroSerie
     *
     * @return string 
     */
    public function getNumeroSerie() {
        return $this->numeroSerie;
    }

    /**
     * Set numeroSiguiente
     *
     * @param integer $numeroSiguiente
     * @return Chequera
     */
    public function setNumeroSiguiente($numeroSiguiente) {
        $this->numeroSiguiente = $numeroSiguiente;

        return $this;
    }

    /**
     * Get numeroSiguiente
     *
     * @return integer 
     */
    public function getNumeroSiguiente() {
        return $this->numeroSiguiente;
    }

    /**
     * Add cheques
     *
     * @param \ADIF\ContableBundle\Entity\Cheque $cheques
     * @return Chequera
     */
    public function addCheque(\ADIF\ContableBundle\Entity\Cheque $cheques) {
        $this->cheques[] = $cheques;

        return $this;
    }

    /**
     * Remove cheques
     *
     * @param \ADIF\ContableBundle\Entity\Cheque $cheques
     */
    public function removeCheque(\ADIF\ContableBundle\Entity\Cheque $cheques) {
        $this->cheques->removeElement($cheques);
    }

    /**
     * Get cheques
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCheques() {
        return $this->cheques;
    }

}
