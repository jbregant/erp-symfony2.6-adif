<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\TipoMovimientoRodante;
use ADIF\InventarioBundle\Form\TipoMovimientoRodanteType;

/**
 * TipoMovimientoRodante controller.
 *
 * @Route("/tipomovimientorodante")
  */
class TipoMovimientoRodanteController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Materiales Rodantes' => '',
            'Tipos de Movimiento Rodante' => $this->generateUrl('tipomovimientorodante')
        );
    }
    /**
     * Lists all TipoMovimientoRodante entities.
     *
     * @Route("/", name="tipomovimientorodante")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Movimiento Rodante'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipo de Movimiento Rodante',
            'page_info' => 'Lista de Tipos de Movimiento Rodante'
        );
    }

    /**
     * Tabla para TipoMovimientoRodante .
     *
     * @Route("/index_table/", name="tipomovimientorodante_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:TipoMovimientoRodante')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Movimiento Rodante'] = null;

        return $this->render('ADIFInventarioBundle:TipoMovimientoRodante:index_table.html.twig', array(
            'entities' => $entities
        ));
    }
    /**
     * Creates a new TipoMovimientoRodante entity.
     *
     * @Route("/insertar", name="tipomovimientorodante_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:TipoMovimientoRodante:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoMovimientoRodante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipomovimientorodante'));
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
            'page_title' => 'Crear Tipo de Movimiento Rodante',
        );
    }

    /**
    * Creates a form to create a TipoMovimientoRodante entity.
    *
    * @param TipoMovimientoRodante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoMovimientoRodante $entity)
    {
        $form = $this->createForm(new TipoMovimientoRodanteType(), $entity, array(
            'action' => $this->generateUrl('tipomovimientorodante_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoMovimientoRodante entity.
     *
     * @Route("/crear", name="tipomovimientorodante_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoMovimientoRodante();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Tipo de Movimiento Rodante'
        );
}

    /**
     * Finds and displays a TipoMovimientoRodante entity.
     *
     * @Route("/{id}", name="tipomovimientorodante_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoMovimientoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMovimientoRodante.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Movimiento Rodante'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Tipo de Movimiento Rodante'
        );
    }

    /**
     * Displays a form to edit an existing TipoMovimientoRodante entity.
     *
     * @Route("/editar/{id}", name="tipomovimientorodante_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:TipoMovimientoRodante:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoMovimientoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMovimientoRodante.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipo de Movimiento Rodante'
        );
    }

    /**
    * Creates a form to edit a TipoMovimientoRodante entity.
    *
    * @param TipoMovimientoRodante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoMovimientoRodante $entity)
    {
        $form = $this->createForm(new TipoMovimientoRodanteType(), $entity, array(
            'action' => $this->generateUrl('tipomovimientorodante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TipoMovimientoRodante entity.
     *
     * @Route("/actualizar/{id}", name="tipomovimientorodante_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:TipoMovimientoRodante:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoMovimientoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMovimientoRodante.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipomovimientorodante'));
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
            'page_title' => 'Editar Tipo de Movimiento Rodante'
        );
    }
    /**
     * Deletes a TipoMovimientoRodante entity.
     *
     * @Route("/borrar/{id}", name="tipomovimientorodante_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFInventarioBundle:TipoMovimientoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMovimientoRodante.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipomovimientorodante'));
    }
}
