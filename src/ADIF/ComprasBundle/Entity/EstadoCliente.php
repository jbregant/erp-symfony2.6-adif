<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoCliente 
 * 
 * Indica el estado del Cliente. 
 * 
 * Por ejemplo:
 *      Activo.
 *      Inactivo.
 *      Suspendido.
 * 
 *
 * @author Manuel Becerra
 * created 08/10/2014
 * 
 * @ORM\Table(name="estado_cliente")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionEstado", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class EstadoCliente extends BaseAuditoria implements BaseAuditable {

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
     * @var \ADIF\ComprasBundle\Entity\TipoImportancia
     *
     * @ORM\ManyToOne(targetEntity="TipoImportancia")
     * @ORM\JoinColumn(name="id_tipo_importancia", referencedColumnName="id")
     * 
     */
    protected $tipoImportancia;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_editable", type="boolean", nullable=false)
     */
    private $esEditable;

    /**
     * @ORM\OneToMany(targetEntity="Cliente", mappedBy="estadoCliente")
     */
    protected $clientes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
        $this->clientes = new ArrayCollection();
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
     * @return EstadoCliente
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
     * @return EstadoCliente
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
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoCliente
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
     * Set tipoImportancia
     *
     * @param \ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia
     * @return EstadoCliente
     */
    public function setTipoImportancia(\ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia = null) {
        $this->tipoImportancia = $tipoImportancia;

        return $this;
    }

    /**
     * Get tipoImportancia
     *
     * @return \ADIF\ComprasBundle\Entity\TipoImportancia 
     */
    public function getTipoImportancia() {
        return $this->tipoImportancia;
    }

    /**
     * Add clientes
     *
     * @param \ADIF\ComprasBundle\Entity\Cliente $clientes
     * @return EstadoCliente
     */
    public function addCliente(\ADIF\ComprasBundle\Entity\Cliente $clientes) {
        $this->clientes[] = $clientes;

        return $this;
    }

    /**
     * Remove clientes
     *
     * @param \ADIF\ComprasBundle\Entity\Cliente $clientes
     */
    public function removeCliente(\ADIF\ComprasBundle\Entity\Cliente $clientes) {
        $this->clientes->removeElement($clientes);
    }

    /**
     * Get clientes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClientes() {
        return $this->clientes;
    }

}
