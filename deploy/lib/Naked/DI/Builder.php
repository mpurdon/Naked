<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

use Naked;
use Naked\DI;

/**
 * Dependency Injection Builder
 *
 * Used to build junk when we need it
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Builder
{
    /**
     * @var array
     */
    protected $specifications = array();

    /**
     * @var array
     */
    protected $factories = array();

    /**
     * @var Naked\Annotations
     */
    protected $annotations;

    /**
     * @var Naked\Log
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Naked\Annotations $annotations
     */
    public function __construct($annotations)
    {
        $this->annotations = $annotations;
        $this->logger = new \Naked\Log();
    }

    public function setLogger(Naked\Log $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Determine if we have the ability to build the service
     *
     * @param string $service
     * @return boolean
     */
    public function has($service, $context='default')
    {
        //echo "Determining if the service is in the builder<br>";

        if ($this->hasSpecification($service, $context)) {
            return true;
        }

        if ($this->hasFactoryMethod($service, $context)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if we have a factory method for the specified service
     *
     * @param string $service
     * @param string $context
     */
    public function hasSpecification($service, $context)
    {
        //echo "Determining if the builder has a specification for the $service service<br>";
        return isset($this->specifications[$service][$context]);
    }

    /**
     * Determine if we have a factory method for the specified service
     *
     * @param string $service
     * @param string $context
     */
    public function hasFactoryMethod($service, $context)
    {
        //echo "Determining if the builder has a factory for the $service service<br>";
        return isset($this->factories[$service][$context]);
    }

    /**
     * Return an instance of the service
     *
     * @param string $service
     * @return object
     */
    public function get($service, $context='default')
    {
        if ($this->hasSpecification($service, $context)) {
            return $this->getUsingSpecification($service, $context);
        }

        if ($this->hasFactoryMethod($service, $context)) {
            return $this->getUsingFactoryMethod($service, $context);
        }

        return $this->getUsingSimpleInstantiation($service, $context);
    }

    /**
     * Build an instance using a specification
     *
     * @param $service
     * @param $context
     */
    protected function getUsingSpecification($service, $context)
    {
        $this->logger->log("Building a $service for the $context context with a specification");
        $specification = $this->specifications[$service][$context];

        // We made be trying to load a one of a kind object
        if ($specification->using) {
            $className = $specification->using;
        } else {
            $className = $specification->class;
        }

        $this->logger->log("The specification instructs us to build a $service as a $className");
        $instance = $this->getInstance($className, $context);

        if ($instance instanceof $className) {
            // If we have specified any values to set in the spec, set them now
            $this->setProperties($instance, $specification);

            return $instance;
        }

        throw new \RuntimeException("Could not create an instance of $className");
    }

    /**
     * Get an instance of a class taking care to make sure construction dependencies
     * are handled
     *
     * @param string $className
     * @param string $context
     */
    protected function getInstance($className, $context)
    {
        $this->logger->log("Getting an instance of class $className");
        $dependencies = $this->getDependencies($className);
        $parameters = array();

        // Figure out constructor injection dependencies
        // @todo Due to the limitations of constructor injection, should I even bother with it?
        if (isset($dependencies['constructor'])) {
            $this->logger->log("Handling constructor injection for $className");
            $di = DI::container();
            foreach ($dependencies['constructor'] as $dependency) {
                $this->logger->log("Instantiating constructor dependency $dependency");
                $parameters[] = $di->get($dependency, $context, true);
            }

            // We only use reflection when we need to pass in dependencies
            $this->logger->log("Instantiating $className with parameters");
            $reflectionClass = new \Reflectionclass($className);
            // @todo What if we couldn't find the class we actually wanted to build?
            $instance = $reflectionClass->newInstanceArgs($parameters);
        } else {
            $instance = new $className();
        }

        // Figure out setter injection dependencies
        if (isset($dependencies['setter'])) {
            $this->logger->log("Handling setter injection for $className");
            $di = DI::container();
            foreach($dependencies['setter'] as $method => $dependency) {
                $this->logger->log("Instantiating setter dependency $dependency for method $method");
                $dependencyInstance = $di->get($dependency, $context, true);
                $this->logger->log("Calling $method with instance: " . get_class($dependencyInstance));
                $instance->$method($dependencyInstance);
            }
        }

        return $instance;
    }

    /**
     * Build an instance using a factory method
     *
     * @param $service
     * @param $context
     */
    protected function getusingFactoryMethod($service, $context)
    {
        $this->logger->log("Building a $service for the $context context with a factory");
        $factoryMethod = $this->factories[$service][$context]->factory;
        $instance = $factoryMethod();

        if (is_object($instance)) {
            // If we have specified any values to set in the spec, set them now
            $this->setProperties($instance, $this->factories[$service][$context]);

            return $instance;
        }

        throw new \RuntimeException("Could not create an instance of $service");
    }

    /**
     * Simply try to instantiate the class
     *
     * @param string $className
     * @param string $context
     */
    public function getUsingSimpleInstantiation($className, $context)
    {
        $this->logger->log("Simply Instantiating!!!");
        $instance = $this->getInstance($className, $context);
        return $instance;
    }

    /**
     * Determine if there are any dependencies for the class we want to build
     *
     * @param string $class
     */
    protected function getDependencies($class)
    {
        $this->logger->log("Getting dependencies for $class");
        $dependencies = array();

        $annotations = $this->annotations->forClass($class);

        if ($annotations->hasConstructorInjection()) {
            $dependencies['constructor'] = $annotations->getConstructorInjectionDependencies();
        }

        if ($annotations->hasSetterInjection()) {
            $dependencies['setter'] = $annotations->getSetterInjectionDependencies();
        }

        return $dependencies;
    }

    /**
     * Based on the specification, set the properties for this instance
     *
     * @param object $instance
     * @param Buildable $specification
     */
    protected function setProperties($instance, $specification)
    {
        foreach ($specification->properties as $property => $value) {
            $instance->$property = $value;
        }
    }

    /**
     * Add a specification for building an object
     *
     * @todo validate the specification before adding it
     *
     * @param DI\Buildable $specification
     */
    public function addSpecification(Buildable $specification)
    {
        /**
         * @todo Should we prevent users from overwriting specifications?
        if ($this->has($specification->class,$specification->context)) {
            throw new \InvalidArgumentException("Builder already has a specification for a {$specification->class} in the context {$specification->context}");
        }
         */
        $this->specifications[$specification->class][$specification->context] = $specification;
    }

    /**
     * Add a factory for building an object
     *
     * @todo Validate the factory before adding it
     *
     * @param DI\FactoryMethod $factory
     */
    public function addFactoryMethod(FactoryMethod $factory)
    {
        /**
         * @todo Should we prevent users from overwriting factories?
        if ($this->has($specification->class,$specification->context)) {
            throw new \InvalidArgumentException("Builder already has a specification for a {$specification->class} in the context {$specification->context}");
        }
        */
        $this->factories[$factory->class][$factory->context] = $factory;
    }

    /**
     * String representation of this builder
     *
     * @return string
     */
    public function __toString()
    {
        $strings = array();
        foreach ($this->specifications as $context) {
            foreach ($context as $specification) {
                $strings[] = (string) $specification;
            }
        }

        foreach ($this->factories as $context) {
            foreach ($context as $factory) {
                $strings[] = (string) $factory;
            }
        }

        return implode(', a ', $strings);
    }
}
