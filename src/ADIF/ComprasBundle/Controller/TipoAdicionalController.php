<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\TipoAdicional;
use ADIF\ComprasBundle\Form\TipoAdicionalType;

/**
 * TipoAdicionalController controller.
 *
 * @Route("/tipoadicional")
 */
class TipoAdicionalController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de adicional' => $this->generateUrl('tipoadicional')
        );
    }

    /**
     * Lists all TipoAdicional entities.
     *
     * @Route("/", name="tipoadicional")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:TipoAdicional')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de adicional'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipo de adicional',
            'page_info' => 'Lista de tipos de adicional'
        );
    }

    /**
     * Creates a new TipoAdicional entity.
     *
     * @Route("/insertar", name="tipoadicional_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:TipoAdicional:new.html.twig")
     */
    public function createAction(Request $request) {

        $tipoAdicional = new TipoAdicional();

        $form = $this->createCreateForm($tipoAdicional);
        $form->handleRequest($request);

        $tipoAdicionalRequest = $request->request->get('adif_comprasbundle_tipoadicional');

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($tipoAdicional);
            $em->flush();

            // Es un popup
            if (!empty($tipoAdicionalRequest['submit']) && $tipoAdicionalRequest['submit'] == 'popup') {

                return $this->render('::base_iframe.html.twig', array(
                            'response' => 'OK',
                            'response_id' => $tipoAdicional->getId())
                );
            }
            // Si no, Redirijo al index
            else {
                return $this->redirect($this->generateUrl('tipoadicional'));
            }
        } //. 
        else {
            $request->attributes->set('form-error', true);

            // Es un popup
            if (!empty($tipoAdicionalRequest['submit']) && $tipoAdicionalRequest['submit'] == 'popup') {

                $request->attributes->set('popup', true);
            }
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $tipoAdicional,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de adicional',
        );
    }

    /**
     * Creates a form to create a TipoAdicional entity.
     *
     * @param TipoAdicional $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoAdicional $entity) {
        $form = $this->createForm(new TipoAdicionalType(), $entity, array(
            'action' => $this->generateUrl('tipoadicional_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoAdicional entity.
     *
     * @Route("/crear", name="tipoadicional_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoAdicional();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de adicional'
        );
    }

    /**
     * Finds and displays a TipoAdicional entity.
     *
     * @Route("/{id}", name="tipoadicional_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoAdicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Adicional Cotización.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionAdicional()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de adicional'
        );
    }

    /**
     * Displays a form to edit an existing TipoAdicional entity.
     *
     * @Route("/editar/{id}", name="tipoadicional_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:TipoAdicional:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoAdicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Adicional Cotización.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionAdicional()] = $this->generateUrl('tipoadicional_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de adicional'
        );
    }

    /**
     * Creates a form to edit a TipoAdicional entity.
     *
     * @param TipoAdicional $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoAdicional $entity) {
        $form = $this->createForm(new TipoAdicionalType(), $entity, array(
            'action' => $this->generateUrl('tipoadicional_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoAdicional entity.
     *
     * @Route("/actualizar/{id}", name="tipoadicional_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:TipoAdicional:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoAdicional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Adicional Cotización.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipoadicional'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionAdicional()] = $this->generateUrl('tipoadicional_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de adicional'
        );
    }

    /**
     * Deletes a TipoAdicional entity.
     *
     * @Route("/borrar/{id}", name="tipoadicional_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * @Route("/lista", name="lista_tipo_adicional")
     */
    public function getTiposAdicionales() {

        $repository = $this->getDoctrine()->getRepository('ADIFComprasBundle:TipoAdicional', //
                $this->getEntityManager());

        $query = $repository->createQueryBuilder('ta')
                ->select('ta.id', 'ta.denominacionAdicional')
                ->orderBy('ta.denominacionAdicional', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
