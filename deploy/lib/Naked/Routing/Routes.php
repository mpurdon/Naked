<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */

namespace Naked\Routing;

use Naked\Routing\PathMatching;

/**
 * Represents a Collection of user defined routes.
 *
 * @package Naked
 * @subpackage Routing
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Routes extends \ArrayObject implements PathMatching
{
    /**#@+
     * @var string
     */
    protected $name;
    protected $pattern;
    /*#@-*/

    /**
     * Constructor
     *
     * @param string $name
     * @param string $pattern
     */
    public function __construct($name, $pattern)
    {
        parent::__construct();

        $this->name = $name;
        if (!is_null($pattern)) {
            $this->pattern = '#' . $pattern . '#';
        }
    }

    /**
     * Add a route to the routes
     *
     * @param Route $route
     * @return Routes
     */
    public function append(PathMatching $route)
    {
        // If the module was not specified in the route, assign our name to it
        if ($route instanceof BasicRoute && !$route->module) {
            $route->setModule($this->name);
        }

        parent::append($route);
        return $this;
    }

    /**
     * Determine if this route matches the provided path.
     *
     * @param string $path
     * @return boolean
     */
    public function matches($path)
    {
        if ($this->patternDoesNotMatch($path)) {
            return false;
        }

        // Search the child routes for a match to the path
        foreach ($this->getIterator() as $route) {
            $matchingRoute = $route->matches($path);
            if ($matchingRoute) {
                return $matchingRoute;
            }
        }

        return false;
    }

    /**
     * Determine if this route matches the path based on the matching pattern
     *
     * @param string $path
     * @boolean
     */
    public function patternDoesNotMatch($path)
    {
        return !is_null($this->pattern) && !preg_match($this->pattern, $path);
    }

    /**
     * String representation of the routes
     */
    public function __toString()
    {
        $routes = array();

        foreach($this->getIterator() as $route) {
            $routes[] = (string) $route;
        }

        return $this->name . ':' . implode(',', $routes);
    }
}
