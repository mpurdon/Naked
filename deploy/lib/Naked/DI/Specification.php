<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @license http://www.opensource.org/licenses/mit-license.php Licensed under the MIT license
 * @version $Id$
 */

namespace Naked\DI;

use Naked\DI\Buildable;

/**
 * Used to describe build specifications for an object instance
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 */
class Specification implements Buildable
{
    protected $class;
    protected $using;
    protected $singleton = false;
    protected $context = 'default';
    protected $properties = array();

    /**
     * (non-PHPdoc)
     * @see deploy/lib/Naked/DI/Naked\DI.Buildable#build()
     */
    public function build($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see deploy/lib/Naked/DI/Naked\DI.Buildable#using()
     */
    public function using($using)
    {
        $this->using = $using;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see deploy/lib/Naked/DI/Naked\DI.Buildable#singleton()
     */
    public function singleton()
    {
        $this->singleton = true;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see deploy/lib/Naked/DI/Naked\DI.Buildable#forContext()
     */
    public function forContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see deploy/lib/Naked/DI/Naked\DI.Buildable#having()
     */
    public function having($property, $value)
    {
        $this->properties[$property] = $value;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see deploy/lib/Naked/DI/Naked\DI.Buildable#isValid()
     */
    public function isValid()
    {
        return (strlen($this->class) > 0 && strlen($this->using) > 0);
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
        $string = $this->using .
                  ' as a ' . $this->class .
                  ' for the context ' . $this->context;

        return $string;
    }
}