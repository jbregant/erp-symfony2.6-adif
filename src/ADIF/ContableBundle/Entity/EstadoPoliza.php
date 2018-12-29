<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EstadoPoliza
 *
 * 
 * @ORM\Table(name="estado_poliza")
 * @ORM\Entity
 */
class EstadoPoliza
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
     * @ORM\Column(name="denominacion", type="string", length=255)
     */
    private $denominacion;
	
	/**
     * @var poliza
     *
     * @ORM\OneToMany(targetEntity="PolizaSeguro", mappedBy="estadoPoliza")
     * 
     */
	protected $polizas;

	public function __construct()
	{
		$this->polizas = new ArrayCollection();
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
     * @return EstadoPoliza
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
	
    public function addPoliza(\ADIF\ContableBundle\Entity\PolizaSeguro $polizaSeguro) 
	{
        $this->polizas[] = $polizaSeguro;

        return $this;
    }

   
    public function removePoliza(\ADIF\ContableBundle\Entity\PolizaSeguro $polizaSeguro) 
	{
        $this->polizas->removeElement($polizaSeguro);
    }

   
    public function getPolizas() 
	{
        return $this->polizas;
    }
	
	public function setPolizas(\ADIF\ContableBundle\Entity\PolizaSeguro $polizaSeguro)
	{
		$this->polizas = $polizaSeguro;
		return $this;
	}
	
	public function __toString()
	{
		return $this->denominacion;
	}
}
