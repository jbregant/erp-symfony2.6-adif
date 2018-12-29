<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ComprasBundle\Entity\Requerimiento;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\CategoriaCuentaPresupuestariaEconomica;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioRemuneracion;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioServiciosNoPersonales;
use ADIF\ContableBundle\Entity\CuentaContable;
use ADIF\ContableBundle\Entity\CuentaPresupuestaria;
use ADIF\ContableBundle\Entity\Presupuesto;
use ADIF\ContableBundle\Entity\TipoConceptoPresupuestarioRemuneracion;
use ADIF\ContableBundle\Form\PresupuestoType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Entity\Constantes\ConstanteMotivoEgreso;
use Doctrine\ORM\Query\ResultSetMapping;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoTransaccionMinisterial;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoAsientoContable;
use Symfony\Component\HttpFoundation\Response;
use mPDF;
use Doctrine\DBAL\Types\Type;
use ADIF\WarehouseBundle\Entity\PresupuestoEjecucion;

/**
 * Presupuesto controller.
 *
 * @Route("/presupuesto")
 */
class PresupuestoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Presupuesto' => $this->generateUrl('presupuesto')
        );
    }

    /**
     * Lists all Presupuesto entities.
     *
     * @Route("/", name="presupuesto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $denominacionEjercicioContableSesion = $this->get('session')
                ->get('ejercicio_contable');

        if ($denominacionEjercicioContableSesion) {

            $presupuestos = $em
                    ->getRepository('ADIFContableBundle:Presupuesto')
                    ->createQueryBuilder('p')
                    ->innerJoin('p.ejercicioContable', 'e')
                    ->where('e.denominacionEjercicio = :denominacionEjercicio')
                    ->setParameter('denominacionEjercicio', $denominacionEjercicioContableSesion)
                    ->getQuery()
                    ->getResult();
        } else {

            $presupuestos = $em->getRepository('ADIFContableBundle:Presupuesto')
                    ->findAll();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Presupuestos'] = null;

        return array(
            'entities' => $presupuestos,
            'breadcrumbs' => $bread,
            'page_title' => 'Presupuestos',
            'page_info' => 'Lista de presupuestos'
        );
    }

    /**
     * Creates a new Presupuesto entity.
     *
     * @Route("/insertar", name="presupuesto_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Presupuesto:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Presupuesto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            foreach ($entity->getCuentasPresupuestarias() as $cuentaPresupuestaria) {
                $cuentaPresupuestaria->setPresupuesto($entity);
                $cuentaPresupuestaria->setMontoActual($cuentaPresupuestaria->getMontoInicial());
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('presupuesto'));
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
            'page_title' => 'Crear presupuesto',
        );
    }

    /**
     * Creates a form to create a Presupuesto entity.
     *
     * @param Presupuesto $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Presupuesto $entity) {
        $form = $this->createForm(new PresupuestoType(), $entity, array(
            'action' => $this->generateUrl('presupuesto_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Presupuesto entity.
     *
     * @Route("/crear", name="presupuesto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Presupuesto();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentasPresupuestariasEconomicas = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->findBy(
                array(), array('codigo' => 'ASC')
        );
//        $cuentasPresupuestariasEconomicas = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->findBy(
//                array(
//                    'esImputable' => 1
//                )
//        );


        foreach ($cuentasPresupuestariasEconomicas as $cuentaPresupuestariaEconomica) {

            $cuentaPresupuestaria = new CuentaPresupuestaria();

            $cuentaPresupuestaria
                    ->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica)
                    ->setMontoInicial(0);

            $entity->addCuentaPresupuestaria($cuentaPresupuestaria);
        }

        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear presupuesto'
        );
    }

    /**
     * Finds and displays a Presupuesto entity.
     *
     * @Route("/{id}", name="presupuesto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        return $this->ejecucionPresupuestariaAction($id, $esShow = true);
    }

    /**
     * Displays a form to edit an existing Presupuesto entity.
     *
     * @Route("/editar/{id}", name="presupuesto_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Presupuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Presupuesto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar presupuesto'
        );
    }

    /**
     * Creates a form to edit a Presupuesto entity.
     *
     * @param Presupuesto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Presupuesto $entity) {
        $form = $this->createForm(new PresupuestoType(), $entity, array(
            'action' => $this->generateUrl('presupuesto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Presupuesto entity.
     *
     * @Route("/actualizar/{id}", name="presupuesto_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Presupuesto:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Presupuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Presupuesto.');
        }

        $montosInicialesArray = array();

        // Guardo los montos iniciales antiguos
        foreach ($entity->getCuentasPresupuestarias() as $cuentaPresupuestaria) {
            $montosInicialesArray[$cuentaPresupuestaria->getId()] = $cuentaPresupuestaria->getMontoInicial();
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Actualizo los montos actuales
            foreach ($entity->getCuentasPresupuestarias() as $cuentaPresupuestaria) {
                $cuentaPresupuestaria->setMontoActual(
                        $cuentaPresupuestaria->getMontoActual() //
                        + $cuentaPresupuestaria->getMontoInicial() //
                        - $montosInicialesArray[$cuentaPresupuestaria->getId()]
                );
            }

            $em->flush();

            return $this->redirect($this->generateUrl('presupuesto'));
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
            'page_title' => 'Editar presupuesto'
        );
    }

    /**
     * Deletes a Presupuesto entity.
     *
     * @Route("/borrar/{id}", name="presupuesto_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Presupuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Presupuesto.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('presupuesto'));
    }

    /**
     * Ejecucion presupuestaria
     *
     * @Route("/{id}/ejecucion", name="presupuesto_ejecucion")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:ejecucion.html.twig")
     */
    public function ejecucionPresupuestariaAction($id, $esShow = false) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentasPresupuestariasCorrienteSuma = $cuentasPresupuestariasCorrienteResta = array();
        $cuentasPresupuestariasCapitalSuma = $cuentasPresupuestariasCapitalResta = array();
        $cuentasPresupuestariasFinanciamientoSuma = $cuentasPresupuestariasFinanciamientoResta = array();

        /* @var $presupuesto Presupuesto */
        $presupuesto = $em->getRepository('ADIFContableBundle:Presupuesto')
                ->createQueryBuilder('p')
                ->select('partial p.{id, ejercicioContable}')
                ->where('p.id = :id')
                ->setParameter(':id', $id)
                ->getQuery()
                ->getOneOrNullResult();

        if (!$presupuesto) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Presupuesto.');
        }

        $cuentasPresupuestarias = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                ->createQueryBuilder('cp')
                ->innerJoin('cp.presupuesto', 'p')
                ->innerJoin('cp.cuentaPresupuestariaEconomica', 'cpe')
                ->select('partial p.{id}', 'cp', 'cpe')
                ->where('p.id = :id')
                ->setParameter(':id', $id)
                ->getQuery()
                ->getResult();

        foreach ($cuentasPresupuestarias as $cuentaPresupuestaria) {

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */

            $cuentaPresupuestariaEconomica = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica();

            $idCategoriaCuentaPresupuestariaEconomica = $cuentaPresupuestariaEconomica
                            ->getCategoriaCuentaPresupuestariaEconomica()->getId();

            if ($idCategoriaCuentaPresupuestariaEconomica == CategoriaCuentaPresupuestariaEconomica::__CORRIENTE) {

                if ($cuentaPresupuestariaEconomica->getSuma() == 1) {
                    $cuentasPresupuestariasCorrienteSuma[] = $cuentaPresupuestaria;
                } // 
                else {
                    $cuentasPresupuestariasCorrienteResta[] = $cuentaPresupuestaria;
                }
            } // 
            elseif ($idCategoriaCuentaPresupuestariaEconomica == CategoriaCuentaPresupuestariaEconomica::__CAPITAL) {

                if ($cuentaPresupuestariaEconomica->getSuma() == 1) {
                    $cuentasPresupuestariasCapitalSuma[] = $cuentaPresupuestaria;
                } // 
                else {
                    $cuentasPresupuestariasCapitalResta[] = $cuentaPresupuestaria;
                }
            } // 
            elseif ($idCategoriaCuentaPresupuestariaEconomica == CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO) {

                if ($cuentaPresupuestariaEconomica->getSuma() == 1) {
                    $cuentasPresupuestariasFinanciamientoSuma[] = $cuentaPresupuestaria;
                } // 
                else {
                    $cuentasPresupuestariasFinanciamientoResta[] = $cuentaPresupuestaria;
                }
            }
        }

		// Cambio: se toma los valores de ejecucion presupuestaria del warehouse creado por un batch que corre
		// a la madrugada en el servidor, ya que la funcion adifprod_contable.tot_presupuestario(), 
		// de Mysql tarda mucho. En el reporte siempre va a buscarlo a la fecha del dia.
		// gluis - 12/01/2017
		$emWareHouse = $this->getDoctrine()->getManager(EntityManagers::getEmWarehouse());
		
		$fechaHoy = new \DateTime();
		$fechaDesde = \DateTime::createFromFormat('Y-m-d H:i:s', $fechaHoy->format('Y-m-d') . ' 00:00:00');
		$fechaHasta = \DateTime::createFromFormat('Y-m-d H:i:s', $fechaHoy->format('Y-m-d') . ' 23:59:59');
		
		$presupuestoEjecucion = $emWareHouse->getRepository('ADIFWarehouseBundle:PresupuestoEjecucion')
									->createQueryBuilder('pe')
									->where('pe.fechaCreacion BETWEEN :fechaDesde AND :fechaHasta')
									->setParameter(':fechaDesde', $fechaDesde)
									->setParameter(':fechaHasta', $fechaHasta)
									->getQuery()
									//->getSql();
									->getResult();
									
		//var_dump($fechaHoy->format('Y-m-d'), $fechaDesde, $fechaHasta);
		//\Doctrine\Common\Util\Debug::dump( $presupuestoEjecucion ); exit; 
		
		$totalesPresupuestarios = array();
		foreach($presupuestoEjecucion as $item) {
			
			$totalesPresupuestarios[ $item->getCodigoCuentaPresupuestariaEconomica() ] = array(
                'denominacion' => $item->getDenominacionCuentaPresupuestariaEconomica(),
				'montoActual' => $item->getMontoActual(),
                'provisorio' => $item->getProvisorio(),
                'definitivo' => $item->getDefinitivo(),
                'devengado' => $item->getDevengado(),
                'ejecutado' => $item->getEjecutado(),
				'saldo' => $item->getSaldo()
            );
		}
		
		//var_dump($totalesPresupuestarios);exit;
		
        $bread = $this->base_breadcrumbs;
		if (!$esShow) {
			$bread['Presupuesto ' . $presupuesto->getEjercicioContable()] = $this->generateUrl('presupuesto_show', ['id' => $id]);
			$bread['Tabla de ejecuci&oacute;n presupuestaria'] = null;
		} else {
			$bread['Presupuesto ' . $presupuesto->getEjercicioContable()] = null;
		}

        return array(
            'entity' => $presupuesto,
            'cuentasPresupuestariasCorrienteSuma' => $cuentasPresupuestariasCorrienteSuma,
            'cuentasPresupuestariasCorrienteResta' => $cuentasPresupuestariasCorrienteResta,
            'cuentasPresupuestariasCapitalSuma' => $cuentasPresupuestariasCapitalSuma,
            'cuentasPresupuestariasCapitalResta' => $cuentasPresupuestariasCapitalResta,
            'cuentasPresupuestariasFinanciamientoSuma' => $cuentasPresupuestariasFinanciamientoSuma,
            'cuentasPresupuestariasFinanciamientoResta' => $cuentasPresupuestariasFinanciamientoResta,
            'totalesPresupuestarios' => $totalesPresupuestarios,
            'breadcrumbs' => $bread,
            'page_title' => !$esShow ? 'Tabla de ejecuci&oacute;n presupuestaria' : 'Ver presupuesto'
        );
    }

    /**
     * Muestra la pantalla de EPE 1.
     *
     * @Route("/epe_1/", name="presupuesto_epe_1")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_1.html.twig")
     */
    public function showEPE1Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 1'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 1'
        );
    }

    /**
     * @Route("/filtrar_epe_1/", name="presupuesto_filtrar_epe_1")
     */
    public function filtrarEPE1Action(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicio = $request->get('ejercicio');

        $mesInicio = $request->get('fechaInicio');

        $mesFin = $request->get('fechaFin');

        $fechaInicial = new DateTime($ejercicio . '-' . $mesInicio . '-01');

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha($fechaInicial);

        $presupuesto = $em->getRepository('ADIFContableBundle:Presupuesto')
                ->findOneByEjercicioContable($ejercicioContable);

        $movimientosPresupuestarios = [];

        if ($presupuesto) {

            foreach ($presupuesto->getCuentasPresupuestarias() as $cuentaPresupuestaria) {

                if ($cuentaPresupuestaria->getCuentaPresupuestariaEconomica() != null) {

                    $idCategoria = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getCategoriaCuentaPresupuestariaEconomica()->getId();

                    $cuentaSuma = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getSuma() ? 'suma' : 'resta';

                    if (!isset($movimientosPresupuestarios[$idCategoria])) {

                        $movimientosPresupuestarios[$idCategoria]['suma'] = array();
                        $movimientosPresupuestarios[$idCategoria]['resta'] = array();
                        $movimientosPresupuestarios[$idCategoria]['resta']['total'] = array();
                        $movimientosPresupuestarios[$idCategoria]['resta']['movimientos'] = array();
                        $movimientosPresupuestarios[$idCategoria]['resta']['total']['montoSectorPublico'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['resta']['total']['montoSectorPrivado'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['resta']['total']['montoEjecucionMes'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['resta']['total']['montoTotalAcumulado'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['suma']['total'] = array();
                        $movimientosPresupuestarios[$idCategoria]['suma']['movimientos'] = array();
                        $movimientosPresupuestarios[$idCategoria]['suma']['total']['montoSectorPublico'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['suma']['total']['montoSectorPrivado'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['suma']['total']['montoEjecucionMes'] = 0;
                        $movimientosPresupuestarios[$idCategoria]['suma']['total']['montoTotalAcumulado'] = 0;
                    }

//                    $montoSectorPublico = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
//                            ->getMontoSectorPublico($mesInicio, $mesFin, $ejercicio);
                    $montoSectorPublico = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            ->getMontoEjecucionMes($mesInicio, $mesFin, $ejercicio);
                    $montoSectorPrivado = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            ->getMontoSectorPrivado($mesInicio, $mesFin, $ejercicio);
                    $montoEjecucionMes = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            ->getMontoEjecucionMes($mesInicio, $mesFin, $ejercicio);
                    $montoTotalAcumulado = $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            ->getMontoTotalAcumulado($mesInicio, $mesFin, $ejercicio);

                    $movimientosPresupuestarios[$idCategoria][$cuentaSuma]['movimientos'][] = [
                        'id' => $cuentaPresupuestaria->getId(),
                        'codigo' => $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getCodigo(),
                        'denominacion' => $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getDenominacion(),
                        'esImputable' => $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getEsImputable() ? 1 : 0,
                        'nivel' => $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getNivel(),
                        'montoSectorPublico' => $montoSectorPublico,
                        'montoSectorPrivado' => $montoSectorPrivado,
                        'montoEjecucionMes' => $montoEjecucionMes,
                        'montoTotalAcumulado' => $montoTotalAcumulado
                    ];

                    if ($cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getCuentaPresupuestariaEconomicaPadre() == null) {
                        $movimientosPresupuestarios[$idCategoria][$cuentaSuma]['total']['montoSectorPublico'] += $montoSectorPublico;
                        $movimientosPresupuestarios[$idCategoria][$cuentaSuma]['total']['montoSectorPrivado'] += $montoSectorPrivado;
                        $movimientosPresupuestarios[$idCategoria][$cuentaSuma]['total']['montoEjecucionMes'] += $montoEjecucionMes;
                        $movimientosPresupuestarios[$idCategoria][$cuentaSuma]['total']['montoTotalAcumulado'] += $montoTotalAcumulado;
                    }
                }
            }

            $movimientosPresupuestarios['resuladoEconomico'] = array();
            $movimientosPresupuestarios['resuladoFinanciero'] = array();
            $movimientosPresupuestarios['totalAplicacionesFinancieras'] = array();

            $movimientosPresupuestarios['resuladoEconomico']['montoSectorPublico'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['suma']['total']['montoSectorPublico'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['resta']['total']['montoSectorPublico'];
            $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] = $movimientosPresupuestarios['resuladoEconomico']['montoSectorPublico'] + $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['suma']['total']['montoSectorPublico'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['resta']['total']['montoSectorPublico'];
            $movimientosPresupuestarios['resuladoEconomico']['montoSectorPrivado'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['suma']['total']['montoSectorPrivado'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['resta']['total']['montoSectorPrivado'];
            $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] = $movimientosPresupuestarios['resuladoEconomico']['montoSectorPrivado'] + $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['suma']['total']['montoSectorPrivado'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['resta']['total']['montoSectorPrivado'];
            $movimientosPresupuestarios['resuladoEconomico']['montoEjecucionMes'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['suma']['total']['montoEjecucionMes'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['resta']['total']['montoEjecucionMes'];
            $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] = $movimientosPresupuestarios['resuladoEconomico']['montoEjecucionMes'] + $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['suma']['total']['montoEjecucionMes'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['resta']['total']['montoEjecucionMes'];
            $movimientosPresupuestarios['resuladoEconomico']['montoTotalAcumulado'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['suma']['total']['montoTotalAcumulado'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CORRIENTE]['resta']['total']['montoTotalAcumulado'];
            $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] = $movimientosPresupuestarios['resuladoEconomico']['montoTotalAcumulado'] + $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['suma']['total']['montoTotalAcumulado'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__CAPITAL]['resta']['total']['montoTotalAcumulado'];

            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['movimientos'][] = [
                'id' => 0,
                'codigo' => '',
                'denominacion' => 'Superhavit',
                'esImputable' => 1,
                'nivel' => 1,
                'montoSectorPublico' => $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] : 0,
                'montoSectorPrivado' => $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] : 0,
                'montoEjecucionMes' => $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] : 0,
                'montoTotalAcumulado' => $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] : 0
            ];

            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['movimientos'][] = [
                'id' => 0,
                'codigo' => '',
                'denominacion' => 'Deficit',
                'esImputable' => 1,
                'nivel' => 1,
                'montoSectorPublico' => $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] * (-1),
                'montoSectorPrivado' => $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] * (-1),
                'montoEjecucionMes' => $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] * (-1),
                'montoTotalAcumulado' => $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] * (-1)
            ];

            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoSectorPublico'] += $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] : 0;
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoSectorPublico'] += $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPublico'] * (-1);
            $movimientosPresupuestarios['totalAplicacionesFinancieras']['montoSectorPublico'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoSectorPublico'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoSectorPublico'];
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoSectorPrivado'] += $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] : 0;
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoSectorPrivado'] += $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoSectorPrivado'] * (-1);
            $movimientosPresupuestarios['totalAplicacionesFinancieras']['montoSectorPrivado'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoSectorPrivado'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoSectorPrivado'];
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoEjecucionMes'] += $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] : 0;
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoEjecucionMes'] += $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoEjecucionMes'] * (-1);
            $movimientosPresupuestarios['totalAplicacionesFinancieras']['montoEjecucionMes'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoEjecucionMes'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoEjecucionMes'];
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoTotalAcumulado'] += $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] > 0 ? $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] : 0;
            $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoTotalAcumulado'] += $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] > 0 ? 0 : $movimientosPresupuestarios['resuladoFinanciero']['montoTotalAcumulado'] * (-1);
            $movimientosPresupuestarios['totalAplicacionesFinancieras']['montoTotalAcumulado'] = $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['suma']['total']['montoTotalAcumulado'] - $movimientosPresupuestarios[CategoriaCuentaPresupuestariaEconomica::__FINANCIAMIENTO]['resta']['total']['montoTotalAcumulado'];
        }

        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * Muestra la pantalla de EPE 2.
     *
     * @Route("/epe_2/", name="presupuesto_epe_2")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_2.html.twig")
     */
    public function showEPE2Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 2'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 2'
        );
    }

    /**
     * @Route("/filtrar_epe_2/", name="presupuesto_filtrar_epe_2")
     */
    public function filtrarEPE2Action(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicio = $request->get('ejercicio');

        $mesInicio = 1;

        $mesFin = $request->get('fechaFin');
        $mesFinDisponibilidades = ($mesFin == 1) ? 1 : $mesFin - 1;

        $movimientosPresupuestarios = [];


        $saldoDisponibilidades = 0;
        $conceptosPresupuestarioDisponibilidades = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDisponibilidades')->findAll();
        /* @var $conceptoPresupuestarioDisponibilidades \ADIF\ContableBundle\Entity\ConceptoPresupuestarioDisponibilidades */
        foreach ($conceptosPresupuestarioDisponibilidades as $conceptoPresupuestarioDisponibilidades) {
            foreach ($conceptoPresupuestarioDisponibilidades->getCuentasContables() as $cuentaContable) {
                $saldoDisponibilidades += $em->getRepository('ADIFContableBundle:CuentaContable')->getSaldoRangoEjercicio($cuentaContable->getId(), $mesInicio, $mesFinDisponibilidades, $ejercicio);
            }
        }

        $movimientosPresupuestarios[] = [
            'concepto' => 'SALDO INICIAL DE DISPONIBILIDADES',
            'montoSectorPublico' => $saldoDisponibilidades,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $saldoDisponibilidades,
            'montoTotalAcumulado' => $saldoDisponibilidades
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => 'INGRESOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- INGRESOS DE OPERACION (NETOS)',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- IMPUESTOS RETENIDOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $totalSectorPublico = $this->getTotalMovimientoMinisterial($em, array(ConstanteConceptoTransaccionMinisterial::CONCEPTO_GASTOS_DE_CAPITAL, ConstanteConceptoTransaccionMinisterial::CONCEPTO_SI_FER), $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- APORTES Y TRANSF. DEL SECTOR PUBLICO',
            'montoSectorPublico' => $totalSectorPublico,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $totalSectorPublico,
            'montoTotalAcumulado' => $totalSectorPublico
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- APORTES Y TRANSF. DEL SECTOR PRIVADO',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- VENTA DE BIENES DE USO Y ACT. INTANGIBLES',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- VENTA DE ACTIVOS FINANCIEROS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OBTENCION DE PRESTAMOS A CORTO PLAZO',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA NACIONAL',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA EXTRANJERA',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OBTENC. DE PREST. A MEDIANO Y LARGO PLAZO',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA NACIONAL',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA EXTRANJERA',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- INGRESOS DIFERIDOS (Anticipos)',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- RENTAS DE LA PROPIEDAD',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $totalOtrosIngresosCorrientes = $this->getTotalMovimientoMinisterial($em, array(ConstanteConceptoTransaccionMinisterial::CONCEPTO_GASTOS_CORRIENTES), $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OTROS INGRESOS CORRIENTES',
            'montoSectorPublico' => $totalOtrosIngresosCorrientes,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $totalOtrosIngresosCorrientes,
            'montoTotalAcumulado' => $totalOtrosIngresosCorrientes
        ];

        $cuentaContableDeudoresADIF = $em->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::CLIENTE);
        $saldoOtrosIngresos = $em->getRepository('ADIFContableBundle:CuentaContable')->getHaberRangoEjercicio($cuentaContableDeudoresADIF->getId(), $mesFin, $mesFin, $ejercicio, ConstanteConceptoAsientoContable::COBRANZAS);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OTROS INGRESOS',
            'montoSectorPublico' => $saldoOtrosIngresos,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $saldoOtrosIngresos,
            'montoTotalAcumulado' => $saldoOtrosIngresos
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => 'SALDO INICIAL + INGRESOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => 'EGRESOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $personal = $this->getTotalOP($em, 'OrdenPagoSueldo', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'OrdenPagoCargasSociales', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'OrdenPagoAnticipoSueldo', $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- PERSONAL',
            'montoSectorPublico' => $personal,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $personal,
            'montoTotalAcumulado' => $personal
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- BIENES Y SERVICIOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $bienesConsumo = $this->getTotalOP($em, 'OrdenPagoComprobante', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'OrdenPagoAnticipoProveedor', $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- BIENES DE CONSUMO',
            'montoSectorPublico' => $bienesConsumo,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $bienesConsumo,
            'montoTotalAcumulado' => $bienesConsumo
        ];

        $serviciosProfesionales = $this->getTotalOP($em, 'Consultoria\OrdenPagoConsultoria', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'OrdenPagoAnticipoContratoConsultoria', $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- SERVICIOS PROFESIONALES',
            'montoSectorPublico' => $serviciosProfesionales,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $serviciosProfesionales,
            'montoTotalAcumulado' => $serviciosProfesionales
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- IMPUESTOS INDIRECTOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $impuestosDirectos = $this->getTotalOP($em, 'OrdenPagoDeclaracionJurada', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'OrdenPagoCargasSociales', $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- IMPUESTOS DIRECTOS',
            'montoSectorPublico' => $impuestosDirectos,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $impuestosDirectos,
            'montoTotalAcumulado' => $impuestosDirectos
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- INTERESES',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA NACIONAL',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA EXTRANJERA',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OTRAS RENTAS DE LA PROPIEDAD',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- DEPOSITO DE IMPUESTOS RETENIDOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $otrosGastosCorrientes = $this->getTotalOP($em, 'EgresoValor\OrdenPagoEgresoValor', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'EgresoValor\OrdenPagoReconocimientoEgresoValor', $ejercicio, $mesFin, $mesFin) + $this->getTotalOP($em, 'EgresoValor\OrdenPagoReconocimientoEgresoValor', $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OTROS GASTOS CORRIENTES',
            'montoSectorPublico' => $otrosGastosCorrientes,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $otrosGastosCorrientes,
            'montoTotalAcumulado' => $otrosGastosCorrientes
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- OTROS GASTOS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- TRANSFERENCIAS CORRIENTES',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        $inversionUso = $this->getTotalOP($em, 'Obras\OrdenPagoObra', $ejercicio, $mesFin, $mesFin);

        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- INVERSION EN BS. DE USO Y ACT. INTANGIBLES',
            'montoSectorPublico' => $inversionUso,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => $inversionUso,
            'montoTotalAcumulado' => $inversionUso
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- TRANSFERENCIAS DE CAPITAL',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- AMORTIZACION DE PRESTAMOS A CORTO PLAZO',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA NACIONAL',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA EXTRANJERA',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- AMORTIZACION DE PRESTAMOS A MEDIANO Y',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;  LARGO PLAZO (PORCION CIRCULANTE)',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA NACIONAL',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;&nbsp;&nbsp;- EN MONEDA EXTRANJERA',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => '&nbsp;&nbsp;- INVERSION EN ACTIVOS FINANCIEROS',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];
        $movimientosPresupuestarios[] = [
            'concepto' => 'SALDO FINAL DE DISPONIBILIDADES',
            'montoSectorPublico' => 0,
            'montoSectorPrivado' => 0,
            'montoEjecucionMes' => 0,
            'montoTotalAcumulado' => 0
        ];

        //actualizo ingresos
        $movimientosPresupuestarios[1]['montoSectorPublico'] = $movimientosPresupuestarios[4]['montoSectorPublico'] + $movimientosPresupuestarios[16]['montoSectorPublico'] + $movimientosPresupuestarios[17]['montoSectorPublico'];
        $movimientosPresupuestarios[1]['montoEjecucionMes'] = $movimientosPresupuestarios[1]['montoSectorPublico'];
        $movimientosPresupuestarios[1]['montoTotalAcumulado'] = $movimientosPresupuestarios[1]['montoSectorPublico'];

        //actualizo saldo inicial + ingresos
        $movimientosPresupuestarios[18]['montoSectorPublico'] = $movimientosPresupuestarios[0]['montoSectorPublico'] + $movimientosPresupuestarios[1]['montoSectorPublico'];
        $movimientosPresupuestarios[18]['montoEjecucionMes'] = $movimientosPresupuestarios[18]['montoSectorPublico'];
        $movimientosPresupuestarios[18]['montoTotalAcumulado'] = $movimientosPresupuestarios[18]['montoSectorPublico'];

        //actualizo bienes y servicios
        $movimientosPresupuestarios[21]['montoSectorPublico'] = $movimientosPresupuestarios[22]['montoSectorPublico'] + $movimientosPresupuestarios[23]['montoSectorPublico'];
        $movimientosPresupuestarios[21]['montoEjecucionMes'] = $movimientosPresupuestarios[21]['montoSectorPublico'];
        $movimientosPresupuestarios[21]['montoTotalAcumulado'] = $movimientosPresupuestarios[21]['montoSectorPublico'];

        //actualizo egresos
        $movimientosPresupuestarios[19]['montoSectorPublico'] = $movimientosPresupuestarios[20]['montoSectorPublico'] + $movimientosPresupuestarios[21]['montoSectorPublico'] + $movimientosPresupuestarios[25]['montoSectorPublico'] + $movimientosPresupuestarios[31]['montoSectorPublico'] + $movimientosPresupuestarios[34]['montoSectorPublico'];
        $movimientosPresupuestarios[19]['montoEjecucionMes'] = $movimientosPresupuestarios[19]['montoSectorPublico'];
        $movimientosPresupuestarios[19]['montoTotalAcumulado'] = $movimientosPresupuestarios[19]['montoSectorPublico'];

        //actualizo total
        $movimientosPresupuestarios[44]['montoSectorPublico'] = $movimientosPresupuestarios[18]['montoSectorPublico'] - $movimientosPresupuestarios[19]['montoSectorPublico'];
        $movimientosPresupuestarios[44]['montoEjecucionMes'] = $movimientosPresupuestarios[44]['montoSectorPublico'];
        $movimientosPresupuestarios[44]['montoTotalAcumulado'] = $movimientosPresupuestarios[44]['montoSectorPublico'];

        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * Muestra la pantalla de EPE 3.
     *
     * @Route("/epe_3/", name="presupuesto_epe_3")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_3.html.twig")
     */
    public function showEPE3Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 3'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 3'
        );
    }

    /**
     * @Route("/filtrar_epe_3/", name="presupuesto_filtrar_epe_3")
     */
    public function filtrarEPE3Action(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicio = $request->get('ejercicio');

        $mesInicio = $request->get('fechaInicio');

        $mesFin = $request->get('fechaFin');

        $movimientosPresupuestarios = [];

        $tiposConceptoPresupuestarioRemuneracion = $em->getRepository('ADIFContableBundle:TipoConceptoPresupuestarioRemuneracion')->findAll();

        /* @var $tipoConceptoPresupuestarioRemuneracion TipoConceptoPresupuestarioRemuneracion */
        foreach ($tiposConceptoPresupuestarioRemuneracion as $tipoConceptoPresupuestarioRemuneracion) {
            $conceptosPresupuestarioRemuneracion = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioRemuneracion')->findByTipoConceptoPresupuestarioRemuneracion($tipoConceptoPresupuestarioRemuneracion);
            if (!isset($movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getDenominacion()])) {
                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['denominacion'] = $tipoConceptoPresupuestarioRemuneracion->getDenominacion();
                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['conceptos'] = array();
                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['financiamiento'] = 0;
                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['ejecucion'] = 0;
            }
            foreach ($conceptosPresupuestarioRemuneracion as $conceptoPresupuestarioRemuneracion) {
                /* @var $conceptoPresupuestarioRemuneracion ConceptoPresupuestarioRemuneracion */

                $montoFinanciamiento = 0;
                $montoEjecucion = 0;

                foreach ($conceptoPresupuestarioRemuneracion->getCuentasContables() as $cuentaContable) {
                    /* @var $cuentaContable CuentaContable */
                    $montoFinanciamiento += $em->getRepository('ADIFContableBundle:Devengado')->getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $ejercicio);
                    $montoEjecucion += $em->getRepository('ADIFContableBundle:Ejecutado')->getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $ejercicio);
                    $montoFinanciamiento += $montoEjecucion;
                }

                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['conceptos'][] = [
                    'denominacion' => $conceptoPresupuestarioRemuneracion->getDenominacionConceptoPresupuestarioRemuneracion(),
                    'financiamiento' => $montoFinanciamiento,
                    'ejecucion' => $montoEjecucion
                ];

                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['financiamiento'] += $montoFinanciamiento;
                $movimientosPresupuestarios[$tipoConceptoPresupuestarioRemuneracion->getId()]['ejecucion'] += $montoEjecucion;
            }
        }

        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * Muestra la pantalla de EPE 4.
     *
     * @Route("/epe_4/", name="presupuesto_epe_4")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_4.html.twig")
     */
    public function showEPE4Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 4'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 4'
        );
    }

    /**
     * Muestra la pantalla de EPE 5.
     *
     * @Route("/epe_5/", name="presupuesto_epe_5")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_5.html.twig")
     */
    public function showEPE5Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 5'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 5'
        );
    }

    /**
     * @Route("/filtrar_epe_5/", name="presupuesto_filtrar_epe_5")
     */
    public function filtrarEPE5Action(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicio = $request->get('ejercicio');

        $mesInicio = $request->get('fechaInicio');

        $mesFin = $request->get('fechaFin');

        $movimientosPresupuestarios = [];

        $conceptosPresupuestarioServiciosNoPersonales = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioServiciosNoPersonales')->findAll();

        /* @var $conceptoPresupuestarioServiciosNoPersonales ConceptoPresupuestarioServiciosNoPersonales */
        foreach ($conceptosPresupuestarioServiciosNoPersonales as $conceptoPresupuestarioServiciosNoPersonales) {

            $montoEjecucion = 0;
            $montoAcumulado = 0;

            foreach ($conceptoPresupuestarioServiciosNoPersonales->getCuentasContables() as $cuentaContable) {
                /* @var $cuentaContable CuentaContable */
                $montoEjecucion += $em->getRepository('ADIFContableBundle:Devengado')->getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $ejercicio);
                $montoEjecucion += $em->getRepository('ADIFContableBundle:Ejecutado')->getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $ejercicio);

                $montoAcumulado += $em->getRepository('ADIFContableBundle:Devengado')->getMontoByCuentaContableAnioYRango($cuentaContable, 1, (new DateTime())->format('m'), $ejercicio);
                $montoAcumulado += $em->getRepository('ADIFContableBundle:Ejecutado')->getMontoByCuentaContableAnioYRango($cuentaContable, 1, (new DateTime())->format('m'), $ejercicio);
            }

            $movimientosPresupuestarios[] = [
                'denominacion' => $conceptoPresupuestarioServiciosNoPersonales->getDenominacionConceptoPresupuestarioServiciosNoPersonales(),
                'ejecucion' => $montoEjecucion,
                'acumulado' => $montoAcumulado
            ];
        }

        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * Muestra la pantalla de EPE 6.
     *
     * @Route("/epe_6/", name="presupuesto_epe_6")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_6.html.twig")
     */
    public function showEPE6Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 6'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 6'
        );
    }

    /**
     * Muestra la pantalla de EPE 7.
     *
     * @Route("/epe_7/", name="presupuesto_epe_7")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_7.html.twig")
     */
    public function showEPE7Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 7'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 7'
        );
    }

    /**
     * @Route("/filtrar_epe_7/", name="presupuesto_filtrar_epe_7")
     */
    public function filtrarEPE7Action(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicio = $request->get('ejercicio');

        $mesInicio = $request->get('fechaInicio');

        $mesFin = $request->get('fechaFin');

        $movimientosPresupuestarios = [];

        $conceptosPresupuestarioNivelVentas = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioNivelVentas')->findAll();

        /* @var $conceptoPresupuestarioNivelVentas ConceptoPresupuestarioNivelVentas */
        foreach ($conceptosPresupuestarioNivelVentas as $conceptoPresupuestarioNivelVentas) {

            $montoAcumulado = 0;

            foreach ($conceptoPresupuestarioNivelVentas->getCuentasContables() as $cuentaContable) {
                /* @var $cuentaContable CuentaContable */
                $montoAcumulado += $em->getRepository('ADIFContableBundle:Devengado')->getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $ejercicio);
                $montoAcumulado += $em->getRepository('ADIFContableBundle:Ejecutado')->getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $ejercicio);
            }

            $movimientosPresupuestarios[] = [
                'denominacion' => $conceptoPresupuestarioNivelVentas->getDenominacion(),
                'unidad' => 'Pesos',
                'acumulado' => $montoAcumulado
            ];
        }

        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * Muestra la pantalla de EPE 8.
     *
     * @Route("/epe_8/", name="presupuesto_epe_8")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:reporte.epe_8.html.twig")
     */
    public function showEPE8Action() {

        $bread = $this->base_breadcrumbs;
        $bread['EPE 8'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EPE 8'
        );
    }

    /**
     * @Route("/filtrar_epe_8/", name="presupuesto_filtrar_epe_8")
     */
    public function filtrarEPE8Action(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $ejercicio = $request->get('ejercicio');

        $mesInicio = $request->get('fechaInicio');

        $mesFin = $request->get('fechaFin');

        $movimientosPresupuestarios = [];
        $movimientosPresupuestarios['dotacion'] = [];
        $movimientosPresupuestarios['movimientos'] = [];

        $conceptosPresupuestarioDotacionPersonal = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDotacionPersonal')->findAll();

        $tipos = array('PERMANENTE', 'TEMPORARIA');


        foreach ($tipos as $tipo) {
            $movimientosPresupuestarios['dotacion'][$tipo] = array();
            $movimientosPresupuestarios['dotacion'][$tipo]['nombre'] = $tipo;
            $movimientosPresupuestarios['dotacion'][$tipo]['total'] = 0;
            $movimientosPresupuestarios['dotacion'][$tipo]['conceptos'] = array();

            foreach ($conceptosPresupuestarioDotacionPersonal as $conceptoPresupuestarioDotacionPersonal) {
                $categoriasRepositorio = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Categoria')->getCategoriasByIds(explode(',', $conceptoPresupuestarioDotacionPersonal->getCategorias()));

                $total = 0;

                /* @var $categoria \ADIF\RecursosHumanosBundle\Entity\Categoria */
                foreach ($categoriasRepositorio as $categoria) {

                    if ($tipo == 'PERMANENTE') {

                        $query = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Empleado')
                                ->createQueryBuilder('e')
                                ->select('COUNT(e) as total')
                                ->innerJoin('e.idSubcategoria', 'sc')
                                ->where('sc.idCategoria = :categoria')
                                ->andWhere('e.fechaBaja IS NULL')
                                ->setParameter('categoria', $categoria)
                                ->getQuery()
                                ->getResult();

                        if (count($query) == 0) {
                            $valor = 0;
                        } else {
                            $valor = $query[0]['total'];
                        }
                    } else {
                        $valor = 0;
                    }

                    $total += $valor;
                }

                $movimientosPresupuestarios['dotacion'][$tipo]['conceptos'][] = [
                    'nombre' => $conceptoPresupuestarioDotacionPersonal->getDenominacion(),
                    'total' => $total,
                ];
                $movimientosPresupuestarios['dotacion'][$tipo]['total'] += $total;
            }
        }

        $movimientosPresupuestarios['movimientos']['bajas'] = [];
        $totalBajas = 0;

        //Motivo renuncia
        $movimientosPresupuestarios['movimientos']['bajas']['RETIRO VOLUNTARIO'] = $this->cantidadBajas($emRRHH, $ejercicio, $mesInicio, $mesFin, array(ConstanteMotivoEgreso::RENUNCIA));
        $totalBajas += $movimientosPresupuestarios['movimientos']['bajas']['RETIRO VOLUNTARIO'];

        //Motivo despido justificado/injustificado
        $movimientosPresupuestarios['movimientos']['bajas']['DESPIDOS'] = $this->cantidadBajas($emRRHH, $ejercicio, $mesInicio, $mesFin, array(ConstanteMotivoEgreso::DESPIDO_INJUSTIFICADO, ConstanteMotivoEgreso::DESPIDO_JUSTIFICADO));
        $totalBajas += $movimientosPresupuestarios['movimientos']['bajas']['DESPIDOS'];

        //sin datos
        $movimientosPresupuestarios['movimientos']['bajas']['CONCESI&Oacute;N'] = 0;
        $movimientosPresupuestarios['movimientos']['bajas']['PROVINCIALIZACI&Oacute;N'] = 0;
        $movimientosPresupuestarios['movimientos']['bajas']['BAJAS VEGETATIVAS'] = 0;

        //Motivo fin contrato
        $movimientosPresupuestarios['movimientos']['bajas']['OTROS'] = $this->cantidadBajas($emRRHH, $ejercicio, $mesInicio, $mesFin, array(ConstanteMotivoEgreso::FIN_CONTRATO));
        $totalBajas += $movimientosPresupuestarios['movimientos']['bajas']['OTROS'];

        $movimientosPresupuestarios['movimientos']['totalBajas'] = $totalBajas;
        //fecha ingreso primer contrato
        $movimientosPresupuestarios['movimientos']['altas'] = $this->cantidadAltas($emRRHH, $ejercicio, $mesInicio, $mesFin);


        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * Print ProvisorioCompra.
     *
     * @Route("/print/provisorio_compra/{idRequerimiento}", name="presupuesto_print_provisorio_compra")
     * @Method("GET")
     * @Template("")
     */
    public function printProvisorioCompraAction($idRequerimiento) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $detalleCuentasPresupuestarias = array();

        $fecha = null;

        /* @var $requerimiento Requerimiento */
        $requerimiento = $emCompras->getRepository('ADIFComprasBundle:Requerimiento')
                ->find($idRequerimiento);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequerimiento) {

            $provisorioCompra = $emContable->getRepository('ADIFContableBundle:ProvisorioCompra')
                    ->findOneByIdRenglonRequerimiento($renglonRequerimiento->getId());

            if ($provisorioCompra != null) {

                $fecha = $provisorioCompra->getFechaProvisorio();

                $this->addCuentaPresupuestariaEconomica($provisorioCompra->getCuentaPresupuestariaEconomica(), $renglonRequerimiento->getBienEconomico(), $renglonRequerimiento->getJustiprecioTotal(), $detalleCuentasPresupuestarias);
            }
        }

        $titulo = 'COMPROMISO PROVISORIO - Requerimiento n&deg; ' . $requerimiento->getNumero();

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView(
                'ADIFContableBundle:Presupuesto:print.asiento_presupuestario.html.twig', [
            'detalleCuentasPresupuestarias' => $detalleCuentasPresupuestarias,
            'fecha' => $fecha,
            'titulo' => $titulo
                ]
        );
        $html .= '</body></html>';

        $filename = 'provisorio_requerimiento_' . $requerimiento->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * Print DefinitivoCompra.
     *
     * @Route("/print/definitivo_compra/{idOrdenCompra}", name="presupuesto_print_definitivo_compra")
     * @Method("GET")
     * @Template("")
     */
    public function printDefinitivoCompraAction($idOrdenCompra) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $detalleCuentasPresupuestarias = array();

        $fecha = null;

        /* @var $ordenCompra OrdenCompra */
        $ordenCompra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')
                ->find($idOrdenCompra);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        foreach ($ordenCompra->getRenglones() as $renglonOrdenCompra) {

            $definitivoCompra = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
                    ->findOneByIdRenglonOrdenCompra($renglonOrdenCompra->getId());

            if ($definitivoCompra != null) {

                $fecha = $definitivoCompra->getFechaDefinitivo();

                $this->addCuentaPresupuestariaEconomica($definitivoCompra->getCuentaPresupuestariaEconomica(), $renglonOrdenCompra->getBienEconomico(), $renglonOrdenCompra->getPrecioTotalProrrateado(), $detalleCuentasPresupuestarias);
            }
        }

        $titulo = 'COMPROMISO DEFINITIVO - Orden de compra n&deg; ' . $ordenCompra->getNumero();

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView(
                'ADIFContableBundle:Presupuesto:print.asiento_presupuestario.html.twig', [
            'oc' => $ordenCompra,
            'proveedor' => $ordenCompra->getProveedor(),
            'detalleCuentasPresupuestarias' => $detalleCuentasPresupuestarias,
            'fecha' => $fecha,
            'titulo' => $titulo
                ]
        );
        $html .= '</body></html>';

        $filename = 'definitivo_ordenCompra_' . $ordenCompra->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * Print DevengadoCompra.
     *
     * @Route("/print/devengado_compra/{idComprobanteCompra}", name="presupuesto_print_devengado_compra")
     * @Method("GET")
     * @Template("")
     */
    public function printDevengadoCompraAction($idComprobanteCompra) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $detalleCuentasPresupuestarias = array();

        $fecha = null;

        /* @var $comprobanteCompra \ADIF\ContableBundle\Entity\ComprobanteCompra */
        $comprobanteCompra = $emContable->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->find($idComprobanteCompra);

        if (!$comprobanteCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $ordenCompra = $comprobanteCompra->getOrdenCompra();

        foreach ($comprobanteCompra->getRenglonesComprobante() as $renglonComprobanteCompra) {

            $devengadoCompra = $emContable->getRepository('ADIFContableBundle:DevengadoCompra')
                    ->findOneByRenglonComprobanteCompra($renglonComprobanteCompra);

            if ($devengadoCompra != null) {

                $fecha = $devengadoCompra->getFechaDevengado();

                $this->addCuentaPresupuestariaEconomica($devengadoCompra->getCuentaPresupuestariaEconomica(), $renglonComprobanteCompra->getBienEconomico(), $renglonComprobanteCompra->getPrecioNetoTotalProrrateado(true), $detalleCuentasPresupuestarias);
            }
        }

        $titulo = 'DEVENGADO';

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView(
                'ADIFContableBundle:Presupuesto:print.asiento_presupuestario.html.twig', [
            'oc' => $ordenCompra,
            'proveedor' => $ordenCompra->getProveedor(),
            'comprobante' => $comprobanteCompra,
            'detalleCuentasPresupuestarias' => $detalleCuentasPresupuestarias,
            'fecha' => $fecha,
            'titulo' => $titulo
                ]
        );
        $html .= '</body></html>';

        $filename = 'devengado_ordenCompra_' . $ordenCompra->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * 
     * @param type $cuentaPresupuestariaEconomica
     * @param type $bienEconomico
     * @param type $detalleCuentasPresupuestarias
     */
    private function addCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica, $bienEconomico, $importe, &$detalleCuentasPresupuestarias) {

        if (!isset($detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()])) {

            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()] = array();
            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['denominacionCuentaEconomica'] = $cuentaPresupuestariaEconomica->__toString();
            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['total'] = 0;
            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['bienesEconomicos'] = array();
        }

        if (!isset($detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['bienesEconomicos'][$bienEconomico->getId()])) {

            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['bienesEconomicos'][$bienEconomico->getId()] = array();

            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['bienesEconomicos'][$bienEconomico->getId()]['denominacionBienEconomico'] = $bienEconomico->getDenominacionBienEconomico();
            $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['bienesEconomicos'][$bienEconomico->getId()]['total'] = 0;
        }

        $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['total'] += $importe;

        $detalleCuentasPresupuestarias[$cuentaPresupuestariaEconomica->getId()]['bienesEconomicos'][$bienEconomico->getId()]['total'] += $importe;
    }

    /**
     * 
     * @param type $emRRHH
     * @param type $mesInicio
     * @param type $mesFin
     * @param type $motivos
     * @return int
     */
    private function cantidadBajas($emRRHH, $ejercicio, $mesInicio, $mesFin, $motivos) {
        $query = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->select('COUNT(e) as total')
                ->innerJoin('e.motivoEgreso', 'me')
                ->where('(e.fechaEgreso IS NOT NULL) AND ((YEAR(e.fechaEgreso) = :anio) AND (MONTH(e.fechaEgreso) BETWEEN :mesInicio AND :mesFin))')
                ->andWhere('me.id in(:motivos)')
                ->setParameter('anio', $ejercicio)
                ->setParameter('mesFin', $mesFin)
                ->setParameter('mesInicio', $mesInicio)
                ->setParameter('motivos', $motivos)
                ->getQuery()
                ->getResult();

        if (count($query) == 0) {
            return 0;
        } else {
            return $query[0]['total'];
        }
    }

    private function cantidadAltas($emRRHH, $ejercicio, $mesInicio, $mesFin) {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('total', 'total');

        $query = $emRRHH->createNativeQuery("
            SELECT
                    COUNT(*) as total
            FROM
                    empleado e
            INNER JOIN (
                    SELECT
                            etc.id_empleado,
                            etc.fecha_desde
                    FROM
                            empleado_tipo_contrato etc
                    WHERE
                            etc.fecha_baja IS NULL
                    AND (
                            (YEAR(etc.fecha_desde) = " . $ejercicio . ")
                            AND (
                                    (
                                            MONTH (etc.fecha_desde) BETWEEN " . $mesInicio . "
                                            AND " . $mesFin . "
                                    )
                            )
                    )
                    GROUP BY
                            etc.id_empleado
                    ORDER BY
                            etc.fecha_desde ASC
            ) AS contratos_empleado ON e.id = contratos_empleado.id_empleado
            WHERE
                    e.fecha_baja IS NULL", $rsm);
        $total = $query->getResult();

        if (count($total) == 0) {
            return 0;
        } else {
            return $total[0]['total'];
        }
    }

    function getTotalOP($em, $tipoOP, $ejercicio, $mesInicio, $mesFin) {

        $total = 0;

        $repository = $em->getRepository('ADIFContableBundle:' . $tipoOP);

        $query = $repository->createQueryBuilder('op')
                ->innerJoin('op.estadoOrdenPago', 'eop')
                ->where('eop.denominacionEstado = :denominacion')
                ->andWhere('MONTH(op.fechaContable) between :mesInicio and :mesFin')
                ->andWhere('YEAR(op.fechaContable) = :ejercicio')
                ->setParameter('denominacion', ConstanteEstadoOrdenPago::ESTADO_PAGADA)
                ->setParameter('mesInicio', $mesInicio)
                ->setParameter('mesFin', $mesFin)
                ->setParameter('ejercicio', $ejercicio)
        ;

        $ops = $query->getQuery()->getResult();

        /* @var $op \ADIF\ContableBundle\Entity\OrdenPago */
        foreach ($ops as $op) {
            $total += $op->getMontoNeto();
        }

        return $total;
    }

    function getTotalMovimientoMinisterial($em, $tiposMovimientoMinisterial, $ejercicio, $mesInicio, $mesFin) {

        $total = 0;

        $repository = $em->getRepository('ADIFContableBundle:MovimientoMinisterial');

        $query = $repository->createQueryBuilder('mm')
                ->innerJoin('mm.conceptoTransaccionMinisterial', 'cdmm')
                ->where('cdmm.denominacion in (:denominacion)')
                ->andWhere('MONTH(mm.fechaContable) between :mesInicio and :mesFin')
                ->andWhere('YEAR(mm.fechaContable) = :ejercicio')
                ->andWhere('mm.esIngreso = true')
                ->setParameter('denominacion', $tiposMovimientoMinisterial)
                ->setParameter('mesInicio', $mesInicio)
                ->setParameter('mesFin', $mesFin)
                ->setParameter('ejercicio', $ejercicio)
        ;

        $movimientos = $query->getQuery()->getResult();

        /* @var $movimiento \ADIF\ContableBundle\Entity\MovimientoMinisterial */
        foreach ($movimientos as $movimiento) {
            $total += $movimiento->getMonto();
        }

        return $total;
    }

}
