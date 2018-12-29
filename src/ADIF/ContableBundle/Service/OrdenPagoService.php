<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\Facturacion\OrdenPagoDevolucionGarantia;
use ADIF\ContableBundle\Entity\OrdenPagoGeneral;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Description of OrdenPagoService
 *
 * @author Manuel Becerra
 * created 04/12/2014
 */
class OrdenPagoService {

    /**
     *
     * @var type 
     */
    protected $container;

    /**
     *
     * @var type 
     */
    protected $doctrine;

    /**
     * 
     * @param \ADIF\ContableBundle\Service\Container $container
     */
    public function __construct(Container $container) {

        $this->container = $container;

        $this->doctrine = $container->get("doctrine");
    }

    /**
     * 
     * @param type $autorizacionContable
     * @param type $concepto
     */
    public function initAutorizacionContable($autorizacionContable, $concepto = '') {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Seteo el concepto
        $autorizacionContable->setConcepto($concepto);

        // Seteo el siguiente numero de autorizacion contable
        $autorizacionContable->setNumeroAutorizacionContable(
                $this->getSiguienteNumeroAutorizacionContable()
        );

        $estadoOrdenPago = ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO;

        // Si la AC requiere visado
        if ($autorizacionContable->getRequiereVisado()) {
            $estadoOrdenPago = ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_AUTORIZACION;
        }

        // Seteo el estado
        $autorizacionContable->setEstadoOrdenPago(
                $emContable->getRepository('ADIFContableBundle:EstadoOrdenPago')
                        ->findOneByDenominacionEstado($estadoOrdenPago)
        );
    }

    /**
     * 
     * @param type $em
     * @param type $egresoValor
     * @param type $importe
     * @param type $concepto
     * @return OrdenPagoEgresoValor
     */
    public function crearAutorizacionContableEgresoValor($em, $egresoValor, $importe, $concepto) {

        $autorizacionContable = new OrdenPagoEgresoValor();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setEgresoValor($egresoValor);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);

        return $autorizacionContable;
    }

    /**
     * 
     * @param type $em
     * @param type $devolucionGarantia
     * @param type $importe
     * @param type $concepto
     * @return OrdenPagoDevolucionGarantia
     */
    public function crearAutorizacionContableDevolucionGarantia($em, $devolucionGarantia, $importe, $concepto) {

        $autorizacionContable = new OrdenPagoDevolucionGarantia();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setDevolucionGarantia($devolucionGarantia);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);

        return $autorizacionContable;
    }

    /**
     * 
     * @param type $em
     * @param type $movimientoBancario
     * @param type $importe
     * @param type $concepto
     * @return \ADIF\ContableBundle\Entity\OrdenPagoMovimientoBancario
     */
    public function crearAutorizacionContableMovimientoBancario($em, $movimientoBancario, $importe, $concepto) {

        $autorizacionContable = new \ADIF\ContableBundle\Entity\OrdenPagoMovimientoBancario();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setMovimientoBancario($movimientoBancario);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);

        return $autorizacionContable;
    }

    /**
     * 
     * @param type $em
     * @param type $movimientoMinisterial
     * @param type $importe
     * @param type $concepto
     * @return \ADIF\ContableBundle\Entity\OrdenPagoMovimientoMinisterial
     */
    public function crearAutorizacionContableMovimientoMinisterial($em, $movimientoMinisterial, $importe, $concepto) {

        $autorizacionContable = new \ADIF\ContableBundle\Entity\OrdenPagoMovimientoMinisterial();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setMovimientoMinisterial($movimientoMinisterial);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);

        return $autorizacionContable;
    }

    /**
     * 
     * @param type $em
     * @param type $conceptoOrdenPago
     * @param type $importe
     * @param type $concepto
     * @return OrdenPagoGeneral
     */
    public function crearAutorizacionContableGeneral($em, $conceptoOrdenPago, $importe, $concepto) {

        $autorizacionContable = new OrdenPagoGeneral();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setConceptoOrdenPago($conceptoOrdenPago);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);

        return $autorizacionContable;
    }

    /**
     * 
     * @param type $em
     * @param type $pagoParcial
     * @param type $importe
     * @param type $concepto
     * @return OrdenPagoGeneral
     */
    public function crearAutorizacionContablePagoParcial($em, $pagoParcial, $importe, $concepto) {

        $autorizacionContable = new OrdenPagoPagoParcial();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setPagoParcial($pagoParcial);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);

        return $autorizacionContable;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumeroAutorizacionContable() {

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:OrdenPago', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('op')
                ->select('op.numeroAutorizacionContable')
                ->orderBy('op.numeroAutorizacionContable', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return $siguienteNumero + 1;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumeroOrdenPago() {

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:OrdenPago', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('op')
                ->select('op.numeroOrdenPago')
                ->orderBy('op.numeroOrdenPago', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return $siguienteNumero + 1;
    }

    /**
     * 
     * @param type $em
     * @param type $reconocimientoEgresoValor
     * @param type $importe
     * @param type $concepto
     */
    public function crearAutorizacionContableReconocimientoEgresoValor($em, $reconocimientoEgresoValor, $importe, $concepto) {

        $autorizacionContable = new OrdenPagoReconocimientoEgresoValor();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setReconocimientoEgresoValor($reconocimientoEgresoValor);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);
    }

    /**
     * 
     * @param type $em
     * @param type $pagoACuenta
     * @param type $importe
     * @param type $concepto
     */
    public function crearAutorizacionContablePagoACuenta($em, $pagoACuenta, $importe, $concepto) {

        $autorizacionContable = new \ADIF\ContableBundle\Entity\OrdenPagoPagoACuenta();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setPagoACuenta($pagoACuenta);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);
    }
    
    /**
     * 
     * @param type $em
     * @param type $devolucionRenglonDeclaracionJurada
     * @param type $importe
     * @param type $concepto
     */
    public function crearAutorizacionContableDevolucionRenglonDeclaracionJurada($em, $devolucionRenglonDeclaracionJurada, $importe, $concepto) {
        $autorizacionContable = new \ADIF\ContableBundle\Entity\OrdenPagoDevolucionRenglonDeclaracionJurada();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setDevolucionRenglonDeclaracionJurada($devolucionRenglonDeclaracionJurada);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);
        
        return $autorizacionContable;
    }

    /**
     * 
     * @param type $em
     * @param type $declaracionJurada
     * @param type $importe
     * @param type $concepto
     */
    public function crearAutorizacionContableDeclaracionJurada($em, $declaracionJurada, $importe, $concepto) {

        $autorizacionContable = new \ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJurada();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        $autorizacionContable->setDeclaracionJurada($declaracionJurada);

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);
    }

    /**
     * 
     * @param type $em
     * @param type $declaracionJurada
     * @param type $importe
     * @param type $concepto
     */
    public function crearAutorizacionContableRenglonesRetencionLiquidacion($em, $renglonesRetencionLiquidacionIds, $importe, $concepto) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $autorizacionContable = new \ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion();

        $this->initAutorizacionContable($autorizacionContable, $concepto);

        // Por cada renglon obtenido
        foreach ($renglonesRetencionLiquidacionIds as $idRenglonRetencionLiquidacion) {
            $renglonRetencionLiquidacion = $emContable->getRepository('ADIFContableBundle:RenglonRetencionLiquidacion')->find($idRenglonRetencionLiquidacion);
            $autorizacionContable->addRenglonesRetencionLiquidacion($renglonRetencionLiquidacion);
        }

        $autorizacionContable->setImporte($importe);

        $em->persist($autorizacionContable);
    }

}
