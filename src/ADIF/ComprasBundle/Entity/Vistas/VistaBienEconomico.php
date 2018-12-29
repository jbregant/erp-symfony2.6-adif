<?php

namespace ADIF\ComprasBundle\Entity\Vistas;

use Doctrine\ORM\Mapping as ORM;

/**
 * VistaBienEconomico 
 * 
 * @ORM\Table(name="vistabieneconomico")
 * @ORM\Entity
 */
class VistaBienEconomico {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esProducto", type="boolean", nullable=true)
     */
    protected $esProducto;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacionBienEconomico", type="string", nullable=true)
     */
    protected $denominacionBienEconomico;

    /**
     * @var string
     *
     * @ORM\Column(name="rubro", type="string", nullable=true)
     */
    protected $rubro;

    /**
     * @var boolean
     *
     * @ORM\Column(name="requiereEspecificacionTecnica", type="boolean", nullable=true)
     */
    protected $requiereEspecificacionTecnica;

    /**
     * @var string
     *
     * @ORM\Column(name="aliasTipoImportancia", type="string", nullable=true)
     */
    protected $aliasTipoImportancia;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoBienEconomico", type="string", nullable=true)
     */
    protected $estadoBienEconomico;

    /**
     * @var string
     *
     * @ORM\Column(name="regimenSUSS", type="string", nullable=true)
     */
    protected $regimenSUSS;

    /**
     * @var string
     *
     * @ORM\Column(name="regimenIVA", type="string", nullable=true)
     */
    protected $regimenIVA;

    /**
     * @var string
     *
     * @ORM\Column(name="regimenIIBB", type="string", nullable=true)
     */
    protected $regimenIIBB;

    /**
     * @var string
     *
     * @ORM\Column(name="regimenGanancias", type="string", nullable=true)
     */
    protected $regimenGanancias;

    /**
     * @var string
     *
     * @ORM\Column(name="cuentaContable", type="string", nullable=true)
     */
    protected $cuentaContable;

    /**
     * Set id
     *
     * @param integer $id
     * @return VistaBienEconomico
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
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
     * Get codigoBienEconomico
     *
     * @return string 
     */
    public function getCodigoBienEconomico() {
        return str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Set esProducto
     *
     * @param boolean $esProducto
     * @return VistaBienEconomico
     */
    public function setEsProducto($esProducto) {
        $this->esProducto = $esProducto;

        return $this;
    }

    /**
     * Get esProducto
     *
     * @return boolean 
     */
    public function getEsProducto() {
        return $this->esProducto;
    }

    /**
     * Set denominacionBienEconomico
     *
     * @param string $denominacionBienEconomico
     * @return VistaBienEconomico
     */
    public function setDenominacionBienEconomico($denominacionBienEconomico) {
        $this->denominacionBienEconomico = $denominacionBienEconomico;

        return $this;
    }

    /**
     * Get denominacionBienEconomico
     *
     * @return string 
     */
    public function getDenominacionBienEconomico() {
        return $this->denominacionBienEconomico;
    }

    /**
     * Set rubro
     *
     * @param string $rubro
     * @return VistaBienEconomico
     */
    public function setRubro($rubro) {
        $this->rubro = $rubro;

        return $this;
    }

    /**
     * Get rubro
     *
     * @return string 
     */
    public function getRubro() {
        return $this->rubro;
    }

    /**
     * Set requiereEspecificacionTecnica
     *
     * @param boolean $requiereEspecificacionTecnica
     * @return VistaBienEconomico
     */
    public function setRequiereEspecificacionTecnica($requiereEspecificacionTecnica) {
        $this->requiereEspecificacionTecnica = $requiereEspecificacionTecnica;

        return $this;
    }

    /**
     * Get requiereEspecificacionTecnica
     *
     * @return boolean 
     */
    public function getRequiereEspecificacionTecnica() {
        return $this->requiereEspecificacionTecnica;
    }

    /**
     * Set aliasTipoImportancia
     *
     * @param string $aliasTipoImportancia
     * @return VistaBienEconomico
     */
    public function setAliasTipoImportancia($aliasTipoImportancia) {
        $this->aliasTipoImportancia = $aliasTipoImportancia;

        return $this;
    }

    /**
     * Get aliasTipoImportancia
     *
     * @return string 
     */
    public function getAliasTipoImportancia() {
        return $this->aliasTipoImportancia;
    }

    /**
     * Set estadoBienEconomico
     *
     * @param string $estadoBienEconomico
     * @return VistaBienEconomico
     */
    public function setEstadoBienEconomico($estadoBienEconomico) {
        $this->estadoBienEconomico = $estadoBienEconomico;

        return $this;
    }

    /**
     * Get estadoBienEconomico
     *
     * @return string 
     */
    public function getEstadoBienEconomico() {
        return $this->estadoBienEconomico;
    }

    /**
     * Set regimenSUSS
     *
     * @param string $regimenSUSS
     * @return VistaBienEconomico
     */
    public function setRegimenSUSS($regimenSUSS) {
        $this->regimenSUSS = $regimenSUSS;

        return $this;
    }

    /**
     * Get regimenSUSS
     *
     * @return string 
     */
    public function getRegimenSUSS() {
        return $this->regimenSUSS;
    }

    /**
     * Set regimenIVA
     *
     * @param string $regimenIVA
     * @return VistaBienEconomico
     */
    public function setRegimenIVA($regimenIVA) {
        $this->regimenIVA = $regimenIVA;

        return $this;
    }

    /**
     * Get regimenIVA
     *
     * @return string 
     */
    public function getRegimenIVA() {
        return $this->regimenIVA;
    }

    /**
     * Set regimenIIBB
     *
     * @param string $regimenIIBB
     * @return VistaBienEconomico
     */
    public function setRegimenIIBB($regimenIIBB) {
        $this->regimenIIBB = $regimenIIBB;

        return $this;
    }

    /**
     * Get regimenIIBB
     *
     * @return string 
     */
    public function getRegimenIIBB() {
        return $this->regimenIIBB;
    }

    /**
     * Set regimenGanancias
     *
     * @param string $regimenGanancias
     * @return VistaBienEconomico
     */
    public function setRegimenGanancias($regimenGanancias) {
        $this->regimenGanancias = $regimenGanancias;

        return $this;
    }

    /**
     * Get regimenGanancias
     *
     * @return string 
     */
    public function getRegimenGanancias() {
        return $this->regimenGanancias;
    }

    /**
     * Set cuentaContable
     *
     * @param string $cuentaContable
     * @return VistaBienEconomico
     */
    public function setCuentaContable($cuentaContable) {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return string 
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

}
