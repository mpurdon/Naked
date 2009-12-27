<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

/**
 * Dependency Injection Registry
 *
 * Used to house instances of stuff to be injected.
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Registry
{
    /**#@+
     * @var array
     */
    protected $services = array();
    protected $shared = array();
    /**#@-*/

    /**
     * Set a sservice
     *
     * @param string $service
     * @param object $instance
     * @param string $context
     */
    public function set($service, $instance, $context='default')
    {
        $this->services[$service][$context] = $instance;
    }

    /**
     * Get a service if we have it
     *
     * @param $service
     * @return object
     */
    public function get($service, $context='default')
    {
        //echo "Attempting to get $service for $context context from registry<br>";

        if ($this->has($service, $context)) {
            //echo "Found a $service for $context context<br>";
            return $this->services[$service][$context];
        }

        return null;
    }

    /**
     * Determine if we have a service
     *
     * @param $service
     */
    public function has($service, $context)
    {
        //echo "Determining if the service is in the registry<br>";
        return isset($this->services[$service][$context]);
    }

    /**
     * String representation of the registered service instances
     *
     * @return string
     */
    public function __toString()
    {
        $strings = array();
        foreach ($this->services as $service => $contexts) {
            foreach ($contexts as $context => $instance) {
                $strings[] = "$service for context $context";
            }
        }

        return implode(',', $strings);
    }
}
