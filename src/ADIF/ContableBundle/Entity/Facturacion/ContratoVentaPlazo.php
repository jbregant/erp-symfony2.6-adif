<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContratoVenta
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="contrato_venta_plazo")
 * @ORM\Entity
 */
class ContratoVentaPlazo extends ContratoVenta implements BaseAuditable {

    /**
     * @var string
     *
     * @ORM\Column(name="numero_inmueble", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de inmueble no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroInmueble;

    /**
     * Set numeroInmueble
     *
     * @param string $numeroInmueble
     * @return ContratoVentaPlazo
     */
    public function setNumeroInmueble($numeroInmueble) {
        $this->numeroInmueble = $numeroInmueble;

        return $this;
    }

    /**
     * Get numeroInmueble
     *
     * @return string 
     */
    public function getNumeroInmueble() {
        return $this->numeroInmueble;
    }

    /**
     * Getter esContratoAlquiler
     * 
     * @return boolean
     */
    public function getEsContratoAlquiler() {
        return false;
    }

    /**
     * Getter esContratoVentaPlazo
     * 
     * @return boolean
     */
    public function getEsContratoVentaPlazo() {
        return true;
    }

    /**
     * Getter indicaNumeroInmueble
     * 
     * @return boolean
     */
    public function getIndicaNumeroInmueble() {
        return true;
    }

//    /**
//     * Get saldoPendienteFacturacion
//     * 
//     * @return float
//     */
//    public function getSaldoPendienteFacturacion() {
//
//        $saldoPendienteFacturacion = $this->getImporteTotal();
//
//        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {
//
//            // Si el comprobante es un Cupón
//            if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::CUPON) {
//                $saldoPendienteFacturacion -= $comprobanteVenta->getImporteTotalNeto();
//            }
//        }
//
//        return $saldoPendienteFacturacion;
//    }

    /**
     * 
     * @return type
     */
    public function getComprobantesModificanSaldo() {

        $comprobantesModificanSaldo = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {

            // Si el comprobante modifica el saldo del contrato
            if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::CUPON) {
                $comprobantesModificanSaldo[] = $comprobanteVenta;
            }
        }

        return $comprobantesModificanSaldo;
    }

    /**
     * 
     * @return type
     */
    public function getComprobantesNoModificanSaldo() {

        $comprobantesNoModificanSaldo = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {

            // Si el comprobante NO modifica el saldo del contrato
            if ($comprobanteVenta->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::CUPON) {
                $comprobantesNoModificanSaldo[] = $comprobanteVenta;
            }
        }

        return $comprobantesNoModificanSaldo;
    }

    /**
     * 
     * @return type
     */
    public function getComprobantesEnCuentaCorriente() {

        $comprobantesEnCuentaCorriente = array();

        foreach ($this->getComprobantesVenta() as $comprobanteVenta) {

            // Si el comprobante debe aparecer en la cuenta corriente
            if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::CUPON || 
                    $comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
                $comprobantesEnCuentaCorriente [] = $comprobanteVenta;
            }
        }

        return $comprobantesEnCuentaCorriente;
    }

}
