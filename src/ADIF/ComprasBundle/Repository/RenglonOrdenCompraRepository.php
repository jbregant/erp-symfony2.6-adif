<?php

namespace ADIF\ComprasBundle\Repository;

use ADIF\BaseBundle\Repository\BaseRepository;

/**
 * 
 */
class RenglonOrdenCompraRepository extends BaseRepository {

    /**
     * 
     * @param type $id
     * @param type $esNotaCredito
     * @return type
     */
    public function getRenglonesOrdenCompra($id, $esNotaCredito = 0) {

        $qb = $this->createQueryBuilder('r');

        $query = $qb
                ->select('r.id, r.cantidad, r.restante, (r.precioUnitario * r.tipoCambio) AS precioUnitario, 
					r.idAlicuotaIva, b.denominacionBienEconomico, rsc.descripcion, o.idCondicionPago, 
					r.tipoCambio, o.idTipoMoneda, b.id AS idBienEconomico, pin.idArea')
                ->innerJoin('r.ordenCompra', 'o')
                ->leftJoin('r.bienEconomico', 'b')
                ->leftJoin('r.renglonCotizacion', 'rc')
                ->leftJoin('rc.renglonRequerimiento', 'rr')
                ->leftJoin('rr.renglonSolicitudCompra', 'rsc')
				->leftJoin('rsc.renglonPedidoInterno', 'rpi')
				->leftJoin('rpi.pedidoInterno', 'pin')
                ->where('o.id = :id')->setParameter('id', $id)
				->andWhere('r.esDesglosado = FALSE')
				;
                
        if ($esNotaCredito == 0) {
            $qb->andWhere('r.restante > 0');
        }
        
        return $query->getQuery()->getResult();
    }

}
