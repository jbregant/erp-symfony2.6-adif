<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoObra
 *
 * @author Darío Rapetti
 * created 29/11/2014
 * 
 * @ORM\Table(name="obras_tipo_obra")
 * @ORM\Entity 
 */
class TipoObra extends BaseAuditoria implements BaseAuditable {

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
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El nombre no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $nombre;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_suss", referencedColumnName="id")
     * 
     */
    protected $regimenRetencionSUSS;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_iva", referencedColumnName="id")
     * 
     */
    protected $regimenRetencionIVA;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_iibb", referencedColumnName="id")
     * 
     */
    protected $regimenRetencionIIBB;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_ganancias", referencedColumnName="id")
     * 
     */
    protected $regimenRetencionGanancias;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getNombre();
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return TipoObra
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
     * Set regimenRetencionSUSS
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionSUSS
     * @return TipoObra
     */
    public function setRegimenRetencionSUSS(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionSUSS) {
        $this->regimenRetencionSUSS = $regimenRetencionSUSS;

        return $this;
    }

    /**
     * Get regimenRetencionSUSS
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionSUSS() {
        return $this->regimenRetencionSUSS;
    }

    /**
     * Set regimenRetencionIVA
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIVA
     * @return TipoObra
     */
    public function setRegimenRetencionIVA(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIVA) {
        $this->regimenRetencionIVA = $regimenRetencionIVA;

        return $this;
    }

    /**
     * Get regimenRetencionIVA
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionIVA() {
        return $this->regimenRetencionIVA;
    }

    /**
     * Set regimenRetencionIIBB
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIIBB
     * @return TipoObra
     */
    public function setRegimenRetencionIIBB(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIIBB) {
        $this->regimenRetencionIIBB = $regimenRetencionIIBB;

        return $this;
    }

    /**
     * Get regimenRetencionIIBB
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionIIBB() {
        return $this->regimenRetencionIIBB;
    }

    /**
     * Set regimenRetencionGanancias
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionGanancias
     * @return TipoObra
     */
    public function setRegimenRetencionGanancias(\ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionGanancias) {
        $this->regimenRetencionGanancias = $regimenRetencionGanancias;

        return $this;
    }

    /**
     * Get regimenRetencionGanancias
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionGanancias() {
        return $this->regimenRetencionGanancias;
    }

}
