<?php

namespace ADIF\ContableBundle\Entity\EgresoValor;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Comprobante;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; 

/**
 * ComprobanteEgresoValor
 * 
 * @ORM\Table(name="comprobante_egreso_valor")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteEgresoValorRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @UniqueEntity(
 *      fields={"id", "fechaComprobante", "letraComprobante", "puntoVenta", "numero", "CUIT", "tipoComprobante"},
 *      message="El número de comprobante ya se encuentra en uso.",
 *      repositoryMethod="validarNumeroComprobanteUnico",
 *      groups={"update"}
 * )
 * @UniqueEntity(
 *      fields={"fechaComprobante", "letraComprobante", "puntoVenta", "numero", "CUIT", "tipoComprobante"},
 *      message="El número de comprobante ya se encuentra en uso.",
 *      repositoryMethod="validarNumeroComprobanteUnico",
 *      groups={"create"}
 * )
 
 
 */
class ComprobanteEgresoValor extends Comprobante {

    /**
     * @var string
     *
     * @ORM\Column(name="punto_venta", type="string", length=4, nullable=true)
     */
    protected $puntoVenta;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_cupon", type="string", length=25, nullable=true)
     */
    protected $numeroCupon;

    /**
     * @var RendicionEgresoValor
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor", inversedBy="comprobantes", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_rendicion_egreso_valor", referencedColumnName="id", nullable=false)
     * })
     */
    protected $rendicionEgresoValor;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La razón social no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El CUIT no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $CUIT;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ingreso_adif", type="datetime", nullable=false)
     */
    protected $fechaIngresoADIF;

    /**
     * Set numeroCupon
     *
     * @param string $numeroCupon
     * @return ComprobanteEgresoValor
     */
    public function setNumeroCupon($numeroCupon) {
        $this->numeroCupon = $numeroCupon;

        return $this;
    }

    /**
     * Get numeroCupon
     *
     * @return string 
     */
    public function getNumeroCupon() {
        return $this->numeroCupon;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return ComprobanteEgresoValor
     */
    public function setRazonSocial($razonSocial) {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Set rendicionEgresoValor
     *
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor
     * @return ComprobanteEgresoValor
     */
    public function setRendicionEgresoValor(\ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor $rendicionEgresoValor) {
        $this->rendicionEgresoValor = $rendicionEgresoValor;

        return $this;
    }

    /**
     * Get rendicionEgresoValor
     *
     * @return \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor 
     */
    public function getRendicionEgresoValor() {
        return $this->rendicionEgresoValor;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial() {
        return $this->razonSocial;
    }

    /**
     * Set CUIT
     *
     * @param string $cUIT
     * @return ComprobanteEgresoValor
     */
    public function setCUIT($cUIT) {
        $this->CUIT = $cUIT;

        return $this;
    }

    /**
     * Get CUIT
     *
     * @return string 
     */
    public function getCUIT() {
        return $this->CUIT;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor $renglonComprobanteEgresoValor
     * @return type
     */
    public function getMontoProrrateado(RenglonComprobanteEgresoValor $renglonComprobanteEgresoValor) {

        $totalProrrateado = 0;

        // Prorateo las percepciones e impuestos
        $porcentajePercepciones = $this->getImporteTotalPercepcion() / $this->getTotalNeto();
        $totalPercepcionesProrrateado = $porcentajePercepciones * $renglonComprobanteEgresoValor->getMontoNeto();

        $porcentajeImpuestos = $this->getImporteTotalImpuesto() / $this->getTotalNeto();
        $totalImpuestosProrrateado = $porcentajeImpuestos * $renglonComprobanteEgresoValor->getMontoNeto();

        return $totalProrrateado + $totalPercepcionesProrrateado + $totalImpuestosProrrateado;
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return ComprobanteEgresoValor
     */
    public function setPuntoVenta($puntoVenta) {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return string 
     */
    public function getPuntoVenta() {
        return $this->puntoVenta;
    }

    /**
     * 
     * @return type
     */
    public function getNumeroCompleto() {

        if ($this->numeroCupon != null) {
            return $this->numeroCupon;
        }

        return $this->puntoVenta . '-' . $this->numero;
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\EgresoValor\ComprobanteEgresoValor
     */
    public function getBeneficiarioIVACompras() {
        return $this;
    }

    /**
     * Set fechaIngresoADIF
     *
     * @param \DateTime $fechaIngresoADIF
     * @return ComprobanteEgresoValor
     */
    public function setFechaIngresoADIF($fechaIngresoADIF) {
        $this->fechaIngresoADIF = $fechaIngresoADIF;

        return $this;
    }

    /**
     * Get fechaIngresoADIF
     *
     * @return \DateTime 
     */
    public function getFechaIngresoADIF() {
        return $this->fechaIngresoADIF;
    }

    /**
     * Get conceptos
     * 
     * @return type
     */
    public function getConceptosEgresoValor() {

        $conceptos = [];

        foreach ($this->renglonesComprobante as $renglonComprobante) {

            /* @var $renglonComprobante RenglonComprobanteEgresoValor */

            $concepto = $renglonComprobante->getConceptoEgresoValor();

            if (!in_array($concepto, $conceptos)) {
                $conceptos[] = $concepto;
            }
        }

        return $conceptos;
    }

    /**
     * Get CondicionImpositiva
     *
     * @return string 
     */
    public function getCondicionImpositivaIVACompras() {

        if (in_array($this->getLetraComprobante()->getLetra(), array(ConstanteLetraComprobante::A, ConstanteLetraComprobante::A_CON_LEYENDA, ConstanteLetraComprobante::B))) {
            return ConstanteTipoResponsable::INSCRIPTO;
        } else if ($this->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::C) {
            return ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO;
        }
    }

    /**
     * 
     * @return string
     */
    public function getClaseConcepto() {

        $renglonComprobante = $this->renglonesComprobante->first();

        if ($renglonComprobante != null) {
            return $renglonComprobante->getConceptoEgresoValor()->getClase();
        }

        return null;
    }

}
