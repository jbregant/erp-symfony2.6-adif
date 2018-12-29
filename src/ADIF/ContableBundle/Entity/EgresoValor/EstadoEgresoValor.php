<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoEgresoValor 
 * 
 * Indica el estado de la EgresoValor
 *
 * @author Manuel Becerra
 * created 15/01/2015
 * 
 * @ORM\Table(name="estado_egreso_valor")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstado", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoEgresoValor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
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
    protected $esEditable;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
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
     * @return EstadoEgresoValor
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
     * @return EstadoEgresoValor
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
     * @return EstadoEgresoValor
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
     * Set codigo
     *
     * @param integer $codigo
     * @return EstadoEgresoValor
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo() {
        return $this->codigo;
    }

}
