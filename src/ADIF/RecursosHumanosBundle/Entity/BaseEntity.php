<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ADIF\RecursosHumanosBundle\Entity;

use ReflectionClass;

/**
 * Description of BaseEntity
 *
 * @author eprimost
 */
class BaseEntity {
    
    /**
     * To String
     * 
     * Intenta invocar primero al método getNombre de la entidad instanciada 
     * y luego getDescripcion si ninguno existe retorna el nombre de la clase
     * 
     * @return string
     */
    public function __toString() {
        $class = new ReflectionClass(get_class($this));
        try {
            try {
                $method = $class->getMethod('getNombre');
            } catch (\ReflectionException $ex) {
                $method = $class->getMethod('getDescripcion');
            }
        } catch (\Exception $ex) {
            return get_class($this);
        }
       
        $name = $method->invoke($this);
        
        return $name !== null ? $name : '';
    }
}
