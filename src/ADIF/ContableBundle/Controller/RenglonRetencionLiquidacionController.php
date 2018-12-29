<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto;
use ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion;
use ADIF\ContableBundle\Form\RenglonRetencionLiquidacionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * RenglonRetencionLiquidacion controller.
 *
 * @Route("/renglonesretencionliquidacion")
 */
class RenglonRetencionLiquidacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'RenglonRetencionLiquidacion' => $this->generateUrl('renglonesretencionliquidacion')
        );
    }

    /**
     * Lists all RenglonRetencionLiquidacion entities.
     *
     * @Route("/", name="renglonesretencionliquidacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['RenglonRetencionLiquidacion'] = null;

        return array(
            'breadcrumbs' => $bread,
            'historico' => false,
            'page_title' => 'RenglonRetencionLiquidacion',
            'page_info' => 'Lista de renglonretencionliquidacion'
        );
    }

    /**
     * Lists all RenglonRetencinLiquidacion entities.
     *
     * @Route("/index_table/", name="index_table")
     * @Method("GET|POST")
     * @Template()
     */
    public function indexTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $beneficiario = $request->query->get('beneficiario', '');
        $historico = $request->query->get('historico');
        
        // Obtengo los renglones de DDJJ 
        $renglonesRetencion = $em->getRepository('ADIFContableBundle:RenglonRetencionLiquidacion')->
                getRenglonesByBeneficiario($beneficiario, $historico);

        return $this->render('ADIFContableBundle:RenglonRetencionLiquidacion:index_table_retenciones.html.twig', array('entities' => $renglonesRetencion));
    }
    
    /**
     * @Route("/crear-ac/", name="renglon_retencion_liquidacion_crear_ac")
     * @Method("POST")     
     */
    public function crearAutorizacionContableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonesRetencionLiquidacionIds = json_decode($request->request->get('renglones_retenciones_ids'));
        
        $importeAutorizacionContable = 0;

        // Por cada renglon obtenido
        foreach ($renglonesRetencionLiquidacionIds as $idRenglonRetencionLiquidacion) {
            $renglonRetencionLiquidacion = $em->getRepository('ADIFContableBundle:RenglonRetencionLiquidacion')->find($idRenglonRetencionLiquidacion);
            
            /* @var $renglonRetencionLiquidacion RenglonRetencionLiquidacion */

            $renglonRetencionLiquidacion->setEstadoRenglonRetencionLiquidacion($em->getRepository('ADIFContableBundle:EstadoRenglonRetencionLiquidacion')->findOneByDenominacion(ConstanteEstadoRenglonRetencionLiquidacion::CON_PAGO));

            $importeAutorizacionContable += $renglonRetencionLiquidacion->getMonto();
        }

        // Si el importe de la AutorizacionContable fuese mayor a cero, la genero
        if ($importeAutorizacionContable > 0) {
            // Obtengo el servicio
            $ordenPagoService = $this->get('adif.orden_pago_service');

            $concepto = 'Retenciones liquidaci&oacute;n '.$renglonRetencionLiquidacion->getLiquidacion().' - '.$renglonRetencionLiquidacion->getBeneficiarioLiquidacion()->getRazonSocial();

            // Genero la AutorizacionContable   
            $ordenPagoService
                    ->crearAutorizacionContableRenglonesRetencionLiquidacion($em, $renglonesRetencionLiquidacionIds, $importeAutorizacionContable, $concepto);
        }

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se gener&oacute; la autorizaci&oacute;n contable con &eacute;xito.");

        return $this->redirect($this->generateUrl('renglonesretencionliquidacion'));
    }
    
    /**
     * Devuelve el template del detalle de un renglon
     *
     * @Route("/form_detalle/", name="renglonesretencionliquidacion_form_detalle")
     * @Method("POST")   
     * @Template("ADIFContableBundle:RenglonRetencionLiquidacion:index.show.conceptos.html.twig")
     */
    public function getFormDetalleRenglonRetencionLiquidacionAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $idRenglon = $request->request->get('id');

        /* @var $renglonRetencionLiquidacion RenglonRetencionLiquidacion */
        $renglonRetencionLiquidacion = $em->getRepository('ADIFContableBundle:RenglonRetencionLiquidacion')->find($idRenglon);
        
        $conceptos = $emRRHH->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleadoConcepto')
                ->createQueryBuilder('lec')
                ->innerJoin('lec.liquidacionEmpleado', 'le')
                ->innerJoin('le.liquidacion', 'l')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('e.persona', 'p')
                ->innerJoin('lec.conceptoVersion', 'cv')
                ->where('l.id = :idLiquidacion')
                    ->andWhere('cv.id = :idConceptoVersion')
                ->setParameters(array('idLiquidacion' => $renglonRetencionLiquidacion->getIdLiquidacion(), 'idConceptoVersion' => $renglonRetencionLiquidacion->getConceptoVersion()->getId()))
                ->orderBy('p.apellido, p.nombre')
                ->getQuery()
        //echo $conceptos->getSQL();die;
                ->getResult();
 
        return array(
            'entity' => $renglonRetencionLiquidacion,
            'conceptos' => $conceptos
        );
    }
    
    /**
     * Lists all RenglonRetencionLiquidacion entities.
     *
     * @Route("/historico/", name="renglonesretencionliquidacion_historico")
     * @Method("GET")
     * @Template("ADIFContableBundle:RenglonRetencionLiquidacion:index.html.twig")
     */
    public function historicoAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Hist&oacute;rico'] = null;

        return array(
            'breadcrumbs' => $bread,
            'historico' => true,
            'page_title' => 'Hist&oacute;rico de renglones de retenciones',
            'page_info' => 'Hist&oacute;rico de renglones de retenciones'
        );
    }

}
