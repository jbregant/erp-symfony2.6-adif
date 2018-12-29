<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of SituacionClienteProveedor
 *
 * @author Dar
 * created 08/10/2014
 * 
 * @ORM\Table(name="situacion_cliente_proveedor")
 * @ORM\Entity
 * @UniqueEntity(fields={"codigo","fechaBaja"}, ignoreNull=false, message="El código de situación ya se encuentra en uso.")
 */
class SituacionClienteProveedor extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", unique=true, nullable=false)
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aplica_impuesto_iva", type="boolean", nullable=false)
     */
    protected $aplicaImpuestoIVA;

    /**
     * Constructor
     */
    public function __construct() {
        $this->aplicaImpuestoIVA = false;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->getCodigo() . ' - ' . $this->getDescripcion();
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
     * Set codigo
     *
     * @param string $codigo
     * @return SituacionClienteProveedor
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return SituacionClienteProveedor
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set aplicaImpuestoIVA
     *
     * @param boolean $aplicaImpuestoIVA
     * @return SituacionClienteProveedor
     */
    public function setAplicaImpuestoIVA($aplicaImpuestoIVA) {
        $this->aplicaImpuestoIVA = $aplicaImpuestoIVA;

        return $this;
    }

    /**
     * Get aplicaImpuestoIVA
     *
     * @return boolean 
     */
    public function getAplicaImpuestoIVA() {
        return $this->aplicaImpuestoIVA;
    }

}
