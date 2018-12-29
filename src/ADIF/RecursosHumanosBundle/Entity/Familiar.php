<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Familiar
 *
 * @ORM\Table(name="familiar", indexes={@ORM\Index(name="persona_1", columns={"id_persona"}), @ORM\Index(name="empleado", columns={"id_empleado"}), @ORM\Index(name="tipo_relacion", columns={"id_tipo_relacion"})})
 * @ORM\Entity
 */
class Familiar {

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
     * @ORM\Column(name="escolaridad", type="string", length=255, nullable=true)
     */
    private $escolaridad;

    /**
     * @var string
     *
     * @ORM\Column(name="anio_cursa", type="string", length=255, nullable=true)
     */
    private $anioCursa;

    /**
     * @var boolean
     *
     * @ORM\Column(name="en_guarderia", type="boolean", nullable=true)
     */
    private $enGuarderia;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="familiares")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id", nullable=false)
     * })
     */
    private $idEmpleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Persona
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_persona", referencedColumnName="id", nullable=false)
     * })
     */
    private $persona;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoRelacion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoRelacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_relacion", referencedColumnName="id", nullable=false)
     * })
     */
    private $idTipoRelacion;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="a_cargo_os", type="boolean", nullable=true)
     */
    private $aCargoOS;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set escolaridad
     *
     * @param string $escolaridad
     * @return Familiar
     */
    public function setEscolaridad($escolaridad) {
        $this->escolaridad = $escolaridad;

        return $this;
    }

    /**
     * Get escolaridad
     *
     * @return string 
     */
    public function getEscolaridad() {
        return $this->escolaridad;
    }

    /**
     * Set anioCursa
     *
     * @param string $anioCursa
     * @return Familiar
     */
    public function setAnioCursa($anioCursa) {
        $this->anioCursa = $anioCursa;

        return $this;
    }

    /**
     * Get anioCursa
     *
     * @return string 
     */
    public function getAnioCursa() {
        return $this->anioCursa;
    }

    /**
     * Set enGuarderia
     *
     * @param boolean $enGuarderia
     * @return Familiar
     */
    public function setEnGuarderia($enGuarderia) {
        $this->enGuarderia = $enGuarderia;

        return $this;
    }

    /**
     * Get enGuarderia
     *
     * @return boolean 
     */
    public function getEnGuarderia() {
        return $this->enGuarderia;
    }

    /**
     * Set idEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado
     * @return Familiar
     */
    public function setIdEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado = null) {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getIdEmpleado() {
        return $this->idEmpleado;
    }

    /**
     * Set persona
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Persona $idPersona
     * @return Familiar
     */
    public function setPersona(\ADIF\RecursosHumanosBundle\Entity\Persona $persona = null) {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Persona 
     */
    public function getPersona() {
        return $this->persona;
    }

    /**
     * Set idTipoRelacion
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoRelacion $idTipoRelacion
     * @return Familiar
     */
    public function setIdTipoRelacion(\ADIF\RecursosHumanosBundle\Entity\TipoRelacion $idTipoRelacion = null) {
        $this->idTipoRelacion = $idTipoRelacion;

        return $this;
    }

    /**
     * Get idTipoRelacion
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoRelacion 
     */
    public function getIdTipoRelacion() {
        return $this->idTipoRelacion;
    }

    /**
     * To String
     * 
     * @return string
     */
    public function __toString() {
        return $this->getPersona()->getNombre();
    }
    
    /**
     * Set aCargoOS
     *
     * @param boolean $aCargoOS
     * @return Familiar
     */
    public function setACargoOS($aCargoOS) {
        $this->aCargoOS = $aCargoOS;

        return $this;
    }

    /**
     * Get aCargoOS
     *
     * @return boolean 
     */
    public function getACargoOS() {
        return $this->aCargoOS;
    }
    
    public function getTipoRelacion() {
        return $this->getIdTipoRelacion();
    }

}
