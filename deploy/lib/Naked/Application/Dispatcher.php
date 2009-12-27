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
            $response = $controller->$actionMethod();
            $this->hasDispatched = true;
        } catch (Exception $e) {
            echo "Caught an exception:<br>",$e->getMessage(),'<br>';
        }

        echo $response;
    }

    /**
     * Using the route, instantiate a controller
     *
     * @return Naked\Controller
     */
    protected function getController($route)
    {
        // We set up a build specification for the controller here so that we
        // don't have to create a build spec for all controllers in the
        // dependency configurations
        $di = DI::container();
        $controllerSpec = new Specification();
        $controllerSpec->build('Controller')
                       ->using($route->controller);

        $di->addBuildSpecification($controllerSpec);

        $controller = $di->get($route->controller);
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
}
