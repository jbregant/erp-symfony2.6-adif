<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\Cliente;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIngresosBrutosVenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoRegimenPercepcion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Facturacion\Contrato;

/**
 * Description of PercepcionesService
 */
class PercepcionesService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param Cliente $cliente
     * @param Contrato $contrato
     * @return type
     */
    public function getAlicuotaIIBB(Cliente $cliente, Contrato $contrato = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $alicuotaCorrespondiente = null;
		
		if ($cliente->getClienteProveedor()->getAplicaRgNormalmente() && $cliente->getIibbCaba() != null) {
			// Si Aplica RG Normalmente (Calificacion fiscal) y tiene asignado un grupo
			$alicuotaCorrespondiente = $cliente->getIibbCaba()->getAlicuota();
			
		} else {

			if ($cliente->getPasiblePercepcionIngresosBrutos()) {
				$porcentajeCorrespondiente = 100;

				// Si el Proveedor es Convenio Multilateral
				if ($cliente->getClienteProveedor()->getCondicionIngresosBrutos()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
					$convenioMultilateral = $cliente->getClienteProveedor()->getConvenioMultilateralIngresosBrutos();

					if ($convenioMultilateral) {
						// Aplico porcentaje CABA
						$porcentajeCorrespondiente = $convenioMultilateral->getPorcentajeAplicacionCABA();
					}
				}

				// Si el proveedor tiene riesgo fiscal
				if ($cliente->getClienteProveedor()->getTieneRiesgoFiscal()) {
					// Aplico Regimen correspondiente al Riesgo Fiscal
					$codigo_alicuota = ConstanteAlicuotaIngresosBrutosVenta::RIESGO_FISCAL;
				} else {
					// Si el Proveedor es Monotributista
					if ($cliente->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
						// Me fijo si está en la base de Magnitudes Superadas
						if ($cliente->getClienteProveedor()->getIncluyeMagnitudesSuperadas()) {
							// Aplico Regimen correspondiente a Magnitudes Superadas
							$codigo_alicuota = ConstanteAlicuotaIngresosBrutosVenta::MAGNITUDES_SUPERADAS;
						}
					} else {
						// Pregunto si es alquiler
						if ($contrato != null && $contrato->getEsContratoAlquiler()) {
							$codigo_alicuota = ConstanteAlicuotaIngresosBrutosVenta::ALQUILERES;
						} else {
							$codigo_alicuota = ConstanteAlicuotaIngresosBrutosVenta::OTROS;
						}
					}
				}
				$alicuota = $emContable->getRepository('ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta')->findOneByCodigo($codigo_alicuota);
				$alicuotaCorrespondiente = $alicuota->getValor() * $porcentajeCorrespondiente / 100;
			}
		}

        return $alicuotaCorrespondiente;
    }

    /**
     * 
     * @param Cliente $cliente
     * @param Contrato $contrato
     * @return type
     */
    public function getRegimenIIBB(Cliente $cliente, Contrato $contrato = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $regimenCorrespondiente = null;
        $codigoRegimen = null;
		
		if ($cliente->getClienteProveedor()->getAplicaRgNormalmente() && $cliente->getIibbCaba() != null) {
			// Si Aplica RG Normalmente (Calificacion fiscal) y tiene asignado un grupo
			$codigoRegimen = ConstanteCodigoRegimenPercepcion::CODIGO_IIBB_CABA;
			$regimenCorrespondiente = $emContable->getRepository('ADIFContableBundle:RegimenPercepcion')->findOneByCodigo($codigoRegimen);
			
		} else {

			if ($cliente->getPasiblePercepcionIngresosBrutos()) {
				$porcentajeCorrespondiente = 100;

				// Si el Proveedor es Convenio Multilateral
				if ($cliente->getClienteProveedor()->getCondicionIngresosBrutos()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
					$convenioMultilateral = $cliente->getClienteProveedor()->getConvenioMultilateralIngresosBrutos();

					if ($convenioMultilateral) {
						// Aplico porcentaje CABA
						$porcentajeCorrespondiente = $convenioMultilateral->getPorcentajeAplicacionCABA();
					}
				}

				// Si el proveedor tiene riesgo fiscal
				if ($cliente->getClienteProveedor()->getTieneRiesgoFiscal()) {
					// Aplico Regimen correspondiente al Riesgo Fiscal
					$codigoRegimen = ConstanteCodigoRegimenPercepcion::CODIGO_RIESGO_FISCAL;
				} else {
					// Si el Proveedor es Monotributista
					if ($cliente->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
						// Me fijo si está en la base de Magnitudes Superadas
						if ($cliente->getClienteProveedor()->getIncluyeMagnitudesSuperadas()) {
							// Aplico Regimen correspondiente a Magnitudes Superadas
							$codigoRegimen = ConstanteCodigoRegimenPercepcion::CODIGO_MAGNITUDES_SUPERADAS;
						}
					} else {
						// Pregunto si es alquiler
						if ($contrato != null && $contrato->getEsContratoAlquiler()) {
							$codigoRegimen = ConstanteCodigoRegimenPercepcion::CODIGO_ALQUILERES;
						} else {
							$codigoRegimen = ConstanteCodigoRegimenPercepcion::CODIGO_GENERAL;
						}
					}
				}
				$regimenCorrespondiente = $emContable->getRepository('ADIFContableBundle:RegimenPercepcion')->findOneByCodigo($codigoRegimen);
			}
		}

        return $regimenCorrespondiente;
    }

    /**
     * 
     * @param Cliente $cliente
     * @return type
     */
    public function getPorcentajeAlicuotaIIBB(Cliente $cliente) {

        $porcentajeCorrespondiente = 0;

        if ($cliente->getPasiblePercepcionIngresosBrutos()) {
            $porcentajeCorrespondiente = 100;
            // Si el Proveedor es Convenio Multilateral
            if ($cliente->getClienteProveedor()->getCondicionIngresosBrutos()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
                $convenioMultilateral = $cliente->getClienteProveedor()->getConvenioMultilateralIngresosBrutos();

                if ($convenioMultilateral) {
                    // Aplico porcentaje CABA
                    $porcentajeCorrespondiente = $convenioMultilateral->getPorcentajeAplicacionCABA();
                }
            }
        }

        return $porcentajeCorrespondiente;
    }

    /**
     * 
     * @param Cliente $cliente
     * @param Contrato $contrato
     * @return type
     */
    public function getRegimenIVA(Cliente $cliente, Contrato $contrato = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $regimenCorrespondiente = null;

        if ($cliente->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::SUJETO_NO_CATEGORIZADO) {

            if ($contrato == null || ($contrato != null && ($contrato->getEsContratoAlquiler() || $contrato->getEsContratoLocacionServicios()))) {
                $regimenCorrespondiente = $emContable->getRepository('ADIFContableBundle:RegimenPercepcion')
                        ->findOneByCodigo(ConstanteCodigoRegimenPercepcion::CODIGO_IVA_MUEBLES);
            }
        }

        return $regimenCorrespondiente;
    }

}
