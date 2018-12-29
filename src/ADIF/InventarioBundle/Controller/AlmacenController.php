<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Almacen;
use ADIF\InventarioBundle\Form\AlmacenType;
use ADIF\InventarioBundle\Entity\Linea;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Almacen controller.
 *
 * @Route("/almacen")
  */
class AlmacenController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuración' => '',
            'Materiales Nuevos y Producidos de Obra' => '',
            'Almacenes' => $this->generateUrl('almacen')
        );
    }
    /**
     * Lists all Almacen entities.
     *
     * @Route("/", name="almacen")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Almacenes'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Almacén',
            'page_info' => 'Lista de Almacén'
        );
    }

    /**
     * Tabla para Almacen .
     *
     * @Route("/index_table/", name="almacen_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Almacen')->findAllAlmacenes();

        $bread = $this->base_breadcrumbs;
        $bread['Almacenes'] = null;

        return $this->render('ADIFInventarioBundle:Almacen:index_table.html.twig', array(
            'entities' => $entities
        ));
    }
    /**
     * Creates a new Almacen entity.
     *
     * @Route("/insertar", name="almacen_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Almacen:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Almacen();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('almacen'));
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
            'page_title' => 'Crear Almacén',
        );
    }

    /**
    * Creates a form to create a Almacen entity.
    *
    * @param Almacen $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Almacen $entity)
    {
        $form = $this->createForm(new AlmacenType(), $entity, array(
            'action' => $this->generateUrl('almacen_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Almacen entity.
     *
     * @Route("/crear", name="almacen_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Almacen();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Almacén'
        );
}

    /**
     * Finds and displays a Almacen entity.
     *
     * @Route("/{id}", name="almacen_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Almacen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Almacen.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Almacenes'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Almacén'
        );
    }

    /**
     * Displays a form to edit an existing Almacen entity.
     *
     * @Route("/editar/{id}", name="almacen_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Almacen:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Almacen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Almacen.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Almacén'
        );
    }

    /**
    * Creates a form to edit a Almacen entity.
    *
    * @param Almacen $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Almacen $entity)
    {
        $form = $this->createForm(new AlmacenType(), $entity, array(
            'action' => $this->generateUrl('almacen_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Almacen entity.
     *
     * @Route("/actualizar/{id}", name="almacen_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Almacen:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Almacen')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Almacen.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('almacen'));
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
            'page_title' => 'Editar Almacén'
        );
    }
    /**
     * Deletes a Almacen entity.
     *
     * @Route("/borrar/{id}", name="almacen_delete")
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
         ->where('u.almacen = :id')
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

    /**
     * @Route("/lista_por_prov_linea", name="almacen_por_prov_linea")
     */
    public function getAlmacenesByProvinciaYLineaAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $idProvincia = $request->request->get('provincia');
            $idLinea = $request->request->get('linea');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $result = $em->getRepository('ADIFInventarioBundle:Almacen')->findByProvinciaYLinea($idProvincia, $idLinea);

            return new JsonResponse($result);
        }
    }
}
