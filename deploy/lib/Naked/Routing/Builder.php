<?php
/**
 * Naked Framework
 *
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Routing;

/**
 * An object that builds the routes for the applcation
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Builder
{
    /**
     * @var Naked\Application\Environment
     */
    protected $environment;

    /**
     * @var Naked\Routing\Routes
     */
    protected $routes;

    /**
     * Constructor
     *
     * @Inject
     * @param $modules array
     * @param $path string
     */
    public function __construct(\Naked\Application\Environment $environment)
    {
        $this->environment = $environment;
        $this->routes = new Routes('root', null);
    }

    /**
     * Build routes that are found in the installed modules
     */
    public function build()
    {
        foreach ($this->environment->getModulePaths() as $module) {
            $routes = null;

            $routeFile = $module . DIRECTORY_SEPARATOR . 'routes.php';

            //echo "Searching for route file $routeFile<br>";

            if (file_exists($routeFile)) {
                //echo "found it, including it<br>";
                include($routeFile);
                if (is_null($routes)) {
                    throw new UnexpectedValueException('Did not find $routes defined in ' . $routeFile);
                }

                //echo "Found ",count($routes)," routes<br/>";

                $this->routes->append($routes);
            }
        }

        //echo "Done building routes<br>";

        return $this->routes;
    }
}