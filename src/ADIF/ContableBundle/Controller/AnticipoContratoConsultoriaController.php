<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoria;
use ADIF\ContableBundle\Form\AnticipoContratoConsultoriaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\AnticipoContratoConsultoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;

/**
 * AnticipoContratoConsultoria controller.
 *
 * @Route("/anticiposcontratoconsultoria")
 */
class AnticipoContratoConsultoriaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Anticipo Contrato Consultoria' => $this->generateUrl('anticiposcontratoconsultoria')
        );
    }

    /**
     * Lists all AnticipoContratoConsultoria entities.
     *
     * @Route("/", name="anticiposcontratoconsultoria")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Anticipo Contrato Consultoria'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Anticipo ContratoConsultoria',
            'page_info' => 'Lista de anticipos de contrato consultoria'
        );
    }

    /**
     * Tabla para AnticipoContratoConsultoria .
     *
     * @Route("/index_table/", name="anticiposcontratoconsultoria_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Anticipo Contrato Consultoria'] = null;

        return $this->render('ADIFContableBundle:AnticipoContratoConsultoria:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new AnticipoContratoConsultoria entity.
     *
     * @Route("/insertar", name="anticiposcontratoconsultoria_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AnticipoContratoConsultoria:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new AnticipoContratoConsultoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $contratoConsultoria = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->find($request->request->get('id_contrato'));

            $entity->setContrato($contratoConsultoria);

            //alerta exencion
            $error = $this->get('adif.retenciones_service')->exencionAnticipo($entity->getConsultor());

            if ($error['error']) {
                $errorMsg = '<span> Se notifica que existen certificados de exenci&oacute;n vencidos::</span>';
                $errorMsg .= '<div style="padding-left: 3em; margin-top: .5em">';
                $errorMsg .= '<ul>';
                if ($error[ConstanteTipoImpuesto::Ganancias]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('consultor_edit', array('id' => $entity->getConsultor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::Ganancias . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::IVA]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('consultor_edit', array('id' => $entity->getConsultor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::IVA . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::SUSS]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('consultor_edit', array('id' => $entity->getConsultor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::SUSS . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::IIBB]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('consultor_edit', array('id' => $entity->getConsultor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::IIBB . '</a></li>';
                }
                $errorMsg .= '</ul>';
                $errorMsg .= '</div>';
                $this->get('session')->getFlashBag()
                        ->add('warning', $errorMsg);
            }

            //\Doctrine\Common\Util\Debug::dump($entity);die;
            $em->persist($entity);

            //Creo la autorizacion contable
            $this->generarAutorizacionContableAnticipoContratoConsultoria($entity);

            $em->flush();

            return $this->redirect($this->generateUrl('anticiposcontratoconsultoria'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Anticipo de Contrato Consultoria',
        );
    }

    /**
     * Creates a form to create a AnticipoContratoConsultoria entity.
     *
     * @param AnticipoContratoConsultoria $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(AnticipoContratoConsultoria $entity) {
        $form = $this->createForm(new AnticipoContratoConsultoriaType(), $entity, array(
            'action' => $this->generateUrl('anticiposcontratoconsultoria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AnticipoContratoConsultoria entity.
     *
     * @Route("/crear", name="anticiposcontratoconsultoria_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new AnticipoContratoConsultoria();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Anticipo de Contrato Consultoria'
        );
    }

    /**
     * Finds and displays a AnticipoContratoConsultoria entity.
     *
     * @Route("/{id}", name="anticiposcontratoconsultoria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoContratoConsultoria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Anticipo Contrato Consultoria'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Anticipo de Contrato Consultoria'
        );
    }

    /**
     * Displays a form to edit an existing AnticipoContratoConsultoria entity.
     *
     * @Route("/editar/{id}", name="anticiposcontratoconsultoria_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:AnticipoContratoConsultoria:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoContratoConsultoria.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Anticipo de Contrato Consultoria'
        );
    }

    /**
     * Creates a form to edit a AnticipoContratoConsultoria entity.
     *
     * @param AnticipoContratoConsultoria $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(AnticipoContratoConsultoria $entity) {
        $form = $this->createForm(new AnticipoContratoConsultoriaType(), $entity, array(
            'action' => $this->generateUrl('anticiposcontratoconsultoria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AnticipoContratoConsultoria entity.
     *
     * @Route("/actualizar/{id}", name="anticiposcontratoconsultoria_update")
     * @Method("PUT")     
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoContratoConsultoria.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('anticiposcontratoconsultoria'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Anticipo de Contrato Consultoria'
        );
    }

    /**
     * Deletes a AnticipoContratoConsultoria entity.
     *
     * @Route("/borrar/{id}", name="anticiposcontratoconsultoria_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoContratoConsultoria.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('anticiposcontratoconsultoria'));
    }

    private function generarAutorizacionContableAnticipoContratoConsultoria(AnticipoContratoConsultoria $anticipoContratoConsultoria) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $autorizacionContable = new OrdenPagoAnticipoContratoConsultoria();
        $autorizacionContable->setAnticipoContratoConsultoria($anticipoContratoConsultoria);

        $this->get('adif.orden_pago_service')->initAutorizacionContable($autorizacionContable, 'Anticipo de contrato consultoria ' . $anticipoContratoConsultoria->getContrato());

        $em->persist($autorizacionContable);
    }

}
