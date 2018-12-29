<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\AlicuotaIva;
use ADIF\ContableBundle\Form\AlicuotaIvaType;

/**
 * AlicuotaIva controller.
 *
 * @Route("/alicuotaiva")
 */
class AlicuotaIvaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Alicuotas IVA' => $this->generateUrl('alicuotaiva')
        );
    }

    /**
     * Lists all AlicuotaIva entities.
     *
     * @Route("/", name="alicuotaiva")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:AlicuotaIva')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Alicuotas IVA'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Alicuotas IVA',
            'page_info' => 'Lista de alicuotas IVA'
        );
    }

    /**
     * Creates a new AlicuotaIva entity.
     *
     * @Route("/insertar", name="alicuotaiva_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AlicuotaIva:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new AlicuotaIva();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('alicuotaiva'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear alicuota IVA',
        );
    }

    /**
     * Creates a form to create a AlicuotaIva entity.
     *
     * @param AlicuotaIva $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AlicuotaIva $entity) {
        $form = $this->createForm(new AlicuotaIvaType(), $entity, array(
            'action' => $this->generateUrl('alicuotaiva_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AlicuotaIva entity.
     *
     * @Route("/crear", name="alicuotaiva_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new AlicuotaIva();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear alicuota IVA'
        );
    }

    /**
     * Finds and displays a AlicuotaIva entity.
     *
     * @Route("/{id}", name="alicuotaiva_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AlicuotaIva')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIva.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Detalle'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver alicuota IVA'
        );
    }

    /**
     * Displays a form to edit an existing AlicuotaIva entity.
     *
     * @Route("/editar/{id}", name="alicuotaiva_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:AlicuotaIva:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AlicuotaIva')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIva.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar alicuota IVA'
        );
    }

    /**
     * Creates a form to edit a AlicuotaIva entity.
     *
     * @param AlicuotaIva $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AlicuotaIva $entity) {
        $form = $this->createForm(new AlicuotaIvaType(), $entity, array(
            'action' => $this->generateUrl('alicuotaiva_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AlicuotaIva entity.
     *
     * @Route("/actualizar/{id}", name="alicuotaiva_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:AlicuotaIva:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AlicuotaIva')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIva.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('alicuotaiva'));
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
            'page_title' => 'Editar alicuota IVA'
        );
    }

    /**
     * Deletes a AlicuotaIva entity.
     *
     * @Route("/borrar/{id}", name="alicuotaiva_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:AlicuotaIva')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIva.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('alicuotaiva'));
    }

}
