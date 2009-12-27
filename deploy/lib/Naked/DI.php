<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked;

use Naked\DI;
use Naked\DI\Registry;
use Naked\DI\Builder;
use Naked\DI\Buildable;

/**
 * Dependency Injection Container
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class DI
{
    const SERVICE_NOT_FOUND = 0;
    const SERVICE_IN_REGISTRY = 1;
    const SERVICE_IN_BUILDER = 2;

    /**
     * @static
     * @var Naked\DI
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var Naked\DI\Registry
     */
    protected $services;

    /**
     * @var Naked\DI\Builder
     */
    protected $builder;

    /**
     * Constructor
     */
    protected function __construct($config=null)
    {
        $this->config = $config;
    }

    /**
     * Get an instance of the Dependency Injection container
     *
     * @return Naked\DI
     */
    public static function container()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DI();
        }

        return self::$instance;
    }

    /**
     * Get the services that are registered
     *
     * @return Naked\DI\Registry
     */
    protected function getServices()
    {
        if (is_null($this->services)) {
            $this->services = new Registry();
        }

        return $this->services;
    }

    /**
     * Get the service builder
     *
     * @return Naked\DI\Builder
     */
    protected function getBuilder()
    {
        if (is_null($this->builder)) {
            $this->builder = new Builder();
        }

        return $this->builder;
    }

    /**
     * Sets a service by the given name
     *
     * @param string $name
     * @param object $service
     * @param string $context
     */
    public function set($name, $service, $context='default')
    {
        $this->getServices()->set($name, $service, $context);
    }

    /**
     * Determine if we have a service
     *
     * @param string $service
     */
    public function hasService($service, $context='default')
    {
        //echo "Looking for service $service<br>";
        if ($this->getServices()->has($service, $context)) {
            return self::SERVICE_IN_REGISTRY;
        }

        if ($this->getBuilder()->has($service, $context)) {
            return self::SERVICE_IN_BUILDER;
        }

        return self::SERVICE_NOT_FOUND;
    }

    /**
     * Determine if a service is shared (singleton) or instantiated on every get
     *
     * @param $serviceName
     * @return boolean
     */
    public function isShared($serviceName)
    {
        return isset($this->shared[$serviceName]);
    }

    /**
     * Get a service
     *
     * @param string $serviceName
     * @param string $context
     * @param boolean $fallBackToDefaultContext
     */
    public function get($serviceName, $context='default', $fallBackToDefaultContext=false)
    {
        $service = null;
        $serviceLocation = $this->hasService($serviceName, $context);

        switch($serviceLocation) {
            case self::SERVICE_NOT_FOUND:
                // If we don't have a service for the specified service and we do not
                // allow falling back to the default context, freak out.
                //echo "No service $serviceName for that context. Checking if I should look in default: ",strcasecmp($context, 'default'),"<br>";
                if (strcasecmp($context, 'default') != 0 && $fallBackToDefaultContext) {
                    // Try to get the job done with the default context
                    $service = $this->get($serviceName, 'default');
                }

                // We have no clue how to build this thing, let's just try it
                // and hope we don't light the atmosphere on fire.
                if (is_null($service)) {
                    //echo "Could not build it using default context, trying to instantiate it<br>";
                    $service = $this->getBuilder()->getUsingSimpleInstantiation($serviceName, 'default');
                }

                break;

            case self::SERVICE_IN_REGISTRY:
                //echo "Service $serviceName found in registry<br>";
                $service = $this->getServices()->get($serviceName, $context);
                break;

            case self::SERVICE_IN_BUILDER:
                //echo "Service $serviceName found in builder<br>";
                $service = $this->getBuilder()->get($serviceName, $context);
                $this->getServices()->set($serviceName, $service, $context);
                break;
        }

        return $service;
    }

    /**
     * Add the given service building specification to the builder
     *
     * @param DI\Buildable $specification
     * @return DI
     */
    public function addBuildSpecification(Buildable $specification)
    {
        $this->getBuilder()->addSpecification($specification);
        return $this;
    }

    /**
     * Add the given service building factory to the builder
     *
     * @param DI\FactoryMethod $factory
     * @return DI
     */
    public function addBuildFactoryMethod(DI\FactoryMethod $factory)
    {
        $this->getBuilder()->addFactoryMethod($factory);
        return $this;
    }

    /**
     * String representation of this DI container.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getServices();
    }
}
