<?php

namespace ADIF\AutenticacionBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="ADIF\AutenticacionBundle\Repository\UsuarioRepository")
 */
class Usuario extends BaseUser implements BaseAuditable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255, nullable=false)
     */
    private $apellido;

    /**
     * @ORM\Column(name="id_area", type="integer", nullable=true)
     */
    protected $idArea;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Area
     */
    protected $area;

    /**
     * @ORM\ManyToMany(targetEntity="ADIF\AutenticacionBundle\Entity\Grupo", inversedBy="usuarios")
     * @ORM\JoinTable(name="usuario_grupo_empresa",
     *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_grupo", referencedColumnName="id")}
	 * )
     * @Assert\NotNull()
     * @Assert\Count(min=1, minMessage="Debe tener al menos {{ limit }} grupo asignado")
     */
    protected $groups;

	/**
     * @ORM\ManyToMany(targetEntity="ADIF\AutenticacionBundle\Entity\Empresa", inversedBy="usuarios")
     * @ORM\JoinTable(name="usuario_grupo_empresa",
     *      joinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_empresa", referencedColumnName="id")}
	 * )
     * @Assert\NotNull()
     * @Assert\Count(min=1, minMessage="Debe tener al menos {{ limit }} empresa asignada")
     */
    protected $empresas;


    public function __construct()
	{
        parent::__construct();
		$this->empresas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->username . ' (' . $this->apellido . ', ' . $this->nombre . ')';
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
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
     */
    public function setNombre($nombre)
	{
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
	{
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Usuario
     */
    public function setApellido($apellido)
	{
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
	{
        return $this->apellido;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
	{
        $this->password = $password;

        return $this;
    }

    /**
     *
     * @return type
     */
    public function getIdArea()
	{
        return $this->idArea;
    }

    /**
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Area $area
     */
    public function setArea($area)
	{

        if (null != $area) {
            $this->idArea = $area->getId();
        } //.
        else {
            $this->idArea = null;
        }

        $this->area = $area;
    }

    /**
     *
     * @return type
     */
    public function getArea()
	{
        return $this->area;
    }

    /**
     *
     * @param type $role
     * @return type
     */
    public function isGranted($role)
	{
        return in_array($role, $this->getRoles());
    }

    /**
     *
     * @return type
     */
    public function getNombreCompleto()
	{

        return $this->nombre . ' ' . $this->apellido;
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

	public function setEmpresas($empresas)
	{
		return $this;
	}

	public function getGruposPorEmpresaYUsuario($idEmpresa, $idUsuario)
	{
		$arrayGrupos = array();
		foreach($this->getEmpresas() as $empresa) {
			foreach($empresa->getGroups() as $grupo) {
				foreach($grupo->getUsuarios() as $user) {
					if ($empresa->getId() == $idEmpresa && $user->getId() == $idUsuario) {
						$arrayGrupos[] = $grupo->getName();
					}
				}
			}
		}
		return $arrayGrupos;
	}
}
