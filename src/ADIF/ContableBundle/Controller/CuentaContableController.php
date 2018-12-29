<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\CuentaContable;
use ADIF\ContableBundle\Form\CuentaContableType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;

/**
 * CuentaContable controller.
 *
 * @Route("/cuentacontable")
 */
class CuentaContableController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    /**
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Plan de cuentas' => $this->generateUrl('cuentacontable')
        );
    }

    /**
     * Lists all CuentaContable entities.
     *
     * @Route("/", name="cuentacontable")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:CuentaContable')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Plan de cuentas'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Plan de cuentas',
            'page_info' => 'Lista de cuentas contables'
        );
    }

    /**
     * Creates a new CuentaContable entity.
     *
     * @Route("/insertar", name="cuentacontable_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:CuentaContable:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CuentaContable();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentacontable'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'mask' => $this->getMascaraPlanDeCuentas(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cuenta contable',
        );
    }

    /**
     * Creates a form to create a CuentaContable entity.
     *
     * @param CuentaContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CuentaContable $entity) {
        $form = $this->createForm(new CuentaContableType(), $entity, array(
            'action' => $this->generateUrl('cuentacontable_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CuentaContable entity.
     *
     * @Route("/crear", name="cuentacontable_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentaContable = new CuentaContable();

        $form = $this->createCreateForm($cuentaContable);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $cuentaContable,
            'mask' => $this->getMascaraPlanDeCuentas(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cuenta contable'
        );
    }

    /**
     * Finds and displays a CuentaContable entity.
     *
     * @Route("/{id}", name="cuentacontable_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaContable.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Cuenta Contable'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver cuenta contable'
        );
    }

    /**
     * Displays a form to edit an existing CuentaContable entity.
     *
     * @Route("/editar/{id}", name="cuentacontable_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:CuentaContable:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')->find($id);

        if (!$cuentaContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaContable.');
        }

        $editForm = $this->createEditForm($cuentaContable);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $cuentaContable,
            'mask' => $this->getMascaraPlanDeCuentas(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta contable'
        );
    }

    /**
     * Creates a form to edit a CuentaContable entity.
     *
     * @param CuentaContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CuentaContable $entity) {
        $form = $this->createForm(new CuentaContableType(), $entity, array(
            'action' => $this->generateUrl('cuentacontable_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing CuentaContable entity.
     *
     * @Route("/actualizar/{id}", name="cuentacontable_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:CuentaContable:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaContable.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cuentacontable'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'mask' => $this->getMascaraPlanDeCuentas(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta contable'
        );
    }

    /**
     * Deletes a CuentaContable entity.
     *
     * @Route("/borrar/{id}", name="cuentacontable_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:CuentaContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaContable.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('cuentacontable'));
    }

    /**
     * Recibe el $id de uan Cuenta Contable, y retorna el codigoInicial de la misma
     * 
     * @Route("/", name="cuentacontable_codigoinicial")
     * @Method("POST")
     */
    public function getCodigoInicialAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->get('id');

        $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')->find($id);

        if (!$cuentaContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaContable.');
        }

        $response = new Response();
        $response->setContent($cuentaContable->getCodigoInicial());
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/tree/", name="cuentacontable_tree")
     * @Method("POST")
     */
    public function getCuentaContableTree(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->get("id");

        $data = array();

        if ($id == "#") {

            $cuentasContablesRaiz = $em->getRepository('ADIFContableBundle:CuentaContable')
                    ->getCuentasContablesRaiz();

            foreach ($cuentasContablesRaiz as $cuentaContable) {
                $data[] = array(
                    "id" => $cuentaContable->getId(),
                    "text" => $cuentaContable->getCodigoCuentaContable() . " - " . $cuentaContable->getDenominacionCuentaContable(),
                    "children" => !$cuentaContable->getCuentasContablesHijas()->isEmpty(),
                    "type" => "root"
                );
            }
        } else {

            $cuentasContablesHijas = $em->getRepository('ADIFContableBundle:CuentaContable')
                    ->getCuentasContablesHijas($id);

            foreach ($cuentasContablesHijas as $cuentaContable) {
                $data[] = array(
                    "id" => $cuentaContable->getId(),
                    "parent" => $cuentaContable->getCuentaContablePadre()->getId(),
                    "text" => $cuentaContable->getCodigoCuentaContable() . " - " . $cuentaContable->getDenominacionCuentaContable(),
                    "children" => !$cuentaContable->getCuentasContablesHijas()->isEmpty(),
                );
            }
        }

        header('Content-type: text/json');
        header('Content-type: application/json');

        return new JsonResponse($data);
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/search/", name="cuentacontable_search")
     * @Method("GET")
     */
    public function searchCuentaContable(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $string = $request->get("str");

        $data = array();

        $cuentasContables = $em->getRepository('ADIFContableBundle:CuentaContable')
                ->getCuentasContablesByString($string);

        foreach ($cuentasContables as $cuentaContable) {
            $data = array_unique(array_merge($data, $cuentaContable->getCuentaContablePadreIds()));
        }

        header('Content-type: text/json');
        header('Content-type: application/json');

        return new JsonResponse($data);
    }

    /**
     * @Route("/lista", name="cuentacontable_lista")
     */
    public function listaCuentaContableAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:CuentaContable', $this->getEntityManager());

        $query = $repository->createQueryBuilder('cc')
                ->select('cc.id', 'cc.codigoCuentaContable', 'cc.denominacionCuentaContable')
                ->orderBy('cc.codigoCuentaContable', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

    /**
     * Tabla para CuentaContable.
     *
     * @Route("/index_table/", name="cuentacontable_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentasContables = $em->getRepository('ADIFContableBundle:CuentaContable')
                ->createQueryBuilder('cc')
                ->select('partial cc.{id, codigoCuentaContable, denominacionCuentaContable, esImputable, activa }')
                ->orderBy('cc.codigoCuentaContable', 'ASC')
                ->getQuery()
                ->getResult();

        return $this->render('ADIFContableBundle:CuentaContable:index_table.html.twig', //
                        array('cuentasContables' => $cuentasContables)
        );
    }

    /**
     * 
     * Obtengo la mascara del plan de cuentas
     * 
     * @return type
     */
    private function getMascaraPlanDeCuentas() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        // Obtengo el Plan de Cuentas
        $planDeCuentas = $em->getRepository('ADIFContableBundle:PlanDeCuentas')->
                findOneBy(array(), array('id' => 'desc'), 1, 0);

        $mask = "";

        // Si existe el Plan de Cuentas, seteo la máscara
        if (null != $planDeCuentas) {


            foreach ($planDeCuentas->getSegmentos() as $segmento) {
                $mask .= str_repeat('9', $segmento->getLongitud());
                $mask .= $segmento->getSeparador();
            }
        }
        return $mask;
    }
	
	/**
     * Insert masivo de cuentas a partir de una tabla de importacion 
     * gluis - 29/12/2016 
	 *
     * @Route("/bulk/", name="cuentacontable_bulk")
     * @Method("GET")
     * 
     */
	public function bulkAction(Request $request)
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$userId = $user->getId();
		$usuarios = array(157,195,220); // gluis, babaroa, aboggon
		if (!in_array($userId, $usuarios)) {
			throw $this->createAccessDeniedException('No tiene privilegios para entrar a esta página.');
			exit;
		}
		
		$logger = new Logger('bulk_cc');

        $monologFormat = "%message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

        $streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/bulk_cuentas_contables_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
        $streamHandler->setFormatter($monologLineFormat);

        $logger->pushHandler($streamHandler);
		
		$logger->info("------------------------------------------------------------------------------");
		$logger->info("------------------------------------------------------------------------------");
		$logger->info("Inicio bulk");
		$logger->info("------------------------------------------------------------------------------");
		
		// La idea es que el dia de mañana, si se quieren mater otra tabla se cambie esta variable y listo
		$tabla = 'importacion_cuentas_contables_2017';
		
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$rsm = new ResultSetMapping();

		$rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('codigo_centro_costo', 'codigo_centro_costo');
		$rsm->addScalarResult('denominacion_centro_costo', 'denominacion_centro_costo');
		$rsm->addScalarResult('codigo_cuenta_contable', 'codigo_cuenta_contable');
		$rsm->addScalarResult('codigo_cuenta_contable_padre', 'codigo_cuenta_contable_padre');
		$rsm->addScalarResult('denominacion_cuenta_contable_padre', 'denominacion_cuenta_contable_padre');
		$rsm->addScalarResult('codigo_cuenta_presupuestaria_economica', 'codigo_cuenta_presupuestaria_economica');
		$rsm->addScalarResult('codigo_cuenta_presupuestaria_objeto_gasto', 'codigo_cuenta_presupuestaria_objeto_gasto');
		$rsm->addScalarResult('denominacion_cuenta_contable', 'denominacion_cuenta_contable');
				
		$sql = "SELECT * FROM $tabla";

		$query = $em->createNativeQuery($sql, $rsm);
		$cuentasContablesNuevas = $query->getResult();
		
		//var_dump($cuentasContablesNuevas);exit;
		
		$em->getConnection()->beginTransaction();
		$id = '';
		try {
		
			$codigoInterno = 1000; // A partir del codigo interno 1000 son las cuentas del año 2017
			foreach($cuentasContablesNuevas as $cuentaContableNueva) {
				
				$id = $cuentaContableNueva['id'];
				
				//$denominacion = $cuentaContableNueva['denominacion_cuenta_contable_padre'] . ' - ' . $cuentaContableNueva['denominacion_centro_costo'];
				$denominacion = $cuentaContableNueva['denominacion_cuenta_contable'];
				
				//die($id . '  '. $denominacion);
				$nuevaCuentaContable = new CuentaContable();
				
				// Codigo
				$nuevaCuentaContable->setCodigoCuentaContable($cuentaContableNueva['codigo_cuenta_contable']);
				
				// Denominacion
				$nuevaCuentaContable->setDenominacionCuentaContable($denominacion);
				
				// Descripcion
				$nuevaCuentaContable->setDescripcionCuentaContable($denominacion);
				
				// Es imputable
				$nuevaCuentaContable->setEsImputable(true);
				
				// Cuenta contable padre
				$cuentaContablePadre = 
					$em->getRepository('ADIFContableBundle:CuentaContable')
						->findOneByCodigoCuentaContable($cuentaContableNueva['codigo_cuenta_contable_padre']);
						
				$nuevaCuentaContable->setCuentaContablePadre($cuentaContablePadre);
				
				// Cuenta presupuestaria economica
				$nuevaCuentaPresupuestariaEconomica = 
					$em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
						->findOneByCodigo($cuentaContableNueva['codigo_cuenta_presupuestaria_economica']);
						
				$nuevaCuentaContable->setCuentaPresupuestariaEconomica($nuevaCuentaPresupuestariaEconomica);
				
				// Cuenta presupuestaria objeto gasto 
				$nuevaCuentaPresupuestariaEconomicaObjetoGasto = 
					$em->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto')
						->findOneByCodigo($cuentaContableNueva['codigo_cuenta_presupuestaria_objeto_gasto']);
						
				$nuevaCuentaContable->setCuentaPresupuestariaObjetoGasto($nuevaCuentaPresupuestariaEconomicaObjetoGasto);
				
				// Tipo de moneda MCL (Moneda curso legal)
				$tipoMonedaMCL = $em->getRepository('ADIFContableBundle:TipoMoneda')->findOneByEsMCL(true);
				$nuevaCuentaContable->setTipoMoneda($tipoMonedaMCL);
				
				// Codigo interno
				$nuevaCuentaContable->setCodigoInterno($codigoInterno);
				$codigoInterno++;
				
				// Activa
				$nuevaCuentaContable->setActiva(true);
				
				$em->persist($nuevaCuentaContable);
				$em->flush();
				
				$logger->info("Se ha guardado la cuenta " . $cuentaContableNueva['codigo_cuenta_contable'] . ' - ' . $denominacion);
				$logger->info("------------------------------------------------------------------------------");
			}
			
			$logger->info("------------------------------------------------------------------------------");
			$logger->info("Fin bulk");
			
			$em->getConnection()->commit();
			
		} catch(\Exception $e) {
			
			$logger->info("No se ha podido guardado la cuenta " . $cuentaContableNueva['codigo_cuenta_contable'] . ' - ' . $denominacion);
			$logger->info("Hubo un error en el ID $id de la tabla de importacion. Se hace rollback");
			$logger->info("------------------------------------------------------------------------------");
			$logger->info("Fin bulk");
			
			$em->getConnection()->rollback();
            $em->close();
			throw $e;
		}
		
		return new Response('Termino bulk plan de cuentas del año 2017');
	}

}
