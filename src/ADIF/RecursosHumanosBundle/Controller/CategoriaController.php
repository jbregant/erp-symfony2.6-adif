<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Categoria;
use ADIF\RecursosHumanosBundle\Form\CategoriaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Categoria controller.
 *
 * @Route("/categorias")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class CategoriaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Categor&iacute;as' => $this->generateUrl('categorias')
        );
    }

    /**
     * Lists all Categoria entities.
     *
     * @Route("/", name="categorias")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Categoria')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Categorías'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Categor&iacute;as',
            'page_info' => 'Lista de categor&iacute;as'
        );
    }

    /**
     * Creates a new Categoria entity.
     *
     * @Route("/insertar", name="categorias_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Categoria:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Categoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear categor&iacute;a',
        );
    }

    /**
     * Creates a form to create a Categoria entity.
     *
     * @param Categoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Categoria $entity) {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('categorias_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Categoria entity.
     *
     * @Route("/crear", name="categorias_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Categoria();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear categor&iacute;a'
        );
    }

    /**
     * Finds and displays a Categoria entity.
     *
     * @Route("/{id}", name="categorias_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Categoria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Categoria'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver categor&iacute;a'
        );
    }

    /**
     * Displays a form to edit an existing Categoria entity.
     *
     * @Route("/editar/{id}", name="categorias_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Categoria:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Categoria.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar categor&iacute;a'
        );
    }

    /**
     * Creates a form to edit a Categoria entity.
     *
     * @param Categoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Categoria $entity) {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('categorias_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Categoria entity.
     *
     * @Route("/actualizar/{id}", name="categorias_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Categoria:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Categoria.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categorias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar categor&iacute;a'
        );
    }

    /**
     * Deletes a Categoria entity.
     *
     * @Route("/borrar/{id}", name="categorias_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * @Route("/lista_categorias", name="lista_categorias")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaCategoriasAction(Request $request) {
        $id_convenio = $request->request->get('id_convenio');

        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:Categoria', $this->getEntityManager());

        $query = $repository->createQueryBuilder('c')
                ->select('c.id', 'c.nombre')
                ->where('c.idConvenio =  :convenio')
                ->setParameter('convenio', $id_convenio)
                ->orderBy('c.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la categoría '
                . 'ya que es referenciada por alguna subcategoría.';
    }

}
