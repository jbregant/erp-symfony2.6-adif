<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;

/**
 * TipoLiquidacion
 *
 * @ORM\Table(name="tipo_liquidacion")
 * @ORM\Entity
 */
class TipoLiquidacion extends BaseEntity {
    
    const __HABITUAL = 1;
    const __ADICIONAL = 2;
    const __SAC = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    function __construct($id) {
        $this->id = $id;
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
     * @return TipoCuenta
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
    
    public function __toString() {
        if ($this->id == self::__HABITUAL){
            return 'Habitual';
        } elseif ($this->id == self::__ADICIONAL){
            return 'Adicional';
        } else {
            return 'SAC';
        }
    }

}
