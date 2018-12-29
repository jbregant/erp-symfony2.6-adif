<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoChequera;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use ADIF\ContableBundle\Entity\Chequera;
use ADIF\ContableBundle\Entity\Cheque;
use ADIF\ContableBundle\Form\ChequeraType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Chequera controller.
 *
 * @Route("/chequera")
 */
class ChequeraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Chequeras' => $this->generateUrl('chequera')
        );
    }

    /**
     * Lists all Chequera entities.
     *
     * @Route("/", name="chequera")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Chequeras'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Chequeras',
            'page_info' => 'Lista de chequeras'
        );
    }

    /**
     * Tabla para Chequera.
     *
     * @Route("/index_table/", name="chequera_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Chequera')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Chequeras'] = null;

        return $this->render('ADIFContableBundle:Chequera:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Chequera entity.
     *
     * @Route("/insertar", name="chequera_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Chequera:new.html.twig")
     */
    public function createAction(Request $request) {

        $chequera = new Chequera();

        $form = $this->createCreateForm($chequera);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $this->initChequera($chequera);

            $em->persist($chequera);
            $em->flush();

            return $this->redirect($this->generateUrl('chequera'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $chequera,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear chequera',
        );
    }

    /**
     * Creates a form to create a Chequera entity.
     *
     * @param Chequera $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Chequera $entity) {
        $form = $this->createForm(new ChequeraType(), $entity, array(
            'action' => $this->generateUrl('chequera_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Chequera entity.
     *
     * @Route("/crear", name="chequera_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Chequera();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear chequera'
        );
    }

    /**
     * Finds and displays a Chequera entity.
     *
     * @Route("/{id}", name="chequera_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Chequera')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Chequera.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Chequera'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver chequera'
        );
    }

    /**
     * Displays a form to edit an existing Chequera entity.
     *
     * @Route("/editar/{id}", name="chequera_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Chequera:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Chequera')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Chequera.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar chequera'
        );
    }

    /**
     * Creates a form to edit a Chequera entity.
     *
     * @param Chequera $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Chequera $entity) {
        $form = $this->createForm(new ChequeraType(), $entity, array(
            'action' => $this->generateUrl('chequera_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Chequera entity.
     *
     * @Route("/actualizar/{id}", name="chequera_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Chequera:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Chequera')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Chequera.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();

            return $this->redirect($this->generateUrl('chequera'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar chequera'
        );
    }

    /**
     * Deletes a Chequera entity.
     *
     * @Route("/borrar/{id}", name="chequera_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        return parent::baseDeleteAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la chequera ya que tiene cheques asociados';
    }

    /**
     * 
     * @param Chequera $chequera
     */
    private function initChequera(Chequera $chequera) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $chequera->setNumeroSiguiente($chequera->getNumeroInicial());

        // Obtengo el EstadoChequera igual a "Activa"
        $estadoIngresada = $em->getRepository('ADIFContableBundle:EstadoChequera')->
                findOneBy(
                array('denominacionEstado' => ConstanteEstadoChequera::ESTADO_CHEQUERA_HABILITADA_ACTIVA), //
                array('id' => 'desc'), 1, 0);

        $chequera->setEstadoChequera($estadoIngresada);
    }

    /**
     * Anula el siguiente Cheque de la Chequera.
     *
     * @Route("/{id}/anular_siguiente_cheque/", name="chequera_anular_siguiente_cheque")
     * @Method("GET")
     */
    public function anularSiguienteChequeAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $chequera = $em->getRepository('ADIFContableBundle:Chequera')->find($id);

        if (!$chequera) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Chequera.');
        }

        $numeroCheque = $request->query->get('cheque');

        $cheque = $em->getRepository('ADIFContableBundle:Cheque')
                ->findOneBy(
                array('chequera' => $chequera, 'numeroCheque' => $numeroCheque), //
                array('id' => 'desc'), 1, 0);

        // Si no se encontraron resultados, creo el nuevo cheque
        if (!$cheque) {

//          $chequeraService = $this->get('adif.chequera_service');

            $cheque = new Cheque;
            $cheque->setChequera($chequera);

//          $cheque->setNumeroCheque($chequeraService->getSiguienteNumeroCheque($em, $chequera));
            $cheque->setNumeroCheque($numeroCheque);
        }

        if ($cheque->getEsAnulable()) {

            // Obtengo el EstadoPago igual a "Anulado"
            $estadoPago = $em->getRepository('ADIFContableBundle:EstadoPago')->
                    findOneBy(
                    array('denominacionEstado' => ConstanteEstadoPago::ESTADO_PAGO_ANULADO), //
                    array('id' => 'desc'), 1, 0);

            $cheque->setEstadoPago($estadoPago);

            $em->persist($cheque);

            $em->flush();

            $this->get('session')->getFlashBag()
                    ->add('success', 'El cheque n&ordm; ' . $cheque->getNumeroCheque() . ' se anuló con éxito.');
        } //
        elseif (!$cheque->getEstaAnulado()) {

            $this->get('session')->getFlashBag()
                    ->add('error', 'El cheque n&ordm; '
                            . $cheque->getNumeroCheque()
                            . ' no se puede anular ya que se encuentra asociado a la OP n&ordm; '
                            . $cheque->getPagoOrdenPago()->getOrdenPagoPagada() . '.');
        }

        return $this->redirect($this->generateUrl('chequera_show', array('id' => $chequera->getId())));
    }

}
