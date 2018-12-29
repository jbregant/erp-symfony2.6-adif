<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoTramo;
use ADIF\ContableBundle\Entity\LicitacionObra;
use ADIF\ContableBundle\Entity\Obras\Tramo;
use ADIF\ContableBundle\Form\Obras\TramoType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Obras\Tramo controller.
 *
 * @Route("/obras/tramos")
 */
class TramoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Licitaciones de obra' => $this->generateUrl('licitacion_obra')
        );
    }

    /**
     * Lists all Obras\Tramo entities.
     *
     * @Route("/", name="obras_tramos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Obras\Tramo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tramos'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Renglones de licitaci&oacute;n',
            'page_info' => 'Lista de renglones de licitaci&oacute;n'
        );
    }

    /**
     * Tabla para Tramo.
     *
     * @Route("/index_table/", name="obras_tramos_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tramos = $em->getRepository('ADIFContableBundle:Obras\Tramo')
                ->createQueryBuilder('t')
                ->innerJoin('t.estadoTramo', 'e')
                ->innerJoin('t.licitacion', 'l')
                ->where('t.idProveedor = :idProveedor')
                ->andWhere('e.codigo != :codigoEstadoTramo')
                ->setParameters(array(
                    'idProveedor' => $request->query->get('id_proveedor'),
                    'codigoEstadoTramo' => ConstanteEstadoTramo::ESTADO_FINALIZADO
                ))
                ->orderBy('l.numero', 'ASC')
                ->getQuery()
                ->getResult();

        $tramosFiltrados = array_filter($tramos, function($tramo) {
            return $tramo->getSaldoFinanciero() > 0 || ($tramo->getSaldoFinanciero() == 0 && $tramo->getSaldoFondoReparo() > 0);
        });

        return $this->render('ADIFContableBundle:Obras\Tramo:index_table_por_proveedor.html.twig', array('tramos' => $tramosFiltrados));
    }

    /**
     * Creates a new Obras\Tramo entity.
     *
     * @Route("/licitacion/{licitacion}/insertar", name="obras_tramos_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\Tramo:new.html.twig")
     */
    public function createAction(Request $request, LicitacionObra $licitacion) {

        $tramo = new Tramo();

        $form = $this->createCreateForm($tramo, $licitacion);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $tramo->setLicitacion($licitacion);

            // A cada FuenteFinanciamientoTramo, le seteo el Tramo
            foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamiento) {
                $fuenteFinanciamiento->setTramo($tramo);
            }

            // A cada PolizaSeguroObra, le seteo el Tramo
            foreach ($tramo->getPolizasSeguro() as $polizaSeguro) {
                $polizaSeguro->setTramo($tramo);
            }

            // Persisto la entidad
            $em->persist($tramo);

            // Genero el definitivo asociado
            $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')
                    ->crearDefinitivoFromTramo($tramo);

            // Si hubo un error
            if ($mensajeError != '') {

                $this->get('session')->getFlashBag()->add('error', $mensajeError);

                $request->attributes->set('form-error', true);
            } else {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {

                    $em->flush();

                    $em->getConnection()->commit();
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('licitacion_obra'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear rengl&oacute;n de licitaci&oacute;n'] = null;

        return array(
            'entity' => $tramo,
            'licitacion' => $licitacion,
            'saldo_total_documentos_financieros' => $tramo->getSaldoTotalDocumentosFinancieros(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rengl&oacute;n de licitaci&oacute;n',
        );
    }

    /**
     * 
     * @param Tramo $entity
     * @param LicitacionObra $licitacion
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tramo $entity, LicitacionObra $licitacion) {

        $form = $this->createForm(new TramoType(), $entity, array(
            'action' => $this->generateUrl('obras_tramos_create', array('licitacion' => $licitacion->getId())),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\Tramo entity.
     *
     * @Route("/licitacion/{licitacion}/crear", name="obras_tramos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(LicitacionObra $licitacion) {

        $tramo = new Tramo();

        $tramo->setLicitacion($licitacion);

        $form = $this->createCreateForm($tramo, $licitacion);

        $bread = $this->base_breadcrumbs;
        $bread['Crear rengl&oacute;n de licitaci&oacute;n'] = null;

        $form->get('saldo')->setData($tramo->getSaldo());

        return array(
            'entity' => $tramo,
            'licitacion' => $licitacion,
            'saldo_total_documentos_financieros' => $tramo->getSaldoTotalDocumentosFinancieros(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rengl&oacute;n de licitaci&oacute;n'
        );
    }

    /**
     * Finds and displays a Obras\Tramo entity.
     *
     * @Route("/{id}", name="obras_tramos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\Tramo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tramo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Rengl&oacute;n de licitaci&oacute;n'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver rengl&oacute;n de licitaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing Obras\Tramo entity.
     *
     * @Route("/editar/{id}", name="obras_tramos_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\Tramo:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tramo = $em->getRepository('ADIFContableBundle:Obras\Tramo')->find($id);

        if (!$tramo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tramo.');
        }

        $licitacion = $tramo->getLicitacion();

        $proveedor = $tramo->getProveedor();

        $editForm = $this->createEditForm($tramo);

        $editForm->get('proveedor_razonSocial')->setData($proveedor->getRazonSocial());

        $editForm->get('proveedor_cuit')->setData($proveedor->getCUIT());

        $editForm->get('saldo')->setData($tramo->getSaldo());

        $tieneDocumentosFinancieros = !$tramo->getDocumentosFinancieros()->isEmpty();

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $tramo,
            'licitacion' => $licitacion,
            'saldo_total_documentos_financieros' => $tramo->getSaldoTotalDocumentosFinancieros(),
            'tiene_documentos_financieros' => $tieneDocumentosFinancieros,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rengl&oacute;n de licitaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a Obras\Tramo entity.
     *
     * @param Tramo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Tramo $entity) {
        $form = $this->createForm(new TramoType(), $entity, array(
            'action' => $this->generateUrl('obras_tramos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Obras\Tramo entity.
     *
     * @Route("/actualizar/{id}", name="obras_tramos_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\Tramo:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $tramo Tramo */
        $tramo = $em->getRepository('ADIFContableBundle:Obras\Tramo')->find($id);

        if (!$tramo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tramo.');
        }

        $licitacion = $tramo->getLicitacion();

        $fuentesFinanciamientoOriginales = new ArrayCollection();

        $polizasOriginales = new ArrayCollection();


        // Creo un ArrayCollection de las FuenteFinanciamientoTramo actuales en la BBDD
        foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamiento) {
            $fuentesFinanciamientoOriginales->add($fuenteFinanciamiento);
        }

        // Creo un ArrayCollection de las PolizaSeguroObra actuales en la BBDD
        foreach ($tramo->getPolizasSeguro() as $polizaSeguro) {
            $polizasOriginales->add($polizaSeguro);
        }

        $editForm = $this->createEditForm($tramo);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $numeroAsiento = 0;

            // Si el estado del tramo genera asiento de obra finalizada
            if ($tramo->getEstadoTramo()->getGeneraAsientoObraFinalizada()) {

                // Persisto los asientos contables y presupuestarios
                $numeroAsiento = $this->get('adif.asiento_service')
                        ->generarAsientoTramoFinalizado($tramo, $this->getUser());
            }

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // A cada FuenteFinanciamientoTramo, le seteo el Tramo
                foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamiento) {
                    $fuenteFinanciamiento->setTramo($tramo);
                }

                // Por cada FuenteFinanciamientoTramo original
                foreach ($fuentesFinanciamientoOriginales as $fuenteFinanciamiento) {

                    // Si fue eliminado
                    if (false === $tramo->getFuentesFinanciamiento()->contains($fuenteFinanciamiento)) {
                        $tramo->removeFuentesFinanciamiento($fuenteFinanciamiento);
                        $em->remove($fuenteFinanciamiento);
                    }
                }


                // A cada PolizaSeguroObra, le seteo el Tramo
                foreach ($tramo->getPolizasSeguro() as $polizaSeguro) {
                    $polizaSeguro->setTramo($tramo);
                }

                // Por cada PolizaSeguroObra original
                foreach ($polizasOriginales as $polizaSeguro) {

                    // Si fue eliminado
                    if (false === $tramo->getPolizasSeguro()->contains($polizaSeguro)) {
                        $tramo->removePolizasSeguro($polizaSeguro);
                        $em->remove($polizaSeguro);
                    }
                }

                // Actualizo el importe del definitivo
                $this->get('adif.contabilidad_presupuestaria_service')
                        ->actualizarDefinitivoFromTramo($tramo);

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em->getConnection()->commit();

                    if ($numeroAsiento != 0) {

                        $this->get('adif.asiento_service')
                                ->showMensajeFlashAsientoContable($numeroAsiento, array());
                    }
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('licitacion_obra'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $tieneDocumentosFinancieros = !$tramo->getDocumentosFinancieros()->isEmpty();

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $tramo,
            'licitacion' => $licitacion,
            'saldo_total_documentos_financieros' => $tramo->getSaldoTotalDocumentosFinancieros(),
            'tiene_documentos_financieros' => $tieneDocumentosFinancieros,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rengl&oacute;n de licitaci&oacute;n'
        );
    }

    /**
     * Deletes a Obras\Tramo entity.
     *
     * @Route("/borrar/{id}", name="obras_tramos_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tramo = $em->getRepository('ADIFContableBundle:Obras\Tramo')->find($id);

        if (!$tramo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tramo.');
        }

        if ($tramo->getEsEliminable()) {

            // Elimino el Definitivo asociado al Tramo
            $this->get('adif.contabilidad_presupuestaria_service')
                    ->eliminarDefinitivoFromTramo($tramo);

            $em->remove($tramo);

            $em->flush();
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        return $this->redirect($this->generateUrl('licitacion_obra'));
    }

    /**
     * 
     * @return string
     */
    public function getIndexPath() {

        return 'licitacion_obra';
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el rengl&oacute;n '
                . 'ya que es referenciado por otras entidades.';
    }

    /**
     * Reporte general de tramos.
     *
     * @Route("/reporte_general/", name="obras_tramos_reporte_general")
     * @Method("GET|POST")
     * 
     */
    public function reporteGeneralTramoAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte general de renglones de licitaci&oacute;n'] = null;

        return $this->render('ADIFContableBundle:Obras\Tramo:reporte_general.html.twig', array(
                    'breadcrumbs' => $bread,
                    'page_title' => 'Reporte general de renglones de licitaci&oacute;n'
        ));
    }

    /**
     * Reporte general de tramos.
     *
     * @Route("/reporte_general_index_table/", name="obras_tramos_reporte_general_index_table")
     * @Method("GET|POST")
     * 
     */
    public function reporteGeneralTramoIndexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tramos = null;

        $fechaFin = null;

        if ($request->query->get('fechaFin')) {

            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $conSaldo = false; // $request->request->get('con_saldo');

            $tramos = $em->getRepository('ADIFContableBundle:Obras\Tramo')
                    ->createQueryBuilder('t')
                    ->innerJoin('t.licitacion', 'l')
                    ->orderBy('l.numero', 'ASC')
                    ->getQuery()
                    ->getResult();

            if ($conSaldo) {
                $tramos = array_filter($tramos, function($tramo) {
                    return $tramo->getSaldo() > 0;
                });
            }
        }

        $cuentaContableObrasEjecucion = $em->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);

        $bread = $this->base_breadcrumbs;
        $bread['Reporte general de renglones de licitaci&oacute;n'] = null;

        return $this->render('ADIFContableBundle:Obras\Tramo:index_table_reporte_general.html.twig', array(
                    'entities' => $tramos,
                    'fechaFin' => $fechaFin,
                    'cuentaContableObrasEjecucion' => $cuentaContableObrasEjecucion
        ));
    }

}
