<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoProveedor 
 * 
 * Indica el estado del Proveedor. 
 * 
 * Por ejemplo:
 *      Activo.
 *      Inactivo.
 *      Suspendido.
 * 
 *
 * @author Manuel Becerra
 * created 08/07/2014
 * 
 * @ORM\Table(name="estado_proveedor")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacionEstadoProveedor", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class EstadoProveedor extends BaseAuditoria implements BaseAuditable {

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
    protected $denominacionEstadoProveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstadoProveedor;

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
     * @ORM\OneToMany(targetEntity="Proveedor", mappedBy="estadoProveedor")
     */
    protected $proveedores;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esEditable = true;
        $this->proveedores = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstadoProveedor;
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
     * Set denominacionEstadoProveedor
     *
     * @param string $denominacionEstadoProveedor
     * @return EstadoProveedor
     */
    public function setDenominacionEstadoProveedor($denominacionEstadoProveedor) {
        $this->denominacionEstadoProveedor = $denominacionEstadoProveedor;

        return $this;
    }

    /**
     * Get denominacionEstadoProveedor
     *
     * @return string 
     */
    public function getDenominacionEstadoProveedor() {
        return $this->denominacionEstadoProveedor;
    }

    /**
     * Set descripcionEstadoProveedor
     *
     * @param string $descripcionEstadoProveedor
     * @return EstadoProveedor
     */
    public function setDescripcionEstadoProveedor($descripcionEstadoProveedor) {
        $this->descripcionEstadoProveedor = $descripcionEstadoProveedor;

        return $this;
    }

    /**
     * Get descripcionEstadoProveedor
     *
     * @return string 
     */
    public function getDescripcionEstadoProveedor() {
        return $this->descripcionEstadoProveedor;
    }

    /**
     * Set tipoImportancia
     *
     * @param \ADIF\ComprasBundle\Entity\TipoImportancia $tipoImportancia
     * @return EstadoBienEconomico
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
     * Set esEditable
     *
     * @param boolean $esEditable
     * @return EstadoBienEconomico
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
     * Add proveedores
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedores
     * @return EstadoProveedor
     */
    public function addProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedores) {
        $this->proveedores[] = $proveedores;

        return $this;
    }

    /**
     * Remove proveedores
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedores
     */
    public function removeProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedores) {
        $this->proveedores->removeElement($proveedores);
    }

    /**
     * Get proveedores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProveedores() {
        return $this->proveedores;
    }

}
