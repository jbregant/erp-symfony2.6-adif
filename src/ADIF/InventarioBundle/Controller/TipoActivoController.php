<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\TipoActivo;
use ADIF\InventarioBundle\Form\tipoActivoType;

/**
 * tipoActivo controller.
 *
 * @Route("/tipoactivo")
  */
class TipoActivoController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Tipos de Activo Lineal' => $this->generateUrl('tipoactivo')
        );
    }
    /**
     * Lists all tipoActivo entities.
     *
     * @Route("/", name="tipoactivo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Activo Lineal'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de Activo Lineal',
            'page_info' => 'Lista Tipos de Activo Lineal'
        );
    }

    /**
     * Tabla para tipoActivo .
     *
     * @Route("/index_table/", name="tipoactivo_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:TipoActivo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Activo Lineal'] = null;

    return $this->render('ADIFInventarioBundle:TipoActivo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new tipoActivo entity.
     *
     * @Route("/insertar", name="tipoactivo_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:TipoActivo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoActivo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipoactivo'));
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
            'page_title' => 'Crear Tipos de Activo Lineal',
        );
    }

    /**
    * Creates a form to create a tipoActivo entity.
    *
    * @param TipoActivo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoActivo $entity)
    {
        $form = $this->createForm(new tipoActivoType(), $entity, array(
            'action' => $this->generateUrl('tipoactivo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new tipoActivo entity.
     *
     * @Route("/crear", name="tipoactivo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoActivo();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipoActivo'
        );
}

    /**
     * Finds and displays a tipoActivo entity.
     *
     * @Route("/{id}", name="tipoactivo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoActivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad tipoActivo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Activo Lineal'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Tipos de Activo Lineal'
        );
    }

    /**
     * Displays a form to edit an existing tipoActivo entity.
     *
     * @Route("/editar/{id}", name="tipoactivo_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:TipoActivo:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoActivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad tipoActivo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipos de Activo Lineal'
        );
    }

    /**
    * Creates a form to edit a tipoActivo entity.
    *
    * @param TipoActivo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoActivo $entity)
    {
        $form = $this->createForm(new tipoActivoType(), $entity, array(
            'action' => $this->generateUrl('tipoactivo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing tipoActivo entity.
     *
     * @Route("/actualizar/{id}", name="tipoactivo_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:TipoActivo:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoActivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad tipoActivo.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipoactivo'));
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
            'page_title' => 'Editar Tipos de Activo Lineal'
        );
    }
    /**
     * Deletes a tipoActivo entity.
     *
     * @Route("/borrar/{id}", name="tipoactivo_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        return parent::baseDeleteAction($id);
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.tipoActivo = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Tipo de Activo '
                . 'ya que es referenciado por otras entidades.';
    }
}
