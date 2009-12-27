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
 * Used to describe build factory for an object instance
 *
 * @todo Make FactoryMethod implement Buildable interface
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class FactoryMethod
{
    protected $class;
    protected $context = 'default';
    protected $factory;
    protected $properties = array();

    /**
     * Specify the class to build
     *
     * @param string $class
     */
    public function build($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Specify the context we are dealing with
     *
     * @param string $context
     */
    public function forContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Specify the function to use to build the object
     *
     * @param function $function
     */
    public function using($factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * Specify a variable to set after the object has been instantiated
     *
     * @param string $property
     * @param mixed $value
     */
    public function having($property, $value)
    {
        $this->properties[$property] = $value;
        return $this;
    }

    /**
     * Determine if this factory method is valid (buildable)
     *
     * @return boolean
     */
    public function isValid()
    {
        return strlen($this->class) > 0;
    }

    /**
     * Magic getter for properties
     *
     * @param $name
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * String representation of this specification
     */
    public function __toString()
    {
        $string = $this->class .
                  ' using a Factory Method for the context ' . $this->context;

        return $string;
    }
}
