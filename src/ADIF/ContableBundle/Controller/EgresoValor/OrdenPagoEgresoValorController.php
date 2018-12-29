<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\ReposicionEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoReposicionEgresoValor;
use ADIF\BaseBundle\Session\EmpresaSession;


/**
 * OrdenPagoEgresoValor controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoEgresoValorController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoEgresoValor entity.
     *
     * @Route("/egresovalor/{id}", name="ordenpagoegresovalor_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/egresovalor/pagar", name="ordenpagoegresovalor_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPago entity.
     *
     * @Route("/egresovalor/print/{id}", name="ordenpagoegresovalor_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPago
     *
     * @Route("/egresovalor/reemplazar_pago", name="ordenpagoegresovalor_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/egresovalor/{id}/anular", name="ordenpagoegresovalor_anular")
     * @Method("GET")   
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {

        /* Genero el contraasiento */
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoEgresoValor($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoEgresoValor($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/egresovalor';
    }

    /**
     * 
     * @param type $ordenPagoPendientePago
     * @param type $emContable
     */
    public function pagarActionCustom($ordenPagoPendientePago, $emContable) {

        // Seteo el EstadoEgresoValor
        $ordenPagoPendientePago->getEgresoValor()->setEstadoEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_ACTIVO)
        );

        $reposicion = new ReposicionEgresoValor();
        $reposicion->setMonto($ordenPagoPendientePago->getTotalBruto());
        $reposicion->setResponsableEgresoValor($ordenPagoPendientePago
                        ->getEgresoValor()->getResponsableEgresoValor()
        );

        $reposicion->setEsCreacion($ordenPagoPendientePago->getEgresoValor()->getRendiciones()->isEmpty());
        $reposicion->setEstadoReposicionEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoReposicionEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoReposicionEgresoValor::ESTADO_PAGADA)
        );

        if (!$reposicion->getEsCreacion()) {
            $ordenPagoReposicion = $emContable->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')
                    ->getUltimaReposicionPaga($ordenPagoPendientePago->getEgresoValor());
            if ((!empty($ordenPagoReposicion)) && ($ordenPagoReposicion[0]->getReposicionEgresoValor()->getResponsableEgresoValor()->getId() != $reposicion->getResponsableEgresoValor()->getId())) {
                $reposicion->setCambiaResponsable(true);
            }
        }

        $ordenPagoPendientePago->setReposicionEgresoValor($reposicion);

        $ordenPagoPendientePago->getEgresoValor()->addReposicione($reposicion);
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        /* @var $entity  \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */

        if ($entity->getReposicionEgresoValor() != null) {

            $responsableEgresoValor = $entity->getReposicionEgresoValor()->getResponsableEgresoValor();
        } else {
            $responsableEgresoValor = $entity->getEgresoValor()->getResponsableEgresoValor();
        }

        return array(
            'nombre' => $responsableEgresoValor,
            'labelNombre' => 'Responsable',
            'identificacion' => $responsableEgresoValor->getNroDocumento(),
            'labelIdentificacion' => $responsableEgresoValor->getTipoDocumento()
        );
    }

    /**
     * 
     * @return type
     */
    public function getConceptoAsientoReemplazoPago() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        return $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                        ->findOneByCodigo('TESORERIA');
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {

        /* @var $ordenPago  \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */

        // $ordenPago->getEgresoValor()->setImporte($ordenPago->getEgresoValor()->getImporte() - $ordenPago->getImporte());
        // Seteo el EstadoEgresoValor
        $ordenPago->getEgresoValor()->setEstadoEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE)
        );

        $ordenPago->getReposicionEgresoValor()->setEstadoReposicionEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoReposicionEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoReposicionEgresoValor::ESTADO_ANULADA)
        );

        $autorizacionContable->setReposicionEgresoValor(null);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/egresovalor/form_pagar", name="ordenpagoegresovalor_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/egresovalor/{id}/historico_general", name="ordenpagoegresovalor_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor $ordenPago
     * @param type $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */

        $egresoValor = $ordenPago->getEgresoValor();

        $resultArray['egresoValor'] = $egresoValor;

        return $resultArray;
    }

    /**
     * @Route("/egresovalor/generarAsientos/", name="egresovalor_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosEgresoValor() {

        gc_enable();

        $parcial = false;

        $offset = 0;
        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $egresosValor = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')
                ->createQueryBuilder('opev')
                ->where('opev.fechaContable >= :fecha')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->setParameter('fecha', '2015-08-01 00:00:00')
                ->orderBy('opev.id', 'asc')
                ->getQuery()
                ->getResult();

        $offset = $limit * $i;
        $i++;
        while (count($egresosValor) > 0) {
            /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
            foreach ($egresosValor as $egresoValor) {
                $this->get('adif.asiento_service')->generarAsientoPagoEgresoValor($egresoValor, $this->getUser());
            }
            unset($egresosValor);
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            $egresosValor = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')
                    ->createQueryBuilder('opev')
                    ->where('opev.fechaContable >= :fecha')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->setParameter('fecha', '2015-08-01 00:00:00')
                    ->orderBy('opev.id', 'asc')
                    ->getQuery()
                    ->getResult();
            $offset = $limit * $i;
            $i++;
        }
        unset($egresosValor);
        $em->clear();
        unset($em);
        gc_collect_cycles();

        if (!$parcial) {
            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de Egreso de valor exitosa');
        }

        return $this->redirect($this->generateUrl('ordenpago'));
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function printHTMLAction($ordenPago) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */

        $arrayResult['op'] = $ordenPago;

        $egresoValor = $ordenPago->getEgresoValor();
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $arrayResult['idEmpresa'] = $idEmpresa;
        
        if ($ordenPago->getBeneficiario() != null) {
            $arrayResult['razonSocial'] = $ordenPago->getBeneficiario()->getRazonSocial();
            $arrayResult['concepto'] = $egresoValor->getTipoEgresoValor();
            $arrayResult['beneficiario'] = $egresoValor->getResponsableEgresoValor();
            $arrayResult['origen'] = $egresoValor->getGerencia();
        }

        return $this->renderView('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor:print.show.html.twig', $arrayResult);
    }

}
