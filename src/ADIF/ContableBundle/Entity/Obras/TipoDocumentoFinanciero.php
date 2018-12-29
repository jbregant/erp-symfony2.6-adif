<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TipoDocumentoFinanciero
 * 
 * @ORM\Table(name="tipo_documento_financiero")
 * @ORM\Entity 
 */
class TipoDocumentoFinanciero extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\CuentaContable")
     * @ORM\JoinColumn(name="id_cuenta_contable", referencedColumnName="id", nullable=true)
     */
    protected $cuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="imputa_cuenta_tipo_documento", type="boolean", nullable=true)
     */
    protected $imputaCuentaTipoDocumento;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_iva", referencedColumnName="id", nullable=false)
     * 
     */
    protected $regimenRetencionIVA;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_iibb", referencedColumnName="id", nullable=false)
     * 
     */
    protected $regimenRetencionIIBB;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_suss", referencedColumnName="id", nullable=false)
     * 
     */
    protected $regimenRetencionSUSS;

    /**
     * @var \ADIF\ContableBundle\Entity\RegimenRetencion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\RegimenRetencion")
     * @ORM\JoinColumn(name="id_regimen_retencion_ganancias", referencedColumnName="id", nullable=false)
     * 
     */
    protected $regimenRetencionGanancias;

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
     * Set nombre
     *
     * @param string $nombre
     * @return TipoDocumentoFinanciero
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
     * @return TipoDocumentoFinanciero
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
     * Set regimenRetencionIVA
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionIVA
     * @return TipoDocumentoFinanciero
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
     * @return TipoDocumentoFinanciero
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
     * Set regimenRetencionSUSS
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionSUSS
     * @return TipoDocumentoFinanciero
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
     * Set regimenRetencionGanancias
     *
     * @param \ADIF\ContableBundle\Entity\RegimenRetencion $regimenRetencionGanancias
     * @return TipoDocumentoFinanciero
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

    /**
     * Get regimenRetencionByImpuesto
     *
     * @return \ADIF\ContableBundle\Entity\RegimenRetencion 
     */
    public function getRegimenRetencionByImpuesto($denominacionImpuesto) {

        $regimenRetencion = null;

        switch ($denominacionImpuesto) {
            case ConstanteTipoImpuesto::IVA:
                $regimenRetencion = $this->regimenRetencionIVA;
                break;
            case ConstanteTipoImpuesto::IIBB:
                $regimenRetencion = $this->regimenRetencionIIBB;
                break;
            case ConstanteTipoImpuesto::SUSS:
                $regimenRetencion = $this->regimenRetencionSUSS;
                break;
            case ConstanteTipoImpuesto::Ganancias:
                $regimenRetencion = $this->regimenRetencionGanancias;
                break;
            default:
                break;
        }

        return $regimenRetencion;
    }

    /**
     * Set imputaCuentaTipoDocumento
     *
     * @param boolean imputaCuentaTipoDocumento
     * @return TipoDocumentoFinanciero
     */
    public function setImputaCuentaTipoDocumento($imputaCuentaTipoDocumento) {
        $this->imputaCuentaTipoDocumento = $imputaCuentaTipoDocumento;

        return $this;
    }

    /**
     * Get imputaCuentaTipoDocumento
     *
     * @return boolean 
     */
    public function getImputaCuentaTipoDocumento() {
        return $this->imputaCuentaTipoDocumento;
    }

}
