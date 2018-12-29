<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\ContableBundle\Controller\ComprobanteRetencionBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaComprobanteRetencionImpuesto;

/**
 * ComprobanteRetencionImpuestoCompras controller.
 *
 * @Route("/comprobanteretencion")
 */
class ComprobanteRetencionImpuestoConsultoriaController extends ComprobanteRetencionBaseController {

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);
    }

    /**
     * @Route("/consultoria/print/{id}", name="comprobanteRetencionImpuestoConsultoria_pdf")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:ComprobanteRetencionImpuestoConsultoria';
    }

    /**
     * 
     * @return string
     */
    public function getComprobantesAplicanImpuesto($comprobanteRetencionImpuesto) {
        return $this->get('adif.retenciones_service')->getComprobantesAplicanImpuestoConsultoria($comprobanteRetencionImpuesto);
    }

//        /**
//     * @Route("/consultoria/generarRenglonDDJJ/", name="comprobanteretencionconsultoria_generarrenglon_ddjj")
//     * @Method("PUT|GET")     
//     */
//    public function generarRenglonDDJJ() {
//
//        gc_enable();
//
//        $parcial = false;
//
//        $offset = 0;
//        $limit = 20;
//        $i = 1;
//
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $comprobantesImportados = $em->getRepository('ADIFContableBundle:ComprobanteRetencionImpuestoConsultoria')
//                ->createQueryBuilder('c')
//                ->innerJoin('c.ordenPago', 'op')
//                ->where("c.renglonDeclaracionJurada is null")
//                ->andWhere("op.fechaContable >= '2015-08-01'")
//                ->setFirstResult($offset)
//                ->setMaxResults($limit)
//                ->orderBy('c.id', 'asc')
//                ->getQuery()
//                ->getResult();
//
//        $offset = $limit * $i;
//        $i++;
//        while (count($comprobantesImportados) > 0) {
//            foreach ($comprobantesImportados as $comprobanteImportado) {
//                // Creo el Renglon de DDJJ asociado
//                $renglonDDJJ = new RenglonDeclaracionJuradaComprobanteRetencionImpuesto();
//
//                $renglonDDJJ->setComprobanteRetencionImpuesto($comprobanteImportado);
//
//                $renglonDDJJ->setFecha(new \DateTime());
//                $renglonDDJJ->setTipoRenglonDeclaracionJurada(
//                        $em->getRepository('ADIFContableBundle:TipoRenglonDeclaracionJurada')
//                                ->findOneByCodigo(ConstanteTipoRenglonDeclaracionJurada::COMPROBANTE_RETENCION_IMPUESTO_COMPRA)
//                );
//
//                $renglonDDJJ->setTipoImpuesto($comprobanteImportado->getRegimenRetencion()->getTipoImpuesto());
//
//                $renglonDDJJ->setEstadoRenglonDeclaracionJurada($em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
//                                ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::PENDIENTE));
//
//                $renglonDDJJ->setMonto($comprobanteImportado->getMonto());
//
//                $em->persist($renglonDDJJ);
//
//                $comprobanteImportado->setRenglonDeclaracionJurada($renglonDDJJ);
//            }
//            unset($comprobantesImportados);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $comprobantesImportados = $em->getRepository('ADIFContableBundle:ComprobanteRetencionImpuestoConsultoria')
//                    ->createQueryBuilder('c')
//                    ->innerJoin('c.ordenPago', 'op')
//                    ->where("c.renglonDeclaracionJurada is null")
//                    ->andWhere("op.fechaContable >= '2015-08-01'")
//                    ->setFirstResult($offset)
//                    ->setMaxResults($limit)
//                    ->orderBy('c.id', 'asc')
//                    ->getQuery()
//                    ->getResult();
//            $offset = $limit * $i;
//            $i++;
//        }
//        unset($comprobantesImportados);
//        $em->clear();
//        unset($em);
//        gc_collect_cycles();
//
//        if (!$parcial) {
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de renglones de DDJJ de consultoria exitosa');
//        }
//
//        return $this->redirect($this->generateUrl('ordenpago'));
//    }
    
}
