<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ConceptosRepository extends EntityRepository {

    /**
     * Retorna la lista de Conceptos para un convenio dado
     */
    public function findAllByConvenio($convenio) {

        $qb = $this->createQueryBuilder("concepto")
                ->join("concepto.convenios", "convenios")
                ->where("convenios.id = :convenio and concepto.activo = 1 and concepto.esNovedad = 0")
                ->setParameter("convenio", $convenio)
                ->addOrderBy('concepto.fechaAlta', 'ASC');

        return $qb;
    }

    /**
     * Retorna la lista de Novedades para un convenio dado
     */
    public function findAllNovedadesByConvenio($convenio) {

        $qb = $this->createQueryBuilder("concepto")
                ->join("concepto.convenios", "convenios")
                ->where("convenios.id = :convenio and concepto.activo = 1 and concepto.esNovedad = 1")
                ->setParameter("convenio", $convenio)
                ->addOrderBy('concepto.fechaAlta', 'ASC');

        return $qb;
    }

    public function findAllByTipoConceptoAndNovedad($tiposConcepto = null, $esNovedad = null, $excepciones = null) {
        $qb = $this
                ->createQueryBuilder('c')
                ->select('c, v')
                ->innerJoin('c.versiones', 'v')
                ->where('1=1')
                ->orderBy('c.codigo', 'ASC');

        if ($esNovedad !== null) {
            $qb->andWhere('c.esNovedad = :esNovedad')->setParameter('esNovedad', $esNovedad);
        }

        if ($tiposConcepto) {
            $qb->andWhere('c.idTipoConcepto IN (:ids)')->setParameter('ids', $tiposConcepto);
        }
        
        if ($excepciones) {
            $qb->andWhere('c.codigo NOT IN (:codigos)')->setParameter('codigos', $excepciones);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * @param type $ids IDS de conceptos a filtrar
     * @return type
     */
    public function findAllByIds($ids = array()) {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c, v')
            ->innerJoin('c.versiones', 'v')
            ->where('c.id IN (:ids)')->setParameter('ids', $ids)
            ->orderBy('c.codigo', 'ASC');
        return $qb->getQuery()->getResult();
    }
    
    /**
     * 
     * @param type $codigos IDS de conceptos a filtrar
     * @return type
     */
    public function findAllByCodigos($codigos = array()) {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c, v')
            ->innerJoin('c.versiones', 'v')
            ->where('c.codigo IN (:codigos)')->setParameter('codigos', $codigos)
            ->orderBy('c.codigo', 'ASC');
        return $qb->getQuery()->getResult();
    }

}
