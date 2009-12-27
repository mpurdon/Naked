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

$controller = new Specification();
$controller->build('Naked\Controller');
$di->addBuildSpecification($controller);

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
$configFactory = new FactoryMethod();
$configFactory->build('Log')
              ->using(function () {
                  echo "getting log<br>";
                  exit();
                    $environment = DI::container()->get('Naked\Application\Environment');
                    $logger = new \Naked\Log();
                    echo "I have the logger <br>";
                    exit();
                    if ($environment->isDevelopment()) {
                        $firebugWriter = new \Naked\Log\Writer\Firebug(\Naked\Log::DEBUG);
                        $logger->addWriter($firebugWriter);
                    } else {
                        $syslogWriter = new \Naked\Log\Writer\Syslog(\Naked\Log::ERR);
                        $logger->addWriter($syslogWriter);
                    }
                    return $logger;
              });

$di->addBuildFactoryMethod($configFactory);
