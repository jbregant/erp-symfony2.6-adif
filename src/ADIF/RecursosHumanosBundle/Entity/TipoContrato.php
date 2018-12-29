<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;

/**
 * TipoContrato
 *
 * @ORM\Table(name="tipo_contrato")
 * @ORM\Entity
 */
class TipoContrato extends BaseEntity {

    const __PLAZO_FIJO_CON_CONVENIO = 1;
    const __PLAZO_FIJO_SIN_CONVENIO = 2;
    const __TIEMPO_INDETERMINADO = 3;
    const __NUEVO_PERIODO_PRUEBA = 4;

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
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", length=3, nullable=false)
     */
    private $codigo;

//    /**
//     *
//     * @var type ADIF\RecursosHumanosBundle\Entity\EmpleadoTipoContrato
//     * 
//     * @ORM\OneToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\EmpleadoTipoContrato", mappedBy="tipoContrato")
//     */
//    private $empleados;

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
     * @return TipoContrato
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

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return TipoContrato
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getCodigo() . ' - ' . $this->getNombre();
    }

}
