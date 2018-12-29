<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPagoACuenta;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class PagoACuentaRepository extends EntityRepository {

    /**
     * 
     * @param type $tipo
     * @param type $mes
     * @return type
     */
    public function findByTipoAndPeriodo($tipo, $mes) {

        $query = $this->createQueryBuilder('pac');
        
        $query->innerJoin('pac.ordenPago', 'op')
                ->innerJoin('pac.estadoPagoACuenta', 'epac')
                ->where('pac.tipoDeclaracionJurada = :tipo')
                ->andWhere('MONTH(pac.fechaCreacion) = :mes')
                ->andWhere('epac.denominacion = :estado')
                ->andWhere('op.fechaOrdenPago IS NOT NULL')
                ->setParameters(array(
                    'tipo' => $tipo, 
                    'mes' => $mes,
                    'estado' => ConstanteEstadoPagoACuenta::PENDIENTE));
        
        return $query->getQuery()->getResult();
    }
    
    /**
     * 
     * @param type $tipo
     * @param type $mes
     * @return type
     */
    public function findByTipoAndFechaDesdeAndFechaHasta($tipo, $fechaDesde, $fechaHasta) {

        $query = $this->createQueryBuilder('pac');
        
        $query->innerJoin('pac.ordenPago', 'op')
                ->innerJoin('pac.estadoPagoACuenta', 'epac')
                ->where('pac.tipoDeclaracionJurada = :tipo')
                ->andWhere('pac.fechaCreacion BETWEEN :fechaDesde AND :fechaHasta')
                ->andWhere('epac.denominacion = :estado')
                ->andWhere('op.fechaOrdenPago IS NOT NULL')
                ->setParameters(array(
                    'tipo' => $tipo, 
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'estado' => ConstanteEstadoPagoACuenta::PENDIENTE));
        
        return $query->getQuery()->getResult();
    }

}
