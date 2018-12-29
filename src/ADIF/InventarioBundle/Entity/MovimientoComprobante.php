<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use ADIF\InventarioBundle\Entity\MovimientoMaterial;

/**
 * MovimientoComprobante
 *
 * @ORM\Table(name="movimiento_comprobante")
 * @ORM\Entity
 */
class MovimientoComprobante
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
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="MovimientoMaterial")
     * @ORM\JoinColumn(name="id_movimiento", referencedColumnName="id", nullable=true)
     */
    private $movimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="comprobante", type="string", length=100)
     */
    private $comprobante;


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
     * Set Id Movimiento
     *
     * @param \ADIF\InventarioBundle\Entity\MovimientoMaterial $movimiento
     * @return MovimientoInventario
     */
    public function setMovimiento(\ADIF\InventarioBundle\Entity\MovimientoMaterial $id)
    {
        $this->movimiento = $id;
        return $this;
    }

    /**
     * Get Id Movimiento
     *
     * @return \ADIF\InventarioBundle\Entity\MovimientoMaterial
     */
    public function getMovimiento()
    {
        return $this->movimiento;
    }

    /**
     * Set comprobante
     *
     * @param string $comprobante
     * @return MovimientoComprobante
     */
    public function setComprobante($comprobante)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return string
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
}
