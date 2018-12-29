<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica;
use ADIF\ContableBundle\Form\CuentaPresupuestariaEconomicaType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoAsientoPresupuestario;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * CuentaPresupuestariaEconomica controller.
 *
 * @Route("/cuentapresupuestariaeconomica")
 */
class CuentaPresupuestariaEconomicaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Cuentas presupuestarias económicas' => $this->generateUrl('cuentapresupuestariaeconomica')
        );
    }

    /**
     * Lists all CuentaPresupuestariaEconomica entities.
     *
     * @Route("/", name="cuentapresupuestariaeconomica")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cuentas presupuestarias económicas'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Cuenta presupuestaria económica',
            'page_info' => 'Lista de cuentas presupuestarias económicas'
        );
    }

    /**
     * Creates a new CuentaPresupuestariaEconomica entity.
     *
     * @Route("/insertar", name="cuentapresupuestariaeconomica_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:CuentaPresupuestariaEconomica:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CuentaPresupuestariaEconomica();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentapresupuestariaeconomica'));
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
            'page_title' => 'Crear cuenta presupuestaria económica',
        );
    }

    /**
     * Creates a form to create a CuentaPresupuestariaEconomica entity.
     *
     * @param CuentaPresupuestariaEconomica $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CuentaPresupuestariaEconomica $entity) {
        $form = $this->createForm(new CuentaPresupuestariaEconomicaType(), $entity, array(
            'action' => $this->generateUrl('cuentapresupuestariaeconomica_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CuentaPresupuestariaEconomica entity.
     *
     * @Route("/crear", name="cuentapresupuestariaeconomica_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CuentaPresupuestariaEconomica();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cuenta presupuestaria económica'
        );
    }

    /**
     * Finds and displays a CuentaPresupuestariaEconomica entity.
     *
     * @Route("/{id}", name="cuentapresupuestariaeconomica_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaEconomica.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver cuenta presupuestaria económica'
        );
    }

    /**
     * Displays a form to edit an existing CuentaPresupuestariaEconomica entity.
     *
     * @Route("/editar/{id}", name="cuentapresupuestariaeconomica_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:CuentaPresupuestariaEconomica:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaEconomica.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = $this->generateUrl('cuentapresupuestariaeconomica_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta presupuestaria económica'
        );
    }

    /**
     * Creates a form to edit a CuentaPresupuestariaEconomica entity.
     *
     * @param CuentaPresupuestariaEconomica $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CuentaPresupuestariaEconomica $entity) {
        $form = $this->createForm(new CuentaPresupuestariaEconomicaType(), $entity, array(
            'action' => $this->generateUrl('cuentapresupuestariaeconomica_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing CuentaPresupuestariaEconomica entity.
     *
     * @Route("/actualizar/{id}", name="cuentapresupuestariaeconomica_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:CuentaPresupuestariaEconomica:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaEconomica.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cuentapresupuestariaeconomica'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = $this->generateUrl('cuentapresupuestariaeconomica_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta presupuestaria económica'
        );
    }

    /**
     * Deletes a CuentaPresupuestariaEconomica entity.
     *
     * @Route("/borrar/{id}", name="cuentapresupuestariaeconomica_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * Recibe el $id de una CuentaPresupuestariaEconomica, y retorna el codigoInicial de la misma
     * 
     * @Route("/", name="cuentapresupuestariaeconomica_codigoinicial")
     * @Method("POST")
     */
    public function getCodigoInicialAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->get('id');

        $cuentaPresupuestariaEconomica = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->find($id);

        if (!$cuentaPresupuestariaEconomica) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaEconomica.');
        }

        $response = new Response();
        $response->setContent($cuentaPresupuestariaEconomica->getCodigoInicial());
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/tree/", name="cuentapresupuestariaeconomica_tree")
     * @Method("POST")
     */
    public function getCuentaPresupuestariaEconomicaTree(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->get("id");

        $data = array();

        if ($id == "#") {

            $cuentasPresupuestariasEconomicasRaiz = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                    ->getCuentasPresupuestariasEconomicasRaiz();

            foreach ($cuentasPresupuestariasEconomicasRaiz as $cuentaPresupuestariaEconomica) {
                $data[] = array(
                    "id" => $cuentaPresupuestariaEconomica->getId(),
                    "text" => $cuentaPresupuestariaEconomica->getCodigo() . " - " . $cuentaPresupuestariaEconomica->getDenominacion(),
                    "children" => !$cuentaPresupuestariaEconomica->getCuentasPresupuestariasEconomicasHijas()->isEmpty(),
                    "type" => "root"
                );
            }
        } else {

            $cuentasPresupuestariasEconomicasHijas = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                    ->getCuentasPresupuestariasEconomicasHijas($id);

            foreach ($cuentasPresupuestariasEconomicasHijas as $cuentaPresupuestariaEconomica) {
                $data[] = array(
                    "id" => $cuentaPresupuestariaEconomica->getId(),
                    "parent" => $cuentaPresupuestariaEconomica->getCuentaPresupuestariaEconomicaPadre()->getId(),
                    "text" => $cuentaPresupuestariaEconomica->getCodigo() . " - " . $cuentaPresupuestariaEconomica->getDenominacion(),
                    "children" => !$cuentaPresupuestariaEconomica->getCuentasPresupuestariasEconomicasHijas()->isEmpty(),
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
     * @Route("/search/", name="cuentapresupuestariaeconomica_search")
     * @Method("GET")
     */
    public function searchCuentaPresupuestariaEconomica(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $string = $request->get("str");

        $data = array();

        $cuentasPresupuestariasEconomicas = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                ->getCuentasPresupuestariasEconomicasByString($string);

        foreach ($cuentasPresupuestariasEconomicas as $cuentaPresupuestariaEconomica) {
            $data = array_unique(array_merge($data, $cuentaPresupuestariaEconomica->getCuentaPresupuestariaEconomicaPadreIds()));
        }

        header('Content-type: text/json');
        header('Content-type: application/json');

        return new JsonResponse($data);
    }

    /**
     * Tabla para CuentaPresupuestariaEconomica.
     *
     * @Route("/index_table/", name="cuentapresupuestariaeconomica_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentasPresupuestariasEconomicas = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                ->createQueryBuilder('cpe')
                ->select('partial cpe.{id, codigo, denominacion, esImputable }')
                ->orderBy('cpe.codigo', 'ASC')
                ->getQuery()
                ->getResult();

        return $this->render('ADIFContableBundle:CuentaPresupuestariaEconomica:index_table.html.twig', //
                        array('cuentasPresupuestariasEconomicas' => $cuentasPresupuestariasEconomicas)
        );
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/asientos_presupuestarios/", name="cuentapresupuestariaeconomica_asientos_presupuestarios")
     * @Method("POST")
     */
    public function getAsientosPresupuestarios(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->get("id_cuenta_presupuestaria_economica");

        $tipoAsientoPresupuestario = $request->get("tipo_asiento_presupuestario");

        $ejercicio = $request->get("ejercicio");

        $cuentaPresupuestariaEconomica = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->find($id);

        if (!$cuentaPresupuestariaEconomica) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaEconomica.');
        }


        $movimientosPresupuestarios = [];


        // PROVISORIOS
        if ($tipoAsientoPresupuestario == ConstanteTipoAsientoPresupuestario::PROVISORIO) {

            $provisorios = $em->getRepository('ADIFContableBundle:Provisorio')
                    ->getProvisoriosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio);

            // Por cada Provisorio
            foreach ($provisorios as $provisorio) {

                /* @var $provisorio \ADIF\ContableBundle\Entity\Provisorio */

                $movimientosPresupuestarios[] = [
                    'id' => $provisorio->getId(),
                    'fecha' => $provisorio->getFechaProvisorio()->format('d/m/Y'),
                    'monto' => $provisorio->getMonto(),
                    'numeroAsiento' => '-',
                    'pathShowAsientoContable' => '#',
                    'detalle' => $provisorio->getDetalle() != null ? $provisorio->getDetalle() : '-'
                ];
            }
        }


        // DEFINITIVOS
        if ($tipoAsientoPresupuestario == ConstanteTipoAsientoPresupuestario::DEFINITIVO) {

            $definitivos = $em->getRepository('ADIFContableBundle:Definitivo')
                    ->getDefinitivosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio);

            // Por cada Definitivo
            foreach ($definitivos as $definitivo) {

                if ($definitivo->getSaldo() != 0) {
                    /* @var $definitivo \ADIF\ContableBundle\Entity\Definitivo */

                    $movimientosPresupuestarios[] = [
                        'id' => $definitivo->getId(),
                        'fecha' => $definitivo->getFechaDefinitivo()->format('d/m/Y'),
                        'monto' => $definitivo->getSaldo(),
                        'numeroAsiento' => '-',
                        'pathShowAsientoContable' => '#',
                        'detalle' => $definitivo->getDetalle() != null ? $definitivo->getDetalle() : '-'
                    ];
                }
            }
        }


        // DEVENGADOS
        if ($tipoAsientoPresupuestario == ConstanteTipoAsientoPresupuestario::DEVENGADO) {

            $devengados = $em->getRepository('ADIFContableBundle:Devengado')
                    ->getDevengadosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio);

            // Por cada Devengado
            foreach ($devengados as $devengado) {

                /* @var $devengado \ADIF\ContableBundle\Entity\Devengado */

                $movimientosPresupuestarios[] = [
                    'id' => $devengado->getId(),
                    'fecha' => $devengado->getFechaDevengado()->format('d/m/Y'),
                    'monto' => $devengado->getMonto(),
                    'numeroAsiento' => $devengado->getAsientoContable() != null //
                            ? $devengado->getAsientoContable()->getNumeroAsiento() //
                            : '-',
                    'pathShowAsientoContable' => $devengado->getAsientoContable() != null //
                            ? $this->generateUrl('asientocontable_show', array('id' => $devengado->getAsientoContable()->getId())) //
                            : '#',
                    'detalle' => $devengado->getDetalle() != null ? $devengado->getDetalle() : '-'
                ];
            }
        }


        // EJECUTADOS
        if ($tipoAsientoPresupuestario == ConstanteTipoAsientoPresupuestario::EJECUTADO) {

            $ejecutados = $em->getRepository('ADIFContableBundle:Ejecutado')
                    ->getEjecutadosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio);

            // Por cada Ejecutado
            foreach ($ejecutados as $ejecutado) {

                /* @var $ejecutado \ADIF\ContableBundle\Entity\Ejecutado */

                $movimientosPresupuestarios[] = [
                    'id' => $ejecutado->getId(),
                    'fecha' => $ejecutado->getFechaEjecutado()->format('d/m/Y'),
                    'monto' => $ejecutado->getMonto(),
                    'numeroAsiento' => $ejecutado->getAsientoContable() != null //
                            ? $ejecutado->getAsientoContable()->getNumeroAsiento() //
                            : '-',
                    'pathShowAsientoContable' => $ejecutado->getAsientoContable() != null //
                            ? $this->generateUrl('asientocontable_show', array('id' => $ejecutado->getAsientoContable()->getId())) //
                            : '#',
                    'detalle' => $ejecutado->getDetalle() != null ? $ejecutado->getDetalle() : '-'
                ];
            }
        }

        return new JsonResponse($movimientosPresupuestarios);
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la cuenta presupuestaria econ&oacute;mica '
                . 'ya que es referenciada por otras entidades.';
    }

}
