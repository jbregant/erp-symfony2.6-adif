<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProveedorSaldoSuss
 *
 * @ORM\Table(name="proveedor_saldo_suss")
 * @ORM\Entity
 */
class ProveedorSaldoSuss
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
     * @ORM\Column(name="id_proveedor", type="integer", nullable=true)
     */
    private $idProveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="saldo_a_favor", type="decimal")
     */
    private $saldoAFavor;
	
	/**
	* @var ADIF\ComprasBundle\Entity\Proveedor
	*/
	private $proveedor;


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
     * Set integer
     *
     * @param string $proveedor
     * @return ProveedorSaldoSuss
     */
    public function setIdProveedor($idProveedor)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get integer
     *
     * @return string 
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }

    /**
     * Set saldoAFavor
     *
     * @param string $saldoAFavor
     * @return ProveedorSaldoSuss
     */
    public function setSaldoAFavor($saldoAFavor)
    {
        $this->saldoAFavor = $saldoAFavor;

        return $this;
    }

    /**
     * Get saldoAFavor
     *
     * @return string 
     */
    public function getSaldoAFavor()
    {
        return $this->saldoAFavor;
    }
	
	 /**
     * Set proveedor
     *
     * @param string $proveedor
     * @return ProveedorSaldoSuss
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return string 
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }
}
