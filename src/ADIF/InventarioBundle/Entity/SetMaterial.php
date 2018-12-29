<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * SetMaterial
 *
 * @ORM\Table(name="set_material")
 * @ORM\Entity
 */
class SetMaterial
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
     * @ORM\ManyToOne(targetEntity="CatalogoMaterialesNuevos")
     * @ORM\JoinColumn(name="id_material_nuevo_componente", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $id_material_nuevo_componente;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;



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
     * Set Id Material Nuevo Componente
     *
     * @param \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos $id_material_nuevo_componente
     * @return SetMaterial
     */
    public function setIdComponente(\ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos $id)
    {
        $this->id_material_nuevo_componente = $id;
        return $this;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return SetMaterial
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
        return $this;
    }

    /**
     * Get id Componente
     *
     * @return \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos
     */
    public function getIdComponente()
    {
        return $this->id_material_nuevo_componente;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }
}
