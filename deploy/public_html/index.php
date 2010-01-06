<?php
/**
 * The Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

 // @todo Cache annotations
 // @todo implement logging
 // @todo finish templating (inheritance to layouts)
 // @todo implement caching
 // @todo implement database connection crap
 // @todo implement ORM modelling (Domain Models)
 // @todo implement Unit of Work
 // @todo implement forms using domain models
 // @todo implement authentication
 // @todo implement i18n
 // @todo make "theming" easy

set_include_path(dirname(dirname(__FILE__)));

/**
* Require environment to get started
*/
require_once 'lib/Naked/Application/Environment.php';

use Naked\Application\Environment;
use Naked\DI;
use Naked\DI\ConfigLoader;
use Naked\Request;

// Add the environment to the DI Container, we have to do this first so that
// we do include paths and autoloader for the rest of the junk to work
$environment = new Environment();
$di = DI::container();
$di->set('Naked\Application\Environment', $environment);

// Add the rest of the dependencies now that we know where to find them.
$dependencyConfigLoader = new ConfigLoader($environment);
$dependencyConfigLoader->load();
$di->initLogging();
unset($dependencyConfigLoader);

// Now we get our application and run it
$application = $di->get('Naked\Application');
$request = $di->get('Naked\Request');
$application->run($request);
