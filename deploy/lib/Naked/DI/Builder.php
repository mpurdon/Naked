<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

use Naked\DI;
use Naked\DI\ReflectionClass;

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
        //echo "Building a $service for the $context context<br>";
        $specification = $this->specifications[$service][$context];

        // We made be trying to load a one of a kind object
        if ($specification->using) {
            $className = $specification->using;
        } else {
            $className = $specification->class;
        }

        $instance = $this->getInstance($className, $context);

        if ($instance instanceof $className) {
            // If we have specified any values to set in the spec, set them now
            $this->setProperties($instance, $specification);

            return $instance;
        }

        throw new RuntimeException("Could not create an instance of $className");
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
        $dependencies = $this->getDependencies($className);
        $parameters = array();

        if (count($dependencies) > 0) {
            $di = DI::container();
            foreach ($dependencies as $dependency) {
                //echo "Instantiating dependency $dependency<br>";
                $parameters[] = $di->get($dependency, $context, true);
            }

            // We only use reflection when we need to pass in dependencies
            $reflectionClass = new \Reflectionclass($className);

            //echo "Instantiating $className with parameters:<pre>",var_dump($parameters),"</pre>";

            $instance = $reflectionClass->newInstanceArgs($parameters);
        } else {
            $instance = new $className();
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
        $factoryMethod = $this->factories[$service][$context]->factory;
        $instance = $factoryMethod();

        if (is_object($instance)) {
            // If we have specified any values to set in the spec, set them now
            $this->setProperties($instance, $this->factories[$service][$context]);

            return $instance;
        }

        throw new RuntimeException("Could not create an instance of $service");
    }

    /**
     * Simply try to instantiate the class
     *
     * @param string $className
     * @param string $context
     */
    public function getUsingSimpleInstantiation($className, $context)
    {
        //echo "Instantiating!!!!<br>";
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
        $dependencies = array();
        $reflectedClass = new ReflectionClass($class);

        if ($reflectedClass->hasConstructorInjection()) {
            $dependencies = $reflectedClass->getInjectionQueue();
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
            $method = 'set' . ucfirst($property);
            $instance->$method($value);
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
