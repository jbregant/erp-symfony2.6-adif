<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * ResponsableEgresoValor
 * 
 * @ORM\Table(name="responsable_egreso_valor")
 * @ORM\Entity
 */
class ResponsableEgresoValor extends BaseAuditoria implements BaseAuditable {

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
     * 
     */
    protected $nombre;

    /**
     * @ORM\Column(name="id_tipo_documento", type="integer", nullable=false)
     */
    protected $idTipoDocumento;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\TipoDocumento
     */
    protected $tipoDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_documento", type="string", length=255, nullable=false)
     */
    private $nroDocumento;

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->nombre . ' ' . $this->tipoDocumento . ': ' . $this->nroDocumento;
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
     * @return ResponsableEgresoValor
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
     * Set idProveedor
     *
     * @param integer $idTipoDocumento
     * @return ResponsableEgresoValor
     */
    public function setIdTipoDocumento($idTipoDocumento) {
        $this->idTipoDocumento = $idTipoDocumento;

        return $this;
    }

    /**
     * Get idTipoDocumento
     *
     * @return integer 
     */
    public function getIdTipoDocumento() {
        return $this->idTipoDocumento;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoDocumento $tipoDocumento
     */
    public function setTipoDocumento($tipoDocumento) {

        if (null != $tipoDocumento) {
            $this->idTipoDocumento = $tipoDocumento->getId();
        } else {
            $this->idTipoDocumento = null;
        }

        $this->tipoDocumento = $tipoDocumento;
    }

    /**
     * 
     * @return type
     */
    public function getTipoDocumento() {
        return $this->tipoDocumento;
    }

    /**
     * Set nroDocumento
     *
     * @param string $nroDocumento
     * @return ResponsableEgresoValor
     */
    public function setNroDocumento($nroDocumento) {
        $this->nroDocumento = $nroDocumento;

        return $this;
    }

    /**
     * Get nroDocumento
     *
     * @return string 
     */
    public function getNroDocumento() {
        return $this->nroDocumento;
    }

    /**
     * @return string 
     */
    public function getRazonSocial() {
        return $this->nombre;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilio() {
        return null;
    }

    /**
     * 
     * @return type
     */
    public function getLocalidad() {
        return null;
    }

}
