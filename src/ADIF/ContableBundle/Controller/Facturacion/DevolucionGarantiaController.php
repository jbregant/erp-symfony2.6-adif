<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia;
use ADIF\ContableBundle\Form\Facturacion\DevolucionGarantiaType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * DevolucionGarantia controller.
 *
 * @Route("/devolucion_garantia")
 */
class DevolucionGarantiaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Devoluciones de garantía' => $this->generateUrl('devolucion_garantia')
        );
    }

    /**
     * Lists all Facturacion\DevolucionGarantia entities.
     *
     * @Route("/", name="devolucion_garantia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Devoluciones de garantía'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Devoluciones de garantía',
            'page_info' => 'Lista de devoluciones de garantía'
        );
    }

    /**
     * Tabla para Facturacion\DevolucionGarantia .
     *
     * @Route("/index_table/", name="devolucion_garantia_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\DevolucionGarantia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Devoluciones de garantía'] = null;

        return $this->render('ADIFContableBundle:Facturacion/DevolucionGarantia:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Facturacion\DevolucionGarantia entity.
     *
     * @Route("/insertar", name="devolucion_garantia_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\DevolucionGarantia:new.html.twig")
     */
    public function createAction(Request $request) {

        $devolucionGarantia = new DevolucionGarantia();

        $form = $this->createCreateForm($devolucionGarantia);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Genero la AutorizacionContable            
            $ordenPagoService = $this->get('adif.orden_pago_service');

            $importe = $devolucionGarantia->getImporte();
            $concepto = 'Devoluci&oacute;n de garant&iacute;a - Contrato '
                    . $devolucionGarantia->getCuponGarantia()->getContrato();

            $autorizacionContable = $ordenPagoService
                    ->crearAutorizacionContableDevolucionGarantia($em, $devolucionGarantia, $importe, $concepto);

            $em->persist($devolucionGarantia);

            $em->flush();

            $this->get('session')->getFlashBag()
                    ->add('success', "Se gener&oacute; la autorizaci&oacute;n "
                            . "contable con &eacute;xito, con un "
                            . "importe de $ " . number_format($importe, 2, ',', '.'));

            $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                    . $this->generateUrl($autorizacionContable->getPathAC()
                            . '_print', ['id' => $autorizacionContable->getId()])
                    . '" class="link-imprimir-op">aqu&iacute;</a>';

            $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);

            return $this->redirect($this->generateUrl('devolucion_garantia'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $devolucionGarantia,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear devolución de garantía',
        );
    }

    /**
     * Creates a form to create a Facturacion\DevolucionGarantia entity.
     *
     * @param DevolucionGarantia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DevolucionGarantia $entity) {
        $form = $this->createForm(new DevolucionGarantiaType(), $entity, array(
            'action' => $this->generateUrl('devolucion_garantia_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Facturacion\DevolucionGarantia entity.
     *
     * @Route("/crear", name="devolucion_garantia_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new DevolucionGarantia();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear devolución de garantía'
        );
    }

    /**
     * Finds and displays a Facturacion\DevolucionGarantia entity.
     *
     * @Route("/{id}", name="devolucion_garantia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\DevolucionGarantia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\DevolucionGarantia.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Devolución de garantía'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver devolución de garantía'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\DevolucionGarantia entity.
     *
     * @Route("/editar/{id}", name="devolucion_garantia_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\DevolucionGarantia:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\DevolucionGarantia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\DevolucionGarantia.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar devolución de garantía'
        );
    }

    /**
     * Creates a form to edit a Facturacion\DevolucionGarantia entity.
     *
     * @param DevolucionGarantia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DevolucionGarantia $entity) {
        $form = $this->createForm(new DevolucionGarantiaType(), $entity, array(
            'action' => $this->generateUrl('devolucion_garantia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\DevolucionGarantia entity.
     *
     * @Route("/actualizar/{id}", name="devolucion_garantia_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\DevolucionGarantia:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\DevolucionGarantia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\DevolucionGarantia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('devolucion_garantia'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar devolución de garantía'
        );
    }

    /**
     * Deletes a Facturacion\DevolucionGarantia entity.
     *
     * @Route("/borrar/{id}", name="devolucion_garantia_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\DevolucionGarantia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\DevolucionGarantia.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('devolucion_garantia'));
    }

}
