<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of EmpleadoRepository
 *
 * @author eprimost
 */
class EmpleadoRepository extends EntityRepository{
    
    public function find($idEmpleado){
        return $this->createQueryBuilder('e')
            ->select('e, p, sub, cat, con, nac, ec, td, d, l, f572, f649, g, cond, etc, etl, eos, rr, f, ce, es, ph, tc, tl, os, tu')
            ->innerJoin('e.persona', 'p')
            ->innerJoin('e.idSubcategoria', 'sub')
            ->innerJoin('sub.idCategoria', 'cat')
            ->leftJoin('cat.idConvenio', 'con')
            ->innerJoin('p.idNacionalidad', 'nac')
            ->innerJoin('p.idEstadoCivil', 'ec')
            ->innerJoin('p.idTipoDocumento', 'td')
            ->innerJoin('p.idDomicilio', 'd')
            ->leftJoin('d.localidad', 'l')
            ->leftJoin('e.formularios572', 'f572')
            ->leftJoin('e.formulario649', 'f649')
            ->leftJoin('e.idGerencia','g')
            ->leftJoin('e.condicion','cond')
            ->leftJoin('e.tiposContrato','etc')
            ->leftJoin('e.tiposLicencia', 'etl' )
            ->leftJoin('e.obrasSociales', 'eos' )
            ->leftJoin('e.rangoRemuneracion', 'rr' )
            ->leftJoin('e.familiares','f')
            ->leftJoin('e.contactosEmergencia','ce')
            ->leftJoin('e.estudios','es')
            ->leftJoin('e.puestosHistoricos','ph')
            ->leftJoin('etc.tipoContrato', 'tc' )
            ->leftJoin('etl.tipoLicencia', 'tl' )
            ->leftJoin('eos.obraSocial', 'os' )
            ->leftJoin('es.titulo', 'tu')
            ->where('e.id = :idEmpleado')->setParameter('idEmpleado', $idEmpleado)
            ->orderBy('ph.fechaDesde', 'ASC')
            ->addOrderBy('etc.fechaDesde', 'ASC')
            ->getQuery()
            ->getSingleResult();
    }
}
