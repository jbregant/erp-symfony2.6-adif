<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\GrupoMaterial;
use ADIF\InventarioBundle\Form\GrupoMaterialType;

/**
 * GrupoMaterial controller.
 *
 * @Route("/grupomaterial")
  */
class GrupoMaterialController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuración' => '',
            'Materiales Nuevos y Producidos de Obra' => '',
            'Grupos de Materiales' => $this->generateUrl('grupomaterial')
        );
    }
    /**
     * Lists all GrupoMaterial entities.
     *
     * @Route("/", name="grupomaterial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Grupos de Materiales'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Grupos de Materiales',
            'page_info' => 'Lista de Grupo de Materiales'
        );
    }

    /**
     * Tabla para GrupoMaterial .
     *
     * @Route("/index_table/", name="grupomaterial_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:GrupoMaterial')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Grupos de Materiales'] = null;

    return $this->render('ADIFInventarioBundle:GrupoMaterial:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new GrupoMaterial entity.
     *
     * @Route("/insertar", name="grupomaterial_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:GrupoMaterial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new GrupoMaterial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('grupomaterial'));
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
            'page_title' => 'Crear Grupo de Material',
        );
    }

    /**
    * Creates a form to create a GrupoMaterial entity.
    *
    * @param GrupoMaterial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(GrupoMaterial $entity)
    {
        $form = $this->createForm(new GrupoMaterialType(), $entity, array(
            'action' => $this->generateUrl('grupomaterial_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new GrupoMaterial entity.
     *
     * @Route("/crear", name="grupomaterial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new GrupoMaterial();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Grupo de Material'
        );
}

    /**
     * Finds and displays a GrupoMaterial entity.
     *
     * @Route("/{id}", name="grupomaterial_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:GrupoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Grupo de Material.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Grupos de Materiales'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Grupo de Material'
        );
    }

    /**
     * Displays a form to edit an existing GrupoMaterial entity.
     *
     * @Route("/editar/{id}", name="grupomaterial_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:GrupoMaterial:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:GrupoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Grupo de Material.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Grupo de Material'
        );
    }

    /**
    * Creates a form to edit a GrupoMaterial entity.
    *
    * @param GrupoMaterial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(GrupoMaterial $entity)
    {
        $form = $this->createForm(new GrupoMaterialType(), $entity, array(
            'action' => $this->generateUrl('grupomaterial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing GrupoMaterial entity.
     *
     * @Route("/actualizar/{id}", name="grupomaterial_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:GrupoMaterial:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:GrupoMaterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Grupo de Material.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('grupomaterial'));
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
            'page_title' => 'Editar Grupo de Material'
        );
    }
    /**
     * Deletes a GrupoMaterial entity.
     *
     * @Route("/borrar/{id}", name="grupomaterial_delete")
     * @Method("GET")
     */
     public function deleteAction($id)
     {
         return parent::baseDeleteAction($id);

     }

    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //Catalogo Material Nuevo
        $qbCatalogoMaterialesNuevos = $em
             ->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')
             ->createQueryBuilder('u')
             ->select('count(u.id)')
             ->where('u.grupoMaterial = :id')
             ->setParameter('id', $id);
        $counCatalogoMaterialesNuevos = $qbCatalogoMaterialesNuevos->getQuery()->getSingleScalarResult();

        //Catalogo Material Producido de Obra
        $qbCatalogoMaterialesProducidosDeObra = $em
             ->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')
             ->createQueryBuilder('u')
             ->select('count(u.id)')
             ->where('u.grupoMaterial = :id')
             ->setParameter('id', $id);
        $counCatalogoMaterialesProducidosDeObra = $qbCatalogoMaterialesProducidosDeObra->getQuery()->getSingleScalarResult();

        return ($counCatalogoMaterialesNuevos+$counCatalogoMaterialesProducidosDeObra) == 0;
    }

     /**
      *
      * @return type
      */
     public function getSessionMessage() {
         return 'No se pudo eliminar el Grupo de Material '
                 . 'ya que es referenciada por otras entidades.';
     }
}
