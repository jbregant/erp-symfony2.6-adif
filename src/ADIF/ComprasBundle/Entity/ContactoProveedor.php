<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactoProveedor
 * 
 * @author Manuel Becerra
 * created 11/07/2014
 * 
 * @ORM\Table(name="contacto_proveedor")
 * @ORM\Entity
 */
class ContactoProveedor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="contactosProveedor")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     * 
     */
    protected $proveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El nombre del contacto no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="cargo", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El cargo del contacto no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $cargo;

    /**
     * @ORM\ManyToMany(targetEntity="DatoContacto", inversedBy="contactosProveedor", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="contacto_proveedor_dato_contacto",
     *      joinColumns={@ORM\JoinColumn(name="id_contacto_proveedor", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_dato_contacto", referencedColumnName="id")}
     *      )
     * */
    protected $datosContacto;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * Constructor
     */
    public function __construct() {
        $this->datosContacto = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->nombre;
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
     * Set nombre
     *
     * @param string $nombre
     * @return ContactoProveedor
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set cargo
     *
     * @param string $cargo
     * @return ContactoProveedor
     */
    public function setCargo($cargo) {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return string 
     */
    public function getCargo() {
        return $this->cargo;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return ContactoProveedor
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     * @return ContactoProveedor
     */
    public function setProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedor = null) {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Add datosContacto
     *
     * @param \ADIF\ComprasBundle\Entity\DatoContacto $datoContacto
     * @return ContactoProveedor
     */
    public function addDatosContacto(DatoContacto $datoContacto) {
        $this->datosContacto[] = $datoContacto;

        return $this;
    }

    /**
     * Remove datosContacto
     *
     * @param \ADIF\ComprasBundle\Entity\DatoContacto $datoContacto
     */
    public function removeDatosContacto(DatoContacto $datoContacto) {
        $this->datosContacto->removeElement($datoContacto);
    }

    /**
     * Get datosContacto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDatosContacto() {
        return $this->datosContacto;
    }

}
