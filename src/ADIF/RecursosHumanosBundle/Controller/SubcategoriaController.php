<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Subcategoria;
use ADIF\RecursosHumanosBundle\Form\SubcategoriaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Subcategoria controller.
 *
 * @Route("/subcategorias")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class SubcategoriaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Subcategorías' => $this->generateUrl('subcategorias')
        );
    }

    /**
     * Lists all Subcategoria entities.
     *
     * @Route("/", name="subcategorias")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Subcategoria')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Subcategorías'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Subcategorías',
            'page_info' => 'Lista de subcategorías'
        );
    }

    /**
     * Creates a new Subcategoria entity.
     *
     * @Route("/insertar", name="subcategorias_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Subcategoria:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Subcategoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subcategorias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear subcategoría',
        );
    }

    /**
     * Creates a form to create a Subcategoria entity.
     *
     * @param Subcategoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Subcategoria $entity) {
        $form = $this->createForm(new SubcategoriaType(), $entity, array(
            'action' => $this->generateUrl('subcategorias_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Subcategoria entity.
     *
     * @Route("/crear", name="subcategorias_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Subcategoria();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear subcategoría'
        );
    }

    /**
     * Finds and displays a Subcategoria entity.
     *
     * @Route("/{id}", name="subcategorias_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Subcategoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Subcategoria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Subcategoria'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver subcategoría'
        );
    }

    /**
     * Displays a form to edit an existing Subcategoria entity.
     *
     * @Route("/editar/{id}", name="subcategorias_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Subcategoria:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Subcategoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Subcategoria.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar subcategoría'
        );
    }

    /**
     * Creates a form to edit a Subcategoria entity.
     *
     * @param Subcategoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Subcategoria $entity) {
        $form = $this->createForm(new SubcategoriaType(), $entity, array(
            'action' => $this->generateUrl('subcategorias_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Subcategoria entity.
     *
     * @Route("/actualizar/{id}", name="subcategorias_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Subcategoria:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Subcategoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Subcategoria.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('subcategorias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar subcategoría'
        );
    }

    /**
     * Deletes a Subcategoria entity.
     *
     * @Route("/borrar/{id}", name="subcategorias_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * @Route("/lista_subcategorias", name="lista_subcategorias")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaSubcategoriasAction(Request $request) {
        $id_categoria = $request->request->get('id_categoria');

        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:Subcategoria', $this->getEntityManager());

        $query = $repository->createQueryBuilder('s')
                ->select('s.id', 's.nombre')
                ->where('s.idCategoria =  :categoria')
                ->setParameter('categoria', $id_categoria)
                ->orderBy('s.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la subcategoría '
                . 'ya que es referenciada por algún empleado.';
    }
    
    /**
     * Llena el campo de "categoria_recibo" con la concatenacion de la subcategoria y categoria
     *
     * @Route("/llenar_categoria_recibo/", name="llenar_categoria_recibo")
     * @Method("GET")
     */
    public function llenarCategoriaReciboAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $subcategorias = $em->getRepository('ADIFRecursosHumanosBundle:Subcategoria')->findAll();
        $categoriaRecibo = '';
        foreach($subcategorias as $subcategoria) {
            $categoriaRecibo = $subcategoria->getCategoria()->getNombre() . ' ' . $subcategoria->getNombre();
            $subcategoria->setCategoriaRecibo($categoriaRecibo);
            $em->persist($subcategoria);
        }
        $em->flush();
    }
}
