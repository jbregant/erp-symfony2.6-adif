<?php

namespace ADIF\AutenticacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Grupo
 *
 * @ORM\Table(name="grupo")
 * @ORM\Entity
 * @UniqueEntity(fields="name", message="El nombre del grupo ya está en uso.")
 */
class Grupo extends BaseGroup {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="groups")
     * 
     */
    protected $usuarios;
	
	/**
     * @ORM\ManyToMany(targetEntity="Empresa", mappedBy="groups")
     * 
     */
    protected $empresas;

    /**
     * Constructor
     */
    public function __construct() 
	{
        parent::__construct(null);
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
		$this->empresas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * To String
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Grupo
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
     * Add usuarios
     *
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuarios
     * @return Grupo
     */
    public function addUsuario(\ADIF\AutenticacionBundle\Entity\Usuario $usuarios) {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\ADIF\AutenticacionBundle\Entity\Usuario $usuarios) {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsuarios() {
        return $this->usuarios;
    }
	
	public function getEmpresas()
	{
		return $this->empresas;
	}
	
	public function addEmpresa($empresa)
	{
		$this->empresas->add($empresa);
		
		return $this;
	}
	
	public function removeEmpresa($empresa)
	{
		$this->empresas->removeElement($empresa);
		
		return $this;
	}

}
