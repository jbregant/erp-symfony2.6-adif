<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\Rubro;
use ADIF\ComprasBundle\Form\RubroType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Rubro controller.
 *
 * @Route("/rubro")
 */
class RubroController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Rubros' => $this->generateUrl('rubro')
        );
    }

    /**
     * Lists all Rubro entities.
     *
     * @Route("/", name="rubro")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:Rubro')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Rubros'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Rubro',
            'page_info' => 'Lista de rubros'
        );
    }

    /**
     * Creates a new Rubro entity.
     *
     * @Route("/insertar", name="rubro_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Rubro:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Rubro();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rubro'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rubro',
        );
    }

    /**
     * Creates a form to create a Rubro entity.
     *
     * @param Rubro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Rubro $entity) {
        $form = $this->createForm(new RubroType(), $entity, array(
            'action' => $this->generateUrl('rubro_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_RRHH' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Rubro entity.
     *
     * @Route("/crear", name="rubro_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Rubro();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rubro'
        );
    }

    /**
     * Finds and displays a Rubro entity.
     *
     * @Route("/{id}", name="rubro_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $entity = $em->getRepository('ADIFComprasBundle:Rubro')->find($id);
       
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Rubro.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionRubro()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver rubro'
        );
    }

    /**
     * Displays a form to edit an existing Rubro entity.
     *
     * @Route("/editar/{id}", name="rubro_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Rubro:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Rubro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Rubro.');
        }
        
        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionRubro()] = $this->generateUrl('rubro_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rubro'
        );
    }

    /**
     * Creates a form to edit a Rubro entity.
     *
     * @param Rubro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Rubro $entity) {
        $form = $this->createForm(new RubroType(), $entity, array(
            'action' => $this->generateUrl('rubro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_RRHH' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Rubro entity.
     *
     * @Route("/actualizar/{id}", name="rubro_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:Rubro:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Rubro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Rubro.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('rubro'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionRubro()] = $this->generateUrl('rubro_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rubro'
        );
    }

    /**
     * Deletes a Rubro entity.
     *
     * @Route("/borrar/{id}", name="rubro_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
