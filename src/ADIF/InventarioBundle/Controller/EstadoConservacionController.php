<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\EstadoConservacion;
use ADIF\InventarioBundle\Form\EstadoConservacionType;

/**
 * EstadoConservacion controller.
 *
 * @Route("/EstadoConservacion")
  */
class EstadoConservacionController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'General' => '',
            'Estados de Conservaci&oacute;n' => $this->generateUrl('EstadoConservacion')
        );
    }
    /**
     * Lists all EstadoConservacion entities.
     *
     * @Route("/", name="EstadoConservacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Estados de Conservaci&oacute;n'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Estados de Conservaci&oacute;n',
            'page_info' => 'Lista de Estados de Conservaci&oacute;n'
        );
    }

    /**
     * Tabla para EstadoConservacion .
     *
     * @Route("/index_table/", name="EstadoConservacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:EstadoConservacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de Conservaci&oacute;n'] = null;

        return $this->render('ADIFInventarioBundle:EstadoConservacion:index_table.html.twig', array(
            'entities' => $entities
        ));
    }
    /**
     * Creates a new EstadoConservacion entity.
     *
     * @Route("/insertar", name="EstadoConservacion_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:EstadoConservacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoConservacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('EstadoConservacion'));
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
            'page_title' => 'Crear Estado de Conservaci&oacute;n',
        );
    }

    /**
    * Creates a form to create a EstadoConservacion entity.
    *
    * @param EstadoConservacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EstadoConservacion $entity)
    {
        $form = $this->createForm(new EstadoConservacionType(), $entity, array(
            'action' => $this->generateUrl('EstadoConservacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoConservacion entity.
     *
     * @Route("/crear", name="EstadoConservacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EstadoConservacion();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Estado de Conservaci&oacute;n'
        );
    }

    /**
     * Finds and displays a EstadoConservacion entity.
     *
     * @Route("/{id}", name="EstadoConservacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:EstadoConservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoConservacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Estados de Conservaci&oacute;n'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Estado de Conservaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing EstadoConservacion entity.
     *
     * @Route("/editar/{id}", name="EstadoConservacion_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:EstadoConservacion:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:EstadoConservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoConservacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Estado de Conservaci&oacute;n'
        );
    }

    /**
    * Creates a form to edit a EstadoConservacion entity.
    *
    * @param EstadoConservacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoConservacion $entity)
    {
        $form = $this->createForm(new EstadoConservacionType(), $entity, array(
            'action' => $this->generateUrl('EstadoConservacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing EstadoConservacion entity.
     *
     * @Route("/actualizar/{id}", name="EstadoConservacion_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:EstadoConservacion:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:EstadoConservacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoConservacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('EstadoConservacion'));
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
            'page_title' => 'Editar Estado de Conservaci&oacute;n'
        );
    }
    /**
     * Deletes a EstadoConservacion entity.
     *
     * @Route("/borrar/{id}", name="EstadoConservacion_delete")
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
         ->where('u.estadoConservacion = :id')
         ->setParameter('id', $id);
         $countInventario = $qbInventario->getQuery()->getSingleScalarResult();

         //Catalogo Material Rodante
         $qbCatalogoMR = $em
         ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
         ->createQueryBuilder('u')
         ->select('count(u.id)')
         ->where('u.idEstadoConservacion = :id')
         ->setParameter('id', $id);
         $countCatalogoMR = $qbCatalogoMR->getQuery()->getSingleScalarResult();

         //Activo Lineal
         $qbActivoLineal = $em
              ->getRepository('ADIFInventarioBundle:ActivoLineal')
              ->createQueryBuilder('u')
              ->select('count(u.id)')
              ->where('u.estadoConservacion = :id')
              ->setParameter('id', $id);
         $counActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

         return ($countInventario+$countCatalogoMR+$counActivoLineal) == 0;
     }

      /**
       *
       * @return type
       */
      public function getSessionMessage() {
          return 'No se pudo eliminar el Estado de Conservación '
                  . 'ya que es referenciada por otras entidades.';
      }
}
