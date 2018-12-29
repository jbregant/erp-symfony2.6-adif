<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;

class EntityType extends DoctrineType
{
    /*
     * La constante @var string clase, es para hacer menos traumatico la migracion del symfony actual 2.6 a 3.x
     * En Symfony 3.x se pasa como segundo parametro al FormBuilder el EntityType::class y en nuestros formularios 
     * lo vamos a tener como EntityType::clase en la definicion de Formularios (Types), para tenerlo lo mas parecido posible, 
     * para cuando migremos, hagamos simplemente un CTRL H en el directorio "Form" de los Bundles con algun IDE, 
     * reemplazando "EntityType::clase" por "EntityType::class" o directamente en debian hacer:
     * find . -type f -name *.php -exec  sed  -i  's/EntityType::clase/EntityType::class/g' {} \;
     * 
     * Para que ande la constante "class", requiere PHP 7 o superior y Symfony 3.x o superior
     * @author Gustavo Luis
     * @date 19/09/2017
     * @see https://symfony.com/doc/current/reference/forms/types/entity.html
     * @see https://symfony.com/doc/2.6/reference/forms/types/entity.html
     */
    const clase = 'entity';
    
    /**
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param mixed         $queryBuilder
     * @param string        $class
     *
     * @return ORMQueryBuilderLoader
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        return new ORMQueryBuilderLoader(
            $queryBuilder,
            $manager,
            $class
        );
    }

    public function getName()
    {
        return 'entity';
    }
}
