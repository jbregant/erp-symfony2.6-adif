<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\TipoMaterial;
use ADIF\InventarioBundle\Form\TipoMaterialType;

/**
 * TipoMaterial controller.
 *
 * @Route("/tipomaterial")
  */
class TipoMaterialController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuración' => '',
            'Materiales Nuevos y Producidos de Obra' => '',
            'Tipos de Material' => $this->generateUrl('tipomaterial')
        );
    }
    /**
     * Lists all TipoMaterial entities.
     *
     * @Route("/", name="tipomaterial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Material'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipo de Material',
            'page_info' => 'Lista de Tipos de Material'
        );
    }

    /**
     * Tabla para TipoMaterial .
     *
     * @Route("/index_table/", name="tipomaterial_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:TipoMaterial')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipo de Material'] = null;

    return $this->render('ADIFInventarioBundle:TipoMaterial:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TipoMaterial entity.
     *
     * @Route("/insertar", name="tipomaterial_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:TipoMaterial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoMaterial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipomaterial'));
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
            'page_title' => 'Crear Tipo de Material',
        );
    }

    /**
    * Creates a form to create a TipoMaterial entity.
    *
    * @param TipoMaterial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoMaterial $entity)
    {
        $form = $this->createForm(new TipoMaterialType(), $entity, array(
            'action' => $this->generateUrl('tipomaterial_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoMaterial entity.
     *
     * @Route("/crear", name="tipomaterial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoMaterial();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Tipo de Material'
        );
}

    /**
     * Finds and displays a TipoMaterial entity.
     *
     * @Route("/{id}", name="tipomaterial_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo de Material.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipo de Material'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Tipo de Material'
        );
    }

    /**
     * Displays a form to edit an existing TipoMaterial entity.
     *
     * @Route("/editar/{id}", name="tipomaterial_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:TipoMaterial:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo de Material.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipo de Material'
        );
    }

    /**
    * Creates a form to edit a TipoMaterial entity.
    *
    * @param TipoMaterial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoMaterial $entity)
    {
        $form = $this->createForm(new TipoMaterialType(), $entity, array(
            'action' => $this->generateUrl('tipomaterial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TipoMaterial entity.
     *
     * @Route("/actualizar/{id}", name="tipomaterial_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:TipoMaterial:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo de Material.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipomaterial'));
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
            'page_title' => 'Editar Tipo de Material'
        );
    }
    /**
     * Deletes a TipoMaterial entity.
     *
     * @Route("/borrar/{id}", name="tipomaterial_delete")
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
         ->where('u.tipoMaterial = :id')
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
