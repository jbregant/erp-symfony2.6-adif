<?php

namespace ADIF\ContableBundle\Service;

use ADIF\ContableBundle\Entity\Chequera;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoChequera;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use ADIF\ContableBundle\Entity\EstadoPagoHistorico;

/**
 * Description of ChequeraService
 *
 * @author Manuel Becerra
 * created 04/12/2014
 */
class ChequeraService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param type $emContable
     * @param Chequera $chequera
     * @return type
     */
    public function getSiguienteNumeroCheque($emContable, Chequera $chequera) {

        $siguienteNumero = $chequera->getNumeroSiguiente();

        if ($siguienteNumero >= $chequera->getNumeroFinal()) {

            $chequera->setEstadoChequera(
                    $emContable->getRepository('ADIFContableBundle:EstadoChequera')
                            ->findOneByDenominacionEstado(ConstanteEstadoChequera::ESTADO_CHEQUERA_AGOTADA)
            );
        }

        $chequera->setNumeroSiguiente($siguienteNumero + 1);

        return $siguienteNumero;
    }
    
    public function acreditarMovimiento($movimiento, $emContable, $usuario, $esConciliacion = true) {
        
        $tipoMovimiento = strtolower($movimiento->getTipo());
        $estadoPagoOriginal = $movimiento->getEstadoPago();
        if ($esConciliacion) {
            $nuevoEstadoPago = $emContable->getRepository('ADIFContableBundle:EstadoPago')->findOneByDenominacionEstado(ConstanteEstadoPago::ESTADO_ACREDITADA);
        } else {
            $repository = $emContable->getRepository('ADIFContableBundle:EstadoPagoHistorico');
            $query = $repository->createQueryBuilder('eph')
                    ->innerJoin('eph.' . $tipoMovimiento, 'c')
                    ->where('c.id = ' . $movimiento->getId())
                    ->setMaxResults(2)
                    ->orderBy('eph.fecha', 'DESC')
                    ->getQuery();
            $historicos = $query->getResult();
            $cantHistoricos = sizeOf($historicos);
            if ($cantHistoricos == 2) {
                $cual =  $historicos[$cantHistoricos-1]->getEstadoPago()->getDenominacionEstado() == ConstanteEstadoPago::ESTADO_ACREDITADA ? $cantHistoricos-2 : $cantHistoricos-1;
                $nuevoEstadoPago =  $historicos[$cual]->getEstadoPago();
            } else {
                $nuevoEstadoPago =  $cantHistoricos == 0 || $historicos[$cantHistoricos-1]->getEstadoPago() == ConstanteEstadoPago::ESTADO_ACREDITADA ? 
                        $emContable->getRepository('ADIFContableBundle:EstadoPago')->findOneByDenominacionEstado(ConstanteEstadoPago::ESTADO_PAGO_CREADO) : 
                        $historicos[$cantHistoricos]->getEstadoPago();  
            }  
        }    
            
        $movimiento->setFechaUltimaModificacionEstado(new \DateTime());
        $estadoPagoHistorico = new EstadoPagoHistorico();
        $estadoPagoHistorico->setUsuario($usuario);
        $tipoMovimiento == 'cheque' ? $estadoPagoHistorico->setCheque($movimiento) : $estadoPagoHistorico->setTransferencia($movimiento);
        $estadoPagoHistorico->setEstadoPago($nuevoEstadoPago);
        $movimiento->addHistoricoEstado($estadoPagoHistorico);
        $movimiento->setEstadoPago($nuevoEstadoPago);
            
               
    }      

}
