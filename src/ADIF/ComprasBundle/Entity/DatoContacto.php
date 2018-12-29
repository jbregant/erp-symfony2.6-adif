<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DatoContacto.
 *
 * @author Manuel Becerra
 * created 10/07/2014
 * 
 * @ORM\Table(name="dato_contacto")
 * @ORM\Entity
 */
class DatoContacto extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\TipoContacto
     *
     * @ORM\ManyToOne(targetEntity="TipoContacto")
     * @ORM\JoinColumn(name="id_tipo_contacto", referencedColumnName="id", nullable=false)
     * 
     */
    protected $tipoContacto;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionDatoContacto;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ContactoProveedor", mappedBy="datosContacto", cascade={"persist", "remove"})
     */
    protected $contactosProveedor;

    /**
     * Constructor
     */
    public function __construct() {
        $this->contactosProveedor = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->descripcionDatoContacto;
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
     * Set tipoContacto
     *
     * @param \ADIF\ComprasBundle\Entity\TipoContacto $tipoContacto
     * @return DatoContacto
     */
    public function setTipoContacto(\ADIF\ComprasBundle\Entity\TipoContacto $tipoContacto = null) {
        $this->tipoContacto = $tipoContacto;

        return $this;
    }

    /**
     * Get tipoContacto
     *
     * @return \ADIF\ComprasBundle\Entity\TipoContacto 
     */
    public function getTipoContacto() {
        return $this->tipoContacto;
    }

    /**
     * Set descripcionDatoContacto
     *
     * @param string $descripcionDatoContacto
     * @return DatoContacto
     */
    public function setDescripcionDatoContacto($descripcionDatoContacto) {
        $this->descripcionDatoContacto = $descripcionDatoContacto;

        return $this;
    }

    /**
     * Get descripcionDatoContacto
     *
     * @return string 
     */
    public function getDescripcionDatoContacto() {
        return $this->descripcionDatoContacto;
    }

    /**
     * Add contactosProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ContactoProveedor $contactoProveedor
     * @return DatoContacto
     */
    public function addContactoProveedor(ContactoProveedor $contactoProveedor) {
        $this->contactosProveedor[] = $contactoProveedor;

        return $this;
    }

    /**
     * Remove contactosProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ContactoProveedor $contactoProveedor
     */
    public function removeContactoProveedor(ContactoProveedor $contactoProveedor) {
        $this->contactosProveedor->removeElement($contactoProveedor);
    }

    /**
     * Get contactosProveedor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactosProveedor() {
        return $this->contactosProveedor;
    }

}
