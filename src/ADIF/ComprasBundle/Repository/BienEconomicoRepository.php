<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Orx;

/**
 * 
 */
class BienEconomicoRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getServicios() {

        $query = $this->createQueryBuilder('be')
                ->where('be.esProducto = 0');

        return $query;
    }

    /**
     * @param array $get
     * @param bool $flag
     * @return array|\Doctrine\ORM\Query
     */
    public function ajaxTable($get, $flag = false) {

        /* Indexed column (used for fast and accurate table cardinality) */
        $alias = "be";

        /* DB table to use */
        $tableObjectName = 'ADIFComprasBundle:BienEconomico';

        /**
         * Set to default
         */
        if (!isset($get['columns']) || empty($get['columns'])) {
            $get['columns'] = array('id');
        }

        $aColumns = array();

        foreach ($get['columns'] as $value) {
            $aColumns[] = $alias . '.' . $value;
        }

        $cb = $this->getEntityManager()
                ->getRepository($tableObjectName)
                ->createQueryBuilder($alias)
                ->select('partial be.{id, denominacionBienEconomico, requiereEspecificacionTecnica, esProducto }, '
                        . 'partial r.{id, denominacionRubro}, '
                        . 'partial es.{id, denominacionEstadoBienEconomico},'
                        . 'partial ti.{id, aliasTipoImportancia}')
                ->leftJoin('be.rubro', 'r')
                ->leftJoin('be.estadoBienEconomico', 'es')
                ->leftJoin('es.tipoImportancia', 'ti');

        if (isset($get['start']) && $get['start'] != '-1' && $get['length'] != '-1') {
            $cb->setFirstResult((int) $get['start'])
                    ->setMaxResults((int) $get['length']);
        }

        /*
         * Ordering
         */
        if (isset($get['order'])) {
            for ($i = 0; $i < intval($get['order']); $i++) {
                $cb->orderBy($aColumns[(int) $get['order'][$i]['column'] - 1], $get['order'][$i]['dir']);
            }
        }

        /*
         * Global Filtering
         */
        if (isset($get['search']) && $get['search']['value'] != '') {

            $aLike = array();

            for ($i = 0; $i < count($aColumns); $i++) {
                $aLike[] = $cb->expr()->like($aColumns[$i], '\'%' . $get['search']['value'] . '%\'');
            }

            if (count($aLike) > 0) {
                $cb->andWhere(new Orx($aLike));
            } else {
                unset($aLike);
            }
        }

        /*
         * TODO Specific Filtering
         */

        /*
         * SQL queries
         * Get data to display
         */
        $query = $cb->getQuery();

        if ($flag) {
            return $query;
        } else {
            return $query->getResult();
        }
    }

    /**
     * 
     * @return type
     */
    public function getCount() {

        $aResultTotal = $this->getEntityManager()
                ->createQuery('SELECT COUNT(be) FROM ADIFComprasBundle:BienEconomico be')
                ->setMaxResults(1)
                ->getResult();

        return $aResultTotal[0][1];
    }

    /**
     * 
     * @param array $get
     * @return type
     */
    public function getFilteredCount(array $get) {

        $alias = "be";

        /* DB table to use */
        $tableObjectName = 'ADIFComprasBundle:BienEconomico';

        $cb = $this->getEntityManager()
                ->getRepository($tableObjectName)
                ->createQueryBuilder($alias)
                ->select("count(be.id)");

        $aColumns = array();

        foreach ($get['columns'] as $value) {
            $aColumns[] = $alias . '.' . $value;
        }

        /*
         * Filtering
         */
        if (isset($get['search']) && $get['search']['value'] != '') {

            $aLike = array();

            for ($i = 0; $i < count($aColumns); $i++) {
                $aLike[] = $cb->expr()->like($aColumns[$i], '\'%' . $get['search']['value'] . '%\'');
            }

            if (count($aLike) > 0) {
                $cb->andWhere(new Orx($aLike));
            } else {
                unset($aLike);
            }
        }

        /*
         * SQL queries
         * Get data to display
         */
        $query = $cb->getQuery();

        $aResultTotal = $query->getResult();

        return $aResultTotal[0][1];
    }

}
