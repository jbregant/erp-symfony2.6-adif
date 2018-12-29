<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\MovimientoBancario;
use ADIF\ContableBundle\Form\MovimientoBancarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * MovimientoBancario controller.
 *
 * @Route("/movimientobancario")
 */
class MovimientoBancarioController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Movimientos bancarios' => $this->generateUrl('movimientobancario')
        );
    }

    /**
     * Lists all MovimientoBancario entities.
     *
     * @Route("/", name="movimientobancario")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Movimientos bancarios'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Movimientos bancarios',
            'page_info' => 'Lista de movimientos bancarios'
        );
    }

    /**
     * Tabla para MovimientoBancario.
     *
     * @Route("/index_table/", name="movimientobancario_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:MovimientoBancario')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Movimientos bancarios'] = null;

        return $this->render('ADIFContableBundle:MovimientoBancario:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new MovimientoBancario entity.
     *
     * @Route("/insertar", name="movimientobancario_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:MovimientoBancario:new.html.twig")
     */
    public function createAction(Request $request) {

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $movimientoBancario = new MovimientoBancario();

        $form = $this->createCreateForm($movimientoBancario);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Genero la AutorizacionContable            
            $ordenPagoService = $this->get('adif.orden_pago_service');

            $importe = $movimientoBancario->getMonto();
            $concepto = $movimientoBancario->getConcepto();

            $autorizacionContable = $ordenPagoService
                    ->crearAutorizacionContableMovimientoBancario($em, $movimientoBancario, $importe, $concepto);

            // Persisto la entidad
            $em->persist($movimientoBancario);

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {

                $em->flush();

                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()
                        ->add('success', "El movimiento bancario se gener&oacute; con &eacute;xito");

                $this->get('session')->getFlashBag()
                        ->add('info', "Se gener&oacute; la autorizaci&oacute;n "
                                . "contable con &eacute;xito, con un "
                                . "importe de $ " . number_format($importe, 2, ',', '.'));

                $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                        . $this->generateUrl($autorizacionContable->getPathAC()
                                . '_print', ['id' => $autorizacionContable->getId()])
                        . '" class="link-imprimir-op">aqu&iacute;</a>';

                $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);
            } //.
            catch (\Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }

            return $this->redirect($this->generateUrl('movimientobancario'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        $cuentasBancariasADIF = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);

        return array(
            'entity' => $movimientoBancario,
            'cuentasBancariasADIF' => $cuentasBancariasADIF,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear movimiento bancario',
        );
    }

    /**
     * Creates a form to create a MovimientoBancario entity.
     *
     * @param MovimientoBancario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MovimientoBancario $entity) {
        $form = $this->createForm(new MovimientoBancarioType(), $entity, array(
            'action' => $this->generateUrl('movimientobancario_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Crea un formulario para anular una entidad MovimientoBancario. 
     *
     * @param MovimientoBancario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAnularForm(MovimientoBancario $entity) {
        $form = $this->createForm(new MovimientoBancarioType(), $entity, array(
            'action' => $this->generateUrl('movimientobancario_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Anular'));

        return $form;
    }

    /**
     * Displays a form to create a new MovimientoBancario entity.
     *
     * @Route("/crear", name="movimientobancario_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entity = new MovimientoBancario();
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
            'page_title' => 'Crear movimiento bancario'
        );
    }

    /**
     * Finds and displays a MovimientoBancario entity.
     *
     * @Route("/{id}", name="movimientobancario_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoBancario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoBancario.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Movimiento bancario'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver movimiento bancario'
        );
    }

    /**
     * Displays a form to edit an existing MovimientoBancario entity.
     *
     * @Route("/editar/{id}", name="movimientobancario_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:MovimientoBancario:new.html.twig")
     */
    public function editAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoBancario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoBancario.');
        }

        $editForm = $this->createEditForm($entity);

        $cuentasBancariasADIF = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'cuentasBancariasADIF' => $cuentasBancariasADIF,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar movimiento bancario'
        );
    }

    /**
     * Creates a form to edit a MovimientoBancario entity.
     *
     * @param MovimientoBancario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MovimientoBancario $entity) {
        $form = $this->createForm(new MovimientoBancarioType(), $entity, array(
            'action' => $this->generateUrl('movimientobancario_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing MovimientoBancario entity.
     *
     * @Route("/actualizar/{id}", name="movimientobancario_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:MovimientoBancario:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoBancario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoBancario.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('movimientobancario'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $cuentasBancariasADIF = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'cuentasBancariasADIF' => $cuentasBancariasADIF,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar movimiento bancario'
        );
    }

    /**
     * Deletes a MovimientoBancario entity.
     *
     * @Route("/borrar/{id}", name="movimientobancario_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:MovimientoBancario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoBancario.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('movimientobancario'));
    }

    /**
     * Muestra un formulario para anular una entidad MovimientoBancario existente.
     *
     * @Route("/anular/{id}", name="movimientobancario_anular")
     * @Method("GET")
     * @Template("ADIFContableBundle:MovimientoBancario:anular.html.twig")
     */
    public function anularAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entityPpal = $em->getRepository('ADIFContableBundle:MovimientoBancario')->find($id);

        if (!$entityPpal) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoBancario.');
        }

        $entityNueva = new MovimientoBancario();
        $entityNueva->setCuentaOrigen($entityPpal->getCuentaDestino());
        $entityNueva->setCuentaDestino($entityPpal->getCuentaOrigen());
        $entityNueva->setMonto($entityPpal->getMonto());

        $anularForm = $this->createAnularForm($entityNueva);

        $bread = $this->base_breadcrumbs;
        $bread['Anular'] = null;

        return array(
//'entityPpal' => $entityPpal,
            'entityNueva' => $entityNueva,
            'form' => $anularForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Anular movimiento bancario'
        );
    }

    /**
     *
     * @Route("/editar_fecha/", name="movimientobancario_editar_fecha")
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
