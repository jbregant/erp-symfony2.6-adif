<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Servicio;
use ADIF\InventarioBundle\Form\ServicioType;

/**
 * Servicio controller.
 *
 * @Route("/servicio")
  */
class ServicioController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Materiales Rodantes' => '',
            'Servicios' => $this->generateUrl('servicio')
        );
    }
    /**
     * Lists all Servicio entities.
     *
     * @Route("/", name="servicio")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Servicios'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Servicio',
            'page_info' => 'Lista de Servicios'
        );
    }

    /**
     * Tabla para Servicio .
     *
     * @Route("/index_table/", name="servicio_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Servicio')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Servicios'] = null;

    return $this->render('ADIFInventarioBundle:Servicio:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Servicio entity.
     *
     * @Route("/insertar", name="servicio_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Servicio:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Servicio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('servicio'));
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
            'page_title' => 'Crear Servicio',
        );
    }

    /**
    * Creates a form to create a Servicio entity.
    *
    * @param Servicio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Servicio $entity)
    {
        $form = $this->createForm(new ServicioType(), $entity, array(
            'action' => $this->generateUrl('servicio_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Servicio entity.
     *
     * @Route("/crear", name="servicio_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Servicio();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Servicio'
        );
}

    /**
     * Finds and displays a Servicio entity.
     *
     * @Route("/{id}", name="servicio_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Servicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Servicio.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Servicios'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Servicio'
        );
    }

    /**
     * Displays a form to edit an existing Servicio entity.
     *
     * @Route("/editar/{id}", name="servicio_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Servicio:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Servicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Servicio.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Servicio'
        );
    }

    /**
    * Creates a form to edit a Servicio entity.
    *
    * @param Servicio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Servicio $entity)
    {
        $form = $this->createForm(new ServicioType(), $entity, array(
            'action' => $this->generateUrl('servicio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Servicio entity.
     *
     * @Route("/actualizar/{id}", name="servicio_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Servicio:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Servicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Servicio.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('servicio'));
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
            'page_title' => 'Editar Servicio'
        );
    }
    /**
     * Deletes a Servicio entity.
     *
     * @Route("/borrar/{id}", name="servicio_delete")
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

        // CatalogoMaterialesRodantes
        $qbMaterialesRodantes = $em
            ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.idEstadoServicio = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();


        return ($countMaterialesRodantes) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Servicio '
                . 'ya que es referenciado por otras entidades.';
    }
}
