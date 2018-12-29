<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoOrdenPago 
 * 
 * Indica el estado de la Orden de Pago.
 *
 * @author Manuel Becerra
 * created 04/11/2014
 * 
 * @ORM\Table(name="estado_orden_pago")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionEstado", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class EstadoOrdenPago extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstado;

    /**
     * @ORM\Column(name="id_tipo_importancia", type="integer", nullable=true)
     */
    protected $idTipoImportancia;

    /**
     * @var ADIF\ComprasBundle\Entity\TipoImportancia
     */
    protected $tipoImportancia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_editable", type="boolean", nullable=false)
     */
    private $esEditable;

    /**
     * @ORM\OneToMany(targetEntity="OrdenPago", mappedBy="estadoOrdenPago")
     */
    protected $ordenesPago;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
        $this->ordenesPago = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstado;
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
     * Set denominacionEstado
     *
     * @param string $denominacionEstado
     * @return EstadoOrdenPago
     */
    public function setDenominacionEstado($denominacionEstado) {
        $this->denominacionEstado = $denominacionEstado;

        return $this;
    }

    /**
     * Get denominacionEstado
     *
     * @return string 
     */
    public function getDenominacionEstado() {
        return $this->denominacionEstado;
    }

    /**
     * Set descripcionEstado
     *
     * @param string $descripcionEstado
     * @return EstadoOrdenPago
     */
    public function setDescripcionEstado($descripcionEstado) {
        $this->descripcionEstado = $descripcionEstado;

        return $this;
    }

    /**
     * Get descripcionEstado
     *
     * @return string 
     */
    public function getDescripcionEstado() {
        return $this->descripcionEstado;
    }

    /**
     * 
     * @return type
     */
    public function getIdTipoImportancia() {
        return $this->idTipoImportancia;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia
     */
    public function setTipoImportancia($tipoImportancia) {

        if (null != $tipoImportancia) {
            $this->idTipoImportancia = $tipoImportancia->getId();
        } //.
        else {
            $this->idTipoImportancia = null;
        }

        $this->tipoImportancia = $tipoImportancia;
    }

    /**
     * 
     * @return type
     */
    public function getTipoImportancia() {
        return $this->tipoImportancia;
    }

    /**
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoOrdenPago
     */
    public function setEsEditable($esEditable) {
        $this->esEditable = $esEditable;

        return $this;
    }

    /**
     * Get esEditable
     *
     * @return boolean 
     */
    public function getEsEditable() {
        return $this->esEditable;
    }

    /**
     * Add ordenesPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPago $ordenesPago
     * @return EstadoOrdenPago
     */
    public function addOrdenesPago(\ADIF\ContableBundle\Entity\OrdenPago $ordenesPago) {
        $this->ordenesPago[] = $ordenesPago;

        return $this;
    }

    /**
     * Remove ordenesPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPago $ordenesPago
     */
    public function removeOrdenesPago(\ADIF\ContableBundle\Entity\OrdenPago $ordenesPago) {
        $this->ordenesPago->removeElement($ordenesPago);
    }

    /**
     * Get ordenesPago
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenesPago() {
        return $this->ordenesPago;
    }

}
