<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Marca;
use ADIF\InventarioBundle\Form\MarcaType;

/**
 * Marca controller.
 *
 * @Route("/marca")
  */
class MarcaController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Materiales Rodantes' => '',
            'Marcas' => $this->generateUrl('marca')
        );
    }
    /**
     * Lists all Marca entities.
     *
     * @Route("/", name="marca")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Marcas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Marcas',
            'page_info' => 'Lista de Marcas'
        );
    }

    /**
     * Tabla para Marca .
     *
     * @Route("/index_table/", name="marca_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Marca')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Marcas'] = null;

        return $this->render('ADIFInventarioBundle:Marca:index_table.html.twig', array(
            'entities' => $entities
        ) );
    }
    /**
     * Creates a new Marca entity.
     *
     * @Route("/insertar", name="marca_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Marca:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Marca();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('marca'));
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
            'page_title' => 'Crear Marcas',
        );
    }

    /**
    * Creates a form to create a Marca entity.
    *
    * @param Marca $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Marca $entity)
    {
        $form = $this->createForm(new MarcaType(), $entity, array(
            'action' => $this->generateUrl('marca_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Marca entity.
     *
     * @Route("/crear", name="marca_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Marca();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Marcas'
        );
}

    /**
     * Finds and displays a Marca entity.
     *
     * @Route("/{id}", name="marca_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Marca')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Marca.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Marcas'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Marcas'
        );
    }

    /**
     * Displays a form to edit an existing Marca entity.
     *
     * @Route("/editar/{id}", name="marca_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Marca:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Marca')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Marca.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Marcas'
        );
    }

    /**
    * Creates a form to edit a Marca entity.
    *
    * @param Marca $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Marca $entity)
    {
        $form = $this->createForm(new MarcaType(), $entity, array(
            'action' => $this->generateUrl('marca_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Marca entity.
     *
     * @Route("/actualizar/{id}", name="marca_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Marca:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Marca')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Marca.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('marca'));
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
            'page_title' => 'Editar Marcas'
        );
    }
    /**
     * Deletes a Marca entity.
     *
     * @Route("/borrar/{id}", name="marca_delete")
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
            ->where('u.idMarca = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        return ($countMaterialesRodantes) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la Marca '
                . 'ya que es referenciado por otras entidades.';
    }
}
