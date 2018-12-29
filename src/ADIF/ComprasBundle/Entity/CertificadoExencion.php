<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of CertificadoExencion
 *
 * @author Manuel Becerra
 * created 23/09/2014
 * 
 * @ORM\Table(name="certificado_exencion")
 * @ORM\Entity
 */
class CertificadoExencion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="numero_certificado", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número del certificado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroCertificado;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_regimen", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="El régimen no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $tipoRegimen;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=false)
     */
    protected $fechaDesde;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=false)
     */
    protected $fechaHasta;

    /**
     * @var float
     * 
     * @ORM\Column(name="porcentaje_exencion", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El porcentaje debe ser de tipo numérico.")
     */
    protected $porcentajeExencion;

    /**
     * @ORM\OneToOne(targetEntity="AdjuntoExencion", mappedBy="certificadoExencion", cascade={"persist", "remove"})
     */
    protected $adjunto;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set numeroCertificado
     *
     * @param string $numeroCertificado
     * @return CertificadoExencion
     */
    public function setNumeroCertificado($numeroCertificado) {
        $this->numeroCertificado = $numeroCertificado;

        return $this;
    }

    /**
     * Get numeroCertificado
     *
     * @return string 
     */
    public function getNumeroCertificado() {
        return $this->numeroCertificado;
    }

    /**
     * Set tipoRegimen
     *
     * @param string $tipoRegimen
     * @return CertificadoExencion
     */
    public function setTipoRegimen($tipoRegimen) {
        $this->tipoRegimen = $tipoRegimen;

        return $this;
    }

    /**
     * Get tipoRegimen
     *
     * @return string 
     */
    public function getTipoRegimen() {
        return $this->tipoRegimen;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return CertificadoExencion
     */
    public function setFechaDesde($fechaDesde) {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde() {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return CertificadoExencion
     */
    public function setFechaHasta($fechaHasta) {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta() {
        return $this->fechaHasta;
    }

    /**
     * Set porcentajeExencion
     *
     * @param float $porcentajeExencion
     * @return CertificadoExencion
     */
    public function setPorcentajeExencion($porcentajeExencion) {
        $this->porcentajeExencion = $porcentajeExencion;

        return $this;
    }

    /**
     * Get porcentajeExencion
     *
     * @return float 
     */
    public function getPorcentajeExencion() {
        return $this->porcentajeExencion;
    }

    /**
     * Set adjunto
     *
     * @param \ADIF\ComprasBundle\Entity\AdjuntoExencion $adjunto
     * @return CertificadoExencion
     */
    public function setAdjunto(\ADIF\ComprasBundle\Entity\AdjuntoExencion $adjunto = null) {
        $this->adjunto = $adjunto;

        return $this;
    }

    /**
     * Get adjunto
     *
     * @return \ADIF\ComprasBundle\Entity\AdjuntoExencion 
     */
    public function getAdjunto() {
        return $this->adjunto;
    }

}
