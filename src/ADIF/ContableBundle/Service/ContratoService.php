<?php

namespace ADIF\ContableBundle\Service;

use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Facturacion\Contrato;
use ADIF\ContableBundle\Entity\Facturacion\ContratoVenta;
use ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteConsultoria;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion;
use ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria;
use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Vistas\VistaCuotasPorCiclos;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;

/**
 * Description of ContratoService
 */
class ContratoService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param ContratoVenta $contrato
     */
    public function getLetraComprobanteVenta(Contrato $contrato) {

        if ($contrato->getEsExportacion()) {
            $letraComprobanteVenta = ConstanteLetraComprobante::E;
        } else {
            $letraComprobanteVenta = $contrato->getCliente()->getLetraComprobanteVenta();
        }

        return $letraComprobanteVenta;
    }

    /**
     * 
     * @param type $ciclosFacturacion
     */
    public function getNumerosCuotasVentas($ciclosFacturacion) {

        $arrayNumeroCuotas = [];

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        /* @var $cicloFacturacion CicloFacturacion */
        foreach ($ciclosFacturacion as $cicloFacturacion) {
            $arrayNumeroCuotas[$cicloFacturacion->getId()] = [];
            if ($cicloFacturacion->getCantidadFacturas() != $cicloFacturacion->getCantidadFacturasPendientes()) {
                $comprobantesCiclo = $emContable->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                        ->createQueryBuilder('cc')
                        ->leftJoin('ADIFContableBundle:Facturacion\CuponVentaPlazo', 'cvp', Join::WITH, 'cc.id = cvp.id')
                        ->leftJoin('ADIFContableBundle:Facturacion\FacturaAlquiler', 'fa', Join::WITH, 'cc.id = fa.id')
                        ->leftJoin('ADIFContableBundle:Facturacion\CicloFacturacion', 'cf', Join::WITH, 'cvp.cicloFacturacion = cf.id OR fa.cicloFacturacion = cf.id')
                        ->where('cf.id = :id')
                        ->setParameter('id', $cicloFacturacion->getId())
                        ->orderBy('cc.id', 'asc')
                        ->getQuery()
                        ->getResult();
                foreach ($comprobantesCiclo as $comprobante) {
                    if (($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) || ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_CANCELADO_NC)) {
                        $arrayNumeroCuotas[$cicloFacturacion->getId()][$comprobante->getNumeroCuota()] = $comprobante->getNumeroCuota();
                    } else {
                        unset($arrayNumeroCuotas[$cicloFacturacion->getId()][$comprobante->getNumeroCuota()]);
                    }
                }
            }
        }

        return $arrayNumeroCuotas;
    }

    /**
     * 
     * @param type $ciclosFacturacion
     */
    public function getNumerosCuotasLocacionServicios($ciclosFacturacion) {

        $arrayNumeroCuotas = [];

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $ciclosFacturacion = $ciclosFacturacion->toArray();
        $ciclosFacturacion = array_values($ciclosFacturacion);

        if (count($ciclosFacturacion) > 0) {
            $contrato = $ciclosFacturacion[0]->getContrato();

            $consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($contrato->getIdConsultor());

            $monotributista = $consultor->getDatosImpositivos()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO;

            /* @var $cicloFacturacion CicloFacturacion */
            foreach ($ciclosFacturacion as $cicloFacturacion) {
                $arrayNumeroCuotas[$cicloFacturacion->getId()] = [];
                if ($cicloFacturacion->getCantidadFacturas() != $cicloFacturacion->getCantidadFacturasPendientes()) {
                    $comprobantesCiclo = $emContable->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
                            ->createQueryBuilder('cc')
                            ->leftJoin('ADIFContableBundle:Consultoria\FacturaConsultoria', 'fc', Join::WITH, 'fc.id = cc.id')
                            ->leftJoin('ADIFContableBundle:Consultoria\ReciboConsultoria', 'rc', Join::WITH, 'rc.id = cc.id')
                            ->leftJoin('ADIFContableBundle:Consultoria\RenglonComprobanteConsultoria', 'rcc', Join::WITH, '(rcc.comprobante = fc.id OR rcc.comprobante = rc.id) AND (rcc.cicloFacturacion = :id)')
                            ->setParameter('id', $cicloFacturacion->getId())
                            ->getQuery()
                            ->getResult();
                    foreach ($comprobantesCiclo as $comprobante) {
                        /* @var $comprobante ComprobanteConsultoria */
                        if (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteConsultoria::FACTURA) //
                                || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteConsultoria::RECIBO)//
                                && (($comprobante->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::C && $monotributista)//
                                || ((!$monotributista) && ( ($comprobante->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::A) || ($comprobante->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::B) ))))) {
                            if ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) {
                                foreach ($comprobante->getRenglonesComprobante() as $renglon) {
                                    $arrayNumeroCuotas[$cicloFacturacion->getId()][$renglon->getNumeroCuota()] = $renglon->getNumeroCuota();
                                }
                            } else {
                                /* @var $renglon \ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria */
                                foreach ($comprobante->getRenglonesComprobante() as $renglon) {
                                    if ($renglon->getCancelado()) {
                                        $arrayNumeroCuotas[$cicloFacturacion->getId()][$renglon->getNumeroCuota()] = $renglon->getNumeroCuota();
                                    } else {
                                        unset($arrayNumeroCuotas[$cicloFacturacion->getId()][$renglon->getNumeroCuota()]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrayNumeroCuotas;
    }

    /**
     * 
     * @param type $ciclosFacturacion
     */
    public function getCuotasPorCiclos() {

        $arrayNumeroCuotas = [];

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $ciclosFacturacion = $emContable->getRepository('ADIFContableBundle:Vistas\VistaCuotasPorCiclos')->findAll();

        /* @var $cicloFacturacion VistaCuotasPorCiclos */
        foreach ($ciclosFacturacion as $cicloFacturacion) {
            if (!isset($arrayNumeroCuotas[$cicloFacturacion->getIdCiclo()])) {
                $arrayNumeroCuotas[$cicloFacturacion->getIdCiclo()] = [];
            }
            $arrayNumeroCuotas[$cicloFacturacion->getIdCiclo()][$cicloFacturacion->getNumeroCuota()] = $cicloFacturacion->getNumeroCuota();
        }
        return $arrayNumeroCuotas;
    }
    
    /**
     * Devuelve los ciclos de facturacion pendientes de un consultor
     * @param int $idConsultor
     * @return array
     */
    public function getCiclosFacturacionPendientesByIdConsultor($idConsultor) 
    {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        
        $contratos = $emContable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstadosAndIdConsultor(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO), $idConsultor);

        $fechaVencimiento = new \DateTime(date((new \DateTime())->format('Y-m-t')));

        $ciclosFacturacion = array();

        // Por cada contrato del Consultor
        foreach ($contratos as $contrato) {

            /* @var $contrato ContratoConsultoria */

            $ciclosFacturacionPendientes = $contrato->getCiclosFacturacionPendientes();

            $numerosCuotasCanceladas = $this->getNumerosCuotasLocacionServicios($ciclosFacturacionPendientes);

            foreach ($ciclosFacturacionPendientes as $cicloFacturacion) {

                /* @var $cicloFacturacion CicloFacturacion */

                $mesSiguienteFactura = $cicloFacturacion->getFechaInicio()->format('m') + ($cicloFacturacion->getCantidadFacturasEmitidas() * $cicloFacturacion->getCantidadUnidadTiempo() * $cicloFacturacion->getUnidadTiempo()->getCantidadMeses());
                
                if ($mesSiguienteFactura > 12) {
                    $anio = strval(intval($cicloFacturacion->getFechaInicio()->format('Y')) + floor($mesSiguienteFactura / 12)) . '-';
                    $mesSiguienteFactura = $mesSiguienteFactura % 12;
                } else {
                    $anio = $cicloFacturacion->getFechaInicio()->format('Y-');
                }
                $fechaLimiteFactura = new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime($anio . $mesSiguienteFactura . '-01 00:00:00'))->format("Y-m-t H:i:s"))));
                $fechaLimiteFacturaAnio = $fechaLimiteFactura->format('Y');
                $fechaLimiteCiclo = $cicloFacturacion->getFechaFin() > $fechaVencimiento ? $fechaVencimiento : $cicloFacturacion->getFechaFin();
                
                while (
                $cicloFacturacion->getCantidadFacturasPendientes() > 0 &&
                $fechaLimiteFactura <= $fechaLimiteCiclo) {

                    //chequeo si tenia canceladas
                    if ((isset($numerosCuotasCanceladas[$cicloFacturacion->getId()])) && (count($numerosCuotasCanceladas[$cicloFacturacion->getId()]) > 0)) {
                        $index = array_keys($numerosCuotasCanceladas[$cicloFacturacion->getId()])[0];
                        $mesSiguienteFactura = $numerosCuotasCanceladas[$cicloFacturacion->getId()][$index];
                        unset($numerosCuotasCanceladas[$cicloFacturacion->getId()][$index]);
                    } else {
                        $mesSiguienteFactura = $cicloFacturacion->getFechaInicio()->format('m') + ($cicloFacturacion->getCantidadFacturasEmitidas() * $cicloFacturacion->getCantidadUnidadTiempo() * $cicloFacturacion->getUnidadTiempo()->getCantidadMeses());
                    }
                    $numeroCuotaAGenerar = $mesSiguienteFactura;
                    if ($mesSiguienteFactura > 12) {
                        $mesSiguienteFactura = $mesSiguienteFactura % 12;
                    }


                    //Decremento la cantidad de facturas pendientes del ciclo de facturacion
                    $cicloFacturacion->setCantidadFacturasPendientes($cicloFacturacion->getCantidadFacturasPendientes() - 1);

                    setlocale(LC_ALL,"es_AR.UTF-8");
                    $mes = ucfirst(strftime('%B', mktime(0, 0, 0, $mesSiguienteFactura)));

                    // Agrego CicloFacturacion a Array
                    $ciclosFacturacion[] = array(
                        'idContrato' => $contrato->getId(),
                        'idCicloFacturacion' => $cicloFacturacion->getId(),
                        'nroContrato' => $contrato->getNumeroContrato(),
                        'gerencia' => $contrato->getGerencia()->getNombre(),
                        'area' => $contrato->getArea()->getNombre(),
                        'mes' => $mes,
                        'anio' => $fechaLimiteFacturaAnio,
                        'importe' => $cicloFacturacion->getImporte(),
                        'numeroCuota' => $numeroCuotaAGenerar
                    );
                    $mesSiguienteFactura = $cicloFacturacion->getFechaInicio()->format('m') + ($cicloFacturacion->getCantidadFacturasEmitidas() * $cicloFacturacion->getCantidadUnidadTiempo() * $cicloFacturacion->getUnidadTiempo()->getCantidadMeses());


                    if ($mesSiguienteFactura > 12) {
                        $anio = strval(intval($cicloFacturacion->getFechaInicio()->format('Y')) + floor($mesSiguienteFactura / 12)) . '-';
                        $mesSiguienteFactura = $mesSiguienteFactura % 12;
                    } else {
                        $anio = $cicloFacturacion->getFechaInicio()->format('Y-');
                    }


                    $fechaLimiteFactura = new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime($anio . $mesSiguienteFactura . '-01 00:00:00'))->format("Y-m-t H:i:s"))));
                }
            }
        }
        //var_dump($ciclosFacturacion);exit;
        return $ciclosFacturacion;
    }

}
