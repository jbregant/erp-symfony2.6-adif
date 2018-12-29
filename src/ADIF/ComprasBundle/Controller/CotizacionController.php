<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\AdicionalCotizacion;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoComparacionCotizacion;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRequerimiento;
use ADIF\ComprasBundle\Entity\Cotizacion;
use ADIF\ComprasBundle\Entity\CotizacionArchivo;
use ADIF\ComprasBundle\Entity\RenglonCotizacion;
use ADIF\ComprasBundle\Form\AdicionalCotizacionType;
use ADIF\ComprasBundle\Form\CotizacionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use mPDF;
use Symfony\Component\HttpFoundation\Response;
use ADIF\BaseBundle\Session\EmpresaSession;


/**
 * Cotizacion controller.
 *
 * @Route("/cotizacion")
 */
class CotizacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Requerimientos' => $this->generateUrl('requerimiento')
        );
    }

    /**
     * Lists all Cotizacion entities.
     *
     * @Route("/", name="cotizacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:Cotizacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cotizaciones'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Cotizaci&oacute;n',
            'page_info' => 'Lista de cotizaciones'
        );
    }

    /**
     * Creates a new Cotizacion entity.
     *
     * @Route("/insertar", name="cotizacion_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Cotizacion:new.html.twig")
     */
    public function createAction(Request $request) {

        $cotizacion = new Cotizacion();

        $form = $this->createCreateForm($cotizacion);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $em->persist($cotizacion);

            $em->flush();

            return $this->redirect($this->generateUrl('cotizacion'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento'] = null;
        $bread['Cotizaciones'] = $this->generateUrl('cotizacion');
        $bread['Crear'] = null;

        return array(
            'entity' => $cotizacion,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cotización',
        );
    }

    /**
     * Creates a form to create a Cotizacion entity.
     *
     * @param Cotizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cotizacion $entity) {
        $form = $this->createForm(new CotizacionType(), $entity, array(
            'action' => $this->generateUrl('cotizacion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Cotizacion entity.
     *
     * @Route("/crear", name="cotizacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Cotizacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento'] = null;
        $bread['Cotizaciones'] = $this->generateUrl('cotizacion');
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cotización'
        );
    }

    /**
     * Muestra los Proveedores invitados.
     *
     * @Route("/{idRequerimiento}", name="cotizacion_show_invitaciones")
     * @Method("GET")
     * @Template()
     */
    public function showAction($idRequerimiento) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')
                ->find($idRequerimiento);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Cotizaciones'] = null;

        return array(
            'requerimiento' => $requerimiento,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver cotizaciones'
        );
    }

    /**
     * Displays a form to edit an existing Cotizacion entity.
     *
     * @Route("/editar/{id}", name="cotizacion_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cotizacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Cotizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cotizacion.');
        }

        $editForm = $this->createEditForm($entity);

        $requerimiento = $entity->getRequerimiento();

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Cotizaciones'] = $this->generateUrl('cotizacion');
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cotizaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a Cotizacion entity.
     *
     * @param Cotizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cotizacion $entity) {
        $form = $this->createForm(new CotizacionType(), $entity, array(
            'action' => $this->generateUrl('cotizacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Cotizacion entity.
     *
     * @Route("/actualizar/{id}", name="cotizacion_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:Cotizacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Cotizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cotizacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cotizacion'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $requerimiento = $entity->getRequerimiento();

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Cotizaciones'] = $this->generateUrl('cotizacion');
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cotizaci&oacute;n'
        );
    }

    /**
     * Deletes a Cotizacion entity.
     *
     * @Route("/borrar/{id}", name="cotizacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:Cotizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cotizacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('cotizacion'));
    }

    /**
     * Muestra y permite las solicitudes de cotizaciones de un requerimiento.
     *
     * @Route("/invitar/{idRequerimiento}", name="cotizacion_invitar")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cotizacion:invitar.html.twig")
     */
    public function invitarProveedoresAction($idRequerimiento) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($idRequerimiento);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $rubrosIds = $this->getRubrosIds($requerimiento);

        $proveedores = $em->getRepository('ADIFComprasBundle:Proveedor')->getProveedorByRubroId($rubrosIds);

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Solicitar cotizaciones'] = null;

        return array(
            'proveedores' => $proveedores,
            'requerimiento' => $requerimiento,
            'breadcrumbs' => $bread,
            'page_title' => 'Solicitar cotizaciones'
        );
    }

    /**
     * 
     * @Route("/guardar-invitaciones", name="cotizacion_guardar_invitaciones")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Cotizacion:invitar.html.twig")
     */
    public function saveInvitacionesAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idRequerimiento = $request->request->get('requerimiento');

        $proveedoresInvitados = $request->request->get('proveedores_invitados');

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($idRequerimiento);

        $cotizacionesOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los proveedoresInvitados actuales en la BBDD
        foreach ($requerimiento->getCotizaciones() as $cotizacion) {
            $cotizacionesOriginales->add($cotizacion);
        }

        // Si la tabla de invitados está vacía debo borrar todos los invitados
        if (!$proveedoresInvitados) {

            // Por cada renglon Original
            foreach ($cotizacionesOriginales as $cotizacionOriginal) {

                // Elimino los renglonesCotizacion de cada cotizacion
                foreach ($cotizacionOriginal->getRenglonesCotizacion() as $renglonCotizacion) {

                    $cotizacionOriginal->removeRenglonesCotizacion($renglonCotizacion);

                    $renglonCotizacion->getRenglonRequerimiento()->removeRenglonesCotizacion($renglonCotizacion);

                    $em->remove($renglonCotizacion);
                }

                // Elimino la cotización del requerimiento
                $requerimiento->removeCotizacione($cotizacionOriginal);

                $em->remove($cotizacionOriginal);
            }
        } else {

            // Por cada cotizacionOriginal
            foreach ($cotizacionesOriginales as $cotizacionOriginal) {

                // Verifico si el cotizador se encuentra
                $encontrado = FALSE;

                foreach ($proveedoresInvitados as $i => &$invitacion) {

                    // Si no lo encuentro
                    if ($cotizacionOriginal->getProveedor()->getId() == $invitacion['id']) {

                        unset($proveedoresInvitados[$i]);

                        $encontrado = TRUE;
                    }
                }

                // Si lo no encontré debo eliminarlo
                if (!$encontrado) {

                    // Elimino los renglonesCotizacion
                    foreach ($cotizacionOriginal->getRenglonesCotizacion() as $renglonCotizacion) {
                        $cotizacionOriginal->removeRenglonesCotizacion($renglonCotizacion);

                        $renglonCotizacion->getRenglonRequerimiento()->removeRenglonesCotizacion($renglonCotizacion);

                        $em->remove($renglonCotizacion);
                    }

                    // Elimino la cotización del requerimiento
                    $requerimiento->removeCotizacione($cotizacionOriginal);

                    $em->remove($cotizacionOriginal);
                }
            }
        }

        // Proceso las invitaciones nuevas
        $fechaInvitacion = new \DateTime();

        if ($proveedoresInvitados) {

            foreach ($proveedoresInvitados as $invitacion) {

                $cotizacion = new Cotizacion();

                // Obtengo el proveedor correspondiente
                $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($invitacion['id']);
                $cotizacion->setProveedor($proveedor);
                $proveedor->addCotizacionSolicitada($cotizacion);
                $cotizacion->setFechaInvitacion($fechaInvitacion);
                $cotizacion->setRequerimiento($requerimiento);
                $requerimiento->addCotizacione($cotizacion);

                // Creo los correspondientes renglonesCotizaciones
                foreach ($requerimiento->getRenglonesRequerimiento() as $renglon) {

                    $renglonCotizacion = new RenglonCotizacion();

                    // Seteo el estado "Creada"
                    $renglonCotizacion->setEstadoComparacionCotizacion(
                            $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                                    ->findOneBy(
                                            array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_CREADA), //
                                            array('id' => 'desc'), 1, 0));

                    $renglonCotizacion->setCotizacion($cotizacion);
                    $renglonCotizacion->setRenglonRequerimiento($renglon);
                    $renglonCotizacion->setCantidad($renglon->getCantidad());

                    $renglon->addRenglonesCotizacion($renglonCotizacion);

                    $cotizacion->addRenglonesCotizacion($renglonCotizacion);
                }
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', 'El proveedores invitados se actualizaron correctamente.');

        return $this->redirect($this->generateUrl('cotizacion_show_invitaciones', array('idRequerimiento' => $idRequerimiento)));
    }

    /**
     * 
     * @Route("/guardar-cotizacion", name="cotizacion_guardar_cotizacion")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Cotizacion:show.cotizacion.html.twig")
     */
    public function saveCotizacionAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $alicuotasIva = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findAll();

        $tipoMonedas = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->findAll();

        $idCotizacion = $request->request->get('cotizacion');

        /* @var $cotizacion Cotizacion */

        $cotizacion = $em->getRepository('ADIFComprasBundle:Cotizacion')->find($idCotizacion);

        $fechaCotizacion = $request->request->get('fecha_cotizacion');

        $fechaInvitacion = $request->request->get('fecha_invitacion');

        $cotizacion->setFechaCotizacion(\DateTime::createFromFormat('d/m/Y', $fechaCotizacion));

        $cotizacion->setFechaInvitacion(\DateTime::createFromFormat('d/m/Y', $fechaInvitacion));

        $renglonesCotizacionTabla = $request->request->get('renglon_cotizacion');

        $adicionalesTabla = $request->request->get('adicionales');

        // Creo un Array con los AdicionalesOriginales
        $adicionalesOriginales = new ArrayCollection();

        foreach ($cotizacion->getAdicionalesCotizacion() as $adicional) {
            $adicionalesOriginales->add($adicional);
        }

        // Si no hay adicionales, debo eliminar todos los adicionalesOriginales
        if (!$adicionalesTabla) {

            foreach ($adicionalesOriginales as $adicionalOriginal) {
                $cotizacion->removeAdicionalesCotizacion($adicionalOriginal);
                $em->remove($adicionalOriginal);
            }
        } //.
        else {

            // Por cada adicionalOriginal
            foreach ($adicionalesOriginales as $adicionalOriginal) {

                $encontrado = false;

                // Busco en adicionalesTabla
                foreach ($adicionalesTabla as $adicional) {

                    // Si lo encuentra
                    if ($adicional['id_adicional'] == $adicionalOriginal->getId()) {

                        // Encontrado
                        $encontrado = true;

                        // Controlo si tengo que modificar los datos

                        if ($adicional['id_tipo_adicional'] != $adicionalOriginal->getTipoAdicional()->getId()) {
                            $tipoAdicional = $em->getRepository('ADIFComprasBundle:TipoAdicional')
                                    ->find($adicional['id_tipo_adicional']);

                            $adicionalOriginal->setTipoAdicional($tipoAdicional);
                        }

                        if ($adicional['signo'] != $adicionalOriginal->getSigno()) {
                            $adicionalOriginal->setSigno($adicional['signo']);
                        }

                        if ($adicional['tipo_valor'] != $adicionalOriginal->getTipoValor()) {
                            $adicionalOriginal->setTipoValor($adicional['tipo_valor']);
                        }

                        if ($adicional['valor'] != $adicionalOriginal->getValor()) {
                            $adicionalOriginal->setValor($adicional['valor']);
                        }

                        if ($adicional['id_alicuota_iva'] != $adicionalOriginal->getIdAlicuotaIva()) {

                            $alicuotaIva = $this->getAlicuotaIva($alicuotasIva, $adicional['id_alicuota_iva']);

                            $adicionalOriginal->setAlicuotaIva($alicuotaIva);
                        }

                        if ($adicional['id_tipo_moneda'] != $adicionalOriginal->getIdTipoMoneda()) {

                            $tipoMoneda = $this->getTipoMoneda($tipoMonedas, $adicional['id_tipo_moneda']);

                            $adicionalOriginal->setTipoMoneda($tipoMoneda);
                        }

                        if ($adicional['tipo_cambio'] != $adicionalOriginal->getTipoCambio()) {

                            $adicionalOriginal->setTipoCambio($adicional['tipo_cambio']);
                        }

                        if ($adicional['observacion'] != $adicionalOriginal->getObservacion()) {
                            $adicionalOriginal->setObservacion($adicional['observacion']);
                        }
                    }
                }

                // Si no lo encontré
                if (!$encontrado) {

                    // Elimino el adicionalOriginal
                    $cotizacion->removeAdicionalesCotizacion($adicionalOriginal);
                    $em->remove($adicionalOriginal);
                }
            }
        }

        // Proceso los adicionalesTabla que son nuevos
        if ($adicionalesTabla) {

            foreach ($adicionalesTabla as $adicional) {

                if (null == $adicional['id_adicional']) {

                    $adicionalNuevo = new AdicionalCotizacion();

                    // Seteo el estado "Creada"
                    $adicionalNuevo->setEstadoComparacionCotizacion(
                            $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                                    ->findOneBy(
                                            array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_CREADA), //
                                            array('id' => 'desc'), 1, 0));

                    // Obtengo el tipoAdicional correspondiente
                    $tipoAdicional = $em->getRepository('ADIFComprasBundle:TipoAdicional')
                            ->find($adicional['id_tipo_adicional']);

                    $adicionalNuevo->setTipoAdicional($tipoAdicional);

                    $adicionalNuevo->setSigno($adicional['signo']);

                    $adicionalNuevo->setTipoValor($adicional['tipo_valor']);

                    $adicionalNuevo->setValor($adicional['valor']);

                    $adicionalNuevo->setAlicuotaIva(
                            $this->getAlicuotaIva($alicuotasIva, $adicional['id_alicuota_iva'])
                    );

                    $adicionalNuevo->setObservacion($adicional['observacion']);

                    $adicionalNuevo->setCotizacion($cotizacion);

                    $cotizacion->addAdicionalesCotizacion($adicionalNuevo);
                }
            }
        }

        // Por cada renglonCotizacion
        foreach ($renglonesCotizacionTabla as $renglonCotizacionTabla) {

            // Busco el renglonCotizacion
            $renglonCotizacion = $em->getRepository('ADIFComprasBundle:RenglonCotizacion')->
                    findOneBy(
                    array('cotizacion' => $cotizacion->getId(), 'renglonRequerimiento' => $renglonCotizacionTabla['id_renglon_requerimiento']), //
                    array('id' => 'desc'), 1, 0);

            if ($renglonCotizacion) {

                $alicuotaIva = $this->getAlicuotaIva($alicuotasIva, $renglonCotizacionTabla['alicuotaIva']);

                $tipoMoneda = $this->getTipoMoneda($tipoMonedas, $renglonCotizacionTabla['tipoMoneda']);

                $renglonCotizacion->setCantidad($renglonCotizacionTabla['cantidad']);
                $renglonCotizacion->setPrecioUnitario($renglonCotizacionTabla['precioUnitario']);
                $renglonCotizacion->setAlicuotaIva($alicuotaIva);
                $renglonCotizacion->setTipoMoneda($tipoMoneda);
                $renglonCotizacion->setTipoCambio($renglonCotizacionTabla['tipoCambio']);
                $renglonCotizacion->setObservacion($renglonCotizacionTabla['observacion']);
            }
        }

        $requerimiento = $cotizacion->getRequerimiento();

        // Si el requerimiento NO está Cotizado
        if (ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_COTIZADO != $requerimiento->getEstadoRequerimiento()) {

            // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Cotizado"
            $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                    findOneBy(
                    array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_COTIZADO), //
                    array('id' => 'desc'), 1, 0)
            ;

            $requerimiento->setEstadoRequerimiento($estadoRequerimiento);
        }

        // Actualiza los archivos adjuntos
        $this->updateAdjuntos($em, $request, $cotizacion);

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La cotización fue agregada correctamente.');

        return $this->redirect($this->generateUrl('cotizacion_show_invitaciones', array('idRequerimiento' => $requerimiento->getId())));
    }

    /**
     * Muestra y permite la edición de las cotizaciones de un Proveedor respecto de un Requerimiento.
     *
     * @Route("/show/{id}", name="cotizacion_show_cotizacion")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cotizacion:show.cotizacion.html.twig")
     */
    public function showCotizacionAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $cotizacion = $em->getRepository('ADIFComprasBundle:Cotizacion')->find($id);

        if (!$cotizacion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cotización.');
        }

        $cotizacionForm = $this->createEditForm($cotizacion);

        $adicionalForm = $this->createAdicionalForm(new AdicionalCotizacion());

        $requerimiento = $cotizacion->getRequerimiento();

        $alicuotasIva = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findAll();

        $tipoMonedas = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->findAll();

        $porcentajeTopeJustiprecioCotizacion = $this->container->getParameter('porcentaje_tope_justiprecio_cotizacion');

        $bread = $this->base_breadcrumbs;
        $bread['Requerimientos'] = $this->generateUrl('requerimiento');
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Cotizaciones'] = $this->generateUrl('cotizacion_show_invitaciones', array('idRequerimiento' => $requerimiento->getId()));
        $bread['Administración'] = null;

        return array(
            'cotizacion' => $cotizacion,
            'alicuotasIva' => $alicuotasIva,
            'tipoMonedas' => $tipoMonedas,
            'form_cotizacion' => $cotizacionForm->createView(),
            'form_adicional' => $adicionalForm->createView(),
            'porcentaje_tope_justiprecio_cotizacion' => $porcentajeTopeJustiprecioCotizacion,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Cotizaci&oacute;n'
        );
    }

    /**
     * 
     * @param AdicionalCotizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAdicionalForm(AdicionalCotizacion $entity) {
        $form = $this->createForm(new AdicionalCotizacionType( $this->getDoctrine()->getManager($this->getEntityManager()),
                                                               $this->getDoctrine()->getManager(EntityManagers::getEmContable())
                                                             ), $entity, array(
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Muestra y permite la elección de la mejor opcion respecto de un Requerimiento.
     *
     * @Route("/comparacion/{idRequerimiento}", name="cotizacion_show_comparacion")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cotizacion:show.comparacion.html.twig")
     */
    public function showComparacionAction($idRequerimiento) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($idRequerimiento);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Requerimientos'] = $this->generateUrl('requerimiento');
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Comparación'] = null;

        return array(
            'requerimiento' => $requerimiento,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comparaci&oacute;n'
        );
    }

    /**
     *
     * @Route("/print/{idRequerimiento}/proveedor/{idProveedor}", name="cotizacion_print")
     * @Method("GET")
     */
    public function printAction($idRequerimiento, $idProveedor) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($idRequerimiento);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $html = '<html><head><style type="text/css">'
                . $this->renderView('::PDF/mpdf.default.css.twig')
                . '</style></head><body>';
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $html .= $this->renderView(
                'ADIFComprasBundle:Cotizacion:print.show.html.twig', [
            'entity' => $requerimiento,
            'renglones' => $requerimiento->getRenglonesRequerimiento(),
            'proveedor' => $proveedor,
            'idEmpresa' => $idEmpresa
                ]                
        );

        $html .= '</body></html>';

        $filename = 'cotizacion_' . $requerimiento->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     *
     * @Route("/comparacion/print", name="cotizacion_print_cuadro_comparativo")
     * @Method("GET|POST")
     */
    public function printCuadroComparativoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')
                ->find($request->request->get('idRequerimiento'));

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $ordenCompraAnterior = $em->getRepository('ADIFComprasBundle:OrdenCompra')
                ->findFechaOrdenCompraAnteriorByRequerimiento($requerimiento);

        $fechaOrdenCompraAnterior = new \DateTime();

        if ($ordenCompraAnterior) {

            $fechaOrdenCompraAnterior = $ordenCompraAnterior->getFechaOrdenCompra();
        }

        $html = '<html><head><meta charset="utf-8"/><style type="text/css">'
                . $this->renderView('ADIFComprasBundle:Cotizacion:comparacion.css.twig')
                . '</style></head><body>';

        $html .= $this->renderView('ADIFComprasBundle:Cotizacion:print.comparacion.html.twig', [
            'fechaOrdenCompraAnterior' => $fechaOrdenCompraAnterior,
            'cuadro' => $request->request->get('htmlCuadro'),
            'requerimiento' => $requerimiento,
        ]);

        $html .= '</body></html>';

        $filename = 'cuadroComparativo_' . $requerimiento->getNumero() . '.pdf';

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->getInternalGenerator()->setTimeout(2 * 3600);
        $snappy->setOption('lowquality', false);

        return new \Symfony\Component\HttpFoundation\Response(
                $snappy->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                )
        );
    }

    /**
     * 
     * @Route("/guardar-comparacion", name="cotizacion_guardar_comparacion")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Cotizacion:show.comparacion.html.twig")
     */
    public function saveComparacionAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idRequerimiento = $request->request->get('id_requerimiento');

        $adicionalesCotizacionElegidos = $request->request->get('adicionales_cotizaciones_elegidos');

        $adicionalesCotizacionNoElegidos = $request->request->get('adicionales_cotizaciones_no_elegidos');

        $renglonesCotizacionElegidos = $request->request->get('renglones_cotizaciones_elegidos');

        $this->updateAdicionalesCotizacion($request, $adicionalesCotizacionElegidos, true, $renglonesCotizacionElegidos);

        $this->updateAdicionalesCotizacion($request, $adicionalesCotizacionNoElegidos, false);

        $this->updateCotizacionesElegidas($request, $renglonesCotizacionElegidos, $idRequerimiento);

        $this->updateCotizacionesNoElegidas($request);


        $em->flush();

        return $this->redirect($this->generateUrl('cotizacion_show_invitaciones', array('idRequerimiento' => $idRequerimiento)));
    }

    /**
     * 
     * @param type $requerimiento
     * @return type
     */
    private function getRubrosIds($requerimiento) {

        $rubroIds = array();

        foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequeriminto) {

            $rubroId = $renglonRequeriminto->getRenglonSolicitudCompra()->getRubro()->getId();

            if (!key_exists($rubroId, $rubroIds)) {
                $rubroIds[] = $rubroId;
            }
        }

        return $rubroIds;
    }

    /**
     * 
     * @param type $alicuotasIva
     * @param type $idAlicuotaIva
     * @return type
     */
    private function getAlicuotaIva($alicuotasIva, $idAlicuotaIva) {

        $alicuotaIvaResultado = array_filter($alicuotasIva, function($alicuotaIva) use (&$idAlicuotaIva) {

            if ($alicuotaIva->getId() != $idAlicuotaIva) {
                return false;
            }
            return true;
        });

        return array_shift($alicuotaIvaResultado);
    }

    /**
     * 
     * @param type $tipoMonedas
     * @param type $idTipoMoneda
     * @return type
     */
    private function getTipoMoneda($tipoMonedas, $idTipoMoneda) {

        $tipoMonedaResultado = array_filter($tipoMonedas, function($tipoMoneda) use (&$idTipoMoneda) {

            if ($tipoMoneda->getId() != $idTipoMoneda) {
                return false;
            }
            return true;
        });

        return array_shift($tipoMonedaResultado);
    }

    /**
     * 
     * @param type $request
     * @param type $adicionalesCotizacion
     * @param type $adicionalElegido
     * @param type $renglonesCotizacion
     * @throws type
     */
    private function updateAdicionalesCotizacion($request, $adicionalesCotizacion, $adicionalElegido, $renglonesCotizacion = null) {

        // Si hay renglones nuevos elegidos y adicionales elegidos
        if ($adicionalesCotizacion && $renglonesCotizacion) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Por cada adicional
            foreach ($adicionalesCotizacion as $adicional) {

                $idAdicional = $adicional['id_adicional'];

                $adicionalCotizacion = $em->getRepository('ADIFComprasBundle:AdicionalCotizacion')
                        ->find($idAdicional);

                if (!$adicionalCotizacion) {
                    throw $this->createNotFoundException('No se puede encontrar la entidad AdicionalCotizacion.');
                }

                $adicionalCotizacion->setAdicionalElegido($adicionalElegido);

                $accion = $request->request->get('accion');

                if (null != $accion) {

                    // Si se apretó el boton "Guardar borrador"
                    if ('save' == $accion) {

                        // Seteo el estado "Borrador"
                        $adicionalCotizacion->setEstadoComparacionCotizacion(
                                $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                                        ->findOneBy(
                                                array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_BORRADOR), //
                                                array('id' => 'desc'), 1, 0));
                    }

                    // Sino, si se apretó el boton "Generar"
                    else if ('generar' == $accion) {

                        // Seteo el estado "Generada"
                        $adicionalCotizacion->setEstadoComparacionCotizacion(
                                $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                                        ->findOneBy(
                                                array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_GENERADA), //
                                                array('id' => 'desc'), 1, 0));
                    }
                }
            }
        }
    }

    /**
     * 
     * @param type $request
     * @param type $renglonesCotizacionElegidos
     * @param type $idRequerimiento
     */
    private function updateCotizacionesElegidas($request, $renglonesCotizacionElegidos, $idRequerimiento) {

        $mensajeFlashComparacionCotizacionesEjecutado = false;
        $mensajeFlashCotizadoresGuardadosEjecutado = false;

        if ($renglonesCotizacionElegidos) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Por cada renglon cotizado que se le indicó un ganador
            foreach ($renglonesCotizacionElegidos as $renglon) {

                $idProveedor = $renglon['id_proveedor'];

                $idCotizacion = $renglon['id_cotizacion'];

                $renglonesCotizados = $renglon['renglones_cotizados'];

                // Por cada proveedor, obtengo los id de los renglones que ganó
                foreach ($renglonesCotizados as $idRenglonCotizacion) {

                    // Seteo los renglones cotizados como "elegidos"
                    $renglonCotizacion = $em->getRepository('ADIFComprasBundle:RenglonCotizacion')
                            ->find($idRenglonCotizacion);

                    $renglonCotizacion->setCotizacionElegida(true);

                    $accion = $request->request->get('accion');

                    if (null != $accion) {

                        // Si se apretó el boton "Guardar borrador"
                        if ('save' == $accion) {

                            // Seteo el estado "Borrador"
                            $renglonCotizacion->setEstadoComparacionCotizacion(
                                    $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                                            ->findOneBy(
                                                    array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_BORRADOR), //
                                                    array('id' => 'desc'), 1, 0));

                            if (!$mensajeFlashComparacionCotizacionesEjecutado) {

                                $mensajeFlashComparacionCotizacionesEjecutado = true;

                                $this->get('session')->getFlashBag()
                                        ->add('success', 'La comparación de cotizaciones fue guardada como borrador correctamente.');
                            }
                        }
                        // Sino, si se apretó el boton "Generar"
                        else if ('generar' == $accion) {

                            // Seteo el estado "Generada"
                            $renglonCotizacion->setEstadoComparacionCotizacion(
                                    $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                                            ->findOneBy(
                                                    array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_GENERADA), //
                                                    array('id' => 'desc'), 1, 0));

                            if (!$mensajeFlashCotizadoresGuardadosEjecutado) {

                                $mensajeFlashCotizadoresGuardadosEjecutado = true;

                                $this->get('session')->getFlashBag()
                                        ->add('success', 'Los cotizadores ganadores fueron guardados correctamente.');
                            }
                        }
                    }
                }

                if ('generar' == $accion) {

                    try {

                        // Por cada proveedor, genero su correspondiente OC
                        $this->get('adif.orden_compra_service')
                                ->generarOrdenCompraFromCotizacion($idProveedor, $idRequerimiento, $idCotizacion, $renglonesCotizados);
                    } //.
                    catch (\Exception $e) {

                        $this->get('session')->getFlashBag()
                                ->add('error', 'Hubo un error al generar las órdenes de compra.');
                    }
                }
            }
        }
    }

    /**
     * 
     * @param type $request
     */
    private function updateCotizacionesNoElegidas($request) {

        $renglonesCotizacionNoElegidos = $request->request
                ->get('renglones_cotizaciones_no_elegidos');

        if ($renglonesCotizacionNoElegidos) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Por cada renglon cotizado que se le indicó un ganador
            foreach ($renglonesCotizacionNoElegidos as $renglon) {

                $renglonesCotizados = $renglon['renglones_cotizados'];

                // Por cada proveedor, obtengo los id de los renglones
                foreach ($renglonesCotizados as $idRenglonCotizacion) {

                    // Seteo los renglones cotizados como "no elegidos"
                    $renglonCotizacion = $em->getRepository('ADIFComprasBundle:RenglonCotizacion')
                            ->find($idRenglonCotizacion);

                    $renglonCotizacion->setCotizacionElegida(false);
                }
            }
        }
    }

    /**
     * 
     * @param type $em
     * @param Request $request
     * @param Cotizacion $cotizacion
     */
    private function updateAdjuntos($em, Request $request, Cotizacion $cotizacion) {

        $requestArchivosEliminados = $request->request->get('archivos_eliminados');

        $requestArchivosAgregados = $request->files->get("adif_comprasbundle_cotizacion");

        // Por cada adjunto original
        if (!empty($requestArchivosEliminados)) {
            // Hot-fix @gluis - 17/03/2016
            
            foreach ($cotizacion->getArchivos() as $adjunto) {

                // Si fue eliminado
                if (in_array($adjunto->getId(), $requestArchivosEliminados)) {

                    $cotizacion->removeArchivo($adjunto);

                    $em->remove($adjunto);
                }
            }
        }

        if ($requestArchivosAgregados["archivos"]) {

            foreach ($requestArchivosAgregados ["archivos"] as $adjunto) {

                if ($adjunto["archivo"] != null) {

                    $cotizacionArchivo = new CotizacionArchivo();

                    $cotizacionArchivo->setArchivo($adjunto["archivo"]);

                    $cotizacionArchivo->setCotizacion($cotizacion);

                    $cotizacionArchivo->setNombre($adjunto["archivo"]->getClientOriginalName());

                    $cotizacion->addArchivo($cotizacionArchivo);
                }
            }
        }
    }

}
