<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Chequera controller.
 *
 * @Route("/pagos")
 */
class PagoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Lists all Pagos entities.
     *
     * @Route("/reporte_pagos", name="pagos_reporte_pagos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $estadosPago = $em->getRepository('ADIFContableBundle:EstadoPago')
                ->findAll();

        $bancos = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Banco')
                ->findAll();
        
        $estadosNetCash = $em->getRepository('ADIFContableBundle:EstadoNetCash')
                ->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['Estado de cheques y transferencias'] = null;

        return array(
            'bancos' => $bancos,
            'estados_pago' => $estadosPago,
            'estados_netcash' => $estadosNetCash,
            'breadcrumbs' => $bread,
            'page_title' => 'Estado de cheques y transferencias',
            'page_info' => 'Reporte de estados de cheques y transferencias'
        );
    }

    /**
     * Tabla para Pagos.
     *
     * @Route("/index_table/", name="pagos_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $pagos = [];

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');


            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('banco', 'banco');
            $rsm->addScalarResult('numeroSucursalYCuenta', 'numeroSucursalYCuenta');
            $rsm->addScalarResult('formaPago', 'formaPago');
            $rsm->addScalarResult('numero', 'numero');
            $rsm->addScalarResult('importe', 'importe');
            $rsm->addScalarResult('beneficiario', 'beneficiario');
            $rsm->addScalarResult('fecha', 'fecha');
            $rsm->addScalarResult('numeroOP', 'numeroOP');
            $rsm->addScalarResult('estado', 'estado');
            $rsm->addScalarResult('fechaUltimaModificacionEstado', 'fechaUltimaModificacionEstado');
            $rsm->addScalarResult('esEditable', 'esEditable');
            $rsm->addScalarResult('editPath', 'editPath');
            $rsm->addScalarResult('historicoPath', 'historicoPath');
            $rsm->addScalarResult('esNetCash', 'esNetCash');

            $native_query = $em->createNativeQuery('
            SELECT
            id,
            banco,
            formaPago,
            numero,
            numeroSucursalYCuenta,
            importe,
            fecha,
            fechaUltimaModificacionEstado,
            numeroOP,
            estado,
            esEditable,
            beneficiario,
            editPath,
            historicoPath,
            esNetCash
            FROM
                vistareportepagos
            where fecha between ? and ?            
        ', $rsm);


            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $pagos = $native_query->getResult();
        }

        return $this->render('ADIFContableBundle:Pago:index_table.html.twig', array(
                    'entities' => $pagos
                        )
        );
    }

    /**
     * Reporte parte saldos.
     *
     * @Route("/reporte_parte_saldos", name="pagos_reporte_parte_saldos")
     * @Method("GET")
     * @Template("ADIFContableBundle:Pago:reporte_parte_saldos.html.twig")
     */
    public function reporteParteSaldosAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte parte saldos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Reporte parte saldos',
            'page_info' => 'Reporte parte saldos'
        );
    }

    /** Filtra el reporte parte saldos.
     *
     * @Route("/filtrar_reporte_parte_saldos/", name="pagos_filtrar_reporte_parte_saldos")
     * 
     */
    public function filtrarReporteParteSaldosAction(Request $request) {

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $cuentas = [];

        if ($request->query->get('fechaPago') && $request->query->get('fechaExtracto')) {

            $fechaPago = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaPago') . ' 00:00:00');

            $fechaExtracto = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaExtracto') . ' 23:59:59');

            $cuentasBancariasADIF = $emRRHH
                    ->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                    ->findByEstaActiva(true);
//                  ->findAll();

            if (count($cuentasBancariasADIF) > 0) {

                /* @var $cuentaBancariaADIF \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF */
                foreach ($cuentasBancariasADIF as $cuentaBancariaADIF) {

                    $conciliacion = $this->getConciliacionByCuentaBancariaADIF($cuentaBancariaADIF->getId(), $fechaExtracto);

                    $saldoBancario = $conciliacion != null ? $conciliacion->getSaldoExtracto() : 0;

                    $montoAGenerar = $this->getMontoPagoByEstadoPago($cuentaBancariaADIF->getId(), ConstanteEstadoPago::ESTADO_A_GENERAR, $fechaPago);

                    $montoALaFirma = $this->getMontoPagoByEstadoPago($cuentaBancariaADIF->getId(), ConstanteEstadoPago::ESTADO_A_LA_FIRMA, $fechaPago);

                    $montoEnCartera = $this->getMontoPagoByEstadoPago($cuentaBancariaADIF->getId(), ConstanteEstadoPago::ESTADO_EN_CARTERA, $fechaPago);

                    $montoRetirado = $this->getMontoPagoByEstadoPago($cuentaBancariaADIF->getId(), ConstanteEstadoPago::ESTADO_RETIRADO, $fechaPago);

                    $montoIngresosPendientes = $cuentaBancariaADIF->getMontoIngresosPendientes();
                    $montoChequesPendientes = $cuentaBancariaADIF->getMontoChequesPendientes();

                    $saldoFinanciero = $saldoBancario - $montoAGenerar - $montoALaFirma - $montoEnCartera - $montoRetirado + $montoIngresosPendientes - $montoChequesPendientes;

                    $cuentas[] = array(
                        'id' => $cuentaBancariaADIF->getId(),
                        'banco' => $cuentaBancariaADIF->getIdBanco(),
                        'numeroSucursalYCuenta' => $cuentaBancariaADIF->getNumeroSucursalYCuenta(),
                        'saldoBancario' => $saldoBancario,
                        'fechaExtracto' => $conciliacion != null ? $conciliacion->getFechaExtracto() : null,
                        'montoAGenerar' => $montoAGenerar,
                        'montoALaFirma' => $montoALaFirma,
                        'montoEnCartera' => $montoEnCartera,
                        'montoRetirado' => $montoRetirado,
                        'montoChequesPendientes' => $montoChequesPendientes,
                        'montoIngresosPendientes' => $montoIngresosPendientes,
                        'saldoFinanciero' => $saldoFinanciero
                    );
                }
            }
        }

        return $this->render('ADIFContableBundle:Pago:index_table_reporte_parte_saldos.html.twig', array(
                    'entities' => $cuentas
        ));
    }

    /**
     * 
     * @param type $idCuentaBancariaADIF
     * @param type $fechaExtracto
     * @return type
     */
    private function getConciliacionByCuentaBancariaADIF($idCuentaBancariaADIF, $fechaExtracto) {

        $fechaExtractoFormatted = \DateTime::createFromFormat('d/m/Y', $fechaExtracto->format('d/m/Y'));

        $repositoryConciliacion = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion', $this->getEntityManager());

        $qbConciliacion = $repositoryConciliacion->createQueryBuilder('c');

        return $qbConciliacion
                        ->where('c.idCuenta = :idCuentaBancariaADIF')
                        ->andWhere($qbConciliacion->expr()->between(':fechaExtracto', 'c.fechaInicio', 'c.fechaFin'))
                        ->setParameter('idCuentaBancariaADIF', $idCuentaBancariaADIF)
                        ->setParameter('fechaExtracto', $fechaExtractoFormatted, Type::DATE)
                        ->orderBy('c.fechaCierre', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

    /**
     * 
     * @param type $idCuentaBancariaADIF
     * @param type $denominacionEstadoPago
     * @param type $fechaPago
     * @return type
     */
    private function getMontoPagoByEstadoPago($idCuentaBancariaADIF, $denominacionEstadoPago, $fechaPago) {

        $fechaPagoFin = \DateTime::createFromFormat('d/m/Y H:i:s', $fechaPago->format('d/m/Y') . ' 23:59:59');
        /*
          $repositoryCheque = $this->getDoctrine()
          ->getRepository('ADIFContableBundle:Cheque', $this->getEntityManager());

          $qbCheque = $repositoryCheque->createQueryBuilder('c');

          $montoCheques = $qbCheque
          ->select('sum(p.monto)')
          ->join('c.chequera', 'ch')
          ->join('c.estadoPago', 'e')
          ->join('c.pagoOrdenPago', 'p')
          ->where('ch.idCuenta = :idCuentaBancariaADIF')
          ->andWhere('e.denominacionEstado = :denominacionEstadoPago')
          ->andWhere($qbCheque->expr()->between('p.fechaPago', ':fechaInicio', ':fechaFin'))
          ->setParameters(new ArrayCollection(array(
          new Parameter('idCuentaBancariaADIF', $idCuentaBancariaADIF),
          new Parameter('denominacionEstadoPago', $denominacionEstadoPago),
          new Parameter('fechaInicio', $fechaPagoInicio, Type::DATETIME),
          new Parameter('fechaFin', $fechaPagoFin, Type::DATETIME)))
          )
          ->getQuery()
          ->getSingleScalarResult();

          $repositoryTransferencia = $this->getDoctrine()
          ->getRepository('ADIFContableBundle:TransferenciaBancaria', $this->getEntityManager());

          $qbTransferencia = $repositoryTransferencia->createQueryBuilder('t');

          $montoTransferencias = $qbTransferencia
          ->select('sum(p.monto)')
          ->join('t.estadoPago', 'e')
          ->join('t.pagoOrdenPago', 'p')
          ->where('t.idCuenta = :idCuentaBancariaADIF')
          ->andWhere('e.denominacionEstado = :denominacionEstadoPago')
          ->andWhere($qbTransferencia->expr()->between('p.fechaPago', ':fechaInicio', ':fechaFin'))
          ->setParameters(new ArrayCollection(array(
          new Parameter('idCuentaBancariaADIF', $idCuentaBancariaADIF),
          new Parameter('denominacionEstadoPago', $denominacionEstadoPago),
          new Parameter('fechaInicio', $fechaPagoInicio, Type::DATETIME),
          new Parameter('fechaFin', $fechaPagoFin, Type::DATETIME)))
          )
          ->getQuery()
          ->getSingleScalarResult();

          return $montoCheques + $montoTransferencias; */

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('sclr_0', 'monto');

        $native_query = $em->createNativeQuery('
            SELECT sum(p.monto) - SUM(IFNULL(c.retenciones, 0)) AS sclr_0
            FROM movimiento_conciliable m
                INNER JOIN (SELECT ch.id, ch.id_estado_pago, che.id_cuenta, ch.fecha_ultima_modificacion_estado
                            FROM cheque ch 
                                INNER JOIN chequera che ON ch.id_chequera = che.id AND (che.fecha_baja IS NULL)
                            UNION 
                            SELECT id, id_estado_pago, id_cuenta, fecha_ultima_modificacion_estado
                            FROM transferencia_bancaria) chtr ON chtr.id = m.id AND (m.fecha_baja IS NULL)	
                INNER JOIN estado_pago e ON chtr.id_estado_pago = e.id AND (e.fecha_baja IS NULL)
                INNER JOIN pago_orden_pago p ON (chtr.id = p.id_cheque OR chtr.id = p.id_transferencia) AND (p.fecha_baja IS NULL)
                INNER JOIN orden_pago op ON op.id_pago = p.id
                LEFT JOIN (SELECT id_orden_pago, SUM(monto) AS retenciones 
                            FROM comprobante_retencion_impuesto c 
                            GROUP BY id_orden_pago) c ON c.id_orden_pago = op.id
            WHERE chtr.id_cuenta = ?
                AND e.denominacion = ?
                AND (chtr.fecha_ultima_modificacion_estado <= ?)
        ', $rsm);

        $native_query->setParameter(1, $idCuentaBancariaADIF);
        $native_query->setParameter(2, $denominacionEstadoPago);
        $native_query->setParameter(3, $fechaPagoFin, Type::DATETIME);

        $result = $native_query->getResult();

        return isset($result[0]) ? $result[0]['monto'] : null;
    }

}
