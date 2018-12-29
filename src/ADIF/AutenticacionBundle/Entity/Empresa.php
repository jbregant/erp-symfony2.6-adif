<?php

namespace ADIF\AutenticacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Empresa
 *
 * @ORM\Table(name="empresa")
 * @ORM\Entity
 */
class Empresa
{
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
     * @ORM\Column(name="denominacion", type="string", length=100)
     */
    private $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=50)
     */
    private $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion_larga", type="string", length=255)
     */
    private $denominacionLarga;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=50)
     */
    private $logo;

	/**
	* @ORM\OneToMany(targetEntity="WebService", mappedBy="empresa")
	*/
	private $webServices;
	
	/**
	* @ORM\ManyToMany(targetEntity="Usuario", mappedBy="empresas")
	*/
	private $usuarios;
	
	/**
     * @ORM\ManyToMany(targetEntity="Grupo", inversedBy="empresas")
     * @ORM\JoinTable(name="usuario_grupo_empresa",
     *      joinColumns={@ORM\JoinColumn(name="id_empresa", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_grupo", referencedColumnName="id")}
	 * )
     * @Assert\NotNull()
     * @Assert\Count(min=1, minMessage="Debe tener al menos {{ limit }} grupo asignado")
     */
    protected $groups;

	public function __construct()
	{
		$this->webServices = new ArrayCollection();
		$this->usuarios = new ArrayCollection();
		$this->groups = new ArrayCollection();
	}
	
	public function __toString()
	{
		$empresa = $this->denominacion;
		if ($this->cuit != null) {
			$empresa .= ' - ' . $this->cuit;
		}
		
		return $empresa;
	}
	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return Empresa
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return Empresa
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string 
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set denominacionLarga
     *
     * @param string $denominacionLarga
     * @return Empresa
     */
    public function setDenominacionLarga($denominacionLarga)
    {
        $this->denominacionLarga = $denominacionLarga;

        return $this;
    }

    /**
     * Get denominacionLarga
     *
     * @return string 
     */
    public function getDenominacionLarga()
    {
        return $this->denominacionLarga;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Empresa
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

	public function getWebServices()
	{
		return $this->webServices;
	}

	public function getUsuarios()
	{
		return $this->usuarios;
	}
	
	public function getGroups()
	{
		return $this->groups;
	}
	
	public function addGroup($group)
	{
		$this->groups->add($group);
		
		return $this;
	}
	
	public function removeGroup($group)
	{
		$this->groups->removeElement($group);
		
		return $this;
	}
}
