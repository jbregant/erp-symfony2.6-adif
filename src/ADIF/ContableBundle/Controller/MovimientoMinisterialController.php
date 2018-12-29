<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\MovimientoMinisterial;
use ADIF\ContableBundle\Form\MovimientoMinisterialType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * MovimientoMinisterial controller.
 *
 * @Route("/movimientoministerial")
 */
class MovimientoMinisterialController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Movimientos ministeriales' => $this->generateUrl('movimientoministerial')
        );
    }

    /**
     * Lists all MovimientoMinisterial entities.
     *
     * @Route("/", name="movimientoministerial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Movimientos ministeriales'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Movimientos ministeriales',
            'page_info' => 'Lista de movimientos ministeriales'
        );
    }

    /**
     * Tabla para MovimientoMinisterial.
     *
     * @Route("/index_table/", name="movimientoministerial_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:MovimientoMinisterial')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Movimientos ministeriales'] = null;

        return $this->render('ADIFContableBundle:MovimientoMinisterial:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new MovimientoMinisterial entity.
     *
     * @Route("/insertar", name="movimientoministerial_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:MovimientoMinisterial:new.html.twig")
     */
    public function createAction(Request $request) {

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entity = new MovimientoMinisterial();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            if (!$entity->getEsIngreso()) {
                // Genero la AutorizacionContable            
                $ordenPagoService = $this->get('adif.orden_pago_service');

                $importe = $entity->getMonto();
                $concepto = $entity->getConcepto();

                $autorizacionContable = $ordenPagoService
                        ->crearAutorizacionContableMovimientoMinisterial($em, $entity, $importe, $concepto);

                $em->persist($entity);

                $em->flush();

                $this->get('session')->getFlashBag()
                        ->add('info', "Se gener&oacute; la autorizaci&oacute;n "
                                . "contable con &eacute;xito, con un "
                                . "importe de $ " . number_format($importe, 2, ',', '.'));

                $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                        . $this->generateUrl($autorizacionContable->getPathAC()
                                . '_print', ['id' => $autorizacionContable->getId()])
                        . '" class="link-imprimir-op">aqu&iacute;</a>';

                $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);
            } else {
                // Persisto la entidad
                $em->persist($entity);

                // Genero el asiento contable
                $resultArray = $this->get('adif.asiento_service')
                        ->generarAsientoMovimientoMinisterial($entity, $this->getUser(), false);

                // Si el asiento presupuestario falló
                if (!empty($resultArray['mensajeErrorPresupuestario'])) {
                    $this->get('session')->getFlashBag()->add('error', $resultArray['mensajeErrorPresupuestario']);
                }

                // Si el asiento contable falló
                if (!empty($resultArray['mensajeErrorContable'])) {

                    $this->get('session')->getFlashBag()->add('error', $resultArray['mensajeErrorContable']);
                }

                // Si no hubo errores en los asientos
                if ($resultArray['numeroAsiento'] != -1) {

                    // Comienzo la transaccion
                    $em->getConnection()->beginTransaction();

                    try {

                        $em->flush();

                        $em->getConnection()->commit();

                        $this->get('session')->getFlashBag()
                                ->add('success', "El movimiento ministerial se gener&oacute; con &eacute;xito");

                        $dataArray = [
                            'data-id-movimiento' => $entity->getId()
                        ];

                        $this->get('adif.asiento_service')
                                ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray);
                    } //.
                    catch (\Exception $e) {

                        $em->getConnection()->rollback();
                        $em->close();

                        throw $e;
                    }
                }
            }

            return $this->redirect($this->generateUrl('movimientoministerial'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $cuentasBancariasADIF = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);


        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'cuentasBancariasADIF' => $cuentasBancariasADIF,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear movimiento ministerial',
        );
    }

    /**
     * Creates a form to create a MovimientoMinisterial entity.
     *
     * @param MovimientoMinisterial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MovimientoMinisterial $entity) {
        $form = $this->createForm(new MovimientoMinisterialType(), $entity, array(
            'action' => $this->generateUrl('movimientoministerial_create'),
            'method' => 'POST',
            'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new MovimientoMinisterial entity.
     *
     * @Route("/crear", name="movimientoministerial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entity = new MovimientoMinisterial();
        $form = $this->createCreateForm($entity);

        $cuentasBancariasADIF = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'cuentasBancariasADIF' => $cuentasBancariasADIF,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear movimiento ministerial'
        );
    }

    /**
     * Finds and displays a MovimientoMinisterial entity.
     *
     * @Route("/{id}", name="movimientoministerial_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoMinisterial.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Movimiento ministerial'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver movimiento ministerial'
        );
    }

    /**
     * Deletes a MovimientoMinisterial entity.
     *
     * @Route("/borrar/{id}", name="movimientoministerial_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:MovimientoMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoMinisterial.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('movimientoministerial'));
    }

    /**
     * Muestra un formulario para anular una entidad MovimientoMinisterial existente.
     *
     * @Route("/anular/{id}", name="movimientoministerial_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoMinisterial.');
        }

        if ($entity->getEsIngreso()) { //si es egreso se tiene que anular por medio de la OP
            if (!$entity->estaConciliado()) {

                $entity->setFechaAnulacion(new DateTime());

                // Genero el asiento contable
                $resultArray = $this->get('adif.asiento_service')
                        ->generarAsientoMovimientoMinisterial($entity, $this->getUser(), true);

                // Si no hubo errores en los asientos
                if ($resultArray['numeroAsiento'] != -1) {

                    // Comienzo la transaccion
                    $em->getConnection()->beginTransaction();

                    try {

                        $em->persist($entity);
                        $em->flush();

                        $em->getConnection()->commit();

                        $dataArray = [
                            'data-id-movimiento' => $entity->getId()
                        ];

                        $mensajeFlash = $this->get('adif.asiento_service')
                                ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                        $this->get('session')->getFlashBag()
                                ->add('success', "El movimiento ministerial se anul&oacute; con &eacute;xito. " . $mensajeFlash);
                    } //.
                    catch (\Exception $e) {

                        $em->getConnection()->rollback();
                        $em->close();

                        throw $e;
                    }
                }
            } else {

                $this->get('session')->getFlashBag()
                        ->add('success', "El movimiento ministerial se encuentra conciliado");
            }
        }

        return $this->redirect($this->generateUrl('movimientoministerial'));
    }

    /**
     *
     * @Route("/editar_fecha/", name="movimientoministerial_editar_fecha")
     */
    public function updateFechaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idMovimiento = $request->request->get('id_movimiento');

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        // Si existe el asiento Y el usuario logueado genera asientos contables
        if (!$asientoContable && false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        /* @var $movimiento \ADIF\ContableBundle\Entity\ConciliacionBancaria\MovimientoConciliable */
        $movimiento = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\MovimientoConciliable')
                ->find($idMovimiento);

        $movimiento->setFechaContable(\DateTime::createFromFormat('d/m/Y', $fecha));

        $em->persist($movimiento);

        $em->flush();

        return new Response();
    }

}
