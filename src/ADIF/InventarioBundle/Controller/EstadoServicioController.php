<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\EstadoServicio;
use ADIF\InventarioBundle\Form\EstadoServicioType;

/**
 * EstadoServicio controller.
 *
 * @Route("/estadoservicio")
  */
class EstadoServicioController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuración' => '',
            'Materiales Nuevos y Producidos de Obra' => '',
            'Estado de Situación' => $this->generateUrl('estadoservicio')
        );
    }
    /**
     * Lists all EstadoServicio entities.
     *
     * @Route("/", name="estadoservicio")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Estado de Situación'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Estado de Situación',
            'page_info' => 'Lista de Estado de Situación'
        );
    }

    /**
     * Tabla para EstadoServicio .
     *
     * @Route("/index_table/", name="estadoservicio_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:EstadoServicio')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estado de Situación'] = null;

    return $this->render('ADIFInventarioBundle:EstadoServicio:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new EstadoServicio entity.
     *
     * @Route("/insertar", name="estadoservicio_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:EstadoServicio:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoServicio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadoservicio'));
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
            'page_title' => 'Crear Estado de Situación',
        );
    }

    /**
    * Creates a form to create a EstadoServicio entity.
    *
    * @param EstadoServicio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EstadoServicio $entity)
    {
        $form = $this->createForm(new EstadoServicioType(), $entity, array(
            'action' => $this->generateUrl('estadoservicio_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoServicio entity.
     *
     * @Route("/crear", name="estadoservicio_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EstadoServicio();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Estado de Situación'
        );
}

    /**
     * Finds and displays a EstadoServicio entity.
     *
     * @Route("/{id}", name="estadoservicio_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:EstadoServicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoServicio.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Estado de Situación'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Estado de Situación'
        );
    }

    /**
     * Displays a form to edit an existing EstadoServicio entity.
     *
     * @Route("/editar/{id}", name="estadoservicio_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:EstadoServicio:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:EstadoServicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoServicio.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Estado de Situación'
        );
    }

    /**
    * Creates a form to edit a EstadoServicio entity.
    *
    * @param EstadoServicio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoServicio $entity)
    {
        $form = $this->createForm(new EstadoServicioType(), $entity, array(
            'action' => $this->generateUrl('estadoservicio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing EstadoServicio entity.
     *
     * @Route("/actualizar/{id}", name="estadoservicio_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:EstadoServicio:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:EstadoServicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoServicio.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadoservicio'));
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
            'page_title' => 'Editar Estado de Situación'
        );
    }
    /**
     * Deletes a EstadoServicio entity.
     *
     * @Route("/borrar/{id}", name="estadoservicio_delete")
     * @Method("GET")
     */
     public function deleteAction($id)
     {
         return parent::baseDeleteAction($id);

     }

     public function validateLocalDeleteById($id) {

         $em = $this->getDoctrine()->getManager($this->getEntityManager());

         //Inventario
         $qbInventario = $em
         ->getRepository('ADIFInventarioBundle:Inventario')
         ->createQueryBuilder('u')
         ->select('count(u.id)')
         ->where('u.estadoServicio = :id')
         ->setParameter('id', $id);
         $countInventario = $qbInventario->getQuery()->getSingleScalarResult();

         //Catalogo Material Rodante
         $qbCatalogoMR = $em
         ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
         ->createQueryBuilder('u')
         ->select('count(u.id)')
         ->where('u.idEstadoServicio = :id')
         ->setParameter('id', $id);
         $countCatalogoMR = $qbCatalogoMR->getQuery()->getSingleScalarResult();


         return ($countInventario+$countCatalogoMR) == 0;
     }

      /**
       *
       * @return type
       */
      public function getSessionMessage() {
          return 'No se pudo eliminar la Linea '
                  . 'ya que es referenciada por otras entidades.';
      }
}
