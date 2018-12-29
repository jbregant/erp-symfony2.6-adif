<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;



/**
 * InfoPresupuestariaController controller.
 *
 * @Route("/presupuestaria")
 * @Security("has_role('ROLE_CONTABLE')")
 */
class InfoPresupuestariaController extends BaseController{
	/**
     *
     * @Route("/devengado", name="info_presup_devengado")
     * @Method("GET")
     * @Template()
     */
    public function devengadoAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Devengado')->createQueryBuilder('d')->orderBy('d.fechaCreacion', 'DESC')->getQuery()->getResult();
        // cre->findAll();

        echo '<table border="1">';
        echo '<thead><tr>'.
    			'<th>Fecha creaci&oacute;n</th>'.
    			'<th>Cuenta contable</th>'.
    			'<th>Renglon</th>'.
    			'<th>OC</th>'.
    			'<th>Monto</th>'.
			 '</th></thead>';
        foreach ($entities as $d) {
        	echo '<tr>'.
        			'<td style="text-align: center;">'.$d->getFechaCreacion()->format('d/m/Y').'</td>'.
        			'<td>'.$d->getCuentaContable().'</td>'.
        			'<td>'.$d->getRenglonComprobanteCompra()->getDescripcion().'</td>'.
        			'<td>'.$d->getRenglonComprobanteCompra()->getComprobante()->getOrdenCompra().'</td>'.
        			'<td style="text-align: right;width: 150px;font-family: \'Courier new\'">'.$d->getMonto().'</td>'.
    			 '</tr>';
        }
        echo '<table>';

        die();
    }

    /**
     *
     * @Route("/provisorio", name="info_presup_provisorio")
     * @Method("GET")
     * @Template()
     */
    public function provisorioAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $entities = $em->getRepository('ADIFContableBundle:Provisorio')->createQueryBuilder('p')->orderBy('p.fechaCreacion', 'DESC')->getQuery()->getResult();
        
        $reng_req_rep = $em_compras->getRepository('ADIFComprasBundle:RenglonRequerimiento');

        echo '<table border="1">';
        echo '<thead><tr>'.
    			'<th>Fecha creaci&oacute;n</th>'.
    			'<th>Cuenta contable</th>'.
    			'<th>Renglon</th>'.
    			'<th>Requerimiento</th>'.
    			'<th>Monto</th>'.
			 '</th></thead>';
        foreach ($entities as $d) {
        	$renglon_req = $reng_req_rep->find($d->getIdRenglonRequerimiento());
        	echo '<tr>'.
        			'<td style="text-align: center;">'.$d->getFechaCreacion()->format('d/m/Y').'</td>'.
        			'<td>'.$d->getCuentaContable().'</td>'.
        			'<td>'.$renglon_req.'</td>'.
        			'<td>'.$renglon_req->getRequerimiento().'</td>'.
        			'<td style="text-align: right;width: 150px;font-family: \'Courier new\'">'.$d->getMonto().'</td>'.
    			 '</tr>';
        }
        echo '<table>';

        die();
    }

    /**
     *
     * @Route("/definitivo", name="info_presup_definitivo")
     * @Method("GET")
     * @Template()
     */
    public function definitivoAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $entities = $em->getRepository('ADIFContableBundle:Definitivo')->createQueryBuilder('d')->orderBy('d.fechaCreacion', 'DESC')->getQuery()->getResult();
        
        $reng_oc_rep = $em_compras->getRepository('ADIFComprasBundle:RenglonOrdenCompra');

        echo '<table border="1">';
        echo '<thead><tr>'.
    			'<th>Fecha creaci&oacute;n</th>'.
    			'<th>Cuenta contable</th>'.
    			// '<th>Renglon</th>'.
    			'<th>OC</th>'.
    			'<th>Monto</th>'.
			 '</th></thead>';
        foreach ($entities as $d) {
        	$renglon = '';
        	$oc = '';
        	if ($d->getIdRenglonOrdenCompra()){
        		$renglon = $reng_oc_rep->find($d->getIdRenglonOrdenCompra());
        		$oc = $renglon->getOrdenCompra();
        	}
        	echo '<tr>'.
        			'<td style="text-align: center;">'.$d->getFechaCreacion()->format('d/m/Y').'</td>'.
        			'<td>'.$d->getCuentaContable().'</td>'.
        			// '<td>'.$renglon->getDescripcion().'</td>'.
        			'<td>'.$oc.'</td>'.
        			'<td style="text-align: right;width: 150px;font-family: \'Courier new\'">'.$d->getMonto().'</td>'.
    			 '</tr>';
        }
        echo '<table>';

        die();
    }

    /**
     *
     * @Route("/ejecutado", name="info_presup_ejecutado")
     * @Method("GET")
     * @Template()
     */
    public function ejecutadoAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        // $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $entities = $em->getRepository('ADIFContableBundle:Ejecutado')->createQueryBuilder('e')->orderBy('e.fechaCreacion', 'DESC')->getQuery()->getResult();
        
        // $reng_op_rep = $em_compras->getRepository('ADIFComprasBundle:RenglonOrdenPago');

        echo '<table border="1">';
        echo '<thead><tr>'.
    			'<th>Fecha creaci&oacute;n</th>'.
    			'<th>Cuenta contable</th>'.
    			'<th>Renglon comprobante compra</th>'.
    			'<th>OP</th>'.
    			'<th>Monto</th>'.
			 '</th></thead>';
        foreach ($entities as $d) {
        	echo '<tr>'.
        			'<td style="text-align: center;">'.$d->getFechaCreacion()->format('d/m/Y').'</td>'.
        			'<td>'.$d->getCuentaContable().'</td>'.
        			'<td>'.$d->getRenglonComprobanteCompra()->getDescripcion().'</td>'.
        			'<td>'.$d->getOrdenPagoComprobante()->getNumeroOrdenPago().'</td>'.
        			'<td style="text-align: right;width: 150px;font-family: \'Courier new\'">'.$d->getMonto().'</td>'.
    			 '</tr>';
        }
        echo '<table>';

        die();
    }
}