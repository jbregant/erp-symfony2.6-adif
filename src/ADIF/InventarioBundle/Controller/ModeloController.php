<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Modelo;
use ADIF\InventarioBundle\Form\ModeloType;

/**
 * Modelo controller.
 *
 * @Route("/modelo")
  */
class ModeloController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Materiales Rodantes' => '',
            'Modelos' => $this->generateUrl('modelo')
        );
    }
    /**
     * Lists all Modelo entities.
     *
     * @Route("/", name="modelo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Modelos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Modelo',
            'page_info' => 'Lista de modelo'
        );
    }

    /**
     * Tabla para Modelo .
     *
     * @Route("/index_table/", name="modelo_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Modelo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Modelos'] = null;

    return $this->render('ADIFInventarioBundle:Modelo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Modelo entity.
     *
     * @Route("/insertar", name="modelo_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Modelo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Modelo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('modelo'));
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
            'page_title' => 'Crear Modelo',
        );
    }

    /**
    * Creates a form to create a Modelo entity.
    *
    * @param Modelo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Modelo $entity)
    {
        $form = $this->createForm(new ModeloType(), $entity, array(
            'action' => $this->generateUrl('modelo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Modelo entity.
     *
     * @Route("/crear", name="modelo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Modelo();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Modelo'
        );
}

    /**
     * Finds and displays a Modelo entity.
     *
     * @Route("/{id}", name="modelo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Modelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Modelo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Modelos'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Modelo'
        );
    }

    /**
     * Displays a form to edit an existing Modelo entity.
     *
     * @Route("/editar/{id}", name="modelo_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Modelo:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Modelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Modelo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Modelo'
        );
    }

    /**
    * Creates a form to edit a Modelo entity.
    *
    * @param Modelo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Modelo $entity)
    {
        $form = $this->createForm(new ModeloType(), $entity, array(
            'action' => $this->generateUrl('modelo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Modelo entity.
     *
     * @Route("/actualizar/{id}", name="modelo_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Modelo:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Modelo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Modelo.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('modelo'));
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
            'page_title' => 'Editar Modelo'
        );
    }
    /**
     * Deletes a Modelo entity.
     *
     * @Route("/borrar/{id}", name="modelo_delete")
     * @Method("GET")
     */
    public function deleteAction($id){
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
            ->where('u.idModelo = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        return ($countMaterialesRodantes) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la Modelo '
                . 'ya que es referenciado por otras entidades.';
    }
}
