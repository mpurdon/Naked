<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Application;

use Naked;

use Naked\DI;
use Naked\DI\Specification;

/**
 * Dispatch a request handler
 *
 * @package Naked
 * @subpackage Application
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Dispatcher
{
    /**
     * @var unknown_type
     */
    protected $environment;

    /**
     * @var boolean
     */
    protected $hasDispatched = false;

    /**
     * Constructor
     *
     * @Inject
     * @param Naked\Application\Environment $environment
     */
    public function __construct(Naked\Application\Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Dispatch a route
     *
     * @return boolean
     */
    public function dispatch($route, $request)
    {
        // We add the module to the include path.
        $this->environment->addModuleToIncludePath($route->module);

        // Figure out what is going to handle the request
        $controller = $this->getController($route);
        $actionMethod = $route->action . 'Action';

        try {
            $this->startUp($request);
            $response = $controller->$actionMethod();
            $this->shutDown($response);
        } catch (RuntimeException $e) {
            echo "Caught an exception:<br>",$e->getMessage(),'<br>';
        }
    }

    /**
     * Using the route, instantiate a controller
     *
     * @return Naked\Controller
     */
    protected function getController($route)
    {
        // We prepend the module to the class name because it's the namespace for it
        $controllerClass = $route->module . '\\controllers\\' . $route->controller;
        $controller = DI::container()->get($controllerClass);

        return $controller;
    }

    /**
     * Determine if this dispatcher has dispatched any requests yet
     *
     * @return boolean
     */
    public function hasDispatched()
    {
        return $this->hasDispatched;
    }

    /**
     * Perform dispatch startup routines
     */
    protected function startUp($request)
    {
        // @todo all startup routines should be in plugins
        $di = DI::container();

        // Commit any changes the Unit of Work has tracked
        $unitOfWork = $di->get('Naked\UnitOfWork');
        $this->hasDispatched = false;
    }

    /**
     * Perform dispatch shut down routines.
     */
    protected function shutDown($response)
    {
        // @todo All of this should be in plugins
        $di = DI::container();

        // Commit any changes the Unit of Work has tracked
        $unitOfWork = $di->get('Naked\UnitOfWork');
        $unitOfWork->commit();

        $this->hasDispatched = true;

        echo $response;
    }
}
