<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Fabricante;
use ADIF\InventarioBundle\Form\FabricanteType;

use ADIF\BaseBundle\Controller\AlertControllerInterface;

/**
 * Fabricante controller.
 *
 * @Route("/fabricante")
  */
class FabricanteController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'General' => '',
            'Fabricantes' => $this->generateUrl('fabricante')
        );
    }
    /**
     * Lists all Fabricante entities.
     *
     * @Route("/", name="fabricante")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Fabricantes'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Fabricantes',
            'page_info' => 'Lista de Fabricantes'
        );
    }

    /**
     * Tabla para Fabricante .
     *
     * @Route("/index_table/", name="fabricante_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Fabricante')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Fabricantes'] = null;

    return $this->render('ADIFInventarioBundle:Fabricante:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Fabricante entity.
     *
     * @Route("/insertar", name="fabricante_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Fabricante:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Fabricante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fabricante'));
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
            'page_title' => 'Crear Fabricante',
        );
    }

    /**
    * Creates a form to create a Fabricante entity.
    *
    * @param Fabricante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Fabricante $entity)
    {
        $form = $this->createForm(new FabricanteType(), $entity, array(
            'action' => $this->generateUrl('fabricante_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Fabricante entity.
     *
     * @Route("/crear", name="fabricante_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Fabricante();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Fabricante'
        );
}

    /**
     * Finds and displays a Fabricante entity.
     *
     * @Route("/{id}", name="fabricante_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Fabricante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Fabricante.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Fabricantes'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Fabricante'
        );
    }

    /**
     * Displays a form to edit an existing Fabricante entity.
     *
     * @Route("/editar/{id}", name="fabricante_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Fabricante:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Fabricante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Fabricante.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Fabricante'
        );
    }

    /**
    * Creates a form to edit a Fabricante entity.
    *
    * @param Fabricante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Fabricante $entity)
    {
        $form = $this->createForm(new FabricanteType(), $entity, array(
            'action' => $this->generateUrl('fabricante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Fabricante entity.
     *
     * @Route("/actualizar/{id}", name="fabricante_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Fabricante:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Fabricante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Fabricante.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('fabricante'));
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
            'page_title' => 'Editar Fabricante'
        );
    }
    /**
     * Deletes a Fabricante entity.
     *
     * @Route("/borrar/{id}", name="fabricante_delete")
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
             ->where('u.fabricante = :id')
             ->setParameter('id', $id);
        $counCatalogoMaterialesNuevos = $qbCatalogoMaterialesNuevos->getQuery()->getSingleScalarResult();

        //Catalogo Material Rodante
        $qbCatalogoMR = $em
        ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
        ->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.idFabricante = :id')
        ->setParameter('id', $id);
        $countCatalogoMR = $qbCatalogoMR->getQuery()->getSingleScalarResult();

        return ($counCatalogoMaterialesNuevos+$countCatalogoMR) == 0;
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
