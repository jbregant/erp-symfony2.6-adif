<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 */
class InventarioMatNuevoProducidoRepository extends EntityRepository {

    /*
     * Funcion par obtener todos los items que corresponden para un Almacen/Obrador para Material Nuevo o Material producido de Obra
     */
    public function getItems($datos){
        
        $provincia = $datos['provincia'];
        //$linea = $datos['linea'];
        $almacen = $datos['almacen'];
        $tipoMaterial = $datos['tipoMaterial'];
        
        $estadoConservacion = NULL;
        if( !empty($datos['estadoConservacion']) ){
            $estadoConservacion = $datos['estadoConservacion'];
        }
        
        $grupoMaterial = NULL;
        if( !empty($datos['grupoMaterial']) ){
            $grupoMaterial = $datos['grupoMaterial'];
        }
        
        
//        Traigo todos los registros de inventario mat nuevo producido, que tienen el almacen seleccionado
//        que no estan en item de hoja de ruta, para cada registro, obtengo los valores correspondientes 
//        de provincia, linea, almacen y tipo de material, para el grupo de material, traigo el grupo de material 
//        que tenga el catalogo del material nuevo y el estado de conservacion lo obtengo del mismo inventario 
        
        if( $tipoMaterial == 2 ){
            // Es el caso para Materiales nuevos 
             
            // Armo la query 
            $sql =  'SELECT DISTINCT i.id, a.idProvincia AS provincia, IDENTITY(a.linea) AS linea, IDENTITY(i.almacen) AS almacen,  IDENTITY(i.tipoMaterial) AS tipoMaterial, IDENTITY(cmn.grupoMaterial) AS grupoMaterial, IDENTITY(i.estadoConservacion) AS estadoConservacion '; 
            $sql .= 'FROM ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido i ';
            $sql .= 'LEFT JOIN ADIF\InventarioBundle\Entity\ItemHojaRutaNuevoProducido ih ';
            $sql .= 'WITH i.id = ih.inventario ';
            $sql .= 'LEFT JOIN ih.hojaRuta hr WITH ( hr.id = ih.hojaRuta AND hr.estadoHojaRuta <> 3 AND hr.fechaVencimiento > CURRENT_DATE() ) ';
            $sql .= 'LEFT JOIN ADIF\InventarioBundle\Entity\Almacen a ';
            $sql .= 'WITH i.almacen = a.id ';
            $sql .= 'LEFT JOIN ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos cmn ';
            $sql .= 'WITH i.materialNuevo = cmn.id ';
            $sql .= 'WHERE ih.inventario IS NULL ';
            $sql .= 'AND i.almacen = '.$almacen.' AND i.tipoMaterial = '.$tipoMaterial;
            if( !empty($estadoConservacion)){
                $sql .= ' AND ih.estadoConservacion = '.$estadoConservacion;
            }
            if( !empty($grupoMaterial)){
                $sql .= ' AND ih.grupoMaterial = '.$grupoMaterial;
            }
            
            
        }else{
            // Armo la query 
            $sql =  'SELECT DISTINCT i.id, a.idProvincia AS provincia, IDENTITY(a.linea) AS linea, IDENTITY(i.almacen) AS almacen,  IDENTITY(i.tipoMaterial) AS tipoMaterial, IDENTITY(cmpdo.grupoMaterial) AS grupoMaterial, IDENTITY(i.estadoConservacion) AS estadoConservacion '; 
            $sql .= 'FROM ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido i ';
            $sql .= 'LEFT JOIN ADIF\InventarioBundle\Entity\ItemHojaRutaNuevoProducido ih ';
            $sql .= 'WITH i.id = ih.inventario ';
            $sql .= 'LEFT JOIN ih.hojaRuta hr WITH ( hr.id = ih.hojaRuta AND hr.estadoHojaRuta <> 3 AND hr.fechaVencimiento > CURRENT_DATE() ) ';
            $sql .= 'LEFT JOIN ADIF\InventarioBundle\Entity\Almacen a ';
            $sql .= 'WITH i.almacen = a.id ';
            $sql .= 'LEFT JOIN ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra cmpdo ';
            $sql .= 'WITH i.materialNuevo = cmpdo.id ';
            $sql .= 'WHERE ih.inventario IS NULL ';
            $sql .= 'AND i.almacen = '.$almacen.' AND i.tipoMaterial = '.$tipoMaterial;
            if( !empty($estadoConservacion)){
                $sql .= ' AND ih.estadoConservacion = '.$estadoConservacion;
            }
            if( !empty($grupoMaterial)){
                $sql .= ' AND ih.grupoMaterial = '.$grupoMaterial;
            }
            
            
            
        }
        
        
        
        $query = $this->getEntityManager()->createQuery($sql);
        
        return $query->getResult();
        
    }
}
