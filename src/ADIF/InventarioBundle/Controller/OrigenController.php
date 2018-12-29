<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Origen;
use ADIF\InventarioBundle\Form\OrigenType;

/**
 * Origen controller.
 *
 * @Route("/origen")
  */
class OrigenController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuración' => '',
            'Materiales Nuevos y Producidos de Obra' => '',
            'Orígenes' => $this->generateUrl('origen')
        );
    }
    /**
     * Lists all Origen entities.
     *
     * @Route("/", name="origen")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Orígenes'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Orígenes',
            'page_info' => 'Lista de Orígenes'
        );
    }

    /**
     * Tabla para Origen .
     *
     * @Route("/index_table/", name="origen_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Origen')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Orígenes'] = null;

    return $this->render('ADIFInventarioBundle:Origen:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Origen entity.
     *
     * @Route("/insertar", name="origen_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Origen:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Origen();
        $entity->setIdEmpresa(1);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Hardcodeo el id de la empresa
            $entity->setIdEmpresa(1);

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('origen'));
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
            'page_title' => 'Crear Origen',
        );
    }

    /**
    * Creates a form to create a Origen entity.
    *
    * @param Origen $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Origen $entity)
    {
        $form = $this->createForm(new OrigenType(), $entity, array(
            'action' => $this->generateUrl('origen_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Origen entity.
     *
     * @Route("/crear", name="origen_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Origen();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Origen'
        );
}

    /**
     * Finds and displays a Origen entity.
     *
     * @Route("/{id}", name="origen_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Origen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Origen.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Orígenes'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Origen'
        );
    }

    /**
     * Displays a form to edit an existing Origen entity.
     *
     * @Route("/editar/{id}", name="origen_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Origen:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Origen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Origen.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Origen'
        );
    }

    /**
    * Creates a form to edit a Origen entity.
    *
    * @param Origen $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Origen $entity)
    {
        $form = $this->createForm(new OrigenType(), $entity, array(
            'action' => $this->generateUrl('origen_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Origen entity.
     *
     * @Route("/actualizar/{id}", name="origen_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Origen:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Origen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Origen.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('origen'));
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
            'page_title' => 'Editar Origen'
        );
    }
    /**
     * Deletes a Origen entity.
     *
     * @Route("/borrar/{id}", name="origen_delete")
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
         ->where('u.origen = :id')
         ->setParameter('id', $id);
         $countInventario = $qbInventario->getQuery()->getSingleScalarResult();

         return ($countInventario) == 0;
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
