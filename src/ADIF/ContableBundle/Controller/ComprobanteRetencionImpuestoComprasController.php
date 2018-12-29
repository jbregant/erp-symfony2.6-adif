<?php

namespace ADIF\ContableBundle\Controller;

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
class ComprobanteRetencionImpuestoComprasController extends ComprobanteRetencionBaseController {

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);
    }

    /**
     * @Route("/compras/print/{id}", name="comprobanteRetencionImpuestoCompras_pdf")
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
        return 'ADIFContableBundle:ComprobanteRetencionImpuestoCompras';
    }

    /**
     * 
     * @return string
     */
    public function getComprobantesAplicanImpuesto($comprobanteRetencionImpuesto) {
        return $this->get('adif.retenciones_service')->getComprobantesAplicanImpuesto($comprobanteRetencionImpuesto);
    }

//    /**
//     * @Route("/compras/generarRenglonDDJJ/", name="comprobanteretencioncompras_generarrenglon_ddjj")
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
//        $comprobantesImportados = $em->getRepository('ADIFContableBundle:ComprobanteRetencionImpuestoCompras')
//                ->createQueryBuilder('c')
//                ->where("c.renglonDeclaracionJurada is null")
//                ->andWhere("c.id in (746,772)")
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
//            $comprobantesImportados = $em->getRepository('ADIFContableBundle:ComprobanteRetencionImpuestoCompras')
//                    ->createQueryBuilder('c')
//                    ->where("c.renglonDeclaracionJurada is null")
//                    ->andWhere("c.id in (746,772)")
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
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de renglones de DDJJ de compras exitosa');
//        }
//
//        return $this->redirect($this->generateUrl('ordenpago'));
//    }

}
