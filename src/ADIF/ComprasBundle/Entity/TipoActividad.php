<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of TipoActividad
 *
 * @author Manuel Becerra
 * created 30/09/2014
 * 
 * @ORM\Table(name="tipo_actividad")
 * @ORM\Entity
 * @UniqueEntity(
 *      fields = {"denominacion", "fechaBaja"}, 
 *      ignoreNull = false,
 *      message="La denominación ingresada ya se encuentra en uso."
 * )
 */
class TipoActividad extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ClienteProveedor", mappedBy="actividades")
     */
    protected $clienteProveedor;

    /**
     * Constructor
     */
    public function __construct() {
        $this->clienteProveedor = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacion;
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return TipoActividad
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return TipoActividad
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Add clienteProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor
     * @return TipoActividad
     */
    public function addClienteProveedor(ClienteProveedor $clienteProveedor) {
        $this->clienteProveedor[] = $clienteProveedor;

        return $this;
    }

    /**
     * Remove clienteProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ContactoProveedor $clienteProveedor
     */
    public function removeClienteProveedor(ClienteProveedor $clienteProveedor) {
        $this->clienteProveedor->removeElement($clienteProveedor);
    }

    /**
     * Get clienteProveedor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClienteProveedor() {
        return $this->clienteProveedor;
    }

}
