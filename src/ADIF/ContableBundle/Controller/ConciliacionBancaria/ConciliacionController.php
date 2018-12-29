<?php

namespace ADIF\ContableBundle\Controller\ConciliacionBancaria;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonConciliacion;
use ADIF\ContableBundle\Form\ConciliacionBancaria\ConciliacionType;
use DateTime;
use Doctrine\ORM\Query\ResultSetMapping;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ADIF\ContableBundle\Entity\Constantes\ConstanteDestinoIvaCompras;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\Exception;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\GastoBancario;
use ADIF\ContableBundle\Entity\RenglonIvaCompras;
use ADIF\BaseBundle\Entity\AdifApi;
use DateInterval;

use ADIF\BaseBundle\Entity\EntityManagers;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;

/**
 * ConciliacionBancaria\Conciliacion controller.
 *
 * @Route("/conciliacion")
 * @Security("has_role('ROLE_USER')")
 */
class ConciliacionController extends BaseController {

    private $base_breadcrumbs;
    
    private $logger;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conciliaciones bancarias' => $this->generateUrl('conciliacion')
        );
    }

    /**
     * Lists all ConciliacionBancaria\Conciliacion entities.
     *
     * @Route("/", name="conciliacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Conciliaciones bancarias'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Conciliaciones bancarias',
            'page_info' => 'Lista de conciliaciones bancarias'
        );
    }

    /**
     * Tabla para Conciliacion .
     *
     * @Route("/index_table/", name="conciliacion_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conciliaciones = null;

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion', $this->getEntityManager());

            $qb = $repository->createQueryBuilder('c');

            $conciliaciones = $qb
                    ->where($qb->expr()->between('c.fechaInicio', ':fechaInicio', ':fechaFin'))
                    ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                    ->addOrderBy('c.fechaInicio', 'DESC')
                    ->getQuery()
                    ->getResult();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Conciliaciones bancarias'] = null;

        return $this->render('ADIFContableBundle:ConciliacionBancaria\Conciliacion:index_table.html.twig', array(
                    'entities' => $conciliaciones
                        )
        );
    }

    /**
     * Creates a new ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/insertar", name="conciliacion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConciliacionBancaria\Conciliacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Conciliacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $fechaIniNueCon = $entity->getFechaInicio();
            $fechaFinNueCon = $entity->getFechaFin();
            $tieneConciliacionesRegistradas = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->findBy(array('idCuenta' => $entity->getIdCuenta()));
            if (!$tieneConciliacionesRegistradas) {

                if ($fechaIniNueCon->format('d') == 1 && $fechaIniNueCon->format('m/Y') == $fechaFinNueCon->format('m/Y') && $fechaIniNueCon->format('d') < $fechaFinNueCon->format('d')) {
                    $entity->setSaldoExtracto(0);
                    $entity->setTipoCambio(1);
                    $entity->setFechaExtracto($entity->getFechaInicio());
                    $em->persist($entity);
                    $em->flush();
                    return $this->redirect($this->generateUrl('conciliacion_edit', array('id' => $entity->getId())));
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'La fecha de inicio debe ser el primero del mes a conciliar y éste debe coincidir con el mes de la fecha fin');
                }
            } else {

//                $tieneConciliacionAbierta = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->findBy(array('idCuenta' => $entity->getIdCuenta(), 'fechaCierre' => null));
//                if ($tieneConciliacionAbierta) {
//                    $this->get('session')->getFlashBag()->add('error', 'La cuenta ' . $entity->getCuenta() . ' tiene una conciliación abierta con fecha de inicio el ' . $tieneConciliacionAbierta[0]->getFechaInicio()->format('d/m/Y'));
//                    return $this->redirect($this->generateUrl('conciliacion'));
//                } else {

                $ultimaConciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->findBy(array('idCuenta' => $entity->getIdCuenta()), array('fechaInicio' => 'DESC'), 1);
                $fechaFinUltCon = $ultimaConciliacion[0]->getFechaFin();

                if ($fechaFinUltCon->add(new DateInterval('P1D'))->format('d/m/Y') == $fechaIniNueCon->format('d/m/Y')) {

                    if ($fechaIniNueCon->format('m/Y') == $fechaFinNueCon->format('m/Y') && $fechaIniNueCon->format('d') <= $fechaFinNueCon->format('d')) {
                        $entity->setSaldoExtracto(0);
                        $entity->setTipoCambio(1);
                        $entity->setFechaExtracto($entity->getFechaInicio());
                        $em->persist($entity);
                        $em->flush();

                        return $this->redirect($this->generateUrl('conciliacion_edit', array('id' => $entity->getId())));
                    } else {
                        $this->get('session')->getFlashBag()->add('error', 'La fecha inicio debe ser menor a la fecha fin y ambas del mismo mes a conciliar');
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'La fecha de inicio de la próxima conciliación para la cuenta seleccionada debe ser ' . $fechaFinUltCon->format('d/m/Y'));
                }
//                }
            }
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear conciliaci&oacute;n bancaria',
        );
    }

    /**
     * Creates a form to create a ConciliacionBancaria\Conciliacion entity.
     *
     * @param Conciliacion $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Conciliacion $entity) {
        $form = $this->createForm(new ConciliacionType(), $entity, array(
            'action' => $this->generateUrl('conciliacion_create'),
            'method' => 'POST',
            //'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/crear", name="conciliacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        //$em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = new Conciliacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear conciliaci&oacute;n bancaria'
        );
    }

    /**
     * Finds and displays a ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/ver/{id}", name="conciliacion_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConciliacionBancaria\Conciliacion:show.html.twig")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Conciliaci&oacute;n bancaria'] = null;

        $arrayPrevisualizacion = $this->generarArrayPrevisualizacionCerrada($id);
        return array_merge(array(
            //'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver conciliaci&oacute;n bancaria'
                ), $arrayPrevisualizacion);
    }

    /**
     * Finds and displays a ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/abrir/{id}", name="conciliacion_abrir")
     * @Method("GET")
     */
    public function abrirAction($id) {
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);
        
        $conciliacion->setFechaCierre(null);
        $conciliacion->setEsReAbierta(1);
        
        $em->flush();
        return $this->redirect($this->generateUrl('conciliacion'));
    }
    
    /**
     * Exporta el reporte de la conciliación en PDF
     *
     * @Route("/conciliacion_PDF/{id}", name="conciliacion_PDF")
     */
    public function conciliacionPDFAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);

        if (!$conciliacion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $encabezado = '<table width="100%" style="vertical-align: center; font-size: 14px; color: #000000; font-weight: bold; border-bottom: 1px solid">
            <tr><td width="100%" align="center" style="font-weight: bold; font-family: "Open Sans";">Conciliaci&oacute;n entre el ' . $conciliacion->getFechaInicio()->format('d.m.Y') . ' y el ' . $conciliacion->getFechaFin()->format('d.m.Y') . ' de la ' . strtolower($conciliacion->getCuenta()->getIdTipoCuenta()) . ' del<br/> '
                . 'banco ' . $conciliacion->getCuenta()->getIdBanco() . ' con CBU ' . $conciliacion->getCuenta()->getCbu() . ($conciliacion->getFechaCierre() ? ' (cerrada el ' . $conciliacion->getFechaCierre()->format('d.m.Y') . ')' : ' (aún abierta)') . '</td></tr></table><br/>';

        $html = $this->renderView(
                'ADIFContableBundle:ConciliacionBancaria/Conciliacion:previsualizacion.html.twig', $this->generarArrayPrevisualizacion($id)
        );
        //$titulo = ($conciliacion->getFechaCierre()?'CERRADA EL '.$conciliacion->getFechaCierre()->format('d.m.Y'):'ABIERTA');
        $nombre = 'Conciliacion ' . $conciliacion->getCuenta()->getIdBanco() . ' ' . $conciliacion->getCuenta()->getIdTipoCuenta() . ' ' . $conciliacion->getCuenta()->getCbu() . ' ' . $conciliacion->getFechaInicio()->format('d.m.Y') . ' al ' . $conciliacion->getFechaFin()->format('d.m.Y');
        AdifApi::printPDF($encabezado . $html, '', $nombre);
        //AdifApi::printPDF($html, 'Conciliaci&oacute;n entre el '.$conciliacion->getFechaInicio()->format('d/m/Y').' y el '.$conciliacion->getFechaFin()->format('d/m/Y').' de la '.$conciliacion->getCuenta()->getIdTipoCuenta().' del banco '.$conciliacion->getCuenta()->getIdBanco().' con CBU '.$conciliacion->getCuenta()->getCbu().' ('.$titulo.')', $nombre);
    }

    /**
     * Devuelve un array de los conceptos que puede tener un RenglonConciliacion
     * 
     */
    private function getTipoConceptos($conceptos) {
        $conceptosArray = [];

        foreach ($conceptos as $concepto) {
            $conceptosArray[$concepto->getId()] = $concepto->getDenominacion() . ': ' . $concepto->getDescripcion();
        }

        return $conceptosArray;
    }

    /**
     * Displays a form to edit an existing ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/editar/{id}", name="conciliacion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConciliacionBancaria\Conciliacion:edit.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $editForm = $this->createEditForm($entity);

        $conceptos = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;
        //var_dump($entity->getFechaInicio()->format('d/m/Y'));die();

        $hoy = new DateTime(date('Y-m-d'));
        $fecha_hoy = DateTime::createFromFormat('Y-m-d H:i:s', $hoy->format('Y-m-d') . ' 00:00:00');
        $fecha_fin = ($entity->getFechaFin() >= $fecha_hoy ? $hoy->sub(new DateInterval('P1D')) : $entity->getFechaFin());

        $query = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->createQueryBuilder('c')
                ->where('c.idCuenta = ' . $entity->getIdCuenta())
                ->andWhere('c.fechaCierre IS NULL')
                ->setMaxResults(1)
                ->orderBy('c.fechaFin', 'ASC')
                ->getQuery();
        //var_dump($query->getSql());die();
        //var_dump($entity->getEsReAbierta());die();
        
        $result = $query->getResult();

        $puede_conciliar = false;
        if ($result) {
            $ultima_conciliacion_abierta = $result[0];
            $puede_conciliar = $ultima_conciliacion_abierta->getId() == $id;
        } else {
            $puede_conciliar = true;
        }
        
        $tiene_tipo_cambio = $entity->tieneTipoCambio();

        return array(
            'entity' => $entity,
            'conceptos' => $this->getTipoConceptos($conceptos),
            'fecha_inicio' => $entity->getFechaInicio()->format('d/m/Y'),
            'fecha_fin' => $fecha_fin->format('d/m/Y'),
            'tiene_tipo_cambio' => $tiene_tipo_cambio,
            'puede_conciliar' => $puede_conciliar,
            'es_re_abierta' => $entity->getEsReAbierta(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar conciliaci&oacute;n bancaria'
        );
    }

    /**
     * Creates a form to edit a ConciliacionBancaria\Conciliacion entity.
     *
     * @param Conciliacion $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Conciliacion $entity) {
        $form = $this->createForm(new ConciliacionType(), $entity, array(
            'action' => $this->generateUrl('cerrar_conciliacion', array('id' => $entity->getId())),
            'method' => 'PUT',
            //'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('cerrar', 'button', array('label' => 'Cerrar conciliaci&oacute;n'));

        return $form;
    }

    /**
     * Edits an existing ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/actualizar/{id}", name="conciliacion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConciliacionBancaria\Conciliacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('conciliacion'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar conciliaci&oacute;n bancaria'
        );
    }

    /**
     * Deletes a ConciliacionBancaria\Conciliacion entity.
     *
     * @Route("/borrar/{id}", name="conciliacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('conciliacion'));
    }

    /**
     * Tabla mayor.
     *
     * @Route("/index_table_mayor/", name="conciliacion_index_table_mayor")
     * @Method("GET|POST")
     * 
     */
    public function indexTableMayorAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id_conciliacion = $request->query->get('id_conciliacion');
        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id_conciliacion);

        //$fecha_inicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_inicio') . ' 00:00:00');
        //$fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_fin') . ' 23:59:59');
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $conciliacion->getFechaFin()->format('d/m/Y') . ' 23:59:59');

        $renglones = $this->getRenglonesMayor($id_conciliacion, null, $fecha_fin, false);

        return $this->render('ADIFContableBundle:ConciliacionBancaria/Conciliacion:index_table_mayor.html.twig', array('entities' => $renglones, 'cuentaBancaria' => $conciliacion->getCuenta()));
    }

    /**
     * Devuelve los renglones del mayor que fueron conciliados
     * 
     */
    private function getRenglonesMayor2($idConciliacion, $fecha_inicio, $fecha_fin) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fecha', 'fecha');
        $rsm->addScalarResult('concepto', 'concepto');
        $rsm->addScalarResult('referencia', 'referencia');
        $rsm->addScalarResult('monto', 'monto');
        $rsm->addScalarResult('tipo', 'tipo');
        //$rsm->addScalarResult('saldo', 'saldo');

        $querySTR = "
            SELECT pago.id, 
                p.fecha_pago AS fecha, 
                pago.concepto,
                pago.referencia,
                p.monto,
                pago.tipo
            FROM pago_orden_pago p                
                INNER JOIN orden_pago o ON p.id = o.id_pago
                INNER JOIN estado_orden_pago e ON e.id = o.id_estado_orden_pago
                INNER JOIN (SELECT c.id, CONCAT('Cheque Nº: ',c.numero) AS concepto, c.numero AS referencia, 'CHEQUE' AS tipo
                            FROM cheque c
                                INNER JOIN estado_pago e ON e.id = c.id_estado_pago
                            WHERE e.denominacion <> '" . ConstanteEstadoPago::ESTADO_PAGO_ANULADO . "' 
                                AND c.id_conciliacion " . ($idConciliacion ? ' = ' . $idConciliacion : 'IS NULL') . "
                        UNION 
                            SELECT t.id, CONCAT('Transferencia Nº: ',t.numero) AS concepto, t.numero AS referencia, 'TRANSFERENCIA' AS tipo
                            FROM transferencia_bancaria t
                                INNER JOIN estado_pago e ON e.id = t.id_estado_pago
                            WHERE e.denominacion <> '" . ConstanteEstadoPago::ESTADO_PAGO_ANULADO . "' 
                                AND t.id_conciliacion " . ($idConciliacion ? ' = ' . $idConciliacion : 'IS NULL') . "
                        ) pago ON (
                                (pago.id = p.id_cheque AND pago.tipo = 'CHEQUE') OR (pago.id = p.id_transferencia AND pago.tipo = 'TRANSFERENCIA'))
            WHERE p.fecha_baja IS NULL
                AND o.fecha_baja IS NULL
                AND o.numero_orden_pago IS NOT NULL
                AND e.denominacion = '" . ConstanteEstadoOrdenPago::ESTADO_PAGADA . "'";

        if ($fecha_inicio && $fecha_fin) {
            $querySTR .= " AND DATE(p.fecha_pago) BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_fin . "'";
        }
        $querySTR .= " ORDER BY p.fecha_pago";

        $query = $em->createNativeQuery($querySTR, $rsm);

        return $query->getResult();
    }

    /**
     * Devuelve los renglones del mayor que fueron conciliados
     * @return ArrayCollection <MovimientoConciliable>
     */
    private function getRenglonesMayor($id_conciliacion, $fecha_inicio, $fecha_fin, $filtrar_conciliacion) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id_conciliacion);

        $resultado = array();

        $repository = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\MovimientoConciliable');
        $query = $repository->createQueryBuilder('m')
                ->where('m.conciliacion ' . ($filtrar_conciliacion ? ' = ' . $id_conciliacion : 'IS NULL'))
                ->getQuery();

        $result = $query->getResult();

        foreach ($result as $movimientoConciliable) {
            /* @var $movimientoConciliable \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable */
            /* @var $conciliacion \ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion */
            if ($movimientoConciliable->cumpleCondicion($conciliacion->getCuenta(), $fecha_inicio, $fecha_fin)) {
                $resultado[] = $movimientoConciliable;
            }
        }

        return $resultado;
    }

    /**
     * Tabla mayor conciliado.
     *
     * @Route("/index_table_mayor_conciliado/", name="conciliacion_index_table_mayor_conciliado")
     * @Method("GET|POST")
     * 
     */
    public function indexTableMayorConciliadoAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id_conciliacion = $request->query->get('id_conciliacion');
        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id_conciliacion);
        $renglones = $this->getRenglonesMayor($id_conciliacion, null, null, true);

        return $this->render('ADIFContableBundle:ConciliacionBancaria/Conciliacion:index_table_mayor.html.twig', array('entities' => $renglones, 'cuentaBancaria' => $conciliacion->getCuenta()));
    }

    /**
     * Procesamiento del extracto bancario.
     *
     * @Route("/cargar_extracto/", name="conciliacion_cargar_extracto")
     * @Method("POST")
     * 
     */
    public function cargarExtractoAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id_conciliacion = $request->request->get('id_conciliacion');
        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id_conciliacion);
        
        $conciliacion->setSaldoExtracto($request->request->get('saldoExtracto'));
        
        if ($request->request->get('tipoCambio')) {
            $conciliacion->setTipoCambio($request->request->get('tipoCambio'));
        }
        
        $fechaRequest = $request->request->get('fechaExtracto');
        if ($fechaRequest) {
            $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
            $fecha = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));
            if ($fecha >= $conciliacion->getFechaInicio() && $fecha <= $conciliacion->getFechaFin()) {
                $conciliacion->setFechaExtracto($fecha);
            } else {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => "La fecha del extracto tiene que estar dentro del pediodo de conciiaci&oacute;n"));
            }
        } else {
            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => "No se envi&oacute; la fecha del extracto"));
        }

        if ($request->files->get('archivo')) {
            $uploadDir = dirname($this->container->getParameter('kernel.root_dir')) . '/web/bundles/adifcontable/importaciones/conciliaciones';
            $newFile = 'importacion_conciliacion_' . $id_conciliacion . '_' . time() . '.xls';
            $request->files->get('archivo')->move($uploadDir, $newFile);

            $objReader = PHPExcel_IOFactory::createReaderForFile($uploadDir . '/' . $newFile);
            if ($objReader->canRead($uploadDir . '/' . $newFile)) {
                $objReader->setReadDataOnly(true);
                try {
                    $objPHPExcel = $objReader->load($uploadDir . '/' . $newFile);
                } catch (Exception $e) {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => $e->printStackTrace()));
                }
                $sheet = $objPHPExcel->getActiveSheet();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $renglones = array();
                $fechasOK = true;

                for ($row = 2; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                    $datos = $rowData[0];

                    if (empty($datos[0]) || empty($datos[1]) || empty($datos[2]) || empty($datos[3])) {
                        //Si falta algún dato en la fila
                        return new JsonResponse(array(
                            'status' => 'ERROR',
                            'message' => "Datos invalidos en la fila " . $row)
                        );
                    } else {
                        $fechaMovimiento = new \DateTime(gmdate('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($datos[1])));

                        if ($fechaMovimiento >= $conciliacion->getFechaInicio() && $fechaMovimiento <= $conciliacion->getFechaFin()) {
                            $renglon = new RenglonConciliacion();
                            $renglon->setEstadoRenglonConciliacion($em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->findOneByDenominacion(ConstanteEstadoRenglonConciliacion::ESTADO_PENDIENTE));
                            $renglon->setNumeroReferencia($datos[0]);
                            $renglon->setFechaMovimientoBancario($fechaMovimiento);
                            $renglon->setDescripcion($datos[2]);
                            $renglon->setMonto($datos[3]);
                            $renglones[] = $renglon;
                        } else {
                            $fechasOK = false;
                            break;
                        }
                    }
                }

                if ($fechasOK) {
                    $importacionConciliacion = new ImportacionConciliacion();
                    $importacionConciliacion->setConciliacion($conciliacion);
                    $importacionConciliacion->setNombre($newFile);
                    $importacionConciliacion->setNombreArchivo($request->files->get('archivo')->getClientOriginalName());
                    if ($request->request->get('tipoCambioImportacion')) {
                        $importacionConciliacion->setTipoCambio($request->request->get('tipoCambioImportacion'));
                    }                    
                    foreach ($renglones as $renglon) {
                        $importacionConciliacion->addRenglonesConciliacion($renglon);
                    }
                    $em->persist($importacionConciliacion);
                    $em->flush();
                } else {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => "Hay movimientos con fecha invalidas "));
                }
            } else {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => "Formato de archivo invalido"));
            }
        }
        $em->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    /**
     * Setea estado conciliado a los renglones y la conciliacion a los cheques, transferencias
     *
     * @Route("/conciliar_renglones_movimientos/", name="conciliar_renglones_movimientos")
     * @Method("POST")
     */
    public function conciliarRenglonesMovimientosAction(Request $request) {

        $this->actualizar_renglones_movimientos($request, true);

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    private function actualizar_renglones_movimientos(Request $request, $esConciliacion) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($esConciliacion) {
            $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($request->request->get('id_conciliacion'));
            $estado = ConstanteEstadoRenglonConciliacion::ESTADO_CONCILIADO;
        } else {
            $conciliacion = null;
            $estado = ConstanteEstadoRenglonConciliacion::ESTADO_PENDIENTE;
        }

        $ids_renglones = json_decode($request->request->get('ids_renglones', '[]'));
        $ids_movimientos = json_decode($request->request->get('ids_movimientos', '[]'));

        foreach ($ids_renglones as $id_renglon) {
            $renglon = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id_renglon);
            $renglon->setEstadoRenglonConciliacion($em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->findOneByDenominacion($estado));
        }

        foreach ($ids_movimientos as $id_movimiento) {
            $movimiento = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\MovimientoConciliable')->find($id_movimiento);
            $movimiento->setConciliacion($conciliacion);
            if (($movimiento->getTipo() == 'Cheque') || ($movimiento->getTipo() == 'TRANSFERENCIA')) {
                $this->get('adif.chequera_service')->acreditarMovimiento($movimiento, $em, $this->getUser(), $esConciliacion);
            }
        }

        $em->flush();
    }

    /**
     * Desconcilia los renglones y movimientos
     *
     * @Route("/desconciliar_renglones_movimientos/", name="desconciliar_renglones_movimientos")
     * @Method("POST")
     */
    public function desconciliarRenglonesMovimientosAction(Request $request) 
    {
        $this->actualizar_renglones_movimientos($request, false);

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    /**
     * Desconcilia los renglones y genera asiento
     *
     * @Route("/desconciliar_renglones_movimientos_comprobante/", name="desconciliar_renglones_movimientos_comprobante")
     * @Method("POST")
     */
    public function desconciliarRenglonesMovimientosComprobanteAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        // Comienzo la transaccion
        $em->getConnection()->beginTransaction();

        try {
            
            $this->logger = new Logger('ganancia');

            $monologFormat = "%message%\n";
            $dateFormat = "Y/m/d H:i:s";
            $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

            $streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/conciliacion_bancaria/desconciliacion_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
            $streamHandler->setFormatter($monologLineFormat);

            $this->logger->pushHandler($streamHandler);
            
            $this->logger->info("------------------------------------------------------------------------------");
            
            // @var DateTime $fechaCheckeoAsientosRenglonConciliacion: indica a partir de que fecha se empieza a contemplar la 
            // validacion de fijarse si antes de desconciliar, ya se haya hecho un asiento de conciliacion previa
            $fechaCheckeoAsientosRenglonConciliacion = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-12-12 00:00:00');
            
            // @var DateTime $fechaCheckeoAsientosConciliacion: idem explicacion anterior, pero a nivel conciliacion, como toda la 
            // conciliacion se hace a mes vencido, determino hasta que fecha va a tener en cuenta la validacion
            $fechaCheckeoAsientosConciliacion = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-12-12 00:00:00');
        
            $this->actualizar_renglones_movimientos($request, false);

            $fecha_comprobante = new \DateTime();

            $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($request->request->get('id_conciliacion'));
            $ids_renglones = json_decode($request->request->get('ids_renglones', '[]'));
            $renglones = array();
            $renglonesConAsientoConciliacion = array();
            $resultArray = array();

            $renglonIva = new RenglonIvaCompras();

            $renglonIva->setConciliacion($conciliacion);
                    //->setEsSuma(false);

            foreach ($ids_renglones as $id_renglon) {
                $renglon = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id_renglon);
                
                switch ($renglon->getConceptoConciliacion()->getDestinoIvaCompras()) {
                    case ConstanteDestinoIvaCompras::IIBB901:
                        //$renglonIva->setIIBB901($renglonIva->getIIBB901() + abs($renglon->getMonto()));
                        $renglonIva->setIIBB901($renglonIva->getIIBB901() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::IIBB902:
                        //$renglonIva->setIIBB902($renglonIva->getIIBB902() + abs($renglon->getMonto()));
                        $renglonIva->setIIBB902($renglonIva->getIIBB902() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::IVA21:
                        //$renglonIva->setIva21($renglonIva->getIva21() + abs($renglon->getMonto()));
                        $renglonIva->setIva21($renglonIva->getIva21() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::EXENTO:
                        //$renglonIva->setGastosExentos($renglonIva->getGastosExentos() + abs($renglon->getMonto()));
                        $renglonIva->setGastosExentos($renglonIva->getGastosExentos() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::GASTOS:
                        //$renglonIva->setGastosBancarios($renglonIva->getGastosBancarios() + abs($renglon->getMonto()));
                        $renglonIva->setGastosBancarios($renglonIva->getGastosBancarios() + $renglon->getMonto());
                        break;
                    default:
                        //$renglonIva->setOtrosImpuestos($renglonIva->getOtrosImpuestos() + abs($renglon->getMonto()));
                        $renglonIva->setOtrosImpuestos($renglonIva->getOtrosImpuestos() + $renglon->getMonto());
                        break;
                }

                $gastoBancario = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\GastoBancario')->findOneByRenglonConciliacion($renglon);
                $fecha_comprobante = $gastoBancario->getFecha();
                $renglonIva->setFecha($gastoBancario->getFecha());

                $em->remove($gastoBancario);
                
                // Comprobacion de desconciliacion con asiento de conciliacion previo - @gluis
                if (    
                        $renglon->getAsientoContableConciliacion() != null &&
                        $renglon->getFechaConciliacion() != null &&
                        $renglon->getFechaConciliacion() >= $fechaCheckeoAsientosRenglonConciliacion
                    ) {
                        $renglonesConAsientoConciliacion[] = $renglon;
                } else {
                    // Reglones viejos sin la asociacion FK con el asiento contable de conciliacion
                    $renglones[] = $renglon;
                }   
            }
            

            $mensajeInvalido = $renglonIva->getMensajeInvalido($renglonIva);
            if ($mensajeInvalido['error']) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => $mensajeInvalido['mensaje']));
            }        

            $renglonIva->setProrrateo();
            //$renglonIva->setResta();

            // Persisto la entidad
            $em->persist($renglonIva);

            /**
             * Mejora: 
             * No se puede crear un asiento de desconciliacion, si previamente no se creo un asiento de conciliacion,
             * apartir del 12/12/2017 para los renglones y para la conciliaciones posteriores a noviembre 2017
             * 
             * @gluis - 11/12/2017
             */
            
            //\Doctrine\Common\Util\Debug::dump( $renglonesConAsientoConciliacion ); 
            //\Doctrine\Common\Util\Debug::dump( $renglones ); 
            //exit;
            if (empty($renglonesConAsientoConciliacion)) {
                
                if (!empty($renglones) && $conciliacion->getFechaInicio() <= $fechaCheckeoAsientosConciliacion) {
                    /**
                     * Caso 1: renglones conciliacion viejos, con ningun renglon de conciliacion con asientos
                     * Que los renglones viejos de las conciliaciones puedan generar el asiento de conciliacion, si ya apartir del 
                     * 12/12/2017 si no tienen su asiento de conociliacion no va a generar el asiento correspondiente
                     */
                    $this->logger->info("CASO 1: Renglones viejos + ningun renglon con asiento.");
                    $resultArray = $this->generarAsientosConciliacion($renglones, false, $conciliacion, $fecha_comprobante);
                } else {
                    // Caso 2: no hay renglones viejos de conciliacion ni renglones con conciliacion con asientos
                    // No pasa nada....no genera asiento
                    $this->logger->info("CASO 2: Ningun renglon viejo + ningun renglon con asiento.");
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/> Los renglones de la conciliaci&oacute;n deberia tener asociado un asiento de conciliaci&oacute;n previo.'));
                }
                
            } else {
                
                if (empty($renglones)) {
                     /**
                      * Caso 3: ningun renglon viejo de conciliacion, con renglones de conciliacion con asientos de conciliacion previos
                      * A medida que pase el tiempo y se empiezen con conciliacion posteriores a noviembre 2017 y 
                      * que todos los renglones de conciliacion tenga su relacion previa con su asiento de conociliacion, 
                      * este caso 3, tiene que ser el caso normal
                      */
                    $this->logger->info("CASO 3: Ningun renglon viejo + renglones con asiento.");
                    $resultArray = $this->generarAsientosConciliacion($renglonesConAsientoConciliacion, false, $conciliacion, $fecha_comprobante);
                } else {
                     /**
                      * Caso 4: renglones viejos + renglones de conciliacion con asientos
                      * Si la conciliacion es de noviembre 2017 o antes, mergeo todo y genero el asiento de desconciliacion por todo
                      * Si la conciliacion es mas grande que noviembre 2017, solo voy generar asiento por por los renglones
                      * de conciliacion que tengan asociado previamente su asiento de conciliacion
                      */
                    $this->logger->info("CASO 4: Renglones viejos + renglones con asiento.");
                    if ($conciliacion->getFechaInicio() <= $fechaCheckeoAsientosConciliacion) {
                        $this->logger->info("CASO 4.1: Merge de los dos");
                        $renglonesMergeados = array_merge($renglonesConAsientoConciliacion, $renglones);
                        $resultArray = $this->generarAsientosConciliacion($renglonesMergeados, false, $conciliacion, $fecha_comprobante);
                    } else {
                        $this->logger->info("CASO 4.2: Solo los renglones con asientos");
                        $resultArray = $this->generarAsientosConciliacion($renglonesConAsientoConciliacion, false, $conciliacion, $fecha_comprobante);
                    }
                }
            }
            

            // Si el asiento presupuestario falló
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }

            // Si no hubo errores en los asientos
            if (!empty($resultArray) && $resultArray['numeroAsiento'] != -1) {

                $em->flush();
                    
                $asientoContable = $em
                        ->getRepository('ADIFContableBundle:AsientoContable')
                        ->findOneBy(array('numeroAsiento' => $resultArray['numeroAsiento']));
                
                if ($asientoContable) {
                    if ( count($asientoContable->getRenglonesAsientoContable()) > 0 ) {

                        $renglonesMergeados = array_merge($renglonesConAsientoConciliacion, $renglones);

                        if ( $renglonesMergeados ) {
                            foreach($renglonesMergeados as $renglon) {
                                $renglon->setAsientoContableDesconciliacion($asientoContable);
                                $em->persist($renglon);
                                $this->logger->info("Renglon ID = " . $renglon->getId() . " Asiento contable desconciliacion ID = " . $asientoContable->getId());
                            }
                        } else {
                            throw new \Exception("Se produjo un error al generar el asiento contable.");
                        }
                    } else {
                        throw new \Exception("Se produjo un error al generar el asiento contable.");
                    }
                }   
                    
                $em->flush();
                $em->getConnection()->commit();
                
            } else {
                 throw new \Exception("Se produjo un error al generar el asiento contable.");
            }
        
        } catch (\Exception $e) {
            //die($e->getMessage());
            $em->getConnection()->rollback();
            $em->close();

            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => $e->getMessage()));
        }
        
        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    /**
     * Desconcilia los renglones y genera asiento
     *
     * @Route("/conciliar_renglones_movimientos_comprobante/", name="conciliar_renglones_movimientos_comprobante")
     * @Method("POST")
     */
    public function conciliarRenglonesMovimientosComprobanteAction(Request $request) 
    {   
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Comienzo la transaccion
        $em->getConnection()->beginTransaction();
        
        try {
            
            $this->logger = new Logger('ganancia');

            $monologFormat = "%message%\n";
            $dateFormat = "Y/m/d H:i:s";
            $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

            $streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/conciliacion_bancaria/conciliacion_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
            $streamHandler->setFormatter($monologLineFormat);

            $this->logger->pushHandler($streamHandler);
            
            $this->logger->info("------------------------------------------------------------------------------");
            
            $this->actualizar_renglones_movimientos($request, true);

            $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($request->request->get('id_conciliacion'));
            $ids_renglones = json_decode($request->request->get('ids_renglones', '[]'));

            $mensajeErrorAsientoContable = '';

            $fechaRequest = $request->request->get('fecha_comprobante');
            $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
            $fecha_comprobante = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));
            $renglones = array();

            $renglonIva = new RenglonIvaCompras();
            $renglonIva->setFecha($fecha_comprobante);
            $renglonIva->setConciliacion($conciliacion);

            foreach ($ids_renglones as $id_renglon) {
                $renglon = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id_renglon);
                $renglones[] = $renglon;
                $gastoBancario = new GastoBancario();
                $gastoBancario->setRenglonConciliacion($renglon);
                $gastoBancario->setConciliacion($conciliacion);
                $gastoBancario->setFecha($fecha_comprobante);
                switch ($renglon->getConceptoConciliacion()->getDestinoIvaCompras()) {
                    case ConstanteDestinoIvaCompras::IIBB901:
                        //$renglonIva->setIIBB901($renglonIva->getIIBB901() + abs($renglon->getMonto()));
                        $renglonIva->setIIBB901($renglonIva->getIIBB901() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::IIBB902:
                        //$renglonIva->setIIBB902($renglonIva->getIIBB902() + abs($renglon->getMonto()));
                        $renglonIva->setIIBB902($renglonIva->getIIBB902() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::IVA21:
                        //$renglonIva->setIva21($renglonIva->getIva21() + abs($renglon->getMonto()));
                        $renglonIva->setIva21($renglonIva->getIva21() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::EXENTO:
                        //$renglonIva->setGastosExentos($renglonIva->getGastosExentos() + abs($renglon->getMonto()));
                        $renglonIva->setGastosExentos($renglonIva->getGastosExentos() + $renglon->getMonto());
                        break;
                    case ConstanteDestinoIvaCompras::GASTOS:
                        //$renglonIva->setGastosBancarios($renglonIva->getGastosBancarios() + abs($renglon->getMonto()));
                        $renglonIva->setGastosBancarios($renglonIva->getGastosBancarios() + $renglon->getMonto());
                        break;
                    default:
                        //$renglonIva->setOtrosImpuestos($renglonIva->getOtrosImpuestos() + abs($renglon->getMonto()));
                        $renglonIva->setOtrosImpuestos($renglonIva->getOtrosImpuestos() + $renglon->getMonto());
                        break;
                }

                $em->persist($gastoBancario);
            }

            $mensajeInvalido = $renglonIva->getMensajeInvalido($renglonIva);
            if ($mensajeInvalido['error']) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => $mensajeInvalido['mensaje']));
            }

            $renglonIva->setResta();
            $renglonIva->setProrrateo();

            // Persisto la entidad
            $em->persist($renglonIva);

            // Genero el asiento contable y presupuestario
            $resultArray = $this
                    ->generarAsientosConciliacion($renglones, true, $conciliacion, $fecha_comprobante);

            // Si el asiento presupuestario falló
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }

            // Si no hubo errores en los asientos
            if ($resultArray['numeroAsiento'] != -1) {

                $em->flush();
                
                $asientoContable = $em
                        ->getRepository('ADIFContableBundle:AsientoContable')
                        ->findOneBy(array('numeroAsiento' => $resultArray['numeroAsiento']));
                
                if($asientoContable ) {
                    if ( count($asientoContable->getRenglonesAsientoContable()) > 0 ) {
                        foreach($renglones as $renglon) {
                            $renglon->setAsientoContableConciliacion($asientoContable);
                            $renglon->setFechaConciliacion(new \DateTime());
                            $em->persist($renglon);
                            $this->logger->info("Renglon ID = " . $renglon->getId() . " Asiento contable conciliacion ID = " . $asientoContable->getId());
                        }
                    } else {
                        throw new \Exception("Se produjo un error al generar el asiento contable.");
                    }
                }
                
                $em->flush();
                $em->getConnection()->commit();
                
            } else {
                throw new \Exception("Se produjo un error al generar el asiento contable.");
            }
            
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->close();

            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => $e->getMessage()));
        }

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    /**
     * 
     * @param type $ids_renglones
     * @param type $esConciliacion
     * @param type $conciliacion
     * @param type $fechaContable
     * @return type
     */
    private function generarAsientosConciliacion($ids_renglones, $esConciliacion, $conciliacion, $fechaContable = null) {

        return $this->get('adif.asiento_service')
                        ->generarAsientosConciliacion($ids_renglones, $this->getUser(), $esConciliacion, $conciliacion, $fechaContable);
    }

    /**
     * Devuelve los renglones del extracto que estén pendientes
     * 
     */
    private function getRenglonesPendientesExtracto($em, $conciliacion) {
        //Consulta parecida a la del método indexTableAction del RenglonConciliacionController
        //$estado = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->findOneByDenominacion(ConstanteEstadoRenglonConciliacion::ESTADO_PENDIENTE);                
        //return $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->findBy(array('estadoRenglonConciliacion' => $estado));        
        $repository = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion');
        $query = $repository->createQueryBuilder('r')
                ->innerJoin('r.estadoRenglonConciliacion', 'e')
                ->innerJoin('r.importacionConciliacion', 'i')
                ->innerJoin('i.conciliacion', 'c')
                ->where('e.denominacion = :estado and c.id = :id')
                ->setParameters(array('estado' => ConstanteEstadoRenglonConciliacion::ESTADO_PENDIENTE, 'id' => $conciliacion->getId()))
                ->orderBy('r.fechaMovimientoBancario', 'ASC')
                ->getQuery();
        return $query->getResult();
    }

    /**
     * Cierra la conciliación
     *
     * @Route("/cerrar_conciliacion/{id}", name="cerrar_conciliacion")
     * @Method("PUT")
     */
    public function cerrarConciliacionAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id);

        $fecha_cierre = new \DateTime();
        if ($conciliacion->getFechaFin() < $fecha_cierre) {
            if ($conciliacion->getFechaFin()->format('Y-m-d') == $conciliacion->getFechaExtracto()->format('Y-m-d')) {
                $partidasSuman = 0;
                $partidasRestan = 0;
                $renglonesMayor = $this->getRenglonesPendientesMayor($conciliacion);
                
                foreach ($renglonesMayor as $renglonMayor) {
                    $monto = $renglonMayor->getMontoMovimiento($conciliacion->getCuenta());
                    //$monto > 0 ? $partidas += $monto : $partidas += (-1) * $monto; // HABER : DEBE
                    ($monto > 0) ? $partidasRestan += $monto : $partidasSuman += (-1) * $monto;
                    if ($conciliacion->getEsReAbierta()) {
                        $em->remove($renglonMayor);
                        $conciliacion->removePartidasConciliatoriasMayor($renglonMayor);
                    }
                    $conciliacion->addPartidasConciliatoriasMayor($renglonMayor);
                }

                
                $renglonesExtracto = $this->getRenglonesPendientesExtracto($em, $conciliacion);
                foreach ($renglonesExtracto as $renglonExtracto) {
                    $monto = $renglonExtracto->getMonto();
                    //$monto < 0 ? $partidas += $monto : $partidas += (-1) * $monto;  // HABER : DEBE
                    ($monto > 0) ? $partidasRestan += $monto : $partidasSuman += (-1) * $monto;
                    
                    if ($conciliacion->getEsReAbierta()) {
                        $em->remove($renglonExtracto);
                        $conciliacion->removePartidasConciliatoriasExtracto($renglonExtracto);
                    }
                    
                    $conciliacion->addPartidasConciliatoriasExtracto($renglonExtracto);
                }

                $partidas = $partidasSuman - $partidasRestan;

                $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $conciliacion->getFechaFin()->format('d/m/Y') . ' 23:59:59');

                $saldoMayor = $em->getRepository('ADIFContableBundle:CuentaContable')->getSaldoALaFecha($conciliacion->getCuenta()->getIdCuentaContable(), $fecha_fin);
                $conciliacion->setFechaCierre($fecha_cierre);
                $conciliacion->setMontoPartidasConciliatorias($partidas);
                $conciliacion->setSaldoMayor($saldoMayor);
                $saldoContable = round($conciliacion->getSaldoExtractoEnPesos(),2) + $partidas;
                $diferencia = $saldoContable - $saldoMayor;
                //            echo $conciliacion->getSaldoExtracto().'<br>';
                //            echo $partidas.'<br>';
                //            echo $saldoMayor;
                //$epsilon = 0.00000001;
                $epsilon = 0.00001;
                

                if (abs($diferencia) < $epsilon) {
                    $em->flush();
                    return $this->redirect($this->generateUrl('conciliacion'));
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'La diferencia entre el saldo contable y el saldo bancario no dió $0');
                }
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Para poder cerrar una conciliaci&oacute;n la fecha del extracto tiene que ser igual a la fecha de fin de la conciliaci&oacute;n');
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Las conciliaciones deben ser cerradas al menos el día siguiente a la fecha de cierre');
        }
        return $this->redirect($this->generateUrl('conciliacion'));
    }

    /**
     * Devuelve los renglones pendientes del mayor
     * @return ArrayCollection <MovimientoConciliable>
     */
    private function getRenglonesPendientesMayor($conciliacion) {
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $conciliacion->getFechaFin()->format('d/m/Y') . ' 23:59:59');
        return $this->getRenglonesMayor($conciliacion->getId(), null, $fecha_fin, false);
    }

    /**
     * Genera el array para la previsualizacion de la conciliacion HTML y PDF
     * además de salvar los valores calculados
     * 
     */
    private function generarArrayPrevisualizacion($id_conciliacion) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id_conciliacion);
        if (!$conciliacion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $renglonesMayor = $this->getRenglonesPendientesMayor($conciliacion);
        $renglonesExtracto = $this->getRenglonesPendientesExtracto($em, $conciliacion);

        $totalPartidasSuman = 0;
        $totalPartidasRestan = 0;

        $renglonesMayorSuman = [];
        $renglonesMayorRestan = [];
        foreach ($renglonesMayor as $renglonM) {
            //if ($renglonM->getFecha()->format('d/m/Y') <= $conciliacion->getFechaFin()->format('d/m/Y')) {
            $monto = $renglonM->getMontoMovimiento($conciliacion->getCuenta());
            if ($monto > 0) {
                // ESTÁ EN EL HABER
                $renglonesMayorRestan[] = $renglonM;
                $totalPartidasRestan += $monto;
            } else {
                // ESTÁ EN EL DEBE
                $renglonesMayorSuman[] = $renglonM;
                $totalPartidasSuman += (-1) * $monto;
            }
            //}    
        }

        $renglonesExtractoSuman = [];
        $renglonesExtractoRestan = [];
        foreach ($renglonesExtracto as $renglonE) {
            $monto = $renglonE->getMonto();
            if ($monto > 0) {
                // ESTÁ EN EL HABER                
                $renglonesExtractoRestan[] = $renglonE;
                $totalPartidasRestan += $monto;
            } else {
                // ESTÁ EN EL DEBE                
                $renglonesExtractoSuman[] = $renglonE;
                $totalPartidasSuman += (-1) * $monto;
            }
        }
        //$totalPartidasRestan = abs($totalPartidasRestan);
        $conciliacion->setMontoPartidasConciliatorias($totalPartidasSuman - $totalPartidasRestan);
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $conciliacion->getFechaFin()->format('d/m/Y') . ' 23:59:59');
        $saldoMayor = $em->getRepository('ADIFContableBundle:CuentaContable')->getSaldoALaFecha($conciliacion->getCuenta()->getIdCuentaContable(), $fecha_fin);

        $conciliacion->setSaldoMayor($saldoMayor);
        $em->flush();

        return array('conciliacion' => $conciliacion,
            'renglonesMayorSuman' => $renglonesMayorSuman,
            'renglonesMayorRestan' => $renglonesMayorRestan,
            'renglonesExtractoSuman' => $renglonesExtractoSuman,
            'renglonesExtractoRestan' => $renglonesExtractoRestan,
            'totalPartidasSuman' => $totalPartidasSuman,
            'totalPartidasRestan' => $totalPartidasRestan,
            'cuentaBancaria' => $conciliacion->getCuenta());
    }
    
    /**
     * Genera el array para la previsualizacion de la conciliacion HTML y PDF
     * además de salvar los valores calculados
     * 
     */
    private function generarArrayPrevisualizacionCerrada($id_conciliacion) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conciliacion = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\Conciliacion')->find($id_conciliacion);
        /* @var $conciliacion Conciliacion */
        if (!$conciliacion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\Conciliacion.');
        }

        $renglonesMayor = $conciliacion->getPartidasConciliatoriasMayor();
        $renglonesExtracto = $conciliacion->getPartidasConciliatoriasExtracto();

        $totalPartidasSuman = 0;
        $totalPartidasRestan = 0;

        $renglonesMayorSuman = [];
        $renglonesMayorRestan = [];
        foreach ($renglonesMayor as $renglonM) {
            $monto = $renglonM->getMontoMovimiento($conciliacion->getCuenta());
            if ($monto > 0) {
                // ESTÁ EN EL HABER
                $renglonesMayorRestan[] = $renglonM;
                $totalPartidasRestan += $monto;
            } else {
                // ESTÁ EN EL DEBE
                $renglonesMayorSuman[] = $renglonM;
                $totalPartidasSuman += (-1) * $monto;
            }   
        }

        $renglonesExtractoSuman = [];
        $renglonesExtractoRestan = [];
        foreach ($renglonesExtracto as $renglonE) {
            $monto = $renglonE->getMonto();
            if ($monto > 0) {
                // ESTÁ EN EL HABER                
                $renglonesExtractoRestan[] = $renglonE;
                $totalPartidasRestan += $monto;
            } else {
                // ESTÁ EN EL DEBE                
                $renglonesExtractoSuman[] = $renglonE;
                $totalPartidasSuman += (-1) * $monto;
            }
        }
        
        $conciliacion->setMontoPartidasConciliatorias($totalPartidasSuman - $totalPartidasRestan);
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $conciliacion->getFechaFin()->format('d/m/Y') . ' 23:59:59');
        $saldoMayor = $em->getRepository('ADIFContableBundle:CuentaContable')->getSaldoALaFecha($conciliacion->getCuenta()->getIdCuentaContable(), $fecha_fin);

        $conciliacion->setSaldoMayor($saldoMayor);
        $em->flush();

        return array('conciliacion' => $conciliacion,
            'renglonesMayorSuman' => $renglonesMayorSuman,
            'renglonesMayorRestan' => $renglonesMayorRestan,
            'renglonesExtractoSuman' => $renglonesExtractoSuman,
            'renglonesExtractoRestan' => $renglonesExtractoRestan,
            'totalPartidasSuman' => $totalPartidasSuman,
            'totalPartidasRestan' => $totalPartidasRestan,
            'cuentaBancaria' => $conciliacion->getCuenta());
    }

    /**
     * Armar la previsualización de la conciliación.
     *
     * @Route("/armar_previsualizacion/", name="armar_previsualizacion")
     * @Method("POST")
     */
    public function armarPrevisualizacionoAction(Request $request) {
        $id_conciliacion = $request->request->get('id_conciliacion');

        return $this->render('ADIFContableBundle:ConciliacionBancaria/Conciliacion:previsualizacion.html.twig', $this->generarArrayPrevisualizacion($id_conciliacion));
        //return new JsonResponse(array('status' => 'OK'));        
    }

}
