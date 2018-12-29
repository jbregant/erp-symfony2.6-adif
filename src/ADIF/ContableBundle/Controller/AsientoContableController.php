<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\AsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoOperacionContable;
use ADIF\ContableBundle\Entity\EjercicioContable;
use ADIF\ContableBundle\Entity\RenglonAsientoContable;
use ADIF\ContableBundle\Form\AsientoContableType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use mPDF;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\BaseBundle\Session\EmpresaSession;


/**
 * AsientoContable controller.
 *
 * @Route("/asientocontable")
 */
class AsientoContableController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Asientos contables' => $this->generateUrl('asientocontable')
        );
    }

    /**
     * Lists all AsientoContable entities.
     *
     * @Route("/", name="asientocontable")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $bread = $this->base_breadcrumbs;
        $bread['Asientos contables'] = null;

        $denominacionEjercicioContableSesion = $this->get('session')
                ->get('ejercicio_contable');

        /* @var $ejercicioContable EjercicioContable */
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByDenominacion($denominacionEjercicioContableSesion);

        $fechaMesCerradoSuperior = $this->get('adif.asiento_service')
                ->getFechaMesCerradoSuperiorByEjercicio($ejercicioContable);

        return array(
            'fechaMesCerradoSuperior' => $fechaMesCerradoSuperior,
            'breadcrumbs' => $bread,
            'page_title' => 'Asientos contables',
            'page_info' => 'Lista de asientos contables'
        );
    }

    /**
     * Creates a new AsientoContable entity.
     *
     * @Route("/insertar", name="asientocontable_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AsientoContable:new.html.twig")
     */
    public function createAction(Request $request) {
        $asientoContable = new AsientoContable();

        $form = $this->createCreateForm($asientoContable);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Al AsientoContable le seteo el Usuario
            $asientoContable->setUsuario($this->getUser());

            $this->setTipoAsientoContable($asientoContable, ConstanteTipoAsientoContable::TIPO_ASIENTO_MANUAL);

            $this->setEstadoAsientoContable($asientoContable, ConstanteEstadoAsientoContable::ESTADO_ASIENTO_GENERADO);

            $renglonesAsientoContable = $request->request->get('renglones_asiento_contable') // 
                    ? $request->request->get('renglones_asiento_contable') //
                    : array();

            $this->actualizarRenglonesAsientoContable($asientoContable, $renglonesAsientoContable);

            if ($request->request->get('copiar_modelo')) {
                // Genero el asiento modelo
                $modeloAsiento = new \ADIF\ContableBundle\Entity\ModeloAsientoContable();
                $modeloAsiento->setConceptoAsientoContable($asientoContable->getConceptoAsientoContable());
                $modeloAsiento->setDenominacionModeloAsientoContable('Modelo generado a partir del asiento: ' . $asientoContable->getDenominacionAsientoContable());
                $modeloAsiento->setTipoAsientoContable($asientoContable->getTipoAsientoContable());
                foreach ($asientoContable->getRenglonesAsientoContable() as $renglonAsientoContable) {
                    /* @var $renglonAsientoContable RenglonAsientoContable */
                    $renglonModelo = new \ADIF\ContableBundle\Entity\RenglonModeloAsientoContable();
                    $renglonModelo->copiarRenglonAsiento($renglonAsientoContable);
                    $modeloAsiento->addRenglonesModeloAsientoContable($renglonModelo);
                }

                $em->persist($modeloAsiento);
            }

            // Abre transacción
            $em->getConnection()->beginTransaction();

            try {
                // Obtengo el siguiente numero de asiento oficial
                $siguienteNumeroAsiento = $this->get('adif.asiento_service')->getSiguienteNumeroAsiento();

                // Seteo los numeros de asiento oficial y original
                $asientoContable->setNumeroOriginal($siguienteNumeroAsiento);
                $asientoContable->setNumeroAsiento($siguienteNumeroAsiento);

                $this->actualizarFechaContable($asientoContable);

                // Persisto la entidad
                $em->persist($asientoContable);

                // Persisto los asientos presupuestarios
                $mensajeErrorAsientoPresupuestario = $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromAsientoManual($asientoContable);

                // Si el asiento presupuestario falló
                if ($mensajeErrorAsientoPresupuestario != '') {
                    $this->get('session')->getFlashBag()->add('error', $mensajeErrorAsientoPresupuestario);
                } else {
                    // Actualizo los numeros de asientos posteriores
                    //$this->get('adif.asiento_service')->actualizarNumeroOficialAsiento($asientoContable);

                    $em->flush();
                }

                $em->getConnection()->commit();
            } catch (Exception $e) {
                // Rollback
                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }

            return $this->redirect($this->generateUrl('asientocontable'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $asientoContable,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear asiento contable',
        );
    }

    /**
     * Creates a form to create a AsientoContable entity.
     *
     * @param AsientoContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AsientoContable $entity) {
        $form = $this->createForm(new AsientoContableType(), $entity, array(
            'action' => $this->generateUrl('asientocontable_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AsientoContable entity.
     *
     * @Route("/crear", name="asientocontable_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $asientoContable = new AsientoContable();

        $form = $this->createCreateForm($asientoContable);

        $cuentasContablesImputables = $em->getRepository('ADIFContableBundle:CuentaContable')->findBy(
                array('esImputable' => true, 'activa' => true), array('codigoCuentaContable' => 'ASC')
        );

        $operacionesContables = $em->getRepository('ADIFContableBundle:TipoOperacionContable')->findAll();

        $tiposMonedaMCL = $em->getRepository('ADIFContableBundle:TipoMoneda')->findBy(
                array('esMCL' => true), //
                array('denominacionTipoMoneda' => 'ASC')
        );

        $denominacionEjercicioContableSesion = $this->get('session')
                ->get('ejercicio_contable');

        /* @var $ejercicioContable EjercicioContable */
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByDenominacion($denominacionEjercicioContableSesion);

        $fechaMesCerradoSuperior = $this->get('adif.asiento_service')
                ->getFechaMesCerradoSuperiorByEjercicio($ejercicioContable);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $asientoContable,
            'cuentasContables' => $cuentasContablesImputables,
            'operacionesContables' => $operacionesContables,
            'tiposMoneda' => $tiposMonedaMCL,
            'fechaMesCerradoSuperior' => $fechaMesCerradoSuperior,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear asiento contable'
        );
    }

    /**
     * Displays a form to edit an existing AsientoContable entity.
     *
     * @Route("/editar/{id}", name="asientocontable_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')->find($id);

        if (!$asientoContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        $editForm = $this->createEditForm($asientoContable);

        $cuentasContablesImputables = $em->getRepository('ADIFContableBundle:CuentaContable')->findBy(
                array('esImputable' => true), //
                array('codigoCuentaContable' => 'ASC')
        );

        $operacionesContables = $em->getRepository('ADIFContableBundle:TipoOperacionContable')->findAll();

        $tiposMonedaMCL = $em->getRepository('ADIFContableBundle:TipoMoneda')->findBy(
                array('esMCL' => true), //
                array('denominacionTipoMoneda' => 'ASC')
        );

        $denominacionEjercicioContableSesion = $this->get('session')
                ->get('ejercicio_contable');

        /* @var $ejercicioContable EjercicioContable */
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByDenominacion($denominacionEjercicioContableSesion);

        $fechaMesCerradoSuperior = $this->get('adif.asiento_service')
                ->getFechaMesCerradoSuperiorByEjercicio($ejercicioContable);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $asientoContable,
            'cuentasContables' => $cuentasContablesImputables,
            'operacionesContables' => $operacionesContables,
            'tiposMoneda' => $tiposMonedaMCL,
            'fechaMesCerradoSuperior' => $fechaMesCerradoSuperior,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar asiento contable'
        );
    }

    /**
     * Creates a form to edit a AsientoContable entity.
     *
     * @param Cheque $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AsientoContable $entity) {
        $form = $this->createForm(new AsientoContableType(), $entity, array(
            'action' => $this->generateUrl('asientocontable_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AsientoContable entity.
     *
     * @Route("/actualizar/{id}", name="asientocontable_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:AsientoContable:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
		
		$logger = new Logger('edicion_asientos');
		$monologFormat = "%message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);
		$streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/asientos/edicion_asientos_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
        $streamHandler->setFormatter($monologLineFormat);

		$logger->pushHandler($streamHandler);
		
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em->clear();

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->find($id);
				
		
        if (!$asientoContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }
		
		$tab = '          ';
		$logger->info('-------------------------------------------------------');
		$logger->info('EDICION DE ASIENTO ID: ' . $id);
		
		$user = $this->get('security.context')->getToken()->getUser();
		$logger->info('USUARIO: ' . $user->getUsername());
		$logger->info('USUARIO ID: ' . $user->getId());
		$dtAhora = new \DateTime();
		$logger->info('FECHA: ' . $dtAhora->format('d/m/Y H:i:s'));
		$logger->info('NRO ASIENTO: ' . $asientoContable->getNumeroAsiento());
		$logger->info('NRO ORIGINAL: ' . $asientoContable->getNumeroOriginal());
		$logger->info('-------------------------------------------------------');
		
		$logger->info('-------------------------------------------------------');
		$logger->info('ASIENTO ORIGINAL');
		
		foreach($asientoContable->getRenglonesAsientoContable() as $renglon) {
			$cuentaContable = $renglon->getCuentaContable();
			if ($renglon->getTipoOperacionContable()->getId() == 1) {
				// DEBE
				$logger->info('D: ' . $cuentaContable->getCodigoCuentaContable() . $tab .  $cuentaContable->getDenominacionCuentaContable() . $tab . $renglon->getImporteMCL() . $tab . $renglon->getDetalle() . ' (CPE: ' . $cuentaContable->getCuentaPresupuestariaEconomica() . ' )');
			}
			
			if ($renglon->getTipoOperacionContable()->getId() == 2) {
				// HABER
				$logger->info('H: '. $tab . $cuentaContable->getCodigoCuentaContable() . $tab .  $cuentaContable->getDenominacionCuentaContable() . $tab . $renglon->getImporteMCL() . $tab . $renglon->getDetalle() . ' (CPE: ' . $cuentaContable->getCuentaPresupuestariaEconomica() . ' )');
			}
		}
		
		$logger->info('-------------------------------------------------------');
		$logger->info('-------------------------------------------------------');
		

        $editForm = $this->createEditForm($asientoContable);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $renglonesAsientoContable = $request->request->get('renglones_asiento_contable') // 
                    ? $request->request->get('renglones_asiento_contable') //
                    : array();

            $this->actualizarRenglonesAsientoContable($asientoContable, $renglonesAsientoContable);

            // Actualizo los numeros de asientos posteriores
            //$this->get('adif.asiento_service')->actualizarNumeroOficialAsiento($asientoContable);

            $em->flush();
			
			$logger->info('-------------------------------------------------------');
			$logger->info('ASIENTO MODIFICADO');
			$logger->info('NRO ASIENTO: ' . $asientoContable->getNumeroAsiento());
			$logger->info('NRO ORIGINAL: ' . $asientoContable->getNumeroOriginal());
			
			foreach($asientoContable->getRenglonesAsientoContable() as $renglon) {
				$cuentaContable = $renglon->getCuentaContable();
				if ($renglon->getTipoOperacionContable()->getId() == 1) {
					// DEBE
					$logger->info('D: ' . $cuentaContable->getCodigoCuentaContable() . $tab .  $cuentaContable->getDenominacionCuentaContable() . $tab . $renglon->getImporteMCL() . $tab . $renglon->getDetalle() . ' (CPE: ' . $cuentaContable->getCuentaPresupuestariaEconomica() . ' )');
				}
				
				if ($renglon->getTipoOperacionContable()->getId() == 2) {
					// HABER
					$logger->info('H: '. $tab . $cuentaContable->getCodigoCuentaContable() . $tab .  $cuentaContable->getDenominacionCuentaContable() . $tab . $renglon->getImporteMCL() . $tab . $renglon->getDetalle() . ' (CPE: ' . $cuentaContable->getCuentaPresupuestariaEconomica() . ' )');
				}
			}
			
			$logger->info('-------------------------------------------------------');
			$logger->info('-------------------------------------------------------');

            return $this->redirect($this->generateUrl('asientocontable'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $asientoContable,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar asiento contable'
        );
    }

    /**
     * Finds and displays a AsientoContable entity.
     *
     * @Route("/{id}", name="asientocontable_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AsientoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }
        /*
          // Persisto los asientos presupuestarios
          $mensajeErrorAsientoPresupuestario = $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromAsientoManual($entity);

          // Si el asiento presupuestario falló
          if ($mensajeErrorAsientoPresupuestario != '') {
          echo "ERROR";
          var_dump($mensajeErrorAsientoPresupuestario);
          } else {
          // Actualizo los numeros de asientos posteriores
          $this->get('adif.asiento_service')->actualizarNumeroOficialAsiento($entity);

          $em->flush();
          echo "TERMINO";
          }
          die;
         */
        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver asiento contable'
        );
    }

    /**
     * Deletes a AsientoContable entity.
     *
     * @Route("/borrar/{id}", name="asientocontable_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:AsientoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('asientocontable'));
    }

    /**
     * @Route("/lista_operacion_contable", name="asientocontable_lista_operacion_contable")
     */
    public function listaTipoOperacionContableAction() {
        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:TipoOperacionContable', $this->getEntityManager());

        $query = $repository->createQueryBuilder('toc')
                ->select('toc.id', 'toc.denominacion')
                ->orderBy('toc.denominacion', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @param type $renglonesAsientoContable
     */
    private function actualizarRenglonesAsientoContable(AsientoContable $asientoContable, $renglonesAsientoContable) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonesOriginales = $asientoContable->getRenglonesAsientoContable();

        // Recorro los RenglonAsientoContable originales, eliminando aquellos 
        // que no vinieron en el Request
        foreach ($renglonesOriginales as $renglonAsientoContableOriginal) {

            $existe = false;

            foreach ($renglonesAsientoContable as $renglonAsientoContable) {

                if ($renglonAsientoContableOriginal->getId() == $renglonAsientoContable['id']) {
                    $existe = true;
                }
            }

            if (!$existe) {
                $em->remove($renglonAsientoContableOriginal);
            }
        }

        foreach ($renglonesAsientoContable as $renglonAsientoContable) {

            // Si no existe lo agrego, sino ya está en la colección
            if ($renglonAsientoContable['id'] == "") {

                $renglonAsientoContableNuevo = new RenglonAsientoContable();

                $asientoContable->addRenglonesAsientoContable($renglonAsientoContableNuevo);
            } else {

                $renglonAsientoContableNuevo = $em->getRepository('ADIFContableBundle:RenglonAsientoContable')
                        ->find($renglonAsientoContable['id']);
            }

            $renglonAsientoContableNuevo->setCuentaContable(
                    $em->getRepository('ADIFContableBundle:CuentaContable')
                            ->find($renglonAsientoContable['idCuentaContable'])
            );

            $renglonAsientoContableNuevo->setTipoOperacionContable(
                    $em->getRepository('ADIFContableBundle:TipoOperacionContable')
                            ->find($renglonAsientoContable['idOperacionContable'])
            );

            $renglonAsientoContableNuevo->setTipoMoneda(
                    $em->getRepository('ADIFContableBundle:TipoMoneda')
                            ->find($renglonAsientoContable['idTipoMoneda'])
            );

            $importeMO = str_replace(',', '.', $renglonAsientoContable['importeMO']);

            $renglonAsientoContableNuevo->setImporteMO($importeMO);
            $renglonAsientoContableNuevo->setImporteMCL($importeMO);

            $renglonAsientoContableNuevo->setDetalle($renglonAsientoContable['detalle']);

            $em->persist($renglonAsientoContableNuevo);
        }
    }

    /**
     * Muestra la pantalla de libro diario.
     *
     * @Route("/librodiario/", name="asientocontable_librodiario")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte.libro_diario.html.twig")
     */
    public function libroDiarioAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Libro diario'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Libro diario'
        );
    }

    /**
     * @Route("/filtrar_librodiario/", name="asientocontable_filtrar_librodiario")
     */
    public function filtrarLibroDiarioAction(Request $request) {

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $tipoReporte = $request->get('tipoReporte');

        $repository = $this->getDoctrine()->getRepository('ADIFContableBundle:RenglonAsientoContable', $this->getEntityManager());

        $qb = $repository->createQueryBuilder('r');

        $renglonesAsientoContable = $qb->innerJoin('r.asientoContable', 'a')
                ->select(
                        'partial r.{id, cuentaContable, tipoOperacionContable, importeMCL, detalle}', //
                        'partial a.{id, fechaContable, denominacionAsientoContable, numeroOriginal, numeroAsiento, fechaCreacion, tipoAsientoContable}')
                ->where($qb->expr()->between('a.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                //->addOrderBy('a.fechaContable', 'ASC')
                ->orderBy('a.id', 'ASC')
                ->addOrderBy('r.tipoOperacionContable', 'ASC')
                //>addOrderBy('a.numeroAsiento', 'ASC')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($renglonesAsientoContable as $renglonAsientoContable) {
            $jsonResult[] = array(
                'id' => $renglonAsientoContable->getId(),
                'idAsiento' => $renglonAsientoContable->getAsientoContable()->getId(),
				'idAsientoConFormato' => $renglonAsientoContable->getAsientoContable()->getIdConFormato(),
                'fechaCreacion' => $renglonAsientoContable->getAsientoContable()->getFechaCreacion()->format('d/m/Y'),
                'fechaContable' => $renglonAsientoContable->getAsientoContable()->getFechaContable()->format('d/m/Y'),
                'numeroAsiento' => $renglonAsientoContable->getAsientoContable()->getNumeroAsiento(),
                'numeroOriginal' => $renglonAsientoContable->getAsientoContable()->getNumeroOriginal(),
                'tipoAsientoContable' => $renglonAsientoContable->getAsientoContable()->getTipoAsientoContable()->__toString(),
                'denominacionAsiento' => $renglonAsientoContable->getAsientoContable()->__toString(),
                'cuentaContable' => $renglonAsientoContable->getCuentaContable()->__toString(),
                'tipoImputacion' => $renglonAsientoContable->getTipoOperacionContable()->__toString(),
                'importeMCL' => $renglonAsientoContable->getImporteMCL(),
                'detalle' => $renglonAsientoContable->getDetalle(),
                'tipoReporte' => $tipoReporte
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * Muestra la pantalla de resumen libro diario.
     *
     * @Route("/resumenlibrodiario/", name="asientocontable_resumen_librodiario")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte.resumen_libro_diario.html.twig")
     */
    public function resumenLibroDiarioAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Libro diario resumido'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $subdiarios = $em->getRepository('ADIFContableBundle:SubdiarioAsientoContable')->findAll();

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Libro diario resumido',
            'subdiarios' => $subdiarios
        );
    }

    /**
     * @Route("/filtrar_resumenlibrodiario/", name="asientocontable_filtrar_resumenlibrodiario")
     */
    public function filtrarResumenLibroDiarioAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $subdiarios = ($request->get('subdiarios') > 0 ) ? $request->get('subdiarios') : array(-1);

        $connection = $em->getConnection();

        $statement = $connection->prepare('
            SELECT
                    acumulado.subdiario AS subdiarioAsientoContable,
                    acumulado.cuenta AS denominacionCuentaContable,
                    acumulado.codigoCuentaContable AS codigoCuentaContable,
                    acumulado.tipoOperacionContable AS tipoOperacionContable,
                    sum(acumulado.debe) AS debe,
                    sum(acumulado.haber) AS haber
            FROM
                (
                    SELECT
                            sac.denominacion AS subdiario,
                            cc.denominacion AS cuenta,
                            cc.id AS idCuentaContable,
                            cc.codigo AS codigoCuentaContable,
                            rac.id_tipo_operacion_contable AS tipoOperacionContable,
                            CASE toc.denominacion
                    WHEN :idDebe THEN
                            sum(rac.importe_mcl)
                    ELSE
                            0
                    END AS debe,
                    CASE toc.denominacion
                    WHEN :idHaber THEN
                            sum(rac.importe_mcl)
                    ELSE
                            0
                    END AS haber
                    FROM asiento_contable ac
                    LEFT JOIN concepto_asiento_contable cac ON cac.id = ac.id_concepto_asiento_contable
                    LEFT JOIN subdiario_asiento_contable sac ON cac.id_subdiario_asiento_contable = sac.id
                    LEFT JOIN renglon_asiento_contable rac ON rac.id_asiento_contable = ac.id
                    LEFT JOIN tipo_operacion_contable toc ON rac.id_tipo_operacion_contable = toc.id
                    LEFT JOIN cuenta_contable cc ON rac.id_cuenta_contable = cc.id
                    WHERE
                        (
                                ac.fecha_contable BETWEEN :fechaInicio
                                AND :fechaFin
                        )
                        AND (rac.fecha_baja IS NULL)
                        AND sac.id in (' . implode(",", $subdiarios) . ')
                    GROUP BY
                            sac.id,
                            toc.id,
                            rac.id_cuenta_contable
                    ) AS acumulado
            GROUP BY
                    subdiario,
                    idCuentaContable
            HAVING (debe <> haber)
            ORDER BY
                    subdiarioAsientoContable,
                    tipoOperacionContable,
                    debe,
                    haber
        ');

        $statement->bindValue('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME);
        $statement->bindValue('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME);
        $statement->bindValue('idDebe', ConstanteTipoOperacionContable::DEBE);
        $statement->bindValue('idHaber', ConstanteTipoOperacionContable::HABER);

        $statement->execute();

        $cuentasContables = $statement->fetchAll();

        $jsonResult = [];

        foreach ($cuentasContables as $cuentaContable) {

            $jsonResult[] = array(
                'subdiarioAsientoContable' => $cuentaContable['subdiarioAsientoContable'],
                'cuentaContable' => $cuentaContable['codigoCuentaContable'] . ' - ' . $cuentaContable['denominacionCuentaContable'],
                'debe' => $cuentaContable['debe'],
                'haber' => $cuentaContable['haber'],
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * Muestra la pantalla de libro subdiario.
     *
     * @Route("/librosubdiario/", name="asientocontable_librosubdiario")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte.libro_subdiario.html.twig")
     */
    public function libroSubdiarioAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $subdiarios = $em->getRepository('ADIFContableBundle:SubdiarioAsientoContable')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Libro subdiario'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Libro subdiario',
            'subdiarios' => $subdiarios
        );
    }

    /**
     * @Route("/filtrar_librosubdiario/", name="asientocontable_filtrar_librosubdiario")
     */
    public function filtrarLibroSubdiarioAction(Request $request) {
        $subdiarioAsientoContable = $request->get('subdiario');

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:RenglonAsientoContable', $this->getEntityManager());

        $qb = $repository->createQueryBuilder('r');

        $renglonesAsientoContable = $qb
                ->innerJoin('r.asientoContable', 'a')
                ->innerJoin('a.conceptoAsientoContable', 'ca')
                ->select(
                        'partial r.{id, cuentaContable, tipoOperacionContable, importeMCL, detalle}', //
                        'partial a.{id, fechaContable, conceptoAsientoContable, numeroAsiento, denominacionAsientoContable}', //
                        'partial ca.{id, subdiarioAsientoContable}')
                ->where('ca.subdiarioAsientoContable = :subdiarioAsientoContable')
                ->andWhere($qb->expr()->between('a.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('subdiarioAsientoContable', $subdiarioAsientoContable)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->addOrderBy('a.fechaContable', 'ASC')
                ->addOrderBy('a.id', 'ASC')
                ->addOrderBy('r.tipoOperacionContable', 'ASC')
                ->addOrderBy('a.numeroAsiento', 'ASC')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($renglonesAsientoContable as $renglonAsientoContable) {

            $jsonResult[] = array(
                'id' => $renglonAsientoContable->getId(),
                'idAsiento' => $renglonAsientoContable->getAsientoContable()->getId(),
                'fechaContable' => $renglonAsientoContable->getAsientoContable()->getFechaContable()->format('d/m/Y'),
                'numeroAsiento' => $renglonAsientoContable->getAsientoContable()->getNumeroAsiento(),
                'denominacionAsiento' => $renglonAsientoContable->getAsientoContable()->__toString(),
                'cuentaContable' => $renglonAsientoContable->getCuentaContable()->__toString(),
                'tipoImputacion' => $renglonAsientoContable->getTipoOperacionContable()->__toString(),
                'importeMCL' => $renglonAsientoContable->getImporteMCL(),
                'detalle' => ($renglonAsientoContable->getDetalle() != null) ? $renglonAsientoContable->getDetalle() : ''
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * Muestra la pantalla el libro mayor.
     *
     * @Route("/libromayor/", name="asientocontable_libromayor")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte.libro_mayor.html.twig")
     */
    public function libroMayorAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Libro mayor'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Libro mayor'
        );
    }

    /**
     * @Route("/filtrar_libromayor/", name="asientocontable_filtrar_libromayor")
     */
    public function filtrarLibroMayorAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentaContable = $request->get('cuentaContable');

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $repository = $this->getDoctrine()->getRepository('ADIFContableBundle:RenglonAsientoContable', $this->getEntityManager());

        $qb = $repository->createQueryBuilder('r');

        $renglonesAsientoContable = $qb->innerJoin('r.asientoContable', 'a')
                ->select(
                        'partial r.{id, cuentaContable, tipoOperacionContable, importeMCL, detalle}', //
                        'partial a.{id, fechaContable, numeroAsiento, numeroOriginal, denominacionAsientoContable, razonSocial, numeroDocumento}')
                ->where('r.cuentaContable = :cuentaContable')
                ->andWhere($qb->expr()->between('a.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('cuentaContable', $cuentaContable)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->addOrderBy('a.fechaContable', 'ASC')
                ->addOrderBy('a.id', 'ASC')
                ->addOrderBy('r.tipoOperacionContable', 'ASC')
                ->addOrderBy('a.numeroAsiento', 'ASC')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        //$fechaInicio->sub(new \DateInterval('PT1S'));

        $fechaInicioSaldo = \DateTime::createFromFormat('d/m/Y H:i:s', '01/01/' . $fechaInicio->format('Y') . ' 00:00:00');

        $saldoActual = $em->getRepository('ADIFContableBundle:CuentaContable')
                ->getSaldoALaFecha($cuentaContable, $fechaInicio, $fechaInicioSaldo);

        $jsonResult[] = array(
            'id' => '-1',
            'idAsiento' => '-1',
            'fechaContable' => ' -- ',
            'numeroAsientoOriginal' => ' -- ',
            'numeroAsiento' => ' -- ',
            'conceptoAsientoContable' => 'Saldo inicial',
            'cuentaContable' => '',
            'tipoImputacion' => '',
            'importeMCL' => '',
            'saldo' => $saldoActual,
            'titulo' => ' -- ',
            'razonSocial' => ' -- ',
            'numeroDocumento' => ' -- ',
            'detalle' => ' -- '
        );

        foreach ($renglonesAsientoContable as $renglonAsientoContable) {

            $saldoActual = $this->getSaldoActual($renglonAsientoContable, $saldoActual);

            $razonSocial = $renglonAsientoContable->getAsientoContable()->getRazonSocial() != null //
                    ? $renglonAsientoContable->getAsientoContable()->getRazonSocial() //
                    : '-';

            $numeroDocumento = $renglonAsientoContable->getAsientoContable()->getNumeroDocumento() != null //
                    ? $renglonAsientoContable->getAsientoContable()->getNumeroDocumento() //
                    : '-';

            $jsonResult[] = array(
                'id' => $renglonAsientoContable->getId(),
                'idAsiento' => $renglonAsientoContable->getAsientoContable()->getId(),
                'fechaContable' => $renglonAsientoContable->getAsientoContable()->getFechaContable()->format('d/m/Y'),
                'numeroAsientoOriginal' => $renglonAsientoContable->getAsientoContable()->getNumeroOriginal(),
                'numeroAsiento' => $renglonAsientoContable->getAsientoContable()->getNumeroAsiento(),
                'conceptoAsientoContable' => $renglonAsientoContable->getAsientoContable()->getConceptoAsientoContable()->__toString(),
                'cuentaContable' => $renglonAsientoContable->getCuentaContable()->__toString(),
                'tipoImputacion' => $renglonAsientoContable->getTipoOperacionContable()->__toString(),
                'importeMCL' => $renglonAsientoContable->getImporteMCL(),
                'saldo' => $saldoActual,
                'titulo' => $renglonAsientoContable->getAsientoContable()->getDenominacionAsientoContable(),
                'razonSocial' => $razonSocial,
                'numeroDocumento' => $numeroDocumento,
                'detalle' => $renglonAsientoContable->getDetalle()
            );
        }

        return new JsonResponse($jsonResult);
    }
	
	/**
     * Reporte en base al libro mayor, para mostrar todas las cuentas 
     *
     * @Route("/libromayor/mostrar_todo/{fechaInicio}/{fechaFin}", name="asientocontable_libromayor_mostrar_todo")
     * @Method("GET")
     */
	public function libroMayorMostrarTodasLasCuentasAction($fechaInicio, $fechaFin)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

        //$cuentaContable = $request->get('cuentaContable');
		
		//var_dump($fechaInicio, $fechaFin);

        $fechaInicio = \DateTime::createFromFormat('d-m-Y H:i:s', $fechaInicio . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d-m-Y H:i:s', $fechaFin . ' 23:59:59');
		
		//var_dump($fechaInicio, $fechaFin);exit;

        $repository = $this->getDoctrine()->getRepository('ADIFContableBundle:RenglonAsientoContable', $this->getEntityManager());

        $qb = $repository->createQueryBuilder('r');

        $renglonesAsientoContable = $qb->innerJoin('r.asientoContable', 'a')
                ->select(
                        'partial r.{id, cuentaContable, tipoOperacionContable, importeMCL, detalle}', //
                        'partial a.{id, fechaContable, numeroAsiento, numeroOriginal, denominacionAsientoContable, razonSocial, numeroDocumento}')
                //->where('r.cuentaContable = :cuentaContable')
                ->where($qb->expr()->between('a.fechaContable', ':fechaInicio', ':fechaFin'))
                //->setParameter('cuentaContable', $cuentaContable)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                //->addOrderBy('a.fechaContable', 'ASC')
				->addOrderBy('r.cuentaContable', 'ASC')
                ->addOrderBy('a.id', 'ASC')
                ->addOrderBy('r.tipoOperacionContable', 'ASC')
                //->addOrderBy('a.numeroAsiento', 'ASC')
                ->getQuery()
                ->getResult();

//        $jsonResult = [];

        //$fechaInicio->sub(new \DateInterval('PT1S'));

        $fechaInicioSaldo = \DateTime::createFromFormat('d/m/Y H:i:s', '01/01/' . $fechaInicio->format('Y') . ' 00:00:00');

/*
        $jsonResult[] = array(
            'id' => '-1',
            'idAsiento' => '-1',
            'fechaContable' => ' -- ',
            'numeroAsientoOriginal' => ' -- ',
            'numeroAsiento' => ' -- ',
            'conceptoAsientoContable' => 'Saldo inicial',
            'cuentaContable' => '',
            'tipoImputacion' => '',
            'importeMCL' => '',
            'saldo' => $saldoActual,
            'titulo' => ' -- ',
            'razonSocial' => ' -- ',
            'numeroDocumento' => ' -- ',
            'detalle' => ' -- '
        );
*/
		$html = '<table border="1">';
		$html .= '<tr>';
		$html .= '<th>Id</th>';
		$html .= '<th>Fecha</th>';
		$html .= '<th>Nro original</th>';
		$html .= '<th>Nro asiento</th>';
		$html .= '<th>Concepto</th>';
		$html .= '<th>Cuenta contable/th>';
		$html .= '<th>Debe</th>';
		$html .= '<th>Haber</th>';
		$html .= '<th>Saldo</th>';
		$html .= '<th>Título</th>';
		$html .= '<th>Razon Social</th>';
		$html .= '<th>Nro doc</th>';
		$html .= '<th>Detalle</th>';
		$html .= '</tr>';
        foreach ($renglonesAsientoContable as $renglonAsientoContable) {
			
			$html .= '<tr>';
			
			$saldoActual = $em->getRepository('ADIFContableBundle:CuentaContable')
                ->getSaldoALaFecha($renglonAsientoContable->getCuentaContable(), $fechaInicio, $fechaInicioSaldo);
			
            $saldoActual = $this->getSaldoActual($renglonAsientoContable, $saldoActual);

            $razonSocial = $renglonAsientoContable->getAsientoContable()->getRazonSocial() != null //
                    ? $renglonAsientoContable->getAsientoContable()->getRazonSocial() //
                    : '-';

            $numeroDocumento = $renglonAsientoContable->getAsientoContable()->getNumeroDocumento() != null //
                    ? $renglonAsientoContable->getAsientoContable()->getNumeroDocumento() //
                    : '-';

			$html .= '<td>' . $renglonAsientoContable->getAsientoContable()->getFechaContable()->format('d/m/Y') . '</td>';
			$html .= '<td>' . $renglonAsientoContable->getAsientoContable()->getNumeroOriginal() . '</td>';
			$html .= '<td>' . $renglonAsientoContable->getAsientoContable()->getNumeroAsiento() . '</td>';
			$html .= '<td>' . $renglonAsientoContable->getAsientoContable()->getConceptoAsientoContable()->__toString() . '</td>';
			$html .= '<td>' . $renglonAsientoContable->getCuentaContable()->__toString() . '</td>';
			if ($renglonAsientoContable->getTipoOperacionContable()->getId() == 1) {
				// Debe
				$html .= '<td>' . $renglonAsientoContable->getImporteMCL() . '</td>';
				$html .= '<td></td>';
			} else {
				// Haber
				$html .= '<td></td>';
				$html .= '<td>' . $renglonAsientoContable->getImporteMCL() . '</td>';
			}
			
			$html .= '<td>' . $saldoActual . '</td>';
			$html .= '<td>' . $renglonAsientoContable->getAsientoContable()->getDenominacionAsientoContable() . '</td>';
			$html .= '<td>' . $razonSocial . '</td>';
			$html .= '<td>' . $numeroDocumento . '</td>';
					
/*
            $jsonResult[] = array(
                'id' => $renglonAsientoContable->getId(),
                'idAsiento' => $renglonAsientoContable->getAsientoContable()->getId(),
                'fechaContable' => $renglonAsientoContable->getAsientoContable()->getFechaContable()->format('d/m/Y'),
                'numeroAsientoOriginal' => $renglonAsientoContable->getAsientoContable()->getNumeroOriginal(),
                'numeroAsiento' => $renglonAsientoContable->getAsientoContable()->getNumeroAsiento(),
                'conceptoAsientoContable' => $renglonAsientoContable->getAsientoContable()->getConceptoAsientoContable()->__toString(),
                'cuentaContable' => $renglonAsientoContable->getCuentaContable()->__toString(),
                'tipoImputacion' => $renglonAsientoContable->getTipoOperacionContable()->__toString(),
                'importeMCL' => $renglonAsientoContable->getImporteMCL(),
                'saldo' => $saldoActual,
                'titulo' => $renglonAsientoContable->getAsientoContable()->getDenominacionAsientoContable(),
                'razonSocial' => $razonSocial,
                'numeroDocumento' => $numeroDocumento,
                'detalle' => $renglonAsientoContable->getDetalle()
            );
*/
			
			
			$html .= '</tr>';
        }

		$html .= '</table>';
		die($html);
		
        return new Response($html);
	}

    /**
     * Muestra la pantalla el balance de sumas y saldos.
     *
     * @Route("/balancesumassaldos/", name="asientocontable_balancesumassaldos")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte.balance_sumas_saldos.html.twig")
     */
    public function balanceSumasYSaldosAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Balance de sumas y saldos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Balance de sumas y saldos'
        );
    }

    /**
     * @Route("/filtrar_balancesumassaldos/", name="asientocontable_filtrar_balancesumassaldos")
     */
    public function filtrarBalanceSumasYSaldosAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha($fechaInicio);

        /* @var $ejercicioContable EjercicioContable */
        $fechaInicioEjercicio = $ejercicioContable->getFechaInicio();

        $soloCuentasConSaldo = $request->get('soloCuentasConSaldo');
        $conAsientosFormales = $request->get('incluirAsientosFormales');

        $connection = $em->getConnection();
		
		// Voy a iterar por todas las cuentas contables vigentes
		$todasCuentasContables = $em->getRepository('ADIFContableBundle:CuentaContable')->findBy(array(
			'activa' => 1,
			'esImputable' => 1
		));

        $queryInicio = '
            SELECT
                acumulado.id_cuenta_contable as id,
                acumulado.denominacion_cuenta_contable as denominacionCuentaContable,
                acumulado.codigo_cuenta_contable as codigoCuentaContable,
                CONCAT(SUBSTRING(acumulado.codigo_cuenta_contable, 1, 4), "00", SUBSTRING(acumulado.codigo_cuenta_contable, 7)) AS codigoCuentaContableOrden,
                CONCAT(SUBSTRING(acumulado.codigo_cuenta_contable, 5, 2)) AS centroCosto,
                SUM(acumulado.debe) as totalDebe,
                SUM(acumulado.haber) as totalHaber,
                SUM(acumulado.debe) - SUM(acumulado.haber) as saldo,
                acumulado.centro_costo AS centroCostoDenominacion
            FROM
                (
                    SELECT
                        cc.id AS id_cuenta_contable,
                        cc.codigo as codigo_cuenta_contable,
                        cc.denominacion AS denominacion_cuenta_contable,
                        cent.denominacion AS centro_costo,
                        CASE toc.denominacion
                            WHEN :denominacionDebe THEN
                                    SUM(r.importe_mcl)
                            ELSE 0
                        END AS debe,
                        CASE toc.denominacion
                            WHEN :denominacionHaber THEN
                                    SUM(r.importe_mcl)
                            ELSE 0
                        END AS haber
                    FROM cuenta_contable cc
                        LEFT JOIN (SELECT r.id_cuenta_contable, r.id_tipo_operacion_contable, r.importe_mcl, a.fecha_contable  
                                    FROM renglon_asiento_contable r
                                        INNER JOIN asiento_contable a ON a.id = r.id_asiento_contable 
                                        INNER JOIN concepto_asiento_contable c ON a.id_concepto_asiento_contable = c.id
                                    WHERE r.fecha_baja IS NULL
                                        AND a.fecha_baja IS NULL';
        if (!$conAsientosFormales) {
            $queryInicio .= '
                                    AND c.codigo NOT IN (:conceptoFormalRefundicion, :conceptoFormalCierre, :conceptoFormalApertura)';
        }

        $queryInicio .= '
                                        ) r ON r.id_cuenta_contable = cc.id
                        LEFT JOIN tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                        LEFT JOIN centro_costo cent ON cent.codigo = SUBSTRING(cc.codigo, 5, 2) * 1
                    WHERE cc.es_imputable = 1  
					AND cc.activa = 1
					AND (r.fecha_contable >= :fechaInicio AND r.fecha_contable < :fechaIntermedia)
                    GROUP BY cc.id, r.id_tipo_operacion_contable
                ) AS acumulado
                GROUP BY acumulado.id_cuenta_contable'
        ;

/*
        if ($soloCuentasConSaldo) {
            $queryInicio .= ' HAVING ROUND(totalDebe, 2) <> ROUND(totalHaber, 2)';
        }
*/

        $queryInicio .= ' ORDER BY codigoCuentaContableOrden ASC, centroCosto ASC';

        $statement = $connection->prepare($queryInicio);

        if (!$conAsientosFormales) {
            $statement->bindValue('conceptoFormalRefundicion', ConstanteConceptoAsientoContable::FORMAL_REFUNDICION);
            $statement->bindValue('conceptoFormalCierre', ConstanteConceptoAsientoContable::FORMAL_CIERRE);
            $statement->bindValue('conceptoFormalApertura', ConstanteConceptoAsientoContable::FORMAL_APERTURA);
        }
        $statement->bindValue('fechaInicio', $fechaInicioEjercicio, \Doctrine\DBAL\Types\Type::DATETIME);
        $statement->bindValue('fechaIntermedia', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME);
        $statement->bindValue('denominacionDebe', ConstanteTipoOperacionContable::DEBE);
        $statement->bindValue('denominacionHaber', ConstanteTipoOperacionContable::HABER);

        $statement->execute();
        //$statement->getParams();die;

        $cuentasContablesAnteriores = new ArrayCollection($statement->fetchAll());
		//$cuentasContables = $statement->fetchAll();

        $queryFin = '
            SELECT
                acumulado.id_cuenta_contable as id,
                acumulado.denominacion_cuenta_contable as denominacionCuentaContable,
                acumulado.codigo_cuenta_contable as codigoCuentaContable,
                CONCAT(SUBSTRING(acumulado.codigo_cuenta_contable, 1, 4), "00", SUBSTRING(acumulado.codigo_cuenta_contable, 7)) AS codigoCuentaContableOrden,
                CONCAT(SUBSTRING(acumulado.codigo_cuenta_contable, 5, 2)) AS centroCosto,
                sum(acumulado.debe) as totalDebe,
                sum(acumulado.haber) as totalHaber,
                sum(acumulado.debe) - sum(acumulado.haber) as saldo,
                acumulado.centro_costo AS centroCostoDenominacion
            FROM
                (
                    SELECT
                        cc.id AS id_cuenta_contable,
                        cc.codigo as codigo_cuenta_contable,
                        cc.denominacion AS denominacion_cuenta_contable,
                        cent.denominacion AS centro_costo,
                        CASE toc.denominacion
                            WHEN :denominacionDebe THEN
                                    sum(r.importe_mcl)
                            ELSE 0
                        END AS debe,
                        CASE toc.denominacion
                            WHEN :denominacionHaber THEN
                                    sum(r.importe_mcl)
                            ELSE 0
                        END AS haber
                    FROM cuenta_contable cc
                        LEFT JOIN (SELECT r.id_cuenta_contable, r.id_tipo_operacion_contable, r.importe_mcl, a.fecha_contable  
                                    FROM renglon_asiento_contable r
                                        INNER JOIN asiento_contable a ON a.id = r.id_asiento_contable
                                        INNER JOIN concepto_asiento_contable c ON a.id_concepto_asiento_contable = c.id
                                    WHERE r.fecha_baja IS NULL
                                        AND a.fecha_baja IS NULL
                        ';

        if (!$conAsientosFormales) {
            $queryFin .= '
                                        AND c.codigo NOT IN (:conceptoFormalRefundicion, :conceptoFormalCierre, :conceptoFormalApertura)';
        }

        $queryFin .= '
                                ) r ON r.id_cuenta_contable = cc.id
                        LEFT JOIN tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                        LEFT JOIN centro_costo cent ON cent.codigo = SUBSTRING(cc.codigo, 5, 2) * 1
                    WHERE cc.es_imputable = 1
					AND cc.activa = 1
					AND (r.fecha_contable >= :fechaInicio AND r.fecha_contable <= :fechaFin)
                    GROUP BY cc.id, r.id_tipo_operacion_contable
                ) AS acumulado                
                GROUP BY acumulado.id_cuenta_contable';
/*
        if ($soloCuentasConSaldo) {
            $queryFin .= ' HAVING ROUND(totalDebe, 2) <> ROUND(totalHaber, 2)';
        }
*/

        $queryFin .= ' ORDER BY codigoCuentaContableOrden ASC, centroCosto ASC';

        $statementFin = $connection->prepare($queryFin);

        if (!$conAsientosFormales) {
            $statementFin->bindValue('conceptoFormalRefundicion', ConstanteConceptoAsientoContable::FORMAL_REFUNDICION);
            $statementFin->bindValue('conceptoFormalCierre', ConstanteConceptoAsientoContable::FORMAL_CIERRE);
            $statementFin->bindValue('conceptoFormalApertura', ConstanteConceptoAsientoContable::FORMAL_APERTURA);
        }

        $statementFin->bindValue('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME);
        $statementFin->bindValue('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME);
        $statementFin->bindValue('denominacionDebe', ConstanteTipoOperacionContable::DEBE);
        $statementFin->bindValue('denominacionHaber', ConstanteTipoOperacionContable::HABER);

        $statementFin->execute();

        //$cuentasContablesActuales = $statementFin->fetchAll();
		$cuentasContablesActuales = new ArrayCollection($statementFin->fetchAll());
		
		//\Doctrine\Common\Util\Debug::dump( $cuentasContablesActuales ); exit; 	

        $jsonResult = [];

		foreach($todasCuentasContables as $cuentaContable) {
			
			$id = $cuentaContable->getId();
			
			$resultadoCuentaContableAnterior = $cuentasContablesAnteriores->filter(
				function($entry) use ($id) {
					return in_array($entry['id'], array($id));
				}
            );
			
			$resultadoCuentaContableActual = $cuentasContablesActuales->filter(
				function($entry) use ($id) {
					return in_array($entry['id'], array($id));
				}
            );
			
            $saldoAnterior = (!$resultadoCuentaContableAnterior->isEmpty() ? $resultadoCuentaContableAnterior->first()['saldo'] : 0);
			$saldoAnterior = round($saldoAnterior, 2);
			$totalDebe = round($resultadoCuentaContableActual->first()['totalDebe'], 2);
			$totalHaber = round($resultadoCuentaContableActual->first()['totalHaber'], 2);
			$saldo = $saldoAnterior + $totalDebe - $totalHaber;
		
			if ($soloCuentasConSaldo && $saldo == 0) {
				continue;
			}
			
            $jsonResult[] = array(
                'cuentaContable' => $cuentaContable->__toString(),
                'saldoMesAnterior' => $saldoAnterior,
                'totalDebe' => $totalDebe,
                'totalHaber' => $totalHaber,
                'saldo' => $saldo
            );
			
		}

        return new JsonResponse($jsonResult);
    }

    /**
     * Muestra la pantalla de movimientos de cliente.
     *
     * @Route("/movimientoscliente/", name="asientocontable_movimientos_cliente")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte.movimientos_cliente.html.twig")
     */
    public function movimientoClienteAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Movimientos cliente'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Movimientos cliente'
        );
    }

    /**
     * @Route("/filtrar_movimientoscliente/", name="asientocontable_filtrar_movimientoscliente")
     */
    public function filtrarMovimientosClienteAction(Request $request) {

        $razonSocial = $request->get('razonSocial');

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:RenglonAsientoContable', $this->getEntityManager());

        $qb = $repository->createQueryBuilder('r');

        $renglonesAsientoContable = $qb->innerJoin('r.asientoContable', 'a')
                ->select(
                        'partial r.{id, cuentaContable, tipoOperacionContable, importeMCL, detalle}', //
                        'partial a.{id, fechaContable, numeroAsiento, numeroOriginal, denominacionAsientoContable, razonSocial, numeroDocumento}')
                ->where('a.razonSocial = :razonSocial')
                ->andWhere($qb->expr()->between('a.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('razonSocial', $razonSocial)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->addOrderBy('a.fechaContable', 'ASC')
                ->addOrderBy('a.id', 'ASC')
                ->addOrderBy('r.tipoOperacionContable', 'ASC')
                ->addOrderBy('a.numeroAsiento', 'ASC')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        $fechaInicio->sub(new \DateInterval('PT1S'));

        foreach ($renglonesAsientoContable as $renglonAsientoContable) {

            $razonSocial = $renglonAsientoContable->getAsientoContable()->getRazonSocial() != null //
                    ? $renglonAsientoContable->getAsientoContable()->getRazonSocial() //
                    : '-';

            $numeroDocumento = $renglonAsientoContable->getAsientoContable()->getNumeroDocumento() != null //
                    ? $renglonAsientoContable->getAsientoContable()->getNumeroDocumento() //
                    : '-';

            $jsonResult[] = array(
                'id' => $renglonAsientoContable->getId(),
                'idAsiento' => $renglonAsientoContable->getAsientoContable()->getId(),
                'fechaContable' => $renglonAsientoContable->getAsientoContable()->getFechaContable()->format('d/m/Y'),
                'razonSocial' => $razonSocial,
                'numeroDocumento' => $numeroDocumento,
                'numeroAsientoOriginal' => $renglonAsientoContable->getAsientoContable()->getNumeroOriginal(),
                'numeroAsiento' => $renglonAsientoContable->getAsientoContable()->getNumeroAsiento(),
                'conceptoAsientoContable' => $renglonAsientoContable->getAsientoContable()->getConceptoAsientoContable()->__toString(),
                'cuentaContable' => $renglonAsientoContable->getCuentaContable()->__toString(),
                'tipoImputacion' => $renglonAsientoContable->getTipoOperacionContable()->__toString(),
                'importeMCL' => $renglonAsientoContable->getImporteMCL(),
                'titulo' => $renglonAsientoContable->getAsientoContable()->getDenominacionAsientoContable(),
                'detalle' => $renglonAsientoContable->getDetalle()
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @param type $denominacionEstado
     */
    private function setEstadoAsientoContable(AsientoContable $asientoContable, $denominacionEstado) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Obtengo el EstadoAsientoContable cuya denominacion sea igual a "$denominacionEstado"
        $estadoAsiento = $em->getRepository('ADIFContableBundle:EstadoAsientoContable')->
                findOneBy(
                array('denominacionEstado' => $denominacionEstado), //
                array('id' => 'desc'), 1, 0)
        ;

        $asientoContable->setEstadoAsientoContable($estadoAsiento);
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @param type $denominacionTipo
     */
    private function setTipoAsientoContable(AsientoContable $asientoContable, $denominacionTipo) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Obtengo el TipoAsientoContable cuya denominacion sea igual a "$denominacionTipo"
        $tipoAsiento = $em->getRepository('ADIFContableBundle:TipoAsientoContable')->
                findOneBy(
                array('denominacion' => $denominacionTipo), //
                array('id' => 'desc'), 1, 0)
        ;

        $asientoContable->setTipoAsientoContable($tipoAsiento);
    }

    /**
     *  Le agrega a la fecha contable, los HH:mm:ss actuales
     * 
     * @param AsientoContable $asientoContable
     */
    public function actualizarFechaContable(AsientoContable $asientoContable) {
        $time = date('H:i:s');

        $timeArr = array_reverse(explode(":", $time));

        $seconds = 0;

        foreach ($timeArr as $key => $value) {
            if ($key > 2) {
                break;
            }

            $seconds += pow(60, $key) * $value;
        }

        $asientoContable->getFechaContable()->add(new \DateInterval('PT' . $seconds . 'S'));
    }

    /**
     * 
     * @param type $renglonAsientoContable
     * @param type $saldoActual
     */
    private function getSaldoActual($renglonAsientoContable, $saldoActual) {
        if (ConstanteTipoOperacionContable::DEBE == $renglonAsientoContable->getTipoOperacionContable()->getDenominacion()) {
            $saldoActual += $renglonAsientoContable->getImporteMCL();
        } else {
            $saldoActual -= $renglonAsientoContable->getImporteMCL();
        }

        return $saldoActual;
    }

    /**
     * Tabla para asientos contables.
     *
     * @Route("/index_table/", name="asientocontable_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request) {

        $asientosContables = array();

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('numeroOriginal', 'numeroOriginal');
            $rsm->addScalarResult('numeroAsiento', 'numeroAsiento');
            $rsm->addScalarResult('fechaContable', 'fechaContable');
            $rsm->addScalarResult('tipoAsientoContable', 'tipoAsientoContable');
            $rsm->addScalarResult('conceptoAsientoContable', 'conceptoAsientoContable');
            $rsm->addScalarResult('codigoConceptoAsientoContable', 'codigoConceptoAsientoContable');
            $rsm->addScalarResult('denominacionAsientoContable', 'denominacionAsientoContable');
            $rsm->addScalarResult('numeroDocumento', 'numeroDocumento');
            $rsm->addScalarResult('razonSocial', 'razonSocial');
            $rsm->addScalarResult('totalDebe', 'totalDebe');
            $rsm->addScalarResult('totalHaber', 'totalHaber');
            $rsm->addScalarResult('estadoAsientoContable', 'estadoAsientoContable');
            $rsm->addScalarResult('esManual', 'esManual');
            $rsm->addScalarResult('fueRevertido', 'fueRevertido');
            $rsm->addScalarResult('usuario', 'usuario');

            $native_query = $em->createNativeQuery('
            SELECT
		id,
		numeroOriginal,
		numeroAsiento,
		fechaContable,
		tipoAsientoContable,
		conceptoAsientoContable,
        codigoConceptoAsientoContable,
		denominacionAsientoContable,
		numeroDocumento,
		razonSocial,
		totalDebe,
		totalHaber,
		estadoAsientoContable,
		esManual,
		fueRevertido,
		usuario
            FROM
                vistaasientoscontables
            WHERE fechaContable BETWEEN ? AND ?
        ', $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $asientosContables = $native_query->getResult();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Asientos contables'] = null;

        return $this->render('ADIFContableBundle:AsientoContable:index_table.html.twig', array(
                    'entities' => $asientosContables
                        )
        );
    }

    /**
     *
     * @Route("/editar_fecha/", name="asientocontable_editar_fecha")
     */
    public function updateFechaAction(Request $request) {

        // Si el usuario logueado genera asientos contables
        if (false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {

            $fecha = $request->request->get('fecha');
            $fechaFormatted = \DateTime::createFromFormat('d/m/Y', $fecha);

            if ($this->get('adif.asiento_service')->getFechaContableValida($fechaFormatted)) {

                $em = $this->getDoctrine()->getManager($this->getEntityManager());

                $numerosAsiento = explode(',', $request->request->get('numero_asiento'));

                foreach ($numerosAsiento as $numeroAsiento) {

                    $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                            ->findOneByNumeroAsiento((int) $numeroAsiento);

                    if (!$asientoContable) {
                        throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
                    }

                    $asientoContable->setFechaContable($fechaFormatted);

                    $em->persist($asientoContable);
                }

                // Actualizo los numeros de asientos posteriores
                //$this->get('adif.asiento_service')->actualizarNumeroOficialAsiento($asientoContable);

                $em->flush();

                $cantidadAsientosGenerados = sizeof($numerosAsiento);

                if ($cantidadAsientosGenerados > 0) {

                    if ($cantidadAsientosGenerados == 1) {

                        $showPath = $this->generateUrl('asientocontable_show', array('id' => $asientoContable->getId()));

                        if ($numerosAsiento == $asientoContable->getNumeroAsiento()) {

                            $mensajeFlash = 'El asiento n&deg; ' . $numerosAsiento
                                    . ' fue modificado correctamente';
                        } else {

                            $mensajeFlash = 'El asiento fue modificado correctamente. '
                                    . 'Se renumer&oacute; al n&deg; '
                                    . $asientoContable->getNumeroAsiento();
                        }
                    } else {

                        $showPath = $this->generateUrl('asientocontable');

                        $mensajeFlash = 'Los asientos fueron modificados correctamente';
                    }

                    $mensajeFlash .= '. Para ver el detalle haga click '
                            . '<a href="' . $showPath . '" class="detalle-asiento-link" target="_blank">aqu&iacute;</a>.';

                    $status = 'OK';

                    $this->get('session')->getFlashBag()->add('success', $mensajeFlash);
                }
            } else {

                $status = 'ERROR';

                $mensajeFlash = 'La fecha indicada pertenece a un período contable cerrado';
            }

            return new JsonResponse(array('status' => $status, 'message' => $mensajeFlash));
        }

        $status = 'ERROR';

        return new JsonResponse(array('status' => $status));
    }

    /**
     *
     * @Route("/revertir/{id}", name="asientocontable_revertir")
     * @Method("GET")
     */
    public function revertirAsientoContableAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->find($id);

        if (!$asientoContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        if ($this->getEsAsientoReversionValido($asientoContable)) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {

                $fechaContable = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_contable') . ' 00:00:00');

                $asientoContableRevertido = $this->get('adif.asiento_service')
                        ->revertirAsientoContable($asientoContable, $fechaContable, $this->getUser());

                $asientoContable->setFueRevertido(true);

                $em->flush();

                $showPath = $this->generateUrl('asientocontable_show', array('id' => $asientoContableRevertido->getId()));

                $mensajeFlash = 'El asiento fue revertido correctamente.'
                        . ' Se gener&oacute; el asiento n&deg; ' . $asientoContableRevertido->getNumeroAsiento()
                        . ', para ver su detalle haga click '
                        . '<a href="' . $showPath . '" class="detalle-asiento-link" target="_blank">aqu&iacute;</a>.';

                $this->get('session')->getFlashBag()->add('success', $mensajeFlash);

                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
        } else {

            $mensajeFlash = 'El asiento formal n&deg; ' . $asientoContable->getNumeroAsiento() . ' no se puede revertir.';

            $this->get('session')->getFlashBag()->add('error', $mensajeFlash);
        }

        return $this->redirect($this->generateUrl('asientocontable'));
    }

    /**
     *
     * @Route("/importar/", name="asientocontable_importar")
     * @Method("GET")
     * @Template()
     */
    public function indexImportarAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Asientos contables'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $denominacionEjercicioContableSesion = $this->get('session')->get('ejercicio_contable');

        /* @var $ejercicioContable EjercicioContable */
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByDenominacion($denominacionEjercicioContableSesion);
        $fechaMesCerradoSuperior = $this->get('adif.asiento_service')
                ->getFechaMesCerradoSuperiorByEjercicio($ejercicioContable);

        $form = $this->createUploadForm()->createView();

        $this->get('session')->remove('asientocontable_importar_data');

        return array(
            'form' => $form, 'breadcrumbs' => $bread,
            'fechaMesCerradoSuperior' => $fechaMesCerradoSuperior,
            'page_title' => 'Importar asiento contable'
        );
    }

    private function createUploadForm() {
        return
                        $this->createFormBuilder(null, array(
                            'action' => $this->generateUrl('asientocontable_upload'),
                            'method' => 'POST',
                        ))->add('fechaContable', 'date', array(
                            'required' => true,
                            'label' => 'Fecha contable',
                            'label_attr' => array('class' => 'control-label'),
                            'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text', 'format' => 'dd/MM/yyyy')
                        )
                        ->add('denominacionAsientoContable', null, array(
                            'required' => true,
                            'label' => 'T&iacute;tulo',
                            'label_attr' => array('class' => 'control-label'),
                            'attr' => array('class' => ' form-control '))
                        )
                        ->add('conceptoAsientoContable', 'entity', array(
                            'label' => 'Concepto',
                            'empty_value' => '-- Elija un concepto --',
                            'class' => 'ADIF\ContableBundle\Entity\ConceptoAsientoContable',
                            'attr' => array('class' => ' form-control choice '))
                        )
                        ->add('file', 'file', array(
                            'required' => true,
                            'label' => 'Archivo',
                            'mapped' => false
                        ))
                        ->add('submit', 'submit', array('label' => 'Previsualizar'))
                        ->getForm();
    }

    /**
     *
     * @Route("/upload", name="asientocontable_upload")
     * @Method("POST|GET")
     * @Template("ADIFContableBundle:AsientoContable:indexImportar.html.twig")
     */
    public function uploadAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $form = $this->createUploadForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $uploadDir = dirname($this->container->getParameter('kernel.root_dir')) . '/web/uploads/asientos/archivos';
            $newFile = 'importacion_asiento_' . date('YmdHis') . '.xls';
            $form['file']->getData()->move($uploadDir, $newFile);
            $objReader = PHPExcel_IOFactory::createReaderForFile($uploadDir . '/' . $newFile);
            if ($objReader->canRead($uploadDir . '/' . $newFile)) {
                $objReader->setReadDataOnly(true);
                try {
                    $objPHPExcel = $objReader->load($uploadDir . '/' . $newFile);
                } catch (Exception $e) {
                    echo $e->printStackTrace();
                    die;
                }
                $sheet = $objPHPExcel->getActiveSheet();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $fechaAsiento = $form['fechaContable']->getData();
                $tituloAsiento = $form['denominacionAsientoContable']->getData();
                $conceptoAsiento = $form['conceptoAsientoContable']->getData();

                $asientoContable = new AsientoContable();
                $asientoContable->setConceptoAsientoContable($conceptoAsiento);
                $asientoContable->setDenominacionAsientoContable($tituloAsiento);
                $asientoContable->setFechaContable($fechaAsiento);

                $total_debe = 0;
                $total_haber = 0;

                //  Loop through each row of the worksheet in turn
                $erroresProvisionObras = array();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                    $codigoCuenta = $rowData[0][0];
                    $detalle = $rowData[0][4];
                    if (!empty($rowData[0][2])) {
                        //Tiene valor en el debe
                        $imputacion = ConstanteTipoOperacionContable::DEBE;
                        $monto = $rowData[0][2];
                        $total_debe += $monto;
                    } else {
                        if (!empty($rowData [0][3])) {
                            //Tiene valor en el haber
                            $imputacion = ConstanteTipoOperacionContable::HABER;

                            $monto = $rowData[0][3];
                            $total_haber += $monto;
                        } else {
                            //Error, no tiene valor ni en el debe ni en el haber
                            $this->get('session')->getFlashBag()->add('error', 'Hay un error en el archivo en la l&iacute;nea ' . $row . '. El rengl&oacute;n no tiene valores');
                            return $this->redirect($this->generateUrl('asientocontable_importar'));
                        }
                    }
                    
                    $idContratoSiso = null;
                    $cuentaContableProvision = null;
                    $provisionMonto = null;
                    $esAsientoProvisionObras = false;
                    if ($conceptoAsiento->getCodigo() == ConstanteConceptoAsientoContable::PROVISION_OBRAS) {
                        $contratoSiso = trim($rowData[0][5]);
                        $idContratoSiso = $this->get('adif.siso_service')->getIdContratoByContrato($contratoSiso);
                        $codigoCuentaContableProvision = trim($rowData[0][11]);
                        $provisionMonto = $rowData[0][10];   
                        
                        $cuentaContableProvision = $em
                            ->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoCuentaContable($codigoCuentaContableProvision);
                        
                        if (!$cuentaContableProvision) {
                            $erroresProvisionObras[] = 'Hay un error en la l&iacute;nea ' . $row . '. No existe la cuenta contable de provisi&oacute;n con el c&oacute;digo: ' . $codigoCuentaContableProvision;
                        }
                        
                        /**
                         * Por pedido de GAF, se saca la validacion cruzada si existe el contrato en SISO
                         * @gluis - 26/01/2018
                         */
//                        if ($idContratoSiso == null) {
//                            $erroresProvisionObras[] = 'Hay un error en la l&iacute;nea ' . $row . '. No existe el contrato "' . $contratoSiso . '" en el sistema SISO.';
//                        }
                        
                        $esAsientoProvisionObras = true;
                    }

                    $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable($codigoCuenta);
                    if (!$cuentaContable) {
                        //echo 'Hay un error en la l&iacute;nea '.$row.'. No existe la cuenta contable con el c&oacute;digo: '.$codigoCuenta;die;
                        $this->get('session')->getFlashBag()->add('error', 'Hay un error en la l&iacute;nea ' . $row . '. No existe la cuenta contable con el c&oacute;digo: ' . $codigoCuenta);
                        return $this->redirect($this->generateUrl('asientocontable_importar'));
                    }

                    $renglonAsiento = new RenglonAsientoContable();
                    $renglonAsiento->setCuentaContable($cuentaContable);
                    $renglonAsiento->setDetalle($detalle);
                    $renglonAsiento->setTipoMoneda($em->getRepository('ADIFContableBundle:TipoMoneda')->findOneByEsMCL(true));
                    $renglonAsiento->setTipoOperacionContable($em->getRepository('ADIFContableBundle:TipoOperacionContable')->findOneByDenominacion($imputacion));
                    $renglonAsiento->setImporteMCL($monto);
                    $renglonAsiento->setImporteMO($monto);
                    // Campos para provision de obras
                    $renglonAsiento->setEsProvisionObra($esAsientoProvisionObras);
                    $renglonAsiento->setIdContratoSiso($idContratoSiso);
                    $renglonAsiento->setCuentaContableProvision($cuentaContableProvision);
                    $renglonAsiento->setProvisionMonto($provisionMonto);
                    
                    $asientoContable->addRenglonesAsientoContable($renglonAsiento);
                }
                
                if (!empty($erroresProvisionObras)) {
                    $this->addErrorFlash(implode('<br>', $erroresProvisionObras));
                    return $this->redirect($this->generateUrl('asientocontable_importar'));
                }

                $diferencia = round($total_haber, 2) - round($total_debe, 2);
                $epsilon = 0.00000001;

                if (!abs($diferencia) < $epsilon) {
                    $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error. El asiento no est&aacute; balanceado');
                    return $this->redirect($this->generateUrl('asientocontable_importar'));
                }
            } else {
                //echo 'Formato de archivo inv&aacute;lido';die;
                $this->get('session')->getFlashBag()->add('error', 'Formato de archivo inv&aacute;lido');
                return $this->redirect($this->generateUrl('asientocontable_importar'));
            }

            $this->get('session')->set('asientocontable_importar_data', array(
                'entity' =>
                $asientoContable
                    )
            );
        } else {
            echo $form->getErrorsAsString();
            die();
        }

        return $this->redirect($this->generateUrl('asientocontable_importar_previsualizar'));
    }

    /**
     * Lists all EmpleadoNovedad entities.
     *
     * @Route("/importar/previsualizar", name="asientocontable_importar_previsualizar")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:previsualizar.html.twig")
     */
    public function

    previsualizarImportarAction() {
        $data = $this->get('session')->get('asientocontable_importar_data');

        $bread = $this->base_breadcrumbs;
        $bread['Novedades'] = null;

        return array(
            'entity' => $data['entity'],
            'breadcrumbs' => $bread,
            'page_title' => 'Previsualizar importación'
        );
    }

    /**
     * Persistir la novedad
     *
     * @Route("/importar/guardar", name="asientocontable_importar_guardar")
     * @Method("GET")
     */
    public function guardarImportarAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $data = $this->get('session')->get('asientocontable_importar_data');
        if ($data) {
            // Abre transacción
            $em->getConnection()->beginTransaction();
            $asientoContableOriginal = $data['entity'];
            $asientoContable = $em->merge($asientoContableOriginal);

            /* @var $asientoContable AsientoContable */
            $asientoContable->setFechaCreacion(new \DateTime());
            $asientoContable->setFechaUltimaActualizacion(new \DateTime());
            $asientoContable->setUsuario($this->getUser());
            $this->setTipoAsientoContable($asientoContable, ConstanteTipoAsientoContable::TIPO_ASIENTO_MANUAL);
            $this->setEstadoAsientoContable($asientoContable, ConstanteEstadoAsientoContable::ESTADO_ASIENTO_GENERADO);
            //$this->actualizarFechaContable($asientoContable);

            foreach ($asientoContable->getRenglonesAsientoContable() as $renglonAsientoContable) {
                /* @var $renglonAsientoContable RenglonAsientoContable */
                $renglonAsientoContable->setFechaCreacion(new \DateTime());
                $renglonAsientoContable->setFechaUltimaActualizacion(new \DateTime());
                $renglonAsientoContable->setTipoOperacionContable($em->getRepository('ADIFContableBundle:TipoOperacionContable')->find($renglonAsientoContable->getTipoOperacionContable()->getId()));
                $renglonAsientoContable->setCuentaContable($em->getRepository('ADIFContableBundle:CuentaContable')->find($renglonAsientoContable->getCuentaContable()->getId()));
            }

            try {
                // Obtengo el siguiente numero de asiento oficial
                $siguienteNumeroAsiento = $this->get('adif.asiento_service')->getSiguienteNumeroAsiento();

                // Seteo los numeros de asiento oficial y original
                $asientoContable->setNumeroOriginal($siguienteNumeroAsiento);
                $asientoContable->setNumeroAsiento($siguienteNumeroAsiento);

                $this->actualizarFechaContable($asientoContable);
                // Persisto la entidad
                $em->persist($asientoContable);

                // Persisto los asientos presupuestarios
                $mensajeErrorAsientoPresupuestario = $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromAsientoManual($asientoContable);

                // Si el asiento presupuestario falló
                if ($mensajeErrorAsientoPresupuestario != '') {
                    $this->get('session')->getFlashBag()->add('error', $mensajeErrorAsientoPresupuestario);
                } else {
                    // Actualizo los numeros de asientos posteriores
                    //$this->get('adif.asiento_service')->actualizarNumeroOficialAsiento($asientoContable);

                    $em->flush();
                }

                $em->getConnection()->commit();
            } catch (Exception $e) {
                // Rollback
                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
            return $this->redirect($this->generateUrl('asientocontable'));
        } else {
            throw $this->createNotFoundException('No se puede importar el asiento.');
        }
    }

    /**
     *
     * @Route("/print_show", name="asientocontable_print_show")
     * @Method("GET|POST")
     */
    public function printShowAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->find($request->request->get('idAsientoContable'));

        if (!$asientoContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . $this->renderView('ADIFContableBundle:AsientoContable:print.show.css.twig') . '</style></head><body>';
        $html .= $this->renderView('ADIFContableBundle:AsientoContable:print.show.html.twig', [
            'entity' => $asientoContable,
            'asientoContable' => $asientoContable,
            'idEmpresa' => $idEmpresa,
        ]);
        $html .= '</body></html>';

        $filename = 'Asiento_Contable_' . $asientoContable->getNumeroAsiento() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * @Route("/refundicion_resultados/{id}", name="asientocontable_refundicion_resultados")
     */
    public function generarAsientoRefundicionResultadosAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $fechaContable = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_contable') . ' 00:00:00');

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->find($id);

        $numeroAsiento = $this->get('adif.asiento_service')
                ->generarAsientoRefundicionResultados($ejercicioContable, $fechaContable, $this->getUser());

        // Si no hubo errores en los asientos
        if (ltrim($numeroAsiento, '0') != -1) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($numeroAsiento, array());
            } //.
            catch (\ Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
        } else {

            return $this->redirect($this->generateUrl('ejercicio'));
        }

        return $this->redirect($this->generateUrl('asientocontable'));
    }

    /**
     * @Route("/cierre_ejercicio/{id}", name="asientocontable_cierre_ejercicio")
     */
    public function generarAsientoCierreEjercicioAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaContable = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_contable') . ' 00:00:00');

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->find($id);

        $numeroAsiento = $this->get('adif.asiento_service')
                ->generarAsientoCierreEjercicio($ejercicioContable, $fechaContable, $this->getUser());

        // Si no hubo errores en los asientos
        if (ltrim($numeroAsiento, '0') != -1) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($numeroAsiento, array());
            } //.
            catch (\ Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
        } else {

            return $this->redirect($this->generateUrl('ejercicio'));
        }

        return $this->redirect($this->generateUrl('asientocontable'));
    }

    /**
     * @Route("/apertura_ejercicio/{id}", name="asientocontable_apertura_ejercicio")
     */
    public function generarAsientoAperturaEjercicioAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaContable = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_contable') . ' 00:00:00');

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->find($id);

        $numeroAsiento = $this->get('adif.asiento_service')
                ->generarAsientoAperturaEjercicio($ejercicioContable, $fechaContable, $this->getUser());

        // Si no hubo errores en los asientos
        if (ltrim($numeroAsiento, '0') != -1) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($numeroAsiento, array());
            } //.
            catch (\Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
        } else {

            return $this->redirect($this->generateUrl('ejercicio'));
        }

        return $this->redirect($this->generateUrl('asientocontable'));
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @return boolean
     */
	 /*
    private function getEsAsientoReversionValido(AsientoContable $asientoContable) {

        $esValido = true;

        $codigoConceptoAsientoContable = $asientoContable->getConceptoAsientoContable()->getCodigo();

        if ($codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_REFUNDICION || $codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_CIERRE) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                    ->getEjercicioContableByFecha($asientoContable->getFechaContable());

            if ($codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_REFUNDICION) {

                $cantidadAsientosCierre = $em->getRepository('ADIFContableBundle:AsientoContable')
                        ->countAsientoFormalByEjercicioYConcepto($ejercicioContable->getDenominacionEjercicio(), ConstanteConceptoAsientoContable::FORMAL_CIERRE);

                $esValido = $cantidadAsientosCierre == 0;
            } else {

                $denominacionEjercicioContableAnterior = ((int) $ejercicioContable->getDenominacionEjercicio()) + 1;

                $cantidadAsientosApertura = $em->getRepository('ADIFContableBundle:AsientoContable')
                        ->countAsientoFormalByEjercicioYConcepto($denominacionEjercicioContableAnterior, ConstanteConceptoAsientoContable::FORMAL_APERTURA);

                $esValido = $cantidadAsientosApertura == 0;
            }
        }

        return $esValido;
    }
	*/
	
	/**
	* El metodo getEsAsientoReversionValido() original es el de arriba, (linea 1908 a 1939) solo hago esta excepcion 
	* a la validacion para que puedan revertir el asiento de cierre del 2015 que esta mal!!
	* gluis - 23/11/2016
	*/
	private function getEsAsientoReversionValido(AsientoContable $asientoContable) {

        $esValido = true;

        $codigoConceptoAsientoContable = $asientoContable->getConceptoAsientoContable()->getCodigo();
		
		if ( $asientoContable->getFechaContable()->format('Y')  == 2015 && 
				(
					$codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_CIERRE || 
					$codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_REFUNDICION || 
					$codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_APERTURA
				)
			) 
		{
			return true;
		}

        if ($codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_REFUNDICION || $codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_CIERRE) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                    ->getEjercicioContableByFecha($asientoContable->getFechaContable());

            if ($codigoConceptoAsientoContable == ConstanteConceptoAsientoContable::FORMAL_REFUNDICION) {

                $cantidadAsientosCierre = $em->getRepository('ADIFContableBundle:AsientoContable')
                        ->countAsientoFormalByEjercicioYConcepto($ejercicioContable->getDenominacionEjercicio(), ConstanteConceptoAsientoContable::FORMAL_CIERRE);

                $esValido = $cantidadAsientosCierre == 0;
            } else {

                $denominacionEjercicioContableAnterior = ((int) $ejercicioContable->getDenominacionEjercicio()) + 1;

                $cantidadAsientosApertura = $em->getRepository('ADIFContableBundle:AsientoContable')
                        ->countAsientoFormalByEjercicioYConcepto($denominacionEjercicioContableAnterior, ConstanteConceptoAsientoContable::FORMAL_APERTURA);

                $esValido = $cantidadAsientosApertura == 0;
            }
        }

        return $esValido;
    }
    
    /**
     * Pantalla inicial del reporte de provision de obra en curso. Puede ser acumulado o mensual
     *
     * @Route("/reporte/control_provision_obras/acumulado", name="asientocontable_reporte_provision_obra_acumulado")
     * @Method("GET")
     * @Template("ADIFContableBundle:AsientoContable:reporte_provision_obra_acumulado.html.twig")
     */
    public function reporteProvisionObraAcumuladoAction() {
        
        $bread = $this->base_breadcrumbs;
        $bread['Reporte control de provis&oacute;n de obra en curso'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Reporte control de provis&oacute;n de obra en curso'
        );
    }
    
    /**
     * @Route("/reporte/control_provision_obras/acumulado_index_table", name="asientocontable_reporte_provision_obra_acumulado_index_table")
     * @Method("POST")
     */
    public function reporteProvisionObraAcumuladoAjaxAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $fechaInicio = $request->get('fechaInicio');
        $fechaFin = $request->get('fechaFin');
        $dtFechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $fechaInicio . ' 00:00:00');
        $dtFechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $fechaFin . ' 23:59:59');
        
        // Traigo todos los renglones de los asientos que matcheen con las cuentas de mayor de fuente de financiamiento
        /*
        $asientosContables = $em
                    ->getRepository('ADIFContableBundle:AsientoContable')
                    ->getRenglonesAsientosFuenteFinanciamiento($dtFechaInicio, $dtFechaFin);
        */
        // Se modificó para que traiga cuentas y descripcion de provision
        $asientosContables = $em
                    ->getRepository('ADIFContableBundle:AsientoContable')
                    ->getRenglonesAsientosFuenteFinanciamientoProvision($dtFechaInicio, $dtFechaFin);
        
        $idCuentaContable = 0;
        $idCuentaContableAnt = -1;
        $provisiones = array();
        $provisionesCuentaContables = array();
        $newAsientosContables = array();
        $asientosCuentas = array();
        
        /*
        // Hago corte control por el $idCuentaContable
        for($i = 0; $i < count($asientosContables); $i++) {
            
            $asientoContable = $asientosContables[$i];
            $idCuentaContable = $asientoContable['id_cuenta_contable'];
            
            if ($idCuentaContable != $idCuentaContableAnt) {
                   
                $provisiones = $em
                                ->getRepository('ADIFContableBundle:AsientoContable')
                                ->getRenglonesAsientosProvisionObraByIdCuentaContable($idCuentaContable);
                
                if (!empty($provisiones)) {
                    // Si la cuenta contable tiene una provision la voy guardando por $idCuentaContable
                    $provisionesCuentaContables[$idCuentaContable] = $provisiones;
                } else {
                    // Voy guardando las demas renglones de los asientos por $idCuentaContable
                    $asientosCuentas[$idCuentaContable][] = $asientoContable;
                }

            } else {
                $provisiones = array();
                // Voy guardando las demas renglones de los asientos por $idCuentaContable
                $asientosCuentas[$idCuentaContable][] = $asientoContable;
            }
            
            $idCuentaContableAnt = $asientoContable['id_cuenta_contable'];
        }
        
        // Hago el merge del total de todas las cuentas vs las que tienen provision 
        foreach($asientosCuentas as $idCuentaContable => $value) {
            
            if (isset($provisionesCuentaContables[$idCuentaContable])) {
                $newAsientosContables[] = array_merge($value, $provisionesCuentaContables[$idCuentaContable]);
            } else {
                $newAsientosContables[] = $value;
            }
        }
*/       
        
        
        // Hago corte control por el $idCuentaContable
        for($i = 0; $i < count($asientosContables); $i++) {
            
            $asientoContable = $asientosContables[$i];
            $idCuentaContable = $asientoContable['id_cuenta_contable'];
            
            if ($idCuentaContable != $idCuentaContableAnt) {
                $asientosCuentas[$idCuentaContable][] = $asientoContable;
            } else {
               // Voy guardando las demas renglones de los asientos por $idCuentaContable
                $asientosCuentas[$idCuentaContable][] = $asientoContable;
            }
            
            $idCuentaContableAnt = $asientoContable['id_cuenta_contable'];
        }
        
        // Hago el merge del total de todas las cuentas vs las que tienen provision 
        foreach($asientosCuentas as $idCuentaContable => $value) {
            $newAsientosContables[] = $value;
        }
        
        // Paso a un array bidimensional
        $newArray = array();
        foreach($newAsientosContables as $subArray){
            foreach($subArray as $val){
                $newArray[] = $val;
            }
        }
                    
        return $this->render('ADIFContableBundle:AsientoContable:reporte_provision_obra_acumulado_index_table.html.twig', 
            array('entities' => $newArray)
        );
    }
}
