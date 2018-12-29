<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RenglonComprobanteCompraCentroDeCosto
 * 
 * @ORM\Table(name="renglon_comprobante_compra_centro_de_costo")
 * @ORM\Entity
 */
class RenglonComprobanteCompraCentroDeCosto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \ADIF\ContableBundle\Entity\RenglonComprobanteCompra
     *
     * @ORM\ManyToOne(targetEntity="RenglonComprobanteCompra", inversedBy="renglonComprobanteCompraCentrosDeCosto")
     * @ORM\JoinColumn(name="id_renglon_comprobante_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $renglonComprobanteCompra;

    /**
     * @var \ADIF\ContableBundle\Entity\CentroCosto
     *
     * @ORM\ManyToOne(targetEntity="CentroCosto")
     * @ORM\JoinColumn(name="id_centro_costo", referencedColumnName="id", nullable=false)
     * 
     */
    protected $centroDeCosto;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="decimal", precision=10, scale=2, nullable=false, options={"default": 0})
     */
    protected $porcentaje;


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
     * Set porcentaje
     *
     * @param string $porcentaje
     * @return RenglonComprobanteCompraCentroDeCosto
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return string 
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set renglonComprobanteCompra
     *
     * @param \ADIF\ContableBundle\Entity\RenglonComprobanteCompra $renglonComprobanteCompra
     * @return RenglonComprobanteCompraCentroDeCosto
     */
    public function setRenglonComprobanteCompra(\ADIF\ContableBundle\Entity\RenglonComprobanteCompra $renglonComprobanteCompra)
    {
        $this->renglonComprobanteCompra = $renglonComprobanteCompra;

        return $this;
    }

    /**
     * Get renglonComprobanteCompra
     *
     * @return \ADIF\ContableBundle\Entity\RenglonComprobanteCompra 
     */
    public function getRenglonComprobanteCompra()
    {
        return $this->renglonComprobanteCompra;
    }

    /**
     * Set centroDeCosto
     *
     * @param \ADIF\ContableBundle\Entity\CentroCosto $centroDeCosto
     * @return RenglonComprobanteCompraCentroDeCosto
     */
    public function setCentroDeCosto(\ADIF\ContableBundle\Entity\CentroCosto $centroDeCosto)
    {
        $this->centroDeCosto = $centroDeCosto;

        return $this;
    }

    /**
     * Get centroDeCosto
     *
     * @return \ADIF\ContableBundle\Entity\CentroCosto 
     */
    public function getCentroDeCosto()
    {
        return $this->centroDeCosto;
    }
}
