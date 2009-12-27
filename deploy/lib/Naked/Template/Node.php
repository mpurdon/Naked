<?php
/**
 * Naked Framework
 *
 * @author Matthew Purdon <matthew@codenaked.org>
 * @version $Id$
 */

namespace Naked\Template;

/**
 * Template Node
 *
 * @package Naked
 * @subpackage Template
 * @abstract
 */
abstract class Node
{
    /**
     * @var boolean
     */
    protected $mustBeFirst = false;
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * String representation of this Node
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Abstract render method for this node
     *
     * @abstract
     * @param Naked\Template\Context $context
     */
    abstract public function render($context);
}
