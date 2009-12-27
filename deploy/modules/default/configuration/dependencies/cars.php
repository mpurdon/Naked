<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Configuration\Dependencies;

use Naked\DI;
use Naked\DI\Specification;

$di = DI::container();

$bmw = new Specification();

$bmw->build('models\Car')
    ->using('models\Car\BMW')
    ->forContext('commuting')
    ->having('model', '750i');

$di->addBuildSpecification($bmw);

$bugatti = new Specification();

$bugatti->build('models\Car')
        ->using('models\Car\Bugatti')
        ->forContext('racing')
        ->having('model', 'Veyron');

$di->addBuildSpecification($bugatti);

$matthew = new Specification();

$matthew->build('models\Driver')
        ->using('models\Driver\Commuter')
        ->having('name', 'Matthew Purdon');

$di->addBuildSpecification($matthew);

$mario = new Specification();

$mario->build('models\Driver')
      ->using('models\Driver\Racer')
      ->forContext('racing')
      ->having('name', 'Mario Andretti');

$di->addBuildSpecification($mario);
