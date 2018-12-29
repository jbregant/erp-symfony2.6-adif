<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\TiposMovimientoMaterial;
use ADIF\InventarioBundle\Form\TiposMovimientoMaterialType;

/**
 * TiposMovimientoMaterial controller.
 *
 * @Route("/tiposmovimientomaterial")
  */
class TiposMovimientoMaterialController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Materiales Nuevos y Producidos de Obra' => '',
            'Tipos de Movimiento de Material' => $this->generateUrl('tiposmovimientomaterial')
        );
    }
    /**
     * Lists all TiposMovimientoMaterial entities.
     *
     * @Route("/", name="tiposmovimientomaterial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Movimiento de Material'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de Movimiento de Material',
            'page_info' => 'Lista de Tipos de Movimiento de Material'
        );
    }

    /**
     * Tabla para TiposMovimientoMaterial .
     *
     * @Route("/index_table/", name="tiposmovimientomaterial_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:TiposMovimientoMaterial')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Movimiento de Material'] = null;

    return $this->render('ADIFInventarioBundle:TiposMovimientoMaterial:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TiposMovimientoMaterial entity.
     *
     * @Route("/insertar", name="tiposmovimientomaterial_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:TiposMovimientoMaterial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TiposMovimientoMaterial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tiposmovimientomaterial'));
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
            'page_title' => 'Crear Tipos de Movimiento de Material',
        );
    }

    /**
    * Creates a form to create a TiposMovimientoMaterial entity.
    *
    * @param TiposMovimientoMaterial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TiposMovimientoMaterial $entity)
    {
        $form = $this->createForm(new TiposMovimientoMaterialType(), $entity, array(
            'action' => $this->generateUrl('tiposmovimientomaterial_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TiposMovimientoMaterial entity.
     *
     * @Route("/crear", name="tiposmovimientomaterial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TiposMovimientoMaterial();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Tipos de Movimiento de Material'
        );
}

    /**
     * Finds and displays a TiposMovimientoMaterial entity.
     *
     * @Route("/{id}", name="tiposmovimientomaterial_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TiposMovimientoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipos de Movimiento de Material.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Movimiento de Material'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Tipos de Movimiento de Material'
        );
    }

    /**
     * Displays a form to edit an existing TiposMovimientoMaterial entity.
     *
     * @Route("/editar/{id}", name="tiposmovimientomaterial_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:TiposMovimientoMaterial:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TiposMovimientoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipos de Movimiento de Material.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipos de Movimiento de Material'
        );
    }

    /**
    * Creates a form to edit a TiposMovimientoMaterial entity.
    *
    * @param TiposMovimientoMaterial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TiposMovimientoMaterial $entity)
    {
        $form = $this->createForm(new TiposMovimientoMaterialType(), $entity, array(
            'action' => $this->generateUrl('tiposmovimientomaterial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TiposMovimientoMaterial entity.
     *
     * @Route("/actualizar/{id}", name="tiposmovimientomaterial_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:TiposMovimientoMaterial:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TiposMovimientoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipos de Movimiento de Material.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tiposmovimientomaterial'));
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
            'page_title' => 'Editar Tipos de Movimiento de Material'
        );
    }
    /**
     * Deletes a TiposMovimientoMaterial entity.
     *
     * @Route("/borrar/{id}", name="tiposmovimientomaterial_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFInventarioBundle:TiposMovimientoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipos de Movimiento de Material.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tiposmovimientomaterial'));
    }
}
