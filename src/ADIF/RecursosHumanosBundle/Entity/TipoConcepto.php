<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;

/**
 * TipoConcepto
 *
 * @ORM\Table(name="tipo_concepto")
 * @ORM\Entity
 */
class TipoConcepto extends BaseEntity
{
    
    const __REMUNERATIVO = 1;
    const __NO_REMUNERATIVO = 2;
    const __APORTE = 3;
    const __CONTRIBUCIONES = 4;
    const __DESCUENTO = 5;
    const __CUOTA_SINDICAL_APORTES = 6;
    const __CUOTA_SINDICAL_CONTRIBUCIONES = 7;
    const __CALCULO_GANANCIAS = 8;
            
    
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
     * @return TipoConcepto
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
}
