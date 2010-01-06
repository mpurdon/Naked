<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Routing;

use Naked\Routing\PathMatching;

/**
 * User defined BasicRoute
 *
 * @package Naked
 * @subpackage Routing
 * @author Matthew Purdon
 */
class BasicRoute implements PathMatching
{
    /**#@+
     * @var string
     */
    protected $name;
    protected $pattern;

    protected $module = 'index';
    protected $controller = 'index';
    protected $action = 'index';
    /**#@-*/

    /**
     * @var array
     */
    protected $defaults;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $pattern
     */
    public function __construct($name, $pattern)
    {
        $this->name = $name;
        $this->pattern = '#' . $pattern . '#';
        $this->defaults = array();
    }

    /**
     * Determine if this route matches the provided path.
     *
     * @param string $path
     * @return boolean
     */
    public function matches($path)
    {
        //echo "Trying to match {$path} with {$this->pattern}<br>";
        if (preg_match($this->pattern, $path)) {
            return $this;
        }

        return false;
    }

    /**
     * Handle getting a route property
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        if (in_array($key, array('module', 'controller', 'action'))) {
            return $this->$key;
        }
    }

    /**
     * Set the module for this route
     *
     * @param string $module
     * @return BasicRoute
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Set the controller for this route
     *
     * @param string $controller
     * @return BasicRoute
     */
    public function setController($controller)
    {
        $this->controller = ucfirst($controller);
        return $this;
    }

    /**
     * Set the action for this route
     *
     * @param string $action
     * @return BasicRoute
     */
    public function setAction($action)
    {
        $this->action = preg_replace('/-(.?)/e',"strtoupper('$1')", $action);
        return $this;
    }

    /**
     * String representation of this BasicRoute
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set the default value for a parameter
     *
     * @param string $key
     * @param mixed $value
     * @return Naked\Routing\BasicRoute
     */
    public function setDefault($key, $value)
    {
        $this->defaults[$key] = $value;
        return $this;
    }

    /**
     * Get the default value for a parameter
     *
     * @param string $key
     * @return mixed
     */
    public function getDefault($key)
    {
        if (isset($this->defaults[$key])) {
            return $this->defaults[$key];
        }

        return null;
    }
}
