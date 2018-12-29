<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EgresoValor\EgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRendicionEgresoValor;
use Symfony\Component\HttpFoundation\JsonResponse;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoReconocimientoEgresoValor;
use ADIF\ContableBundle\Form\EgresoValor\EgresoValorType;
use ADIF\ContableBundle\Form\EgresoValor\ResponsableEgresoValorType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\BaseBundle\Controller\ExporterController;
use ADIF\ContableBundle\Controller\EgresoValor\ComprobanteEgresoValorController;
use ADIF\BaseBundle\Entity\EntityManagers;

use mPDF;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * EgresoValor\EgresoValor controller.
 *
 * @Route("/egresovalor")
 */
class EgresoValorController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Egresos de valor' => $this->generateUrl('egresovalor')
        );
    }

    /**
     * Lists all EgresoValor\EgresoValor entities.
     *
     * @Route("/", name="egresovalor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Egresos de valor'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Egresos de valor',
            'page_info' => 'Lista de egresos de valor'
        );
    }

    /**
     * Creates a new EgresoValor\EgresoValor entity.
     *
     * @Route("/insertar", name="egresovalor_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:new.html.twig")
     */
    public function createAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipoEgresoValor = $request->request
                        ->get('adif_contablebundle_egresovalor_egresovalor', false)['tipoEgresoValor'];

        $egresoValor = ConstanteTipoEgresoValor::getSubclass($tipoEgresoValor);


        $form = $this->createCreateForm($egresoValor);
        $form->handleRequest($request);

        if ($form->isValid()) {

            if ($this->validarEgresoValor($em, $egresoValor)) {

                // Seteo el EstadoEgresoValor
                $egresoValor->setEstadoEgresoValor(
                        $em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                                ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE)
                );


                // Genero la AutorizacionContable            
                $ordenPagoService = $this->get('adif.orden_pago_service');

                /*
                if ($egresoValor->getTipoEgresoValor()->getId() == ConstanteTipoEgresoValor::FONDO_FIJO_SERVICIOS) {
                    $egresoValor->setGerencia(null);
                }
                */
                
                $importe = $egresoValor->getImporte();
                $concepto = $egresoValor->getTipoEgresoValor()
                        . ' - ' . $egresoValor->getResponsableEgresoValor()
                        . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

                $autorizacionContable = $ordenPagoService
                        ->crearAutorizacionContableEgresoValor($em, $egresoValor, $importe, $concepto);

                $em->persist($egresoValor);
                $em->flush();

                $this->get('session')->getFlashBag()
                        ->add('success', "Se gener&oacute; la autorizaci&oacute;n "
                                . "contable con &eacute;xito, con un "
                                . "importe de $ " . number_format($importe, 2, ',', '.'));

                $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                        . $this->generateUrl($autorizacionContable->getPathAC()
                                . '_print', ['id' => $autorizacionContable->getId()])
                        . '" class="link-imprimir-op">aqu&iacute;</a>';

                $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);

                return $this->redirect($this->generateUrl('egresovalor'));
            }
        }
        $request->attributes->set('form-error', true);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $egresoValor,
            'topes' => $this->getConfiguracion(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear egreso de valor',
        );
    }

    /**
     * Creates a form to create a EgresoValor\EgresoValor entity.
     *
     * @param EgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EgresoValor $entity) {
        $form = $this->createForm(new EgresoValorType(), $entity, array(
            'action' => $this->generateUrl('egresovalor_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EgresoValor\EgresoValor entity.
     *
     * @Route("/crear", name="egresovalor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EgresoValor();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'topes' => $this->getConfiguracion(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear egreso de valor'
        );
    }

    /**
     * Finds and displays a EgresoValor\EgresoValor entity.
     *
     * @Route("/{id}", name="egresovalor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Egreso de valor'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver egreso de valor'
        );
    }

    /**
     * Displays a form to edit an existing EgresoValor\EgresoValor entity.
     *
     * @Route("/editar/{id}", name="egresovalor_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'topes' => $this->getConfiguracion(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar egreso de valor'
        );
    }

    /**
     * Creates a form to edit a EgresoValor\EgresoValor entity.
     *
     * @param EgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EgresoValor $entity) {
        $form = $this->createForm(new EgresoValorType(), $entity, array(
            'action' => $this->generateUrl('egresovalor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EgresoValor\EgresoValor entity.
     *
     * @Route("/actualizar/{id}", name="egresovalor_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $editForm = $this->createEditForm($egresoValor);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($this->validarEgresoValor($em, $egresoValor, 0)) {
                // Seteo el EstadoEgresoValor
                $egresoValor->setEstadoEgresoValor(
                        $em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                                ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE)
                );

                // Genero la AutorizacionContable            
                $ordenPagoService = $this->get('adif.orden_pago_service');

                $importe = $egresoValor->getImporte();
                $concepto = $egresoValor->getTipoEgresoValor() . ' - ' . $egresoValor->getResponsableEgresoValor() . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

                $ordenPagoService->crearAutorizacionContableEgresoValor($em, $egresoValor, $importe, $concepto);

                $em->flush();

                return $this->redirect($this->generateUrl('egresovalor'));
            }
        }
        $request->attributes->set('form-error', true);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $egresoValor,
            'topes' => $this->getConfiguracion(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar egreso de valor'
        );
    }

    /**
     * Deletes a EgresoValor\EgresoValor entity.
     *
     * @Route("/borrar/{id}", name="egresovalor_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('egresovalor'));
    }

    /**
     * Reposicion de EgresoValor.
     *
     * @Route("/reponer/{id}", name="egresovalor_reponer")
     * @Method("GET|POST")
     * @Template()
     */
    public function reponerAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        //si el responsable es nuevo
        if (($egresoValor->getResponsableEgresoValor()->getNombre() != $request->request->get('nombre'))//
                || ($egresoValor->getResponsableEgresoValor()->getIdTipoDocumento() != $request->request->get('tipoDocumento')) //
                || ($egresoValor->getResponsableEgresoValor()->getNroDocumento() != $request->request->get('numeroDocumento'))) {

            $nuevoResponsable = new ResponsableEgresoValor();
            $nuevoResponsable->setNombre($request->request->get('nombre'))
                    ->setIdTipoDocumento($request->request->get('tipoDocumento'))
                    ->setNroDocumento($request->request->get('numeroDocumento'));

            $egresoValor->setResponsableEgresoValor($nuevoResponsable);
        }


        // Genero la AutorizacionContable            
        $ordenPagoService = $this->get('adif.orden_pago_service');

        $importe = $egresoValor->getImporteAReponer();
        $concepto = 'Reposición de ' . strtolower($egresoValor->getTipoEgresoValor()) . ' - ' . $egresoValor->getResponsableEgresoValor() . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

        $ordenPagoService
                ->crearAutorizacionContableEgresoValor($em, $egresoValor, $importe, $concepto);

        $egresoValor->setEstadoEgresoValor(
                $em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE)
        );

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se gener&oacute; la autorizaci&oacute;n "
                        . "contable con &eacute;xito, con un "
                        . "importe de $ " . number_format($importe, 2, ',', '.'));

        return $this->redirect($this->generateUrl('egresovalor'));
    }

    /**
     * Devuelve el template para pagar reponer egreso valor
     *
     * @Route("/form_reponer", name="egresovalor_reponer_form")
     * @Method("POST")   
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:reponer_form.html.twig")
     */
    public function getFormReponerAction(Request $request) {

        $idEgresoValor = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($idEgresoValor);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        
        $form = $this->createForm(new ResponsableEgresoValorType($this->getDoctrine()->getManager(EntityManagers::getEmRrhh())), $egresoValor->getResponsableEgresoValor(), array(
            'action' => $this->generateUrl('egresovalor_reponer', array('id' => $idEgresoValor)),
            'method' => 'POST'
        ));

        return array(
            'form' => $form->createView(),
            'montoAReponer' => $egresoValor->getImporteAReponer()
        );
    }

    /**
     *
     * @Route("/{id}/historico", name="egresovalor_historico")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:historico.html.twig")
     */
    public function showHistoricoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $historicos = [];

        $ultimaReposicion = null;

        // Por cada Reposicion
        foreach ($egresoValor->getReposiciones() as $reposicion) {

            /* @var $reposicion \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor */

            /* @var $ordenPagoReposicion \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */
            $ordenPagoReposicion = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')
                    ->findOneByReposicionEgresoValor($reposicion);

            if (($reposicion->getEsCreacion()) && ($ordenPagoReposicion->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA)) {
                $ultimaReposicion = $reposicion;
            }
            $impresionRendicion = [];
            if ((!$reposicion->getEsCreacion()) && ($ordenPagoReposicion->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA)) {
                /* @var $rendicion RendicionEgresoValor */
                foreach ($egresoValor->getRendiciones() as $rendicion) {
                    if ($ultimaReposicion != null && ($rendicion->getFechaRendicion() >= $ultimaReposicion->getFechaReposicion()) && ($rendicion->getFechaRendicion() <= $reposicion->getFechaReposicion())) {
                        $impresionRendicion[] = $rendicion->getId();
                    }
                }
                $ultimaReposicion = $reposicion;
            }

            $historicos[] = [
                'id' => $reposicion->getId(),
                'fecha' => $reposicion->getFechaReposicion(),
                'tipo' => $reposicion->getEsCreacion() ? 'Creaci&oacute;n' : 'Reposici&oacute;n',
                'font_color' => 'font-yellow-gold',
                'responsable' => $reposicion->getResponsableEgresoValor(),
                'cambiaResponsable' => $reposicion->getCambiaResponsable(),
                'importe' => $reposicion->getMonto(),
                'link_color' => 'yellow-gold',
                'link_icon' => 'fa-refresh',
                'estado' => $ordenPagoReposicion->getEstadoOrdenPago(),
                'link' => $this->generateUrl('ordenpagoegresovalor_show', array('id' => $ordenPagoReposicion->getId())),
                'link_impresion_rendicion' => sizeof($impresionRendicion) > 0 ? $this->generarLinkImpresionRendicion($impresionRendicion, 'yellow-gold') : ''
            ];
        }

        // Por cada Rendicion
        foreach ($egresoValor->getRendiciones() as $rendicion) {

            /* @var $rendicion \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor */

            $historicos[] = [
                'id' => $rendicion->getId(),
                'fecha' => $rendicion->getFechaRendicion(),
                'tipo' => 'Rendici&oacute;n',
                'font_color' => 'font-blue',
                'responsable' => $rendicion->getResponsableEgresoValor(),
                'cambiaResponsable' => false,
                'importe' => $rendicion->getImporteRendido(),
                'link_color' => 'blue',
                'link_icon' => 'fa-sort-amount-asc',
                'estado' => '-',
                'link' => $this->generateUrl('egresovalor_rendicion_show', array('id' => $rendicion->getId())),
                'link_impresion_rendicion' => ''
            ];
        }

        // Por cada Reconocimiento
        foreach ($egresoValor->getReconocimientos() as $reconocimiento) {

            /* @var $reconocimiento \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor */

            $ordenPagoReconocimiento = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoReconocimientoEgresoValor')
                    ->findOneByReconocimientoEgresoValor($reconocimiento);

            if ($reconocimiento->getEstadoReconocimientoEgresoValor()->getCodigo() != ConstanteEstadoReconocimientoEgresoValor::ESTADO_GENERADO) {

                $historicos[] = [
                    'id' => $reconocimiento->getId(),
                    'fecha' => $reconocimiento->getFechaReconocimiento(),
                    'tipo' => ($reconocimiento->getEstadoReconocimientoEgresoValor()->getCodigo() == ConstanteEstadoReconocimientoEgresoValor::ESTADO_RECONOCIDO) ? 'Reconocimiento de gasto' : 'Gasto a ganancia',
                    'font_color' => ($reconocimiento->getEstadoReconocimientoEgresoValor()->getCodigo() == ConstanteEstadoReconocimientoEgresoValor::ESTADO_RECONOCIDO) ? 'font-red' : 'font-purple-studio',
                    'responsable' => $reconocimiento->getResponsableEgresoValor(),
                    'importe' => $reconocimiento->getMonto(),
                    'cambiaResponsable' => false,
                    'link_color' => ($reconocimiento->getEstadoReconocimientoEgresoValor()->getCodigo() == ConstanteEstadoReconocimientoEgresoValor::ESTADO_RECONOCIDO) ? 'red' : 'purple-studio',
                    'link_icon' => ($reconocimiento->getEstadoReconocimientoEgresoValor()->getCodigo() == ConstanteEstadoReconocimientoEgresoValor::ESTADO_RECONOCIDO) ? 'fa-user' : 'fa-line-chart',
                    'estado' => ($ordenPagoReconocimiento != null) ? $ordenPagoReconocimiento->getEstadoOrdenPago() : '',
                    'link' => ($ordenPagoReconocimiento != null) ? $this->generateUrl('ordenpagoreconocimiento_show', array('id' => $ordenPagoReconocimiento->getId())) : '',
                    'link_impresion_rendicion' => ''
                ];
            }
        }

        if ($egresoValor->getFechaCierre() != null) {

            $impresionRendicion = [];
            if ($ultimaReposicion != null) {
                /* @var $rendicion RendicionEgresoValor */
                foreach ($egresoValor->getRendiciones() as $rendicion) {
                    if (($rendicion->getFechaRendicion() >= $ultimaReposicion->getFechaReposicion()) && ($rendicion->getFechaRendicion() <= $egresoValor->getFechaCierre())) {
                        $impresionRendicion[] = $rendicion->getId();
                    }
                }
                $ultimaReposicion = $reposicion;
            }

            $historicos[] = [
                'id' => $egresoValor->getId(),
                'fecha' => $egresoValor->getFechaCierre(),
                'tipo' => 'Cierre',
                'font_color' => 'font-red',
                'responsable' => $egresoValor->getResponsableEgresoValor(),
                'importe' => $egresoValor->getSaldo(),
                'cambiaResponsable' => false,
                'link_color' => 'red',
                'link_icon' => 'fa-file-pdf-o',
                'estado' => '',
                'link' => $this->generateUrl('egresovalor_cierre_print', array('id' => $egresoValor->getId())),
                'link_impresion_rendicion' => sizeof($impresionRendicion) > 0 ? $this->generarLinkImpresionRendicion($impresionRendicion, 'red') : ''
            ];
        }

        // Ordeno el historico por fecha
        usort($historicos, function ($a, $b) {

            return ($a['fecha'] < $b['fecha']) ? -1 : 1;
        });


        $responsable = $egresoValor->getTipoEgresoValor()->__toString()
                . ' - ' . $egresoValor->getResponsableEgresoValor()->getNombre();

        $bread = $this->base_breadcrumbs;
        $bread[$responsable] = null;
        $bread['Hist&oacute;rico'] = null;

        return array(
            'entity' => $egresoValor,
            'historicos' => $historicos,
            'breadcrumbs' => $bread,
            'page_title' => 'Histórico de egreso de valor'
        );
    }

    /**
     * Rendición de EgresoValor.
     *
     * @Route("/rendir/{id}", name="egresovalor_rendir")
     * @Method("GET|POST")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:index.html.twig")
     */
    public function rendirAction($id) {

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        /* @var $rendicionEgresoValor \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor */
        $rendicionEgresoValor = $egresoValor->getRendicionEgresoValor();

        foreach ($rendicionEgresoValor->getComprobantes() as $comprobante) {
            $comprobante->setFechaContable(new \DateTime());
        }

        // Genero el asiento contable y presupuestario
        $numeroAsiento = $this->get('adif.asiento_service')
                ->generarAsientoFromRendicionEgresoValor($rendicionEgresoValor, $this->getUser());

        // Si no hubo errores en los asientos
        if ($numeroAsiento != -1) {

            $rendicionEgresoValor->setEstadoRendicionEgresoValor(
                    $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoRendicionEgresoValor')
                            ->findOneByCodigo(ConstanteEstadoRendicionEgresoValor::ESTADO_GENERADA)
            );

            // Comienzo la transaccion
            $emContable->getConnection()->beginTransaction();

            try {
                $emContable->flush();

                $emContable->getConnection()->commit();

                $this->get('session')->getFlashBag()
                        ->add('success', "La rendici&oacute;n se gener&oacute; con &eacute;xito");

                $dataArray = [
                    'data-id-rendicion' => $rendicionEgresoValor->getId(),
                    'data-fecha-ultima-reposicion' => $egresoValor->getUltimaReposicionPagada()->getFechaReposicion()->format('d/m/Y')
                ];

                $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
            } //.
            catch (\Exception $e) {

                $emContable->getConnection()->rollback();
                $emContable->close();

                throw $e;
            }
        }

        return $this->redirect($this->generateUrl('egresovalor'));
    }

    /**
     *
     * @Route("/reconocimientoGasto/{id}", name="egresovalor_reconocimientogasto")
     * @Method("POST|GET")
     */
    public function reconocimientoGastoAction($id) {

        $em = $this->getDoctrine(
                )->getManager($this->getEntityManager());

        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $estadoGenerado = $em->getRepository('ADIFContableBundle:EgresoValor\EstadoReconocimientoEgresoValor')
                ->findOneByCodigo(ConstanteEstadoReconocimientoEgresoValor::ESTADO_GENERADO);

        $reconocimientoEgresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\ReconocimientoEgresoValor')
                ->findOneBy(
                array(
                    'estadoReconocimientoEgresoValor' => $estadoGenerado,
                    'egresoValor' => $egresoValor
                )
        );

        if (!$reconocimientoEgresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ReconocimientoEgresoValor.');
        }

        $egresoValor->setEstadoEgresoValor(
                $em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE)
        );

        // Genero la AutorizacionContable            
        $ordenPagoService = $this->get('adif.orden_pago_service');

        $importe = $reconocimientoEgresoValor->getMonto();
        $concepto = 'Reconocimiento de gasto ' . strtolower($egresoValor->getTipoEgresoValor()) . ' - ' . $egresoValor->getResponsableEgresoValor() . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

        $ordenPagoService
                ->crearAutorizacionContableReconocimientoEgresoValor($em, $reconocimientoEgresoValor, $importe, $concepto);

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se realizó el reconocimiento de gasto con &eacute;xito");

        return $this->redirect($this->generateUrl('egresovalor'));
    }

    /**
     *
     * @Route("/cierreGasto/{id}", name="egresovalor_cierregasto")
     * @Method("POST|GET")
     */
    public function cierreGastoAction($id) {

        $em = $this->getDoctrine(
                )->getManager($this->getEntityManager());

        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $estadoGenerado = $em->getRepository('ADIFContableBundle:EgresoValor\EstadoReconocimientoEgresoValor')
                ->findOneByCodigo(ConstanteEstadoReconocimientoEgresoValor::ESTADO_GENERADO);

        $reconocimientoEgresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\ReconocimientoEgresoValor')
                ->findOneBy(
                array(
                    'estadoReconocimientoEgresoValor' => $estadoGenerado,
                    'egresoValor' => $egresoValor
                )
        );

        if (!$reconocimientoEgresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ReconocimientoEgresoValor.');
        }

        $reconocimientoEgresoValor->setEstadoReconocimientoEgresoValor(
                $em->getRepository('ADIFContableBundle:EgresoValor\EstadoReconocimientoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoReconocimientoEgresoValor::ESTADO_NO_RECONOCIDO)
        );

        // Genero el asiento contable y presupuestario
        $numeroAsiento = $this->get('adif.asiento_service')
                ->generarAsientoCierreReconocimientoEgresoValor($reconocimientoEgresoValor, $this->getUser());

        // Si el asiento contable falló
        if ($numeroAsiento != -1) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {

                $em->flush();

                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()
                        ->add('success', "El excedente de gasto gener&oacute; ganancia");

                $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($numeroAsiento, array());
            } //.
            catch (\Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
        }

        return $this->redirect($this->generateUrl('egresovalor'));
    }

    /**
     *
     * @Route("/cierre/{id}", name="egresovalor_cierre")
     * @Method("POST|GET")
     */
    public function cierreAction($id) {

        $em = $this->getDoctrine(
                )->getManager($this->getEntityManager());

        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        // Seteo el EstadoEgresoValor
        $egresoValor->setEstadoEgresoValor(
                $em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CERRADO)
        );

        $egresoValor->setFechaCierre(new \DateTime());

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se realizó el cierre con &eacute;xito");

        return $this->redirect($this->generateUrl('egresovalor'));
    }

    /**
     * 
     * @return type
     * @throws type
     */
    private function getConfiguracion() {
        $em = $this->getDoctrine(
                )->getManager($this->getEntityManager());

        $configuraciones = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->findAll();

        if (!$configuraciones) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValorGerencia.');
        }
        $tiposEgresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor')->findAll();

        if (!$tiposEgresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoEgresoValor.');
        }

        $configuracionesArray = [];

        foreach ($tiposEgresoValor as $tipoEgresoValor) {
            /* @var $tipoEgresoValor \ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor */
            $configuracionesArray[$tipoEgresoValor->getId()] = [];
        }

        foreach ($configuraciones as $configuracion) {

            /* @var $configuracion \ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia */

            $configuracionesArray[$configuracion->getTipoEgresoValor()->getId()][$configuracion->getIdGerencia()] = $configuracion->getMonto();
        }

        return $configuracionesArray;
    }

    /**
     * 
     * @param type $em
     * @param EgresoValor $egresoValorNuevo
     * @param type $total
     * @return boolean
     */
    private function validarEgresoValor($em, EgresoValor $egresoValorNuevo, $total = 1) {

        /*
          if ($egresoValorNuevo->getTipoEgresoValor()->getLimitaGerencia() || $egresoValorNuevo->getTipoEgresoValor()->getLimitaPersona()) {

          $cantidad = $egresoValorNuevo->getTipoEgresoValor()->getCantidadMaxima();

          if ($egresoValorNuevo->getTipoEgresoValor()->getLimitaGerencia()) {

          $egresosValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
          ->findByTipoEgresoValorYGerencia(
          $egresoValorNuevo->getTipoEgresoValor(), //
          $egresoValorNuevo->getIdGerencia()
          );

          $egresosValorFiltrados = array_filter($egresosValor, function($egresosValor) {
          return $egresosValor->getSaldo() > 0;
          });

          foreach ($egresosValorFiltrados as $egresoValor) {
          $total++;
          }

          if ($total > $cantidad) {
          $this->get('session')->getFlashBag()
          ->add('error', "La gerencia indicada tiene la m&aacute;xima cantidad de " . $egresoValorNuevo->getTipoEgresoValor()->getDenominacion() . " posibles.");
          }
          } else {

          $egresosValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
          ->findByTipoEgresoValorYPersona(
          $egresoValorNuevo->getTipoEgresoValor(), //
          $egresoValorNuevo->getResponsableEgresoValor()->getNroDocumento(), //
          $egresoValorNuevo->getResponsableEgresoValor()->getIdTipoDocumento()
          );

          $egresosValorFiltrados = array_filter($egresosValor, function($egresosValor) {
          return $egresosValor->getSaldo() > 0;
          });

          foreach ($egresosValorFiltrados as $egresoValor) {
          $total++;
          }

          if ($total > $cantidad) {
          $this->get('session')->getFlashBag()
          ->add('error', "La persona indicada tiene la m&aacute;xima cantidad de " . $egresoValorNuevo->getTipoEgresoValor()->getDenominacion() . " asignados.");
          }
          }

          return $total <= $cantidad;
          } else {
          return true;
          }
         */

        return true;
    }

    /**
     * @Route("/estados", name="egreso_valor_estados")
     */
    public function listaEstadoEgresoValorAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstado')
                ->orderBy('e.denominacionEstado', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'egreso_valor_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * @Route("/tipos", name="egreso_valor_tipos")
     */
    public function listaTipoEgresoValorAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacion')
                ->orderBy('e.denominacion', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'egreso_valor_tipos')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * Tabla para EgresoValor.
     *
     * @Route("/index_table/", name="egresovalor_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('tipoEgresoValor', 'tipoEgresoValor');
        $rsm->addScalarResult('carpeta', 'carpeta');
        $rsm->addScalarResult('responsableEgresoValor', 'responsableEgresoValor');
        $rsm->addScalarResult('fechaCreacion', 'fechaCreacion');
        $rsm->addScalarResult('gerencia', 'gerencia');
        $rsm->addScalarResult('saldo', 'saldo');
        $rsm->addScalarResult('permiteReposicion', 'permiteReposicion');
        $rsm->addScalarResult('porcentajeRendido', 'porcentajeRendido');
        $rsm->addScalarResult('estadoEgresoValor', 'estadoEgresoValor');
        $rsm->addScalarResult('aliasTipoImportancia', 'aliasTipoImportancia');
        $rsm->addScalarResult('codigoEstadoEgresoValor', 'codigoEstadoEgresoValor');
        $rsm->addScalarResult('tieneRendicionEgresoValor', 'tieneRendicionEgresoValor');
        $rsm->addScalarResult('minimoRendicion', 'minimoRendicion');
        
        $native_query = $em->createNativeQuery('
            SELECT
                id,
                tipoEgresoValor,
                carpeta,
                responsableEgresoValor,
                fechaCreacion,
                gerencia,
                saldo,
                permiteReposicion,
                porcentajeRendido,
                estadoEgresoValor,
                aliasTipoImportancia,
                codigoEstadoEgresoValor,
                tieneRendicionEgresoValor,
                minimoRendicion
            FROM
                vistaegresosvalor           
        ', $rsm);

        $entities = $native_query->getResult();

        return $this->render('ADIFContableBundle:EgresoValor\EgresoValor:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * @Route("/showcierre/{id}", name="egresovalor_cierre_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValor:cierre.show.html.twig")
     */
    public function showCierreAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getResponsableEgresoValor()->__toString()] = null;
        $bread['Cierre'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Cierre egreso de valor'
        );
    }

    /**
     *
     * @Route("/editar_fecha/", name="egresovalor_editar_fecha")
     */
    public function updateFechaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idRendicion = $request->request->get('id_rendicion');

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        // Si existe el asiento Y el usuario logueado genera asientos contables
        if (!$asientoContable && false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        /* @var $rendicion \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor */
        $rendicion = $em->getRepository('ADIFContableBundle:EgresoValor\RendicionEgresoValor')
                ->find($idRendicion);

        $rendicion->setFechaRendicion(\DateTime::createFromFormat('d/m/Y', $fecha));

        $em->persist($rendicion);

        $em->flush();

        return new Response();
    }

    /**
     * @Route("/printCierre/{id}", name="egresovalor_cierre_print")
     * @Method("GET")
     * @Template()
     */
    public function printCierreAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $fechaCreacion = new \DateTime();

        foreach ($entity->getReposiciones() as $reposicion) {

            /* @var $reposicion \ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor */

            if ($reposicion->getEsCreacion()) {

                /* @var $ordenPagoReposicion \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */
                $ordenPagoReposicion = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')
                        ->findOneByReposicionEgresoValor($reposicion);

                if ($ordenPagoReposicion->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                    $fechaCreacion = $ordenPagoReposicion->getFechaOrdenPago();
                }
            }
        }

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView('ADIFContableBundle:EgresoValor\EgresoValor:cierre.print.html.twig', array(
            'entity' => $entity,
            'fechaCreacion' => $fechaCreacion
                )
        );
        $html .= '</body></html>';

        $filename = 'CierreCajaChica.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);
        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    private function generarLinkImpresionRendicion($collection, $color) {
        $stringIds = implode(",", $collection);

        return '<a href="' . $this->generateUrl('impresion_resumen_rendicion', array('ids' => $stringIds)) . '" class="btn btn-xs tooltips" data-original-title="Imprimir resumen rendici&oacute;n">
                    <i class="fa fa-search font-' . $color . '"></i>
                </a>';
    }

    /**
     * @Route("/printResumenRendicion/", name="impresion_resumen_rendicion")
     * @Method("GET")
     */
    public function impresionResumenRendicionAction(Request $request) {
        $arrayIds = explode(',', $request->query->get('ids'));
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rendicionController = new ComprobanteEgresoValorController();
        $rendicionController->setContainer($this->container);

        $comprobantes = [];

        $egresoValor = '';

        foreach ($arrayIds as $id) {
            /* @var $entity \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor */
            $entity = $em->getRepository('ADIFContableBundle:EgresoValor\RendicionEgresoValor')->find($id);
            $comprobantes = array_merge($comprobantes, $rendicionController->getComprobantes($entity));
            $egresoValor = 'Rendicion de '
                    . $entity->getEgresoValor()->getTipoEgresoValor()
                    . ' - ' . $entity->getEgresoValor()->getResponsableEgresoValor()->getNombre()
                    . ($entity->getEgresoValor()->getGerencia() ? (' - ' . $entity->getEgresoValor()->getGerencia()) : '');
        }

        $content = [
            'title' => 'resumen_rendicion',
            'sheets' => []
        ];

        $content['sheets'][0] = [
            'title' => 'resumen_rendicion',
            'tables' => []
        ];

        $headers = [];
        $headers[] = [
            'texto' => 'Fecha',
            'formato' => 'text',
        ];
        $headers[] = [
            'texto' => 'Comprobante',
            'formato' => 'text',
        ];
        $headers[] = [
            'texto' => 'Numero',
            'formato' => 'text',
        ];
        $headers[] = [
            'texto' => 'Proveedor',
            'formato' => 'text',
        ];
        $headers[] = [
            'texto' => 'Total',
            'formato' => 'currency',
        ];

        $rows = [];

        foreach ($comprobantes as $value) {
            $row = [];
            $row[] = $value['fecha']->format('d/m/Y');
            $row[] = $value['tipoComprobante'];
            $row[] = $value['numero'];
            $row[] = $value['proveedor'];
            $row[] = '$ ' . number_format($value['importe'], 2, ',', '.');
            $rows[] = $row;
        }

        $content['sheets'][0]['tables'][0] = [
            'title' => $egresoValor,
            'titulo_alternativo' => '',
            'headers' => json_encode($headers),
            'data' => json_encode($rows)
        ];

        $query = [];
        $query['content'] = json_encode($content);

        $requestImpresion = new Request($query);
        $eController = new ExporterController();
        $eController->setContainer($this->container);
        return $eController->exportAction($requestImpresion);
    }

}
