<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template;

/**
 * Represents the context that is used to render a particular template
 *
 * @package Naked
 * @subpackage Template
 */
class Context extends \ArrayObject
{
    /**
     * Magic getter method
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return null;
    }

    /**
     * Magic setter method
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }
}
