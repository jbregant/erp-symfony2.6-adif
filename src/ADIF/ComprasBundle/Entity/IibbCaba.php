<?php

namespace ADIF\ComprasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * IibbCaba
 *
 * @ORM\Table(name="iibb_caba")
 * @ORM\Entity
 */
class IibbCaba extends BaseAuditoria implements BaseAuditable
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
     * @var integer
     *
     * @ORM\Column(name="grupo", type="integer")
     */
    private $grupo;

    /**
     * @var string
     *
     * @ORM\Column(name="alicuota", type="decimal")
     */
    private $alicuota;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esProveedor", type="boolean")
     */
    private $esProveedor;
	
	/**
     * @ORM\OneToMany(targetEntity="Cliente", mappedBy="iibbCaba")
     */
    private $clientes;
	
	/**
     * @ORM\OneToMany(targetEntity="Proveedor", mappedBy="iibbCaba")
     */
    private $proveedores;

	
	public function __construct()
	{
		$this->clientes = new ArrayCollection();
		$this->proveedores = new ArrayCollection();
	}
	
	public function __toString()
	{
		return 'Grupo ' . $this->grupo . ' - ' . $this->alicuota . ' %';
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
     * Set grupo
     *
     * @param integer $grupo
     * @return IibbCaba
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return integer 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set alicuota
     *
     * @param string $alicuota
     * @return IibbCaba
     */
    public function setAlicuota($alicuota)
    {
        $this->alicuota = $alicuota;

        return $this;
    }

    /**
     * Get alicuota
     *
     * @return string 
     */
    public function getAlicuota()
    {
        return $this->alicuota;
    }

    /**
     * Set esProveedor
     *
     * @param boolean $esProveedor
     * @return IibbCaba
     */
    public function setEsProveedor($esProveedor)
    {
        $this->esProveedor = $esProveedor;

        return $this;
    }

    /**
     * Get esProveedor
     *
     * @return boolean 
     */
    public function getEsProveedor()
    {
        return $this->esProveedor;
    }
	
	
	public function setClientes($clientes)
	{
		$this->clientes = $clientes;
		return $this;
	}
	
	public function getClientes()
	{
		return $this->clientes;
	}
	
	public function setProveedores($proveedores)
	{
		$this->proveedores = $proveedores;
		return $this;
	}
	
	public function getProveedores()
	{
		return $this->proveedores;
	}
}
