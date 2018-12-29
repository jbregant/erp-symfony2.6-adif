<?php
namespace ADIF\InventarioBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ProgresivaContinua extends Constraint
{
    public $message1 = 'El activo no cumple con las progresivas inicio / final requeridas.';
    public $message = 'El tramo no respeta la progresividad de los activos lineales.';

    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy() {
        return 'progresiva_continua';
    }
}
