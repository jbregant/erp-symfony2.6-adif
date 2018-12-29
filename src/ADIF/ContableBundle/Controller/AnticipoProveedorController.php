<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoOrdenCompra;
use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Anticipo;
use ADIF\ContableBundle\Entity\AnticipoOrdenCompra;
use ADIF\ContableBundle\Entity\AnticipoProveedor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\OrdenPagoAnticipoProveedor;
use ADIF\ContableBundle\Form\AnticipoProveedorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * AnticipoProveedor controller.
 *
 * @Route("/anticiposproveedor")
 */
class AnticipoProveedorController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Anticipo Proveedor' => $this->generateUrl('anticiposproveedor')
        );
    }

    /**
     * Lists all AnticipoProveedor entities.
     *
     * @Route("/", name="anticiposproveedor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Anticipo Proveedor'] = null;
                
                
        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Anticipo Proveedor',
            'page_info' => 'Lista de anticipos de proveedor'
        );
    }

    /**
     * Tabla para AnticipoProveedor .
     *
     * @Route("/index_table/", name="anticiposproveedor_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:AnticipoProveedor')->findAll();
        //$empresaSession = EmpresaSession::getInstance();

        $bread = $this->base_breadcrumbs;
        $bread['Anticipo Proveedor'] = null;

        return $this->render('ADIFContableBundle:AnticipoProveedor:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new AnticipoProveedor entity.
     *
     * @Route("/insertar", name="anticiposproveedor_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AnticipoProveedor:new.html.twig")
     */
    public function createAction(Request $request) {

        $entity = new AnticipoProveedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $id_proveedor = $request->request->get('id_proveedor');
        $id_oc_tramo = $request->request->get('id_oc_tramo');
        $tipo_anticipo = $request->request->get('tipo_anticipo');

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

            if ($id_proveedor) {

                $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                        ->find($id_proveedor);

                $entity->setProveedor($proveedor);
            }

            if ($tipo_anticipo) {

                if ($tipo_anticipo == Anticipo::TIPO_ANTICIPO_ORDEN_COMPRA) {

                    // Es un anticipo de la orden de compra
                    $anticipo = new AnticipoOrdenCompra($entity);

                    $ordenCompra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')
                            ->find($id_oc_tramo);

                    $anticipo->setOrdenCompra($ordenCompra);

                    // Alerta exencion
                    $error = $this->get('adif.retenciones_service')->exencionAnticipo($anticipo->getProveedor());

                    if ($error['error']) {

                        $errorMsg = '<span> Se notifica que existen certificados de exenci&oacute;n vencidos::</span>';
                        $errorMsg .= '<div style="padding-left: 3em; margin-top: .5em">';
                        $errorMsg .= '<ul>';

                        if ($error[ConstanteTipoImpuesto::Ganancias]) {
                            $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('proveedor_edit', array('id' => $anticipo->getProveedor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::Ganancias . '</a></li>';
                        }

                        if ($error[ConstanteTipoImpuesto::IVA]) {
                            $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('proveedor_edit', array('id' => $anticipo->getProveedor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::IVA . '</a></li>';
                        }

                        if ($error[ConstanteTipoImpuesto::SUSS]) {
                            $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('proveedor_edit', array('id' => $anticipo->getProveedor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::SUSS . '</a></li>';
                        }

                        if ($error[ConstanteTipoImpuesto::IIBB]) {
                            $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl('proveedor_edit', array('id' => $anticipo->getProveedor()->getId())) . '#tab_3">' . ConstanteTipoImpuesto::IIBB . '</a></li>';
                        }

                        $errorMsg .= '</ul>';
                        $errorMsg .= '</div>';

                        $this->get('session')->getFlashBag()->add('warning', $errorMsg);
                    }

                    $em->persist($anticipo);


                    //Creo la autorizacion contable
                    $this->generarAutorizacionContableAnticipoProveedor($anticipo);
                }
            } //. 
            else {

                $em->persist($entity);

                // Creo la autorizacion contable
                $this->generarAutorizacionContableAnticipoProveedor($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('anticiposproveedor'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Anticipo de Proveedor',
        );
    }

    /**
     * Creates a form to create a AnticipoProveedor entity.
     *
     * @param AnticipoProveedor $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(AnticipoProveedor $entity) {
        $form = $this->createForm(new AnticipoProveedorType(), $entity, array(
            'action' => $this->generateUrl('anticiposproveedor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AnticipoProveedor entity.
     *
     * @Route("/crear", name="anticiposproveedor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new AnticipoProveedor();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Anticipo de Proveedor'
        );
    }

    /**
     * Finds and displays a AnticipoProveedor entity.
     *
     * @Route("/{id}", name="anticiposproveedor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoProveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Anticipo Proveedor'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Anticipo de Proveedor'
        );
    }

    /**
     * Displays a form to edit an existing AnticipoProveedor entity.
     *
     * @Route("/editar/{tipo}/{id}", name="anticiposproveedor_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:AnticipoProveedor:new.html.twig")
     */
    public function editAction($tipo, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoProveedor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'tipo' => $tipo,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Anticipo de Proveedor'
        );
    }

    /**
     * Creates a form to edit a AnticipoProveedor entity.
     *
     * @param AnticipoProveedor $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(AnticipoProveedor $entity) {
        $form = $this->createForm(new AnticipoProveedorType(), $entity, array(
            'action' => $this->generateUrl('anticiposproveedor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AnticipoProveedor entity.
     *
     * @Route("/actualizar/{id}", name="anticiposproveedor_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:AnticipoProveedornew.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoProveedor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('anticiposproveedor'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Anticipo de Proveedor'
        );
    }

    /**
     * Deletes a AnticipoProveedor entity.
     *
     * @Route("/borrar/{id}", name="anticiposproveedor_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:AnticipoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoProveedor.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('anticiposproveedor'));
    }

    /**
     * 
     * @param type $anticipo
     */
    private function generarAutorizacionContableAnticipoProveedor($anticipo) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $autorizacionContable = new OrdenPagoAnticipoProveedor();
        $autorizacionContable->setAnticipoProveedor($anticipo);

        $this->get('adif.orden_pago_service')->initAutorizacionContable($autorizacionContable, 'Anticipo de proveedor ');

        $em->persist($autorizacionContable);
    }

    /**
     * Tabla para OrdenCompra.
     *
     * @Route("/index_table_oc_tramo/", name="anticiposproveedor_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableOCTramoAction(Request $request) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $resultado = array();

        $ordenes_compra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')
                ->createQueryBuilder('o')
                ->innerJoin('o.proveedor', 'p')
                ->innerJoin('o.estadoOrdenCompra', 'e')
                ->innerJoin('o.cotizacion', 'c')
                ->where('o.ordenCompraOriginal IS NOT NULL')
                ->andWhere('p.id = :idProveedor')
                ->andWhere('e.denominacionEstado = :estadoOrdenCompra')
                ->setParameters(array('idProveedor' => $request->query->get('id_proveedor'), 'estadoOrdenCompra' => ConstanteEstadoOrdenCompra::ESTADO_OC_GENERADA))
                ->orderBy('o.fechaOrdenCompra', 'DESC')
                ->getQuery()
                ->getResult();

        foreach ($ordenes_compra as $ordenCompra) {

            /* @var $ordenCompra OrdenCompra */

            $ordenCompraService = $this->get('adif.orden_compra_service');

            $saldo = $ordenCompraService->getSaldoOrdenCompra($ordenCompra);

            if ($ordenCompra->getCantidadRestanteTotal() > 0 && $saldo > 0) {

                $resultado[] = array(
                    'id' => $ordenCompra->getId(),
                    'tipo' => 'Orden compra',
                    'numero' => $ordenCompra->getNumero(),
                    'descripcion' => $ordenCompra->getObservacion(),
                    'monto' => $ordenCompra->getMonto(),
                    'tipo_anticipo' => Anticipo::TIPO_ANTICIPO_ORDEN_COMPRA
                );
            }
        }

        return $this->render('ADIFContableBundle:AnticipoProveedor:index_table_por_proveedor.html.twig', array('ordenes_compra_tramos' => $resultado));
    }

    /**
     * Anula el anticipo
     *
     * @Route("/anular/{id}", name="anticiposproveedorordencompra_anular")
     * @Method("GET")
     */
    public function anularAnticipoOrdenCompraAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity AnticipoOrdenCompra */
        $entity = $em->getRepository('ADIFContableBundle:AnticipoOrdenCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoOrdenCompra.');
        }

        $estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);

        $entity->setEstadoComprobante($estadoAnulado);
        $entity->setFechaAnulacion(new \DateTime());

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El anticipo fue anulado');

        return $this->redirect($this->generateUrl('comprobantes_compra'));
    }

}
