<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FuenteFinanciamiento
 * 
 * @ORM\Table(name="fuente_financiamiento")
 * @ORM\Entity 
 */
class FuenteFinanciamiento extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El nombre no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=true)
     */
    protected $cuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="modifica_cuenta_contable", type="boolean", nullable=false)
     */
    protected $modificaCuentaContable;

    /**
     * Constructor
     */
    public function __construct() {
        $this->modificaCuentaContable = false;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getNombre();
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
     * @param integer $codigo
     * @return FuenteFinanciamiento
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
     * Set nombre
     *
     * @param string $nombre
     * @return FuenteFinanciamiento
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
     * Set cuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return FuenteFinanciamiento
     */
    public function setCuentaContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentaContable = $cuentaContable;

        return $this;
    }

    /**
     * Get cuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\CuentaContable 
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set modificaCuentaContable
     *
     * @param boolean $modificaCuentaContable
     * @return FuenteFinanciamiento
     */
    public function setModificaCuentaContable($modificaCuentaContable) {
        $this->modificaCuentaContable = $modificaCuentaContable;

        return $this;
    }

    /**
     * Get modificaCuentaContable
     *
     * @return boolean 
     */
    public function getModificaCuentaContable() {
        return $this->modificaCuentaContable;
    }

}
