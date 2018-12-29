<?php

namespace ADIF\RecursosHumanosBundle\Controller\Consultoria;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\CertificadoExencion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\RecursosHumanosBundle\Form\Consultoria\ConsultorType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Consultoria\Consultor controller.
 *
 * @Route("/consultor")
 */
class ConsultorController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Consultores' => $this->generateUrl('consultor')
        );
    }

    /**
     * Lists all Consultoria\Consultor entities.
     *
     * @Route("/", name="consultor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Consultores',
            'page_info' => 'Lista de consultores'
        );
    }

    /**
     * Tabla para Consultoria\Consultor .
     *
     * @Route("/index_table/", name="consultor_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->findByActivo(1);

        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = null;

        return $this->render('ADIFRecursosHumanosBundle:Consultoria/Consultor:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Lists all Consultoria\Consultor entities.
     *
     * @Route("/historico", name="consultor_historico")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Consultoria/Consultor:index_historico.html.twig")
     */
    public function indexHistoricoAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Consultores Historicos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Consultores historicos',
            'page_info' => 'Lista de consultores historicos'
        );
    }

    /**
     * Tabla para Consultoria\Consultor historicos.
     *
     * @Route("/historico/index_table/", name="consultor_table_historico")
     * @Method("GET|POST")
     */
    public function indexTableHistoricoAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->findByActivo(0);

        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = null;

        return $this->render('ADIFRecursosHumanosBundle:Consultoria/Consultor:index_table_historico.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Consultoria\Consultor entity.
     *
     * @Route("/insertar", name="consultor_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:new.html.twig")
     */
    public function createAction(Request $request) {

        $consultor = new Consultor();

        $form = $this->createCreateForm($consultor);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $emRRHH = $this->getDoctrine()->getManager($this->getEntityManager());

            // Abre transacción
            $emRRHH->getConnection()->beginTransaction();

            try {

                $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

                // Actualizo las fechas impositivas
                $this->updateFechasDatosImpositivos($consultor);

                // Si la Cuenta NO fue seteada
                if ($consultor->getCuenta() == null || $consultor->getCuenta()->getCbu() == null) {
                    $consultor->setCuenta(null);
                }

                // Actualiza el Convenio Multilateral
                $this->updateConvenioMultilateral($emRRHH, $consultor);

                // Actualiza los Certificados de Exención
                $this->updateCertificadosExencion($emCompras, $consultor);

                // A cada CAI, le seteo el Consultor
                foreach ($consultor->getCais() as $cai) {
                    $cai->setConsultor($consultor);
                }

                // Actualiza los archivos adjuntos
                $this->updateAdjuntos($consultor);

                $emCompras->persist($consultor->getDatosImpositivos());

                // Persisto los certificados de exencion al Consultor
                $this->persistirCertificadosExencion($emCompras, $consultor);

                $emCompras->flush();

                // Asigno los certificados de exencion al Consultor
                $this->asignarCertificadosExencion($consultor);

                $consultor->setIdDatosImpositivos($consultor->getDatosImpositivos()->getId());

                $emRRHH->persist($consultor);
                $emRRHH->flush();

                $emRRHH->getConnection()->commit();
            } // 
            catch (\Exception $e) {

                // Rollback
                $emRRHH->getConnection()->rollback();
                $emRRHH->close();

                throw $e;
            }

            return $this->redirect($this->generateUrl('consultor'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $consultor,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear consultor',
        );
    }

    /**
     * Creates a form to create a Consultoria\Consultor entity.
     *
     * @param Consultor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Consultor $entity) {
        $form = $this->createForm(new ConsultorType(), $entity, array(
            'action' => $this->generateUrl('consultor_create'),
            'method' => 'POST',
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Consultoria\Consultor entity.
     *
     * @Route("/crear", name="consultor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $consultor = new Consultor();

        $consultorService = $this->get('adif.consultor_service');

        // Set Legajo
        $consultor->setLegajo($consultorService->getSiguienteLegajoConsultor());

        // Obtengo el TipoMoneda de curso legal
        $tipoMonedaMCL = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->
                findOneBy(array('esMCL' => true), array('id' => 'desc'), 1, 0);

        $consultor->setTipoMoneda($tipoMonedaMCL);

        $form = $this->createCreateForm($consultor);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $consultor,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear consultor'
        );
    }

    /**
     * Finds and displays a Consultoria\Consultor entity.
     *
     * @Route("/{id}", name="consultor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($id);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\Consultor.');
        }

        $contratos = $emContable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstadosAndIdConsultor(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO), $id);

        $bread = $this->base_breadcrumbs;
        $bread['Consultor'] = null;

        return array(
            'entity' => $consultor,
            'contratos' => $contratos,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver consultor'
        );
    }

    /**
     * Displays a form to edit an existing Consultoria\Consultor entity.
     *
     * @Route("/editar/{id}", name="consultor_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\Consultor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar consultor'
        );
    }

    /**
     * Creates a form to edit a Consultoria\Consultor entity.
     *
     * @param Consultor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Consultor $entity) {
        $form = $this->createForm(new ConsultorType(), $entity, array(
            'action' => $this->generateUrl('consultor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Consultoria\Consultor entity.
     *
     * @Route("/actualizar/{id}", name="consultor_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $emRRHH = $this->getDoctrine()->getManager($this->getEntityManager());

        $consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($id);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\Consultor.');
        }

        $adjuntosOriginales = new ArrayCollection();

        $caisOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los adjuntos actuales en la BBDD
        foreach ($consultor->getArchivos() as $adjunto) {
            $adjuntosOriginales->add($adjunto);
        }

        // Creo un ArrayCollection de los CAIs actuales en la BBDD
        foreach ($consultor->getCais() as $cai) {
            $caisOriginales->add($cai);
        }


        $editForm = $this->createEditForm($consultor);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

            $consultor->setFechaUltimaActualizacion(new \DateTime());

            // Si la Cuenta NO fue seteada
            if ($consultor->getCuenta() == null || $consultor->getCuenta()->getCbu() == null) {
                $consultor->setCuenta(null);
            }

            // Actualiza el Convenio Multilateral
            $this->updateConvenioMultilateral($emRRHH, $consultor);

            // Actualiza los Certificados de Exención
            $this->updateCertificadosExencion($emCompras, $consultor);

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($consultor);

            // Si la Cuenta NO fue seteada
            if ($consultor->getCuenta() == null || $consultor->getCuenta()->getCbu() == null) {
                $consultor->setCuenta(null);
            }

            // A cada CAI, le seteo el Consultor
            foreach ($consultor->getCais() as $cai) {
                $cai->setConsultor($consultor);
            }

            // Por cada adjunto original
            foreach ($adjuntosOriginales as $adjunto) {

                // Si fue eliminado
                if (false === $consultor->getArchivos()->contains($adjunto)) {

                    $consultor->removeArchivo($adjunto);

                    $emRRHH->remove($adjunto);
                }
            }

            // Por cada cai original
            foreach ($caisOriginales as $cai) {

                // Si fue eliminado
                if (false === $consultor->getCais()->contains($cai)) {

                    $consultor->removeCai($cai);

                    $emRRHH->remove($cai);
                }
            }

            // Abre transacción
            $emRRHH->getConnection()->beginTransaction();

            try {

                $emCompras->persist($consultor->getDatosImpositivos());

                // Persisto los certificados de exencion al Consultor
                $this->persistirCertificadosExencion($emCompras, $consultor);

                $emCompras->flush();

                // Asigno los certificados de exencion al Consultor
                $this->asignarCertificadosExencion($consultor);

                $emRRHH->flush();

                $emRRHH->getConnection()->commit();
            } // 
            catch (\Exception $e) {

                // Rollback
                $emRRHH->getConnection()->rollback();
                $emRRHH->close();

                throw $e;
            }

            return $this->redirect($this->generateUrl('consultor'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $consultor,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar consultor'
        );
    }

    /**
     * Deletes a Consultoria\Consultor entity.
     *
     * @Route("/borrar/{id}", name="consultor_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\Consultor.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('consultor'));
    }

    /**
     * @Route("/autocomplete/form", name="autocomplete_consultor")
     */
    public function getConsultoresAction(Request $request) {

        $term = $request->query->get('term', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $consultores = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')
                ->createQueryBuilder('c')
                ->where('upper(c.razonSocial) LIKE :term')
                ->orWhere('c.CUIT LIKE :term')
                ->andWhere('c.activo = 1')
                ->orderBy('c.razonSocial', 'DESC')
                ->setParameter('term', '%' . strtoupper($term) . '%')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($consultores as $consultor) {

            $jsonResult[] = array(
                'id' => $consultor->getId(),
                'razonSocial' => $consultor->getRazonSocial(),
                'CUIT' => $consultor->getCUIT(),
                'cais' => $consultor->getCaisPorPuntoVenta()
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * Cambia el estado activo del consultor.
     * @author gluis
     * 
     * @Route("/activar/{id}/{activo}", name="consultor_activar")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function activarAction($id, $activo = 1) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        /* @var $consultor Consultor */
        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($id);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultor.');
        }

        if ($activo == 0) {
            // Si quieren desactivar al consultor, valido primero si tiene facturas pendientes que se le tiene que pagar
            $ciclosFacturacion = $this->get('adif.contrato_service')->getCiclosFacturacionPendientesByIdConsultor($consultor->getId());
            if (!empty($ciclosFacturacion)) {
                $this->get('session')->getFlashBag()->add('error', 'No se puede desactivar al consultor: <b>' . $consultor->getRazonSocial() . '</b>, debido a que tiene ciclos de facturación pendientes por facturar.');
                return $this->redirect($this->generateUrl('consultor'));
            }
        }

        $consultor->setActivo($activo == 1);

        $em->flush();

        if ($activo == 1) {
            // Si activa, es porque estaba en la pantalla del historico
            return $this->redirect($this->generateUrl('consultor_historico'));
        }
        return $this->redirect($this->generateUrl('consultor'));
    }

    /**
     * 
     * @param type $em
     * @param Consultor $consultor
     */
    private function updateConvenioMultilateral($em, Consultor $consultor) {

        if (!$consultor->getDatosImpositivos()->getCondicionIngresosBrutos() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
            if (null != $consultor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos()) {
                $em->remove($consultor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos());
                $consultor->getDatosImpositivos()->setConvenioMultilateralIngresosBrutos(null);
            }
        } else {
            if (null != $consultor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos()) {
                $consultor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos()
                        ->setDatosImpositivos($consultor->getDatosImpositivos());
            }
        }
    }

    /**
     * 
     * @param type $emCompras
     * @param Consultor $consultor
     */
    private function updateCertificadosExencion($emCompras, Consultor $consultor) {

        if (null != $consultor->getCertificadoExencionIVA()) {

            if (null == $consultor->getCertificadoExencionIVA()->getNumeroCertificado()) {
                $emCompras->remove($consultor->getCertificadoExencionIVA());
                $consultor->setCertificadoExencionIVA(null);
            } else {
                $this->setAdjuntoACertificadoExencion($consultor->getCertificadoExencionIVA());
            }
        }

        if (null != $consultor->getCertificadoExencionGanancias()) {

            if (null == $consultor->getCertificadoExencionGanancias()->getNumeroCertificado()) {
                $emCompras->remove($consultor->getCertificadoExencionGanancias());
                $consultor->setCertificadoExencionGanancias(null);
            } else {
                $this->setAdjuntoACertificadoExencion($consultor->getCertificadoExencionGanancias());
            }
        }

        if (null != $consultor->getCertificadoExencionIngresosBrutos()) {

            if (null == $consultor->getCertificadoExencionIngresosBrutos()->getNumeroCertificado()) {
                $emCompras->remove($consultor->getCertificadoExencionIngresosBrutos());
                $consultor->setCertificadoExencionIngresosBrutos(null);
            } else {
                $this->setAdjuntoACertificadoExencion($consultor->getCertificadoExencionIngresosBrutos());
            }
        }

        if (null != $consultor->getCertificadoExencionSUSS()) {

            if (null == $consultor->getCertificadoExencionSUSS()->getNumeroCertificado()) {
                $emCompras->remove($consultor->getCertificadoExencionSUSS());
                $consultor->setCertificadoExencionSUSS(null);
            } else {
                $this->setAdjuntoACertificadoExencion($consultor->getCertificadoExencionSUSS());
            }
        }
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Controller\CertificadoExencion $certificadoExencion
     */
    private function setAdjuntoACertificadoExencion(CertificadoExencion $certificadoExencion) {

        if (null != $certificadoExencion->getAdjunto() && //
                null != $certificadoExencion->getAdjunto()->getArchivo()) {

            $certificadoExencion->getAdjunto()
                    ->setNombre($certificadoExencion->getAdjunto()->getArchivo()->getClientOriginalName());

            $certificadoExencion->getAdjunto()->setCertificadoExencion($certificadoExencion);
        }
    }

    /**
     * 
     * @param Consultor $consultor
     */
    private function updateFechasDatosImpositivos(Consultor $consultor) {

        $consultor->getDatosImpositivos()
                ->setFechaUltimaActualizacionCodigoSituacion(new \DateTime());
        $consultor->getDatosImpositivos()
                ->setFechaUltimaActualizacionRiesgoFiscal(new \DateTime());
        $consultor->getDatosImpositivos()
                ->setFechaUltimaActualizacionIncluyeMagnitudesSuperadas(new \DateTime());
        $consultor->getDatosImpositivos()
                ->setFechaUltimaActualizacionTieneProblemasAFIP(new \DateTime());
    }

    /**
     * 
     * @param Consultor $consultor
     */
    private function updateAdjuntos(Consultor $consultor) {

        foreach ($consultor->getArchivos() as $adjunto) {

            if ($adjunto->getArchivo() != null) {

                $adjunto->setConsultor($consultor);

                $adjunto->setNombre($adjunto->getArchivo()->getClientOriginalName());
            }
        }
    }

    /**
     * 
     * @param type $emCompras
     * @param Consultor $consultor
     */
    private function persistirCertificadosExencion($emCompras, Consultor $consultor) {

        if ($consultor->getCertificadoExencionIVA() != null) {

            $emCompras->persist($consultor->getCertificadoExencionIVA());
        }

        if ($consultor->getCertificadoExencionGanancias() != null) {
            $emCompras->persist($consultor->getCertificadoExencionGanancias());
        }

        if ($consultor->getCertificadoExencionIngresosBrutos() != null) {
            $emCompras->persist($consultor->getCertificadoExencionIngresosBrutos());
        }

        if ($consultor->getCertificadoExencionSUSS() != null) {
            $emCompras->persist($consultor->getCertificadoExencionSUSS());
        }
    }

    /**
     * 
     * @param Consultor $consultor
     */
    private function asignarCertificadosExencion(Consultor $consultor) {

        if ($consultor->getCertificadoExencionIVA() != null) {
            $consultor->setIdCertificadoExencionIVA($consultor->getCertificadoExencionIVA()->getId());
        }

        if ($consultor->getCertificadoExencionGanancias() != null) {
            $consultor->setIdCertificadoExencionGanancias($consultor->getCertificadoExencionGanancias()->getId());
        }

        if ($consultor->getCertificadoExencionIngresosBrutos() != null) {
            $consultor->setIdCertificadoExencionIngresosBrutos($consultor->getCertificadoExencionIngresosBrutos()->getId());
        }

        if ($consultor->getCertificadoExencionSUSS() != null) {
            $consultor->setIdCertificadoExencionSUSS($consultor->getCertificadoExencionSUSS()->getId());
        }
    }

    /**
     * Muestra la cuenta corriente del consultor.
     *
     * @Route("/{idConsultor}/cuentacorriente", name="consultor_cta_cte")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:cuenta_corriente.html.twig")
     */
    public function cuentaCorrienteIndexAction($idConsultor) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        /* @var $consultor Consultor */
        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($idConsultor);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = $this->generateUrl('consultor');
        $bread[$consultor->getRazonSocial()] = $this->generateUrl('consultor_show', ['id' => $consultor->getId()]);
        $bread['Cuenta corriente'] = null;

        $contratos = $em_contable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->findByIdConsultor($idConsultor);

        return array(
            'contratos' => $contratos,
            'consultor' => $consultor,
            'breadcrumbs' => $bread,
            'page_title' => 'Consultor | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Muestra la cuenta corriente del consultor.
     *
     * @Route("/{idConsultor}/cuentacorriente/{idContrato}", name="consultor_cta_cte_detalle")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:cuenta_corriente_detalle.html.twig")
     */
    public function cuentaCorrienteDetalleIndexAction($idConsultor, $idContrato) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em_contable = $this->getDoctrine()->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmContable());

        /* @var $consultor Consultor */
        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($idConsultor);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = $this->generateUrl('consultor');
        $bread[$consultor->getRazonSocial()] = $this->generateUrl('consultor_show', ['id' => $consultor->getId()]);
        $bread['Cuenta corriente'] = null;

        $contrato = $em_contable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->find($idContrato);
        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoConsultoria.');
        }

        $comprobantes = $em_contable->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->findByContrato($contrato);

        $comprobantes_result = [];

        // CANCELAN
        $ordenesPago = [
            'ids' => []
        ];

        foreach ($comprobantes as $comprobante) {
            $comprobantes_result[] = [
                'id' => $comprobante->getId(),
                'numero' => $comprobante->getNumeroCompleto(),
                'fecha' => $comprobante->getFechaComprobante(),
                'tipo' => $comprobante->getTipoComprobante(),
                'monto' => $comprobante->getTotal(),
                'deuda' => true,
                'es_op' => false
            ];

            if ($comprobante->getOrdenPago() != null) {
                $op = $comprobante->getOrdenPago();
                if (!in_array($op->getId(), $ordenesPago['ids'])) {
                    $ordenesPago['ids'][] = $op->getId();

                    // Se agrega a los comprobantes que restan deuda
                    $comprobantes_result[] = [
                        'id' => $op->getId(),
                        'numero' => $op->getNumeroOrdenPago(),
                        'fecha' => $op->getFechaOrdenPago(),
                        'tipo' => 'Orden de pago',
                        'monto' => $op->getTotalBruto(),
                        'deuda' => false,
                        'es_op' => true
                    ];
                }
            }
        }

        return array(
            'comprobantes_html' => $comprobantes_result,
            'consultor' => $consultor,
            'contrato' => $contrato,
            'breadcrumbs' => $bread,
            'page_title' => 'Consultor | Cuenta corriente',
            'page_info' => 'Cuenta corriente - Detalle'
        );
    }

    /**
     * Muestra la cuenta corriente del consultor.
     *
     * @Route("/{idConsultor}/cuentacorriente/{idContrato}", name="conwsultor_cta_cte_detalle")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:cuenta_corriente_detalle.html.twig")
     */
    public function cuentaCorrienteDetallewIndexAction($idConsultor, $idContrato) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        /* @var $consultor Consultor */
        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($idConsultor);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = $this->generateUrl('consultor');
        $bread[$consultor->getRazonSocial()] = $this->generateUrl('consultor_show', ['id' => $consultor->getId()]);
        $bread['Cuenta corriente'] = null;

        $contrato = $em_contable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->find($idContrato);
        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoConsultoria.');
        }

        return array(
            'contrato' => $contrato,
            'consultor' => $consultor,
            'breadcrumbs' => $bread,
            'page_title' => 'Consultor | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Muestra la cuenta corriente del consultor.
     *
     * @Route("/cuentacorrientedetalletotal/{idConsultor}/", name="consultor_cta_cte_detalle_total")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Consultoria\Consultor:cuenta_corriente_detalle_total.html.twig")
     */
    public function cuentaCorrienteDetalleTotalIndexAction($idConsultor) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        /* @var $consultor Consultor */
        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($idConsultor);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Consultores'] = $this->generateUrl('consultor');
        $bread[$consultor->getRazonSocial()] = $this->generateUrl('consultor_show', ['id' => $consultor->getId()]);
        $bread['Cuenta corriente'] = null;

        $contratos = array();
        $saldoTotal = 0;

        $repository_cc = $em_contable->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria');
        
        $tiposComprobantesSumanSaldo = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA
        ];

        $query = $em_contable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->createQueryBuilder('c')
                ->innerJoin('c.estadoContrato', 'ec')
                ->where('c.idConsultor = :id')
                ->andWhere('ec.codigo IN (:codigos)')
                ->setParameter('id', $idConsultor)
                ->setParameter('codigos', array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO, ConstanteEstadoContrato::ACTIVO_OK), \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->orderBy('c.numeroContrato, c.id', 'ASC');
        $contratosConsultor = $query->getQuery()->getResult();

        foreach ($contratosConsultor as $contrato) {
            /* @var $contrato \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria */
            if ($contrato->getContratoOrigen() == null) {
                $contratos[$contrato->getId()] = [
                    'id' => $contrato->getId(),
                    'saldo' => 0,
                    'nombre' => $contrato->__toString(),
                    'total' => $contrato->getImporteTotal(),
                    'restante' => $contrato->getSaldoPendienteFacturacion(),
                    'comprobantes' => array()
                ];
            }

            $comprobantes = $repository_cc->getComprobantesConsultoriaByContrato($contrato);

            $anticiposAplicados = array();
            $opAplicadas = array();

            /* @var $comprobante \ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria */
            foreach ($comprobantes as $comprobante) {
                if ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::CUPON) {
                    $comprobante_array = [
                        'id' => $comprobante->getId(),
                        'fecha' => $comprobante->getFechaComprobante(),
                        'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                        'numero' => $comprobante->getNumeroCompleto(),
                        'monto' => $comprobante->getTotal(),
                        'saldo' => ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) ? 0 : $comprobante->getTotal() * (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesSumanSaldo) ? 1 : -1),
                        'anulado' => $comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO
                    ];

                    if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {

                        $comprobante_array['restaSaldo'] = false;
                        /*$contratos[($contrato->getContratoOrigen() == null) //
                                        ? $contrato->getId() //
                                        : $contrato->getIdContratoInicial()]['saldo'] += $comprobante->getTotal() * (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesSumanSaldo) ? 1 : -1);*/
                    }

                    $contratos[($contrato->getContratoOrigen() == null) //
                                    ? $contrato->getId() //
                                    : $contrato->getIdContratoInicial()]['comprobantes'][] = $comprobante_array;

                    $indexComprobante = sizeof($contratos[($contrato->getContratoOrigen() == null) //
                                            ? $contrato->getId() //
                                            : $contrato->getIdContratoInicial()]['comprobantes']) - 1;

                    if ($comprobante->getOrdenPago() != null) {
                        $op = $comprobante->getOrdenPago();
                        if (!in_array($op->getId(), $opAplicadas)) {
                            if (($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) || ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA)) {
                                $monto = $op->getTotalBruto();
                                if ($op->getAnticipos() != null) {
                                    $anticipos = $op->getAnticipos();
                                    foreach ($anticipos as $anticipo) {
                                        if (!in_array($anticipo->getId(), $anticiposAplicados)) {
                                            $comprobante_array = [
                                                'id' => $anticipo->getId(),
                                                'fecha' => $anticipo->getFecha(),
                                                'tipoComprobante' => 'Anticipo',
                                                'numero' => '-',
                                                'monto' => $anticipo->getMonto(),
                                                'saldo' => 0,
                                                'anulado' => $op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                                                'restaSaldo' => true
                                            ];

                                            $anticiposAplicados[] = $anticipo->getId();
                                            $monto -= $anticipo->getMonto();

                                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {

                                                $contratos[($contrato->getContratoOrigen() == null) //
                                                                ? $contrato->getId() //
                                                                : $contrato->getIdContratoInicial()]['comprobantes'][$indexComprobante]['saldo'] -= $anticipo->getMonto();

                                                $contratos[($contrato->getContratoOrigen() == null) //
                                                                ? $contrato->getId() //
                                                                : $contrato->getIdContratoInicial()]['saldo'] -= $anticipo->getMonto();
                                            }

                                            $contratos[($contrato->getContratoOrigen() == null) //
                                                            ? $contrato->getId() //
                                                            : $contrato->getIdContratoInicial()]['comprobantes'][] = $comprobante_array;
                                        }
                                    }
                                }

                                if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                    $contratos[($contrato->getContratoOrigen() == null) //
                                                    ? $contrato->getId() //
                                                    : $contrato->getIdContratoInicial()]['comprobantes'][$indexComprobante]['saldo'] = 0;

                                    $contratos[($contrato->getContratoOrigen() == null) //
                                                    ? $contrato->getId() //
                                                    : $contrato->getIdContratoInicial()]['saldo'] = 0;
                                }

                                $comprobante_array = [
                                    'id' => $op->getId(),
                                    'fecha' => $op->getFechaOrdenPago(),
                                    'tipoComprobante' => 'Orden de pago',
                                    'numero' => $op->getNumeroOrdenPago(),
                                    'monto' => $monto,
                                    'saldo' => 0,
                                    'anulado' => $op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                                    'restaSaldo' => true
                                ];

                                $contratos[($contrato->getContratoOrigen() == null) //
                                                ? $contrato->getId() //
                                                : $contrato->getIdContratoInicial()]['comprobantes'][] = $comprobante_array;
                                if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                    $opAplicadas[] = $op->getId();
                                }
                            }
                        } else {
                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                $contratos[($contrato->getContratoOrigen() == null) //
                                                ? $contrato->getId() //
                                                : $contrato->getIdContratoInicial()]['comprobantes'][$indexComprobante]['saldo'] = 0;
                            }
                        }
                    }
                }
            }

            //anticipos no cancelados
            $anticiposNoAplicados = $em_contable->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')
                    ->createQueryBuilder('a')
                    ->where('a.contrato = :contrato')
                    ->andWhere('a.ordenPagoCancelada IS NULL')
                    ->setParameter('contrato', $contrato)
                    ->getQuery()
                    ->getResult();

            foreach ($anticiposNoAplicados as $anticipo) {
                $comprobante_array = [
                    'id' => $anticipo->getId(),
                    'fecha' => $anticipo->getFecha(),
                    'tipoComprobante' => 'Anticipo',
                    'numero' => '-',
                    'monto' => $anticipo->getMonto(),
                    'saldo' => ($anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA) ? 0 : $anticipo->getMonto(),
                    'anulado' => $anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                    'restaSaldo' => true
                ];

                $contratos[($contrato->getContratoOrigen() == null) //
                                ? $contrato->getId() //
                                : $contrato->getIdContratoInicial()]['saldo'] -= $comprobante_array['saldo'];

                $contratos[($contrato->getContratoOrigen() == null) //
                                ? $contrato->getId() //
                                : $contrato->getIdContratoInicial()]['comprobantes'][] = $comprobante_array;
            }
            
            foreach ($contratos[($contrato->getContratoOrigen() == null) //
                            ? $contrato->getId() //
                            : $contrato->getIdContratoInicial()]['comprobantes'] as $comprobantesC) {
                $contratos[($contrato->getContratoOrigen() == null) //
                            ? $contrato->getId() //
                            : $contrato->getIdContratoInicial()]['saldo'] += $comprobantesC['saldo'];
            }

            $saldoTotal += $contratos[($contrato->getContratoOrigen() == null) //
                            ? $contrato->getId() //
                            : $contrato->getIdContratoInicial()]['saldo'];
        }

        // actualizo saldos
        $contratosActivos = $em_contable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstadosAndIdConsultor(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO), $idConsultor);

        /* @var $contratosActivos \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria */
        foreach ($contratosActivos as $contrato) {
            if ($contrato->getContratoOrigen() != null) {
                $contratos[$contrato->getIdContratoInicial()]['total'] = $contrato->getImporteTotal();
                $contratos[$contrato->getIdContratoInicial()]['restante'] = $contrato->getSaldoPendienteFacturacion();
            }
        }

        return array(
            'contratos' => $contratos,
            'saldoTotal' => $saldoTotal,
            'consultor' => $consultor,
            'breadcrumbs' => $bread,
            'page_title' => 'Consultor | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Reporte resumen de cuenta corriente de consultor.
     *
     * @Route("/resumen_cuenta_corriente/", name="consultor_resumen_cuenta_corriente")
     * @Method("GET|POST")
     * 
     */
    public function reporteResumenCuentaCorrienteAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Consultor'] = $this->generateUrl('consultor');
        $bread['Resumen cuenta corriente consultor'] = null;

        return $this->render('ADIFRecursosHumanosBundle:Consultoria\Consultor:reporte.resumen_cuenta_corriente.html.twig', array(
                    //'consultores' => $consultores,
                    'breadcrumbs' => $bread,
                    'page_title' => 'Consultor | Resumen cuenta corriente',
                    'page_info' => 'Resumen cuenta corriente consultor'
        ));
    }

    /**
     * Tabla para reporte resumen de cuenta corriente .
     *
     * @Route("/index_table_reporte_resumen_cuenta_corriente/", name="index_table_consultor_reporte_resumen_cuenta_corriente")
     * @Method("GET|POST")
     */
    public function indexTableReporteResumenCuetaCorrienteAction(Request $request) {
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $fechaRequest = $request->query->get('fechaFin');
        $fecha = $fechaRequest == null ? (new \DateTime()) : \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');

        $consultores = [];

        $contratos = $emContable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstados(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO));


        /* @var $contrato \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria */

        foreach ($contratos as $contrato) {

            /* @var $consultor Consultor */

            $consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($contrato->getIdConsultor());

            if (!isset($consultores[$consultor->getId()])) {

                $consultores[$consultor->getId()] = array(
                    'id' => $contrato->getId(),
                    'legajo' => $consultor->getLegajo(),
                    'razonSocial' => $consultor->getRazonSocial(),
                    'cuit' => $consultor->getCUIT(),
                    'tipoContratacion' => $contrato->getClaseContrato()->__toString(),
                    'numeroContrato' => $contrato->getNumeroContrato(),
                    'cuentaContable' => $consultor->getCuentaContable(),
                    'saldoPendientePago' => $this->saldoContrato($emContable, $contrato, $fecha),
                    'muestraDetalle' => false
                );
            } else {

                $linkVerDetalle = '<a data-id-consultor=\"' . $consultor->getId() . '\"class=\"tooltips link-detalle-saldo\" data-original-title=\"Ver detalle\">Ver detalle</a>';

                $consultores[$consultor->getId()]['tipoContratacion'] = $linkVerDetalle;

                $consultores[$consultor->getId()]['numeroContrato'] = $linkVerDetalle;

                $consultores[$consultor->getId()]['saldoPendientePago'] += $this->saldoContrato($emContable, $contrato, $fecha);

                $consultores[$consultor->getId()]['muestraDetalle'] = true;
            }
        }

        return $this->render('ADIFRecursosHumanosBundle:Consultoria\Consultor:index_table_reporte.resumen_cuenta_corriente.html.twig', array(
                    'consultores' => $consultores,
        ));
    }

    /**
     * @Route("/detalle_resumen_cuenta_corriente/", name="consultor_detalle_resumen_cuenta_corriente")
     * @Method("GET|POST")
     */
    public function detalleResumenCuentaCorrienteConsultorAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $fechaRequest = $request->query->get('fechaFin');
        $fecha = $fechaRequest == null ? (new \DateTime()) : \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');

        $contratosJson = [];

        $contratos = $emContable->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstadosAndIdConsultor(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO), $request->request->get('id_consultor'));

        foreach ($contratos as $contrato) {

            $contratosJson[] = [
                'id' => $contrato->getId(),
                'claseContrato' => $contrato->getClaseContrato()->__toString(),
                'numeroContrato' => $contrato->getNumeroContrato(),
                'saldoPendientePago' => $this->saldoContrato($emContable, $contrato, $fecha)
            ];
        }

        return new JsonResponse($contratosJson);
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/letras_comprobante", name="consultor_letras_comprobante")
     * @Method("POST")
     */
    function getLetrasComprobante(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $idConsultor = $request->get('idConsultor');

        $consultor = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')
                ->find($idConsultor);

        if (!$consultor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultor.');
        }

        $letrasProveedor = $consultor->getLetrasComprobante();

        $letrasComprobante = $emContable->getRepository('ADIFContableBundle:LetraComprobante')
                ->getLetrasComprobanteByDenominacion($letrasProveedor);

        return new JsonResponse($letrasComprobante);
    }

    private function saldoContrato($emContable, $contrato, $fecha) {

        $saldo = 0;

        /* @var $comprobanteConsultoria \ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria */
        foreach ($contrato->getComprobantesConsultoria() as $comprobanteConsultoria) {

            if ($comprobanteConsultoria->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO &&
                    $comprobanteConsultoria->getFechaContable() <= $fecha) {
                if (($comprobanteConsultoria->getOrdenPago() != null) && (($comprobanteConsultoria->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_PAGADA) && ($comprobanteConsultoria->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA))) {
                    $saldo += (($comprobanteConsultoria->getEsNotaCredito() ? -1 : 1) * $comprobanteConsultoria->getTotal());
                }
            }
        }

        //anticipos no cancelados
        $anticiposNoAplicados = $emContable->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')
                ->createQueryBuilder('a')
                ->where('a.contrato = :contrato')
                ->andWhere('a.ordenPagoCancelada IS NULL')
                ->setParameter('contrato', $contrato)
                ->getQuery()
                ->getResult();

        foreach ($anticiposNoAplicados as $anticipo) {
            if ($anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA &&
                    $anticipo->getOrdenPago()->getFechaContable() <= $fecha) {
                $saldo -= $anticipo->getMonto();
            }
        }

        return $saldo;
    }

}
