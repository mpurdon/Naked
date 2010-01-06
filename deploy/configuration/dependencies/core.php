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
use Naked\DI\FactoryMethod;

$di = DI::container();

// Use specifications to describe the way in which an object should be built

$cache = new Specification();
$cache->build('Naked\Cache')
      ->using('Naked\Cache\Memcached');
$di->addBuildSpecification($cache);

$querySetQuery = new Specification();
$querySetQuery->build('Naked\Objects\Query')
              ->using('Naked\Objects\Query\MySQL');
$di->addBuildSpecification($querySetQuery);

// Use lambda functions to describe the way in which an object should be built

// Routes
$routeFactory = new FactoryMethod();
$routeFactory->build('Naked\Routing\Routes')
             ->using(function () {
                    $routeBuilder = DI::container()->get('Naked\Routing\Builder');
                    return $routeBuilder->build();
             });

$di->addBuildFactoryMethod($routeFactory);

// Configuration
$configFactory = new FactoryMethod();
$configFactory->build('Naked\Application\Configuration')
              ->using(function () {
                    $environment = DI::container()->get('Naked\Application\Environment');
                    $configurationBuilder = new \Naked\Application\Configuration\Builder($environment);
                    return $configurationBuilder->build();
              });

$di->addBuildFactoryMethod($configFactory);

// Logging
$loggingFactory = new FactoryMethod();
$loggingFactory->build('Naked\Log')
               ->using(function () {
                    $di = DI::container();
                    $configuration = $di->get('Naked\Application\Configuration');

                    if (!$configuration->logging) {
                        return new \Naked\Log\BlackHole();
                    }

                    $logger = new \Naked\Log();
                    $environment = $di->get('Naked\Application\Environment');

                    if ($environment->isDevelopment()) {
                        $firebugWriter = new \Naked\Log\Writer\Firebug(LOG_DEBUG);
                        $logger->addWriter($firebugWriter);
                    } else {
                        // @todo Should create a wackamole/spread logger
                        $syslogWriter = new \Naked\Log\Writer\Syslog(LOG_CRIT);
                        $logger->addWriter($syslogWriter);
                    }

                    return $logger;
              });

$di->addBuildFactoryMethod($loggingFactory);
