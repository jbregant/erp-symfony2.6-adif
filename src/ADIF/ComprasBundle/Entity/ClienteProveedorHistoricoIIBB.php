<?php

namespace ADIF\ComprasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ClienteProveedorHistoricoIIBB
 *
 * @author Manuel Becerra
 * created 03/11/2014
 * 
 * @ORM\Entity
 */
class ClienteProveedorHistoricoIIBB extends ClienteProveedorHistoricoCondicionFiscal {

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_percepcion", type="boolean", nullable=false)
     */
    protected $pasiblePercepcion;

    /**
     * @var string
     *
     * @ORM\Column(name="jurisdiccion", type="string", length=1024, nullable=true)
     * @Assert\Length(
     *      max="1024", 
     *      maxMessage="La jurisdicción no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $jurisdiccion;

    /**
     * @var float
     * 
     * @ORM\Column(name="porcentaje_aplicacion_CABA", type="float", nullable=true)
     * @Assert\Type(
     *   type="numeric",
     *   message="El porcentaje debe ser de tipo numérico.")
     */
    protected $porcentajeAplicacionCABA;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->pasiblePercepcion = false;
        $this->aplicaConvenioMultilateral = false;
    }

    /**
     * Set pasiblePercepcion
     *
     * @param boolean $pasiblePercepcion
     * @return ClienteProveedorHistoricoIIBB
     */
    public function setPasiblePercepcion($pasiblePercepcion) {
        $this->pasiblePercepcion = $pasiblePercepcion;

        return $this;
    }

    /**
     * Get pasiblePercepcion
     *
     * @return boolean 
     */
    public function getPasiblePercepcion() {
        return $this->pasiblePercepcion;
    }

    /**
     * Set jurisdiccion
     *
     * @param string $jurisdiccion
     * @return ClienteProveedorHistoricoIIBB
     */
    public function setJurisdiccion($jurisdiccion) {
        $this->jurisdiccion = $jurisdiccion;

        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return string 
     */
    public function getJurisdiccion() {
        return $this->jurisdiccion;
    }

    /**
     * Set porcentajeAplicacionCABA
     *
     * @param float $porcentajeAplicacionCABA
     * @return ClienteProveedorHistoricoIIBB
     */
    public function setPorcentajeAplicacionCABA($porcentajeAplicacionCABA) {
        $this->porcentajeAplicacionCABA = $porcentajeAplicacionCABA;

        return $this;
    }

    /**
     * Get porcentajeAplicacionCABA
     *
     * @return float 
     */
    public function getPorcentajeAplicacionCABA() {
        return $this->porcentajeAplicacionCABA;
    }

}
