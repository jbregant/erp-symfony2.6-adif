<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\PortalProveedoresBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\PortalProveedoresBundle\Entity\Notificacion;
use ADIF\PortalProveedoresBundle\Entity\NotificacionUsuario;
use ADIF\PortalProveedoresBundle\Form\NotificacionType;

/**
 * Notificacion controller.
 *
 * @Route("/notificacion")
  */
class NotificacionController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Notificacion' => $this->generateUrl('notificacion')
        );
    }
    /**
     * Lists all Notificacion entities.
     *
     * @Route("/", name="notificacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Notificacion'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Notificacion',
            'page_info' => 'Lista de notificacion'
        );
    }

    /**
     * Tabla para Notificacion .
     *
     * @Route("/index_table/", name="notificacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['Notificacion'] = null;

    return $this->render('ADIFPortalProveedoresBundle:Notificacion:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Notificacion entity.
     *
     * @Route("/insertar", name="notificacion_create")
     * @Method("POST")
     * @Template("ADIFPortalProveedoresBundle:Notificacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Notificacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $em->persist($entity);

            $lista = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->findAllUsuarios();

            foreach ($lista as $data) { 

                $usuario = $em->getRepository('ADIFPortalProveedoresBundle:Usuario')->findOneById($data['idUsuario']);
                $notificacionUsuario = new NotificacionUsuario();
                $notificacionUsuario->setNotificacionIdnotificacion($entity);
                $notificacionUsuario->setIdProveedor($data['idProveedor']);
                $notificacionUsuario->setUsuarioIdusuario($usuario);
                $notificacionUsuario->setLeido(0); // default no leido

                $em->persist($notificacionUsuario);
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Se ha creado correctamente.');
            return $this->redirect($this->generateUrl('notificacion'));
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
            'page_title' => 'Crear Notificacion',
        );
    }

    /**
    * Creates a form to create a Notificacion entity.
    *
    * @param Notificacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Notificacion $entity)
    {
        $form = $this->createForm(new NotificacionType(), $entity, array(
            'action' => $this->generateUrl('notificacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Notificacion entity.
     *
     * @Route("/crear", name="notificacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Notificacion();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Notificacion'
        );
}

    /**
     * Finds and displays a Notificacion entity.
     *
     * @Route("/{id}", name="notificacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Notificacion.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Notificacion'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Notificacion'
        );
    }

    /**
     * Displays a form to edit an existing Notificacion entity.
     *
     * @Route("/editar/{id}", name="notificacion_edit")
     * @Method("GET")
     * @Template("ADIFPortalProveedoresBundle:Notificacion:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Notificacion.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Notificacion'
        );
    }

    /**
    * Creates a form to edit a Notificacion entity.
    *
    * @param Notificacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Notificacion $entity)
    {
        $form = $this->createForm(new NotificacionType(), $entity, array(
            'action' => $this->generateUrl('notificacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'validation_groups' => ['create'],
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Notificacion entity.
     *
     * @Route("/actualizar/{id}", name="notificacion_update")
     * @Method("PUT")
     * @Template("ADIFPortalProveedoresBundle:Notificacion:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Notificacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'La actualizaci&oacute;n se realiz&oacute; con &eacute;xito.');
            return $this->redirect($this->generateUrl('notificacion'));
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
            'page_title' => 'Editar Notificacion'
        );
    }

    /**
     *
     * Muestra los detalles de auditoria en las notificaciones 
     *
     * @Route("/auditoria/{id}", name="notificacion_auditoria")
     * @Method("GET")
     * @Template("ADIFPortalProveedoresBundle:Notificacion:show.auditoria.html.twig")
     */
    public function auditoriaAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Notificacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Notificacion'] = null;

        return array(
            'entity' => $entity,
            'id' => $id,
            'breadcrumbs' => $bread,
            'page_title' => 'Auditoria Notificacion'
        );
    }

    /**
     * Tabla para items Notificacion.
     *
     * @Route("/items_table/{id}/{tipo}", name="notificacion_items_table")
     * @Method("GET")
     */
    public function itemsTableAction($id, $tipo)
    {
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if($tipo == 'Auditoria'){
            $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->findNotificaciones($id);
        }elseif ($tipo == 'Detalle') {
            $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->findDetalles($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Notificacion Usuario.');
        }

        return $this->render("ADIFPortalProveedoresBundle:Notificacion:{$tipo}/items_table.html.twig", array(
            'entity' => $entity
        ));
    }

    /**
     * Deletes a Notificacion entity.
     *
     * @Route("/borrar/{id}", name="notificacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Notificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Notificacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('notificacion'));
    }
}
