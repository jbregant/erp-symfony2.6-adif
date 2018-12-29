<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Facturacion\Talonario;
use ADIF\ContableBundle\Form\Facturacion\TalonarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Facturacion\Talonario controller.
 *
 * @Route("/talonarios")
 */
class TalonarioController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Talonarios' => $this->generateUrl('talonarios')
        );
    }

    /**
     * Lists all Facturacion\Talonario entities.
     *
     * @Route("/", name="talonarios")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_TALONARIOS')")
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Talonarios'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Talonario',
            'page_info' => 'Lista de talonarios'
        );
    }

    /**
     * Tabla para Talonario.
     *
     * @Route("/index_table/", name="talonarios_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Talonarios'] = null;

        return $this->render('ADIFContableBundle:Facturacion\Talonario:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Facturacion\Talonario entity.
     *
     * @Route("/insertar", name="talonarios_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\Talonario:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Talonario();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            if ($this->chequearSuperposicion($em, $entity)) {
                $this->get('session')->getFlashBag()->add(
                        'error', 'Hay superposición de talonarios para la letra ' . $entity->getLetraComprobante() . ' - Punto de venta ' . $entity->getPuntoVenta()
                );
                $request->attributes->set('form-error', true);
            } else {
                $entity->setNumeroSiguiente($entity->getNumeroDesde());
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('talonarios'));
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
            'page_title' => 'Crear talonario',
        );
    }

    /**
     * Creates a form to create a Facturacion\Talonario entity.
     *
     * @param Talonario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Talonario $entity) {
        $form = $this->createForm(new TalonarioType(), $entity, array(
            'action' => $this->generateUrl('talonarios_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Facturacion\Talonario entity.
     *
     * @Route("/crear", name="talonarios_new")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_TALONARIOS')")
     */
    public function newAction() {
        $entity = new Talonario();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear talonario'
        );
    }

    /**
     * Finds and displays a Facturacion\Talonario entity.
     *
     * @Route("/{id}", name="talonarios_show")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_TALONARIOS')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\Talonario.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Talonario'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver talonario'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\Talonario entity.
     *
     * @Route("/editar/{id}", name="talonarios_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Talonario:new.html.twig")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_TALONARIOS')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\Talonario.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar talonario'
        );
    }

    /**
     * Creates a form to edit a Facturacion\Talonario entity.
     *
     * @param Talonario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Talonario $entity) {
        $form = $this->createForm(new TalonarioType(), $entity, array(
            'action' => $this->generateUrl('talonarios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\Talonario entity.
     *
     * @Route("/actualizar/{id}", name="talonarios_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\Talonario:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\Talonario.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($this->chequearSuperposicion($em, $entity, true)) {
                $this->get('session')->getFlashBag()->add(
                        'error', 'Hay superposición de talonarios para la letra ' . $entity->getLetraComprobante() . ' - Punto de venta ' . $entity->getPuntoVenta()
                );
                $request->attributes->set('form-error', true);
            } else {
                $em->flush();
                return $this->redirect($this->generateUrl('talonarios'));
            }
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar talonario'
        );
    }

    /**
     * Deletes a Facturacion\Talonario entity.
     *
     * @Route("/borrar/{id}", name="talonarios_delete")
     * @Method("DELETE")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_TALONARIOS')")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\Talonario.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('talonarios'));
    }

    /**
     * 
     * Se valida que no haya superposicion de talonarios
     * 
     * @param type $em
     * @param Talonario $entity
     * @param type $edit
     * @return type
     */
    private function chequearSuperposicion($em, Talonario $entity, $edit = false) {
        $haySuperposicion = false;

        $talonarios = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->findBy(
                array(
                    'puntoVenta' => $entity->getPuntoVenta(),
                    'letraComprobante' => $entity->getLetraComprobante(),
                    'tipoComprobante' => $entity->getTipoComprobante()
                )
        );

        foreach ($talonarios as $talonario) {
            if ($edit) {
                if ($entity->getId() != $talonario->getId()) {
                    $haySuperposicion |= ($entity->getNumeroHasta() >= $talonario->getNumeroDesde() && $talonario->getNumeroHasta() >= $entity->getNumeroDesde());
                }
            } else {
                $haySuperposicion |= ($entity->getNumeroHasta() >= $talonario->getNumeroDesde() && $talonario->getNumeroHasta() >= $entity->getNumeroDesde());
            }
        }

        return $haySuperposicion;
    }

}
