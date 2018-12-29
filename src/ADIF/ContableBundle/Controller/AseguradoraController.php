<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Aseguradora;
use ADIF\ContableBundle\Form\AseguradoraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Aseguradora controller.
 *
 * @Route("/aseguradoras")
  */
class AseguradoraController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Aseguradora' => $this->generateUrl('aseguradoras')
        );
    }
    /**
     * Lists all Aseguradora entities.
     *
     * @Route("/", name="aseguradoras")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Aseguradora'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Aseguradora',
            'page_info' => 'Lista de aseguradora'
        );
    }

    /**
     * Tabla para Aseguradora .
     *
     * @Route("/index_table/", name="aseguradoras_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Aseguradora')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Aseguradora'] = null;

    return $this->render('ADIFContableBundle:Aseguradora:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Aseguradora entity.
     *
     * @Route("/insertar", name="aseguradoras_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Aseguradora:new.html.twig")
     * @Security("has_role('ROLE_MENU_ADMINISTRACION_FONDOS_COMPLETO')")
     */
    public function createAction(Request $request)
    {
        $entity = new Aseguradora();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('aseguradoras'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Aseguradora',
        );
    }

    /**
    * Creates a form to create a Aseguradora entity.
    *
    * @param Aseguradora $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Aseguradora $entity)
    {
        $form = $this->createForm(new AseguradoraType(), $entity, array(
            'action' => $this->generateUrl('aseguradoras_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Aseguradora entity.
     *
     * @Route("/crear", name="aseguradoras_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Aseguradora();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Aseguradora'
        );
}

    /**
     * Finds and displays a Aseguradora entity.
     *
     * @Route("/{id}", name="aseguradoras_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Aseguradora')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Aseguradora.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Aseguradora'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Aseguradora'
        );
    }

    /**
     * Displays a form to edit an existing Aseguradora entity.
     *
     * @Route("/editar/{id}", name="aseguradoras_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Aseguradora:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Aseguradora')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Aseguradora.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Aseguradora'
        );
    }

    /**
    * Creates a form to edit a Aseguradora entity.
    *
    * @param Aseguradora $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Aseguradora $entity)
    {
        $form = $this->createForm(new AseguradoraType(), $entity, array(
            'action' => $this->generateUrl('aseguradoras_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Aseguradora entity.
     *
     * @Route("/actualizar/{id}", name="aseguradoras_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Aseguradora:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Aseguradora')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Aseguradora.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('aseguradoras'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Aseguradora'
        );
    }
    /**
     * Deletes a Aseguradora entity.
     *
     * @Route("/borrar/{id}", name="aseguradoras_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Aseguradora')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Aseguradora.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('aseguradoras'));
    }
}
